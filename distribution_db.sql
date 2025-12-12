-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 12, 2025 at 06:25 PM
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
-- Database: `distribution_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `assistants`
--

CREATE TABLE `assistants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `total_deliveries` int(11) NOT NULL DEFAULT 0,
  `average_rating` decimal(3,2) NOT NULL DEFAULT 5.00,
  `address` text DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assistants`
--

INSERT INTO `assistants` (`id`, `name`, `phone`, `status`, `total_deliveries`, `average_rating`, `address`, `hire_date`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'Shammi Nethupul', '0764839777', 'active', 0, 5.00, '552/05, Pitipana South, Homagama.', NULL, NULL, '2025-12-11 00:52:03', '2025-12-11 00:52:03');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `nic` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `customer_type` varchar(255) NOT NULL DEFAULT 'retail',
  `credit_limit` decimal(10,2) NOT NULL DEFAULT 0.00,
  `outstanding_balance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `full_cylinders_issued` int(11) NOT NULL DEFAULT 0,
  `empty_cylinders_returned` int(11) NOT NULL DEFAULT 0,
  `status` enum('Active','Inactive','Suspended') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `email`, `address`, `nic`, `city`, `customer_type`, `credit_limit`, `outstanding_balance`, `full_cylinders_issued`, `empty_cylinders_returned`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Shammi Nethupul', '0764839777', 'nethupulmax143@gmail.com', '552/05, Pitipana South, Homagama.', '200145212222', 'Pitipana', 'Dealer', 1500000.00, 210000.00, 50, 0, 'Active', '2025-12-11 00:51:03', '2025-12-12 11:13:28');

-- --------------------------------------------------------

--
-- Table structure for table `customer_cylinders`
--

CREATE TABLE `customer_cylinders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `gas_type_id` bigint(20) UNSIGNED NOT NULL,
  `transaction_type` enum('Issued','Returned') NOT NULL,
  `quantity` int(11) NOT NULL,
  `transaction_date` date NOT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_cylinders`
--

INSERT INTO `customer_cylinders` (`id`, `customer_id`, `gas_type_id`, `transaction_type`, `quantity`, `transaction_date`, `reference`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'Issued', 50, '2025-12-11', 'ORD-1', 'Order #ORD-000001', '2025-12-11 01:31:50', '2025-12-11 01:31:50');

-- --------------------------------------------------------

--
-- Table structure for table `customer_pricing_tiers`
--

CREATE TABLE `customer_pricing_tiers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_type` enum('Dealer','Commercial','Individual') NOT NULL,
  `gas_type_id` bigint(20) UNSIGNED NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_routes`
--

CREATE TABLE `delivery_routes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `route_name` varchar(255) NOT NULL,
  `route_date` date NOT NULL,
  `driver_id` bigint(20) UNSIGNED DEFAULT NULL,
  `route_status` enum('Planned','InProgress','Completed','Cancelled') NOT NULL DEFAULT 'Planned',
  `actual_start_time` timestamp NULL DEFAULT NULL,
  `actual_end_time` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `assistant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `vehicle_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `delivery_routes`
--

INSERT INTO `delivery_routes` (`id`, `route_name`, `route_date`, `driver_id`, `route_status`, `actual_start_time`, `actual_end_time`, `notes`, `assistant_id`, `vehicle_id`, `created_at`, `updated_at`) VALUES
(1, 'Kandy', '2025-12-11', 1, 'InProgress', '2025-12-11 02:44:03', NULL, 'abc', 1, 1, '2025-12-11 02:41:16', '2025-12-11 02:44:03'),
(2, 'Kandy', '2025-12-12', 1, 'Planned', NULL, NULL, NULL, 1, 1, '2025-12-12 11:25:11', '2025-12-12 11:25:11');

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `license_number` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `total_deliveries` int(11) NOT NULL DEFAULT 0,
  `on_time_deliveries` int(11) NOT NULL DEFAULT 0,
  `average_rating` decimal(3,2) NOT NULL DEFAULT 5.00,
  `address` text DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`id`, `name`, `phone`, `license_number`, `status`, `total_deliveries`, `on_time_deliveries`, `average_rating`, `address`, `hire_date`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'Shammi Nethupul', '0764839777', '23323333333333333', 'active', 0, 0, 5.00, 'No. 365, Lihiniyagama, Damanewela', NULL, NULL, '2025-12-11 00:52:25', '2025-12-11 00:52:25');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gas_types`
--

CREATE TABLE `gas_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gas_types`
--

INSERT INTO `gas_types` (`id`, `name`, `price`, `created_at`, `updated_at`) VALUES
(2, '12.5Kg', 5800.00, '2025-12-09 11:57:29', '2025-12-09 11:57:29');

-- --------------------------------------------------------

--
-- Table structure for table `gas_type_customer_price`
--

CREATE TABLE `gas_type_customer_price` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `gas_type_id` bigint(20) UNSIGNED NOT NULL,
  `custom_price` decimal(10,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gas_type_supplier`
