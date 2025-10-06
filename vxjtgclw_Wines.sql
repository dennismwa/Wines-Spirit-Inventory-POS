-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 06, 2025 at 04:34 PM
-- Server version: 8.0.42
-- PHP Version: 8.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vxjtgclw_Wines`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `parent_id` int DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `parent_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Whisky', 'All types of whisky', NULL, 'active', '2025-10-06 12:42:40', '2025-10-06 12:42:40'),
(2, 'Wine', 'Red, white and rosé wines', NULL, 'active', '2025-10-06 12:42:40', '2025-10-06 12:42:40'),
(3, 'Beer', 'Lagers, ales, and craft beers', NULL, 'active', '2025-10-06 12:42:40', '2025-10-06 12:42:40'),
(4, 'Vodka', 'Premium vodkas', NULL, 'active', '2025-10-06 12:42:40', '2025-10-06 12:42:40'),
(5, 'Gin', 'Gins and gin-based spirits', NULL, 'active', '2025-10-06 12:42:40', '2025-10-06 12:42:40'),
(6, 'Rum', 'White and dark rums', NULL, 'active', '2025-10-06 12:42:40', '2025-10-06 12:42:40'),
(7, 'Brandy', 'Cognacs and brandies', NULL, 'active', '2025-10-06 12:42:40', '2025-10-06 12:42:40'),
(8, 'Tequila', 'Tequilas and mezcals', NULL, 'active', '2025-10-06 12:42:40', '2025-10-06 12:42:40'),
(9, 'Champagne', 'Champagnes and sparkling wines', NULL, 'active', '2025-10-06 12:42:40', '2025-10-06 12:42:40'),
(10, 'Liqueurs', 'Flavored liqueurs and cordials', NULL, 'active', '2025-10-06 12:42:40', '2025-10-06 12:42:40'),
(11, 'Soft Drinks', 'Mixers and soft beverages', NULL, 'active', '2025-10-06 12:42:40', '2025-10-06 12:42:40'),
(12, 'Accessories', 'Glasses, openers, and bar tools', NULL, 'active', '2025-10-06 12:42:40', '2025-10-06 12:42:40');

-- --------------------------------------------------------

--
-- Stand-in structure for view `daily_sales_summary`
-- (See below for the actual view)
--
CREATE TABLE `daily_sales_summary` (
`sale_date` date
,`total_transactions` bigint
,`active_sellers` bigint
,`total_sales` decimal(32,2)
,`total_tax` decimal(32,2)
,`total_discount` decimal(32,2)
,`total_profit` decimal(32,2)
);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `barcode` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `category_id` int DEFAULT NULL,
  `supplier_id` int DEFAULT NULL,
  `brand` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'piece',
  `cost_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `selling_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `quantity` int NOT NULL DEFAULT '0',
  `reorder_level` int DEFAULT '10',
  `tax_rate` decimal(5,2) DEFAULT '0.00',
  `status` enum('active','inactive','out_of_stock') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `product_performance`
