<?php
/**
 * Product Management Class
 * Wines & Spirits POS System
 */

class Product {
    private $db;
    private $table = 'products';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Create new product
     */
    public function create($data) {
        try {
            // Validate required fields
            $required = ['name', 'selling_price', 'cost_price', 'quantity'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    return ['success' => false, 'message' => ucfirst($field) . ' is required'];
                }
            }
            
            // Generate SKU if not provided
            if (empty($data['sku'])) {
                $data['sku'] = $this->generateSKU($data['name']);
            }
            
            // Check if SKU exists
            if ($this->skuExists($data['sku'])) {
                return ['success' => false, 'message' => 'SKU already exists'];
            }
            
            // Check if barcode exists
            if (!empty($data['barcode']) && $this->barcodeExists($data['barcode'])) {
                return ['success' => false, 'message' => 'Barcode already exists'];
            }
            
            // Insert product
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} 
                (barcode, sku, name, description, category_id, supplier_id, brand, 
                 unit, cost_price, selling_price, quantity, reorder_level, tax_rate, 
                 status, image, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['barcode'] ?? null,
                $data['sku'],
                $data['name'],
                $data['description'] ?? null,
                $data['category_id'] ?? null,
                $data['supplier_id'] ?? null,
                $data['brand'] ?? null,
                $data['unit'] ?? 'piece',
                $data['cost_price'],
                $data['selling_price'],
                $data['quantity'],
                $data['reorder_level'] ?? 10,
                $data['tax_rate'] ?? 16,
                $data['status'] ?? 'active',
                $data['image'] ?? null,
                $_SESSION['user_id'] ?? null
            ]);
            
            $productId = $this->db->lastInsertId();
            
            // Log stock movement
            $this->logStockMovement($productId, 'purchase', $data['quantity'], 0, $data['quantity'], $data['cost_price']);
            
            // Log activity
            logActivity('create_product', 'products', "Created product: {$data['name']}");
            
            return [
                'success' => true,
                'message' => 'Product created successfully',
                'product_id' => $productId
            ];
            
        } catch (Exception $e) {
            error_log("Create product error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to create product'];
        }
    }
    
    /**
     * Update product
     */
    public function update($id, $data) {
        try {
            $updates = [];
            $params = [];
            
            // Build update query dynamically
            $allowedFields = [
                'barcode', 'sku', 'name', 'description', 'category_id', 
                'supplier_id', 'brand', 'unit', 'cost_price', 'selling_price', 
                'quantity', 'reorder_level', 'tax_rate', 'status', 'image'
            ];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    // Validate unique fields
                    if ($field === 'sku' && $this->skuExists($data[$field], $id)) {
                        return ['success' => false, 'message' => 'SKU already exists'];
                    }
                    
                    if ($field === 'barcode' && !empty($data[$field]) && $this->barcodeExists($data[$field], $id)) {
                        return ['success' => false, 'message' => 'Barcode already exists'];
                    }
                    
                    // Track quantity changes for stock movement
                    if ($field === 'quantity') {
                        $oldQuantity = $this->getQuantity($id);
                        if ($oldQuantity !== false && $data['quantity'] != $oldQuantity) {
                            $difference = $data['quantity'] - $oldQuantity;
                            $this->logStockMovement(
                                $id, 
                                'adjustment', 
                                $difference, 
                                $oldQuantity, 
                                $data['quantity'],
                                $data['cost_price'] ?? null
                            );
                        }
                    }
                    
                    $updates[] = "$field = ?";
                    $params[] = $data[$field];
                }
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
            
            logActivity('update_product', 'products', "Updated product ID: $id");
            
            return ['success' => true, 'message' => 'Product updated successfully'];
            
        } catch (Exception $e) {
            error_log("Update product error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to update product'];
        }
    }
    
    /**
     * Delete product
     */
    public function delete($id) {
        try {
            // Check if product has sales history
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM sale_items WHERE product_id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->fetchColumn() > 0) {
                // Soft delete - just deactivate
                $stmt = $this->db->prepare("UPDATE {$this->table} SET status = 'inactive' WHERE id = ?");
                $stmt->execute([$id]);
                $message = 'Product deactivated (has sales history)';
            } else {
                // Hard delete
                $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
                $stmt->execute([$id]);
                $message = 'Product deleted successfully';
            }
            
            logActivity('delete_product', 'products', "Deleted/deactivated product ID: $id");
            
            return ['success' => true, 'message' => $message];
            
        } catch (Exception $e) {
            error_log("Delete product error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to delete product'];
        }
    }
    
    /**
     * Get product by ID
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, c.name as category_name, s.name as supplier_name
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN suppliers s ON p.supplier_id = s.id
                WHERE p.id = ?
            ");
            
            $stmt->execute([$id]);
            return $stmt->fetch();
            
        } catch (Exception $e) {
            error_log("Get product error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get product by barcode
     */
    public function getByBarcode($barcode) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, c.name as category_name
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.barcode = ? AND p.status = 'active'
            ");
            
            $stmt->execute([$barcode]);
            return $stmt->fetch();
            
        } catch (Exception $e) {
            error_log("Get product by barcode error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get all products with filters
     */
    public function getAll($filters = [], $limit = null, $offset = 0) {
        try {
            $where = [];
            $params = [];
            
            // Apply filters
            if (!empty($filters['category_id'])) {
                $where[] = "p.category_id = ?";
                $params[] = $filters['category_id'];
            }
            
            if (!empty($filters['supplier_id'])) {
                $where[] = "p.supplier_id = ?";
                $params[] = $filters['supplier_id'];
            }
            
            if (!empty($filters['status'])) {
                $where[] = "p.status = ?";
                $params[] = $filters['status'];
            }
            
            if (!empty($filters['search'])) {
                $where[] = "(p.name LIKE ? OR p.sku LIKE ? OR p.barcode LIKE ? OR p.brand LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            if (isset($filters['low_stock']) && $filters['low_stock']) {
                $where[] = "p.quantity <= p.reorder_level";
            }
            
            if (isset($filters['out_of_stock']) && $filters['out_of_stock']) {
                $where[] = "p.quantity = 0";
            }
            
            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
            
            // Build query
            $sql = "
                SELECT p.*, c.name as category_name, s.name as supplier_name,
                       (p.selling_price - p.cost_price) as profit_margin,
                       ((p.selling_price - p.cost_price) / p.cost_price * 100) as profit_percentage
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN suppliers s ON p.supplier_id = s.id
                $whereClause
                ORDER BY p.name ASC
            ";
            
            // Add limit if specified
            if ($limit !== null) {
                $sql .= " LIMIT $limit OFFSET $offset";
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            error_log("Get products error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Search products for POS
     */
    public function searchForPOS($query) {
        try {
            $stmt = $this->db->prepare("
                SELECT id, barcode, sku, name, brand, selling_price, quantity, tax_rate, unit
                FROM {$this->table}
                WHERE status = 'active' 
                AND quantity > 0
                AND (name LIKE ? OR sku LIKE ? OR barcode = ? OR brand LIKE ?)
                LIMIT 10
            ");
            
            $searchTerm = '%' . $query . '%';
            $stmt->execute([$searchTerm, $searchTerm, $query, $searchTerm]);
            
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            error_log("Search products error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get low stock products
     */
    public function getLowStock() {
        try {
            $stmt = $this->db->query("
                SELECT p.*, c.name as category_name
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.quantity <= p.reorder_level 
                AND p.status = 'active'
                ORDER BY p.quantity ASC
            ");
            
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            error_log("Get low stock error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Update stock
     */
    public function updateStock($productId, $quantity, $type = 'add') {
        try {
            $currentQuantity = $this->getQuantity($productId);
            
            if ($currentQuantity === false) {
                return ['success' => false, 'message' => 'Product not found'];
            }
            
            if ($type === 'add') {
                $newQuantity = $currentQuantity + $quantity;
            } elseif ($type === 'subtract') {
                $newQuantity = $currentQuantity - $quantity;
                if ($newQuantity < 0) {
                    return ['success' => false, 'message' => 'Insufficient stock'];
                }
            } else {
                $newQuantity = $quantity; // Set absolute value
            }
            
            $stmt = $this->db->prepare("UPDATE {$this->table} SET quantity = ? WHERE id = ?");
            $stmt->execute([$newQuantity, $productId]);
            
            // Update status based on quantity
            if ($newQuantity <= 0) {
                $this->updateStatus($productId, 'out_of_stock');
            } else {
                $this->updateStatus($productId, 'active');
            }
            
            // Log stock movement
            $movementType = $type === 'add' ? 'purchase' : 'adjustment';
            $this->logStockMovement($productId, $movementType, $quantity, $currentQuantity, $newQuantity);
            
            return ['success' => true, 'message' => 'Stock updated successfully'];
            
        } catch (Exception $e) {
            error_log("Update stock error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to update stock'];
        }
    }
    
    /**
     * Bulk import products
     */
    public function bulkImport($data) {
        $imported = 0;
        $failed = 0;
        $errors = [];
        
        foreach ($data as $row) {
            $result = $this->create($row);
            if ($result['success']) {
                $imported++;
            } else {
                $failed++;
                $errors[] = "Row {$row['name']}: " . $result['message'];
            }
        }
        
        return [
            'success' => true,
            'imported' => $imported,
            'failed' => $failed,
            'errors' => $errors
        ];
    }
    
    /**
     * Get product statistics
     */
    public function getStatistics() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    COUNT(*) as total_products,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_products,
                    SUM(CASE WHEN quantity <= reorder_level THEN 1 ELSE 0 END) as low_stock,
                    SUM(CASE WHEN quantity = 0 THEN 1 ELSE 0 END) as out_of_stock,
                    SUM(quantity) as total_items,
                    SUM(quantity * cost_price) as total_inventory_value,
                    AVG((selling_price - cost_price) / cost_price * 100) as avg_profit_margin
                FROM {$this->table}
            ");
            
            return $stmt->fetch();
            
        } catch (Exception $e) {
            error_log("Get product statistics error: " . $e->getMessage());
            return [];
        }
    }
    
    // Helper methods
    
    private function generateSKU($productName) {
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $productName), 0, 3));
        $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        return $prefix . $random;
    }
    
    private function skuExists($sku, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE sku = ?";
        $params = [$sku];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }
    
    private function barcodeExists($barcode, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE barcode = ?";
        $params = [$barcode];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }
    
    private function getQuantity($productId) {
        $stmt = $this->db->prepare("SELECT quantity FROM {$this->table} WHERE id = ?");
        $stmt->execute([$productId]);
        return $stmt->fetchColumn();
    }
    
    private function updateStatus($productId, $status) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = ? WHERE id = ?");
        $stmt->execute([$status, $productId]);
    }
    
    private function logStockMovement($productId, $type, $quantity, $previousStock, $newStock, $cost = null) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO stock_movements 
                (product_id, movement_type, quantity, previous_stock, new_stock, cost, user_id)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $productId,
                $type,
                $quantity,
                $previousStock,
                $newStock,
                $cost,
                $_SESSION['user_id'] ?? null
            ]);
        } catch (Exception $e) {
            error_log("Log stock movement error: " . $e->getMessage());
        }
    }
}