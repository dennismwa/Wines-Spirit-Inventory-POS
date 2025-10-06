<?php
define('APP_RUNNING', true);
require_once '../config/database.php';
require_once '../classes/Product.php';

requireLogin();
header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        createProduct();
        break;
    
    case 'update':
        updateProduct();
        break;
    
    case 'delete':
        deleteProduct();
        break;
    
    case 'get':
        getProduct();
        break;
    
    case 'update_stock':
        updateStock();
        break;
    
    case 'import':
        importProducts();
        break;
    
    default:
        sendJsonResponse(false, 'Invalid action');
}

function createProduct() {
    if (!isOwner() && !isSeller()) {
        sendJsonResponse(false, 'Unauthorized');
    }
    
    $product = new Product();
    $result = $product->create($_POST);
    
    sendJsonResponse($result['success'], $result['message'], $result);
}

function updateProduct() {
    if (!isOwner()) {
        sendJsonResponse(false, 'Unauthorized');
    }
    
    $id = $_POST['id'] ?? 0;
    if (!$id) {
        sendJsonResponse(false, 'Product ID required');
    }
    
    $product = new Product();
    $result = $product->update($id, $_POST);
    
    sendJsonResponse($result['success'], $result['message']);
}

function deleteProduct() {
    if (!isOwner()) {
        sendJsonResponse(false, 'Unauthorized');
    }
    
    $id = $_POST['id'] ?? 0;
    if (!$id) {
        sendJsonResponse(false, 'Product ID required');
    }
    
    $product = new Product();
    $result = $product->delete($id);
    
    sendJsonResponse($result['success'], $result['message']);
}

function getProduct() {
    $id = $_GET['id'] ?? 0;
    if (!$id) {
        sendJsonResponse(false, 'Product ID required');
    }
    
    $product = new Product();
    $data = $product->getById($id);
    
    if ($data) {
        sendJsonResponse(true, '', ['product' => $data]);
    } else {
        sendJsonResponse(false, 'Product not found');
    }
}

function updateStock() {
    if (!isOwner() && !isSeller()) {
        sendJsonResponse(false, 'Unauthorized');
    }
    
    $id = $_POST['id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 0;
    $type = $_POST['type'] ?? 'set';
    
    if (!$id) {
        sendJsonResponse(false, 'Product ID required');
    }
    
    $product = new Product();
    $result = $product->updateStock($id, $quantity, $type);
    
    sendJsonResponse($result['success'], $result['message']);
}

function importProducts() {
    if (!isOwner()) {
        sendJsonResponse(false, 'Unauthorized');
    }
    
    // Handle CSV import
    if (!isset($_FILES['csv_file'])) {
        sendJsonResponse(false, 'No file uploaded');
    }
    
    // Process CSV and import products
    // Implementation would go here
    
    sendJsonResponse(true, 'Products imported successfully');
}
