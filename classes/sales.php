<?php
/**
 * Sale Management Class
 * Wines & Spirits POS System
 */

class Sale {
    private $db;
    private $table = 'sales';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Create new sale
     */
    public function create($data) {
        try {
            $this->db->beginTransaction();
            
            // Generate invoice number
            $invoiceNo = $this->generateInvoiceNumber();
            
            // Calculate totals
            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            
            $taxRate = floatval(getSetting('tax_rate', 16)) / 100;
            $taxAmount = $subtotal * $taxRate;
            $discountAmount = $data['discount'] ?? 0;
            $totalAmount = $subtotal + $taxAmount - $discountAmount;
            $paidAmount = $data['paid_amount'] ?? $totalAmount;
            $changeAmount = $paidAmount - $totalAmount;
            
            // Insert sale
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} 
                (invoice_no, customer_name, customer_phone, subtotal, tax_amount, 
                 discount_amount, total_amount, paid_amount, change_amount, 
                 payment_method, payment_reference, seller_id, sale_date, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), 'completed')
            ");
            
            $stmt->execute([
                $invoiceNo,
                $data['customer_name'] ?? null,
                $data['customer_phone'] ?? null,
                $subtotal,
                $taxAmount,
                $discountAmount,
                $totalAmount,
                $paidAmount,
                $changeAmount,
                $data['payment_method'] ?? 'cash',
                $data['payment_reference'] ?? null,
                $_SESSION['user_id'] ?? 1
            ]);
            
            $saleId = $this->db->lastInsertId();
            
