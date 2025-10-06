<?php
/**
 * ajax/login.php - Login AJAX Handler
 */

define('APP_RUNNING', true);
define('SKIP_INIT', true);
require_once '../config/database.php';
require_once '../classes/User.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, 'Invalid request method');
}

$username = sanitizeInput($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

if (empty($username) || empty($password)) {
    sendJsonResponse(false, 'Username and password are required');
}

$user = new User();
$result = $user->login($username, $password);

if ($result['success']) {
    // Set remember me cookie if requested
    if ($remember) {
        setcookie('remember_username', $username, time() + (30 * 24 * 60 * 60), '/');
    }
    
    sendJsonResponse(true, 'Login successful', ['redirect' => '/dashboard.php']);
} else {
    sendJsonResponse(false, $result['message']);
}
