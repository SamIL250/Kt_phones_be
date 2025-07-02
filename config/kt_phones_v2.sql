-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2025 at 07:20 PM
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
-- Database: `kt_phones_v2`
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
('ADDR684dfe207327e', NULL, 'cisofaxe@mailinator.com', 'Wynne', 'Morrow', '36 Green Hague Extension', 'Harum dolore ut quia', 'Sit non dolorem et ', 'Labore deleniti volu', 'Facilis quod quis ve', 'Expedita tempor pari', '+1 (719) 673-1017', 'both', 0, 1, '2025-06-15 00:56:32', '2025-06-15 00:56:32'),
('ADDR6857c9582da75', NULL, 'kiragowoxa@mailinator.com', 'Harriet', 'Roy', '56 Cowley Road', 'Porro adipisci beata', 'Totam commodo aliqua', 'Accusamus assumenda ', 'Nisi Nam voluptas qu', 'Quia aute amet culp', '+1 (105) 519-8028', 'both', 0, 1, '2025-06-22 11:14:00', '2025-06-22 11:14:00'),
('ADDR6859867477fd2', 6, 'scamil350@gmail.com', 'Samuel', 'NIZEYIMANA', 'Kigali, Rwanda', 'Kigali, Nyarugenge TCB', 'Rwanda', 'Nyarugenge', 'Nyakabanda I', 'Nyakabanda', '0798874111', 'both', 1, 0, '2025-06-23 18:53:08', '2025-06-23 18:53:08');

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
(48, 1, '100TB'),
(2, 1, '128GB'),
(5, 1, '1TB'),
(3, 1, '256GB'),
(49, 1, '50GB'),
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
(47, 4, 'Calmer'),
(21, 4, 'Gold'),
(20, 4, 'Green'),
(46, 4, 'Indingo'),
(44, 4, 'Orange'),
(19, 4, 'Red'),
(45, 4, 'Violet'),
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
(41, 7, 'Android 15'),
(40, 7, 'Android 16'),
(42, 7, 'Android 7'),
(43, 7, 'Android 8'),
(53, 7, 'iO S19'),
(52, 7, 'iOS 12'),
(31, 7, 'iOS 16'),
(32, 7, 'iOS 17'),
(38, 7, 'iOS 18'),
(54, 7, 'iOS 20'),
(39, 7, 'iOS 26'),
(50, 7, 'iOS12'),
(51, 7, 'iOS13'),
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
(6, 'Anker', 'Chinese electronics company', 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9c/Anker_logo.svg/2560px-Anker_logo.svg.png'),
(7, 'TikToks', 'TikTok', 'https://static.vecteezy.com/system/resources/previews/018/930/463/non_2x/tiktok-logo-tikok-icon-transparent-tikok-app-logo-free-png.png');

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
('BAgb8yPP8XNHhOoNWpNN', 6, 'active', '2025-06-22 21:43:36', '2025-06-22 21:43:36'),
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
  `unit_price` decimal(10,2) NOT NULL,
  `variant_id` INT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_item`
--

INSERT INTO `cart_item` (`cart_item_id`, `cart_id`, `product_id`, `quantity`, `unit_price`, `variant_id`) VALUES
(1, 'jW6G4fE0YHkZGOJu7l3S', 'Q6OxEAysxDdTXHiNdDyi', 1, 0.00, NULL),
(6, 'ZFZC36SZUsInDIE9KPIO', 'LcrWDxLVIhi2tqukk5sp', 1, 0.00, NULL),
(7, 'ZFZC36SZUsInDIE9KPIO', '2vbaCWVBZAMipZwpZXgo', 3, 0.00, NULL),
(17, '3iEFp428At1wurftj0Yv', 'LcrWDxLVIhi2tqukk5sp', 1, 0.00, NULL),
(19, 'QOIG9kLRoJ9Ni9jG5v16', 'AMHANe6cVJ8zCUQxzvs2', 1, 420000.00, NULL),
(20, 'QOIG9kLRoJ9Ni9jG5v16', 'Q6OxEAysxDdTXHiNdDyi', 1, 420000.00, NULL),
(21, 'QOIG9kLRoJ9Ni9jG5v16', '2vbaCWVBZAMipZwpZXgo', 2, 860000.00, NULL),
(22, 'QOIG9kLRoJ9Ni9jG5v16', 'LcrWDxLVIhi2tqukk5sp', 1, 420000.00, NULL),
(32, 'BAgb8yPP8XNHhOoNWpNN', 'Q6OxEAysxDdTXHiNdDyi', 3, 1120000.00, NULL),
(33, 'BAgb8yPP8XNHhOoNWpNN', 'LcrWDxLVIhi2tqukk5sp', 5, 1120000.00, NULL),
(34, 'BAgb8yPP8XNHhOoNWpNN', '2vbaCWVBZAMipZwpZXgo', 3, 860000.00, NULL);

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
(1, 'Power banks', 'Battery powerbanks', 2, 'power-banks', 1),
(2, 'Accessories', 'Mobile accessories', NULL, 'accessories', 1),
(3, 'Watches', 'Smart watchs', 2, 'watches', 1),
(4, 'Phones', 'Mobile Phones', NULL, 'phones', 1),
(5, 'Air Pods', 'Apple Air buds', 2, 'air-pods', 1),
(6, 'Chargers', 'Charging accessories', 2, 'chargers', 1),
(7, 'Screen Protectors', 'Mobile screen protectors', 2, 'screen-protectors', 1),
(9, 'Speakers', 'speakers for sounds', 2, 'speakers', 1);

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
('ORD684d559d4e6da', NULL, 'regejejoso@mailinator.com', 2796600.00, 'cancelled', '2025-06-14 12:57:33', 'ADDR684d559d4c166', 'ADDR684d559d4c166', 1, 'cash', 'pending', 'standard', 3000.00, 453600.00, NULL, NULL),
('ORD684d864c877f1', 2, NULL, 2976600.00, 'delivered', '2025-06-14 16:25:16', '0', 'ADDR684d559d4c167', 0, 'cash', 'paid', 'Bike', 3000.00, 453600.00, '', NULL),
('ORD684d8837b2f59', 2, NULL, 994200.00, 'pending', '2025-06-14 16:33:27', '0', 'ADDR684d559d4c167', 0, 'cash', 'pending', '0', 3000.00, 151200.00, NULL, NULL),
('ORD684d8b2a9e52a', 2, NULL, 1513400.00, 'pending', '2025-06-14 16:46:02', '0', 'ADDR684d559d4c167', 0, 'cash', 'pending', '0', 3000.00, 230400.00, NULL, NULL),
('ORD684da05d2048c', 2, NULL, 5053400.00, 'pending', '2025-06-14 18:16:29', '0', 'ADDR684d559d4c167', 0, 'cash', 'pending', '0', 3000.00, 770400.00, NULL, NULL),
('ORD684dfc021675b', 2, NULL, 994200.00, 'pending', '2025-06-15 00:47:30', '0', 'ADDR684d559d4c167', 0, 'cash', 'pending', '0', 3000.00, 151200.00, NULL, NULL),
('ORD684dfdf6a7f01', 2, NULL, 1017800.00, 'pending', '2025-06-15 00:55:50', '0', 'ADDR684d559d4c167', 0, 'cash', 'pending', '0', 3000.00, 154800.00, NULL, NULL),
('ORD684dfe2073b2c', NULL, 'cisofaxe@mailinator.com', 1985400.00, 'cancelled', '2025-06-15 00:56:32', 'ADDR684dfe207327e', 'ADDR684dfe207327e', 1, 'cash', 'pending', '0', 3000.00, 302400.00, NULL, NULL),
('ORD6857c9583227a', NULL, 'kiragowoxa@mailinator.com', 3967800.00, 'pending', '2025-06-22 11:14:00', 'ADDR6857c9582da75', 'ADDR6857c9582da75', 1, 'cash', 'pending', '0', 3000.00, 604800.00, NULL, NULL),
('ORD68598689a481d', 6, NULL, 13927000.00, 'pending', '2025-06-23 18:53:29', '0', 'ADDR6859867477fd2', 0, 'cash', 'pending', '0', 3000.00, 999999.99, NULL, NULL),
('ORD685988a625920', 6, NULL, 1324600.00, 'pending', '2025-06-23 19:02:30', '0', 'ADDR6859867477fd2', 0, 'cash', 'pending', '0', 3000.00, 201600.00, NULL, NULL),
('ORD685989f08a533', 6, NULL, 1324600.00, 'pending', '2025-06-23 19:08:00', '0', 'ADDR6859867477fd2', 0, 'cash', 'pending', '0', 3000.00, 201600.00, NULL, NULL),
('ORD68598ad9a898a', 6, NULL, 1324600.00, 'pending', '2025-06-23 19:11:53', '0', 'ADDR6859867477fd2', 0, 'cash', 'pending', '0', 3000.00, 201600.00, NULL, NULL),
('ORD68598c4c5c119', 6, NULL, 1324600.00, 'pending', '2025-06-23 19:18:04', '0', 'ADDR6859867477fd2', 0, 'cash', 'pending', '0', 3000.00, 201600.00, NULL, NULL),
('ORD68598cd491578', 6, NULL, 1324600.00, 'pending', '2025-06-23 19:20:20', '0', 'ADDR6859867477fd2', 0, 'cash', 'pending', '0', 3000.00, 201600.00, NULL, NULL);

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
  `subtotal` decimal(10,2) NOT NULL,
  `variant_id` INT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `unit_price`, `subtotal`, `variant_id`) VALUES
(1, 'ORD684d559d4e6da', 'AMHANe6cVJ8zCUQxzvs2', 1, 420000.00, 420000.00, NULL),
(2, 'ORD684d559d4e6da', 'LcrWDxLVIhi2tqukk5sp', 1, 420000.00, 420000.00, NULL),
(3, 'ORD684d559d4e6da', 'Q6OxEAysxDdTXHiNdDyi', 4, 420000.00, 1680000.00, NULL),
(4, 'ORD684d864c877f1', 'AMHANe6cVJ8zCUQxzvs2', 1, 420000.00, 420000.00, NULL),
(5, 'ORD684d864c877f1', 'LcrWDxLVIhi2tqukk5sp', 1, 420000.00, 420000.00, NULL),
(6, 'ORD684d864c877f1', 'Q6OxEAysxDdTXHiNdDyi', 4, 420000.00, 1680000.00, NULL),
(7, 'ORD684d8837b2f59', 'LcrWDxLVIhi2tqukk5sp', 1, 420000.00, 420000.00, NULL),
(8, 'ORD684d8837b2f59', 'Q6OxEAysxDdTXHiNdDyi', 1, 420000.00, 420000.00, NULL),
(9, 'ORD684d8b2a9e52a', '2vbaCWVBZAMipZwpZXgo', 1, 860000.00, 860000.00, NULL),
(10, 'ORD684d8b2a9e52a', 'LcrWDxLVIhi2tqukk5sp', 1, 420000.00, 420000.00, NULL),
(11, 'ORD684da05d2048c', '2vbaCWVBZAMipZwpZXgo', 4, 860000.00, 3440000.00, NULL),
(12, 'ORD684da05d2048c', 'LcrWDxLVIhi2tqukk5sp', 1, 420000.00, 420000.00, NULL),
(13, 'ORD684da05d2048c', 'Q6OxEAysxDdTXHiNdDyi', 1, 420000.00, 420000.00, NULL),
(14, 'ORD684dfc021675b', 'LcrWDxLVIhi2tqukk5sp', 1, 420000.00, 420000.00, NULL),
(15, 'ORD684dfc021675b', 'Q6OxEAysxDdTXHiNdDyi', 1, 420000.00, 420000.00, NULL),
(16, 'ORD684dfdf6a7f01', '2vbaCWVBZAMipZwpZXgo', 1, 860000.00, 860000.00, NULL),
(17, 'ORD684dfe2073b2c', 'AMHANe6cVJ8zCUQxzvs2', 4, 420000.00, 1680000.00, NULL),
(18, 'ORD6857c9583227a', 'LcrWDxLVIhi2tqukk5sp', 1, 1120000.00, 1120000.00, NULL),
(19, 'ORD6857c9583227a', 'Q6OxEAysxDdTXHiNdDyi', 2, 1120000.00, 2240000.00, NULL),
(20, 'ORD68598689a481d', '2vbaCWVBZAMipZwpZXgo', 2, 860000.00, 1720000.00, NULL),
(21, 'ORD68598689a481d', 'AMHANe6cVJ8zCUQxzvs2', 2, 1120000.00, 2240000.00, NULL),
(22, 'ORD68598689a481d', 'LcrWDxLVIhi2tqukk5sp', 4, 1120000.00, 4480000.00, NULL),
(23, 'ORD68598689a481d', 'Q6OxEAysxDdTXHiNdDyi', 3, 1120000.00, 3360000.00, NULL),
(24, 'ORD685988a625920', 'Q6OxEAysxDdTXHiNdDyi', 1, 1120000.00, 1120000.00, NULL),
(25, 'ORD685989f08a533', 'LcrWDxLVIhi2tqukk5sp', 1, 1120000.00, 1120000.00, NULL),
(26, 'ORD68598ad9a898a', 'Q6OxEAysxDdTXHiNdDyi', 1, 1120000.00, 1120000.00, NULL),
(27, 'ORD68598c4c5c119', 'LcrWDxLVIhi2tqukk5sp', 1, 1120000.00, 1120000.00, NULL),
(28, 'ORD68598cd491578', 'LcrWDxLVIhi2tqukk5sp', 1, 1120000.00, 1120000.00, NULL);

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
('2vbaCWVBZAMipZwpZXgo', 'Samsung s24', 'Samsung Galaxy S24 Ultra - Latest flagship smartphone with advanced AI features', 'SAM-S24-001', 890000.00, 860000.00, 5, 1, 0, 1, 0, 'true', '2025-05-14 20:00:04', '2025-06-27 17:30:11'),
('AMHANe6cVJ8zCUQxzvs2', 'iPhone 15', 'Some iphone description', 'pieces', 1250000.00, 1120000.00, 4, 2, 18, 1, 1, 'true', '2025-05-14 19:42:58', '2025-06-27 17:40:54'),
('Hra3SGh0RTMwYUy6RKHS', 'iPhone 11 Pro max', 'The iPhone 11 Pro Max features a large 6.5-inch Super Retina XDR display, a triple-lens 12MP camera system, and is powered by the A13 Bionic chip. It offers features like Face ID, cinematic video stabilization, and is rated IP68 for water and dust resistance', 'pieces', 800000.00, 740000.00, 4, 2, 22, 1, 1, 'true', '2025-06-27 17:39:16', '2025-06-27 17:39:16'),
('LcrWDxLVIhi2tqukk5sp', 'iPhone 15', 'Some iphone description', 'pieces', 1250000.00, 1120000.00, 4, 2, 12, 1, 0, 'true', '2025-05-23 13:24:57', '2025-06-27 17:30:06'),
('Q6OxEAysxDdTXHiNdDyi', 'iPhone 15', 'Some iphone description', 'pieces', 1250000.00, 1120000.00, 4, 2, 13, 1, 0, 'true', '2025-05-23 13:36:26', '2025-06-27 17:30:02'),
('t6JtIXLbkHWlipkGyA4p', 'AirPods', 'AirPods are a line of wireless Bluetooth earbuds developed by Apple. They are known for their portability, ease of use, and integration with Apple\\\'s ecosystem.', 'pieces', 120000.00, 117000.00, 5, 2, 85, 0, 1, 'true', '2025-06-27 17:55:27', '2025-06-27 17:55:27'),
('TIuItzjMVRiBL1BZuRej', 'iPhone 16', 'The iPhone 16 is a smartphone developed by Apple Inc. Key features include a design with rounded edges, a slightly curved display, and a vertical camera layout. It boasts an advanced dual-camera system with a 48MP Fusion camera, enabling stunning super-high-resolution photos and 2x optical-quality zoom, and a new Ultra Wide camera with autofocus for macro photography and wider scenes.', 'pieces', 1700000.00, 1650000.00, 4, 2, 56, 1, 1, 'true', '2025-06-27 17:51:11', '2025-06-27 17:51:11'),
('WKXzq5iiVOBPmdJMNaoV', 'iPhone 13', 'The iPhone 13 is a smartphone featuring a 6.1-inch Super Retina XDR display, the A15 Bionic chip, and an advanced dual-camera system. It boasts a durable design with Ceramic Shield front cover and is water-resistant with an IP68 rating.', 'pieces', 900000.00, 850000.00, 4, 2, 5, 1, 1, 'true', '2025-06-27 17:45:28', '2025-06-27 17:45:28');

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
(33, 'Q6OxEAysxDdTXHiNdDyi', 8, 34, '2025-06-12 14:50:40', '2025-06-12 14:50:40'),
(48, 'Hra3SGh0RTMwYUy6RKHS', 1, 2, '2025-06-27 17:39:17', '2025-06-27 17:39:17'),
(49, 'Hra3SGh0RTMwYUy6RKHS', 2, 10, '2025-06-27 17:39:17', '2025-06-27 17:39:17'),
(50, 'Hra3SGh0RTMwYUy6RKHS', 3, 11, '2025-06-27 17:39:17', '2025-06-27 17:39:17'),
(51, 'Hra3SGh0RTMwYUy6RKHS', 4, 17, '2025-06-27 17:39:17', '2025-06-27 17:39:17'),
(52, 'Hra3SGh0RTMwYUy6RKHS', 5, 25, '2025-06-27 17:39:17', '2025-06-27 17:39:17'),
(53, 'Hra3SGh0RTMwYUy6RKHS', 6, 27, '2025-06-27 17:39:17', '2025-06-27 17:39:17'),
(54, 'Hra3SGh0RTMwYUy6RKHS', 7, 38, '2025-06-27 17:39:17', '2025-06-27 17:39:17'),
(55, 'Hra3SGh0RTMwYUy6RKHS', 8, 34, '2025-06-27 17:39:17', '2025-06-27 17:39:17'),
(56, 'AMHANe6cVJ8zCUQxzvs2', 1, 2, '2025-06-27 17:40:54', '2025-06-27 17:40:54'),
(57, 'AMHANe6cVJ8zCUQxzvs2', 2, 10, '2025-06-27 17:40:54', '2025-06-27 17:40:54'),
(58, 'AMHANe6cVJ8zCUQxzvs2', 4, 19, '2025-06-27 17:40:54', '2025-06-27 17:40:54'),
(59, 'AMHANe6cVJ8zCUQxzvs2', 7, 38, '2025-06-27 17:40:54', '2025-06-27 17:40:54'),
(60, 'AMHANe6cVJ8zCUQxzvs2', 8, 35, '2025-06-27 17:40:54', '2025-06-27 17:40:54'),
(61, 'WKXzq5iiVOBPmdJMNaoV', 1, 3, '2025-06-27 17:45:28', '2025-06-27 17:45:28'),
(62, 'WKXzq5iiVOBPmdJMNaoV', 2, 10, '2025-06-27 17:45:28', '2025-06-27 17:45:28'),
(63, 'WKXzq5iiVOBPmdJMNaoV', 3, 11, '2025-06-27 17:45:28', '2025-06-27 17:45:28'),
(64, 'WKXzq5iiVOBPmdJMNaoV', 4, 46, '2025-06-27 17:45:28', '2025-06-27 17:45:28'),
(65, 'WKXzq5iiVOBPmdJMNaoV', 5, 22, '2025-06-27 17:45:28', '2025-06-27 17:45:28'),
(66, 'WKXzq5iiVOBPmdJMNaoV', 7, 38, '2025-06-27 17:45:28', '2025-06-27 17:45:28'),
(67, 'TIuItzjMVRiBL1BZuRej', 1, 3, '2025-06-27 17:51:11', '2025-06-27 17:51:11'),
(68, 'TIuItzjMVRiBL1BZuRej', 2, 9, '2025-06-27 17:51:11', '2025-06-27 17:51:11'),
(69, 'TIuItzjMVRiBL1BZuRej', 3, 11, '2025-06-27 17:51:11', '2025-06-27 17:51:11'),
(70, 'TIuItzjMVRiBL1BZuRej', 5, 23, '2025-06-27 17:51:11', '2025-06-27 17:51:11'),
(71, 'TIuItzjMVRiBL1BZuRej', 7, 39, '2025-06-27 17:51:11', '2025-06-27 17:51:11'),
(72, 't6JtIXLbkHWlipkGyA4p', 4, 17, '2025-06-27 17:55:27', '2025-06-27 17:55:27');

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
(5, '2vbaCWVBZAMipZwpZXgo', 'https://lh3.googleusercontent.com/KC8u7yNvg5eSI4vbuHvbQiaHo0rUSMcF48FGUyL8CSj9Le9HatDiti__hGIT_1-qbYmT7E8xAVv4VLEXFMzAi4U-m9cdsbCRhsc', 'Product Image 1', 1, 1),
(6, '2vbaCWVBZAMipZwpZXgo', 'https://static.vecteezy.com/system/resources/previews/041/329/788/non_2x/samsung-galaxy-s24-ultra-titanium-blue-back-view-free-png.png', 'Product Image 2', 2, 0),
(7, '2vbaCWVBZAMipZwpZXgo', 'https://images.samsung.com/is/image/samsung/p6pim/ae/2401/gallery/ae-galaxy-s24-s928-490685-sm-s928bzowmea-thumb-539416786', 'Product Image 3', 3, 0),
(8, '2vbaCWVBZAMipZwpZXgo', 'https://russellcellular.com/wp-content/uploads/2024/01/samsung-eureka-e3-titaniumgray-f.webp.png', 'Product Image 4', 4, 0),
(23, 'Q6OxEAysxDdTXHiNdDyi', 'https://stratanetworks.com/wp-content/uploads/2023/12/Google-8-Pro-Porcelain-R30.png', 'Product Image 1', 1, 1),
(24, 'Q6OxEAysxDdTXHiNdDyi', 'https://stratanetworks.com/wp-content/uploads/2023/12/Google-8-Pro-Obsidian.png', 'Product Image 2', 2, 0),
(25, 'Q6OxEAysxDdTXHiNdDyi', 'https://stratanetworks.com/wp-content/uploads/2023/12/Google-8-Pro-Porcelain-Front.png', 'Product Image 3', 3, 0),
(26, 'Q6OxEAysxDdTXHiNdDyi', 'https://stratanetworks.com/wp-content/uploads/2023/12/Google-8-Pro-Porcelain-Back.png', 'Product Image 4', 4, 0),
(30, 'LcrWDxLVIhi2tqukk5sp', 'https://www.clove.co.uk/cdn/shop/products/google-pixel-6-kinda-coral-back_1200x.png?v=1665057482', 'Product Image 1', 1, 1),
(31, 'LcrWDxLVIhi2tqukk5sp', 'https://revendo.ch/cdn/shop/files/google-pixel-6-pro-stormy-black-guenstig-gebraucht-kaufen-1.png?v=1738154861', 'Product Image 2', 2, 0),
(32, 'LcrWDxLVIhi2tqukk5sp', 'https://down-tw.img.susercontent.com/file/tw-11134207-7r98o-lnch2nvgsahp44', 'Product Image 3', 3, 0),
(33, 'Q6OxEAysxDdTXHiNdDyi', 'https://phones.ultramobile.com/wp-content/uploads/2022/11/google_pixel_7_front.png', 'Pixel 7 phone', 2, 0),
(40, 'Hra3SGh0RTMwYUy6RKHS', 'https://image.similarpng.com/file/similarpng/original-picture/2021/08/IPhone-11-pro-Max-SG-2-on-transparent-background-PNG.png', 'Product Image 1', 1, 1),
(41, 'Hra3SGh0RTMwYUy6RKHS', 'https://pngimg.com/d/iphone_11_PNG28.png', 'Product Image 2', 2, 0),
(42, 'Hra3SGh0RTMwYUy6RKHS', 'https://my-apple.com.ua/image/catalog/products/iphone/iphone-15-pro-15-pro-max/blue-titanium-1.png', 'Product Image 3', 3, 0),
(43, 'Hra3SGh0RTMwYUy6RKHS', 'https://justcelit.co.za/wp-content/uploads/2022/06/iPhone-6-14.png', 'Product Image 4', 4, 0),
(44, 'AMHANe6cVJ8zCUQxzvs2', 'https://www.greentelcom.ph/wp-content/uploads/2023/10/BLUE-TITANIUM.png', 'Product Image 1', 1, 1),
(45, 'AMHANe6cVJ8zCUQxzvs2', 'https://raw.githubusercontent.com/hdpngworld/HPW/main/uploads/65038654434d0-iPhone%2015%20Pro%20Natural%20titanium%20png.png', 'Product Image 2', 2, 0),
(46, 'AMHANe6cVJ8zCUQxzvs2', 'https://www.progresif.com/media/catalog/product/cache/a57e14b7b22013b5665a7323ba2dcb64/i/p/iphone-15-pro-max_titanium-natural-2_2.png', 'Product Image 3', 3, 0),
(47, 'WKXzq5iiVOBPmdJMNaoV', 'https://pngimg.com/uploads/iphone_13/iphone_13_PNG27.png', 'Product Image 1', 1, 1),
(48, 'WKXzq5iiVOBPmdJMNaoV', 'https://www.pngplay.com/wp-content/uploads/13/iPhone-13-Download-Free-PNG.png', 'Product Image 2', 2, 0),
(49, 'WKXzq5iiVOBPmdJMNaoV', 'https://png.monster/wp-content/uploads/2022/09/png.monster-210.png', 'Product Image 3', 3, 0),
(50, 'WKXzq5iiVOBPmdJMNaoV', 'https://pngimg.com/d/iphone_13_PNG31.png', 'Product Image 4', 4, 0),
(51, 'TIuItzjMVRiBL1BZuRej', 'https://media-ik.croma.com/prod/https://media.croma.com/image/upload/v1744356549/Croma%20Assets/Communication/Mobiles/Images/309700_0_x8gmxs.png', 'Product Image 1', 1, 1),
(52, 'TIuItzjMVRiBL1BZuRej', 'https://bsimg.nl/images/apple-iphone-16-128gb-zwart-eu_1.png/RQdaWlNoWrdJXXCAsUJ2efa9_ME%3D/fit-in/257x400/filters%3Aformat%28png%29%3Aupscale%28%29', 'Product Image 2', 2, 0),
(53, 'TIuItzjMVRiBL1BZuRej', 'https://gogizmo.in/wp-content/uploads/2024/10/Apple-iPhone-16-Black2-1.png', 'Product Image 3', 3, 0),
(54, 't6JtIXLbkHWlipkGyA4p', 'https://png.pngtree.com/png-clipart/20230508/original/pngtree-airpods-png-image_9149137.png', 'Product Image 1', 1, 1),
(55, 't6JtIXLbkHWlipkGyA4p', 'https://pngimg.com/d/airPods_PNG10.png', 'Product Image 2', 2, 0),
(56, 't6JtIXLbkHWlipkGyA4p', 'https://static.vecteezy.com/system/resources/previews/050/817/512/non_2x/white-apple-airpods-in-a-white-case-on-a-transparent-background-png.png', 'Product Image 3', 3, 0);

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
(4, 'gaming', '2025-05-11 19:02:38', '2025-05-11 19:02:38'),
(7, 'bestseller', '2025-05-11 19:02:38', '2025-06-22 14:34:25'),
(8, 'new-arrival', '2025-05-11 19:02:38', '2025-05-11 19:02:38');

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
('Q6OxEAysxDdTXHiNdDyi', 8),
('LcrWDxLVIhi2tqukk5sp', 7),
('Hra3SGh0RTMwYUy6RKHS', 7),
('AMHANe6cVJ8zCUQxzvs2', 7),
('WKXzq5iiVOBPmdJMNaoV', 7),
('TIuItzjMVRiBL1BZuRej', 8),
('t6JtIXLbkHWlipkGyA4p', 7);

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
(1, 'kt-phones', 'kt-phones', 'info@ktphones.com', '123456', NULL, NULL, 1, 1, '2025-05-11 17:04:14', '2025-06-21 14:11:51'),
(2, 'John', 'Peterr', 'peter@gmail.com', '12', '0798874111', '2004-01-02', 1, 0, '2025-05-18 01:40:38', '2025-06-14 17:39:44'),
(3, 'John', 'Karemas', 'john@gmail.com', '123', NULL, NULL, 1, 0, '2025-06-08 23:12:28', '2025-06-20 17:42:51'),
(4, 'Extreme', 'Dore', 'dore@gmail.com', '123', NULL, NULL, 1, 0, '2025-06-15 13:26:08', '2025-06-15 13:26:08'),
(6, 'Samuel', 'NIZEYIMANA', 'scamil350@gmail.com', '123', '', NULL, 1, 0, '2025-06-22 11:19:21', '2025-06-22 21:43:09');

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
('V5VB7roxv0zsR43m7MSG', 2, 'My Wishlist', '2025-06-14 16:28:47', '2025-06-14 16:28:47'),
('WL684eadf46dcd5', 4, 'My Wishlist', '2025-06-15 13:26:44', '2025-06-15 13:26:44'),
('WL68585d5e40e24', 6, 'My Wishlist', '2025-06-22 21:45:34', '2025-06-22 21:45:34'),
('zdww5I5dXVei6TfvOUlq', 2, 'My Wishlist', '2025-06-07 12:59:10', '2025-06-07 12:59:10');

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
(42, '8dWkVIPvVjTLODhnBXDo', 'Q6OxEAysxDdTXHiNdDyi', '2025-06-15 03:28:26'),
(44, '8dWkVIPvVjTLODhnBXDo', '2vbaCWVBZAMipZwpZXgo', '2025-06-15 03:36:07'),
(45, '8dWkVIPvVjTLODhnBXDo', 'AMHANe6cVJ8zCUQxzvs2', '2025-06-15 03:37:21'),
(48, 'O6SAq6ucH70QgZGlFwrz', 'AMHANe6cVJ8zCUQxzvs2', '2025-06-15 23:36:45'),
(49, 'O6SAq6ucH70QgZGlFwrz', '2vbaCWVBZAMipZwpZXgo', '2025-06-21 14:28:28'),
(58, 'WL68585d5e40e24', 'Q6OxEAysxDdTXHiNdDyi', '2025-06-24 10:36:04'),
(60, 'WL68585d5e40e24', 'AMHANe6cVJ8zCUQxzvs2', '2025-06-27 13:30:53'),
(61, 'WL68585d5e40e24', '2vbaCWVBZAMipZwpZXgo', '2025-06-27 13:52:18');

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
-- Indexes for table `product_attributes`
--
ALTER TABLE `product_attributes`
  ADD PRIMARY KEY (`product_attribute_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`image_id`);

