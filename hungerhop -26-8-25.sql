-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 26, 2025 at 01:13 PM
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
-- Database: `hungerhop`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `record_id` bigint(20) UNSIGNED DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `automated_payout_requests`
--

CREATE TABLE `automated_payout_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `payment_id` bigint(20) UNSIGNED NOT NULL,
  `user_type` enum('restaurant','delivery_partner') NOT NULL,
  `bank_account_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `wallet_balance_before` decimal(10,2) NOT NULL,
  `wallet_balance_after` decimal(10,2) NOT NULL,
  `status` enum('initiated','processing','completed','failed','retry_pending') NOT NULL DEFAULT 'initiated',
  `gateway_transfer_id` varchar(191) DEFAULT NULL,
  `failure_reason` text DEFAULT NULL,
  `retry_count` int(11) NOT NULL DEFAULT 0,
  `max_retry_attempts` int(11) NOT NULL DEFAULT 3,
  `next_retry_at` timestamp NULL DEFAULT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bank_accounts`
--

CREATE TABLE `bank_accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `account_type` enum('restaurant','delivery_partner') NOT NULL,
  `account_holder_name` varchar(191) NOT NULL,
  `bank_name` varchar(191) NOT NULL,
  `account_number` varchar(50) NOT NULL,
  `ifsc_code` varchar(20) NOT NULL,
  `branch_name` varchar(191) DEFAULT NULL,
  `upi_id` varchar(100) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verification_method` enum('penny_drop','manual','api') DEFAULT NULL,
  `verification_date` timestamp NULL DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 1,
  `min_transfer_amount` decimal(8,2) NOT NULL DEFAULT 100.00,
  `max_daily_transfer` decimal(10,2) NOT NULL DEFAULT 50000.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(191) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(191) NOT NULL,
  `owner` varchar(191) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `state_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `state_id`, `name`, `created_at`, `updated_at`) VALUES
(1, 1, 'Mumbai', NULL, NULL),
(2, 1, 'Pune', NULL, NULL),
(3, 2, 'Bangalore', NULL, NULL),
(4, 3, 'New Delhi', NULL, NULL),
(5, 4, 'Ahmedabad', NULL, NULL),
(6, 5, 'Kolkata', NULL, NULL),
(7, 6, 'Los Angeles', NULL, NULL),
(8, 6, 'San Francisco', NULL, NULL),
(9, 7, 'Houston', NULL, NULL),
(10, 8, 'New York City', NULL, NULL),
(11, 9, 'Miami', NULL, NULL),
(12, 10, 'Chicago', NULL, NULL),
(13, 11, 'Toronto', NULL, NULL),
(14, 12, 'Montreal', NULL, NULL),
(15, 13, 'Vancouver', NULL, NULL),
(16, 14, 'Calgary', NULL, NULL),
(17, 15, 'Winnipeg', NULL, NULL),
(18, 16, 'Sydney', NULL, NULL),
(19, 17, 'Melbourne', NULL, NULL),
(20, 18, 'Brisbane', NULL, NULL),
(21, 19, 'Perth', NULL, NULL),
(22, 20, 'Adelaide', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `iso_code` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`, `iso_code`, `created_at`, `updated_at`) VALUES
(1, 'India', 'IN', NULL, NULL),
(2, 'United States', 'US', NULL, NULL),
(3, 'Canada', 'CA', NULL, NULL),
(4, 'Australia', 'AU', NULL, NULL),
(5, 'United Kingdom', 'UK', NULL, NULL),
(6, 'Germany', 'DE', NULL, NULL),
(7, 'France', 'FR', NULL, NULL),
(8, 'Italy', 'IT', NULL, NULL),
(9, 'Spain', 'ES', NULL, NULL),
(10, 'Brazil', 'BR', NULL, NULL),
(11, 'Mexico', 'MX', NULL, NULL),
(12, 'China', 'CN', NULL, NULL),
(13, 'Japan', 'JP', NULL, NULL),
(14, 'South Korea', 'KR', NULL, NULL),
(15, 'Russia', 'RU', NULL, NULL),
(16, 'South Africa', 'ZA', NULL, NULL),
(17, 'Saudi Arabia', 'SA', NULL, NULL),
(18, 'United Arab Emirates', 'AE', NULL, NULL),
(19, 'Singapore', 'SG', NULL, NULL),
(20, 'New Zealand', 'NZ', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer_addresses`
--

CREATE TABLE `customer_addresses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `address_type` enum('home','work','other') NOT NULL DEFAULT 'home',
  `address_line1` varchar(191) NOT NULL,
  `address_line2` varchar(191) DEFAULT NULL,
  `landmark` varchar(191) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_addresses`
--

INSERT INTO `customer_addresses` (`id`, `customer_id`, `address_type`, `address_line1`, `address_line2`, `landmark`, `city`, `state`, `postal_code`, `latitude`, `longitude`, `is_default`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 1, 'home', '123 Main Street', 'Apt 4B', 'Near City Park', 'Pune', 'StateMubai', '123456', 28.61393900, 77.20902300, 1, '2025-08-21 04:36:21', '2025-08-21 10:07:51', NULL),
(3, 1, 'home', '123 Test Street', 'Apt 4B', 'Near Central Park', 'New York', 'NY', '10001', 40.71280000, -74.00600000, 1, '2025-08-22 11:16:23', '2025-08-22 11:16:23', NULL),
(4, 1, 'home', '123 Test Street', NULL, 'Near Market', 'Test City', 'TS', '123456', 25.31760000, 82.97390000, 1, '2025-08-22 11:18:01', '2025-08-22 11:18:01', NULL),
(5, 2, 'home', '123 Main Street', NULL, 'Near City Mall', 'Mumbai', 'MH', '400001', 19.07600000, 72.87770000, 1, '2025-08-26 04:42:27', '2025-08-26 04:42:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer_favorite_items`
--

CREATE TABLE `customer_favorite_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_profiles`
--

CREATE TABLE `customer_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `profile_image_url` varchar(500) DEFAULT NULL,
  `total_orders` int(11) NOT NULL DEFAULT 0,
  `total_spent` decimal(10,2) NOT NULL DEFAULT 0.00,
  `loyalty_points` int(11) NOT NULL DEFAULT 0,
  `referral_code` varchar(20) DEFAULT NULL,
  `referred_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_profiles`
--

INSERT INTO `customer_profiles` (`id`, `user_id`, `date_of_birth`, `gender`, `profile_image_url`, `total_orders`, `total_spent`, `loyalty_points`, `referral_code`, `referred_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 7, '1990-01-01', 'female', NULL, 0, 0.00, 0, 'ot0axOoy', NULL, '2025-08-21 04:36:21', '2025-08-26 04:33:13', NULL),
(2, 7, NULL, NULL, NULL, 0, 0.00, 0, NULL, NULL, '2025-08-22 03:40:27', '2025-08-26 04:33:07', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `delivery_assignments`
--

CREATE TABLE `delivery_assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `partner_id` bigint(20) UNSIGNED NOT NULL,
  `assigned_by` bigint(20) UNSIGNED DEFAULT NULL,
  `pickup_latitude` decimal(10,8) NOT NULL,
  `pickup_longitude` decimal(11,8) NOT NULL,
  `delivery_latitude` decimal(10,8) NOT NULL,
  `delivery_longitude` decimal(11,8) NOT NULL,
  `estimated_distance_km` decimal(6,2) DEFAULT NULL,
  `estimated_duration_minutes` int(11) DEFAULT NULL,
  `delivery_fee` decimal(6,2) NOT NULL,
  `tip_amount` decimal(6,2) NOT NULL DEFAULT 0.00,
  `status` enum('assigned','accepted','rejected','picked_up','delivered','cancelled') NOT NULL DEFAULT 'assigned',
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `accepted_at` timestamp NULL DEFAULT NULL,
  `picked_up_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_partners`
--

CREATE TABLE `delivery_partners` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_type` enum('bicycle','motorcycle','car') NOT NULL,
  `vehicle_number` varchar(20) NOT NULL,
  `license_number` varchar(50) NOT NULL,
  `profile_image_url` varchar(500) DEFAULT NULL,
  `current_latitude` decimal(10,8) DEFAULT NULL,
  `current_longitude` decimal(11,8) DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 0,
  `is_online` tinyint(1) NOT NULL DEFAULT 0,
  `total_deliveries` int(11) NOT NULL DEFAULT 0,
  `total_earnings` decimal(10,2) NOT NULL DEFAULT 0.00,
  `average_rating` decimal(3,2) NOT NULL DEFAULT 0.00,
  `total_reviews` int(11) NOT NULL DEFAULT 0,
  `commission_percentage` decimal(5,2) NOT NULL DEFAULT 20.00,
  `last_location_update` timestamp NULL DEFAULT NULL,
  `status` enum('pending','approved','suspended','rejected') NOT NULL DEFAULT 'pending',
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_partner_documents`
--

CREATE TABLE `delivery_partner_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `partner_id` bigint(20) UNSIGNED NOT NULL,
  `document_type` enum('id_proof','driving_license','rc','address_proof','bank_passbook') NOT NULL,
  `document_path` varchar(500) NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_tracking`
--

CREATE TABLE `delivery_tracking` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `assignment_id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `speed_kmh` decimal(5,2) DEFAULT NULL,
  `heading_degrees` int(11) DEFAULT NULL,
  `accuracy_meters` int(11) DEFAULT NULL,
  `battery_level` int(11) DEFAULT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_zones`
--

CREATE TABLE `delivery_zones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `zone_name` varchar(191) NOT NULL,
  `zone_polygon` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`zone_polygon`)),
  `delivery_fee` decimal(6,2) NOT NULL,
  `minimum_order_amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `estimated_delivery_time` int(11) NOT NULL DEFAULT 30,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_items`
--

CREATE TABLE `inventory_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `item_name` varchar(191) NOT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item_compatibility`
--

CREATE TABLE `item_compatibility` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `compatible_item_id` bigint(20) UNSIGNED NOT NULL,
  `compatibility_type` enum('recommended','frequently_bought_together','substitute') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item_disputes`
--

CREATE TABLE `item_disputes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `raised_by` bigint(20) UNSIGNED NOT NULL,
  `dispute_type` enum('food_quality','wrong_order','late_delivery','damaged_food','missing_items','billing_issue','delivery_issue') NOT NULL,
  `description` text NOT NULL,
  `evidence_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`evidence_images`)),
  `status` enum('pending','investigating','resolved','rejected','escalated') NOT NULL DEFAULT 'pending',
  `resolution_notes` text DEFAULT NULL,
  `compensation_amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `compensation_type` enum('refund','wallet_credit','voucher','none') DEFAULT NULL,
  `wallet_deduction_restaurant` decimal(8,2) NOT NULL DEFAULT 0.00,
  `wallet_deduction_delivery` decimal(8,2) NOT NULL DEFAULT 0.00,
  `assigned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `resolved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(191) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_categories`
--

CREATE TABLE `menu_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `menu_template_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_categories`
--

