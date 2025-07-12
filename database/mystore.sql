
CREATE DATABASE IF NOT EXISTS `mystore`;
USE `mystore`;

-- Table structure for table `admin_table`

CREATE TABLE `admin_table` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_name` varchar(100) NOT NULL,
  `admin_email` varchar(100) NOT NULL,
  `admin_password` varchar(255) NOT NULL,
  `admin_image` text NOT NULL,
  `register_date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`admin_id`)
);


-- Table structure for table `brands`
CREATE TABLE `brands` (
  `brand_id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_title` varchar(100) NOT NULL,
  PRIMARY KEY (`brand_id`)
);

INSERT INTO `brands` VALUES
(1, 'Samsung'),
(2, 'Nike'),
(3, 'LG'),
(4, 'Apple'),
(5, 'Adidas');

-- Table structure for table `card_details`
CREATE TABLE `card_details` (
  `product_id` int(11) NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `quantity` int(100) NOT NULL
);



-- Table structure for table `categories`
CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_title` varchar(100) NOT NULL,
  PRIMARY KEY (`category_id`)
);

INSERT INTO `categories` VALUES
(1, 'Electronics'),
(2, 'Clothing'),
(3, 'Home Appliances'),
(4, 'Books'),
(5, 'Sports & Fitness');

-- Table structure for table `orders_pending`
CREATE TABLE `orders_pending` (
  `pending_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `invoice_number` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `order_status` varchar(255) NOT NULL,
  PRIMARY KEY (`pending_id`)
);



-- Table structure for table `payments`
CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `invoice_number` varchar(255) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `payment_mode` varchar(50) DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`payment_id`)
);



-- Table structure for table `products`
CREATE TABLE `products` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_title` varchar(100) NOT NULL,
  `product_description` varchar(255) NOT NULL,
  `product_keywords` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `product_image1` varchar(255) NOT NULL,
  `product_image2` varchar(255) NOT NULL,
  `product_image3` varchar(255) NOT NULL,
  `product_price` varchar(100) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(100) NOT NULL,
  PRIMARY KEY (`product_id`)
);


-- Table structure for table `user_orders`
CREATE TABLE `user_orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `amount_due` int(11) DEFAULT NULL,
  `invoice_number` int(11) DEFAULT NULL,
  `total_products` int(11) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `order_status` varchar(255) DEFAULT NULL,
  `payment_method` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`order_id`)
);


-- Table structure for table `user_table`
CREATE TABLE `user_table` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(100) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_image` text NOT NULL,
  `user_ip` varchar(100) NOT NULL,
  `user_address` varchar(255) NOT NULL,
  `user_mobile` varchar(20) NOT NULL,
  `register_date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`)
);