            // Insert sale items
            $stmt = $this->db->prepare("
                INSERT INTO sale_items 
                (sale_id, product_id, product_name, quantity, unit_price, cost_price, total_price) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            foreach ($data['items'] as $item) {
                // Get product details
                $product = $this->getProductById($item['product_id']);
                if (!$product) continue;
                
                $itemTotal = $item['price'] * $item['quantity'];
                
                $stmt->execute([
                    $saleId,
                    $item['product_id'],
                    $product['name'],
                    $item['quantity'],
                    $item['price'],
                    $product['cost_price'],
                    $itemTotal
                ]);
            }
            
            $this->db->commit();
            
            logActivity('create_sale', 'sales', "Created sale: $invoiceNo");
            
            return [
                'success' => true,
                'message' => 'Sale completed successfully',
                'sale_id' => $saleId,
                'invoice_no' => $invoiceNo
            ];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Create sale error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to process sale'];
        }
    }
    
    /**
     * Cancel sale
     */
    public function cancel($saleId, $reason = '') {
        try {
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET status = 'cancelled', notes = ? 
                WHERE id = ?
            ");
            
            $stmt->execute([$reason, $saleId]);
            
            logActivity('cancel_sale', 'sales', "Cancelled sale ID: $saleId");
            
            return ['success' => true, 'message' => 'Sale cancelled successfully'];
            
        } catch (Exception $e) {
            error_log("Cancel sale error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to cancel sale'];
        }
    }
    
    /**
     * Get sale by ID
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT s.*, u.full_name as seller_name
                FROM {$this->table} s
                LEFT JOIN users u ON s.seller_id = u.id
                WHERE s.id = ?
            ");
            
            $stmt->execute([$id]);
            return $stmt->fetch();
            
        } catch (Exception $e) {
            error_log("Get sale error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get sale items
     */
    public function getSaleItems($saleId) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM sale_items 
                WHERE sale_id = ? 
                ORDER BY id ASC
            ");
            
            $stmt->execute([$saleId]);
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            error_log("Get sale items error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Generate receipt
     */
    public function generateReceipt($saleId) {
        $sale = $this->getById($saleId);
        if (!$sale) return null;
        
        $items = $this->getSaleItems($saleId);
        
        return [
            'company_name' => getSetting('company_name', 'Wines & Spirits Shop'),
            'company_address' => getSetting('company_address', ''),
            'company_phone' => getSetting('company_phone', ''),
            'invoice_no' => $sale['invoice_no'],
            'date' => $sale['created_at'],
            'seller' => $sale['seller_name'],
            'customer' => $sale['customer_name'],
            'items' => $items,
            'subtotal' => $sale['subtotal'],
            'tax' => $sale['tax_amount'],
            'discount' => $sale['discount_amount'],
            'total' => $sale['total_amount'],
            'paid' => $sale['paid_amount'],
            'change' => $sale['change_amount'],
            'payment_method' => $sale['payment_method'],
            'footer' => getSetting('receipt_footer', 'Thank you for shopping with us!')
        ];
    }
    
    /**
     * Get sales report
     */
    public function getSalesReport($startDate, $endDate) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    DATE(created_at) as period,
                    COUNT(*) as total_transactions,
                    SUM(total_amount) as total_sales,
                    SUM(tax_amount) as total_tax,
                    SUM(discount_amount) as total_discount,
                    SUM(subtotal) as subtotal,
                    AVG(total_amount) as avg_sale
                FROM {$this->table}
                WHERE DATE(created_at) BETWEEN ? AND ?
                AND status = 'completed'
                GROUP BY DATE(created_at)
                ORDER BY DATE(created_at) ASC
            ");
            
            $stmt->execute([$startDate, $endDate]);
            $results = $stmt->fetchAll();
            
            // Calculate profit
            foreach ($results as &$row) {
                $profitStmt = $this->db->prepare("
                    SELECT SUM((si.unit_price - si.cost_price) * si.quantity) as profit
                    FROM sale_items si
                    JOIN sales s ON si.sale_id = s.id
                    WHERE DATE(s.created_at) = ?
                    AND s.status = 'completed'
                ");
                $profitStmt->execute([$row['period']]);
                $profitResult = $profitStmt->fetch();
                $row['total_profit'] = $profitResult['profit'] ?? 0;
            }
            
            return $results;
            
        } catch (Exception $e) {
            error_log("Get sales report error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get top selling products
     */
    public function getTopSellingProducts($limit = 10, $startDate = null, $endDate = null) {
        try {
            $where = "s.status = 'completed'";
            $params = [];
            
            if ($startDate && $endDate) {
                $where .= " AND DATE(s.created_at) BETWEEN ? AND ?";
                $params[] = $startDate;
                $params[] = $endDate;
            }
            
            $params[] = $limit;
            
            $stmt = $this->db->prepare("
                SELECT 
                    si.product_id,
                    si.product_name as name,
                    p.sku,
                    c.name as category,
                    SUM(si.quantity) as units_sold,
                    SUM(si.total_price) as revenue,
                    SUM((si.unit_price - si.cost_price) * si.quantity) as profit
                FROM sale_items si
                JOIN sales s ON si.sale_id = s.id
                LEFT JOIN products p ON si.product_id = p.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE $where
                GROUP BY si.product_id, si.product_name, p.sku, c.name
                ORDER BY units_sold DESC
                LIMIT ?
            ");
            
            $stmt->execute($params);
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            error_log("Get top products error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get seller performance
     */
    public function getSellerPerformance($startDate = null, $endDate = null) {
        try {
            $where = "s.status = 'completed'";
            $params = [];
            
            if ($startDate && $endDate) {
                $where .= " AND DATE(s.created_at) BETWEEN ? AND ?";
                $params[] = $startDate;
                $params[] = $endDate;
            }
            
            $stmt = $this->db->prepare("
                SELECT 
                    u.id,
                    u.username,
                    u.full_name,
                    COUNT(s.id) as total_sales,
                    SUM(s.total_amount) as total_revenue,
                    AVG(s.total_amount) as avg_sale,
                    MAX(s.total_amount) as highest_sale,
                    MIN(s.total_amount) as lowest_sale
                FROM users u
                LEFT JOIN sales s ON u.id = s.seller_id AND $where
                WHERE u.status = 'active'
                GROUP BY u.id, u.username, u.full_name
                ORDER BY total_revenue DESC
            ");
            
            $stmt->execute($params);
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            error_log("Get seller performance error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get today's summary
     */
    public function getTodaySummary() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    COUNT(*) as total_sales,
                    SUM(total_amount) as total_revenue,
                    SUM(CASE WHEN payment_method = 'cash' THEN total_amount ELSE 0 END) as cash_sales,
                    SUM(CASE WHEN payment_method = 'mpesa' THEN total_amount ELSE 0 END) as mpesa_sales,
                    SUM(CASE WHEN payment_method = 'mpesa_manual' THEN total_amount ELSE 0 END) as mpesa_manual_sales,
                    AVG(total_amount) as avg_sale,
                    MAX(total_amount) as highest_sale
                FROM {$this->table}
                WHERE DATE(created_at) = CURDATE()
                AND status = 'completed'
            ");
            
            return $stmt->fetch();
            
        } catch (Exception $e) {
            error_log("Get today summary error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Search sales
     */
    public function search($query) {
        try {
            $stmt = $this->db->prepare("
                SELECT s.*, u.full_name as seller_name
                FROM {$this->table} s
                LEFT JOIN users u ON s.seller_id = u.id
                WHERE s.invoice_no LIKE ?
                OR s.customer_name LIKE ?
                OR s.customer_phone LIKE ?
                ORDER BY s.created_at DESC
                LIMIT 50
            ");
            
            $searchTerm = '%' . $query . '%';
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
            
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            error_log("Search sales error: " . $e->getMessage());
            return [];
        }
    }
    
    // Helper methods
    
    private function generateInvoiceNumber() {
        $prefix = 'INV';
        $date = date('Ymd');
        $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        return $prefix . $date . $random;
    }
    
    private function getProductById($id) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