--
-- Indexes for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `product_id_fk` (`product_id`),
  ADD KEY `reviewer_fk` (`user_id`);

--
-- Indexes for table `product_tags`
--
ALTER TABLE `product_tags`
  ADD PRIMARY KEY (`tag_id`);

--
-- Indexes for table `product_tag_map`
--
ALTER TABLE `product_tag_map`
  ADD KEY `tag_id` (`tag_id`),
  ADD KEY `product_tag_fk` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`wishlist_id`),
  ADD KEY `users_fk` (`user_id`);

--
-- Indexes for table `wishlist_items`
--
ALTER TABLE `wishlist_items`
  ADD PRIMARY KEY (`wishlist_item_id`),
  ADD KEY `wishlist_id_fk` (`wishlist_id`),
  ADD KEY `product_fk` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attribute_type`
--
ALTER TABLE `attribute_type`
  MODIFY `attribute_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `attribute_value`
--
ALTER TABLE `attribute_value`
  MODIFY `attribute_value_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `cart_item`
--
ALTER TABLE `cart_item`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `product_attributes`
--
ALTER TABLE `product_attributes`
  MODIFY `product_attribute_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_tags`
--
ALTER TABLE `product_tags`
  MODIFY `tag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `wishlist_items`
--
ALTER TABLE `wishlist_items`
  MODIFY `wishlist_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `product_id_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviewer_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_tag_map`
--
ALTER TABLE `product_tag_map`
  ADD CONSTRAINT `product_tag_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tag_fk` FOREIGN KEY (`tag_id`) REFERENCES `product_tags` (`tag_id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `users_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist_items`
--
ALTER TABLE `wishlist_items`
  ADD CONSTRAINT `product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_id_fk` FOREIGN KEY (`wishlist_id`) REFERENCES `wishlists` (`wishlist_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
