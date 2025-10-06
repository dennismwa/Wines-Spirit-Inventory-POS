<?php
/**
 * User Management Class
 * Wines & Spirits POS System
 */

class User {
    private $db;
    private $table = 'users';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Authenticate user login
     */
    public function login($username, $password) {
        try {
            // Check login attempts
            if ($this->isLockedOut($username)) {
                return ['success' => false, 'message' => 'Account locked due to multiple failed attempts. Please try again later.'];
            }
            
            $stmt = $this->db->prepare("
                SELECT id, username, email, password, full_name, role, status 
                FROM {$this->table} 
                WHERE (username = ? OR email = ?) AND status = 'active'
            ");
            
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Clear login attempts
                $this->clearLoginAttempts($username);
                
                // Update last login
                $this->updateLastLogin($user['id']);
                
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['login_time'] = time();
                
                // Generate session token
                $sessionToken = bin2hex(random_bytes(32));
                $_SESSION['session_token'] = $sessionToken;
                
                // Store session in database
                $this->createSession($user['id'], $sessionToken);
                
                // Log activity
                logActivity('login', 'authentication', 'User logged in successfully');
                
                return ['success' => true, 'message' => 'Login successful'];
            } else {
                // Record failed attempt
                $this->recordLoginAttempt($username);
                
                return ['success' => false, 'message' => 'Invalid username or password'];
            }
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'message' => 'System error. Please try again.'];
        }
    }
    
    /**
     * Logout user
     */
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            // Remove session from database
            if (isset($_SESSION['session_token'])) {
                $this->deleteSession($_SESSION['session_token']);
            }
            
            logActivity('logout', 'authentication', 'User logged out');
        }
        
        // Destroy session
        session_unset();
        session_destroy();
        
        return ['success' => true, 'message' => 'Logged out successfully'];
    }
    
    /**
     * Create new user
     */
    public function create($data) {
        try {
            // Validate required fields
            $required = ['username', 'email', 'password', 'full_name', 'role'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return ['success' => false, 'message' => ucfirst($field) . ' is required'];
                }
            }
            
            // Check if username exists
            if ($this->usernameExists($data['username'])) {
                return ['success' => false, 'message' => 'Username already exists'];
            }
            
            // Check if email exists
            if ($this->emailExists($data['email'])) {
                return ['success' => false, 'message' => 'Email already exists'];
            }
            
            // Validate email
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'message' => 'Invalid email format'];
            }
            
            // Validate password strength
            if (strlen($data['password']) < PASSWORD_MIN_LENGTH) {
                return ['success' => false, 'message' => 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters'];
            }
            
            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_HASH_ALGO);
            
            // Insert user
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} 
                (username, email, password, full_name, phone, role, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['username'],
                $data['email'],
                $hashedPassword,
                $data['full_name'],
                $data['phone'] ?? null,
                $data['role'],
                $data['status'] ?? 'active'
            ]);
            
            $userId = $this->db->lastInsertId();
            
            logActivity('create_user', 'users', "Created user: {$data['username']}");
            
            return [
                'success' => true, 
                'message' => 'User created successfully',
                'user_id' => $userId
            ];
            
        } catch (Exception $e) {
            error_log("Create user error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to create user'];
        }
    }
    
    /**
     * Update user
     */
    public function update($id, $data) {
        try {
            $updates = [];
            $params = [];
            
            // Build update query dynamically
            $allowedFields = ['username', 'email', 'full_name', 'phone', 'role', 'status'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    // Check uniqueness for username
                    if ($field === 'username' && $this->usernameExists($data[$field], $id)) {
                        return ['success' => false, 'message' => 'Username already exists'];
                    }
                    
                    // Check uniqueness for email
                    if ($field === 'email') {
                        if (!filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                            return ['success' => false, 'message' => 'Invalid email format'];
                        }
                        if ($this->emailExists($data[$field], $id)) {
                            return ['success' => false, 'message' => 'Email already exists'];
                        }
                    }
                    
                    $updates[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            // Handle password update separately
            if (!empty($data['password'])) {
                if (strlen($data['password']) < PASSWORD_MIN_LENGTH) {
                    return ['success' => false, 'message' => 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters'];
                }
                $updates[] = "password = ?";
                $params[] = password_hash($data['password'], PASSWORD_HASH_ALGO);
            }
            
            if (empty($updates)) {
                return ['success' => false, 'message' => 'No data to update'];
            }
            
            $params[] = $id;
            
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET " . implode(', ', $updates) . " 
                WHERE id = ?
            ");
            
            $stmt->execute($params);
            
            logActivity('update_user', 'users', "Updated user ID: $id");
            
            return ['success' => true, 'message' => 'User updated successfully'];
            
        } catch (Exception $e) {
            error_log("Update user error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to update user'];
        }
    }
    
    /**
     * Delete user
     */
    public function delete($id) {
        try {
            // Don't allow deleting own account
            if ($id == $_SESSION['user_id']) {
                return ['success' => false, 'message' => 'Cannot delete your own account'];
            }
            
            // Check if user has sales
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM sales WHERE seller_id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->fetchColumn() > 0) {
                // Soft delete - just deactivate
                $stmt = $this->db->prepare("UPDATE {$this->table} SET status = 'inactive' WHERE id = ?");
                $stmt->execute([$id]);
                $message = 'User deactivated (has sales history)';
            } else {
                // Hard delete
                $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
                $stmt->execute([$id]);
                $message = 'User deleted successfully';
            }
            
            logActivity('delete_user', 'users', "Deleted/deactivated user ID: $id");
            
            return ['success' => true, 'message' => $message];
            
        } catch (Exception $e) {
            error_log("Delete user error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to delete user'];
        }
    }
    
    /**
     * Get user by ID
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT id, username, email, full_name, phone, role, status, 
                       last_login, last_activity, created_at 
                FROM {$this->table} 
                WHERE id = ?
            ");
            
            $stmt->execute([$id]);
            return $stmt->fetch();
            
        } catch (Exception $e) {
            error_log("Get user error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get all users
     */
    public function getAll($filters = []) {
        try {
            $where = [];
            $params = [];
            
            if (!empty($filters['role'])) {
                $where[] = "role = ?";
                $params[] = $filters['role'];
            }
            
            if (!empty($filters['status'])) {
                $where[] = "status = ?";
                $params[] = $filters['status'];
            }
            
            if (!empty($filters['search'])) {
                $where[] = "(username LIKE ? OR email LIKE ? OR full_name LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
            
            $stmt = $this->db->prepare("
                SELECT id, username, email, full_name, phone, role, status, 
                       last_login, last_activity, created_at 
                FROM {$this->table} 
                $whereClause 
                ORDER BY created_at DESC
            ");
            
            $stmt->execute($params);
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            error_log("Get users error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Change user password
     */
    public function changePassword($userId, $currentPassword, $newPassword) {
        try {
            // Get current password hash
            $stmt = $this->db->prepare("SELECT password FROM {$this->table} WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            if (!$user) {
                return ['success' => false, 'message' => 'User not found'];
            }
            
            // Verify current password
            if (!password_verify($currentPassword, $user['password'])) {
                return ['success' => false, 'message' => 'Current password is incorrect'];
            }
            
            // Validate new password
            if (strlen($newPassword) < PASSWORD_MIN_LENGTH) {
                return ['success' => false, 'message' => 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters'];
            }
            
            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_HASH_ALGO);
            $stmt = $this->db->prepare("UPDATE {$this->table} SET password = ? WHERE id = ?");
            $stmt->execute([$hashedPassword, $userId]);
            
            logActivity('change_password', 'users', 'Changed password');
            
            return ['success' => true, 'message' => 'Password changed successfully'];
            
        } catch (Exception $e) {
            error_log("Change password error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to change password'];
        }
    }
    
    /**
     * Get user statistics
     */
    public function getStatistics($userId = null) {
        try {
            if ($userId) {
                $stmt = $this->db->prepare("
                    SELECT 
                        COUNT(DISTINCT s.id) as total_sales,
                        COALESCE(SUM(s.total_amount), 0) as total_revenue,
                        COUNT(DISTINCT DATE(s.created_at)) as days_active
                    FROM sales s
                    WHERE s.seller_id = ? AND s.status = 'completed'
                ");
                $stmt->execute([$userId]);
            } else {
                $stmt = $this->db->query("
                    SELECT 
                        COUNT(*) as total_users,
                        SUM(CASE WHEN role = 'owner' THEN 1 ELSE 0 END) as owners,
                        SUM(CASE WHEN role = 'seller' THEN 1 ELSE 0 END) as sellers,
                        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_users
                    FROM {$this->table}
                ");
            }
            
            return $stmt->fetch();
            
        } catch (Exception $e) {
            error_log("Get user statistics error: " . $e->getMessage());
            return [];
        }
    }
    
    // Helper methods
    
    private function usernameExists($username, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE username = ?";
        $params = [$username];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }
    
    private function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE email = ?";
        $params = [$email];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }
    
    private function updateLastLogin($userId) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$userId]);
    }
    
    private function createSession($userId, $token) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO sessions (user_id, session_token, ip_address, user_agent, last_activity) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $userId,
                $token,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
        } catch (Exception $e) {
            error_log("Create session error: " . $e->getMessage());
        }
    }
    
    private function deleteSession($token) {
        try {
            $stmt = $this->db->prepare("DELETE FROM sessions WHERE session_token = ?");
            $stmt->execute([$token]);
        } catch (Exception $e) {
            error_log("Delete session error: " . $e->getMessage());
        }
    }
    
    private function isLockedOut($username) {
        // Implementation for login attempt tracking
        // This would check a login_attempts table
        return false;
    }
    
    private function recordLoginAttempt($username) {
        // Implementation for recording failed login attempts
    }
    
    private function clearLoginAttempts($username) {
        // Implementation for clearing login attempts after successful login
    }
}