--

CREATE TABLE `gas_type_supplier` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `gas_type_id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gas_type_supplier`
--

INSERT INTO `gas_type_supplier` (`id`, `gas_type_id`, `supplier_id`, `rate`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 5000.00, '2025-12-09 12:44:43', '2025-12-09 12:44:43');

-- --------------------------------------------------------

--
-- Table structure for table `grns`
--

CREATE TABLE `grns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `grn_number` varchar(255) NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_order_id` bigint(20) UNSIGNED NOT NULL,
  `received_date` date NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT 0,
  `approved_at` timestamp NULL DEFAULT NULL,
  `variance_notes` text DEFAULT NULL,
  `rejection_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grns`
--

INSERT INTO `grns` (`id`, `grn_number`, `supplier_id`, `purchase_order_id`, `received_date`, `status`, `created_at`, `updated_at`, `approved`, `approved_at`, `variance_notes`, `rejection_notes`) VALUES
(1, 'GRN-00001', 1, 1, '2025-12-11', 'Approved', '2025-12-10 04:46:50', '2025-12-11 22:20:10', 1, '2025-12-11 22:20:10', NULL, NULL),
(2, 'GRN-00002', 1, 2, '2025-12-10', 'Approved', '2025-12-10 04:59:08', '2025-12-11 00:50:17', 1, '2025-12-11 00:50:17', NULL, NULL),
(3, 'GRN-00003', 1, 3, '2025-12-12', 'Pending', '2025-12-12 11:11:53', '2025-12-12 11:11:53', 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `grn_items`
--

CREATE TABLE `grn_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `grn_id` bigint(20) UNSIGNED NOT NULL,
  `gas_type_id` bigint(20) UNSIGNED NOT NULL,
  `ordered_qty` int(11) NOT NULL,
  `received_qty` int(11) NOT NULL,
  `damaged_qty` int(11) NOT NULL DEFAULT 0,
  `rejected_qty` int(11) NOT NULL DEFAULT 0,
  `rejection_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grn_items`
--

INSERT INTO `grn_items` (`id`, `grn_id`, `gas_type_id`, `ordered_qty`, `received_qty`, `damaged_qty`, `rejected_qty`, `rejection_notes`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 300, 300, 0, 0, NULL, '2025-12-10 04:46:50', '2025-12-10 04:46:50'),
(2, 2, 2, 100, 100, 0, 0, NULL, '2025-12-10 04:59:08', '2025-12-10 04:59:08'),
(3, 3, 2, 100, 100, 5, 0, NULL, '2025-12-12 11:11:53', '2025-12-12 11:11:53');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
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
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
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
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_12_09_143242_create_suppliers_table', 2),
(5, '2025_12_09_170716_create_gas_types_table', 3),
(6, '2025_12_09_173303_create_purchase_orders_table', 4),
(7, '2025_12_09_173304_create_purchase_order_items_table', 4),
(8, '2025_12_09_180719_create_gas_type_supplier_table', 5),
(9, '2025_12_09_224640_create_grns_table', 6),
(10, '2025_12_09_224641_create_grn_items_table', 6),
(11, '2025_12_10_100045_add_rejected_qty_to_grn_items', 7),
(12, '2025_12_10_100326_add_approval_columns_to_grns', 8),
(13, '2025_12_10_101046_create_stocks_table', 9),
(14, '2025_12_10_111239_create_customers_table', 10),
(15, '2025_12_10_163349_create_drivers_table', 11),
(16, '2025_12_10_163350_create_assistants_table', 11),
(17, '2025_12_10_163422_create_vehicles_table', 11),
(18, '2025_12_11_043108_create_delivery_routes_table', 11),
(19, '2025_12_10_160431_create_orders_table', 12),
(20, '2025_12_15_000001_update_purchase_orders_table', 13),
(21, '2025_12_16_000001_create_supplier_payments_table', 14),
(22, '2025_12_11_add_grn_enhancements', 15),
(23, '2025_12_11_create_refill_tracking', 16),
(24, '2025_12_12_enhance_customers', 17),
(25, '2025_12_12_enhance_orders', 18),
(26, '2025_12_12_enhance_delivery_routes', 19),
(27, '2025_12_12_enhance_personnel_and_vehicles', 20),
(28, '2025_12_11_add_type_to_vehicles', 21),
(29, '2025_12_11_update_vehicles_status_enum', 22),
(30, '2025_12_11_071100_add_route_name_to_delivery_routes_table', 23),
(31, '2025_12_11_071200_update_delivery_routes_table_add_route_date_and_remove_unused', 24);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(255) NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `delivery_route_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `loaded_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `order_date` date NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `is_urgent` tinyint(1) NOT NULL DEFAULT 0,
  `urgent` tinyint(1) NOT NULL DEFAULT 0,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `customer_id`, `delivery_route_id`, `order_total`, `notes`, `loaded_at`, `delivered_at`, `completed_at`, `order_date`, `status`, `is_urgent`, `urgent`, `total_amount`, `created_at`, `updated_at`) VALUES
(1, 'ORD-000001', 1, NULL, 210000.00, NULL, '2025-12-11 02:43:23', NULL, NULL, '2025-12-11', 'Loaded', 0, 0, 0.00, '2025-12-11 01:31:50', '2025-12-11 02:43:23');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `gas_type_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `line_total` decimal(12,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `gas_type_id`, `quantity`, `unit_price`, `line_total`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 50, 4200.00, 210000.00, NULL, '2025-12-11 01:31:50', '2025-12-11 01:31:50');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `po_number` varchar(255) NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `order_date` date NOT NULL,
  `delivery_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `received_count` int(11) NOT NULL DEFAULT 0,
  `refilled_count` int(11) NOT NULL DEFAULT 0,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `po_number`, `supplier_id`, `order_date`, `delivery_date`, `notes`, `status`, `received_count`, `refilled_count`, `total_amount`, `created_at`, `updated_at`) VALUES
(1, 'PO-00001', 1, '2025-12-10', NULL, NULL, 'Completed', 0, 0, 1500000.00, '2025-12-09 12:09:50', '2025-12-11 22:20:10'),
(2, 'PO-00002', 1, '2025-12-10', NULL, NULL, 'Completed', 0, 0, 500000.00, '2025-12-09 13:25:03', '2025-12-11 00:50:17'),
(3, 'PO-000003', 1, '2025-12-12', '2025-12-19', NULL, 'Pending', 0, 0, 500000.00, '2025-12-12 11:05:48', '2025-12-12 11:05:48');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_order_id` bigint(20) UNSIGNED NOT NULL,
  `gas_type_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_order_items`
--

INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `gas_type_id`, `quantity`, `unit_price`, `total`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 300, 5000.00, 1500000.00, '2025-12-09 12:09:50', '2025-12-09 12:09:50'),
(2, 2, 2, 100, 5000.00, 500000.00, '2025-12-09 13:25:03', '2025-12-09 13:25:03'),
(3, 3, 2, 100, 5000.00, 500000.00, '2025-12-12 11:05:48', '2025-12-12 11:05:48');

