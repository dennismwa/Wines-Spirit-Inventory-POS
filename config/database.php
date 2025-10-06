<?php
/**
 * Database Configuration
 * Wines & Spirits POS System
 */

// Prevent direct access
if (!defined('APP_RUNNING')) {
    header('HTTP/1.0 403 Forbidden');
    exit('Direct access not permitted');
}

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'vxjtgclw_Wines');
define('DB_USER', 'vxjtgclw_wines');
define('DB_PASS', 'v])2LgS+I-JJ[?Z}');
define('DB_CHARSET', 'utf8mb4');

// Application configuration
define('APP_NAME', 'Wines & Spirits POS');
define('APP_URL', 'http://' . $_SERVER['HTTP_HOST']);
define('APP_ROOT', dirname(dirname(__FILE__)));
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'production'); // development or production

// Session configuration
define('SESSION_NAME', 'wines_pos_session');
define('SESSION_LIFETIME', 1800); // 30 minutes
define('SESSION_PATH', '/');
define('SESSION_SECURE', false); // Set to true if using HTTPS
define('SESSION_HTTPONLY', true);

// Security configuration
define('CSRF_TOKEN_NAME', 'csrf_token');
define('PASSWORD_HASH_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_MIN_LENGTH', 8);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// Regional configuration
define('TIMEZONE', 'Africa/Nairobi');
define('DATE_FORMAT', 'd/m/Y');
define('TIME_FORMAT', 'H:i:s');
define('DATETIME_FORMAT', 'd/m/Y H:i:s');
define('CURRENCY', 'KES');
define('CURRENCY_SYMBOL', 'KSh');
define('DECIMAL_PLACES', 2);

// Upload configuration
define('UPLOAD_PATH', APP_ROOT . '/uploads/');
define('UPLOAD_MAX_SIZE', 10485760); // 10MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('ALLOWED_FILE_TYPES', ['pdf', 'xls', 'xlsx', 'csv']);

// Pagination
define('ITEMS_PER_PAGE', 20);

// Error handling
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', APP_ROOT . '/logs/error.log');
}

// Set timezone
date_default_timezone_set(TIMEZONE);

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path' => SESSION_PATH,
        'secure' => SESSION_SECURE,
        'httponly' => SESSION_HTTPONLY,
        'samesite' => 'Strict'
    ]);
    session_start();
}

/**
 * Database Connection Class
 */
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET . " COLLATE utf8mb4_unicode_ci"
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch (PDOException $e) {
            if (APP_ENV === 'development') {
                die("Database connection failed: " . $e->getMessage());
            } else {
                error_log("Database connection failed: " . $e->getMessage());
                die("System error. Please contact administrator.");
            }
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Prevent cloning
    private function __clone() {}
    
    // Prevent deserialization
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

/**
 * Global helper functions
 */

function db() {
    return Database::getInstance()->getConnection();
}

function escape($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function generateCSRFToken() {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

function verifyCSRFToken($token) {
    if (empty($_SESSION[CSRF_TOKEN_NAME]) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: /index.php');
        exit();
    }
}

function hasRole($role) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

function isOwner() {
    return hasRole('owner');
}

function isSeller() {
    return hasRole('seller');
}

function formatCurrency($amount) {
    return CURRENCY_SYMBOL . ' ' . number_format($amount, DECIMAL_PLACES);
}

function formatDate($date, $format = null) {
    if (empty($date)) return '';
    if ($format === null) $format = DATE_FORMAT;
    return date($format, strtotime($date));
}

function formatDateTime($datetime, $format = null) {
    if (empty($datetime)) return '';
    if ($format === null) $format = DATETIME_FORMAT;
    return date($format, strtotime($datetime));
}

function generateInvoiceNumber() {
    $prefix = 'INV';
    $date = date('Ymd');
    $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    return $prefix . $date . $random;
}

function generateSKU($productName) {
    $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $productName), 0, 3));
    $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    return $prefix . $random;
}

function logActivity($action, $module, $description = '') {
    if (!isLoggedIn()) return;
    
    try {
        $stmt = db()->prepare("
            INSERT INTO activity_logs (user_id, action, module, description, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $_SESSION['user_id'],
            $action,
            $module,
            $description,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (Exception $e) {
        error_log("Activity log failed: " . $e->getMessage());
    }
}

function updateLastActivity() {
    if (!isLoggedIn()) return;
    
    try {
        $stmt = db()->prepare("UPDATE users SET last_activity = NOW() WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    } catch (Exception $e) {
        error_log("Update last activity failed: " . $e->getMessage());
    }
}

function checkSessionTimeout() {
    if (isset($_SESSION['last_activity'])) {
        $inactive = time() - $_SESSION['last_activity'];
        if ($inactive > SESSION_LIFETIME) {
            session_unset();
            session_destroy();
            header('Location: /index.php?timeout=1');
            exit();
        }
    }
    $_SESSION['last_activity'] = time();
}

function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone($phone) {
    // Kenyan phone number format
    return preg_match('/^(\+254|0)[17][0-9]{8}$/', $phone);
}

function sendJsonResponse($success, $message = '', $data = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

function getSettings() {
    static $settings = null;
    
    if ($settings === null) {
        try {
            $stmt = db()->query("SELECT setting_key, setting_value FROM settings");
            $results = $stmt->fetchAll();
            $settings = [];
            foreach ($results as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (Exception $e) {
            error_log("Failed to load settings: " . $e->getMessage());
            $settings = [];
        }
    }
    
    return $settings;
}

function getSetting($key, $default = '') {
    $settings = getSettings();
    return $settings[$key] ?? $default;
}

function updateSetting($key, $value) {
    try {
        $stmt = db()->prepare("
            INSERT INTO settings (setting_key, setting_value) 
            VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
        ");
        return $stmt->execute([$key, $value]);
    } catch (Exception $e) {
        error_log("Failed to update setting: " . $e->getMessage());
        return false;
    }
}

// Initialize application
if (!defined('SKIP_INIT')) {
    checkSessionTimeout();
    updateLastActivity();
}