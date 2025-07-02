-- UI theme inspired by Maserati and implemented with the help of ChatGPT/Cursor AI assistant, 2025.

-- New Arrivals UI in src/views/home/components/new_arrivals.php implemented with the help of ChatGPT/Cursor AI assistant, 2025.

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 15, 2025 at 11:37 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kt_phones`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `address_id` varchar(50) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `address_line1` varchar(100) NOT NULL,
  `address_line2` varchar(100) DEFAULT NULL,
  `country` varchar(50) NOT NULL,
  `district` varchar(50) DEFAULT NULL,
  `sector` varchar(20) DEFAULT NULL,
  `cell` varchar(50) DEFAULT NULL,
  `phone_number` varchar(20) NOT NULL,
  `address_type` enum('shipping','billing','both') NOT NULL DEFAULT 'both',
  `is_default` tinyint(1) DEFAULT 0,
  `is_guest` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`address_id`, `user_id`, `email`, `first_name`, `last_name`, `address_line1`, `address_line2`, `country`, `district`, `sector`, `cell`, `phone_number`, `address_type`, `is_default`, `is_guest`, `created_at`, `updated_at`) VALUES
('ADDR684d559d4c166', NULL, 'regejejoso@mailinator.com', 'Alec', 'Pickett', '75 New Parkway', 'Deleniti quo minus d', 'Proident assumenda ', 'Quo quia esse mollit', 'Aliquid ea libero as', 'Cumque debitis ullam', '+1 (536) 851-3325', 'both', 0, 1, '2025-06-14 12:57:33', '2025-06-14 12:57:33'),
('ADDR684d559d4c167', 2, 'peter@gmail.com', 'John', 'Peter', 'Kigali, Nyakabanda', 'Kigali, Nyarugenge TCB', 'Rwanda', 'Nyarugenge', 'Nyakabanda I', 'Nyakabanda', '0798874111', 'both', 1, 0, '2025-06-14 12:57:33', '2025-06-14 23:16:49'),
('ADDR684dfe207327e', NULL, 'cisofaxe@mailinator.com', 'Wynne', 'Morrow', '36 Green Hague Extension', 'Harum dolore ut quia', 'Sit non dolorem et ', 'Labore deleniti volu', 'Facilis quod quis ve', 'Expedita tempor pari', '+1 (719) 673-1017', 'both', 0, 1, '2025-06-15 00:56:32', '2025-06-15 00:56:32');

-- --------------------------------------------------------

--
-- Table structure for table `attribute_type`
--

CREATE TABLE `attribute_type` (
  `attribute_type_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attribute_type`
--

INSERT INTO `attribute_type` (`attribute_type_id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Storage', 'Internal storage capacity', '2025-05-10 11:11:56', '2025-05-10 11:11:56'),
(2, 'RAM', 'Random Access Memory', '2025-05-10 11:11:56', '2025-05-10 11:11:56'),
(3, 'Screen Size', 'Display size in inches', '2025-05-10 11:11:56', '2025-05-10 11:11:56'),
(4, 'Color', 'Device color', '2025-05-10 11:11:56', '2025-05-10 11:11:56'),
(5, 'Battery', 'Battery capacity in mAh', '2025-05-10 11:11:56', '2025-05-10 11:11:56'),
(6, 'Processor', 'CPU model', '2025-05-10 11:11:56', '2025-05-10 11:11:56'),
(7, 'OS', 'Operating system', '2025-05-10 11:11:56', '2025-05-10 11:11:56'),
(8, 'Camera', 'Camera specifications', '2025-05-10 11:11:56', '2025-05-10 11:11:56');

-- --------------------------------------------------------

--
-- Table structure for table `attribute_value`
--