-- --------------------------------------------------------

--
-- Table structure for table `refills`
--

CREATE TABLE `refills` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `refill_ref` varchar(255) NOT NULL,
  `gas_type_id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `cylinders_refilled` int(11) NOT NULL,
  `refill_date` date NOT NULL,
  `cost_per_cylinder` decimal(10,2) NOT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `route_stops`
--

CREATE TABLE `route_stops` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `delivery_route_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `stop_order` int(11) NOT NULL COMMENT 'Order within route: 1, 2, 3...',
  `planned_time` time DEFAULT NULL COMMENT 'Planned delivery time for this stop',
  `actual_time` timestamp NULL DEFAULT NULL COMMENT 'Actual delivery time when driver confirmed',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `route_stops`
--

INSERT INTO `route_stops` (`id`, `delivery_route_id`, `customer_id`, `order_id`, `stop_order`, `planned_time`, `actual_time`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, '14:45:00', NULL, NULL, '2025-12-11 02:41:16', '2025-12-11 02:41:16'),
(2, 2, 1, NULL, 1, NULL, NULL, NULL, '2025-12-12 11:25:11', '2025-12-12 11:25:11');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('uUDL5dHPB48GYroS5P7kGiesCULQos63ak58fb18', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQnl4U21IYzlTVFZWWUowM0FjOWpJZGNqckI1bEJnWWV1YlNjSm1iTyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9vcmRlcnMiO3M6NToicm91dGUiO3M6MTI6Im9yZGVycy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1765558550);

