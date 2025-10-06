<?php
define('APP_RUNNING', true);
require_once '../config/database.php';
require_once '../classes/Sale.php';
require_once '../classes/Product.php';

requireLogin();
header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        createSale();
        break;
    
    case 'get_products':
        getProducts();
        break;
    
    case 'search_product':
        searchProduct();
        break;
    
    case 'get_receipt':
        getReceipt();
        break;
    
    case 'cancel':
        cancelSale();
        break;
    
    default:
        sendJsonResponse(false, 'Invalid action');
}

function createSale() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendJsonResponse(false, 'Invalid request method');
    }
    
    $data = json_decode($_POST['sale_data'] ?? '{}', true);
    
    if (empty($data['items'])) {
        sendJsonResponse(false, 'No items in sale');
    }
    
    $sale = new Sale();
    $result = $sale->create($data);
    
    if ($result['success']) {
        // Generate receipt HTML
        $receiptData = $sale->generateReceipt($result['sale_id']);
        $result['receipt'] = generateReceiptHTML($receiptData);
    }
    
    sendJsonResponse($result['success'], $result['message'], $result);
}

function getProducts() {
    $product = new Product();
    $categoryId = $_GET['category_id'] ?? null;
    
    $filters = [];
    if ($categoryId) {
        $filters['category_id'] = $categoryId;
    }
    $filters['status'] = 'active';
    
    $products = $product->getAll($filters, 50);
    
    sendJsonResponse(true, '', ['products' => $products]);
}

function searchProduct() {
    $query = $_GET['query'] ?? '';
    
    if (strlen($query) < 2) {
        sendJsonResponse(false, 'Search query too short');
    }
    
    $product = new Product();
    $products = $product->searchForPOS($query);
    
    sendJsonResponse(true, '', ['products' => $products]);
}

function getReceipt() {
    $saleId = $_GET['sale_id'] ?? 0;
    
    if (!$saleId) {
        sendJsonResponse(false, 'Sale ID required');
    }
    
    $sale = new Sale();
    $receiptData = $sale->generateReceipt($saleId);
    
    if (!$receiptData) {
        sendJsonResponse(false, 'Sale not found');
    }
    
    $html = generateReceiptHTML($receiptData);
    sendJsonResponse(true, '', ['receipt' => $html]);
}

function cancelSale() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendJsonResponse(false, 'Invalid request method');
    }
    
    $saleId = $_POST['sale_id'] ?? 0;
    $reason = $_POST['reason'] ?? '';
    
    if (!$saleId) {
        sendJsonResponse(false, 'Sale ID required');
    }
    
    $sale = new Sale();
    $result = $sale->cancel($saleId, $reason);
    
    sendJsonResponse($result['success'], $result['message']);
}

function generateReceiptHTML($data) {
    $html = '<div style="font-family: monospace; width: 300px; margin: 0 auto;">';
    $html .= '<div style="text-align: center; margin-bottom: 20px;">';
    $html .= '<h2>' . escape($data['company_name']) . '</h2>';
    $html .= '<p>' . escape($data['company_address']) . '</p>';
    $html .= '<p>Tel: ' . escape($data['company_phone']) . '</p>';
    $html .= '</div>';
    
    $html .= '<div style="border-top: 1px dashed #000; margin: 10px 0;"></div>';
    
    $html .= '<p>Invoice: ' . escape($data['invoice_no']) . '</p>';
    $html .= '<p>Date: ' . formatDateTime($data['date']) . '</p>';
    $html .= '<p>Served by: ' . escape($data['seller']) . '</p>';
    
    if ($data['customer']) {
        $html .= '<p>Customer: ' . escape($data['customer']) . '</p>';
    }
    
    $html .= '<div style="border-top: 1px dashed #000; margin: 10px 0;"></div>';
    
    // Items
    foreach ($data['items'] as $item) {
        $html .= '<div style="margin: 5px 0;">';
        $html .= '<div>' . escape($item['product_name']) . '</div>';
        $html .= '<div style="display: flex; justify-content: space-between;">';
        $html .= '<span>' . $item['quantity'] . ' x ' . formatCurrency($item['unit_price']) . '</span>';
        $html .= '<span>' . formatCurrency($item['total_price']) . '</span>';
        $html .= '</div>';
        $html .= '</div>';
    }
    
    $html .= '<div style="border-top: 1px dashed #000; margin: 10px 0;"></div>';
    
    // Totals
    $html .= '<div style="display: flex; justify-content: space-between;"><span>Subtotal:</span><span>' . formatCurrency($data['subtotal']) . '</span></div>';
    $html .= '<div style="display: flex; justify-content: space-between;"><span>Tax:</span><span>' . formatCurrency($data['tax']) . '</span></div>';
    
    if ($data['discount'] > 0) {
        $html .= '<div style="display: flex; justify-content: space-between;"><span>Discount:</span><span>-' . formatCurrency($data['discount']) . '</span></div>';
    }
    
    $html .= '<div style="display: flex; justify-content: space-between; font-weight: bold; margin-top: 5px;"><span>TOTAL:</span><span>' . formatCurrency($data['total']) . '</span></div>';
    
    $html .= '<div style="display: flex; justify-content: space-between;"><span>Paid:</span><span>' . formatCurrency($data['paid']) . '</span></div>';
    $html .= '<div style="display: flex; justify-content: space-between;"><span>Change:</span><span>' . formatCurrency($data['change']) . '</span></div>';
    
    $html .= '<div style="border-top: 1px dashed #000; margin: 10px 0;"></div>';
    
    $html .= '<div style="text-align: center; margin-top: 20px;">';
    $html .= '<p>' . escape($data['footer']) . '</p>';
    $html .= '</div>';
    
    $html .= '</div>';
    
    return $html;
}