CREATE TABLE `attribute_value` (
  `attribute_value_id` int(11) NOT NULL,
  `attribute_type_id` int(11) NOT NULL,
  `value` varchar(100) NOT NULL CHECK (char_length(`value`) > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attribute_value`
--

INSERT INTO `attribute_value` (`attribute_value_id`, `attribute_type_id`, `value`) VALUES
(2, 1, '128GB'),
(5, 1, '1TB'),
(3, 1, '256GB'),
(4, 1, '512GB'),
(1, 1, '64GB'),
(9, 2, '12GB'),
(10, 2, '16GB'),
(6, 2, '4GB'),
(7, 2, '6GB'),
(8, 2, '8GB'),
(11, 3, '5.8\"'),
(12, 3, '6.1\"'),
(13, 3, '6.4\"'),
(14, 3, '6.7\"'),
(15, 3, '6.9\"'),
(16, 4, 'Black'),
(18, 4, 'Blue'),
(21, 4, 'Gold'),
(20, 4, 'Green'),
(19, 4, 'Red'),
(17, 4, 'White'),
(22, 5, '3000mAh'),
(23, 5, '4000mAh'),
(24, 5, '5000mAh'),
(25, 5, '6000mAh'),
(27, 6, 'A16 Bionic'),
(28, 6, 'Exynos 2200'),
(29, 6, 'Google Tensor'),
(26, 6, 'Snapdragon 8 Gen 2'),
(30, 7, 'Android 13'),
(31, 7, 'iOS 16'),
(32, 7, 'iOS 17'),
(35, 8, '108MP'),
(33, 8, '12MP'),
(34, 8, '48MP'),
(37, 8, 'Quad Camera'),
(36, 8, 'Triple Camera');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `brand_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`brand_id`, `name`, `description`, `logo_url`) VALUES
(1, 'Samsung', 'South Korean multinational electronics company', 'https://download.logo.wine/logo/Samsung/Samsung-Logo.wine.png'),
(2, 'Apple', 'American multinational technology company', 'https://1000logos.net/wp-content/uploads/2017/02/Apple-Logosu.png'),
(3, 'Xiaomi', 'Chinese electronics company', 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/ae/Xiaomi_logo_%282021-%29.svg/512px-Xiaomi_logo_%282021-%29.svg.png'),
(4, 'Google', 'American multinational technology company', 'https://cdn1.iconfinder.com/data/icons/google-s-logo/150/Google_Icons-09-512.png'),
(5, 'Belkin', 'American manufacturer of consumer electronics', 'https://1000logos.net/wp-content/uploads/2020/09/Belkin-Logo.png'),
(6, 'Anker', 'Chinese electronics company', 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9c/Anker_logo.svg/2560px-Anker_logo.svg.png');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('active','converted','abandoned') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `status`, `created_at`, `updated_at`) VALUES
('QOIG9kLRoJ9Ni9jG5v16', 2, 'active', '2025-06-07 14:00:10', '2025-06-07 14:00:10'),
('ZFZC36SZUsInDIE9KPIO', 3, 'active', '2025-06-08 23:17:02', '2025-06-08 23:17:02');

-- --------------------------------------------------------

--
-- Table structure for table `cart_item`
--

CREATE TABLE `cart_item` (
  `cart_item_id` int(11) NOT NULL,
  `cart_id` varchar(50) NOT NULL,
  `product_id` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1 CHECK (`quantity` > 0),
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_item`
--

INSERT INTO `cart_item` (`cart_item_id`, `cart_id`, `product_id`, `quantity`, `unit_price`) VALUES
(1, 'jW6G4fE0YHkZGOJu7l3S', 'Q6OxEAysxDdTXHiNdDyi', 1, 0.00),
(6, 'ZFZC36SZUsInDIE9KPIO', 'LcrWDxLVIhi2tqukk5sp', 1, 0.00),
(7, 'ZFZC36SZUsInDIE9KPIO', '2vbaCWVBZAMipZwpZXgo', 3, 0.00),
(17, '3iEFp428At1wurftj0Yv', 'LcrWDxLVIhi2tqukk5sp', 1, 0.00),
(19, 'QOIG9kLRoJ9Ni9jG5v16', 'AMHANe6cVJ8zCUQxzvs2', 1, 420000.00),
(20, 'QOIG9kLRoJ9Ni9jG5v16', 'Q6OxEAysxDdTXHiNdDyi', 1, 420000.00),
(21, 'QOIG9kLRoJ9Ni9jG5v16', '2vbaCWVBZAMipZwpZXgo', 2, 860000.00),
(22, 'QOIG9kLRoJ9Ni9jG5v16', 'LcrWDxLVIhi2tqukk5sp', 1, 420000.00);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `parent_category_id` int(11) DEFAULT NULL,
  `slug` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `description`, `parent_category_id`, `slug`, `is_active`) VALUES
(1, 'Samsung', 'All smartphones', NULL, 'samsung', 1),
(2, 'Accessories', 'Mobile accessories', NULL, 'accessories', 1),
(3, 'Google', 'Samsung smartphones', 1, 'google', 1),
(4, 'Apple', 'Apple iPhones', 1, 'apple', 1),
(5, 'Cases', 'Phone cases and covers', 2, 'cases', 1),
(6, 'Chargers', 'Charging accessories', 2, 'chargers', 1),
(7, 'Screen Protectors', 'Mobile screen protectors', 2, 'screen-protectors', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` varchar(50) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `guest_email` varchar(100) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `order_date` datetime DEFAULT current_timestamp(),
  `shipping_address_id` varchar(50) NOT NULL,
  `billing_address_id` varchar(50) NOT NULL,
  `is_guest_order` tinyint(1) DEFAULT 0,
  `payment_method` varchar(50) NOT NULL,
  `payment_status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `shipping_method` varchar(50) NOT NULL,
  `shipping_cost` decimal(8,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `tracking_number` varchar(100) DEFAULT NULL,
  `session_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `guest_email`, `total_amount`, `status`, `order_date`, `shipping_address_id`, `billing_address_id`, `is_guest_order`, `payment_method`, `payment_status`, `shipping_method`, `shipping_cost`, `tax_amount`, `tracking_number`, `session_id`) VALUES
('ORD684d559d4e6da', NULL, 'regejejoso@mailinator.com', 2796600.00, 'pending', '2025-06-14 12:57:33', 'ADDR684d559d4c166', 'ADDR684d559d4c166', 1, 'cash', 'pending', 'standard', 3000.00, 453600.00, NULL, NULL),
('ORD684d864c877f1', 2, NULL, 2976600.00, 'pending', '2025-06-14 16:25:16', '0', 'ADDR684d559d4c167', 0, 'cash', 'pending', '0', 3000.00, 453600.00, NULL, NULL),
('ORD684d8837b2f59', 2, NULL, 994200.00, 'pending', '2025-06-14 16:33:27', '0', 'ADDR684d559d4c167', 0, 'cash', 'pending', '0', 3000.00, 151200.00, NULL, NULL),
('ORD684d8b2a9e52a', 2, NULL, 1513400.00, 'pending', '2025-06-14 16:46:02', '0', 'ADDR684d559d4c167', 0, 'cash', 'pending', '0', 3000.00, 230400.00, NULL, NULL),
('ORD684da05d2048c', 2, NULL, 5053400.00, 'pending', '2025-06-14 18:16:29', '0', 'ADDR684d559d4c167', 0, 'cash', 'pending', '0', 3000.00, 770400.00, NULL, NULL),
('ORD684dfc021675b', 2, NULL, 994200.00, 'pending', '2025-06-15 00:47:30', '0', 'ADDR684d559d4c167', 0, 'cash', 'pending', '0', 3000.00, 151200.00, NULL, NULL),
('ORD684dfdf6a7f01', 2, NULL, 1017800.00, 'pending', '2025-06-15 00:55:50', '0', 'ADDR684d559d4c167', 0, 'cash', 'pending', '0', 3000.00, 154800.00, NULL, NULL),
('ORD684dfe2073b2c', NULL, 'cisofaxe@mailinator.com', 1985400.00, 'pending', '2025-06-15 00:56:32', 'ADDR684dfe207327e', 'ADDR684dfe207327e', 1, 'cash', 'pending', '0', 3000.00, 302400.00, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `product_id` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `unit_price`, `subtotal`) VALUES
(1, 'ORD684d559d4e6da', 'AMHANe6cVJ8zCUQxzvs2', 1, 420000.00, 420000.00),
(2, 'ORD684d559d4e6da', 'LcrWDxLVIhi2tqukk5sp', 1, 420000.00, 420000.00),
(3, 'ORD684d559d4e6da', 'Q6OxEAysxDdTXHiNdDyi', 4, 420000.00, 1680000.00),
(4, 'ORD684d864c877f1', 'AMHANe6cVJ8zCUQxzvs2', 1, 420000.00, 420000.00),
(5, 'ORD684d864c877f1', 'LcrWDxLVIhi2tqukk5sp', 1, 420000.00, 420000.00),
(6, 'ORD684d864c877f1', 'Q6OxEAysxDdTXHiNdDyi', 4, 420000.00, 1680000.00),
(7, 'ORD684d8837b2f59', 'LcrWDxLVIhi2tqukk5sp', 1, 420000.00, 420000.00),
(8, 'ORD684d8837b2f59', 'Q6OxEAysxDdTXHiNdDyi', 1, 420000.00, 420000.00),
(9, 'ORD684d8b2a9e52a', '2vbaCWVBZAMipZwpZXgo', 1, 860000.00, 860000.00),
(10, 'ORD684d8b2a9e52a', 'LcrWDxLVIhi2tqukk5sp', 1, 420000.00, 420000.00),
(11, 'ORD684da05d2048c', '2vbaCWVBZAMipZwpZXgo', 4, 860000.00, 3440000.00),
(12, 'ORD684da05d2048c', 'LcrWDxLVIhi2tqukk5sp', 1, 420000.00, 420000.00),
(13, 'ORD684da05d2048c', 'Q6OxEAysxDdTXHiNdDyi', 1, 420000.00, 420000.00),
(14, 'ORD684dfc021675b', 'LcrWDxLVIhi2tqukk5sp', 1, 420000.00, 420000.00),
(15, 'ORD684dfc021675b', 'Q6OxEAysxDdTXHiNdDyi', 1, 420000.00, 420000.00),
(16, 'ORD684dfdf6a7f01', '2vbaCWVBZAMipZwpZXgo', 1, 860000.00, 860000.00),
(17, 'ORD684dfe2073b2c', 'AMHANe6cVJ8zCUQxzvs2', 4, 420000.00, 1680000.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `sku` varchar(50) NOT NULL,
  `base_price` decimal(10,2) NOT NULL CHECK (`base_price` >= 0),
  `discount_price` decimal(10,2) DEFAULT NULL CHECK (`discount_price` >= 0 and `discount_price` <= `base_price`),
  `category_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0 CHECK (`stock_quantity` >= 0),
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `published` enum('true','false') NOT NULL DEFAULT 'true',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `sku`, `base_price`, `discount_price`, `category_id`, `brand_id`, `stock_quantity`, `is_featured`, `is_active`, `published`, `created_at`, `updated_at`) VALUES
('2vbaCWVBZAMipZwpZXgo', 'Samsung s24', 'Samsung Galaxy S24 Ultra - Latest flagship smartphone with advanced AI features', 'SAM-S24-001', 890000.00, 860000.00, 1, 1, 111, 1, 1, 'true', '2025-05-14 20:00:04', '2025-06-15 00:55:50'),
('AMHANe6cVJ8zCUQxzvs2', 'iPhone 15', 'Google Pixel 7 - Advanced camera system with AI features', 'GP-P7-001', 450000.00, 420000.00, 4, 4, 15, 1, 1, 'true', '2025-05-14 19:42:58', '2025-06-15 03:41:53'),
('LcrWDxLVIhi2tqukk5sp', 'Google Pixel 7 Pro', 'Google Pixel 7 Pro - Premium Android experience with exceptional camera', 'GP-P7P-001', 450000.00, 420000.00, 3, 4, 15, 1, 1, 'true', '2025-05-23 13:24:57', '2025-06-15 00:47:30'),
('Q6OxEAysxDdTXHiNdDyi', 'Google Pixel 8', 'Google Pixel 8 - Latest Google smartphone with advanced features', 'GP-P8-001', 450000.00, 420000.00, 3, 4, 10, 1, 1, 'true', '2025-05-23 13:36:26', '2025-06-15 00:47:30');

-- --------------------------------------------------------

--
-- Table structure for table `product_attributes`
--

CREATE TABLE `product_attributes` (
  `product_attribute_id` int(11) NOT NULL,
  `product_id` varchar(50) NOT NULL,
  `attribute_type_id` int(11) NOT NULL,
  `attribute_value_id` int(11) NOT NULL,
  `attribute_created_at` datetime DEFAULT current_timestamp(),
  `attribute_updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_attributes`
--

INSERT INTO `product_attributes` (`product_attribute_id`, `product_id`, `attribute_type_id`, `attribute_value_id`, `attribute_created_at`, `attribute_updated_at`) VALUES
(9, 'AMHANe6cVJ8zCUQxzvs2', 1, 2, '2025-05-14 19:42:58', '2025-05-14 19:42:58'),
(10, 'AMHANe6cVJ8zCUQxzvs2', 2, 10, '2025-05-14 19:42:58', '2025-05-14 19:42:58'),
(11, 'AMHANe6cVJ8zCUQxzvs2', 4, 19, '2025-05-14 19:42:58', '2025-05-14 19:42:58'),
(12, 'AMHANe6cVJ8zCUQxzvs2', 7, 32, '2025-05-14 19:42:58', '2025-05-14 19:42:58'),
(13, '2vbaCWVBZAMipZwpZXgo', 1, 4, '2025-05-14 20:00:04', '2025-05-14 20:00:04'),
(14, '2vbaCWVBZAMipZwpZXgo', 2, 10, '2025-05-14 20:00:04', '2025-05-14 20:00:04'),
(15, '2vbaCWVBZAMipZwpZXgo', 4, 16, '2025-05-14 20:00:04', '2025-05-14 20:00:04'),
(16, '2vbaCWVBZAMipZwpZXgo', 5, 23, '2025-05-14 20:00:04', '2025-05-14 20:00:04'),
(17, '2vbaCWVBZAMipZwpZXgo', 7, 30, '2025-05-14 20:00:04', '2025-05-14 20:00:04'),
(26, 'Q6OxEAysxDdTXHiNdDyi', 1, 3, '2025-06-12 14:50:40', '2025-06-12 14:50:40'),
(27, 'Q6OxEAysxDdTXHiNdDyi', 2, 9, '2025-06-12 14:50:40', '2025-06-12 14:50:40'),
(28, 'Q6OxEAysxDdTXHiNdDyi', 3, 11, '2025-06-12 14:50:40', '2025-06-12 14:50:40'),
(29, 'Q6OxEAysxDdTXHiNdDyi', 4, 21, '2025-06-12 14:50:40', '2025-06-12 14:50:40'),
(30, 'Q6OxEAysxDdTXHiNdDyi', 5, 24, '2025-06-12 14:50:40', '2025-06-12 14:50:40'),
(31, 'Q6OxEAysxDdTXHiNdDyi', 6, 29, '2025-06-12 14:50:40', '2025-06-12 14:50:40'),
(32, 'Q6OxEAysxDdTXHiNdDyi', 7, 30, '2025-06-12 14:50:40', '2025-06-12 14:50:40'),
(33, 'Q6OxEAysxDdTXHiNdDyi', 8, 34, '2025-06-12 14:50:40', '2025-06-12 14:50:40');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `image_id` int(11) NOT NULL,
  `product_id` varchar(50) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_primary` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`image_id`, `product_id`, `image_url`, `alt_text`, `display_order`, `is_primary`) VALUES
(1, 'AMHANe6cVJ8zCUQxzvs2', 'https://raw.githubusercontent.com/hdpngworld/HPW/main/uploads/65038654434d0-iPhone%2015%20Pro%20Natural%20titanium%20png.png', 'Product Image 1', 1, 1),
(2, 'AMHANe6cVJ8zCUQxzvs2', 'https://www.greentelcom.ph/wp-content/uploads/2023/10/BLUE-TITANIUM.png', 'Product Image 2', 2, 0),
(3, 'AMHANe6cVJ8zCUQxzvs2', 'https://toppng.com/uploads/preview/hd-png-of-apple-iphone-15-pro-max-in-natural-titanium-11695045967pz2x70tl2h.webp', 'Product Image 3', 3, 0),
(4, 'AMHANe6cVJ8zCUQxzvs2', 'https://itronics.in/wp-content/uploads/2023/09/iphone-15-pro-finish-select-202309-6-1inch_GEO_EMEA-2.png', 'Product Image 4', 4, 0),
(5, '2vbaCWVBZAMipZwpZXgo', 'https://lh3.googleusercontent.com/KC8u7yNvg5eSI4vbuHvbQiaHo0rUSMcF48FGUyL8CSj9Le9HatDiti__hGIT_1-qbYmT7E8xAVv4VLEXFMzAi4U-m9cdsbCRhsc', 'Product Image 1', 1, 1),
(6, '2vbaCWVBZAMipZwpZXgo', 'https://static.vecteezy.com/system/resources/previews/041/329/788/non_2x/samsung-galaxy-s24-ultra-titanium-blue-back-view-free-png.png', 'Product Image 2', 2, 0),
(7, '2vbaCWVBZAMipZwpZXgo', 'https://images.samsung.com/is/image/samsung/p6pim/ae/2401/gallery/ae-galaxy-s24-s928-490685-sm-s928bzowmea-thumb-539416786', 'Product Image 3', 3, 0),
(8, '2vbaCWVBZAMipZwpZXgo', 'https://russellcellular.com/wp-content/uploads/2024/01/samsung-eureka-e3-titaniumgray-f.webp.png', 'Product Image 4', 4, 0),
(9, '2vbaCWVBZAMipZwpZXgo', 'https://p.turbosquid.com/ts-thumb/b5/xMDCI0/4V/preview360/png/1706188801/1920x1080/turn_fit_q99/cde1805dd628e096fa35c31e3b7019c399847d02/preview360-1.jpg', 'Product Image 5', 5, 0),
(20, 'LcrWDxLVIhi2tqukk5sp', 'https://www.clove.co.uk/cdn/shop/products/google-pixel-6-kinda-coral-back_1200x.png?v=1665057482', 'Product Image 1', 1, 1),
(21, 'LcrWDxLVIhi2tqukk5sp', 'https://revendo.ch/cdn/shop/files/google-pixel-6-pro-stormy-black-guenstig-gebraucht-kaufen-1.png?v=1738154861', 'Product Image 2', 2, 0),
(22, 'LcrWDxLVIhi2tqukk5sp', 'https://down-tw.img.susercontent.com/file/tw-11134207-7r98o-lnch2nvgsahp44', 'Product Image 3', 3, 0),
(23, 'Q6OxEAysxDdTXHiNdDyi', 'https://stratanetworks.com/wp-content/uploads/2023/12/Google-8-Pro-Porcelain-R30.png', 'Product Image 1', 1, 1),
(24, 'Q6OxEAysxDdTXHiNdDyi', 'https://stratanetworks.com/wp-content/uploads/2023/12/Google-8-Pro-Obsidian.png', 'Product Image 2', 2, 0),
(25, 'Q6OxEAysxDdTXHiNdDyi', 'https://stratanetworks.com/wp-content/uploads/2023/12/Google-8-Pro-Porcelain-Front.png', 'Product Image 3', 3, 0),
(26, 'Q6OxEAysxDdTXHiNdDyi', 'https://stratanetworks.com/wp-content/uploads/2023/12/Google-8-Pro-Porcelain-Back.png', 'Product Image 4', 4, 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_reviews`
--

CREATE TABLE `product_reviews` (
  `review_id` int(11) NOT NULL,
  `product_id` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_verified_purchase` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_tags`
--

CREATE TABLE `product_tags` (
  `tag_id` int(11) NOT NULL,
  `tag_name` varchar(20) DEFAULT NULL,
  `tag_created_at` datetime DEFAULT current_timestamp(),
  `tag_updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_tags`
--

INSERT INTO `product_tags` (`tag_id`, `tag_name`, `tag_created_at`, `tag_updated_at`) VALUES
(1, '5g', '2025-05-11 19:02:38', '2025-05-11 19:02:38'),
(2, 'dual-sim', '2025-05-11 19:02:38', '2025-05-11 19:02:38'),
(3, 'wireless-charging', '2025-05-11 19:02:38', '2025-05-11 19:02:38'),
(4, 'gaming', '2025-05-11 19:02:38', '2025-05-11 19:02:38'),
(5, '108mp-camera', '2025-05-11 19:02:38', '2025-05-11 19:02:38'),
(6, 'nfc', '2025-05-11 19:02:38', '2025-05-11 19:02:38'),
(7, 'bestseller', '2025-05-11 19:02:38', '2025-05-11 19:02:38'),
(8, 'new-arrival', '2025-05-11 19:02:38', '2025-05-11 19:02:38'),
(9, 'screen-protector', '2025-05-11 19:02:38', '2025-05-11 19:02:38'),
(10, 'power-bank', '2025-05-11 19:02:38', '2025-05-11 19:02:38');

-- --------------------------------------------------------

--
-- Table structure for table `product_tag_map`
--

CREATE TABLE `product_tag_map` (
  `product_id` varchar(50) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_tag_map`
--

INSERT INTO `product_tag_map` (`product_id`, `tag_id`) VALUES
('2vbaCWVBZAMipZwpZXgo', 7),
('AMHANe6cVJ8zCUQxzvs2', 8),
('LcrWDxLVIhi2tqukk5sp', 7),
('Q6OxEAysxDdTXHiNdDyi', 8);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `password_hash`, `phone_number`, `date_of_birth`, `is_active`, `is_admin`, `created_at`, `updated_at`) VALUES
(1, 'kt-phones', 'kt-phones', 'info@ktphones.com', '123', NULL, NULL, 1, 1, '2025-05-11 17:04:14', '2025-05-11 17:04:14'),
(2, 'John', 'Peterr', 'peter@gmail.com', '12', '0798874111', '2004-01-02', 1, 0, '2025-05-18 01:40:38', '2025-06-14 17:39:44'),
(3, 'John', 'Karema', 'john@gmail.com', '123', NULL, NULL, 1, 0, '2025-06-08 23:12:28', '2025-06-08 23:12:28'),
(4, 'Extreme', 'Dore', 'dore@gmail.com', '123', NULL, NULL, 1, 0, '2025-06-15 13:26:08', '2025-06-15 13:26:08');

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `wishlist_id` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT 'My Wishlist',
  `wishlist_created_at` datetime DEFAULT current_timestamp(),
  `wishlist_updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlists`
--

INSERT INTO `wishlists` (`wishlist_id`, `user_id`, `name`, `wishlist_created_at`, `wishlist_updated_at`) VALUES
('8dWkVIPvVjTLODhnBXDo', 2, 'My Wishlist', '2025-06-07 12:59:08', '2025-06-07 12:59:08'),
('cDVj30rxuP9Cmy9tHqdi', 2, 'My Wishlist', '2025-06-07 12:59:55', '2025-06-07 12:59:55'),
('JWzMiHDSYV1Zv1tdUwhB', 2, 'My Wishlist', '2025-06-07 12:59:07', '2025-06-07 12:59:07'),
('O6SAq6ucH70QgZGlFwrz', 3, 'My Wishlist', '2025-06-08 23:17:17', '2025-06-08 23:17:17'),
('qe9bkePpQZ1KO9wDej0D', 3, 'My Wishlist', '2025-06-08 23:14:53', '2025-06-08 23:14:53'),
('zdww5I5dXVei6TfvOUlq', 2, 'My Wishlist', '2025-06-07 12:59:10', '2025-06-07 12:59:10'),
('V5VB7roxv0zsR43m7MSG', 2, 'My Wishlist', '2025-06-14 16:28:47', '2025-06-14 16:28:47'),
('WL684eadf46dcd5', 4, 'My Wishlist', '2025-06-15 13:26:44', '2025-06-15 13:26:44');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist_items`
--

CREATE TABLE `wishlist_items` (
  `wishlist_item_id` int(11) NOT NULL,
  `wishlist_id` varchar(50) NOT NULL,
  `product_id` varchar(50) NOT NULL,
  `added_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist_items`
--

INSERT INTO `wishlist_items` (`wishlist_item_id`, `wishlist_id`, `product_id`, `added_at`) VALUES
(35, 'qe9bkePpQZ1KO9wDej0D', 'LcrWDxLVIhi2tqukk5sp', '2025-06-08 23:14:53'),
(42, '8dWkVIPvVjTLODhnBXDo', 'Q6OxEAysxDdTXHiNdDyi', '2025-06-15 03:28:26'),
(44, '8dWkVIPvVjTLODhnBXDo', '2vbaCWVBZAMipZwpZXgo', '2025-06-15 03:36:07'),
(45, '8dWkVIPvVjTLODhnBXDo', 'AMHANe6cVJ8zCUQxzvs2', '2025-06-15 03:37:21'),
(46, 'WL684eadf46dcd5', 'Q6OxEAysxDdTXHiNdDyi', '2025-06-15 13:26:44'),
(48, 'O6SAq6ucH70QgZGlFwrz', 'AMHANe6cVJ8zCUQxzvs2', '2025-06-15 23:36:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attribute_type`
--
ALTER TABLE `attribute_type`
  ADD PRIMARY KEY (`attribute_type_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `attribute_value`
--
ALTER TABLE `attribute_value`
  ADD PRIMARY KEY (`attribute_value_id`),
  ADD UNIQUE KEY `attribute_type_id` (`attribute_type_id`,`value`),
  ADD KEY `idx_attribute_type` (`attribute_type_id`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`brand_id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `cart_item`
--
ALTER TABLE `cart_item`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD UNIQUE KEY `cart_id` (`cart_id`,`product_id`),
  ADD KEY `idx_cart_id` (`cart_id`),
  ADD KEY `idx_product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_parent_category` (`parent_category_id`),
  ADD KEY `idx_slug` (`slug`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_guest_email` (`guest_email`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_order_date` (`order_date`),
  ADD KEY `idx_session_id` (`session_id`),
  ADD KEY `fk_shipping_address` (`shipping_address_id`),
  ADD KEY `fk_billing_address` (`billing_address_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_brand` (`brand_id`),
  ADD KEY `idx_is_featured` (`is_featured`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `wishlist_items`
--
ALTER TABLE `wishlist_items`
  ADD PRIMARY KEY (`wishlist_item_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart_item`
--
ALTER TABLE `cart_item`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `wishlist_items`
--
ALTER TABLE `wishlist_items`
  MODIFY `wishlist_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