INSERT INTO `menu_categories` (`id`, `tenant_id`, `restaurant_id`, `name`, `description`, `image_url`, `sort_order`, `is_active`, `created_at`, `updated_at`, `deleted_at`, `menu_template_id`) VALUES
(6, 3, 6, 'Starters', 'Appetizers and light dishes', 'https://example.com/images/starters.png', 1, 1, '2025-08-21 05:15:48', '2025-08-21 05:15:48', NULL, 1),
(7, 3, 6, 'Main Course', 'Hearty meals and signature dishes', 'https://example.com/images/maincourse.png', 2, 1, '2025-08-21 05:15:48', '2025-08-21 05:15:48', NULL, 1),
(8, 3, 6, 'Desserts', 'Sweet treats and desserts', 'https://example.com/images/desserts.png', 3, 1, '2025-08-21 05:15:48', '2025-08-21 05:15:48', NULL, 1),
(9, 3, 6, 'Beverages', 'Hot and cold drinks', 'https://example.com/images/beverages.png', 4, 1, '2025-08-21 05:15:48', '2025-08-21 05:15:48', NULL, 1),
(10, 3, 6, 'Specials', 'Chef’s specials and seasonal items', 'https://example.com/images/specials.png', 5, 1, '2025-08-21 05:15:48', '2025-08-21 05:15:48', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `menu_category_id` bigint(20) UNSIGNED NOT NULL,
  `item_name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `base_price` decimal(8,2) NOT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `is_vegetarian` tinyint(1) NOT NULL DEFAULT 0,
  `is_vegan` tinyint(1) NOT NULL DEFAULT 0,
  `is_gluten_free` tinyint(1) NOT NULL DEFAULT 0,
  `ingredients` text DEFAULT NULL,
  `allergens` varchar(500) DEFAULT NULL,
  `preparation_time` int(11) NOT NULL DEFAULT 15,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `is_popular` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `total_sales` int(11) NOT NULL DEFAULT 0,
  `average_rating` decimal(3,2) NOT NULL DEFAULT 0.00,
  `total_reviews` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `tenant_id`, `restaurant_id`, `menu_category_id`, `item_name`, `description`, `base_price`, `image_url`, `is_vegetarian`, `is_vegan`, `is_gluten_free`, `ingredients`, `allergens`, `preparation_time`, `is_available`, `is_popular`, `sort_order`, `total_sales`, `average_rating`, `total_reviews`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, 6, 6, 'Shellie Eaton', 'Do blanditiis harum', 69.00, 'http://127.0.0.1:8000/storage/menu_items/1755757105_zOprTgLezg.png', 1, 1, 1, 'Rem quo eligendi odi', 'Culpa necessitatibus', 163, 1, 1, 64, 0, 0.00, 0, '2025-08-21 00:48:25', '2025-08-22 08:35:20', NULL),
(2, 3, 6, 8, 'Chancellor Blevins', 'Enim enim id rem do', 670.00, 'http://127.0.0.1:8000/storage/menu_items/1755766905_6SWRAOyEaN.png', 0, 1, 0, 'Sequi similique accu', 'Fugiat deserunt cupi', 22, 1, 1, 55, 0, 0.00, 0, '2025-08-21 03:31:45', '2025-08-22 09:38:42', NULL),
(4, 3, 6, 10, 'Barry Pugh', 'Reprehenderit optio', 229.00, 'http://127.0.0.1:8000/storage/menu_items/1756200394_SnZlUdJDwA.png', 0, 1, 1, 'Est mollit veniam', 'Ut labore optio nem', 120, 0, 0, 30, 0, 0.00, 0, '2025-08-26 03:56:34', '2025-08-26 03:56:34', NULL),
(5, 3, 6, 8, 'Chancellor Blevins (Copy)', 'Enim enim id rem do', 670.00, 'http://127.0.0.1:8000/storage/menu_items/1755766905_6SWRAOyEaN.png', 0, 1, 0, 'Sequi similique accu', 'Fugiat deserunt cupi', 22, 1, 1, 55, 0, 0.00, 0, '2025-08-26 03:57:14', '2025-08-26 03:57:14', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `menu_item_reviews`
--

CREATE TABLE `menu_item_reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_item_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `review_text` text DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_item_variations`
--

CREATE TABLE `menu_item_variations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `variation_name` varchar(191) NOT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `max_selections` int(11) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_templates`
--

CREATE TABLE `menu_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `template_name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_templates`
--

INSERT INTO `menu_templates` (`id`, `tenant_id`, `template_name`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, 'Default Template', 'Default menu template for Tenant 3', '2025-08-21 05:15:08', '2025-08-21 05:15:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `menu_variations`
--

CREATE TABLE `menu_variations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `menu_item_id` bigint(20) UNSIGNED NOT NULL,
  `label` varchar(191) NOT NULL,
  `price_delta` decimal(6,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_versions`
--

CREATE TABLE `menu_versions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `version_name` varchar(191) NOT NULL,
  `snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`snapshot`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000001_create_cache_table', 1),
(2, '0001_01_01_000002_create_jobs_table', 1),
(3, '2025_07_21_205244_create_pulse_tables', 1),
(4, '2025_07_22_065616_create_tenants_table', 1),
(5, '2025_07_22_065617_create_users_table', 1),
(6, '2025_07_22_111530_create_restaurants_table', 1),
(7, '2025_07_22_111721_create_restaurant_documents_table', 1),
(8, '2025_07_22_113421_create_restaurant_working_hours_table', 1),
(9, '2025_07_22_113756_create_menu_categories_table', 1),
(10, '2025_07_22_113956_create_menu_items_table', 1),
(11, '2025_07_22_114305_create_menu_item_variations_table', 1),
(12, '2025_07_22_115017_create_variation_options_table', 1),
(13, '2025_07_22_120030_create_customer_profiles_table', 1),
(14, '2025_07_22_120923_create_customer_addresses_table', 1),
(15, '2025_07_22_121137_create_delivery_partners_table', 1),
(16, '2025_07_22_122816_create_delivery_partner_documents_table', 1),
(17, '2025_07_22_123051_create_bank_accounts_table', 1),
(18, '2025_07_22_123315_create_orders_table', 1),
(19, '2025_07_22_123836_create_order_items_table', 1),
(20, '2025_07_22_124429_create_order_item_customizations_table', 1),
(21, '2025_07_22_124601_create_promotions_table', 1),
(22, '2025_07_22_125336_create_restaurant_banners_table', 1),
(23, '2025_07_22_125622_create_wallets_table', 1),
(24, '2025_07_22_125749_create_wallet_transactions_table', 1),
(25, '2025_07_22_130038_create_wallet_split_transactions_table', 1),
(26, '2025_07_22_130441_create_payments__table', 1),
(27, '2025_07_22_130606_create_refunds_table', 1),
(28, '2025_07_22_130746_create_automated_payout_requests_table', 1),
(29, '2025_07_22_132409_create_delivery_assignments_table', 1),
(30, '2025_07_22_132553_create_delivery_tracking_table', 1),
(31, '2025_07_22_132657_create_otp_verifications_table', 1),
(32, '2025_07_22_132822_create_reviews_table', 1),
(33, '2025_07_23_045416_create_countries_table', 1),
(34, '2025_07_23_045417_create_states_table', 1),
(35, '2025_07_23_045744_create_menu_item_reviews_table', 1),
(36, '2025_07_23_050038_create_customer_favorite_items_table', 1),
(37, '2025_07_23_050218_create_referrals_table', 1),
(38, '2025_07_23_050326_create_notifications_table', 1),
(39, '2025_07_23_050958_create_restaurant_staff_table', 1),
(40, '2025_07_23_051140_create_audit_logs_table', 1),
(41, '2025_07_23_051234_create_system_settings_table', 1),
(42, '2025_07_23_051255_create_item_compatibility_table', 1),
(43, '2025_07_23_051533_create_item_disputes_table', 1),
(44, '2025_07_23_051721_create_subscription_payments_table', 1),
(45, '2025_07_23_051836_create_delivery_zones_table', 1),
(46, '2025_07_23_051920_create_transaction_logs_table', 1),
(47, '2025_07_23_053746_create_password_reset_tokens_table', 1),
(48, '2025_07_23_054017_create_personal_access_tokens_table', 1),
(49, '2025_07_23_054143_create_sessions_table', 1),
(50, '2025_08_01_045417_create_cities_table', 1),
(51, '2025_08_01_052303_make_tenant_id_nullable_in_restaurant_documents_table', 1),
(52, '2025_08_01_132138_city_type_change_to_restaurants_table', 1),
(53, '2025_08_05_032607_create_menu_templates_table', 1),
(54, '2025_08_05_033628_add_columns_to_menu_categories_table', 1),
(55, '2025_08_05_034554_rename_columns_to_menu_items_table', 1),
(56, '2025_08_05_035138_create_menu_variations_table', 1),
(57, '2025_08_05_035327_create_restaurant_menu_items_table', 1),
(58, '2025_08_05_035610_create_menu_versions_table', 1),
(59, '2025_08_05_035712_create_inventory_items_table', 1),
(60, '2025_08_12_120151_add_restaurant_onboarding_fields', 1),
(61, '2025_08_18_151218_add_contact_person_name_to_restaurants_table', 1),
(62, '2025_08_19_074527_update_restaurants_business_hours_column', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `user_type` enum('customer','restaurant','delivery_partner','admin') NOT NULL,
  `notification_type` enum('order_status','promotion','system','payment','review','referral','payout') NOT NULL,
  `title` varchar(191) NOT NULL,
  `message` text NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `channels` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`channels`)),
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `push_sent_at` timestamp NULL DEFAULT NULL,
  `email_sent_at` timestamp NULL DEFAULT NULL,
  `sms_sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `delivery_address_id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('placed','accepted','preparing','ready_for_pickup','assigned_to_delivery','picked_up','out_for_delivery','delivered','cancelled','rejected') NOT NULL DEFAULT 'placed',
  `subtotal` decimal(10,2) NOT NULL,
  `tax_amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `delivery_fee` decimal(6,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `restaurant_amount` decimal(10,2) NOT NULL,
  `delivery_amount` decimal(6,2) NOT NULL,
  `platform_fee` decimal(6,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('wallet','card','upi','netbanking','cod') NOT NULL,
  `payment_status` enum('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
  `special_instructions` text DEFAULT NULL,
  `pickup_otp` varchar(6) DEFAULT NULL,
  `delivery_otp` varchar(6) DEFAULT NULL,
  `pickup_otp_verified_at` timestamp NULL DEFAULT NULL,
  `delivery_otp_verified_at` timestamp NULL DEFAULT NULL,
  `estimated_delivery_time` timestamp NULL DEFAULT NULL,
  `actual_delivery_time` timestamp NULL DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `cancelled_by` enum('customer','restaurant','admin') DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `auto_accept_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `customer_id`, `restaurant_id`, `delivery_address_id`, `tenant_id`, `status`, `subtotal`, `tax_amount`, `delivery_fee`, `discount_amount`, `total_amount`, `restaurant_amount`, `delivery_amount`, `platform_fee`, `payment_method`, `payment_status`, `special_instructions`, `pickup_otp`, `delivery_otp`, `pickup_otp_verified_at`, `delivery_otp_verified_at`, `estimated_delivery_time`, `actual_delivery_time`, `cancellation_reason`, `rejection_reason`, `cancelled_by`, `cancelled_at`, `auto_accept_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(7, 'ORD-TEST-0001', 1, 6, 2, 3, 'accepted', 100.00, 5.00, 20.00, 0.00, 125.00, 100.00, 20.00, 5.00, '', 'pending', 'Force inserted order', '1111', '2222', NULL, NULL, '2025-08-22 11:39:06', NULL, NULL, NULL, NULL, NULL, '2025-08-22 11:20:06', '2025-08-22 11:19:06', '2025-08-26 04:00:20', NULL),
(12, 'ORD-20250826-001', 2, 6, 5, 3, 'ready_for_pickup', 20.00, 25.00, 5.00, 10.00, 40.00, 0.00, 0.00, 0.00, 'wallet', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-26 04:45:04', '2025-08-26 04:45:06', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(8,2) NOT NULL,
  `total_price` decimal(8,2) NOT NULL,
  `special_instructions` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `item_id`, `tenant_id`, `item_name`, `quantity`, `unit_price`, `total_price`, `special_instructions`, `created_at`, `updated_at`, `deleted_at`) VALUES
(5, 7, 1, 3, 'Shellie Eaton', 2, 250.00, 500.00, 'Extra cheese please', '2025-08-26 06:21:55', '2025-08-26 06:21:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_item_customizations`
--

CREATE TABLE `order_item_customizations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_item_id` bigint(20) UNSIGNED NOT NULL,
  `variation_id` bigint(20) UNSIGNED NOT NULL,
  `option_id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `variation_name` varchar(255) NOT NULL,
  `option_name` varchar(255) NOT NULL,
  `price_modifier` decimal(8,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `otp_verifications`
--

CREATE TABLE `otp_verifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `otp_type` enum('pickup','delivery') NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `verified_by` bigint(20) UNSIGNED DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `attempts_count` int(11) NOT NULL DEFAULT 0,
  `max_attempts` int(11) NOT NULL DEFAULT 3,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `payment_method` enum('wallet','card','upi','netbanking','cod') NOT NULL,
  `payment_gateway` enum('razorpay','stripe','paytm','phonepe','wallet') NOT NULL,
  `gateway_transaction_id` varchar(191) DEFAULT NULL,
  `gateway_payment_id` varchar(191) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'INR',
  `status` enum('pending','completed','failed','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `gateway_response` text DEFAULT NULL,
  `failure_reason` text DEFAULT NULL,
  `initiated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `tenant_id`, `payment_method`, `payment_gateway`, `gateway_transaction_id`, `gateway_payment_id`, `amount`, `currency`, `status`, `gateway_response`, `failure_reason`, `initiated_at`, `completed_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 7, 3, 'card', 'stripe', 'test_txn_68ac050fc21bf', NULL, 250.00, 'INR', 'completed', '{\"test\":\"success\"}', NULL, '2025-08-25 00:59:11', '2025-08-25 01:09:11', '2025-08-25 01:09:11', '2025-08-25 01:09:11', NULL),
(2, 7, 3, 'card', 'stripe', 'pi_3Rzu3nD8Z2NgLaVq1AuAQ1Fz', NULL, 500.00, 'inr', 'failed', '{\"id\":\"pi_3Rzu3nD8Z2NgLaVq1AuAQ1Fz\",\"object\":\"payment_intent\",\"amount\":50000,\"amount_capturable\":0,\"amount_details\":{\"tip\":[]},\"amount_received\":0,\"application\":null,\"application_fee_amount\":null,\"automatic_payment_methods\":{\"allow_redirects\":\"always\",\"enabled\":true},\"canceled_at\":null,\"cancellation_reason\":null,\"capture_method\":\"automatic_async\",\"client_secret\":\"pi_3Rzu3nD8Z2NgLaVq1AuAQ1Fz_secret_WoDFAeHd3hYx4G23Xe3oNOb1c\",\"confirmation_method\":\"automatic\",\"created\":1756103959,\"currency\":\"inr\",\"customer\":null,\"description\":null,\"excluded_payment_method_types\":null,\"last_payment_error\":null,\"latest_charge\":null,\"livemode\":false,\"metadata\":{\"order_id\":\"7\",\"tenant_id\":\"3\"},\"next_action\":null,\"on_behalf_of\":null,\"payment_method\":null,\"payment_method_configuration_details\":{\"id\":\"pmc_1RQugPD8Z2NgLaVqI3jqNKwa\",\"parent\":null},\"payment_method_options\":{\"card\":{\"installments\":null,\"mandate_options\":null,\"network\":null,\"request_three_d_secure\":\"automatic\"},\"link\":{\"persistent_token\":null}},\"payment_method_types\":[\"card\",\"link\"],\"processing\":null,\"receipt_email\":null,\"review\":null,\"setup_future_usage\":null,\"shipping\":null,\"source\":null,\"statement_descriptor\":null,\"statement_descriptor_suffix\":null,\"status\":\"requires_payment_method\",\"transfer_data\":null,\"transfer_group\":null}', 'Payment method required', '2025-08-25 01:09:26', NULL, '2025-08-25 01:09:26', '2025-08-25 01:18:31', NULL),
(3, 7, 3, 'card', 'stripe', 'pi_3RzuB0D8Z2NgLaVq10PfbXkj', NULL, 500.00, 'inr', 'failed', '{\"id\":\"pi_3RzuB0D8Z2NgLaVq10PfbXkj\",\"object\":\"payment_intent\",\"amount\":50000,\"amount_capturable\":0,\"amount_details\":{\"tip\":[]},\"amount_received\":0,\"application\":null,\"application_fee_amount\":null,\"automatic_payment_methods\":{\"allow_redirects\":\"always\",\"enabled\":true},\"canceled_at\":null,\"cancellation_reason\":null,\"capture_method\":\"automatic_async\",\"client_secret\":\"pi_3RzuB0D8Z2NgLaVq10PfbXkj_secret_pkZ3dR2ITz3p4aYrmyXohSFiw\",\"confirmation_method\":\"automatic\",\"created\":1756104406,\"currency\":\"inr\",\"customer\":null,\"description\":null,\"excluded_payment_method_types\":null,\"last_payment_error\":null,\"latest_charge\":null,\"livemode\":false,\"metadata\":{\"order_id\":\"7\",\"tenant_id\":\"3\"},\"next_action\":null,\"on_behalf_of\":null,\"payment_method\":null,\"payment_method_configuration_details\":{\"id\":\"pmc_1RQugPD8Z2NgLaVqI3jqNKwa\",\"parent\":null},\"payment_method_options\":{\"card\":{\"installments\":null,\"mandate_options\":null,\"network\":null,\"request_three_d_secure\":\"automatic\"},\"link\":{\"persistent_token\":null}},\"payment_method_types\":[\"card\",\"link\"],\"processing\":null,\"receipt_email\":null,\"review\":null,\"setup_future_usage\":null,\"shipping\":null,\"source\":null,\"statement_descriptor\":null,\"statement_descriptor_suffix\":null,\"status\":\"requires_payment_method\",\"transfer_data\":null,\"transfer_group\":null}', 'Payment method required', '2025-08-25 01:16:54', NULL, '2025-08-25 01:16:54', '2025-08-25 02:38:24', NULL),
(4, 7, 3, 'card', 'stripe', 'pi_3RzvCgD8Z2NgLaVq14vZnQPB', NULL, 500.00, 'inr', 'pending', '{\"id\":\"pi_3RzvCgD8Z2NgLaVq14vZnQPB\",\"object\":\"payment_intent\",\"amount\":50000,\"amount_capturable\":0,\"amount_details\":{\"tip\":[]},\"amount_received\":0,\"application\":null,\"application_fee_amount\":null,\"automatic_payment_methods\":{\"allow_redirects\":\"always\",\"enabled\":true},\"canceled_at\":null,\"cancellation_reason\":null,\"capture_method\":\"automatic_async\",\"client_secret\":\"pi_3RzvCgD8Z2NgLaVq14vZnQPB_secret_DbfuX5jemNtbF4iTcfaRGhFHz\",\"confirmation_method\":\"automatic\",\"created\":1756108354,\"currency\":\"inr\",\"customer\":null,\"description\":null,\"excluded_payment_method_types\":null,\"last_payment_error\":null,\"latest_charge\":null,\"livemode\":false,\"metadata\":{\"order_id\":\"7\",\"tenant_id\":\"3\"},\"next_action\":null,\"on_behalf_of\":null,\"payment_method\":null,\"payment_method_configuration_details\":{\"id\":\"pmc_1RQugPD8Z2NgLaVqI3jqNKwa\",\"parent\":null},\"payment_method_options\":{\"card\":{\"installments\":null,\"mandate_options\":null,\"network\":null,\"request_three_d_secure\":\"automatic\"},\"link\":{\"persistent_token\":null}},\"payment_method_types\":[\"card\",\"link\"],\"processing\":null,\"receipt_email\":null,\"review\":null,\"setup_future_usage\":null,\"shipping\":null,\"source\":null,\"statement_descriptor\":null,\"statement_descriptor_suffix\":null,\"status\":\"requires_payment_method\",\"transfer_data\":null,\"transfer_group\":null}', NULL, '2025-08-25 02:22:41', NULL, '2025-08-25 02:22:41', '2025-08-25 02:22:41', NULL),
(5, 7, 3, 'card', 'stripe', 'pi_3RzvDxD8Z2NgLaVq08F3ON8l', NULL, 500.00, 'usd', 'pending', '{\"id\":\"pi_3RzvDxD8Z2NgLaVq08F3ON8l\",\"object\":\"payment_intent\",\"amount\":50000,\"amount_capturable\":0,\"amount_details\":{\"tip\":[]},\"amount_received\":0,\"application\":null,\"application_fee_amount\":null,\"automatic_payment_methods\":{\"allow_redirects\":\"always\",\"enabled\":true},\"canceled_at\":null,\"cancellation_reason\":null,\"capture_method\":\"automatic_async\",\"client_secret\":\"pi_3RzvDxD8Z2NgLaVq08F3ON8l_secret_W1NuGFvqxBXHHKkx4qJJ4Eniy\",\"confirmation_method\":\"automatic\",\"created\":1756108433,\"currency\":\"usd\",\"customer\":null,\"description\":null,\"excluded_payment_method_types\":null,\"last_payment_error\":null,\"latest_charge\":null,\"livemode\":false,\"metadata\":{\"order_id\":\"7\",\"tenant_id\":\"3\"},\"next_action\":null,\"on_behalf_of\":null,\"payment_method\":null,\"payment_method_configuration_details\":{\"id\":\"pmc_1RQugPD8Z2NgLaVqI3jqNKwa\",\"parent\":null},\"payment_method_options\":{\"affirm\":[],\"amazon_pay\":{\"express_checkout_element_session_id\":null},\"card\":{\"installments\":null,\"mandate_options\":null,\"network\":null,\"request_three_d_secure\":\"automatic\"},\"cashapp\":[],\"klarna\":{\"preferred_locale\":null},\"link\":{\"persistent_token\":null}},\"payment_method_types\":[\"card\",\"klarna\",\"link\",\"affirm\",\"cashapp\",\"amazon_pay\"],\"processing\":null,\"receipt_email\":null,\"review\":null,\"setup_future_usage\":null,\"shipping\":null,\"source\":null,\"statement_descriptor\":null,\"statement_descriptor_suffix\":null,\"status\":\"requires_payment_method\",\"transfer_data\":null,\"transfer_group\":null}', NULL, '2025-08-25 02:23:59', NULL, '2025-08-25 02:23:59', '2025-08-25 02:23:59', NULL),
(6, 7, 3, 'card', 'stripe', 'pi_3RzvQZD8Z2NgLaVq1nYE4HGB', NULL, 50.00, 'usd', 'failed', '{\"id\":\"pi_3RzvQZD8Z2NgLaVq1nYE4HGB\",\"object\":\"payment_intent\",\"amount\":5000,\"amount_capturable\":0,\"amount_details\":{\"tip\":[]},\"amount_received\":0,\"application\":null,\"application_fee_amount\":null,\"automatic_payment_methods\":{\"allow_redirects\":\"always\",\"enabled\":true},\"canceled_at\":null,\"cancellation_reason\":null,\"capture_method\":\"automatic_async\",\"client_secret\":\"pi_3RzvQZD8Z2NgLaVq1nYE4HGB_secret_nfgtICt6UqrnwGsT9YuH6Ttyc\",\"confirmation_method\":\"automatic\",\"created\":1756109215,\"currency\":\"usd\",\"customer\":null,\"description\":null,\"excluded_payment_method_types\":null,\"last_payment_error\":null,\"latest_charge\":null,\"livemode\":false,\"metadata\":{\"order_id\":\"7\",\"tenant_id\":\"3\"},\"next_action\":null,\"on_behalf_of\":null,\"payment_method\":null,\"payment_method_configuration_details\":{\"id\":\"pmc_1RQugPD8Z2NgLaVqI3jqNKwa\",\"parent\":null},\"payment_method_options\":{\"affirm\":[],\"amazon_pay\":{\"express_checkout_element_session_id\":null},\"card\":{\"installments\":null,\"mandate_options\":null,\"network\":null,\"request_three_d_secure\":\"automatic\"},\"cashapp\":[],\"klarna\":{\"preferred_locale\":null},\"link\":{\"persistent_token\":null}},\"payment_method_types\":[\"card\",\"klarna\",\"link\",\"affirm\",\"cashapp\",\"amazon_pay\"],\"processing\":null,\"receipt_email\":null,\"review\":null,\"setup_future_usage\":null,\"shipping\":null,\"source\":null,\"statement_descriptor\":null,\"statement_descriptor_suffix\":null,\"status\":\"requires_payment_method\",\"transfer_data\":null,\"transfer_group\":null}', 'Payment method required', '2025-08-25 02:37:02', NULL, '2025-08-25 02:37:02', '2025-08-25 02:57:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(191) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `promotion_code` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `discount_type` enum('percentage','fixed_amount') NOT NULL,
  `discount_value` decimal(8,2) NOT NULL,
  `minimum_order_amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `maximum_discount_amount` decimal(8,2) DEFAULT NULL,
  `usage_limit_per_customer` int(11) NOT NULL DEFAULT 1,
  `total_usage_limit` int(11) DEFAULT NULL,
  `current_usage_count` int(11) NOT NULL DEFAULT 0,
  `valid_from` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `valid_until` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pulse_aggregates`
--

CREATE TABLE `pulse_aggregates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bucket` int(10) UNSIGNED NOT NULL,
  `period` mediumint(8) UNSIGNED NOT NULL,
  `type` varchar(191) NOT NULL,
  `key` mediumtext NOT NULL,
  `key_hash` binary(16) GENERATED ALWAYS AS (unhex(md5(`key`))) VIRTUAL,
  `aggregate` varchar(191) NOT NULL,
  `value` decimal(20,2) NOT NULL,
  `count` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pulse_aggregates`
--

INSERT INTO `pulse_aggregates` (`id`, `bucket`, `period`, `type`, `key`, `aggregate`, `value`, `count`) VALUES
(4, 1755684000, 10080, 'user_request', '1', 'count', 57.00, NULL),
(84, 1755684000, 10080, 'user_request', '2', 'count', 2.00, NULL),
(237, 1755754560, 10080, 'slow_request', '[\"GET\",\"\\/admin\\/dashboard\",\"Closure\"]', 'count', 1.00, NULL),
(241, 1755754560, 10080, 'slow_user_request', '3', 'count', 1.00, NULL),
(245, 1755754560, 10080, 'user_request', '3', 'count', 5.00, NULL),
(249, 1755754560, 10080, 'slow_request', '[\"GET\",\"\\/admin\\/dashboard\",\"Closure\"]', 'max', 2580.00, NULL),
(269, 1755764640, 10080, 'exception', '[\"Symfony\\\\Component\\\\Console\\\\Exception\\\\NamespaceNotFoundException\",\"vendor\\\\symfony\\\\console\\\\Application.php:659\"]', 'count', 2.00, NULL),
(273, 1755764640, 10080, 'exception', '[\"Symfony\\\\Component\\\\Console\\\\Exception\\\\NamespaceNotFoundException\",\"vendor\\\\symfony\\\\console\\\\Application.php:659\"]', 'max', 1755764775.00, NULL),
(277, 1755764640, 10080, 'user_request', '3', 'count', 65.00, NULL),
(313, 1755764640, 10080, 'slow_request', '[\"GET\",\"\\/restaurant\\/menu\\/categories\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\MenuCategoryController@index\"]', 'count', 1.00, NULL),
(317, 1755764640, 10080, 'slow_user_request', '3', 'count', 3.00, NULL),
(322, 1755764640, 10080, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\MenuCategoryController.php:42\"]', 'count', 4.00, NULL),
(329, 1755764640, 10080, 'slow_request', '[\"GET\",\"\\/restaurant\\/menu\\/categories\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\MenuCategoryController@index\"]', 'max', 1010.00, NULL),
(333, 1755764640, 10080, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\MenuCategoryController.php:42\"]', 'max', 1755767044.00, NULL),
(353, 1755764640, 10080, 'user_request', '1', 'count', 10.00, NULL),
(381, 1755764640, 10080, 'user_request', '2', 'count', 5.00, NULL),
(385, 1755764640, 10080, 'slow_request', '[\"GET\",\"\\/restaurant\\/dashboard\",\"Closure\"]', 'count', 4.00, NULL),
(389, 1755764640, 10080, 'slow_user_request', '2', 'count', 2.00, NULL),
(393, 1755764640, 10080, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 'count', 6.00, NULL),
(401, 1755764640, 10080, 'slow_request', '[\"GET\",\"\\/restaurant\\/dashboard\",\"Closure\"]', 'max', 1285.00, NULL),
(405, 1755764640, 10080, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 'max', 1755766741.00, NULL),
(725, 1755764640, 10080, 'exception', '[\"Error\",\"app\\\\Http\\\\Controllers\\\\Admin\\\\RestaurantManagementController.php:229\"]', 'count', 1.00, NULL),
(733, 1755764640, 10080, 'exception', '[\"Error\",\"app\\\\Http\\\\Controllers\\\\Admin\\\\RestaurantManagementController.php:229\"]', 'max', 1755770276.00, NULL),
(745, 1755764640, 10080, 'exception', '[\"Illuminate\\\\Contracts\\\\Container\\\\BindingResolutionException\",\"vendor\\\\laravel\\\\framework\\\\src\\\\Illuminate\\\\Container\\\\Container.php:1019\"]', 'count', 2.00, NULL),
(749, 1755764640, 10080, 'exception', '[\"Illuminate\\\\Contracts\\\\Container\\\\BindingResolutionException\",\"vendor\\\\laravel\\\\framework\\\\src\\\\Illuminate\\\\Container\\\\Container.php:1019\"]', 'max', 1755770698.00, NULL),
(753, 1755774720, 10080, 'slow_request', '[\"GET\",\"\\/restaurant\\/menu\\/list\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\MenuItemController@index\"]', 'count', 1.00, NULL),
(757, 1755774720, 10080, 'slow_user_request', '3', 'count', 1.00, NULL),
(761, 1755774720, 10080, 'user_request', '3', 'count', 10.00, NULL),
(765, 1755774720, 10080, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 'count', 4.00, NULL),
(769, 1755774720, 10080, 'slow_request', '[\"GET\",\"\\/restaurant\\/menu\\/list\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\MenuItemController@index\"]', 'max', 1116.00, NULL),
(773, 1755774720, 10080, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 'max', 1755775574.00, NULL),
(837, 1755774720, 10080, 'user_request', '6', 'count', 6.00, NULL),
(861, 1755835200, 10080, 'exception', '[\"Error\",\"app\\\\Models\\\\User.php:13\"]', 'count', 3.00, NULL),
(865, 1755835200, 10080, 'exception', '[\"Error\",\"app\\\\Models\\\\User.php:13\"]', 'max', 1755839464.00, NULL),
(877, 1755835200, 10080, 'slow_request', '[\"GET\",\"\\/\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\RegisterController@showRegistrationForm\"]', 'count', 1.00, NULL),
(885, 1755835200, 10080, 'slow_request', '[\"GET\",\"\\/\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\RegisterController@showRegistrationForm\"]', 'max', 1240.00, NULL),
(893, 1755835200, 10080, 'user_request', '1', 'count', 3.00, NULL),
(905, 1755835200, 10080, 'exception', '[\"InvalidArgumentException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\NearestRestaurantController.php:59\"]', 'count', 1.00, NULL),
(909, 1755835200, 10080, 'exception', '[\"InvalidArgumentException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\NearestRestaurantController.php:59\"]', 'max', 1755844842.00, NULL),
(913, 1755845280, 10080, 'exception', '[\"Error\",\"app\\\\Http\\\\Middleware\\\\CorsMiddleware.php:27\"]', 'count', 1.00, NULL),
(917, 1755845280, 10080, 'exception', '[\"Error\",\"app\\\\Http\\\\Middleware\\\\CorsMiddleware.php:27\"]', 'max', 1755847261.00, NULL),
(921, 1755845280, 10080, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\NearestRestaurantController.php:22\"]', 'count', 2.00, NULL),
(925, 1755845280, 10080, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\NearestRestaurantController.php:22\"]', 'max', 1755847962.00, NULL),
(937, 1755845280, 10080, 'exception', '[\"BadMethodCallException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\NearestRestaurantController.php:68\"]', 'count', 1.00, NULL),
(941, 1755845280, 10080, 'exception', '[\"BadMethodCallException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\NearestRestaurantController.php:68\"]', 'max', 1755848277.00, NULL),
(945, 1755845280, 10080, 'exception', '[\"BadMethodCallException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\NearestRestaurantController.php:49\"]', 'count', 1.00, NULL),
(949, 1755845280, 10080, 'exception', '[\"BadMethodCallException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\NearestRestaurantController.php:49\"]', 'max', 1755848516.00, NULL),
(953, 1755845280, 10080, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\NearestRestaurantController.php:68\"]', 'count', 2.00, NULL),
(957, 1755845280, 10080, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\NearestRestaurantController.php:68\"]', 'max', 1755849033.00, NULL),
(969, 1755845280, 10080, 'slow_request', '[\"GET\",\"\\/api\\/v1\\/restaurant\\/{id}\\/menu\",\"App\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\NearestRestaurantController@menuWithCategories\"]', 'count', 1.00, NULL),
(973, 1755845280, 10080, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\NearestRestaurantController.php:23\"]', 'count', 1.00, NULL),
(977, 1755845280, 10080, 'slow_request', '[\"GET\",\"\\/api\\/v1\\/restaurant\\/{id}\\/menu\",\"App\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\NearestRestaurantController@menuWithCategories\"]', 'max', 5035.00, NULL),
(981, 1755845280, 10080, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\NearestRestaurantController.php:23\"]', 'max', 1755849941.00, NULL),
(985, 1755845280, 10080, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"database\\\\seeders\\\\ReviewSeeder.php:110\"]', 'count', 2.00, NULL),
(989, 1755845280, 10080, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"database\\\\seeders\\\\ReviewSeeder.php:110\"]', 'max', 1755852296.00, NULL),
(993, 1755845280, 10080, 'slow_request', '[\"POST\",\"\\/api\\/v1\\/auth\\/login\",\"App\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\AuthController@login\"]', 'count', 1.00, NULL),
(997, 1755845280, 10080, 'slow_user_request', '6', 'count', 1.00, NULL),
(1001, 1755845280, 10080, 'user_request', '6', 'count', 1.00, NULL),
(1005, 1755845280, 10080, 'slow_request', '[\"POST\",\"\\/api\\/v1\\/auth\\/login\",\"App\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\AuthController@login\"]', 'max', 1254.00, NULL),
(1009, 1755845280, 10080, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"database\\\\seeders\\\\DemoFullSeeder.php:16\"]', 'count', 2.00, NULL),
(1013, 1755845280, 10080, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"database\\\\seeders\\\\DemoFullSeeder.php:16\"]', 'max', 1755853437.00, NULL),
(1017, 1755845280, 10080, 'exception', '[\"Illuminate\\\\Database\\\\UniqueConstraintViolationException\",\"database\\\\seeders\\\\DemoFullSeeder.php:16\"]', 'count', 2.00, NULL),
(1021, 1755845280, 10080, 'exception', '[\"Illuminate\\\\Database\\\\UniqueConstraintViolationException\",\"database\\\\seeders\\\\DemoFullSeeder.php:16\"]', 'max', 1755853499.00, NULL),
(1025, 1755845280, 10080, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"database\\\\seeders\\\\DemoFullSeeder.php:42\"]', 'count', 2.00, NULL),
(1029, 1755845280, 10080, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"database\\\\seeders\\\\DemoFullSeeder.php:42\"]', 'max', 1755853560.00, NULL),
(1033, 1755845280, 10080, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"database\\\\seeders\\\\DemoFullSeeder.php:62\"]', 'count', 2.00, NULL),
(1037, 1755845280, 10080, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"database\\\\seeders\\\\DemoFullSeeder.php:62\"]', 'max', 1755853827.00, NULL),
(1039, 1756101960, 360, 'slow_request', '[\"POST\",\"\\/api\\/v1\\/auth\\/login\",\"App\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\AuthController@login\"]', 'count', 1.00, NULL),
(1040, 1756101600, 1440, 'slow_request', '[\"POST\",\"\\/api\\/v1\\/auth\\/login\",\"App\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\AuthController@login\"]', 'count', 1.00, NULL),
(1041, 1756097280, 10080, 'slow_request', '[\"POST\",\"\\/api\\/v1\\/auth\\/login\",\"App\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\AuthController@login\"]', 'count', 1.00, NULL),
(1043, 1756101960, 360, 'slow_user_request', '6', 'count', 1.00, NULL),
(1044, 1756101600, 1440, 'slow_user_request', '6', 'count', 1.00, NULL),
(1045, 1756097280, 10080, 'slow_user_request', '6', 'count', 1.00, NULL),
(1047, 1756101960, 360, 'user_request', '6', 'count', 1.00, NULL),
(1048, 1756101600, 1440, 'user_request', '6', 'count', 1.00, NULL),
(1049, 1756097280, 10080, 'user_request', '6', 'count', 7.00, NULL),
(1051, 1756101960, 360, 'slow_request', '[\"POST\",\"\\/api\\/v1\\/auth\\/login\",\"App\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\AuthController@login\"]', 'max', 1248.00, NULL),
(1052, 1756101600, 1440, 'slow_request', '[\"POST\",\"\\/api\\/v1\\/auth\\/login\",\"App\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\AuthController@login\"]', 'max', 1248.00, NULL),
(1053, 1756097280, 10080, 'slow_request', '[\"POST\",\"\\/api\\/v1\\/auth\\/login\",\"App\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\AuthController@login\"]', 'max', 1248.00, NULL),
(1055, 1756102680, 360, 'exception', '[\"Illuminate\\\\Contracts\\\\Container\\\\BindingResolutionException\",\"app\\\\Http\\\\Middleware\\\\CorsMiddleware.php:27\"]', 'count', 1.00, NULL),
(1056, 1756101600, 1440, 'exception', '[\"Illuminate\\\\Contracts\\\\Container\\\\BindingResolutionException\",\"app\\\\Http\\\\Middleware\\\\CorsMiddleware.php:27\"]', 'count', 1.00, NULL),
(1057, 1756097280, 10080, 'exception', '[\"Illuminate\\\\Contracts\\\\Container\\\\BindingResolutionException\",\"app\\\\Http\\\\Middleware\\\\CorsMiddleware.php:27\"]', 'count', 2.00, NULL),
(1059, 1756102680, 360, 'exception', '[\"Illuminate\\\\Contracts\\\\Container\\\\BindingResolutionException\",\"app\\\\Http\\\\Middleware\\\\CorsMiddleware.php:27\"]', 'max', 1756102936.00, NULL),
(1060, 1756101600, 1440, 'exception', '[\"Illuminate\\\\Contracts\\\\Container\\\\BindingResolutionException\",\"app\\\\Http\\\\Middleware\\\\CorsMiddleware.php:27\"]', 'max', 1756102936.00, NULL),
(1061, 1756097280, 10080, 'exception', '[\"Illuminate\\\\Contracts\\\\Container\\\\BindingResolutionException\",\"app\\\\Http\\\\Middleware\\\\CorsMiddleware.php:27\"]', 'max', 1756103060.00, NULL),
(1063, 1756103040, 360, 'slow_request', '[\"POST\",\"\\/api\\/payment\\/intent\\/{orderId}\",\"App\\\\Http\\\\Controllers\\\\API\\\\v1\\\\PaymentController@createIntent\"]', 'count', 1.00, NULL),
(1064, 1756103040, 1440, 'slow_request', '[\"POST\",\"\\/api\\/payment\\/intent\\/{orderId}\",\"App\\\\Http\\\\Controllers\\\\API\\\\v1\\\\PaymentController@createIntent\"]', 'count', 3.00, NULL),
(1065, 1756097280, 10080, 'slow_request', '[\"POST\",\"\\/api\\/payment\\/intent\\/{orderId}\",\"App\\\\Http\\\\Controllers\\\\API\\\\v1\\\\PaymentController@createIntent\"]', 'count', 3.00, NULL),
(1067, 1756103040, 360, 'exception', '[\"Illuminate\\\\Contracts\\\\Container\\\\BindingResolutionException\",\"app\\\\Http\\\\Middleware\\\\CorsMiddleware.php:27\"]', 'count', 1.00, NULL),
(1068, 1756103040, 1440, 'exception', '[\"Illuminate\\\\Contracts\\\\Container\\\\BindingResolutionException\",\"app\\\\Http\\\\Middleware\\\\CorsMiddleware.php:27\"]', 'count', 1.00, NULL),
(1071, 1756103040, 360, 'slow_request', '[\"POST\",\"\\/api\\/payment\\/intent\\/{orderId}\",\"App\\\\Http\\\\Controllers\\\\API\\\\v1\\\\PaymentController@createIntent\"]', 'max', 1022.00, NULL),
(1072, 1756103040, 1440, 'slow_request', '[\"POST\",\"\\/api\\/payment\\/intent\\/{orderId}\",\"App\\\\Http\\\\Controllers\\\\API\\\\v1\\\\PaymentController@createIntent\"]', 'max', 1807.00, NULL),
(1073, 1756097280, 10080, 'slow_request', '[\"POST\",\"\\/api\\/payment\\/intent\\/{orderId}\",\"App\\\\Http\\\\Controllers\\\\API\\\\v1\\\\PaymentController@createIntent\"]', 'max', 1807.00, NULL),
(1075, 1756103040, 360, 'exception', '[\"Illuminate\\\\Contracts\\\\Container\\\\BindingResolutionException\",\"app\\\\Http\\\\Middleware\\\\CorsMiddleware.php:27\"]', 'max', 1756103060.00, NULL),
(1076, 1756103040, 1440, 'exception', '[\"Illuminate\\\\Contracts\\\\Container\\\\BindingResolutionException\",\"app\\\\Http\\\\Middleware\\\\CorsMiddleware.php:27\"]', 'max', 1756103060.00, NULL),
(1079, 1756103040, 360, 'exception', '[\"Error\",\"app\\\\Services\\\\StripeService.php:15\"]', 'count', 1.00, NULL),
(1080, 1756103040, 1440, 'exception', '[\"Error\",\"app\\\\Services\\\\StripeService.php:15\"]', 'count', 1.00, NULL),
(1081, 1756097280, 10080, 'exception', '[\"Error\",\"app\\\\Services\\\\StripeService.php:15\"]', 'count', 1.00, NULL),
(1083, 1756103040, 360, 'exception', '[\"Error\",\"app\\\\Services\\\\StripeService.php:15\"]', 'max', 1756103122.00, NULL),
(1084, 1756103040, 1440, 'exception', '[\"Error\",\"app\\\\Services\\\\StripeService.php:15\"]', 'max', 1756103122.00, NULL),
(1085, 1756097280, 10080, 'exception', '[\"Error\",\"app\\\\Services\\\\StripeService.php:15\"]', 'max', 1756103122.00, NULL),
(1087, 1756103400, 360, 'slow_request', '[\"POST\",\"\\/api\\/payment\\/intent\\/{orderId}\",\"App\\\\Http\\\\Controllers\\\\API\\\\v1\\\\PaymentController@createIntent\"]', 'count', 1.00, NULL),
(1089, 1756103400, 360, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Services\\\\StripeService.php:30\"]', 'count', 1.00, NULL),
(1090, 1756103040, 1440, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Services\\\\StripeService.php:30\"]', 'count', 1.00, NULL),
(1091, 1756097280, 10080, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Services\\\\StripeService.php:30\"]', 'count', 1.00, NULL),
(1095, 1756103400, 360, 'slow_request', '[\"POST\",\"\\/api\\/payment\\/intent\\/{orderId}\",\"App\\\\Http\\\\Controllers\\\\API\\\\v1\\\\PaymentController@createIntent\"]', 'max', 1807.00, NULL),
(1097, 1756103400, 360, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Services\\\\StripeService.php:30\"]', 'max', 1756103439.00, NULL),
(1098, 1756103040, 1440, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Services\\\\StripeService.php:30\"]', 'max', 1756103439.00, NULL),
(1099, 1756097280, 10080, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Services\\\\StripeService.php:30\"]', 'max', 1756103439.00, NULL),
(1103, 1756104120, 360, 'exception', '[\"ErrorException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\PaymentController.php:62\"]', 'count', 1.00, NULL),
(1104, 1756103040, 1440, 'exception', '[\"ErrorException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\PaymentController.php:62\"]', 'count', 1.00, NULL),
(1105, 1756097280, 10080, 'exception', '[\"ErrorException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\PaymentController.php:62\"]', 'count', 2.00, NULL),
(1107, 1756104120, 360, 'exception', '[\"ErrorException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\PaymentController.php:62\"]', 'max', 1756104146.00, NULL),
(1108, 1756103040, 1440, 'exception', '[\"ErrorException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\PaymentController.php:62\"]', 'max', 1756104146.00, NULL),
(1109, 1756097280, 10080, 'exception', '[\"ErrorException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\PaymentController.php:62\"]', 'max', 1756104871.00, NULL),
(1111, 1756104120, 360, 'slow_request', '[\"POST\",\"\\/api\\/payment\\/intent\\/{orderId}\",\"App\\\\Http\\\\Controllers\\\\API\\\\v1\\\\PaymentController@createIntent\"]', 'count', 1.00, NULL),
(1115, 1756104120, 360, 'slow_request', '[\"POST\",\"\\/api\\/payment\\/intent\\/{orderId}\",\"App\\\\Http\\\\Controllers\\\\API\\\\v1\\\\PaymentController@createIntent\"]', 'max', 1271.00, NULL),
(1119, 1756104840, 360, 'slow_request', '[\"GET\",\"\\/api\\/payment\\/history\",\"App\\\\Http\\\\Controllers\\\\API\\\\v1\\\\PaymentController@history\"]', 'count', 1.00, NULL),
(1120, 1756104480, 1440, 'slow_request', '[\"GET\",\"\\/api\\/payment\\/history\",\"App\\\\Http\\\\Controllers\\\\API\\\\v1\\\\PaymentController@history\"]', 'count', 1.00, NULL),
(1121, 1756097280, 10080, 'slow_request', '[\"GET\",\"\\/api\\/payment\\/history\",\"App\\\\Http\\\\Controllers\\\\API\\\\v1\\\\PaymentController@history\"]', 'count', 1.00, NULL),
(1123, 1756104840, 360, 'exception', '[\"ErrorException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\PaymentController.php:62\"]', 'count', 1.00, NULL),
(1124, 1756104480, 1440, 'exception', '[\"ErrorException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\PaymentController.php:62\"]', 'count', 1.00, NULL),
(1127, 1756104840, 360, 'slow_request', '[\"GET\",\"\\/api\\/payment\\/history\",\"App\\\\Http\\\\Controllers\\\\API\\\\v1\\\\PaymentController@history\"]', 'max', 5204.00, NULL),
(1128, 1756104480, 1440, 'slow_request', '[\"GET\",\"\\/api\\/payment\\/history\",\"App\\\\Http\\\\Controllers\\\\API\\\\v1\\\\PaymentController@history\"]', 'max', 5204.00, NULL),
(1129, 1756097280, 10080, 'slow_request', '[\"GET\",\"\\/api\\/payment\\/history\",\"App\\\\Http\\\\Controllers\\\\API\\\\v1\\\\PaymentController@history\"]', 'max', 5204.00, NULL),
(1131, 1756104840, 360, 'exception', '[\"ErrorException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\PaymentController.php:62\"]', 'max', 1756104871.00, NULL),
(1132, 1756104480, 1440, 'exception', '[\"ErrorException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\PaymentController.php:62\"]', 'max', 1756104871.00, NULL),
(1135, 1756104840, 360, 'user_request', '6', 'count', 1.00, NULL),
(1136, 1756104480, 1440, 'user_request', '6', 'count', 6.00, NULL),
(1138, 1756104840, 360, 'cache_miss', 'Jg6cRJEE0V1FWjWU', 'count', 2.00, NULL),
(1139, 1756104480, 1440, 'cache_miss', 'Jg6cRJEE0V1FWjWU', 'count', 6.00, NULL),
(1140, 1756097280, 10080, 'cache_miss', 'Jg6cRJEE0V1FWjWU', 'count', 6.00, NULL),
(1143, 1756105200, 360, 'user_request', '6', 'count', 5.00, NULL),
(1145, 1756105200, 360, 'cache_miss', 'Jg6cRJEE0V1FWjWU', 'count', 4.00, NULL),
(1164, 1756105200, 360, 'cache_miss', '2sNW8vCdCCO0mUNo', 'count', 4.00, NULL),
(1165, 1756104480, 1440, 'cache_miss', '2sNW8vCdCCO0mUNo', 'count', 4.00, NULL),
(1166, 1756097280, 10080, 'cache_miss', '2sNW8vCdCCO0mUNo', 'count', 4.00, NULL),
(1179, 1756107720, 360, 'user_request', '6', 'count', 2.00, NULL),
(1180, 1756107360, 1440, 'user_request', '6', 'count', 7.00, NULL),
(1181, 1756107360, 10080, 'user_request', '6', 'count', 34.00, NULL),
(1183, 1756107720, 360, 'cache_miss', 'VSdb44prdid8I63N', 'count', 2.00, NULL),
(1184, 1756107360, 1440, 'cache_miss', 'VSdb44prdid8I63N', 'count', 12.00, NULL),
(1185, 1756107360, 10080, 'cache_miss', 'VSdb44prdid8I63N', 'count', 12.00, NULL),
(1191, 1756108080, 360, 'user_request', '6', 'count', 5.00, NULL),
(1193, 1756108080, 360, 'cache_miss', 'VSdb44prdid8I63N', 'count', 10.00, NULL),
(1207, 1756108080, 360, 'slow_request', '[\"POST\",\"\\/api\\/v1\\/payment\\/intent\\/{orderId}\",\"App\\\\Http\\\\Controllers\\\\API\\\\v1\\\\PaymentController@createIntent\"]', 'count', 1.00, NULL),
(1208, 1756107360, 1440, 'slow_request', '[\"POST\",\"\\/api\\/v1\\/payment\\/intent\\/{orderId}\",\"App\\\\Http\\\\Controllers\\\\API\\\\v1\\\\PaymentController@createIntent\"]', 'count', 1.00, NULL),
(1209, 1756107360, 10080, 'slow_request', '[\"POST\",\"\\/api\\/v1\\/payment\\/intent\\/{orderId}\",\"App\\\\Http\\\\Controllers\\\\API\\\\v1\\\\PaymentController@createIntent\"]', 'count', 1.00, NULL),
(1211, 1756108080, 360, 'slow_user_request', '6', 'count', 1.00, NULL),
(1212, 1756107360, 1440, 'slow_user_request', '6', 'count', 1.00, NULL),
(1213, 1756107360, 10080, 'slow_user_request', '6', 'count', 1.00, NULL),
(1223, 1756108080, 360, 'slow_request', '[\"POST\",\"\\/api\\/v1\\/payment\\/intent\\/{orderId}\",\"App\\\\Http\\\\Controllers\\\\API\\\\v1\\\\PaymentController@createIntent\"]', 'max', 1170.00, NULL),
(1224, 1756107360, 1440, 'slow_request', '[\"POST\",\"\\/api\\/v1\\/payment\\/intent\\/{orderId}\",\"App\\\\Http\\\\Controllers\\\\API\\\\v1\\\\PaymentController@createIntent\"]', 'max', 1170.00, NULL),
(1225, 1756107360, 10080, 'slow_request', '[\"POST\",\"\\/api\\/v1\\/payment\\/intent\\/{orderId}\",\"App\\\\Http\\\\Controllers\\\\API\\\\v1\\\\PaymentController@createIntent\"]', 'max', 1170.00, NULL),
(1243, 1756108440, 360, 'user_request', '3', 'count', 1.00, NULL),
(1244, 1756107360, 1440, 'user_request', '3', 'count', 1.00, NULL),
(1245, 1756107360, 10080, 'user_request', '3', 'count', 4.00, NULL),
(1246, 1756108800, 60, 'user_request', '3', 'count', 1.00, NULL),
(1247, 1756108800, 360, 'user_request', '3', 'count', 1.00, NULL),
(1248, 1756108800, 1440, 'user_request', '3', 'count', 3.00, NULL),
(1249, 1756108800, 60, 'cache_miss', 'DRA5tb3VIYvx30SC', 'count', 2.00, NULL),
(1250, 1756108800, 360, 'cache_miss', 'DRA5tb3VIYvx30SC', 'count', 2.00, NULL),
(1251, 1756108800, 1440, 'cache_miss', 'DRA5tb3VIYvx30SC', 'count', 6.00, NULL),
(1252, 1756107360, 10080, 'cache_miss', 'DRA5tb3VIYvx30SC', 'count', 6.00, NULL),
(1254, 1756109160, 60, 'user_request', '3', 'count', 2.00, NULL),
(1255, 1756109160, 360, 'user_request', '3', 'count', 2.00, NULL),
(1256, 1756109160, 60, 'cache_miss', 'DRA5tb3VIYvx30SC', 'count', 4.00, NULL),
(1257, 1756109160, 360, 'cache_miss', 'DRA5tb3VIYvx30SC', 'count', 4.00, NULL),
(1262, 1756109160, 60, 'user_request', '6', 'count', 1.00, NULL),
(1263, 1756109160, 360, 'user_request', '6', 'count', 4.00, NULL),
(1264, 1756108800, 1440, 'user_request', '6', 'count', 6.00, NULL),
(1274, 1756109220, 60, 'user_request', '6', 'count', 1.00, NULL),
(1275, 1756109220, 60, 'cache_miss', 'dADBKCsBKGy1x0Ci', 'count', 2.00, NULL),
(1276, 1756109160, 360, 'cache_miss', 'dADBKCsBKGy1x0Ci', 'count', 6.00, NULL),
(1277, 1756108800, 1440, 'cache_miss', 'dADBKCsBKGy1x0Ci', 'count', 10.00, NULL),
(1278, 1756107360, 10080, 'cache_miss', 'dADBKCsBKGy1x0Ci', 'count', 26.00, NULL),
(1282, 1756109280, 60, 'user_request', '6', 'count', 2.00, NULL),
(1283, 1756109280, 60, 'cache_miss', 'dADBKCsBKGy1x0Ci', 'count', 4.00, NULL),
(1298, 1756109940, 60, 'user_request', '6', 'count', 2.00, NULL),
(1299, 1756109880, 360, 'user_request', '6', 'count', 2.00, NULL),
(1300, 1756109940, 60, 'cache_miss', 'dADBKCsBKGy1x0Ci', 'count', 4.00, NULL),
(1301, 1756109880, 360, 'cache_miss', 'dADBKCsBKGy1x0Ci', 'count', 4.00, NULL),
(1302, 1756109940, 60, 'exception', '[\"Stripe\\\\Exception\\\\InvalidArgumentException\",\"app\\\\Services\\\\StripeService.php:15\"]', 'count', 1.00, NULL),
(1303, 1756109880, 360, 'exception', '[\"Stripe\\\\Exception\\\\InvalidArgumentException\",\"app\\\\Services\\\\StripeService.php:15\"]', 'count', 1.00, NULL),
(1304, 1756108800, 1440, 'exception', '[\"Stripe\\\\Exception\\\\InvalidArgumentException\",\"app\\\\Services\\\\StripeService.php:15\"]', 'count', 1.00, NULL),
(1305, 1756107360, 10080, 'exception', '[\"Stripe\\\\Exception\\\\InvalidArgumentException\",\"app\\\\Services\\\\StripeService.php:15\"]', 'count', 1.00, NULL),
(1310, 1756109940, 60, 'exception', '[\"Stripe\\\\Exception\\\\InvalidArgumentException\",\"app\\\\Services\\\\StripeService.php:15\"]', 'max', 1756109963.00, NULL),
(1311, 1756109880, 360, 'exception', '[\"Stripe\\\\Exception\\\\InvalidArgumentException\",\"app\\\\Services\\\\StripeService.php:15\"]', 'max', 1756109963.00, NULL),
(1312, 1756108800, 1440, 'exception', '[\"Stripe\\\\Exception\\\\InvalidArgumentException\",\"app\\\\Services\\\\StripeService.php:15\"]', 'max', 1756109963.00, NULL),
(1313, 1756107360, 10080, 'exception', '[\"Stripe\\\\Exception\\\\InvalidArgumentException\",\"app\\\\Services\\\\StripeService.php:15\"]', 'max', 1756109963.00, NULL),
(1322, 1756110420, 60, 'user_request', '6', 'count', 1.00, NULL),
(1323, 1756110240, 360, 'user_request', '6', 'count', 1.00, NULL),
(1324, 1756110240, 1440, 'user_request', '6', 'count', 3.00, NULL),
(1325, 1756110420, 60, 'cache_miss', 'dADBKCsBKGy1x0Ci', 'count', 2.00, NULL),
(1326, 1756110240, 360, 'cache_miss', 'dADBKCsBKGy1x0Ci', 'count', 2.00, NULL),
(1327, 1756110240, 1440, 'cache_miss', 'dADBKCsBKGy1x0Ci', 'count', 6.00, NULL),
(1330, 1756111620, 60, 'user_request', '6', 'count', 2.00, NULL),
(1331, 1756111320, 360, 'user_request', '6', 'count', 2.00, NULL),
(1332, 1756111620, 60, 'cache_miss', 'dADBKCsBKGy1x0Ci', 'count', 4.00, NULL),
(1333, 1756111320, 360, 'cache_miss', 'dADBKCsBKGy1x0Ci', 'count', 4.00, NULL),
(1346, 1756111860, 60, 'user_request', '6', 'count', 1.00, NULL),
(1347, 1756111680, 360, 'user_request', '6', 'count', 4.00, NULL),
(1348, 1756111680, 1440, 'user_request', '6', 'count', 18.00, NULL),
(1349, 1756111860, 60, 'cache_miss', 'dADBKCsBKGy1x0Ci', 'count', 2.00, NULL),
(1350, 1756111680, 360, 'cache_miss', 'dADBKCsBKGy1x0Ci', 'count', 2.00, NULL),
(1351, 1756111680, 1440, 'cache_miss', 'dADBKCsBKGy1x0Ci', 'count', 10.00, NULL),
(1354, 1756111920, 60, 'user_request', '6', 'count', 1.00, NULL),
(1358, 1756111980, 60, 'user_request', '6', 'count', 2.00, NULL),
(1359, 1756111980, 60, 'cache_miss', 'fjMClhlXHC7CtGsf', 'count', 4.00, NULL),
(1360, 1756111680, 360, 'cache_miss', 'fjMClhlXHC7CtGsf', 'count', 4.00, NULL),
(1361, 1756111680, 1440, 'cache_miss', 'fjMClhlXHC7CtGsf', 'count', 6.00, NULL),
(1362, 1756107360, 10080, 'cache_miss', 'fjMClhlXHC7CtGsf', 'count', 6.00, NULL),
(1374, 1756112100, 60, 'user_request', '6', 'count', 2.00, NULL),
(1375, 1756112040, 360, 'user_request', '6', 'count', 6.00, NULL),
(1376, 1756112100, 60, 'cache_miss', 'fjMClhlXHC7CtGsf', 'count', 2.00, NULL),
(1377, 1756112040, 360, 'cache_miss', 'fjMClhlXHC7CtGsf', 'count', 2.00, NULL),
(1386, 1756112160, 60, 'user_request', '6', 'count', 4.00, NULL),
(1387, 1756112160, 60, 'cache_miss', 'dADBKCsBKGy1x0Ci', 'count', 6.00, NULL),
(1388, 1756112040, 360, 'cache_miss', 'dADBKCsBKGy1x0Ci', 'count', 6.00, NULL),
(1394, 1756112160, 60, 'cache_miss', '5NpQ2Jz7QPZYR1W6', 'count', 2.00, NULL),
(1395, 1756112040, 360, 'cache_miss', '5NpQ2Jz7QPZYR1W6', 'count', 2.00, NULL),
(1396, 1756111680, 1440, 'cache_miss', '5NpQ2Jz7QPZYR1W6', 'count', 4.00, NULL),
(1397, 1756107360, 10080, 'cache_miss', '5NpQ2Jz7QPZYR1W6', 'count', 4.00, NULL),
(1418, 1756112580, 60, 'user_request', '6', 'count', 2.00, NULL),
(1419, 1756112400, 360, 'user_request', '6', 'count', 2.00, NULL),
(1420, 1756112580, 60, 'cache_miss', '5NpQ2Jz7QPZYR1W6', 'count', 2.00, NULL),
(1421, 1756112400, 360, 'cache_miss', '5NpQ2Jz7QPZYR1W6', 'count', 2.00, NULL),
(1426, 1756112580, 60, 'cache_miss', 'dADBKCsBKGy1x0Ci', 'count', 2.00, NULL),
(1427, 1756112400, 360, 'cache_miss', 'dADBKCsBKGy1x0Ci', 'count', 2.00, NULL),
(1434, 1756112700, 60, 'user_request', '4', 'count', 2.00, NULL),
(1435, 1756112400, 360, 'user_request', '4', 'count', 2.00, NULL),
(1436, 1756111680, 1440, 'user_request', '4', 'count', 2.00, NULL),
(1437, 1756107360, 10080, 'user_request', '4', 'count', 2.00, NULL),
(1438, 1756112700, 60, 'cache_miss', 'Cth8mqp123fxCbIh', 'count', 2.00, NULL),
(1439, 1756112400, 360, 'cache_miss', 'Cth8mqp123fxCbIh', 'count', 2.00, NULL),
(1440, 1756111680, 1440, 'cache_miss', 'Cth8mqp123fxCbIh', 'count', 2.00, NULL),
(1441, 1756107360, 10080, 'cache_miss', 'Cth8mqp123fxCbIh', 'count', 2.00, NULL),
(1446, 1756112760, 60, 'user_request', '6', 'count', 1.00, NULL),
(1447, 1756112760, 360, 'user_request', '6', 'count', 6.00, NULL),
(1450, 1756112820, 60, 'user_request', '6', 'count', 2.00, NULL),
(1451, 1756112820, 60, 'cache_miss', '6lv68ouHttz5nTbA', 'count', 4.00, NULL),
(1452, 1756112760, 360, 'cache_miss', '6lv68ouHttz5nTbA', 'count', 10.00, NULL),
(1453, 1756111680, 1440, 'cache_miss', '6lv68ouHttz5nTbA', 'count', 10.00, NULL),
(1454, 1756107360, 10080, 'cache_miss', '6lv68ouHttz5nTbA', 'count', 10.00, NULL),
(1466, 1756112880, 60, 'user_request', '6', 'count', 1.00, NULL),
(1467, 1756112880, 60, 'cache_miss', '6lv68ouHttz5nTbA', 'count', 2.00, NULL),
(1474, 1756112940, 60, 'user_request', '6', 'count', 2.00, NULL),
(1475, 1756112940, 60, 'cache_miss', '6lv68ouHttz5nTbA', 'count', 4.00, NULL),
(1490, 1756181700, 60, 'slow_request', '[\"GET\",\"\\/\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\RegisterController@showRegistrationForm\"]', 'count', 1.00, NULL),
(1491, 1756181520, 360, 'slow_request', '[\"GET\",\"\\/\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\RegisterController@showRegistrationForm\"]', 'count', 1.00, NULL),
(1492, 1756180800, 1440, 'slow_request', '[\"GET\",\"\\/\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\RegisterController@showRegistrationForm\"]', 'count', 1.00, NULL),
(1493, 1756177920, 10080, 'slow_request', '[\"GET\",\"\\/\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\RegisterController@showRegistrationForm\"]', 'count', 1.00, NULL),
(1494, 1756181700, 60, 'slow_request', '[\"GET\",\"\\/\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\RegisterController@showRegistrationForm\"]', 'max', 2193.00, NULL),
(1495, 1756181520, 360, 'slow_request', '[\"GET\",\"\\/\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\RegisterController@showRegistrationForm\"]', 'max', 2193.00, NULL),
(1496, 1756180800, 1440, 'slow_request', '[\"GET\",\"\\/\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\RegisterController@showRegistrationForm\"]', 'max', 2193.00, NULL),
(1497, 1756177920, 10080, 'slow_request', '[\"GET\",\"\\/\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\RegisterController@showRegistrationForm\"]', 'max', 2193.00, NULL),
(1498, 1756181760, 60, 'user_request', '1', 'count', 3.00, NULL),
(1499, 1756181520, 360, 'user_request', '1', 'count', 3.00, NULL),
(1500, 1756180800, 1440, 'user_request', '1', 'count', 4.00, NULL),
(1501, 1756177920, 10080, 'user_request', '1', 'count', 4.00, NULL),
(1510, 1756182180, 60, 'slow_request', '[\"POST\",\"\\/logout\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\LoginController@logout\"]', 'count', 1.00, NULL),
(1511, 1756181880, 360, 'slow_request', '[\"POST\",\"\\/logout\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\LoginController@logout\"]', 'count', 1.00, NULL),
(1512, 1756180800, 1440, 'slow_request', '[\"POST\",\"\\/logout\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\LoginController@logout\"]', 'count', 1.00, NULL),
(1513, 1756177920, 10080, 'slow_request', '[\"POST\",\"\\/logout\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\LoginController@logout\"]', 'count', 1.00, NULL),
(1514, 1756182180, 60, 'slow_user_request', '1', 'count', 1.00, NULL),
(1515, 1756181880, 360, 'slow_user_request', '1', 'count', 1.00, NULL),
(1516, 1756180800, 1440, 'slow_user_request', '1', 'count', 1.00, NULL),
(1517, 1756177920, 10080, 'slow_user_request', '1', 'count', 1.00, NULL),
(1518, 1756182180, 60, 'user_request', '1', 'count', 1.00, NULL),
(1519, 1756181880, 360, 'user_request', '1', 'count', 1.00, NULL),
(1522, 1756182180, 60, 'slow_request', '[\"POST\",\"\\/logout\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\LoginController@logout\"]', 'max', 2758.00, NULL),
(1523, 1756181880, 360, 'slow_request', '[\"POST\",\"\\/logout\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\LoginController@logout\"]', 'max', 2758.00, NULL),
(1524, 1756180800, 1440, 'slow_request', '[\"POST\",\"\\/logout\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\LoginController@logout\"]', 'max', 2758.00, NULL),
(1525, 1756177920, 10080, 'slow_request', '[\"POST\",\"\\/logout\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\LoginController@logout\"]', 'max', 2758.00, NULL),
(1526, 1756182180, 60, 'user_request', '3', 'count', 3.00, NULL),
(1527, 1756181880, 360, 'user_request', '3', 'count', 3.00, NULL),
(1528, 1756180800, 1440, 'user_request', '3', 'count', 3.00, NULL),
(1529, 1756177920, 10080, 'user_request', '3', 'count', 39.00, NULL),
(1530, 1756182180, 60, 'slow_request', '[\"GET\",\"\\/admin\\/dashboard\",\"Closure\"]', 'count', 1.00, NULL),
(1531, 1756181880, 360, 'slow_request', '[\"GET\",\"\\/admin\\/dashboard\",\"Closure\"]', 'count', 1.00, NULL),
(1532, 1756180800, 1440, 'slow_request', '[\"GET\",\"\\/admin\\/dashboard\",\"Closure\"]', 'count', 1.00, NULL),
(1533, 1756177920, 10080, 'slow_request', '[\"GET\",\"\\/admin\\/dashboard\",\"Closure\"]', 'count', 1.00, NULL),
(1534, 1756182180, 60, 'slow_user_request', '3', 'count', 1.00, NULL),
(1535, 1756181880, 360, 'slow_user_request', '3', 'count', 1.00, NULL),
(1536, 1756180800, 1440, 'slow_user_request', '3', 'count', 1.00, NULL),
(1537, 1756177920, 10080, 'slow_user_request', '3', 'count', 2.00, NULL),
(1542, 1756182180, 60, 'slow_request', '[\"GET\",\"\\/admin\\/dashboard\",\"Closure\"]', 'max', 1014.00, NULL),
(1543, 1756181880, 360, 'slow_request', '[\"GET\",\"\\/admin\\/dashboard\",\"Closure\"]', 'max', 1014.00, NULL),
(1544, 1756180800, 1440, 'slow_request', '[\"GET\",\"\\/admin\\/dashboard\",\"Closure\"]', 'max', 1014.00, NULL),
(1545, 1756177920, 10080, 'slow_request', '[\"GET\",\"\\/admin\\/dashboard\",\"Closure\"]', 'max', 1014.00, NULL),
(1550, 1756182660, 60, 'user_request', '3', 'count', 1.00, NULL),
(1551, 1756182600, 360, 'user_request', '3', 'count', 3.00, NULL),
(1552, 1756182240, 1440, 'user_request', '3', 'count', 9.00, NULL),
(1554, 1756182780, 60, 'user_request', '3', 'count', 1.00, NULL),
(1558, 1756182900, 60, 'user_request', '3', 'count', 1.00, NULL),
(1562, 1756183020, 60, 'user_request', '3', 'count', 3.00, NULL),
(1563, 1756182960, 360, 'user_request', '3', 'count', 3.00, NULL),
(1574, 1756183500, 60, 'user_request', '3', 'count', 2.00, NULL),
(1575, 1756183320, 360, 'user_request', '3', 'count', 3.00, NULL),
(1582, 1756183620, 60, 'user_request', '3', 'count', 1.00, NULL),
(1586, 1756183740, 60, 'user_request', '3', 'count', 2.00, NULL),
(1587, 1756183680, 360, 'user_request', '3', 'count', 7.00, NULL),
(1588, 1756183680, 1440, 'user_request', '3', 'count', 11.00, NULL),
(1594, 1756183800, 60, 'user_request', '3', 'count', 4.00, NULL),
(1610, 1756183860, 60, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"Closure\"]', 'count', 1.00, NULL),
(1611, 1756183680, 360, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"Closure\"]', 'count', 1.00, NULL),
(1612, 1756183680, 1440, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"Closure\"]', 'count', 1.00, NULL),
(1613, 1756177920, 10080, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"Closure\"]', 'count', 1.00, NULL),
(1614, 1756183860, 60, 'slow_user_request', '3', 'count', 1.00, NULL),
(1615, 1756183680, 360, 'slow_user_request', '3', 'count', 1.00, NULL),
(1616, 1756183680, 1440, 'slow_user_request', '3', 'count', 1.00, NULL),
(1617, 1756183860, 60, 'user_request', '3', 'count', 1.00, NULL),
(1618, 1756183860, 60, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(1619, 1756183680, 360, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(1620, 1756183680, 1440, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(1621, 1756177920, 10080, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 5.00, NULL),
(1626, 1756183860, 60, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"Closure\"]', 'max', 3338.00, NULL),
(1627, 1756183680, 360, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"Closure\"]', 'max', 3338.00, NULL),
(1628, 1756183680, 1440, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"Closure\"]', 'max', 3338.00, NULL),
(1629, 1756177920, 10080, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"Closure\"]', 'max', 3338.00, NULL),
(1630, 1756183860, 60, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756183914.00, NULL),
(1631, 1756183680, 360, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756183914.00, NULL),
(1632, 1756183680, 1440, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756183914.00, NULL),
(1633, 1756177920, 10080, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756185534.00, NULL),
(1634, 1756184280, 60, 'user_request', '3', 'count', 1.00, NULL),
(1635, 1756184040, 360, 'user_request', '3', 'count', 1.00, NULL),
(1638, 1756184400, 60, 'user_request', '3', 'count', 1.00, NULL),
(1639, 1756184400, 360, 'user_request', '3', 'count', 1.00, NULL),
(1642, 1756184820, 60, 'user_request', '3', 'count', 1.00, NULL),
(1643, 1756184760, 360, 'user_request', '3', 'count', 2.00, NULL),
(1646, 1756184940, 60, 'user_request', '3', 'count', 1.00, NULL),
(1650, 1756185120, 60, 'user_request', '3', 'count', 2.00, NULL),
(1651, 1756185120, 360, 'user_request', '3', 'count', 5.00, NULL),
(1652, 1756185120, 1440, 'user_request', '3', 'count', 12.00, NULL),
(1654, 1756185120, 60, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(1655, 1756185120, 360, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 3.00, NULL),
(1656, 1756185120, 1440, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 4.00, NULL),
(1662, 1756185120, 60, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756185160.00, NULL),
(1663, 1756185120, 360, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756185251.00, NULL),
(1664, 1756185120, 1440, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756185534.00, NULL),
(1666, 1756185180, 60, 'user_request', '3', 'count', 1.00, NULL),
(1667, 1756185180, 60, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(1674, 1756185180, 60, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756185215.00, NULL),
(1678, 1756185240, 60, 'user_request', '3', 'count', 2.00, NULL),
(1682, 1756185240, 60, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(1690, 1756185240, 60, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756185251.00, NULL),
(1694, 1756185480, 60, 'user_request', '3', 'count', 1.00, NULL),
(1695, 1756185480, 360, 'user_request', '3', 'count', 3.00, NULL),
(1696, 1756185480, 60, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(1697, 1756185480, 360, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(1702, 1756185480, 60, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756185534.00, NULL),
(1703, 1756185480, 360, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756185534.00, NULL),
(1706, 1756185600, 60, 'user_request', '3', 'count', 1.00, NULL),
(1707, 1756185600, 60, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:29\"]', 'count', 1.00, NULL),
(1708, 1756185480, 360, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:29\"]', 'count', 1.00, NULL),
(1709, 1756185120, 1440, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:29\"]', 'count', 1.00, NULL),
(1710, 1756177920, 10080, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:29\"]', 'count', 1.00, NULL),
(1714, 1756185600, 60, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:29\"]', 'max', 1756185650.00, NULL),
(1715, 1756185480, 360, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:29\"]', 'max', 1756185650.00, NULL),
(1716, 1756185120, 1440, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:29\"]', 'max', 1756185650.00, NULL),
(1717, 1756177920, 10080, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:29\"]', 'max', 1756185650.00, NULL),
(1718, 1756185720, 60, 'user_request', '3', 'count', 1.00, NULL),
(1719, 1756185720, 60, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(1720, 1756185480, 360, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(1721, 1756185120, 1440, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(1722, 1756177920, 10080, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(1726, 1756185720, 60, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756185745.00, NULL),
(1727, 1756185480, 360, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756185745.00, NULL),
(1728, 1756185120, 1440, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756185745.00, NULL),
(1729, 1756177920, 10080, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756185745.00, NULL),
(1730, 1756185900, 60, 'user_request', '3', 'count', 1.00, NULL),
(1731, 1756185840, 360, 'user_request', '3', 'count', 3.00, NULL),
(1734, 1756186140, 60, 'user_request', '3', 'count', 2.00, NULL),
(1742, 1756186440, 60, 'user_request', '3', 'count', 1.00, NULL),
(1743, 1756186200, 360, 'user_request', '3', 'count', 1.00, NULL),
(1746, 1756186560, 60, 'user_request', '3', 'count', 1.00, NULL),
(1747, 1756186560, 360, 'user_request', '3', 'count', 2.00, NULL),
(1748, 1756186560, 1440, 'user_request', '3', 'count', 4.00, NULL),
(1750, 1756186860, 60, 'user_request', '3', 'count', 1.00, NULL),
(1754, 1756186920, 60, 'user_request', '3', 'count', 1.00, NULL),
(1755, 1756186920, 360, 'user_request', '3', 'count', 2.00, NULL),
(1758, 1756187100, 60, 'user_request', '3', 'count', 1.00, NULL),
(1762, 1756188240, 60, 'user_request', '3', 'count', 2.00, NULL),
(1763, 1756188000, 360, 'user_request', '3', 'count', 2.00, NULL),
(1764, 1756188000, 1440, 'user_request', '3', 'count', 10.00, NULL),
(1765, 1756188000, 10080, 'user_request', '3', 'count', 105.00, NULL),
(1770, 1756188420, 60, 'user_request', '3', 'count', 1.00, NULL),
(1771, 1756188360, 360, 'user_request', '3', 'count', 4.00, NULL),
(1774, 1756188600, 60, 'user_request', '3', 'count', 3.00, NULL),
(1786, 1756189140, 60, 'user_request', '3', 'count', 2.00, NULL),
(1787, 1756189080, 360, 'user_request', '3', 'count', 4.00, NULL),
(1794, 1756189320, 60, 'user_request', '3', 'count', 1.00, NULL),
(1798, 1756189380, 60, 'user_request', '3', 'count', 1.00, NULL),
(1802, 1756189440, 60, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'count', 1.00, NULL),
(1803, 1756189440, 360, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'count', 1.00, NULL),
(1804, 1756189440, 1440, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'count', 1.00, NULL),
(1805, 1756188000, 10080, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'count', 2.00, NULL),
(1806, 1756189440, 60, 'slow_user_request', '3', 'count', 1.00, NULL),
(1807, 1756189440, 360, 'slow_user_request', '3', 'count', 1.00, NULL),
(1808, 1756189440, 1440, 'slow_user_request', '3', 'count', 1.00, NULL),
(1809, 1756188000, 10080, 'slow_user_request', '3', 'count', 2.00, NULL),
(1810, 1756189440, 60, 'user_request', '3', 'count', 2.00, NULL),
(1811, 1756189440, 360, 'user_request', '3', 'count', 3.00, NULL),
(1812, 1756189440, 1440, 'user_request', '3', 'count', 3.00, NULL),
(1813, 1756189440, 60, 'exception', '[\"InvalidArgumentException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(1814, 1756189440, 360, 'exception', '[\"InvalidArgumentException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(1815, 1756189440, 1440, 'exception', '[\"InvalidArgumentException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(1816, 1756188000, 10080, 'exception', '[\"InvalidArgumentException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(1818, 1756189440, 60, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'max', 1009.00, NULL),
(1819, 1756189440, 360, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'max', 1009.00, NULL),
(1820, 1756189440, 1440, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'max', 1009.00, NULL),
(1821, 1756188000, 10080, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'max', 1101.00, NULL),
(1822, 1756189440, 60, 'exception', '[\"InvalidArgumentException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756189449.00, NULL),
(1823, 1756189440, 360, 'exception', '[\"InvalidArgumentException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756189449.00, NULL),
(1824, 1756189440, 1440, 'exception', '[\"InvalidArgumentException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756189449.00, NULL),
(1825, 1756188000, 10080, 'exception', '[\"InvalidArgumentException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756189449.00, NULL),
(1830, 1756189560, 60, 'user_request', '3', 'count', 1.00, NULL),
(1834, 1756192200, 60, 'user_request', '3', 'count', 1.00, NULL),
(1835, 1756191960, 360, 'user_request', '3', 'count', 1.00, NULL),
(1836, 1756190880, 1440, 'user_request', '3', 'count', 1.00, NULL),
(1838, 1756192440, 60, 'user_request', '3', 'count', 1.00, NULL),
(1839, 1756192320, 360, 'user_request', '3', 'count', 4.00, NULL);
INSERT INTO `pulse_aggregates` (`id`, `bucket`, `period`, `type`, `key`, `aggregate`, `value`, `count`) VALUES
(1840, 1756192320, 1440, 'user_request', '3', 'count', 12.00, NULL),
(1842, 1756192500, 60, 'user_request', '3', 'count', 2.00, NULL),
(1843, 1756192500, 60, 'exception', '[\"ParseError\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(1844, 1756192320, 360, 'exception', '[\"ParseError\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(1845, 1756192320, 1440, 'exception', '[\"ParseError\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(1846, 1756188000, 10080, 'exception', '[\"ParseError\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(1850, 1756192500, 60, 'exception', '[\"ParseError\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756192507.00, NULL),
(1851, 1756192320, 360, 'exception', '[\"ParseError\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756192507.00, NULL),
(1852, 1756192320, 1440, 'exception', '[\"ParseError\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756192507.00, NULL),
(1853, 1756188000, 10080, 'exception', '[\"ParseError\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756192507.00, NULL),
(1858, 1756192620, 60, 'user_request', '3', 'count', 1.00, NULL),
(1862, 1756192920, 60, 'user_request', '3', 'count', 3.00, NULL),
(1863, 1756192680, 360, 'user_request', '3', 'count', 3.00, NULL),
(1864, 1756192920, 60, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:33\"]', 'count', 2.00, NULL),
(1865, 1756192680, 360, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:33\"]', 'count', 2.00, NULL),
(1866, 1756192320, 1440, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:33\"]', 'count', 3.00, NULL),
(1867, 1756188000, 10080, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:33\"]', 'count', 3.00, NULL),
(1870, 1756192920, 60, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:33\"]', 'max', 1756192939.00, NULL),
(1871, 1756192680, 360, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:33\"]', 'max', 1756192939.00, NULL),
(1872, 1756192320, 1440, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:33\"]', 'max', 1756193283.00, NULL),
(1873, 1756188000, 10080, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:33\"]', 'max', 1756193283.00, NULL),
(1890, 1756193280, 60, 'user_request', '3', 'count', 2.00, NULL),
(1891, 1756193040, 360, 'user_request', '3', 'count', 2.00, NULL),
(1892, 1756193280, 60, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:33\"]', 'count', 1.00, NULL),
(1893, 1756193040, 360, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:33\"]', 'count', 1.00, NULL),
(1898, 1756193280, 60, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:33\"]', 'max', 1756193283.00, NULL),
(1899, 1756193040, 360, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:33\"]', 'max', 1756193283.00, NULL),
(1902, 1756193280, 60, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:34\"]', 'count', 1.00, NULL),
(1903, 1756193040, 360, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:34\"]', 'count', 1.00, NULL),
(1904, 1756192320, 1440, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:34\"]', 'count', 1.00, NULL),
(1905, 1756188000, 10080, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:34\"]', 'count', 1.00, NULL),
(1910, 1756193280, 60, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:34\"]', 'max', 1756193311.00, NULL),
(1911, 1756193040, 360, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:34\"]', 'max', 1756193311.00, NULL),
(1912, 1756192320, 1440, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:34\"]', 'max', 1756193311.00, NULL),
(1913, 1756188000, 10080, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:34\"]', 'max', 1756193311.00, NULL),
(1914, 1756193400, 60, 'user_request', '3', 'count', 1.00, NULL),
(1915, 1756193400, 360, 'user_request', '3', 'count', 3.00, NULL),
(1918, 1756193580, 60, 'user_request', '3', 'count', 1.00, NULL),
(1922, 1756193700, 60, 'user_request', '3', 'count', 1.00, NULL),
(1926, 1756193820, 60, 'user_request', '3', 'count', 2.00, NULL),
(1927, 1756193760, 360, 'user_request', '3', 'count', 12.00, NULL),
(1928, 1756193760, 1440, 'user_request', '3', 'count', 29.00, NULL),
(1934, 1756193880, 60, 'user_request', '3', 'count', 3.00, NULL),
(1946, 1756193940, 60, 'user_request', '3', 'count', 1.00, NULL),
(1950, 1756194000, 60, 'user_request', '3', 'count', 5.00, NULL),
(1970, 1756194060, 60, 'user_request', '3', 'count', 1.00, NULL),
(1974, 1756194120, 60, 'user_request', '3', 'count', 1.00, NULL),
(1975, 1756194120, 360, 'user_request', '3', 'count', 5.00, NULL),
(1978, 1756194180, 60, 'user_request', '3', 'count', 3.00, NULL),
(1990, 1756194360, 60, 'user_request', '3', 'count', 1.00, NULL),
(1994, 1756194720, 60, 'user_request', '3', 'count', 3.00, NULL),
(1995, 1756194480, 360, 'user_request', '3', 'count', 4.00, NULL),
(2006, 1756194780, 60, 'user_request', '3', 'count', 1.00, NULL),
(2010, 1756194900, 60, 'user_request', '3', 'count', 2.00, NULL),
(2011, 1756194840, 360, 'user_request', '3', 'count', 8.00, NULL),
(2018, 1756194960, 60, 'user_request', '3', 'count', 5.00, NULL),
(2038, 1756195080, 60, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'count', 1.00, NULL),
(2039, 1756194840, 360, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'count', 1.00, NULL),
(2040, 1756193760, 1440, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'count', 1.00, NULL),
(2041, 1756195080, 60, 'slow_user_request', '3', 'count', 1.00, NULL),
(2042, 1756194840, 360, 'slow_user_request', '3', 'count', 1.00, NULL),
(2043, 1756193760, 1440, 'slow_user_request', '3', 'count', 1.00, NULL),
(2044, 1756195080, 60, 'user_request', '3', 'count', 1.00, NULL),
(2045, 1756195080, 60, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(2046, 1756194840, 360, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(2047, 1756193760, 1440, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(2048, 1756188000, 10080, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 2.00, NULL),
(2054, 1756195080, 60, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'max', 1101.00, NULL),
(2055, 1756194840, 360, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'max', 1101.00, NULL),
(2056, 1756193760, 1440, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'max', 1101.00, NULL),
(2057, 1756195080, 60, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756195089.00, NULL),
(2058, 1756194840, 360, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756195089.00, NULL),
(2059, 1756193760, 1440, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756195089.00, NULL),
(2060, 1756188000, 10080, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756198065.00, NULL),
(2062, 1756195260, 60, 'user_request', '3', 'count', 8.00, NULL),
(2063, 1756195200, 360, 'user_request', '3', 'count', 8.00, NULL),
(2064, 1756195200, 1440, 'user_request', '3', 'count', 31.00, NULL),
(2094, 1756195560, 60, 'user_request', '3', 'count', 3.00, NULL),
(2095, 1756195560, 360, 'user_request', '3', 'count', 10.00, NULL),
(2106, 1756195800, 60, 'user_request', '3', 'count', 6.00, NULL),
(2130, 1756195860, 60, 'user_request', '3', 'count', 1.00, NULL),
(2134, 1756196040, 60, 'user_request', '3', 'count', 1.00, NULL),
(2135, 1756195920, 360, 'user_request', '3', 'count', 2.00, NULL),
(2138, 1756196160, 60, 'user_request', '3', 'count', 1.00, NULL),
(2142, 1756196280, 60, 'user_request', '3', 'count', 1.00, NULL),
(2143, 1756196280, 360, 'user_request', '3', 'count', 11.00, NULL),
(2146, 1756196340, 60, 'user_request', '3', 'count', 5.00, NULL),
(2166, 1756196460, 60, 'user_request', '3', 'count', 3.00, NULL),
(2178, 1756196520, 60, 'user_request', '3', 'count', 2.00, NULL),
(2186, 1756196760, 60, 'user_request', '3', 'count', 3.00, NULL),
(2187, 1756196640, 360, 'user_request', '3', 'count', 11.00, NULL),
(2188, 1756196640, 1440, 'user_request', '3', 'count', 19.00, NULL),
(2198, 1756196820, 60, 'user_request', '3', 'count', 1.00, NULL),
(2202, 1756196880, 60, 'user_request', '3', 'count', 3.00, NULL),
(2214, 1756196940, 60, 'user_request', '3', 'count', 4.00, NULL),
(2218, 1756196940, 60, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(2219, 1756196640, 360, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(2220, 1756196640, 1440, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 2.00, NULL),
(2221, 1756188000, 10080, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 2.00, NULL),
(2226, 1756196940, 60, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756196947.00, NULL),
(2227, 1756196640, 360, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756196947.00, NULL),
(2228, 1756196640, 1440, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756197460.00, NULL),
(2229, 1756188000, 10080, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756197460.00, NULL),
(2238, 1756197060, 60, 'user_request', '3', 'count', 3.00, NULL),
(2239, 1756197000, 360, 'user_request', '3', 'count', 3.00, NULL),
(2250, 1756197420, 60, 'user_request', '3', 'count', 2.00, NULL),
(2251, 1756197360, 360, 'user_request', '3', 'count', 3.00, NULL),
(2252, 1756197420, 60, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(2253, 1756197360, 360, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(2258, 1756197420, 60, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756197460.00, NULL),
(2259, 1756197360, 360, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756197460.00, NULL),
(2262, 1756197420, 60, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:19\"]', 'count', 1.00, NULL),
(2263, 1756197360, 360, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:19\"]', 'count', 2.00, NULL),
(2264, 1756196640, 1440, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:19\"]', 'count', 2.00, NULL),
(2265, 1756188000, 10080, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:19\"]', 'count', 2.00, NULL),
(2270, 1756197420, 60, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:19\"]', 'max', 1756197467.00, NULL),
(2271, 1756197360, 360, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:19\"]', 'max', 1756197496.00, NULL),
(2272, 1756196640, 1440, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:19\"]', 'max', 1756197496.00, NULL),
(2273, 1756188000, 10080, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:19\"]', 'max', 1756197496.00, NULL),
(2274, 1756197480, 60, 'user_request', '3', 'count', 1.00, NULL),
(2275, 1756197480, 60, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:19\"]', 'count', 1.00, NULL),
(2282, 1756197480, 60, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:19\"]', 'max', 1756197496.00, NULL),
(2286, 1756198020, 60, 'user_request', '3', 'count', 2.00, NULL),
(2287, 1756197720, 360, 'user_request', '3', 'count', 2.00, NULL),
(2290, 1756198020, 60, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(2291, 1756197720, 360, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(2292, 1756196640, 1440, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(2298, 1756198020, 60, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756198065.00, NULL),
(2299, 1756197720, 360, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756198065.00, NULL),
(2300, 1756196640, 1440, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756198065.00, NULL),
(2302, 1756198080, 60, 'user_request', '3', 'count', 1.00, NULL),
(2303, 1756198080, 360, 'user_request', '3', 'count', 5.00, NULL),
(2304, 1756198080, 1440, 'user_request', '3', 'count', 26.00, NULL),
(2305, 1756198080, 10080, 'user_request', '3', 'count', 117.00, NULL),
(2306, 1756198080, 60, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 'count', 1.00, NULL),
(2307, 1756198080, 360, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 'count', 3.00, NULL),
(2308, 1756198080, 1440, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 'count', 5.00, NULL),
(2309, 1756198080, 10080, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 'count', 5.00, NULL),
(2310, 1756198080, 60, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 'max', 1756198123.00, NULL),
(2311, 1756198080, 360, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 'max', 1756198266.00, NULL),
(2312, 1756198080, 1440, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 'max', 1756198499.00, NULL),
(2313, 1756198080, 10080, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 'max', 1756198499.00, NULL),
(2314, 1756198140, 60, 'user_request', '3', 'count', 1.00, NULL),
(2315, 1756198140, 60, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 'count', 1.00, NULL),
(2322, 1756198140, 60, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 'max', 1756198163.00, NULL),
(2326, 1756198260, 60, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'count', 1.00, NULL),
(2327, 1756198080, 360, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'count', 2.00, NULL),
(2328, 1756198080, 1440, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'count', 2.00, NULL),
(2329, 1756198080, 10080, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'count', 4.00, NULL),
(2330, 1756198260, 60, 'slow_user_request', '3', 'count', 1.00, NULL),
(2331, 1756198080, 360, 'slow_user_request', '3', 'count', 2.00, NULL),
(2332, 1756198080, 1440, 'slow_user_request', '3', 'count', 2.00, NULL),
(2333, 1756198080, 10080, 'slow_user_request', '3', 'count', 4.00, NULL),
(2334, 1756198260, 60, 'user_request', '3', 'count', 1.00, NULL),
(2335, 1756198260, 60, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 'count', 1.00, NULL),
(2342, 1756198260, 60, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'max', 1161.00, NULL),
(2343, 1756198080, 360, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'max', 1161.00, NULL),
(2344, 1756198080, 1440, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'max', 1161.00, NULL),
(2345, 1756198080, 10080, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'max', 2960.00, NULL),
(2346, 1756198260, 60, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 'max', 1756198266.00, NULL),
(2350, 1756198320, 60, 'user_request', '3', 'count', 1.00, NULL),
(2351, 1756198320, 60, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(2352, 1756198080, 360, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 2.00, NULL),
(2353, 1756198080, 1440, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 2.00, NULL),
(2354, 1756198080, 10080, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 2.00, NULL),
(2358, 1756198320, 60, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756198339.00, NULL),
(2359, 1756198080, 360, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756198424.00, NULL),
(2360, 1756198080, 1440, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756198424.00, NULL),
(2361, 1756198080, 10080, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756198424.00, NULL),
(2362, 1756198380, 60, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'count', 1.00, NULL),
(2363, 1756198380, 60, 'slow_user_request', '3', 'count', 1.00, NULL),
(2364, 1756198380, 60, 'user_request', '3', 'count', 1.00, NULL),
(2365, 1756198380, 60, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(2378, 1756198380, 60, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'max', 1073.00, NULL),
(2379, 1756198380, 60, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756198424.00, NULL),
(2386, 1756198440, 60, 'user_request', '3', 'count', 2.00, NULL),
(2387, 1756198440, 360, 'user_request', '3', 'count', 8.00, NULL),
(2388, 1756198440, 60, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 'count', 2.00, NULL),
(2389, 1756198440, 360, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 'count', 2.00, NULL),
(2394, 1756198440, 60, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 'max', 1756198499.00, NULL),
(2395, 1756198440, 360, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 'max', 1756198499.00, NULL),
(2410, 1756198560, 60, 'user_request', '3', 'count', 2.00, NULL),
(2414, 1756198560, 60, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:47\"]', 'count', 1.00, NULL),
(2415, 1756198440, 360, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:47\"]', 'count', 2.00, NULL),
(2416, 1756198080, 1440, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:47\"]', 'count', 2.00, NULL),
(2417, 1756198080, 10080, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:47\"]', 'count', 2.00, NULL),
(2422, 1756198560, 60, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:47\"]', 'max', 1756198582.00, NULL),
(2423, 1756198440, 360, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:47\"]', 'max', 1756198631.00, NULL),
(2424, 1756198080, 1440, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:47\"]', 'max', 1756198631.00, NULL),
(2425, 1756198080, 10080, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:47\"]', 'max', 1756198631.00, NULL),
(2426, 1756198620, 60, 'user_request', '3', 'count', 4.00, NULL),
(2427, 1756198620, 60, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:47\"]', 'count', 1.00, NULL),
(2434, 1756198620, 60, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:47\"]', 'max', 1756198631.00, NULL),
(2450, 1756199040, 60, 'user_request', '3', 'count', 3.00, NULL),
(2451, 1756198800, 360, 'user_request', '3', 'count', 4.00, NULL),
(2462, 1756199100, 60, 'user_request', '3', 'count', 1.00, NULL),
(2466, 1756199340, 60, 'user_request', '3', 'count', 5.00, NULL),
(2467, 1756199160, 360, 'user_request', '3', 'count', 9.00, NULL),
(2486, 1756199400, 60, 'user_request', '3', 'count', 2.00, NULL),
(2494, 1756199460, 60, 'user_request', '3', 'count', 2.00, NULL),
(2502, 1756199520, 60, 'user_request', '3', 'count', 6.00, NULL),
(2503, 1756199520, 360, 'user_request', '3', 'count', 12.00, NULL),
(2504, 1756199520, 1440, 'user_request', '3', 'count', 35.00, NULL),
(2526, 1756199580, 60, 'user_request', '3', 'count', 2.00, NULL),
(2534, 1756199760, 60, 'user_request', '3', 'count', 2.00, NULL),
(2542, 1756199820, 60, 'user_request', '3', 'count', 2.00, NULL),
(2550, 1756199940, 60, 'user_request', '3', 'count', 2.00, NULL),
(2551, 1756199880, 360, 'user_request', '3', 'count', 12.00, NULL),
(2558, 1756200000, 60, 'user_request', '3', 'count', 5.00, NULL),
(2578, 1756200060, 60, 'user_request', '3', 'count', 3.00, NULL),
(2590, 1756200180, 60, 'user_request', '3', 'count', 2.00, NULL),
(2598, 1756200360, 60, 'user_request', '3', 'count', 2.00, NULL),
(2599, 1756200240, 360, 'user_request', '3', 'count', 4.00, NULL),
(2606, 1756200420, 60, 'user_request', '3', 'count', 2.00, NULL),
(2614, 1756200600, 60, 'user_request', '3', 'count', 6.00, NULL),
(2615, 1756200600, 360, 'user_request', '3', 'count', 7.00, NULL),
(2638, 1756200720, 60, 'user_request', '3', 'count', 1.00, NULL),
(2642, 1756200960, 60, 'user_request', '3', 'count', 3.00, NULL),
(2643, 1756200960, 360, 'user_request', '3', 'count', 3.00, NULL),
(2644, 1756200960, 1440, 'user_request', '3', 'count', 9.00, NULL),
(2654, 1756202160, 60, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'count', 1.00, NULL),
(2655, 1756202040, 360, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'count', 1.00, NULL),
(2656, 1756200960, 1440, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'count', 1.00, NULL),
(2657, 1756202160, 60, 'slow_user_request', '3', 'count', 1.00, NULL),
(2658, 1756202040, 360, 'slow_user_request', '3', 'count', 1.00, NULL),
(2659, 1756200960, 1440, 'slow_user_request', '3', 'count', 1.00, NULL),
(2660, 1756202160, 60, 'user_request', '3', 'count', 6.00, NULL),
(2661, 1756202040, 360, 'user_request', '3', 'count', 6.00, NULL),
(2666, 1756202160, 60, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'max', 2784.00, NULL),
(2667, 1756202040, 360, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'max', 2784.00, NULL),
(2668, 1756200960, 1440, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'max', 2784.00, NULL),
(2690, 1756202460, 60, 'user_request', '3', 'count', 1.00, NULL),
(2691, 1756202400, 360, 'user_request', '3', 'count', 1.00, NULL),
(2692, 1756202400, 1440, 'user_request', '3', 'count', 23.00, NULL),
(2694, 1756202940, 60, 'user_request', '3', 'count', 6.00, NULL),
(2695, 1756202760, 360, 'user_request', '3', 'count', 6.00, NULL),
(2718, 1756203180, 60, 'user_request', '3', 'count', 1.00, NULL),
(2719, 1756203120, 360, 'user_request', '3', 'count', 7.00, NULL),
(2722, 1756203300, 60, 'user_request', '3', 'count', 4.00, NULL),
(2738, 1756203420, 60, 'user_request', '3', 'count', 2.00, NULL),
(2742, 1756203420, 60, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'count', 1.00, NULL),
(2743, 1756203120, 360, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'count', 1.00, NULL),
(2744, 1756202400, 1440, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'count', 1.00, NULL),
(2745, 1756203420, 60, 'slow_user_request', '3', 'count', 1.00, NULL),
(2746, 1756203120, 360, 'slow_user_request', '3', 'count', 1.00, NULL),
(2747, 1756202400, 1440, 'slow_user_request', '3', 'count', 1.00, NULL),
(2748, 1756203420, 60, 'exception', '[\"ParseError\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(2749, 1756203120, 360, 'exception', '[\"ParseError\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(2750, 1756202400, 1440, 'exception', '[\"ParseError\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(2751, 1756198080, 10080, 'exception', '[\"ParseError\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'count', 1.00, NULL),
(2758, 1756203420, 60, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'max', 2960.00, NULL),
(2759, 1756203120, 360, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'max', 2960.00, NULL),
(2760, 1756202400, 1440, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 'max', 2960.00, NULL),
(2761, 1756203420, 60, 'exception', '[\"ParseError\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756203476.00, NULL),
(2762, 1756203120, 360, 'exception', '[\"ParseError\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756203476.00, NULL),
(2763, 1756202400, 1440, 'exception', '[\"ParseError\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756203476.00, NULL),
(2764, 1756198080, 10080, 'exception', '[\"ParseError\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 'max', 1756203476.00, NULL),
(2766, 1756203480, 60, 'user_request', '3', 'count', 5.00, NULL),
(2767, 1756203480, 360, 'user_request', '3', 'count', 9.00, NULL),
(2786, 1756203540, 60, 'user_request', '3', 'count', 2.00, NULL),
(2794, 1756203660, 60, 'user_request', '3', 'count', 2.00, NULL),
(2802, 1756204560, 60, 'user_request', '3', 'count', 4.00, NULL),
(2803, 1756204560, 360, 'user_request', '3', 'count', 7.00, NULL),
(2804, 1756203840, 1440, 'user_request', '3', 'count', 7.00, NULL),
(2818, 1756204620, 60, 'user_request', '3', 'count', 3.00, NULL),
(2830, 1756205520, 60, 'user_request', '3', 'count', 5.00, NULL),
(2831, 1756205280, 360, 'user_request', '3', 'count', 7.00, NULL),
(2832, 1756205280, 1440, 'user_request', '3', 'count', 17.00, NULL),
(2850, 1756205580, 60, 'user_request', '3', 'count', 2.00, NULL),
(2858, 1756205940, 60, 'user_request', '3', 'count', 3.00, NULL),
(2859, 1756205640, 360, 'user_request', '3', 'count', 3.00, NULL),
(2870, 1756206000, 60, 'user_request', '3', 'count', 4.00, NULL),
(2871, 1756206000, 360, 'user_request', '3', 'count', 4.00, NULL),
(2886, 1756206060, 60, 'user_request', '6', 'count', 4.00, NULL),
(2887, 1756206000, 360, 'user_request', '6', 'count', 13.00, NULL),
(2888, 1756205280, 1440, 'user_request', '6', 'count', 14.00, NULL),
(2889, 1756198080, 10080, 'user_request', '6', 'count', 14.00, NULL),
(2902, 1756206120, 60, 'user_request', '6', 'count', 2.00, NULL),
(2910, 1756206180, 60, 'user_request', '6', 'count', 2.00, NULL),
(2918, 1756206240, 60, 'user_request', '6', 'count', 2.00, NULL),
(2926, 1756206300, 60, 'user_request', '6', 'count', 3.00, NULL),
(2938, 1756206360, 60, 'user_request', '6', 'count', 1.00, NULL),
(2939, 1756206360, 360, 'user_request', '6', 'count', 1.00, NULL),
(2942, 1756206360, 60, 'user_request', '1', 'count', 9.00, NULL),
(2943, 1756206360, 360, 'user_request', '1', 'count', 10.00, NULL),
(2944, 1756205280, 1440, 'user_request', '1', 'count', 10.00, NULL),
(2945, 1756198080, 10080, 'user_request', '1', 'count', 10.00, NULL),
(2978, 1756206420, 60, 'user_request', '1', 'count', 1.00, NULL),
(2982, 1756206420, 60, 'user_request', '3', 'count', 3.00, NULL),
(2983, 1756206360, 360, 'user_request', '3', 'count', 3.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pulse_entries`
--

CREATE TABLE `pulse_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `timestamp` int(10) UNSIGNED NOT NULL,
  `type` varchar(191) NOT NULL,
  `key` mediumtext NOT NULL,
  `key_hash` binary(16) GENERATED ALWAYS AS (unhex(md5(`key`))) VIRTUAL,
  `value` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pulse_entries`
--

INSERT INTO `pulse_entries` (`id`, `timestamp`, `type`, `key`, `value`) VALUES
(1, 1755689538, 'user_request', '1', NULL),
(2, 1755689539, 'user_request', '1', NULL),
(3, 1755689548, 'user_request', '1', NULL),
(4, 1755689584, 'user_request', '1', NULL),
(5, 1755689584, 'user_request', '1', NULL),
(6, 1755689587, 'user_request', '1', NULL),
(7, 1755689596, 'user_request', '1', NULL),
(8, 1755689600, 'user_request', '1', NULL),
(9, 1755689601, 'user_request', '1', NULL),
(10, 1755689609, 'user_request', '1', NULL),
(11, 1755689744, 'user_request', '1', NULL),
(12, 1755689774, 'user_request', '1', NULL),
(13, 1755689775, 'user_request', '1', NULL),
(14, 1755689851, 'user_request', '1', NULL),
(15, 1755689851, 'user_request', '1', NULL),
(16, 1755689884, 'user_request', '1', NULL),
(17, 1755689884, 'user_request', '1', NULL),
(18, 1755690118, 'user_request', '1', NULL),
(19, 1755690119, 'user_request', '1', NULL),
(20, 1755690144, 'user_request', '1', NULL),
(21, 1755690217, 'user_request', '2', NULL),
(22, 1755690217, 'user_request', '2', NULL),
(23, 1755690239, 'user_request', '1', NULL),
(24, 1755690239, 'user_request', '1', NULL),
(25, 1755690243, 'user_request', '1', NULL),
(26, 1755690395, 'user_request', '1', NULL),
(27, 1755690395, 'user_request', '1', NULL),
(28, 1755690430, 'user_request', '1', NULL),
(29, 1755690430, 'user_request', '1', NULL),
(30, 1755691303, 'user_request', '1', NULL),
(31, 1755691311, 'user_request', '1', NULL),
(32, 1755691422, 'user_request', '1', NULL),
(33, 1755691422, 'user_request', '1', NULL),
(34, 1755691427, 'user_request', '1', NULL),
(35, 1755691504, 'user_request', '1', NULL),
(36, 1755691504, 'user_request', '1', NULL),
(37, 1755691536, 'user_request', '1', NULL),
(38, 1755691536, 'user_request', '1', NULL),
(39, 1755691767, 'user_request', '1', NULL),
(40, 1755691767, 'user_request', '1', NULL),
(41, 1755691827, 'user_request', '1', NULL),
(42, 1755691827, 'user_request', '1', NULL),
(43, 1755691856, 'user_request', '1', NULL),
(44, 1755691888, 'user_request', '1', NULL),
(45, 1755691889, 'user_request', '1', NULL),
(46, 1755691961, 'user_request', '1', NULL),
(47, 1755691961, 'user_request', '1', NULL),
(48, 1755692018, 'user_request', '1', NULL),
(49, 1755692018, 'user_request', '1', NULL),
(50, 1755692038, 'user_request', '1', NULL),
(51, 1755692038, 'user_request', '1', NULL),
(52, 1755692042, 'user_request', '1', NULL),
(53, 1755692100, 'user_request', '1', NULL),
(54, 1755692100, 'user_request', '1', NULL),
(55, 1755692134, 'user_request', '1', NULL),
(56, 1755692135, 'user_request', '1', NULL),
(57, 1755692144, 'user_request', '1', NULL),
(58, 1755692361, 'user_request', '1', NULL),
(59, 1755692362, 'user_request', '1', NULL),
(254, 1755764305, 'slow_request', '[\"GET\",\"\\/admin\\/dashboard\",\"Closure\"]', 2580),
(255, 1755764305, 'slow_user_request', '3', NULL),
(256, 1755764305, 'user_request', '3', NULL),
(257, 1755764359, 'user_request', '3', NULL),
(258, 1755764362, 'user_request', '3', NULL),
(259, 1755764365, 'user_request', '3', NULL),
(260, 1755764380, 'user_request', '3', NULL),
(261, 1755764775, 'exception', '[\"Symfony\\\\Component\\\\Console\\\\Exception\\\\NamespaceNotFoundException\",\"vendor\\\\symfony\\\\console\\\\Application.php:659\"]', 1755764775),
(262, 1755764775, 'exception', '[\"Symfony\\\\Component\\\\Console\\\\Exception\\\\NamespaceNotFoundException\",\"vendor\\\\symfony\\\\console\\\\Application.php:659\"]', 1755764775),
(263, 1755765331, 'user_request', '3', NULL),
(264, 1755765437, 'user_request', '3', NULL),
(265, 1755765462, 'user_request', '3', NULL),
(266, 1755765545, 'user_request', '3', NULL),
(267, 1755765545, 'user_request', '3', NULL),
(268, 1755765638, 'user_request', '3', NULL),
(269, 1755765641, 'user_request', '3', NULL),
(270, 1755765653, 'user_request', '3', NULL),
(271, 1755765837, 'user_request', '3', NULL),
(272, 1755765841, 'slow_request', '[\"GET\",\"\\/restaurant\\/menu\\/categories\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\MenuCategoryController@index\"]', 1010),
(273, 1755765841, 'slow_user_request', '3', NULL),
(274, 1755765841, 'user_request', '3', NULL),
(275, 1755765841, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\MenuCategoryController.php:42\"]', 1755765841),
(276, 1755765857, 'user_request', '3', NULL),
(277, 1755765864, 'user_request', '3', NULL),
(278, 1755765874, 'user_request', '3', NULL),
(279, 1755765879, 'user_request', '3', NULL),
(280, 1755765885, 'user_request', '1', NULL),
(281, 1755765886, 'user_request', '1', NULL),
(282, 1755765893, 'user_request', '1', NULL),
(283, 1755766186, 'user_request', '3', NULL),
(284, 1755766186, 'user_request', '3', NULL),
(285, 1755766370, 'user_request', '3', NULL),
(286, 1755766375, 'user_request', '3', NULL),
(287, 1755766398, 'user_request', '2', NULL),
(288, 1755766399, 'slow_request', '[\"GET\",\"\\/restaurant\\/dashboard\",\"Closure\"]', 1116),
(289, 1755766399, 'slow_user_request', '2', NULL),
(290, 1755766399, 'user_request', '2', NULL),
(291, 1755766399, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 1755766399),
(292, 1755766539, 'slow_request', '[\"GET\",\"\\/restaurant\\/dashboard\",\"Closure\"]', 1285),
(293, 1755766539, 'slow_user_request', '2', NULL),
(294, 1755766539, 'user_request', '2', NULL),
(295, 1755766540, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 1755766540),
(296, 1755766545, 'user_request', '2', NULL),
(297, 1755766545, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 1755766545),
(298, 1755766551, 'user_request', '2', NULL),
(299, 1755766551, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 1755766551),
(300, 1755766658, 'user_request', '1', NULL),
(301, 1755766658, 'user_request', '1', NULL),
(302, 1755766662, 'user_request', '1', NULL),
(303, 1755766666, 'user_request', '1', NULL),
(304, 1755766673, 'user_request', '3', NULL),
(305, 1755766673, 'user_request', '3', NULL),
(306, 1755766676, 'user_request', '3', NULL),
(307, 1755766698, 'user_request', '3', NULL),
(308, 1755766698, 'slow_request', '[\"GET\",\"\\/restaurant\\/dashboard\",\"Closure\"]', 1097),
(309, 1755766698, 'slow_user_request', '3', NULL),
(310, 1755766698, 'user_request', '3', NULL),
(311, 1755766699, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 1755766699),
(312, 1755766709, 'user_request', '3', NULL),
(313, 1755766720, 'user_request', '3', NULL),
(314, 1755766728, 'user_request', '3', NULL),
(315, 1755766728, 'user_request', '3', NULL),
(316, 1755766741, 'slow_request', '[\"GET\",\"\\/restaurant\\/dashboard\",\"Closure\"]', 1255),
(317, 1755766741, 'slow_user_request', '3', NULL),
(318, 1755766741, 'user_request', '3', NULL),
(319, 1755766741, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 1755766741),
(320, 1755766757, 'user_request', '3', NULL),
(321, 1755766770, 'user_request', '3', NULL),
(322, 1755766776, 'user_request', '3', NULL),
(323, 1755766776, 'user_request', '3', NULL),
(324, 1755766860, 'user_request', '3', NULL),
(325, 1755766864, 'user_request', '3', NULL),
(326, 1755766866, 'user_request', '3', NULL),
(327, 1755766870, 'user_request', '3', NULL),
(328, 1755766872, 'user_request', '3', NULL),
(329, 1755766875, 'user_request', '3', NULL),
(330, 1755766875, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\MenuCategoryController.php:42\"]', 1755766875),
(331, 1755766884, 'user_request', '3', NULL),
(332, 1755766887, 'user_request', '3', NULL),
(333, 1755766905, 'user_request', '3', NULL),
(334, 1755766905, 'user_request', '3', NULL),
(335, 1755766908, 'user_request', '3', NULL),
(336, 1755766908, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\MenuCategoryController.php:42\"]', 1755766908),
(337, 1755767044, 'user_request', '3', NULL),
(338, 1755767044, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\MenuCategoryController.php:42\"]', 1755767044),
(339, 1755767373, 'user_request', '3', NULL),
(340, 1755767375, 'user_request', '3', NULL),
(341, 1755767393, 'user_request', '3', NULL),
(342, 1755767396, 'user_request', '3', NULL),
(343, 1755768242, 'user_request', '3', NULL),
(344, 1755768317, 'user_request', '3', NULL),
(345, 1755768440, 'user_request', '3', NULL),
(346, 1755768447, 'user_request', '1', NULL),
(347, 1755768447, 'user_request', '1', NULL),
(348, 1755768457, 'user_request', '1', NULL),
(349, 1755768618, 'user_request', '3', NULL),
(350, 1755768619, 'user_request', '3', NULL),
(351, 1755770081, 'user_request', '3', NULL),
(352, 1755770085, 'user_request', '3', NULL),
(353, 1755770088, 'user_request', '3', NULL),
(354, 1755770092, 'user_request', '3', NULL),
(355, 1755770094, 'user_request', '3', NULL),
(356, 1755770096, 'user_request', '3', NULL),
(357, 1755770098, 'user_request', '3', NULL),
(358, 1755770101, 'user_request', '3', NULL),
(359, 1755770271, 'user_request', '3', NULL),
(360, 1755770276, 'user_request', '3', NULL),
(361, 1755770276, 'exception', '[\"Error\",\"app\\\\Http\\\\Controllers\\\\Admin\\\\RestaurantManagementController.php:229\"]', 1755770276),
(362, 1755770285, 'user_request', '3', NULL),
(363, 1755770286, 'user_request', '3', NULL),
(364, 1755770698, 'exception', '[\"Illuminate\\\\Contracts\\\\Container\\\\BindingResolutionException\",\"vendor\\\\laravel\\\\framework\\\\src\\\\Illuminate\\\\Container\\\\Container.php:1019\"]', 1755770698),
(365, 1755770698, 'exception', '[\"Illuminate\\\\Contracts\\\\Container\\\\BindingResolutionException\",\"vendor\\\\laravel\\\\framework\\\\src\\\\Illuminate\\\\Container\\\\Container.php:1019\"]', 1755770698),
(366, 1755775534, 'slow_request', '[\"GET\",\"\\/restaurant\\/menu\\/list\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\MenuItemController@index\"]', 1116),
(367, 1755775534, 'slow_user_request', '3', NULL),
(368, 1755775534, 'user_request', '3', NULL),
(369, 1755775534, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 1755775534),
(370, 1755775549, 'user_request', '3', NULL),
(371, 1755775549, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 1755775549),
(372, 1755775560, 'user_request', '3', NULL),
(373, 1755775560, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 1755775560),
(374, 1755775574, 'user_request', '3', NULL),
(375, 1755775574, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 1755775574),
(376, 1755775592, 'user_request', '3', NULL),
(377, 1755775604, 'user_request', '3', NULL),
(378, 1755776759, 'user_request', '3', NULL),
(379, 1755776766, 'user_request', '3', NULL),
(380, 1755776766, 'user_request', '3', NULL),
(381, 1755778000, 'user_request', '3', NULL),
(382, 1755778031, 'user_request', '6', NULL),
(383, 1755778031, 'user_request', '6', NULL),
(384, 1755778039, 'user_request', '6', NULL),
(385, 1755778044, 'user_request', '6', NULL),
(386, 1755778048, 'user_request', '6', NULL),
(387, 1755780602, 'user_request', '6', NULL),
(388, 1755839323, 'exception', '[\"Error\",\"app\\\\Models\\\\User.php:13\"]', 1755839323),
(389, 1755839331, 'exception', '[\"Error\",\"app\\\\Models\\\\User.php:13\"]', 1755839331),
(390, 1755839464, 'slow_request', '[\"GET\",\"\\/\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\RegisterController@showRegistrationForm\"]', 1240),
(391, 1755839464, 'exception', '[\"Error\",\"app\\\\Models\\\\User.php:13\"]', 1755839464),
(392, 1755840799, 'user_request', '1', NULL),
(393, 1755840800, 'user_request', '1', NULL),
(394, 1755840803, 'user_request', '1', NULL),
(395, 1755844842, 'exception', '[\"InvalidArgumentException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\NearestRestaurantController.php:59\"]', 1755844842),
(396, 1755847261, 'exception', '[\"Error\",\"app\\\\Http\\\\Middleware\\\\CorsMiddleware.php:27\"]', 1755847261),
(397, 1755847954, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\NearestRestaurantController.php:22\"]', 1755847954),
(398, 1755847962, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\NearestRestaurantController.php:22\"]', 1755847962),
(399, 1755848277, 'exception', '[\"BadMethodCallException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\NearestRestaurantController.php:68\"]', 1755848277),
(400, 1755848516, 'exception', '[\"BadMethodCallException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\NearestRestaurantController.php:49\"]', 1755848516),
(401, 1755849022, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\NearestRestaurantController.php:68\"]', 1755849022),
(402, 1755849033, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\NearestRestaurantController.php:68\"]', 1755849033),
(403, 1755849939, 'slow_request', '[\"GET\",\"\\/api\\/v1\\/restaurant\\/{id}\\/menu\",\"App\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\NearestRestaurantController@menuWithCategories\"]', 5035),
(404, 1755849941, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\NearestRestaurantController.php:23\"]', 1755849941),
(405, 1755852296, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"database\\\\seeders\\\\ReviewSeeder.php:110\"]', 1755852296),
(406, 1755852296, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"database\\\\seeders\\\\ReviewSeeder.php:110\"]', 1755852296),
(407, 1755852674, 'slow_request', '[\"POST\",\"\\/api\\/v1\\/auth\\/login\",\"App\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\AuthController@login\"]', 1254),
(408, 1755852674, 'slow_user_request', '6', NULL),
(409, 1755852674, 'user_request', '6', NULL),
(410, 1755853437, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"database\\\\seeders\\\\DemoFullSeeder.php:16\"]', 1755853437),
(411, 1755853437, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"database\\\\seeders\\\\DemoFullSeeder.php:16\"]', 1755853437),
(412, 1755853499, 'exception', '[\"Illuminate\\\\Database\\\\UniqueConstraintViolationException\",\"database\\\\seeders\\\\DemoFullSeeder.php:16\"]', 1755853499),
(413, 1755853499, 'exception', '[\"Illuminate\\\\Database\\\\UniqueConstraintViolationException\",\"database\\\\seeders\\\\DemoFullSeeder.php:16\"]', 1755853499),
(414, 1755853560, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"database\\\\seeders\\\\DemoFullSeeder.php:42\"]', 1755853560),
(415, 1755853560, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"database\\\\seeders\\\\DemoFullSeeder.php:42\"]', 1755853560),
(416, 1755853827, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"database\\\\seeders\\\\DemoFullSeeder.php:62\"]', 1755853827),
(417, 1755853827, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"database\\\\seeders\\\\DemoFullSeeder.php:62\"]', 1755853827),
(418, 1756102172, 'slow_request', '[\"POST\",\"\\/api\\/v1\\/auth\\/login\",\"App\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\AuthController@login\"]', 1248),
(419, 1756102172, 'slow_user_request', '6', NULL),
(420, 1756102172, 'user_request', '6', NULL),
(421, 1756102936, 'exception', '[\"Illuminate\\\\Contracts\\\\Container\\\\BindingResolutionException\",\"app\\\\Http\\\\Middleware\\\\CorsMiddleware.php:27\"]', 1756102936),
(422, 1756103060, 'slow_request', '[\"POST\",\"\\/api\\/payment\\/intent\\/{orderId}\",\"App\\\\Http\\\\Controllers\\\\API\\\\v1\\\\PaymentController@createIntent\"]', 1022),
(423, 1756103060, 'exception', '[\"Illuminate\\\\Contracts\\\\Container\\\\BindingResolutionException\",\"app\\\\Http\\\\Middleware\\\\CorsMiddleware.php:27\"]', 1756103060),
(424, 1756103122, 'exception', '[\"Error\",\"app\\\\Services\\\\StripeService.php:15\"]', 1756103122),
(425, 1756103438, 'slow_request', '[\"POST\",\"\\/api\\/payment\\/intent\\/{orderId}\",\"App\\\\Http\\\\Controllers\\\\API\\\\v1\\\\PaymentController@createIntent\"]', 1807),
(426, 1756103439, 'exception', '[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Services\\\\StripeService.php:30\"]', 1756103439),
(427, 1756104146, 'exception', '[\"ErrorException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\PaymentController.php:62\"]', 1756104146),
(428, 1756104413, 'slow_request', '[\"POST\",\"\\/api\\/payment\\/intent\\/{orderId}\",\"App\\\\Http\\\\Controllers\\\\API\\\\v1\\\\PaymentController@createIntent\"]', 1271),
(429, 1756104869, 'slow_request', '[\"GET\",\"\\/api\\/payment\\/history\",\"App\\\\Http\\\\Controllers\\\\API\\\\v1\\\\PaymentController@history\"]', 5204),
(430, 1756104871, 'exception', '[\"ErrorException\",\"app\\\\Http\\\\Controllers\\\\Api\\\\v1\\\\PaymentController.php:62\"]', 1756104871),
(431, 1756104989, 'user_request', '6', NULL),
(432, 1756104990, 'cache_miss', 'Jg6cRJEE0V1FWjWU', NULL),
(433, 1756104990, 'cache_miss', 'Jg6cRJEE0V1FWjWU', NULL),
(434, 1756105274, 'user_request', '6', NULL),
(435, 1756105274, 'cache_miss', 'Jg6cRJEE0V1FWjWU', NULL),
(436, 1756105274, 'cache_miss', 'Jg6cRJEE0V1FWjWU', NULL),
(437, 1756105321, 'user_request', '6', NULL),
(438, 1756105321, 'cache_miss', 'Jg6cRJEE0V1FWjWU', NULL),
(439, 1756105321, 'cache_miss', 'Jg6cRJEE0V1FWjWU', NULL),
(440, 1756105346, 'user_request', '6', NULL),
(441, 1756105385, 'user_request', '6', NULL),
(442, 1756105385, 'cache_miss', '2sNW8vCdCCO0mUNo', NULL),
(443, 1756105385, 'cache_miss', '2sNW8vCdCCO0mUNo', NULL),
(444, 1756105410, 'user_request', '6', NULL),
(445, 1756105411, 'cache_miss', '2sNW8vCdCCO0mUNo', NULL),
(446, 1756105411, 'cache_miss', '2sNW8vCdCCO0mUNo', NULL),
(447, 1756107978, 'user_request', '6', NULL),
(448, 1756108006, 'user_request', '6', NULL),
(449, 1756108007, 'cache_miss', 'VSdb44prdid8I63N', NULL),
(450, 1756108007, 'cache_miss', 'VSdb44prdid8I63N', NULL),
(451, 1756108231, 'user_request', '6', NULL),
(452, 1756108231, 'cache_miss', 'VSdb44prdid8I63N', NULL),
(453, 1756108231, 'cache_miss', 'VSdb44prdid8I63N', NULL),
(454, 1756108265, 'user_request', '6', NULL),
(455, 1756108265, 'cache_miss', 'VSdb44prdid8I63N', NULL),
(456, 1756108265, 'cache_miss', 'VSdb44prdid8I63N', NULL),
(457, 1756108360, 'slow_request', '[\"POST\",\"\\/api\\/v1\\/payment\\/intent\\/{orderId}\",\"App\\\\Http\\\\Controllers\\\\API\\\\v1\\\\PaymentController@createIntent\"]', 1170),
(458, 1756108360, 'slow_user_request', '6', NULL),
(459, 1756108360, 'user_request', '6', NULL),
(460, 1756108360, 'cache_miss', 'VSdb44prdid8I63N', NULL),
(461, 1756108360, 'cache_miss', 'VSdb44prdid8I63N', NULL),
(462, 1756108403, 'user_request', '6', NULL),
(463, 1756108403, 'cache_miss', 'VSdb44prdid8I63N', NULL),
(464, 1756108403, 'cache_miss', 'VSdb44prdid8I63N', NULL),
(465, 1756108438, 'user_request', '6', NULL),
(466, 1756108439, 'cache_miss', 'VSdb44prdid8I63N', NULL),
(467, 1756108439, 'cache_miss', 'VSdb44prdid8I63N', NULL),
(468, 1756108614, 'user_request', '3', NULL),
(469, 1756108805, 'user_request', '3', NULL),
(470, 1756108805, 'cache_miss', 'DRA5tb3VIYvx30SC', NULL),
(471, 1756108805, 'cache_miss', 'DRA5tb3VIYvx30SC', NULL),
(472, 1756109167, 'user_request', '3', NULL),
(473, 1756109167, 'cache_miss', 'DRA5tb3VIYvx30SC', NULL),
(474, 1756109167, 'cache_miss', 'DRA5tb3VIYvx30SC', NULL),
(475, 1756109179, 'user_request', '6', NULL),
(476, 1756109182, 'user_request', '3', NULL),
(477, 1756109182, 'cache_miss', 'DRA5tb3VIYvx30SC', NULL),
(478, 1756109182, 'cache_miss', 'DRA5tb3VIYvx30SC', NULL),
(479, 1756109221, 'user_request', '6', NULL),
(480, 1756109221, 'cache_miss', 'dADBKCsBKGy1x0Ci', NULL),
(481, 1756109221, 'cache_miss', 'dADBKCsBKGy1x0Ci', NULL),
(482, 1756109303, 'user_request', '6', NULL),
(483, 1756109304, 'cache_miss', 'dADBKCsBKGy1x0Ci', NULL),
(484, 1756109304, 'cache_miss', 'dADBKCsBKGy1x0Ci', NULL),
(485, 1756109334, 'user_request', '6', NULL),
(486, 1756109334, 'cache_miss', 'dADBKCsBKGy1x0Ci', NULL),
(487, 1756109334, 'cache_miss', 'dADBKCsBKGy1x0Ci', NULL),
(488, 1756109963, 'user_request', '6', NULL),
(489, 1756109963, 'cache_miss', 'dADBKCsBKGy1x0Ci', NULL),
(490, 1756109963, 'cache_miss', 'dADBKCsBKGy1x0Ci', NULL),
(491, 1756109963, 'exception', '[\"Stripe\\\\Exception\\\\InvalidArgumentException\",\"app\\\\Services\\\\StripeService.php:15\"]', 1756109963),
(492, 1756109971, 'user_request', '6', NULL),
(493, 1756109971, 'cache_miss', 'dADBKCsBKGy1x0Ci', NULL),
(494, 1756109971, 'cache_miss', 'dADBKCsBKGy1x0Ci', NULL),
(495, 1756110466, 'user_request', '6', NULL),
(496, 1756110466, 'cache_miss', 'dADBKCsBKGy1x0Ci', NULL),
(497, 1756110466, 'cache_miss', 'dADBKCsBKGy1x0Ci', NULL),
(498, 1756111629, 'user_request', '6', NULL),
(499, 1756111629, 'cache_miss', 'dADBKCsBKGy1x0Ci', NULL),
(500, 1756111629, 'cache_miss', 'dADBKCsBKGy1x0Ci', NULL),
(501, 1756111650, 'user_request', '6', NULL),
(502, 1756111650, 'cache_miss', 'dADBKCsBKGy1x0Ci', NULL),
(503, 1756111650, 'cache_miss', 'dADBKCsBKGy1x0Ci', NULL),
(504, 1756111867, 'user_request', '6', NULL),
(505, 1756111867, 'cache_miss', 'dADBKCsBKGy1x0Ci', NULL),
(506, 1756111867, 'cache_miss', 'dADBKCsBKGy1x0Ci', NULL),
(507, 1756111949, 'user_request', '6', NULL),
(508, 1756111985, 'user_request', '6', NULL),
(509, 1756111985, 'cache_miss', 'fjMClhlXHC7CtGsf', NULL),
(510, 1756111985, 'cache_miss', 'fjMClhlXHC7CtGsf', NULL),
(511, 1756111997, 'user_request', '6', NULL),
(512, 1756111998, 'cache_miss', 'fjMClhlXHC7CtGsf', NULL),
(513, 1756111998, 'cache_miss', 'fjMClhlXHC7CtGsf', NULL),
(514, 1756112131, 'user_request', '6', NULL),
(515, 1756112131, 'cache_miss', 'fjMClhlXHC7CtGsf', NULL),
(516, 1756112131, 'cache_miss', 'fjMClhlXHC7CtGsf', NULL),
(517, 1756112151, 'user_request', '6', NULL),
(518, 1756112165, 'user_request', '6', NULL),
(519, 1756112165, 'cache_miss', 'dADBKCsBKGy1x0Ci', NULL),
(520, 1756112165, 'cache_miss', 'dADBKCsBKGy1x0Ci', NULL),
(521, 1756112194, 'user_request', '6', NULL),
(522, 1756112194, 'cache_miss', '5NpQ2Jz7QPZYR1W6', NULL),
(523, 1756112194, 'cache_miss', '5NpQ2Jz7QPZYR1W6', NULL),
(524, 1756112208, 'user_request', '6', NULL),
(525, 1756112208, 'cache_miss', 'dADBKCsBKGy1x0Ci', NULL),
(526, 1756112208, 'cache_miss', 'dADBKCsBKGy1x0Ci', NULL),
(527, 1756112215, 'user_request', '6', NULL),
(528, 1756112215, 'cache_miss', 'dADBKCsBKGy1x0Ci', NULL),
(529, 1756112215, 'cache_miss', 'dADBKCsBKGy1x0Ci', NULL),
(530, 1756112589, 'user_request', '6', NULL),
(531, 1756112589, 'cache_miss', '5NpQ2Jz7QPZYR1W6', NULL),
(532, 1756112589, 'cache_miss', '5NpQ2Jz7QPZYR1W6', NULL),
(533, 1756112600, 'user_request', '6', NULL),
(534, 1756112600, 'cache_miss', 'dADBKCsBKGy1x0Ci', NULL),
(535, 1756112600, 'cache_miss', 'dADBKCsBKGy1x0Ci', NULL),
(536, 1756112702, 'user_request', '4', NULL),
(537, 1756112739, 'user_request', '4', NULL),
(538, 1756112739, 'cache_miss', 'Cth8mqp123fxCbIh', NULL),
(539, 1756112739, 'cache_miss', 'Cth8mqp123fxCbIh', NULL),
(540, 1756112811, 'user_request', '6', NULL),
(541, 1756112833, 'user_request', '6', NULL),
(542, 1756112833, 'cache_miss', '6lv68ouHttz5nTbA', NULL),
(543, 1756112833, 'cache_miss', '6lv68ouHttz5nTbA', NULL),
(544, 1756112844, 'user_request', '6', NULL),
(545, 1756112844, 'cache_miss', '6lv68ouHttz5nTbA', NULL),
(546, 1756112844, 'cache_miss', '6lv68ouHttz5nTbA', NULL),
(547, 1756112907, 'user_request', '6', NULL),
(548, 1756112907, 'cache_miss', '6lv68ouHttz5nTbA', NULL),
(549, 1756112907, 'cache_miss', '6lv68ouHttz5nTbA', NULL),
(550, 1756112943, 'user_request', '6', NULL),
(551, 1756112943, 'cache_miss', '6lv68ouHttz5nTbA', NULL),
(552, 1756112943, 'cache_miss', '6lv68ouHttz5nTbA', NULL),
(553, 1756112960, 'user_request', '6', NULL),
(554, 1756112960, 'cache_miss', '6lv68ouHttz5nTbA', NULL),
(555, 1756112960, 'cache_miss', '6lv68ouHttz5nTbA', NULL),
(556, 1756181745, 'slow_request', '[\"GET\",\"\\/\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\RegisterController@showRegistrationForm\"]', 2193),
(557, 1756181775, 'user_request', '1', NULL),
(558, 1756181776, 'user_request', '1', NULL),
(559, 1756181781, 'user_request', '1', NULL),
(560, 1756182195, 'slow_request', '[\"POST\",\"\\/logout\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\LoginController@logout\"]', 2758),
(561, 1756182195, 'slow_user_request', '1', NULL),
(562, 1756182195, 'user_request', '1', NULL),
(563, 1756182224, 'user_request', '3', NULL),
(564, 1756182225, 'slow_request', '[\"GET\",\"\\/admin\\/dashboard\",\"Closure\"]', 1014),
(565, 1756182225, 'slow_user_request', '3', NULL),
(566, 1756182225, 'user_request', '3', NULL),
(567, 1756182229, 'user_request', '3', NULL),
(568, 1756182673, 'user_request', '3', NULL),
(569, 1756182795, 'user_request', '3', NULL),
(570, 1756182951, 'user_request', '3', NULL),
(571, 1756183020, 'user_request', '3', NULL),
(572, 1756183024, 'user_request', '3', NULL),
(573, 1756183025, 'user_request', '3', NULL),
(574, 1756183509, 'user_request', '3', NULL),
(575, 1756183555, 'user_request', '3', NULL),
(576, 1756183647, 'user_request', '3', NULL),
(577, 1756183764, 'user_request', '3', NULL),
(578, 1756183775, 'user_request', '3', NULL),
(579, 1756183824, 'user_request', '3', NULL),
(580, 1756183832, 'user_request', '3', NULL),
(581, 1756183834, 'user_request', '3', NULL),
(582, 1756183848, 'user_request', '3', NULL),
(583, 1756183914, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"Closure\"]', 3338),
(584, 1756183914, 'slow_user_request', '3', NULL),
(585, 1756183914, 'user_request', '3', NULL),
(586, 1756183914, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 1756183914),
(587, 1756184313, 'user_request', '3', NULL),
(588, 1756184433, 'user_request', '3', NULL),
(589, 1756184841, 'user_request', '3', NULL),
(590, 1756184994, 'user_request', '3', NULL),
(591, 1756185157, 'user_request', '3', NULL),
(592, 1756185160, 'user_request', '3', NULL),
(593, 1756185160, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 1756185160),
(594, 1756185215, 'user_request', '3', NULL),
(595, 1756185215, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 1756185215),
(596, 1756185245, 'user_request', '3', NULL),
(597, 1756185251, 'user_request', '3', NULL),
(598, 1756185251, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 1756185251),
(599, 1756185533, 'user_request', '3', NULL),
(600, 1756185534, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 1756185534),
(601, 1756185650, 'user_request', '3', NULL),
(602, 1756185650, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:29\"]', 1756185650),
(603, 1756185745, 'user_request', '3', NULL),
(604, 1756185745, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 1756185745),
(605, 1756185902, 'user_request', '3', NULL),
(606, 1756186155, 'user_request', '3', NULL),
(607, 1756186174, 'user_request', '3', NULL),
(608, 1756186467, 'user_request', '3', NULL),
(609, 1756186601, 'user_request', '3', NULL),
(610, 1756186901, 'user_request', '3', NULL),
(611, 1756186961, 'user_request', '3', NULL),
(612, 1756187118, 'user_request', '3', NULL),
(613, 1756188280, 'user_request', '3', NULL),
(614, 1756188295, 'user_request', '3', NULL),
(615, 1756188442, 'user_request', '3', NULL),
(616, 1756188625, 'user_request', '3', NULL),
(617, 1756188633, 'user_request', '3', NULL),
(618, 1756188654, 'user_request', '3', NULL),
(619, 1756189161, 'user_request', '3', NULL),
(620, 1756189188, 'user_request', '3', NULL),
(621, 1756189320, 'user_request', '3', NULL),
(622, 1756189398, 'user_request', '3', NULL),
(623, 1756189449, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 1009),
(624, 1756189449, 'slow_user_request', '3', NULL),
(625, 1756189449, 'user_request', '3', NULL),
(626, 1756189449, 'exception', '[\"InvalidArgumentException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 1756189449),
(627, 1756189464, 'user_request', '3', NULL),
(628, 1756189597, 'user_request', '3', NULL),
(629, 1756192203, 'user_request', '3', NULL),
(630, 1756192471, 'user_request', '3', NULL),
(631, 1756192507, 'user_request', '3', NULL),
(632, 1756192507, 'exception', '[\"ParseError\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 1756192507),
(633, 1756192512, 'user_request', '3', NULL),
(634, 1756192637, 'user_request', '3', NULL),
(635, 1756192925, 'user_request', '3', NULL),
(636, 1756192925, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:33\"]', 1756192925),
(637, 1756192939, 'user_request', '3', NULL),
(638, 1756192939, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:33\"]', 1756192939),
(639, 1756192945, 'user_request', '3', NULL),
(640, 1756193283, 'user_request', '3', NULL),
(641, 1756193283, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:33\"]', 1756193283),
(642, 1756193311, 'user_request', '3', NULL),
(643, 1756193311, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:34\"]', 1756193311),
(644, 1756193428, 'user_request', '3', NULL),
(645, 1756193587, 'user_request', '3', NULL),
(646, 1756193752, 'user_request', '3', NULL),
(647, 1756193862, 'user_request', '3', NULL),
(648, 1756193865, 'user_request', '3', NULL),
(649, 1756193904, 'user_request', '3', NULL),
(650, 1756193917, 'user_request', '3', NULL),
(651, 1756193922, 'user_request', '3', NULL),
(652, 1756193945, 'user_request', '3', NULL),
(653, 1756194007, 'user_request', '3', NULL),
(654, 1756194011, 'user_request', '3', NULL),
(655, 1756194033, 'user_request', '3', NULL),
(656, 1756194043, 'user_request', '3', NULL),
(657, 1756194058, 'user_request', '3', NULL),
(658, 1756194114, 'user_request', '3', NULL),
(659, 1756194123, 'user_request', '3', NULL),
(660, 1756194202, 'user_request', '3', NULL),
(661, 1756194209, 'user_request', '3', NULL),
(662, 1756194231, 'user_request', '3', NULL),
(663, 1756194368, 'user_request', '3', NULL),
(664, 1756194720, 'user_request', '3', NULL),
(665, 1756194757, 'user_request', '3', NULL),
(666, 1756194775, 'user_request', '3', NULL),
(667, 1756194820, 'user_request', '3', NULL),
(668, 1756194901, 'user_request', '3', NULL),
(669, 1756194958, 'user_request', '3', NULL),
(670, 1756194980, 'user_request', '3', NULL),
(671, 1756194980, 'user_request', '3', NULL),
(672, 1756194984, 'user_request', '3', NULL),
(673, 1756194987, 'user_request', '3', NULL),
(674, 1756194996, 'user_request', '3', NULL),
(675, 1756195089, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 1101),
(676, 1756195089, 'slow_user_request', '3', NULL),
(677, 1756195089, 'user_request', '3', NULL),
(678, 1756195089, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 1756195089),
(679, 1756195267, 'user_request', '3', NULL),
(680, 1756195279, 'user_request', '3', NULL),
(681, 1756195285, 'user_request', '3', NULL),
(682, 1756195286, 'user_request', '3', NULL),
(683, 1756195289, 'user_request', '3', NULL),
(684, 1756195290, 'user_request', '3', NULL),
(685, 1756195308, 'user_request', '3', NULL),
(686, 1756195308, 'user_request', '3', NULL),
(687, 1756195610, 'user_request', '3', NULL),
(688, 1756195616, 'user_request', '3', NULL),
(689, 1756195617, 'user_request', '3', NULL),
(690, 1756195800, 'user_request', '3', NULL),
(691, 1756195805, 'user_request', '3', NULL),
(692, 1756195805, 'user_request', '3', NULL),
(693, 1756195819, 'user_request', '3', NULL),
(694, 1756195823, 'user_request', '3', NULL),
(695, 1756195833, 'user_request', '3', NULL),
(696, 1756195916, 'user_request', '3', NULL),
(697, 1756196065, 'user_request', '3', NULL),
(698, 1756196200, 'user_request', '3', NULL),
(699, 1756196304, 'user_request', '3', NULL),
(700, 1756196368, 'user_request', '3', NULL),
(701, 1756196370, 'user_request', '3', NULL),
(702, 1756196374, 'user_request', '3', NULL),
(703, 1756196379, 'user_request', '3', NULL),
(704, 1756196381, 'user_request', '3', NULL),
(705, 1756196482, 'user_request', '3', NULL),
(706, 1756196486, 'user_request', '3', NULL),
(707, 1756196492, 'user_request', '3', NULL),
(708, 1756196578, 'user_request', '3', NULL),
(709, 1756196579, 'user_request', '3', NULL),
(710, 1756196778, 'user_request', '3', NULL),
(711, 1756196794, 'user_request', '3', NULL),
(712, 1756196795, 'user_request', '3', NULL),
(713, 1756196873, 'user_request', '3', NULL),
(714, 1756196891, 'user_request', '3', NULL),
(715, 1756196892, 'user_request', '3', NULL),
(716, 1756196932, 'user_request', '3', NULL),
(717, 1756196943, 'user_request', '3', NULL),
(718, 1756196947, 'user_request', '3', NULL),
(719, 1756196947, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 1756196947),
(720, 1756196966, 'user_request', '3', NULL),
(721, 1756196967, 'user_request', '3', NULL),
(722, 1756197072, 'user_request', '3', NULL),
(723, 1756197085, 'user_request', '3', NULL),
(724, 1756197094, 'user_request', '3', NULL),
(725, 1756197459, 'user_request', '3', NULL),
(726, 1756197460, 'exception', '[\"ErrorException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 1756197460),
(727, 1756197467, 'user_request', '3', NULL),
(728, 1756197467, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:19\"]', 1756197467),
(729, 1756197496, 'user_request', '3', NULL),
(730, 1756197496, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:19\"]', 1756197496),
(731, 1756198061, 'user_request', '3', NULL),
(732, 1756198065, 'user_request', '3', NULL),
(733, 1756198065, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 1756198065),
(734, 1756198123, 'user_request', '3', NULL),
(735, 1756198123, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 1756198123),
(736, 1756198163, 'user_request', '3', NULL),
(737, 1756198163, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 1756198163),
(738, 1756198266, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 1161),
(739, 1756198266, 'slow_user_request', '3', NULL),
(740, 1756198266, 'user_request', '3', NULL),
(741, 1756198266, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 1756198266),
(742, 1756198339, 'user_request', '3', NULL),
(743, 1756198339, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 1756198339),
(744, 1756198424, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 1073),
(745, 1756198424, 'slow_user_request', '3', NULL),
(746, 1756198424, 'user_request', '3', NULL),
(747, 1756198424, 'exception', '[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 1756198424),
(748, 1756198471, 'user_request', '3', NULL),
(749, 1756198472, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 1756198472),
(750, 1756198499, 'user_request', '3', NULL),
(751, 1756198499, 'exception', '[\"Illuminate\\\\View\\\\ViewException\",\"resources\\\\views\\\\partials\\\\sidebar.blade.php\"]', 1756198499),
(752, 1756198579, 'user_request', '3', NULL),
(753, 1756198582, 'user_request', '3', NULL),
(754, 1756198582, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:47\"]', 1756198582),
(755, 1756198631, 'user_request', '3', NULL),
(756, 1756198631, 'exception', '[\"Illuminate\\\\Database\\\\Eloquent\\\\RelationNotFoundException\",\"app\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController.php:47\"]', 1756198631),
(757, 1756198663, 'user_request', '3', NULL),
(758, 1756198670, 'user_request', '3', NULL),
(759, 1756198670, 'user_request', '3', NULL),
(760, 1756199063, 'user_request', '3', NULL),
(761, 1756199090, 'user_request', '3', NULL),
(762, 1756199096, 'user_request', '3', NULL),
(763, 1756199150, 'user_request', '3', NULL),
(764, 1756199345, 'user_request', '3', NULL),
(765, 1756199345, 'user_request', '3', NULL),
(766, 1756199363, 'user_request', '3', NULL),
(767, 1756199387, 'user_request', '3', NULL),
(768, 1756199387, 'user_request', '3', NULL),
(769, 1756199437, 'user_request', '3', NULL),
(770, 1756199439, 'user_request', '3', NULL),
(771, 1756199486, 'user_request', '3', NULL),
(772, 1756199518, 'user_request', '3', NULL),
(773, 1756199542, 'user_request', '3', NULL),
(774, 1756199545, 'user_request', '3', NULL),
(775, 1756199549, 'user_request', '3', NULL),
(776, 1756199552, 'user_request', '3', NULL),
(777, 1756199558, 'user_request', '3', NULL),
(778, 1756199565, 'user_request', '3', NULL),
(779, 1756199592, 'user_request', '3', NULL),
(780, 1756199592, 'user_request', '3', NULL),
(781, 1756199762, 'user_request', '3', NULL),
(782, 1756199762, 'user_request', '3', NULL),
(783, 1756199830, 'user_request', '3', NULL),
(784, 1756199830, 'user_request', '3', NULL),
(785, 1756199956, 'user_request', '3', NULL),
(786, 1756199956, 'user_request', '3', NULL),
(787, 1756200001, 'user_request', '3', NULL),
(788, 1756200005, 'user_request', '3', NULL),
(789, 1756200008, 'user_request', '3', NULL),
(790, 1756200044, 'user_request', '3', NULL),
(791, 1756200045, 'user_request', '3', NULL),
(792, 1756200065, 'user_request', '3', NULL),
(793, 1756200088, 'user_request', '3', NULL),
(794, 1756200088, 'user_request', '3', NULL),
(795, 1756200185, 'user_request', '3', NULL),
(796, 1756200185, 'user_request', '3', NULL),
(797, 1756200394, 'user_request', '3', NULL),
(798, 1756200394, 'user_request', '3', NULL),
(799, 1756200434, 'user_request', '3', NULL),
(800, 1756200434, 'user_request', '3', NULL),
(801, 1756200604, 'user_request', '3', NULL),
(802, 1756200608, 'user_request', '3', NULL),
(803, 1756200612, 'user_request', '3', NULL),
(804, 1756200619, 'user_request', '3', NULL),
(805, 1756200620, 'user_request', '3', NULL),
(806, 1756200626, 'user_request', '3', NULL),
(807, 1756200748, 'user_request', '3', NULL),
(808, 1756201004, 'user_request', '3', NULL),
(809, 1756201010, 'user_request', '3', NULL),
(810, 1756201010, 'user_request', '3', NULL),
(811, 1756202184, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 2784),
(812, 1756202184, 'slow_user_request', '3', NULL),
(813, 1756202184, 'user_request', '3', NULL),
(814, 1756202192, 'user_request', '3', NULL),
(815, 1756202210, 'user_request', '3', NULL),
(816, 1756202210, 'user_request', '3', NULL),
(817, 1756202218, 'user_request', '3', NULL),
(818, 1756202218, 'user_request', '3', NULL),
(819, 1756202469, 'user_request', '3', NULL),
(820, 1756202985, 'user_request', '3', NULL),
(821, 1756202985, 'user_request', '3', NULL),
(822, 1756202990, 'user_request', '3', NULL),
(823, 1756202990, 'user_request', '3', NULL),
(824, 1756202996, 'user_request', '3', NULL),
(825, 1756202996, 'user_request', '3', NULL),
(826, 1756203198, 'user_request', '3', NULL),
(827, 1756203306, 'user_request', '3', NULL),
(828, 1756203306, 'user_request', '3', NULL),
(829, 1756203312, 'user_request', '3', NULL),
(830, 1756203326, 'user_request', '3', NULL),
(831, 1756203438, 'user_request', '3', NULL),
(832, 1756203475, 'slow_request', '[\"GET\",\"\\/restaurant\\/orders\\/{id}\",\"App\\\\Http\\\\Controllers\\\\Restaurant\\\\OrderController@ShowDetails\"]', 2960),
(833, 1756203475, 'slow_user_request', '3', NULL),
(834, 1756203475, 'user_request', '3', NULL),
(835, 1756203476, 'exception', '[\"ParseError\",\"resources\\\\views\\\\pages\\\\restaurant_staff\\\\order_details.blade.php\"]', 1756203476),
(836, 1756203482, 'user_request', '3', NULL),
(837, 1756203501, 'user_request', '3', NULL),
(838, 1756203511, 'user_request', '3', NULL),
(839, 1756203529, 'user_request', '3', NULL),
(840, 1756203537, 'user_request', '3', NULL),
(841, 1756203560, 'user_request', '3', NULL),
(842, 1756203596, 'user_request', '3', NULL),
(843, 1756203678, 'user_request', '3', NULL),
(844, 1756203701, 'user_request', '3', NULL),
(845, 1756204571, 'user_request', '3', NULL),
(846, 1756204578, 'user_request', '3', NULL),
(847, 1756204602, 'user_request', '3', NULL),
(848, 1756204611, 'user_request', '3', NULL),
(849, 1756204621, 'user_request', '3', NULL),
(850, 1756204643, 'user_request', '3', NULL),
(851, 1756204659, 'user_request', '3', NULL),
(852, 1756205538, 'user_request', '3', NULL),
(853, 1756205560, 'user_request', '3', NULL),
(854, 1756205570, 'user_request', '3', NULL),
(855, 1756205577, 'user_request', '3', NULL),
(856, 1756205579, 'user_request', '3', NULL),
(857, 1756205608, 'user_request', '3', NULL),
(858, 1756205618, 'user_request', '3', NULL),
(859, 1756205949, 'user_request', '3', NULL),
(860, 1756205978, 'user_request', '3', NULL),
(861, 1756205999, 'user_request', '3', NULL),
(862, 1756206001, 'user_request', '3', NULL),
(863, 1756206014, 'user_request', '3', NULL),
(864, 1756206017, 'user_request', '3', NULL),
(865, 1756206026, 'user_request', '3', NULL),
(866, 1756206071, 'user_request', '6', NULL),
(867, 1756206071, 'user_request', '6', NULL),
(868, 1756206078, 'user_request', '6', NULL),
(869, 1756206081, 'user_request', '6', NULL),
(870, 1756206126, 'user_request', '6', NULL),
(871, 1756206145, 'user_request', '6', NULL),
(872, 1756206220, 'user_request', '6', NULL),
(873, 1756206239, 'user_request', '6', NULL),
(874, 1756206243, 'user_request', '6', NULL),
(875, 1756206285, 'user_request', '6', NULL),
(876, 1756206340, 'user_request', '6', NULL),
(877, 1756206342, 'user_request', '6', NULL),
(878, 1756206354, 'user_request', '6', NULL),
(879, 1756206361, 'user_request', '6', NULL),
(880, 1756206378, 'user_request', '1', NULL),
(881, 1756206378, 'user_request', '1', NULL),
(882, 1756206383, 'user_request', '1', NULL),
(883, 1756206395, 'user_request', '1', NULL),
(884, 1756206400, 'user_request', '1', NULL),
(885, 1756206403, 'user_request', '1', NULL),
(886, 1756206407, 'user_request', '1', NULL),
(887, 1756206410, 'user_request', '1', NULL),
(888, 1756206415, 'user_request', '1', NULL),
(889, 1756206424, 'user_request', '1', NULL),
(890, 1756206440, 'user_request', '3', NULL),
(891, 1756206440, 'user_request', '3', NULL),
(892, 1756206447, 'user_request', '3', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pulse_values`
--

CREATE TABLE `pulse_values` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `timestamp` int(10) UNSIGNED NOT NULL,
  `type` varchar(191) NOT NULL,
  `key` mediumtext NOT NULL,
  `key_hash` binary(16) GENERATED ALWAYS AS (unhex(md5(`key`))) VIRTUAL,
  `value` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referrals`
--

CREATE TABLE `referrals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `referrer_id` bigint(20) UNSIGNED NOT NULL,
  `referred_id` bigint(20) UNSIGNED NOT NULL,
  `referral_code` varchar(20) NOT NULL,
  `referrer_reward_amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `referred_reward_amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `referrer_reward_status` enum('pending','credited','expired') NOT NULL DEFAULT 'pending',
  `referred_reward_status` enum('pending','credited','expired') NOT NULL DEFAULT 'pending',
  `first_order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `first_order_completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
--

CREATE TABLE `refunds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `initiated_by` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `refund_amount` decimal(10,2) NOT NULL,
  `refund_reason` enum('order_cancelled','order_rejected','dispute_resolution','partial_refund') NOT NULL,
  `refund_method` enum('original_source','wallet') NOT NULL,
  `gateway_refund_id` varchar(191) DEFAULT NULL,
  `status` enum('pending','processing','completed','failed') NOT NULL DEFAULT 'pending',
  `gateway_response` text DEFAULT NULL,
  `failure_reason` text DEFAULT NULL,
  `initiated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `location_admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `restaurant_name` varchar(191) NOT NULL,
  `contact_person_name` varchar(191) DEFAULT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `cuisine_type` varchar(100) DEFAULT NULL,
  `address` text NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `city` bigint(20) UNSIGNED NOT NULL,
  `state` bigint(20) UNSIGNED NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(191) NOT NULL,
  `website_url` varchar(500) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `cover_image_url` varchar(500) DEFAULT NULL,
  `delivery_radius_km` int(11) NOT NULL DEFAULT 10,
  `minimum_order_amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `base_delivery_fee` decimal(6,2) NOT NULL DEFAULT 0.00,
  `restaurant_commission_percentage` decimal(5,2) NOT NULL DEFAULT 80.00,
  `estimated_delivery_time` int(11) NOT NULL DEFAULT 30,
  `tax_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `is_open` tinyint(1) NOT NULL DEFAULT 1,
  `accepts_orders` tinyint(1) NOT NULL DEFAULT 1,
  `status` enum('pending','approved','suspended','rejected') NOT NULL DEFAULT 'pending',
  `average_rating` decimal(3,2) NOT NULL DEFAULT 0.00,
  `total_reviews` int(11) NOT NULL DEFAULT 0,
  `total_orders` int(11) NOT NULL DEFAULT 0,
  `business_hours` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`business_hours`)),
  `special_instructions` text DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `setup_completed` tinyint(1) NOT NULL DEFAULT 0,
  `onboarding_step` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `rejected_by` bigint(20) UNSIGNED DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`id`, `tenant_id`, `location_admin_id`, `user_id`, `restaurant_name`, `contact_person_name`, `slug`, `description`, `cuisine_type`, `address`, `latitude`, `longitude`, `city`, `state`, `postal_code`, `phone`, `email`, `website_url`, `image_url`, `cover_image_url`, `delivery_radius_km`, `minimum_order_amount`, `base_delivery_fee`, `restaurant_commission_percentage`, `estimated_delivery_time`, `tax_percentage`, `is_open`, `accepts_orders`, `status`, `average_rating`, `total_reviews`, `total_orders`, `business_hours`, `special_instructions`, `is_featured`, `setup_completed`, `onboarding_step`, `created_at`, `updated_at`, `approved_at`, `approved_by`, `rejected_at`, `rejected_by`, `rejection_reason`, `deleted_at`) VALUES
(3, 1, 2, 1, 'Hyatt Mcbride', 'Nina Mccarthy', 'hyatt-mcbride', NULL, NULL, 'Ea sunt natus susci', 47.00000000, 76.00000000, 5, 4, '75836', '+1 (347) 296-7339', 'nahyto@mailinator.com', NULL, NULL, NULL, 41, 91.00, 5.00, 10.00, 20, 4.00, 0, 1, 'pending', 0.00, 0, 0, '\"{\\\"monday\\\":{\\\"day\\\":\\\"monday\\\",\\\"is_open\\\":false,\\\"opening_time\\\":null,\\\"closing_time\\\":null},\\\"tuesday\\\":{\\\"day\\\":\\\"tuesday\\\",\\\"is_open\\\":true,\\\"opening_time\\\":\\\"17:56\\\",\\\"closing_time\\\":\\\"05:32\\\"},\\\"wednesday\\\":{\\\"day\\\":\\\"wednesday\\\",\\\"is_open\\\":true,\\\"opening_time\\\":\\\"03:30\\\",\\\"closing_time\\\":\\\"14:52\\\"},\\\"thursday\\\":{\\\"day\\\":\\\"thursday\\\",\\\"is_open\\\":true,\\\"opening_time\\\":\\\"12:00\\\",\\\"closing_time\\\":\\\"20:24\\\"},\\\"friday\\\":{\\\"day\\\":\\\"friday\\\",\\\"is_open\\\":true,\\\"opening_time\\\":\\\"02:01\\\",\\\"closing_time\\\":\\\"05:35\\\"},\\\"saturday\\\":{\\\"day\\\":\\\"saturday\\\",\\\"is_open\\\":true,\\\"opening_time\\\":\\\"11:54\\\",\\\"closing_time\\\":\\\"15:42\\\"},\\\"sunday\\\":{\\\"day\\\":\\\"sunday\\\",\\\"is_open\\\":false,\\\"opening_time\\\":null,\\\"closing_time\\\":null}}\"', NULL, 0, 0, 1, '2025-08-20 06:40:27', '2025-08-20 06:40:27', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 1, 2, 1, 'Jada Lancaster', 'Hannah Burris', 'jada-lancaster', NULL, NULL, 'Sit sed sint tempori', 66.00000000, 8.00000000, 6, 5, '71280', '+1 (474) 229-6168', 'nivudo@mailinator.com', NULL, NULL, NULL, 1, 53.00, 35.00, 8.00, 19, 18.00, 1, 1, 'pending', 0.00, 0, 0, '\"{\\\"monday\\\":{\\\"day\\\":\\\"monday\\\",\\\"is_open\\\":true,\\\"opening_time\\\":\\\"09:00\\\",\\\"closing_time\\\":\\\"22:00\\\"},\\\"tuesday\\\":{\\\"day\\\":\\\"tuesday\\\",\\\"is_open\\\":true,\\\"opening_time\\\":\\\"09:00\\\",\\\"closing_time\\\":\\\"22:00\\\"},\\\"wednesday\\\":{\\\"day\\\":\\\"wednesday\\\",\\\"is_open\\\":true,\\\"opening_time\\\":\\\"09:00\\\",\\\"closing_time\\\":\\\"22:00\\\"},\\\"thursday\\\":{\\\"day\\\":\\\"thursday\\\",\\\"is_open\\\":true,\\\"opening_time\\\":\\\"09:00\\\",\\\"closing_time\\\":\\\"22:00\\\"},\\\"friday\\\":{\\\"day\\\":\\\"friday\\\",\\\"is_open\\\":true,\\\"opening_time\\\":\\\"00:09\\\",\\\"closing_time\\\":\\\"18:05\\\"},\\\"saturday\\\":{\\\"day\\\":\\\"saturday\\\",\\\"is_open\\\":true,\\\"opening_time\\\":\\\"06:48\\\",\\\"closing_time\\\":\\\"16:27\\\"},\\\"sunday\\\":{\\\"day\\\":\\\"sunday\\\",\\\"is_open\\\":true,\\\"opening_time\\\":\\\"09:00\\\",\\\"closing_time\\\":\\\"22:00\\\"}}\"', NULL, 0, 0, 1, '2025-08-20 06:43:58', '2025-08-20 06:43:58', NULL, NULL, NULL, NULL, NULL, NULL),
(5, 2, 2, 1, 'pizza wala', 'Kuame Richard', 'pizza-wala', NULL, 'indian', 'Esse optio nulla fu', 5.00000000, 65.00000000, 1, 1, '90713', '+1 (351) 195-3795', 'abhishek@mailinator.com', NULL, NULL, NULL, 5, 45.00, 25.00, 20.00, 80, 4.00, 1, 1, 'pending', 0.00, 0, 0, '\"{\\\"monday\\\":{\\\"day\\\":\\\"monday\\\",\\\"is_open\\\":true,\\\"opening_time\\\":\\\"09:00\\\",\\\"closing_time\\\":\\\"22:00\\\"},\\\"tuesday\\\":{\\\"day\\\":\\\"tuesday\\\",\\\"is_open\\\":true,\\\"opening_time\\\":\\\"03:31\\\",\\\"closing_time\\\":\\\"09:17\\\"},\\\"wednesday\\\":{\\\"day\\\":\\\"wednesday\\\",\\\"is_open\\\":true,\\\"opening_time\\\":\\\"13:59\\\",\\\"closing_time\\\":\\\"12:45\\\"},\\\"thursday\\\":{\\\"day\\\":\\\"thursday\\\",\\\"is_open\\\":true,\\\"opening_time\\\":\\\"09:00\\\",\\\"closing_time\\\":\\\"22:00\\\"},\\\"friday\\\":{\\\"day\\\":\\\"friday\\\",\\\"is_open\\\":true,\\\"opening_time\\\":\\\"01:59\\\",\\\"closing_time\\\":\\\"16:54\\\"},\\\"saturday\\\":{\\\"day\\\":\\\"saturday\\\",\\\"is_open\\\":true,\\\"opening_time\\\":\\\"11:05\\\",\\\"closing_time\\\":\\\"11:41\\\"},\\\"sunday\\\":{\\\"day\\\":\\\"sunday\\\",\\\"is_open\\\":true,\\\"opening_time\\\":\\\"09:00\\\",\\\"closing_time\\\":\\\"22:00\\\"}}\"', NULL, 0, 0, 1, '2025-08-20 06:45:35', '2025-08-20 06:45:35', NULL, NULL, NULL, NULL, NULL, NULL),
(6, 3, 3, 1, 'kala jaado', 'hritick kaka', 'kala-jaado', NULL, 'Italian', 'Nemo error magna iru', 22.00000000, 75.00000000, 3, 2, '50814', '+1 (263) 811-6847', 'rohit@gmail.com', NULL, NULL, NULL, 33, 61.00, 29.00, 10.00, 47, 8.00, 1, 1, 'rejected', 3.00, 0, 0, '\"{\\\"monday\\\":{\\\"day\\\":\\\"monday\\\",\\\"is_open\\\":false,\\\"opening_time\\\":null,\\\"closing_time\\\":null},\\\"tuesday\\\":{\\\"day\\\":\\\"tuesday\\\",\\\"is_open\\\":false,\\\"opening_time\\\":null,\\\"closing_time\\\":null},\\\"wednesday\\\":{\\\"day\\\":\\\"wednesday\\\",\\\"is_open\\\":true,\\\"opening_time\\\":\\\"09:00\\\",\\\"closing_time\\\":\\\"22:00\\\"},\\\"thursday\\\":{\\\"day\\\":\\\"thursday\\\",\\\"is_open\\\":true,\\\"opening_time\\\":\\\"09:00\\\",\\\"closing_time\\\":\\\"22:00\\\"},\\\"friday\\\":{\\\"day\\\":\\\"friday\\\",\\\"is_open\\\":true,\\\"opening_time\\\":\\\"10:37\\\",\\\"closing_time\\\":\\\"17:56\\\"},\\\"saturday\\\":{\\\"day\\\":\\\"saturday\\\",\\\"is_open\\\":true,\\\"opening_time\\\":\\\"04:53\\\",\\\"closing_time\\\":\\\"12:05\\\"},\\\"sunday\\\":{\\\"day\\\":\\\"sunday\\\",\\\"is_open\\\":true,\\\"opening_time\\\":\\\"13:28\\\",\\\"closing_time\\\":\\\"21:18\\\"}}\"', 'Rejection Reason: asdfghjk', 0, 0, 1, '2025-08-20 23:12:09', '2025-08-21 04:28:05', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_banners`
--

CREATE TABLE `restaurant_banners` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(500) NOT NULL,
  `link_type` enum('restaurant','menu_item','promotion','external') NOT NULL,
  `link_id` bigint(20) UNSIGNED DEFAULT NULL,
  `external_url` varchar(500) DEFAULT NULL,
  `banner_position` enum('home_slider','restaurant_page','category_page') NOT NULL DEFAULT 'home_slider',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `click_count` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `valid_from` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `valid_until` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_documents`
--

CREATE TABLE `restaurant_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `document_type` enum('food_safety_certificate','business_license','pan_card','gst_certificate','owner_id_proof','bank_details','insurance_certificate','fire_safety_certificate','trade_license','pollution_certificate') NOT NULL,
  `document_path` varchar(500) NOT NULL,
  `document_name` varchar(191) NOT NULL,
  `original_filename` varchar(500) NOT NULL,
  `file_size` int(11) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_menu_items`
--

CREATE TABLE `restaurant_menu_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `parent_menu_item_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(8,2) NOT NULL,
  `image_url` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_staff`
--

CREATE TABLE `restaurant_staff` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role` enum('location_admin','manager','chef','cashier') NOT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `hired_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `terminated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_working_hours`
--

CREATE TABLE `restaurant_working_hours` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `day_of_week` tinyint(4) NOT NULL,
  `is_open` tinyint(1) NOT NULL DEFAULT 1,
  `open_time` time NOT NULL DEFAULT '09:00:00',
  `close_time` time NOT NULL DEFAULT '22:00:00',
  `break_start_time` time DEFAULT NULL,
  `break_end_time` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `reviewable_type` varchar(255) NOT NULL,
  `reviewable_id` bigint(20) UNSIGNED NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `review_text` text DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `is_anonymous` tinyint(1) NOT NULL DEFAULT 0,
  `admin_response` text DEFAULT NULL,
  `admin_responded_at` timestamp NULL DEFAULT NULL,
  `admin_responded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `order_id`, `tenant_id`, `customer_id`, `reviewable_type`, `reviewable_id`, `rating`, `review_text`, `images`, `is_anonymous`, `admin_response`, `admin_responded_at`, `admin_responded_by`, `is_featured`, `created_at`, `updated_at`, `deleted_at`) VALUES
(9, 7, 3, 1, 'restaurant', 6, 5, 'restaurant is bahoot badhiya hovat hai', NULL, 0, NULL, NULL, NULL, 0, '2025-08-25 00:37:37', '2025-08-25 00:37:37', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(191) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE `states` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `country_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `iso_code` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`id`, `country_id`, `name`, `iso_code`, `created_at`, `updated_at`) VALUES
(1, 1, 'Maharashtra', 'MH', NULL, NULL),
(2, 1, 'Karnataka', 'KA', NULL, NULL),
(3, 1, 'Delhi', 'DL', NULL, NULL),
(4, 1, 'Gujarat', 'GJ', NULL, NULL),
(5, 1, 'West Bengal', 'WB', NULL, NULL),
(6, 2, 'California', 'CA', NULL, NULL),
(7, 2, 'Texas', 'TX', NULL, NULL),
(8, 2, 'New York', 'NY', NULL, NULL),
(9, 2, 'Florida', 'FL', NULL, NULL),
(10, 2, 'Illinois', 'IL', NULL, NULL),
(11, 3, 'Ontario', 'ON', NULL, NULL),
(12, 3, 'Quebec', 'QC', NULL, NULL),
(13, 3, 'British Columbia', 'BC', NULL, NULL),
(14, 3, 'Alberta', 'AB', NULL, NULL),
(15, 3, 'Manitoba', 'MB', NULL, NULL),
(16, 4, 'New South Wales', 'NSW', NULL, NULL),
(17, 4, 'Victoria', 'VIC', NULL, NULL),
(18, 4, 'Queensland', 'QLD', NULL, NULL),
(19, 4, 'Western Australia', 'WA', NULL, NULL),
(20, 4, 'South Australia', 'SA', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subscription_payments`
--

CREATE TABLE `subscription_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `subscription_plan` enum('LITE','PLUS','PRO_MAX') NOT NULL,
  `restaurant_count` int(11) NOT NULL,
  `base_amount` decimal(10,2) NOT NULL,
  `per_restaurant_amount` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `billing_period_start` date NOT NULL,
  `billing_period_end` date NOT NULL,
  `payment_method` enum('card','upi','netbanking','wallet') NOT NULL,
  `payment_gateway` enum('razorpay','stripe','paytm','phonepe') NOT NULL,
  `gateway_transaction_id` varchar(191) DEFAULT NULL,
  `status` enum('pending','completed','failed','cancelled') NOT NULL DEFAULT 'pending',
  `due_date` date NOT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `failure_reason` text DEFAULT NULL,
  `auto_retry_count` int(11) NOT NULL DEFAULT 0,
  `next_retry_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  `setting_type` enum('string','integer','decimal','boolean','json') NOT NULL,
  `description` text DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tenants`
--

CREATE TABLE `tenants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_name` varchar(191) NOT NULL,
  `contact_person` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(191) NOT NULL,
  `subscription_plan` enum('LITE','PLUS','PRO_MAX') NOT NULL DEFAULT 'LITE',
  `total_restaurants` int(11) NOT NULL DEFAULT 0,
  `monthly_base_fee` decimal(10,2) NOT NULL,
  `per_restaurant_fee` decimal(10,2) NOT NULL,
  `banner_limit` int(11) NOT NULL DEFAULT 0,
  `status` enum('pending','approved','suspended','rejected','subscription_expired') NOT NULL DEFAULT 'pending',
  `subscription_start_date` date DEFAULT NULL,
  `next_billing_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tenants`
--

INSERT INTO `tenants` (`id`, `tenant_name`, `contact_person`, `email`, `phone`, `subscription_plan`, `total_restaurants`, `monthly_base_fee`, `per_restaurant_fee`, `banner_limit`, `status`, `subscription_start_date`, `next_billing_date`, `created_at`, `updated_at`, `approved_at`, `approved_by`, `deleted_at`) VALUES
(1, 'new tenant', 'tenant-contect', 'tenant@gmail.com', '741236985', 'PLUS', 12, 99.00, 5.00, 15, 'approved', '2025-08-20', '2025-09-20', '2025-08-20 06:03:04', '2025-08-20 06:43:58', '2025-08-20 06:03:20', 1, NULL),
(2, 'pizza wala', 'akash', 'akashbhalani21@gmail.com', '789456123', 'LITE', 1, 0.00, 0.00, 5, 'approved', '2025-08-20', '2025-09-20', '2025-08-20 06:45:35', '2025-08-20 06:45:35', NULL, NULL, NULL),
(3, 'kala jaado', 'rosan kaka', 'kaka@gmail.com', '12365478963332', 'LITE', 1, 0.00, 0.00, 5, 'approved', '2025-08-21', '2025-09-21', '2025-08-20 23:12:09', '2025-08-20 23:12:09', NULL, NULL, NULL),
(4, 'Demo Tenant', 'John Doe', 'tenant@example.com', '1234567890', 'LITE', 0, 100.00, 10.00, 3, 'approved', '2025-08-21', '2025-09-21', '2025-08-21 04:36:21', '2025-08-21 04:36:21', NULL, NULL, NULL),
(6, 'Demo Tenant', 'Demo Owner', 'tenant_68a832f8c548d@example.com', '1234567890', 'LITE', 1, 0.00, 0.00, 0, 'approved', '2025-08-22', '2025-09-22', '2025-09-22 03:36:00', '2025-09-22 03:36:00', NULL, NULL, NULL),
(7, 'Demo Tenant', 'Demo Owner', 'tenant_68a8340364d77@example.com', '1234567890', 'LITE', 1, 0.00, 0.00, 0, 'approved', '2025-08-22', '2025-09-22', '2025-08-22 03:40:27', '2025-08-22 03:40:27', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `transaction_logs`
--

CREATE TABLE `transaction_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `user_type` enum('restaurant','delivery_partner') NOT NULL,
  `transaction_type` enum('order_credit','bank_transfer_debit') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payout_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(500) NOT NULL,
  `wallet_balance_before` decimal(10,2) NOT NULL,
  `wallet_balance_after` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `restaurant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(191) NOT NULL,
  `password` varchar(191) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `role` enum('super_admin','tenant_admin','location_admin','restaurant_staff','customer','delivery_partner') NOT NULL,
  `status` enum('active','inactive','suspended','pending_approval') NOT NULL DEFAULT 'active',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `phone_verified_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `fcm_token` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `tenant_id`, `restaurant_id`, `email`, `phone`, `password`, `first_name`, `last_name`, `role`, `status`, `email_verified_at`, `phone_verified_at`, `last_login_at`, `fcm_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, NULL, 'Admin@gmail.com', '8980202753', '$2y$12$RdLggxnqBOwtwJjb7FpJZuJ73AeY6j2mjgpCtZsOej0mOzHtu9XKq', 'Admin', 'Admin', 'super_admin', 'active', NULL, NULL, '2025-08-26 05:36:18', NULL, '2025-08-20 06:01:51', '2025-08-26 05:36:18', NULL),
(2, NULL, NULL, 'location@gmail.comQA', '741258963', '$2y$12$rBv05drnt056G.a9Qk2kKeegDxWu/cljMWrf/EA/EyNGpD4OwQbQ.', 'location', 'admin', 'location_admin', 'active', NULL, NULL, '2025-08-21 03:23:18', NULL, '2025-08-20 06:12:57', '2025-08-21 03:23:18', NULL),
(3, 3, 6, 'karish@mailinator.com', '+1 (614) 238-4646', '$2y$12$RdLggxnqBOwtwJjb7FpJZuJ73AeY6j2mjgpCtZsOej0mOzHtu9XKq', 'koimillgya', 'nothing', 'tenant_admin', 'active', NULL, NULL, '2025-08-26 05:37:20', NULL, '2025-08-20 23:12:09', '2025-08-26 05:37:20', NULL),
(4, NULL, NULL, 'fuki@mailinator.com', '+1 (139) 912-9823', '$2y$12$ti1/2XroY2Pgz1JUEb1WNOx1sse8DKAXHZ8LGd8Xe2wzGVnP1X1mm', 'Glenna', 'Sellers', 'customer', 'active', NULL, NULL, '2025-08-25 03:35:02', NULL, '2025-08-21 00:52:36', '2025-08-25 03:35:02', NULL),
(5, NULL, NULL, 'baburao@gmail.com', '8523697412', '$2y$12$SeIw/jXeEdxa4Ks36TgDM.vAIL4bmIeqV9wzh3.AUM6/yXmHs8WNK', 'baburao', 'babukaka', 'customer', 'active', NULL, NULL, NULL, NULL, '2025-08-21 01:03:00', '2025-08-21 01:03:00', NULL),
(6, 4, NULL, 'customer@example.com', '9876543210', '$2y$12$SeIw/jXeEdxa4Ks36TgDM.vAIL4bmIeqV9wzh3.AUM6/yXmHs8WNK', 'Jane', 'Smith', 'customer', 'active', NULL, NULL, '2025-08-26 05:31:11', NULL, '2025-08-21 04:36:21', '2025-08-26 05:31:11', NULL),
(7, 7, NULL, 'customer_68a8340366c52@example.com', '9999999999', '$2y$12$iKT2GRIoDdwefNRLQaAVvOKdkMDM0ADy8.npa9wZaFPxZLnEajBta', 'Demo', 'Customer', 'customer', 'active', NULL, NULL, NULL, NULL, '2025-08-22 03:40:27', '2025-08-22 03:40:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `variation_options`
--

CREATE TABLE `variation_options` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `variation_id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `option_name` varchar(191) NOT NULL,
  `price_modifier` decimal(8,2) NOT NULL DEFAULT 0.00,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `user_type` enum('customer','restaurant','delivery_partner') NOT NULL,
  `current_balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `pending_balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `available_balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_credited` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_debited` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `daily_transfer_limit` decimal(10,2) NOT NULL DEFAULT 50000.00,
  `monthly_transfer_limit` decimal(10,2) NOT NULL DEFAULT 1000000.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wallet_split_transactions`
--

CREATE TABLE `wallet_split_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_wallet_id` bigint(20) UNSIGNED NOT NULL,
  `delivery_wallet_id` bigint(20) UNSIGNED NOT NULL,
  `total_order_amount` decimal(10,2) NOT NULL,
  `restaurant_amount` decimal(10,2) NOT NULL,
  `delivery_amount` decimal(6,2) NOT NULL,
  `platform_fee` decimal(6,2) NOT NULL DEFAULT 0.00,
  `restaurant_released` tinyint(1) NOT NULL DEFAULT 0,
  `delivery_released` tinyint(1) NOT NULL DEFAULT 0,
  `restaurant_release_trigger` enum('pickup_otp','manual','auto') DEFAULT NULL,
  `delivery_release_trigger` enum('delivery_otp','manual','auto') DEFAULT NULL,
  `restaurant_released_at` timestamp NULL DEFAULT NULL,
  `delivery_released_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wallet_transactions`
--

CREATE TABLE `wallet_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `wallet_id` bigint(20) UNSIGNED NOT NULL,
  `reference_number` varchar(100) NOT NULL,
  `transaction_type` enum('credit','debit') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `purpose` enum('order_payment','order_credit_restaurant','order_credit_delivery','refund','dispute_fine','subscription_payment','payout_request','wallet_topup','tip_received','commission_deduction','pickup_release','delivery_release') NOT NULL,
  `reference_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `previous_balance` decimal(10,2) NOT NULL,
  `current_balance` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','failed','cancelled') NOT NULL DEFAULT 'completed',
  `failure_reason` text DEFAULT NULL,
  `processed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_action` (`user_id`,`action`),
  ADD KEY `idx_table_record` (`table_name`,`record_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `automated_payout_requests`
--
ALTER TABLE `automated_payout_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `automated_payout_requests_payment_id_foreign` (`payment_id`),
  ADD KEY `automated_payout_requests_bank_account_id_foreign` (`bank_account_id`),
  ADD KEY `idx_user_status` (`user_id`,`status`),
  ADD KEY `idx_requested_date` (`requested_at`),
  ADD KEY `idx_retry_schedule` (`status`,`next_retry_at`),
  ADD KEY `idx_processing_status` (`status`,`processed_at`);

--
-- Indexes for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_type` (`user_id`,`account_type`),
  ADD KEY `idx_verification` (`is_verified`,`verification_date`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cities_state_id_foreign` (`state_id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_addresses`
--
ALTER TABLE `customer_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_customer_default` (`customer_id`,`is_default`),
  ADD KEY `idx_location` (`latitude`,`longitude`);

--
-- Indexes for table `customer_favorite_items`
--
ALTER TABLE `customer_favorite_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_customer_item` (`customer_id`,`item_id`),
  ADD KEY `customer_favorite_items_item_id_foreign` (`item_id`),
  ADD KEY `customer_favorite_items_restaurant_id_foreign` (`restaurant_id`),
  ADD KEY `idx_customer_favorites` (`customer_id`,`added_at`),
  ADD KEY `idx_tenant_restaurant` (`tenant_id`);

--
-- Indexes for table `customer_profiles`
--
ALTER TABLE `customer_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_profiles_referral_code_unique` (`referral_code`),
  ADD KEY `customer_profiles_user_id_foreign` (`user_id`),
  ADD KEY `customer_profiles_referred_by_foreign` (`referred_by`),
  ADD KEY `idx_referral_code` (`referral_code`),
  ADD KEY `idx_loyalty_points` (`loyalty_points`);

--
-- Indexes for table `delivery_assignments`
--
ALTER TABLE `delivery_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `delivery_assignments_assigned_by_foreign` (`assigned_by`),
  ADD KEY `idx_order_status` (`order_id`,`status`),
  ADD KEY `idx_partner_status` (`partner_id`,`status`),
  ADD KEY `idx_assignment_date` (`assigned_at`,`status`),
  ADD KEY `idx_tenant_restaurant` (`tenant_id`);

--
-- Indexes for table `delivery_partners`
--
ALTER TABLE `delivery_partners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `delivery_partners_user_id_foreign` (`user_id`),
  ADD KEY `idx_status_available` (`status`,`is_available`),
  ADD KEY `idx_location` (`current_latitude`,`current_longitude`);

--
-- Indexes for table `delivery_partner_documents`
--
ALTER TABLE `delivery_partner_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_partner_type` (`partner_id`,`document_type`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `delivery_tracking`
--
ALTER TABLE `delivery_tracking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_assignment_time` (`assignment_id`,`recorded_at`),
  ADD KEY `idx_location` (`latitude`,`longitude`),
  ADD KEY `idx_tenant_restaurant` (`tenant_id`);

--
-- Indexes for table `delivery_zones`
--
ALTER TABLE `delivery_zones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_restaurant_active` (`restaurant_id`,`is_active`),
  ADD KEY `idx_tenant_restaurant` (`tenant_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_items_restaurant_id_foreign` (`restaurant_id`);

--
-- Indexes for table `item_compatibility`
--
ALTER TABLE `item_compatibility`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_compatibility` (`item_id`,`compatible_item_id`,`compatibility_type`),
  ADD KEY `item_compatibility_compatible_item_id_foreign` (`compatible_item_id`),
  ADD KEY `idx_item_type` (`item_id`,`compatibility_type`);

--
-- Indexes for table `item_disputes`
--
ALTER TABLE `item_disputes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_disputes_raised_by_foreign` (`raised_by`),
  ADD KEY `item_disputes_resolved_by_foreign` (`resolved_by`),
  ADD KEY `idx_order_status` (`order_id`,`status`),
  ADD KEY `idx_assigned_status` (`assigned_to`,`status`),
  ADD KEY `idx_tenant_restaurant` (`tenant_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_categories`
--
ALTER TABLE `menu_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_categories_restaurant_id_foreign` (`restaurant_id`),
  ADD KEY `idx_tenant_restaurant` (`tenant_id`,`restaurant_id`),
  ADD KEY `idx_sort_active` (`sort_order`,`is_active`),
  ADD KEY `menu_categories_menu_template_id_foreign` (`menu_template_id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_items_restaurant_id_foreign` (`restaurant_id`),
  ADD KEY `idx_tenant_restaurant` (`tenant_id`,`restaurant_id`),
  ADD KEY `idx_category_available` (`menu_category_id`,`is_available`),
  ADD KEY `idx_popular_rating` (`is_popular`,`average_rating`);

--
-- Indexes for table `menu_item_reviews`
--
ALTER TABLE `menu_item_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_item_reviews_order_item_id_foreign` (`order_item_id`),
  ADD KEY `idx_item_rating` (`item_id`,`rating`),
  ADD KEY `idx_customer_item` (`customer_id`,`item_id`);

--
-- Indexes for table `menu_item_variations`
--
ALTER TABLE `menu_item_variations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_item_sort` (`item_id`,`sort_order`),
  ADD KEY `idx_tenant_restaurant` (`tenant_id`);

--
-- Indexes for table `menu_templates`
--
ALTER TABLE `menu_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_templates_tenant_id_foreign` (`tenant_id`);

--
-- Indexes for table `menu_variations`
--
ALTER TABLE `menu_variations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_variations_menu_item_id_foreign` (`menu_item_id`);

--
-- Indexes for table `menu_versions`
--
ALTER TABLE `menu_versions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_versions_restaurant_id_foreign` (`restaurant_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_type_read` (`user_id`,`notification_type`,`is_read`),
  ADD KEY `idx_created_read` (`created_at`,`is_read`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_number_unique` (`order_number`),
  ADD KEY `orders_delivery_address_id_foreign` (`delivery_address_id`),
  ADD KEY `idx_customer_status` (`customer_id`,`status`),
  ADD KEY `idx_restaurant_status` (`restaurant_id`,`status`),
  ADD KEY `idx_order_number` (`order_number`),
  ADD KEY `idx_created_status` (`created_at`,`status`),
  ADD KEY `idx_otp_verification` (`pickup_otp`,`delivery_otp`),
  ADD KEY `idx_tenant_restaurant` (`tenant_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_item_id_foreign` (`item_id`),
  ADD KEY `idx_order_item` (`order_id`,`item_id`),
  ADD KEY `idx_tenant_restaurant` (`tenant_id`);

--
-- Indexes for table `order_item_customizations`
--
ALTER TABLE `order_item_customizations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_item_customizations_variation_id_foreign` (`variation_id`),
  ADD KEY `order_item_customizations_option_id_foreign` (`option_id`),
  ADD KEY `idx_order_item_variation` (`order_item_id`,`variation_id`),
  ADD KEY `idx_tenant_restaurant` (`tenant_id`);

--
-- Indexes for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `otp_verifications_verified_by_foreign` (`verified_by`),
  ADD KEY `idx_order_type` (`order_id`,`otp_type`),
  ADD KEY `idx_verification_status` (`is_verified`,`expires_at`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_status` (`order_id`,`status`),
  ADD KEY `idx_gateway_txn` (`gateway_transaction_id`),
  ADD KEY `idx_status_date` (`status`,`completed_at`),
  ADD KEY `idx_tenant_restaurant` (`tenant_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `promotions_promotion_code_unique` (`promotion_code`),
  ADD KEY `promotions_restaurant_id_foreign` (`restaurant_id`),
  ADD KEY `promotions_created_by_foreign` (`created_by`),
  ADD KEY `idx_code_active` (`promotion_code`,`is_active`),
  ADD KEY `idx_tenant_restaurant` (`tenant_id`,`restaurant_id`),
  ADD KEY `idx_validity` (`valid_from`,`valid_until`);

--
-- Indexes for table `pulse_aggregates`
--
ALTER TABLE `pulse_aggregates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pulse_aggregates_bucket_period_type_aggregate_key_hash_unique` (`bucket`,`period`,`type`,`aggregate`,`key_hash`),
  ADD KEY `pulse_aggregates_period_bucket_index` (`period`,`bucket`),
  ADD KEY `pulse_aggregates_type_index` (`type`),
  ADD KEY `pulse_aggregates_period_type_aggregate_bucket_index` (`period`,`type`,`aggregate`,`bucket`);

--
-- Indexes for table `pulse_entries`
--
ALTER TABLE `pulse_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pulse_entries_timestamp_index` (`timestamp`),
  ADD KEY `pulse_entries_type_index` (`type`),
  ADD KEY `pulse_entries_key_hash_index` (`key_hash`),
  ADD KEY `pulse_entries_timestamp_type_key_hash_value_index` (`timestamp`,`type`,`key_hash`,`value`);

--
-- Indexes for table `pulse_values`
--
ALTER TABLE `pulse_values`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pulse_values_type_key_hash_unique` (`type`,`key_hash`),
  ADD KEY `pulse_values_timestamp_index` (`timestamp`),
  ADD KEY `pulse_values_type_index` (`type`);

--
-- Indexes for table `referrals`
--
ALTER TABLE `referrals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `referrals_referred_id_foreign` (`referred_id`),
  ADD KEY `referrals_first_order_id_foreign` (`first_order_id`),
  ADD KEY `idx_referrer_status` (`referrer_id`,`referrer_reward_status`),
  ADD KEY `idx_referral_code` (`referral_code`);

--
-- Indexes for table `refunds`
--
ALTER TABLE `refunds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `refunds_initiated_by_foreign` (`initiated_by`),
  ADD KEY `idx_payment_status` (`payment_id`,`status`),
  ADD KEY `idx_order_reason` (`order_id`,`refund_reason`),
  ADD KEY `idx_tenant_restaurant` (`tenant_id`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `restaurants_slug_unique` (`slug`),
  ADD KEY `restaurants_location_admin_id_foreign` (`location_admin_id`),
  ADD KEY `restaurants_approved_by_foreign` (`approved_by`),
  ADD KEY `idx_tenant_status` (`tenant_id`,`status`),
  ADD KEY `idx_location_status` (`latitude`,`longitude`,`status`),
  ADD KEY `idx_city_status` (`city`,`status`),
  ADD KEY `idx_user_restaurant` (`user_id`),
  ADD KEY `restaurants_state_foreign` (`state`);

--
-- Indexes for table `restaurant_banners`
--
ALTER TABLE `restaurant_banners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_restaurant_position` (`restaurant_id`,`banner_position`),
  ADD KEY `idx_tenant_active` (`tenant_id`,`is_active`),
  ADD KEY `idx_active_validity` (`is_active`,`valid_from`,`valid_until`);

--
-- Indexes for table `restaurant_documents`
--
ALTER TABLE `restaurant_documents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_restaurant_document_type` (`restaurant_id`,`document_type`),
  ADD KEY `restaurant_documents_reviewed_by_foreign` (`reviewed_by`),
  ADD KEY `idx_restaurant_type` (`restaurant_id`,`document_type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_tenant_restaurant` (`tenant_id`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- Indexes for table `restaurant_menu_items`
--
ALTER TABLE `restaurant_menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `restaurant_menu_items_restaurant_id_foreign` (`restaurant_id`),
  ADD KEY `restaurant_menu_items_parent_menu_item_id_foreign` (`parent_menu_item_id`);

--
-- Indexes for table `restaurant_staff`
--
ALTER TABLE `restaurant_staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_restaurant_user` (`restaurant_id`,`user_id`),
  ADD KEY `restaurant_staff_user_id_foreign` (`user_id`),
  ADD KEY `idx_restaurant_role` (`restaurant_id`,`role`,`is_active`),
  ADD KEY `idx_tenant_restaurant` (`tenant_id`);

--
-- Indexes for table `restaurant_working_hours`
--
ALTER TABLE `restaurant_working_hours`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_restaurant_day` (`restaurant_id`,`day_of_week`),
  ADD KEY `idx_tenant_restaurant` (`tenant_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviews_order_id_foreign` (`order_id`),
  ADD KEY `reviews_admin_responded_by_foreign` (`admin_responded_by`),
  ADD KEY `idx_reviewable` (`reviewable_type`,`reviewable_id`),
  ADD KEY `idx_rating_featured` (`rating`,`is_featured`),
  ADD KEY `idx_customer_reviews` (`customer_id`,`created_at`),
  ADD KEY `idx_tenant_restaurant` (`tenant_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`id`),
  ADD KEY `states_country_id_foreign` (`country_id`);

--
-- Indexes for table `subscription_payments`
--
ALTER TABLE `subscription_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tenant_restaurant` (`tenant_id`),
  ADD KEY `idx_due_date` (`due_date`,`status`),
  ADD KEY `idx_billing_period` (`billing_period_start`,`billing_period_end`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `system_settings_setting_key_unique` (`setting_key`),
  ADD KEY `system_settings_updated_by_foreign` (`updated_by`),
  ADD KEY `idx_key_public` (`setting_key`,`is_public`);

--
-- Indexes for table `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tenants_email_unique` (`email`);

--
-- Indexes for table `transaction_logs`
--
ALTER TABLE `transaction_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_logs_order_id_foreign` (`order_id`),
  ADD KEY `transaction_logs_payout_id_foreign` (`payout_id`),
  ADD KEY `idx_user_type_date` (`user_id`,`user_type`,`created_at`),
  ADD KEY `idx_transaction_type` (`transaction_type`,`created_at`),
  ADD KEY `idx_tenant_restaurant` (`tenant_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_phone_unique` (`phone`),
  ADD KEY `idx_role_status` (`role`,`status`),
  ADD KEY `idx_tenant_role` (`tenant_id`,`role`);

--
-- Indexes for table `variation_options`
--
ALTER TABLE `variation_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_variation_sort` (`variation_id`,`sort_order`),
  ADD KEY `idx_tenant_restaurant` (`tenant_id`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_wallet` (`user_id`,`user_type`),
  ADD KEY `idx_user_type` (`user_id`,`user_type`);

--
-- Indexes for table `wallet_split_transactions`
--
ALTER TABLE `wallet_split_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wallet_split_transactions_restaurant_wallet_id_foreign` (`restaurant_wallet_id`),
  ADD KEY `wallet_split_transactions_delivery_wallet_id_foreign` (`delivery_wallet_id`),
  ADD KEY `idx_order_release` (`order_id`,`restaurant_released`,`delivery_released`);

--
-- Indexes for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wallet_transactions_reference_number_unique` (`reference_number`),
  ADD KEY `idx_wallet_type` (`wallet_id`,`transaction_type`),
  ADD KEY `idx_reference` (`reference_number`),
  ADD KEY `idx_purpose_status` (`purpose`,`status`),
  ADD KEY `idx_processed_date` (`processed_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `automated_payout_requests`
--
ALTER TABLE `automated_payout_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `customer_addresses`
--
ALTER TABLE `customer_addresses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `customer_favorite_items`
--
ALTER TABLE `customer_favorite_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_profiles`
--
ALTER TABLE `customer_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `delivery_assignments`
--
ALTER TABLE `delivery_assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_partners`
--
ALTER TABLE `delivery_partners`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_partner_documents`
--
ALTER TABLE `delivery_partner_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_tracking`
--
ALTER TABLE `delivery_tracking`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_zones`
--
ALTER TABLE `delivery_zones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_items`
--
ALTER TABLE `inventory_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_compatibility`
--
ALTER TABLE `item_compatibility`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_disputes`
--
ALTER TABLE `item_disputes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_categories`
--
ALTER TABLE `menu_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `menu_item_reviews`
--
ALTER TABLE `menu_item_reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_item_variations`
--
ALTER TABLE `menu_item_variations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_templates`
--
ALTER TABLE `menu_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `menu_variations`
--
ALTER TABLE `menu_variations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_versions`
--
ALTER TABLE `menu_versions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_item_customizations`
--
ALTER TABLE `order_item_customizations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pulse_aggregates`
--
ALTER TABLE `pulse_aggregates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2994;

--
-- AUTO_INCREMENT for table `pulse_entries`
--
ALTER TABLE `pulse_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=893;

--
-- AUTO_INCREMENT for table `pulse_values`
--
ALTER TABLE `pulse_values`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `referrals`
--
ALTER TABLE `referrals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `refunds`
--
ALTER TABLE `refunds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `restaurant_banners`
--
ALTER TABLE `restaurant_banners`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `restaurant_documents`
--
ALTER TABLE `restaurant_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `restaurant_menu_items`
--
ALTER TABLE `restaurant_menu_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `restaurant_staff`
--
ALTER TABLE `restaurant_staff`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `restaurant_working_hours`
--
ALTER TABLE `restaurant_working_hours`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `subscription_payments`
--
ALTER TABLE `subscription_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `transaction_logs`
--
ALTER TABLE `transaction_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `variation_options`
--
ALTER TABLE `variation_options`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wallet_split_transactions`
--
ALTER TABLE `wallet_split_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `automated_payout_requests`
--
ALTER TABLE `automated_payout_requests`
  ADD CONSTRAINT `automated_payout_requests_bank_account_id_foreign` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `automated_payout_requests_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `automated_payout_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD CONSTRAINT `bank_accounts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `cities_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_addresses`
--
ALTER TABLE `customer_addresses`
  ADD CONSTRAINT `customer_addresses_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customer_profiles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_favorite_items`
--
ALTER TABLE `customer_favorite_items`
  ADD CONSTRAINT `customer_favorite_items_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customer_profiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_favorite_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_favorite_items_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_favorite_items_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_profiles`
--
ALTER TABLE `customer_profiles`
  ADD CONSTRAINT `customer_profiles_referred_by_foreign` FOREIGN KEY (`referred_by`) REFERENCES `customer_profiles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `customer_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `delivery_assignments`
--
ALTER TABLE `delivery_assignments`
  ADD CONSTRAINT `delivery_assignments_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `delivery_assignments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `delivery_assignments_partner_id_foreign` FOREIGN KEY (`partner_id`) REFERENCES `delivery_partners` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `delivery_assignments_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `delivery_partners`
--
ALTER TABLE `delivery_partners`
  ADD CONSTRAINT `delivery_partners_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `delivery_partner_documents`
--
ALTER TABLE `delivery_partner_documents`
  ADD CONSTRAINT `delivery_partner_documents_partner_id_foreign` FOREIGN KEY (`partner_id`) REFERENCES `delivery_partners` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `delivery_tracking`
--
ALTER TABLE `delivery_tracking`
  ADD CONSTRAINT `delivery_tracking_assignment_id_foreign` FOREIGN KEY (`assignment_id`) REFERENCES `delivery_assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `delivery_tracking_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `delivery_zones`
--
ALTER TABLE `delivery_zones`
  ADD CONSTRAINT `delivery_zones_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `delivery_zones_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD CONSTRAINT `inventory_items_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `item_compatibility`
--
ALTER TABLE `item_compatibility`
  ADD CONSTRAINT `item_compatibility_compatible_item_id_foreign` FOREIGN KEY (`compatible_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_compatibility_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `item_disputes`
--
ALTER TABLE `item_disputes`
  ADD CONSTRAINT `item_disputes_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `item_disputes_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_disputes_raised_by_foreign` FOREIGN KEY (`raised_by`) REFERENCES `customer_profiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_disputes_resolved_by_foreign` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `item_disputes_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `menu_categories`
--
ALTER TABLE `menu_categories`
  ADD CONSTRAINT `menu_categories_menu_template_id_foreign` FOREIGN KEY (`menu_template_id`) REFERENCES `menu_templates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `menu_categories_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `menu_categories_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_category_id_foreign` FOREIGN KEY (`menu_category_id`) REFERENCES `menu_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `menu_items_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `menu_items_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `menu_item_reviews`
--
ALTER TABLE `menu_item_reviews`
  ADD CONSTRAINT `menu_item_reviews_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customer_profiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `menu_item_reviews_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `menu_item_reviews_order_item_id_foreign` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `menu_item_variations`
--
ALTER TABLE `menu_item_variations`
  ADD CONSTRAINT `menu_item_variations_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `menu_item_variations_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `menu_templates`
--
ALTER TABLE `menu_templates`
  ADD CONSTRAINT `menu_templates_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `menu_variations`
--
ALTER TABLE `menu_variations`
  ADD CONSTRAINT `menu_variations_menu_item_id_foreign` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `menu_versions`
--
ALTER TABLE `menu_versions`
  ADD CONSTRAINT `menu_versions_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customer_profiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_delivery_address_id_foreign` FOREIGN KEY (`delivery_address_id`) REFERENCES `customer_addresses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_item_customizations`
--
ALTER TABLE `order_item_customizations`
  ADD CONSTRAINT `order_item_customizations_option_id_foreign` FOREIGN KEY (`option_id`) REFERENCES `variation_options` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_item_customizations_order_item_id_foreign` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_item_customizations_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_item_customizations_variation_id_foreign` FOREIGN KEY (`variation_id`) REFERENCES `menu_item_variations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  ADD CONSTRAINT `otp_verifications_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `otp_verifications_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `promotions`
--
ALTER TABLE `promotions`
  ADD CONSTRAINT `promotions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `promotions_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `promotions_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `referrals`
--
ALTER TABLE `referrals`
  ADD CONSTRAINT `referrals_first_order_id_foreign` FOREIGN KEY (`first_order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `referrals_referred_id_foreign` FOREIGN KEY (`referred_id`) REFERENCES `customer_profiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `referrals_referrer_id_foreign` FOREIGN KEY (`referrer_id`) REFERENCES `customer_profiles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `refunds`
--
ALTER TABLE `refunds`
  ADD CONSTRAINT `refunds_initiated_by_foreign` FOREIGN KEY (`initiated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `refunds_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `refunds_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `refunds_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD CONSTRAINT `restaurants_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `restaurants_city_foreign` FOREIGN KEY (`city`) REFERENCES `cities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `restaurants_location_admin_id_foreign` FOREIGN KEY (`location_admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `restaurants_state_foreign` FOREIGN KEY (`state`) REFERENCES `states` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `restaurants_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `restaurants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `restaurant_banners`
--
ALTER TABLE `restaurant_banners`
  ADD CONSTRAINT `restaurant_banners_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `restaurant_banners_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `restaurant_documents`
--
ALTER TABLE `restaurant_documents`
  ADD CONSTRAINT `restaurant_documents_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `restaurant_documents_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `restaurant_documents_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `restaurant_menu_items`
--
ALTER TABLE `restaurant_menu_items`
  ADD CONSTRAINT `restaurant_menu_items_parent_menu_item_id_foreign` FOREIGN KEY (`parent_menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `restaurant_menu_items_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `restaurant_staff`
--
ALTER TABLE `restaurant_staff`
  ADD CONSTRAINT `restaurant_staff_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `restaurant_staff_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `restaurant_staff_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `restaurant_working_hours`
--
ALTER TABLE `restaurant_working_hours`
  ADD CONSTRAINT `restaurant_working_hours_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `restaurant_working_hours_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_admin_responded_by_foreign` FOREIGN KEY (`admin_responded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reviews_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customer_profiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `states`
--
ALTER TABLE `states`
  ADD CONSTRAINT `states_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subscription_payments`
--
ALTER TABLE `subscription_payments`
  ADD CONSTRAINT `subscription_payments_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD CONSTRAINT `system_settings_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `transaction_logs`
--
ALTER TABLE `transaction_logs`
  ADD CONSTRAINT `transaction_logs_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transaction_logs_payout_id_foreign` FOREIGN KEY (`payout_id`) REFERENCES `automated_payout_requests` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transaction_logs_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `variation_options`
--
ALTER TABLE `variation_options`
  ADD CONSTRAINT `variation_options_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `variation_options_variation_id_foreign` FOREIGN KEY (`variation_id`) REFERENCES `menu_item_variations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `wallets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wallet_split_transactions`
--
ALTER TABLE `wallet_split_transactions`
  ADD CONSTRAINT `wallet_split_transactions_delivery_wallet_id_foreign` FOREIGN KEY (`delivery_wallet_id`) REFERENCES `wallets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wallet_split_transactions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wallet_split_transactions_restaurant_wallet_id_foreign` FOREIGN KEY (`restaurant_wallet_id`) REFERENCES `wallets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD CONSTRAINT `wallet_transactions_wallet_id_foreign` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