-- --------------------------------------------------------

--
-- Table structure for table `stocks`
--

CREATE TABLE `stocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `gas_type_id` bigint(20) UNSIGNED NOT NULL,
  `full_qty` int(11) NOT NULL DEFAULT 0,
  `empty_qty` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stocks`
--

INSERT INTO `stocks` (`id`, `gas_type_id`, `full_qty`, `empty_qty`, `created_at`, `updated_at`) VALUES
(1, 2, 800, 0, '2025-12-10 05:31:00', '2025-12-11 22:20:10');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `address`, `phone`, `email`, `created_at`, `updated_at`) VALUES
(1, 'Shammi Nethupul', '552/05, Pitipana South, Homagama.', '0764839777', 'nethupulmax143@gmail.com', '2025-12-09 09:18:27', '2025-12-09 09:18:27'),
(2, 'kamal hasan', 'No. 365, Lihiniyagama, Damanewela', '0764839777', 'shamminethupul.class@gmail.com', '2025-12-09 09:22:48', '2025-12-09 09:26:44'),
(3, 'kumara senanayaka', '552/05, Pitipana South, Homagama.', '0764839777', 'nethupulmax14@gmail.com', '2025-12-09 11:27:53', '2025-12-09 11:27:53');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_invoices`
--

CREATE TABLE `supplier_invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `invoice_date` date NOT NULL,
  `invoice_amount` decimal(12,2) NOT NULL,
  `status` enum('Pending','Reconciled','Disputed') NOT NULL DEFAULT 'Pending',
  `description` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supplier_payments`
--

CREATE TABLE `supplier_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_ref` varchar(255) NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_order_id` bigint(20) UNSIGNED NOT NULL,
  `po_amount` decimal(10,2) NOT NULL,
  `payment_amount` decimal(10,2) NOT NULL,
  `payment_mode` enum('Cheque','Bank Transfer','Cash','Online') NOT NULL DEFAULT 'Cheque',
  `cheque_number` varchar(255) DEFAULT NULL,
  `cheque_date` date DEFAULT NULL,
  `payment_date` date NOT NULL,
  `status` enum('Pending','Cleared','Bounced') NOT NULL DEFAULT 'Pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supplier_payments`
--

INSERT INTO `supplier_payments` (`id`, `payment_ref`, `supplier_id`, `purchase_order_id`, `po_amount`, `payment_amount`, `payment_mode`, `cheque_number`, `cheque_date`, `payment_date`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'PAY-20251211-0001', 1, 2, 500000.00, 500000.00, 'Cheque', '1234', '2025-12-11', '2025-12-11', 'Cleared', NULL, '2025-12-11 09:50:05', '2025-12-11 09:50:14'),
(2, 'PAY-20251212-0002', 1, 1, 1500000.00, 1500000.00, 'Cheque', '1234', '2025-12-12', '2025-12-12', 'Pending', NULL, '2025-12-12 11:24:25', '2025-12-12 11:24:25');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(100) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@gas.com', NULL, '$2y$12$2/00zPhPr25YLAIA6Y0Gte/JHtdhuRUetg5T7z5LGyUxdKAZyb32S', 'admin', NULL, '2025-12-09 06:40:09', '2025-12-09 06:40:09');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_number` varchar(255) NOT NULL,
  `model` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL COMMENT 'Vehicle type: Truck, Van, Bike, etc.',
  `capacity` int(11) NOT NULL DEFAULT 0 COMMENT 'capacity in cylinders',
  `status` enum('active','inactive','maintenance') NOT NULL DEFAULT 'active',
  `total_deliveries` int(11) NOT NULL DEFAULT 0,
  `total_km` int(11) NOT NULL DEFAULT 0,
  `fuel_consumption` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'km per liter',
  `last_maintenance_date` date DEFAULT NULL,
  `next_maintenance_due` date DEFAULT NULL,
  `registration_expiry` date DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `vehicle_number`, `model`, `type`, `capacity`, `status`, `total_deliveries`, `total_km`, `fuel_consumption`, `last_maintenance_date`, `next_maintenance_due`, `registration_expiry`, `purchase_date`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'LF-4576', NULL, 'Truck', 200, 'active', 0, 0, 0.00, NULL, NULL, NULL, NULL, NULL, '2025-12-11 01:03:23', '2025-12-11 01:03:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assistants`