-- (See below for the actual view)
--
CREATE TABLE `product_performance` (
`id` int
,`name` varchar(200)
,`sku` varchar(50)
,`category` varchar(100)
,`current_stock` int
,`selling_price` decimal(10,2)
,`units_sold` decimal(32,0)
,`revenue` decimal(32,2)
,`total_profit` decimal(32,2)
);

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `variant_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cost_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `selling_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `quantity` int NOT NULL DEFAULT '0',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int NOT NULL,
  `invoice_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `change_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_method` enum('cash','mpesa','mpesa_manual','credit','bank') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `payment_reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('completed','pending','cancelled','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `seller_id` int NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `sale_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `sales`
--
DELIMITER $$
CREATE TRIGGER `after_sale_update` AFTER UPDATE ON `sales` FOR EACH ROW BEGIN
    IF OLD.status = 'completed' AND NEW.status = 'cancelled' THEN
        -- Restore product quantities
        UPDATE products p
        JOIN sale_items si ON p.id = si.product_id
        SET p.quantity = p.quantity + si.quantity
        WHERE si.sale_id = NEW.id AND si.variant_id IS NULL;
        
        UPDATE product_variants pv
        JOIN sale_items si ON pv.id = si.variant_id
        SET pv.quantity = pv.quantity + si.quantity
        WHERE si.sale_id = NEW.id AND si.variant_id IS NOT NULL;
        
        -- Log the stock movement
        INSERT INTO stock_movements (
            product_id, variant_id, movement_type, quantity,
            reference_type, reference_id, previous_stock, new_stock, user_id
        )
        SELECT 
            si.product_id, si.variant_id, 'return', si.quantity,
            'sale_cancellation', NEW.id,
            CASE 
                WHEN si.variant_id IS NULL THEN p.quantity - si.quantity
                ELSE pv.quantity - si.quantity
            END,
            CASE 
                WHEN si.variant_id IS NULL THEN p.quantity
                ELSE pv.quantity
            END,
            NEW.seller_id
        FROM sale_items si
        LEFT JOIN products p ON si.product_id = p.id AND si.variant_id IS NULL
        LEFT JOIN product_variants pv ON si.variant_id = pv.id
        WHERE si.sale_id = NEW.id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `id` int NOT NULL,
  `sale_id` int NOT NULL,
  `product_id` int NOT NULL,
  `variant_id` int DEFAULT NULL,
  `product_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `cost_price` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) DEFAULT '0.00',
  `discount_amount` decimal(10,2) DEFAULT '0.00',
  `total_price` decimal(10,2) NOT NULL,
  `profit` decimal(10,2) GENERATED ALWAYS AS (((`unit_price` - `cost_price`) * `quantity`)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `sale_items`
--
DELIMITER $$
CREATE TRIGGER `after_sale_item_insert` AFTER INSERT ON `sale_items` FOR EACH ROW BEGIN
    IF NEW.variant_id IS NULL THEN
        UPDATE products 
        SET quantity = quantity - NEW.quantity 
        WHERE id = NEW.product_id;
        
        INSERT INTO stock_movements (
            product_id, movement_type, quantity, 
            reference_type, reference_id, 
            previous_stock, new_stock, user_id
        )
        SELECT 
            NEW.product_id, 'sale', -NEW.quantity,
            'sale', NEW.sale_id,
            p.quantity + NEW.quantity, p.quantity,
            s.seller_id
        FROM products p
        JOIN sales s ON s.id = NEW.sale_id
        WHERE p.id = NEW.product_id;
    ELSE
        UPDATE product_variants 
        SET quantity = quantity - NEW.quantity 
        WHERE id = NEW.variant_id;
        
        INSERT INTO stock_movements (
            product_id, variant_id, movement_type, quantity, 
            reference_type, reference_id, 
            previous_stock, new_stock, user_id
        )
        SELECT 
            NEW.product_id, NEW.variant_id, 'sale', -NEW.quantity,
            'sale', NEW.sale_id,
            pv.quantity + NEW.quantity, pv.quantity,
            s.seller_id
        FROM product_variants pv
        JOIN sales s ON s.id = NEW.sale_id
        WHERE pv.id = NEW.variant_id;
    END IF;
    
    -- Update product status if out of stock
    UPDATE products 
    SET status = CASE 
        WHEN quantity <= 0 THEN 'out_of_stock' 
        ELSE status 
    END
    WHERE id = NEW.product_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `seller_performance`
-- (See below for the actual view)
--
CREATE TABLE `seller_performance` (
`id` int
,`full_name` varchar(100)
,`username` varchar(50)
,`sale_date` date
,`transactions` bigint
,`total_sales` decimal(32,2)
,`avg_transaction` decimal(14,6)
);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `session_token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `last_activity` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int NOT NULL,
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `setting_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'text',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `updated_at`) VALUES
(1, 'company_name', 'Wines & Spirits Shop', 'text', '2025-10-06 12:42:40'),
(2, 'company_address', '', 'text', '2025-10-06 12:42:40'),
(3, 'company_phone', '', 'text', '2025-10-06 12:42:40'),
(4, 'company_email', '', 'email', '2025-10-06 12:42:40'),
(5, 'tax_rate', '16', 'number', '2025-10-06 12:42:40'),
(6, 'currency', 'KES', 'text', '2025-10-06 12:42:40'),
(7, 'currency_symbol', 'KSh', 'text', '2025-10-06 12:42:40'),
(8, 'timezone', 'Africa/Nairobi', 'text', '2025-10-06 12:42:40'),
(9, 'date_format', 'DD/MM/YYYY', 'text', '2025-10-06 12:42:40'),
(10, 'low_stock_alert', '10', 'number', '2025-10-06 12:42:40'),
(11, 'receipt_footer', 'Thank you for shopping with us!', 'text', '2025-10-06 12:42:40'),
(12, 'enable_barcode', '1', 'boolean', '2025-10-06 12:42:40'),
(13, 'auto_logout_time', '30', 'number', '2025-10-06 12:42:40'),
(14, 'mpesa_till_number', '', 'text', '2025-10-06 12:42:40'),
(15, 'backup_enabled', '1', 'boolean', '2025-10-06 12:42:40');

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `variant_id` int DEFAULT NULL,
  `movement_type` enum('purchase','sale','adjustment','return','transfer') COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `reference_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` int DEFAULT NULL,
  `previous_stock` int NOT NULL,
  `new_stock` int NOT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `user_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('owner','seller') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'seller',
  `status` enum('active','inactive','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `last_activity` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `phone`, `role`, `status`, `last_login`, `last_activity`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@winesandspirits.co.ke', '$2y$10$YqVqP6K8.P0GzB5/oGGqhOj5H9eGDbIpzqFJwKqQH8V0K/nFjKPGm', 'System Administrator', NULL, 'owner', 'active', NULL, NULL, '2025-10-06 12:42:40', '2025-10-06 12:42:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_module` (`module`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_activity_composite` (`user_id`,`module`,`created_at`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_parent` (`parent_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD UNIQUE KEY `barcode` (`barcode`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_supplier` (`supplier_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_quantity` (`quantity`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `fk_product_user` (`created_by`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `barcode` (`barcode`),
  ADD KEY `idx_product` (`product_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_no` (`invoice_no`),
  ADD KEY `idx_seller` (`seller_id`),
  ADD KEY `idx_sale_date` (`sale_date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_payment_method` (`payment_method`),
  ADD KEY `idx_sales_date_range` (`sale_date`,`status`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sale` (`sale_id`),
  ADD KEY `idx_product` (`product_id`),
  ADD KEY `idx_variant` (`variant_id`),
  ADD KEY `idx_sale_items_composite` (`sale_id`,`product_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_token` (`session_token`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_last_activity` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product` (`product_id`),
  ADD KEY `idx_variant` (`variant_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_movement_type` (`movement_type`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_stock_movements_date` (`created_at`,`movement_type`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_role_status` (`role`,`status`),
  ADD KEY `idx_last_activity` (`last_activity`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

-- --------------------------------------------------------

--
-- Structure for view `daily_sales_summary`
--
DROP TABLE IF EXISTS `daily_sales_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`vxjtgclw`@`localhost` SQL SECURITY DEFINER VIEW `daily_sales_summary`  AS SELECT cast(`s`.`created_at` as date) AS `sale_date`, count(distinct `s`.`id`) AS `total_transactions`, count(distinct `s`.`seller_id`) AS `active_sellers`, sum(`s`.`total_amount`) AS `total_sales`, sum(`s`.`tax_amount`) AS `total_tax`, sum(`s`.`discount_amount`) AS `total_discount`, sum(`si`.`profit`) AS `total_profit` FROM (`sales` `s` join `sale_items` `si` on((`s`.`id` = `si`.`sale_id`))) WHERE (`s`.`status` = 'completed') GROUP BY cast(`s`.`created_at` as date) ;

-- --------------------------------------------------------

--
-- Structure for view `product_performance`
--
DROP TABLE IF EXISTS `product_performance`;

CREATE ALGORITHM=UNDEFINED DEFINER=`vxjtgclw`@`localhost` SQL SECURITY DEFINER VIEW `product_performance`  AS SELECT `p`.`id` AS `id`, `p`.`name` AS `name`, `p`.`sku` AS `sku`, `c`.`name` AS `category`, `p`.`quantity` AS `current_stock`, `p`.`selling_price` AS `selling_price`, coalesce(sum(`si`.`quantity`),0) AS `units_sold`, coalesce(sum(`si`.`total_price`),0) AS `revenue`, coalesce(sum(`si`.`profit`),0) AS `total_profit` FROM (((`products` `p` left join `categories` `c` on((`p`.`category_id` = `c`.`id`))) left join `sale_items` `si` on((`p`.`id` = `si`.`product_id`))) left join `sales` `s` on(((`si`.`sale_id` = `s`.`id`) and (`s`.`status` = 'completed')))) GROUP BY `p`.`id` ;

-- --------------------------------------------------------

--
-- Structure for view `seller_performance`
--
DROP TABLE IF EXISTS `seller_performance`;

CREATE ALGORITHM=UNDEFINED DEFINER=`vxjtgclw`@`localhost` SQL SECURITY DEFINER VIEW `seller_performance`  AS SELECT `u`.`id` AS `id`, `u`.`full_name` AS `full_name`, `u`.`username` AS `username`, cast(`s`.`created_at` as date) AS `sale_date`, count(`s`.`id`) AS `transactions`, sum(`s`.`total_amount`) AS `total_sales`, avg(`s`.`total_amount`) AS `avg_transaction` FROM (`users` `u` join `sales` `s` on((`u`.`id` = `s`.`seller_id`))) WHERE (`s`.`status` = 'completed') GROUP BY `u`.`id`, cast(`s`.`created_at` as date) ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `fk_log_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_product_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_product_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `fk_variant_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `fk_sale_seller` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `fk_item_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `fk_item_sale` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_item_variant` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`);

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `fk_session_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `fk_movement_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `fk_movement_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_movement_variant` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
