
CREATE DATABASE IF NOT EXISTS `mystore`;
USE `mystore`;

-- ─── admin_table ─────────────────────────────────────────────────────────────
CREATE TABLE `admin_table` (
  `admin_id`      int(11)      NOT NULL AUTO_INCREMENT,
  `admin_name`    varchar(100) NOT NULL,
  `admin_email`   varchar(100) NOT NULL,
  `admin_password` varchar(255) NOT NULL,
  `admin_image`   text         NOT NULL,
  `register_date` datetime     NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `admin_name` (`admin_name`),
  UNIQUE KEY `admin_email` (`admin_email`)
);

-- ─── brands ──────────────────────────────────────────────────────────────────
CREATE TABLE `brands` (
  `brand_id`    int(11)      NOT NULL AUTO_INCREMENT,
  `brand_title` varchar(100) NOT NULL,
  PRIMARY KEY (`brand_id`)
);

INSERT INTO `brands` VALUES
(1, 'Samsung'),
(2, 'Nike'),
(3, 'LG'),
(4, 'Apple'),
(5, 'Adidas');

-- ─── card_details ────────────────────────────────────────────────────────────
-- Added PRIMARY KEY (auto-increment id) and a UNIQUE constraint to prevent
-- duplicate cart rows for the same product + IP combination.
CREATE TABLE `card_details` (
  `cart_id`    int(11)      NOT NULL AUTO_INCREMENT,
  `product_id` int(11)      NOT NULL,
  `ip_address` varchar(45)  NOT NULL,
  `quantity`   int(11)      NOT NULL DEFAULT 1,
  PRIMARY KEY (`cart_id`),
  UNIQUE KEY `uq_cart_item` (`product_id`, `ip_address`)
);

-- ─── categories ──────────────────────────────────────────────────────────────
CREATE TABLE `categories` (
  `category_id`    int(11)      NOT NULL AUTO_INCREMENT,
  `category_title` varchar(100) NOT NULL,
  PRIMARY KEY (`category_id`)
);

INSERT INTO `categories` VALUES
(1, 'Electronics'),
(2, 'Clothing'),
(3, 'Home Appliances'),
(4, 'Books'),
(5, 'Sports & Fitness');

-- ─── orders_pending ──────────────────────────────────────────────────────────
CREATE TABLE `orders_pending` (
  `pending_id`     int(11)      NOT NULL AUTO_INCREMENT,
  `user_id`        int(11)      NOT NULL,
  `invoice_number` int(11)      NOT NULL,
  `product_id`     int(11)      NOT NULL,
  `quantity`       int(11)      NOT NULL DEFAULT 1,
  `order_status`   varchar(50)  NOT NULL,
  PRIMARY KEY (`pending_id`)
);

-- ─── payments ────────────────────────────────────────────────────────────────
CREATE TABLE `payments` (
  `payment_id`     int(11)        NOT NULL AUTO_INCREMENT,
  `order_id`       int(11)        DEFAULT NULL,
  `invoice_number` varchar(255)   DEFAULT NULL,
  `amount`         decimal(10,2)  DEFAULT NULL,
  `payment_mode`   varchar(50)    DEFAULT NULL,
  `payment_date`   timestamp      NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`payment_id`)
);

-- ─── products ────────────────────────────────────────────────────────────────
-- product_price changed from varchar(100) to decimal(10,2) for correct arithmetic.
CREATE TABLE `products` (
  `product_id`          int(11)        NOT NULL AUTO_INCREMENT,
  `product_title`       varchar(100)   NOT NULL,
  `product_description` varchar(255)   NOT NULL,
  `product_keywords`    varchar(255)   NOT NULL,
  `category_id`         int(11)        NOT NULL,
  `brand_id`            int(11)        NOT NULL,
  `product_image1`      varchar(255)   NOT NULL,
  `product_image2`      varchar(255)   NOT NULL,
  `product_image3`      varchar(255)   NOT NULL,
  `product_price`       decimal(10,2)  NOT NULL,
  `date`                timestamp      NOT NULL DEFAULT current_timestamp(),
  `status`              varchar(20)    NOT NULL DEFAULT 'true',
  PRIMARY KEY (`product_id`)
);

-- ─── user_orders ─────────────────────────────────────────────────────────────
-- amount_due changed to decimal(10,2) to match product_price precision.
CREATE TABLE `user_orders` (
  `order_id`       int(11)        NOT NULL AUTO_INCREMENT,
  `user_id`        int(11)        NOT NULL,
  `amount_due`     decimal(10,2)  DEFAULT NULL,
  `invoice_number` int(11)        DEFAULT NULL,
  `total_products` int(11)        DEFAULT NULL,
  `order_date`     timestamp      NOT NULL DEFAULT current_timestamp(),
  `order_status`   varchar(50)    DEFAULT NULL,
  `payment_method` varchar(20)    DEFAULT NULL,
  PRIMARY KEY (`order_id`)
);

-- ─── user_table ──────────────────────────────────────────────────────────────
CREATE TABLE `user_table` (
  `user_id`       int(11)      NOT NULL AUTO_INCREMENT,
  `user_name`     varchar(100) NOT NULL,
  `user_email`    varchar(100) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_image`    text         NOT NULL,
  `user_ip`       varchar(45)  NOT NULL,
  `user_address`  varchar(255) NOT NULL,
  `user_mobile`   varchar(20)  NOT NULL,
  `register_date` datetime     NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_email` (`user_email`)
);

-- ─── Migration script (run on existing databases) ────────────────────────────
-- Uncomment and run these ALTER statements if upgrading an existing database
-- rather than creating fresh:
--
-- ALTER TABLE `products`
--   MODIFY `product_price` decimal(10,2) NOT NULL;
--
-- ALTER TABLE `user_orders`
--   MODIFY `amount_due` decimal(10,2) DEFAULT NULL;
--
-- ALTER TABLE `payments`
--   MODIFY `amount` decimal(10,2) DEFAULT NULL;
--
-- ALTER TABLE `card_details`
--   ADD COLUMN `cart_id` int(11) NOT NULL AUTO_INCREMENT FIRST,
--   ADD PRIMARY KEY (`cart_id`),
--   ADD UNIQUE KEY `uq_cart_item` (`product_id`, `ip_address`),
--   MODIFY `ip_address` varchar(45) NOT NULL,
--   MODIFY `quantity` int(11) NOT NULL DEFAULT 1;
--
-- ALTER TABLE `user_table`
--   ADD UNIQUE KEY `user_email` (`user_email`);