--
ALTER TABLE `assistants`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_cylinders`
--
ALTER TABLE `customer_cylinders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_cylinders_customer_id_index` (`customer_id`),
  ADD KEY `customer_cylinders_gas_type_id_index` (`gas_type_id`),
  ADD KEY `customer_cylinders_transaction_date_index` (`transaction_date`);

--
-- Indexes for table `customer_pricing_tiers`
--
ALTER TABLE `customer_pricing_tiers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_pricing_tiers_customer_type_gas_type_id_unique` (`customer_type`,`gas_type_id`),
  ADD KEY `customer_pricing_tiers_gas_type_id_foreign` (`gas_type_id`),
  ADD KEY `customer_pricing_tiers_customer_type_index` (`customer_type`);

--
-- Indexes for table `delivery_routes`
--
ALTER TABLE `delivery_routes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `delivery_routes_driver_id_foreign` (`driver_id`),
  ADD KEY `delivery_routes_assistant_id_foreign` (`assistant_id`),
  ADD KEY `delivery_routes_vehicle_id_foreign` (`vehicle_id`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `gas_types`
--
ALTER TABLE `gas_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gas_type_customer_price`
--
ALTER TABLE `gas_type_customer_price`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gas_type_customer_price_customer_id_gas_type_id_unique` (`customer_id`,`gas_type_id`),
  ADD KEY `gas_type_customer_price_customer_id_index` (`customer_id`),
  ADD KEY `gas_type_customer_price_gas_type_id_index` (`gas_type_id`);

--
-- Indexes for table `gas_type_supplier`
--
ALTER TABLE `gas_type_supplier`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gas_type_supplier_gas_type_id_supplier_id_unique` (`gas_type_id`,`supplier_id`),
  ADD KEY `gas_type_supplier_supplier_id_foreign` (`supplier_id`);

--
-- Indexes for table `grns`
--
ALTER TABLE `grns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `grns_grn_number_unique` (`grn_number`),
  ADD KEY `grns_supplier_id_foreign` (`supplier_id`),
  ADD KEY `grns_purchase_order_id_foreign` (`purchase_order_id`);

--
-- Indexes for table `grn_items`
--
ALTER TABLE `grn_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grn_items_grn_id_foreign` (`grn_id`),
  ADD KEY `grn_items_gas_type_id_foreign` (`gas_type_id`);

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
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_number_unique` (`order_number`),
  ADD KEY `orders_customer_id_foreign` (`customer_id`),
  ADD KEY `orders_delivery_route_id_foreign` (`delivery_route_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_gas_type_id_foreign` (`gas_type_id`),
  ADD KEY `order_items_order_id_gas_type_id_index` (`order_id`,`gas_type_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_orders_po_number_unique` (`po_number`),
  ADD KEY `purchase_orders_supplier_id_foreign` (`supplier_id`);

--
-- Indexes for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_items_purchase_order_id_foreign` (`purchase_order_id`),
  ADD KEY `purchase_order_items_gas_type_id_foreign` (`gas_type_id`);

--
-- Indexes for table `refills`
--
ALTER TABLE `refills`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `refills_refill_ref_unique` (`refill_ref`),
  ADD KEY `refills_supplier_id_index` (`supplier_id`),
  ADD KEY `refills_gas_type_id_index` (`gas_type_id`),
  ADD KEY `refills_refill_date_index` (`refill_date`);

--
-- Indexes for table `route_stops`
--
ALTER TABLE `route_stops`
  ADD PRIMARY KEY (`id`),
  ADD KEY `route_stops_delivery_route_id_index` (`delivery_route_id`),
  ADD KEY `route_stops_customer_id_index` (`customer_id`),
  ADD KEY `route_stops_order_id_index` (`order_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `stocks`
--
ALTER TABLE `stocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stocks_gas_type_id_foreign` (`gas_type_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supplier_invoices`
--
ALTER TABLE `supplier_invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `supplier_invoices_invoice_number_unique` (`invoice_number`),
  ADD KEY `supplier_invoices_supplier_id_index` (`supplier_id`),
  ADD KEY `supplier_invoices_purchase_order_id_index` (`purchase_order_id`),
  ADD KEY `supplier_invoices_invoice_date_index` (`invoice_date`);

--
-- Indexes for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `supplier_payments_payment_ref_unique` (`payment_ref`),
  ADD KEY `supplier_payments_supplier_id_foreign` (`supplier_id`),
  ADD KEY `supplier_payments_purchase_order_id_foreign` (`purchase_order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicles_vehicle_number_unique` (`vehicle_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assistants`
--
ALTER TABLE `assistants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customer_cylinders`
--
ALTER TABLE `customer_cylinders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customer_pricing_tiers`
--
ALTER TABLE `customer_pricing_tiers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_routes`
--
ALTER TABLE `delivery_routes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gas_types`
--
ALTER TABLE `gas_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `gas_type_customer_price`
--
ALTER TABLE `gas_type_customer_price`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gas_type_supplier`
--
ALTER TABLE `gas_type_supplier`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `grns`
--
ALTER TABLE `grns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `grn_items`
--
ALTER TABLE `grn_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `refills`
--
ALTER TABLE `refills`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `route_stops`
--
ALTER TABLE `route_stops`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `stocks`
--
ALTER TABLE `stocks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `supplier_invoices`
--
ALTER TABLE `supplier_invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customer_cylinders`
--
ALTER TABLE `customer_cylinders`
  ADD CONSTRAINT `customer_cylinders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_cylinders_gas_type_id_foreign` FOREIGN KEY (`gas_type_id`) REFERENCES `gas_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_pricing_tiers`
--
ALTER TABLE `customer_pricing_tiers`
  ADD CONSTRAINT `customer_pricing_tiers_gas_type_id_foreign` FOREIGN KEY (`gas_type_id`) REFERENCES `gas_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `delivery_routes`
--
ALTER TABLE `delivery_routes`
  ADD CONSTRAINT `delivery_routes_assistant_id_foreign` FOREIGN KEY (`assistant_id`) REFERENCES `assistants` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `delivery_routes_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `delivery_routes_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `gas_type_customer_price`
--
ALTER TABLE `gas_type_customer_price`
  ADD CONSTRAINT `gas_type_customer_price_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gas_type_customer_price_gas_type_id_foreign` FOREIGN KEY (`gas_type_id`) REFERENCES `gas_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gas_type_supplier`
--
ALTER TABLE `gas_type_supplier`
  ADD CONSTRAINT `gas_type_supplier_gas_type_id_foreign` FOREIGN KEY (`gas_type_id`) REFERENCES `gas_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gas_type_supplier_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `grns`
--
ALTER TABLE `grns`
  ADD CONSTRAINT `grns_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grns_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `grn_items`
--
ALTER TABLE `grn_items`
  ADD CONSTRAINT `grn_items_gas_type_id_foreign` FOREIGN KEY (`gas_type_id`) REFERENCES `gas_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grn_items_grn_id_foreign` FOREIGN KEY (`grn_id`) REFERENCES `grns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_delivery_route_id_foreign` FOREIGN KEY (`delivery_route_id`) REFERENCES `delivery_routes` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_gas_type_id_foreign` FOREIGN KEY (`gas_type_id`) REFERENCES `gas_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `purchase_order_items_gas_type_id_foreign` FOREIGN KEY (`gas_type_id`) REFERENCES `gas_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_order_items_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `refills`
--
ALTER TABLE `refills`
  ADD CONSTRAINT `refills_gas_type_id_foreign` FOREIGN KEY (`gas_type_id`) REFERENCES `gas_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `refills_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `route_stops`
--
ALTER TABLE `route_stops`
  ADD CONSTRAINT `route_stops_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `route_stops_delivery_route_id_foreign` FOREIGN KEY (`delivery_route_id`) REFERENCES `delivery_routes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `route_stops_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `stocks`
--
ALTER TABLE `stocks`
  ADD CONSTRAINT `stocks_gas_type_id_foreign` FOREIGN KEY (`gas_type_id`) REFERENCES `gas_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `supplier_invoices`
--
ALTER TABLE `supplier_invoices`
  ADD CONSTRAINT `supplier_invoices_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `supplier_invoices_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  ADD CONSTRAINT `supplier_payments_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `supplier_payments_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
