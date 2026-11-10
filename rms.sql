-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 08, 2026 at 03:14 PM
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
-- Database: `rmse_erp`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounting_cost_centers`
--

CREATE TABLE `accounting_cost_centers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `entity_id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(50) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'cost',
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `accounting_cost_center_translations`
--

CREATE TABLE `accounting_cost_center_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cost_center_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `accounting_lock_dates`
--

CREATE TABLE `accounting_lock_dates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `entity_id` bigint(20) UNSIGNED NOT NULL,
  `lock_all_date` date DEFAULT NULL,
  `tax_lock_date` date DEFAULT NULL,
  `fiscal_year_lock_date` date DEFAULT NULL,
  `hard_lock_date` date DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `accounting_lock_exceptions`
--

CREATE TABLE `accounting_lock_exceptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `entity_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `lock_type` varchar(30) NOT NULL,
  `valid_from` datetime NOT NULL,
  `valid_until` datetime NOT NULL,
  `reason` text NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `revoked_at` datetime DEFAULT NULL,
  `revoked_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `accounting_payment_matchings`
--

CREATE TABLE `accounting_payment_matchings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `entity_id` bigint(20) UNSIGNED NOT NULL,
  `debit_line_id` bigint(20) UNSIGNED NOT NULL,
  `credit_line_id` bigint(20) UNSIGNED NOT NULL,
  `matched_amount` decimal(15,4) NOT NULL,
  `notes` text DEFAULT NULL,
  `matched_by` bigint(20) UNSIGNED DEFAULT NULL,
  `matched_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `entity_id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(50) NOT NULL,
  `account_type` varchar(50) NOT NULL,
  `normal_balance` varchar(10) NOT NULL,
  `level` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_group` tinyint(1) NOT NULL DEFAULT 0,
  `allow_posting` tinyint(1) NOT NULL DEFAULT 1,
  `reconcile` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deprecated` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `entity_id`, `parent_id`, `code`, `account_type`, `normal_balance`, `level`, `is_group`, `allow_posting`, `reconcile`, `is_active`, `is_deprecated`, `sort_order`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, NULL, '1', 'asset', 'debit', 1, 1, 0, 0, 1, 0, 1, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(2, 1, 1, '11', 'asset', 'debit', 2, 1, 0, 0, 1, 0, 11, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(3, 1, 2, '1101', 'asset', 'debit', 3, 1, 0, 0, 1, 0, 1101, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(4, 1, 3, '110101', 'asset', 'debit', 4, 0, 1, 1, 1, 0, 110101, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(5, 1, 3, '110102', 'asset', 'debit', 4, 0, 1, 1, 1, 0, 110102, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(6, 1, 3, '110103', 'asset', 'debit', 4, 0, 1, 1, 1, 0, 110103, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(7, 1, 3, '110104', 'asset', 'debit', 4, 0, 1, 1, 1, 0, 110104, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(8, 1, 2, '1102', 'asset', 'debit', 3, 1, 0, 0, 1, 0, 1102, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(9, 1, 8, '110201', 'asset', 'debit', 4, 0, 1, 1, 1, 0, 110201, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(10, 1, 8, '110202', 'asset', 'debit', 4, 0, 1, 1, 1, 0, 110202, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(11, 1, 8, '110203', 'asset', 'debit', 4, 0, 1, 1, 1, 0, 110203, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(12, 1, 8, '110204', 'asset', 'debit', 4, 0, 1, 0, 1, 0, 110204, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(13, 1, 2, '1103', 'asset', 'debit', 3, 1, 0, 0, 1, 0, 1103, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(14, 1, 13, '110301', 'asset', 'debit', 4, 0, 1, 0, 1, 0, 110301, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(15, 1, 13, '110302', 'asset', 'debit', 4, 0, 1, 0, 1, 0, 110302, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(16, 1, 1, '12', 'asset', 'debit', 2, 1, 0, 0, 1, 0, 12, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(17, 1, 16, '1201', 'asset', 'debit', 3, 0, 1, 0, 1, 0, 1201, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(18, 1, 16, '1202', 'asset', 'debit', 3, 0, 1, 0, 1, 0, 1202, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(19, 1, 16, '1203', 'asset', 'debit', 3, 0, 1, 0, 1, 0, 1203, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(20, 1, 16, '1204', 'asset', 'debit', 3, 0, 1, 0, 1, 0, 1204, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(21, 1, 16, '1205', 'asset', 'debit', 3, 0, 1, 0, 1, 0, 1205, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(22, 1, NULL, '2', 'liability', 'credit', 1, 1, 0, 0, 1, 0, 2, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(23, 1, 22, '21', 'liability', 'credit', 2, 1, 0, 0, 1, 0, 21, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(24, 1, 23, '2101', 'liability', 'credit', 3, 0, 1, 1, 1, 0, 2101, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(25, 1, 23, '2102', 'liability', 'credit', 3, 0, 1, 1, 1, 0, 2102, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(26, 1, 23, '2103', 'liability', 'credit', 3, 0, 1, 0, 1, 0, 2103, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(27, 1, 23, '2104', 'liability', 'credit', 3, 0, 1, 0, 1, 0, 2104, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(28, 1, 23, '2105', 'liability', 'credit', 3, 0, 1, 0, 1, 0, 2105, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(29, 1, NULL, '3', 'equity', 'credit', 1, 1, 0, 0, 1, 0, 3, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(30, 1, 29, '31', 'equity', 'credit', 2, 0, 1, 0, 1, 0, 31, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(31, 1, 29, '32', 'equity', 'credit', 2, 0, 1, 0, 1, 0, 32, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(32, 1, 29, '33', 'equity', 'credit', 2, 0, 1, 0, 1, 0, 33, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(33, 1, NULL, '4', 'income', 'credit', 1, 1, 0, 0, 1, 0, 4, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(34, 1, 33, '41', 'income', 'credit', 2, 1, 0, 0, 1, 0, 41, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(35, 1, 34, '4101', 'income', 'credit', 3, 0, 1, 0, 1, 0, 4101, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(36, 1, 34, '4102', 'income', 'credit', 3, 0, 1, 0, 1, 0, 4102, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(37, 1, 34, '4103', 'income', 'credit', 3, 0, 1, 0, 1, 0, 4103, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(38, 1, 34, '4104', 'income', 'credit', 3, 0, 1, 0, 1, 0, 4104, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(39, 1, 33, '42', 'income', 'credit', 2, 1, 0, 0, 1, 0, 42, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(40, 1, 39, '4201', 'income', 'credit', 3, 0, 1, 0, 1, 0, 4201, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(41, 1, 39, '4202', 'income', 'credit', 3, 0, 1, 0, 1, 0, 4202, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(42, 1, 39, '4203', 'income', 'credit', 3, 0, 1, 0, 1, 0, 4203, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(43, 1, 33, '43', 'income', 'credit', 2, 0, 1, 0, 1, 0, 43, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(44, 1, NULL, '5', 'expense', 'debit', 1, 1, 0, 0, 1, 0, 5, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(45, 1, 44, '51', 'expense', 'debit', 2, 1, 0, 0, 1, 0, 51, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(46, 1, 45, '5101', 'expense', 'debit', 3, 0, 1, 0, 1, 0, 5101, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(47, 1, 45, '5102', 'expense', 'debit', 3, 0, 1, 0, 1, 0, 5102, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(48, 1, 45, '5103', 'expense', 'debit', 3, 0, 1, 0, 1, 0, 5103, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(49, 1, 45, '5104', 'expense', 'debit', 3, 0, 1, 0, 1, 0, 5104, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(50, 1, 44, '52', 'expense', 'debit', 2, 1, 0, 0, 1, 0, 52, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(51, 1, 50, '5201', 'expense', 'debit', 3, 0, 1, 0, 1, 0, 5201, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(52, 1, 50, '5202', 'expense', 'debit', 3, 0, 1, 0, 1, 0, 5202, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(53, 1, 50, '5203', 'expense', 'debit', 3, 0, 1, 0, 1, 0, 5203, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(54, 1, 44, '53', 'expense', 'debit', 2, 1, 0, 0, 1, 0, 53, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(55, 1, 54, '5301', 'expense', 'debit', 3, 0, 1, 0, 1, 0, 5301, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(56, 1, 54, '5302', 'expense', 'debit', 3, 0, 1, 0, 1, 0, 5302, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(57, 1, 54, '5303', 'expense', 'debit', 3, 0, 1, 0, 1, 0, 5303, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(58, 1, 54, '5304', 'expense', 'debit', 3, 0, 1, 0, 1, 0, 5304, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(59, 1, 54, '5305', 'expense', 'debit', 3, 0, 1, 0, 1, 0, 5305, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL),
(60, 1, 54, '5306', 'expense', 'debit', 3, 0, 1, 0, 1, 0, 5306, 1, '2026-11-08 12:09:59', '2026-11-08 12:09:59', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `account_mappings`
--

CREATE TABLE `account_mappings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `entity_id` bigint(20) UNSIGNED NOT NULL,
  `operation_key` varchar(255) NOT NULL,
  `debit_account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `credit_account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `fee_account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `journal_id` bigint(20) UNSIGNED DEFAULT NULL,
  `auto_post` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `account_translations`
--

CREATE TABLE `account_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `account_translations`
--

INSERT INTO `account_translations` (`id`, `account_id`, `locale`, `name`, `description`) VALUES
(1, 1, 'ar', 'الأصول', NULL),
(2, 1, 'en', 'Assets', NULL),
(3, 2, 'ar', 'الأصول المتداولة', NULL),
(4, 2, 'en', 'Current Assets', NULL),
(5, 3, 'ar', 'النقدية وما في حكمها', NULL),
(6, 3, 'en', 'Cash and Cash Equivalents', NULL),
(7, 4, 'ar', 'الصندوق الرئيسي (الخزينة)', NULL),
(8, 4, 'en', 'Main Cash Safe', NULL),
(9, 5, 'ar', 'البنك التجاري (الحساب الجاري)', NULL),
(10, 5, 'en', 'Commercial Bank - Current Account', NULL),
(11, 6, 'ar', 'أجهزة نقاط البيع وبوابات الدفع (K-Net)', NULL),
(12, 6, 'en', 'POS & K-Net Payment Gateways', NULL),
(13, 7, 'ar', 'عهد نقدية ومصروفات نثرية', NULL),
(14, 7, 'en', 'Petty Cash Custody', NULL),
(15, 8, 'ar', 'المدينون وحسابات القبض', NULL),
(16, 8, 'en', 'Accounts Receivable', NULL),
(17, 9, 'ar', 'مدينو عقود الإيجار (المستأجرون)', NULL),
(18, 9, 'en', 'Lease Tenants Receivable', NULL),
(19, 10, 'ar', 'مدينو خدمات الشركات والاستشارات', NULL),
(20, 10, 'en', 'Corporate Services Clients Receivable', NULL),
(21, 11, 'ar', 'شيكات تحت التحصيل', NULL),
(22, 11, 'en', 'Cheques Under Collection', NULL),
(23, 12, 'ar', 'إيرادات إيجارات مستحقة غير محصلة', NULL),
(24, 12, 'en', 'Accrued Rent Receivables', NULL),
(25, 13, 'ar', 'أرصدة مدينة ومدفوعات مقدماً', NULL),
(26, 13, 'en', 'Prepaid Expenses & Other Receivables', NULL),
(27, 14, 'ar', 'تأمينات ورخص مدفوعة مقدماً', NULL),
(28, 14, 'en', 'Prepaid Insurance & Licences', NULL),
(29, 15, 'ar', 'تأمينات مستردة لدى الغير (مرافق وكهرباء)', NULL),
(30, 15, 'en', 'Refundable Security Deposits with Others', NULL),
(31, 16, 'ar', 'الأصول غير المتداولة (الثابتة والاستثمارية)', NULL),
(32, 16, 'en', 'Non-Current & Fixed Assets', NULL),
(33, 17, 'ar', 'العقارات والمباني الاستثمارية', NULL),
(34, 17, 'en', 'Investment Properties & Buildings', NULL),
(35, 18, 'ar', 'الأراضي', NULL),
(36, 18, 'en', 'Lands', NULL),
(37, 19, 'ar', 'أسطول السيارات والآليات والمعدات', NULL),
(38, 19, 'en', 'Fleet, Vehicles & Equipment', NULL),
(39, 20, 'ar', 'الأثاث والتجهيزات المكتبية', NULL),
(40, 20, 'en', 'Furniture and Office Fixtures', NULL),
(41, 21, 'ar', 'مجمع إهلاك الأصول الثابتة', NULL),
(42, 21, 'en', 'Accumulated Depreciation', NULL),
(43, 22, 'ar', 'الالتزامات والخصوم', NULL),
(44, 22, 'en', 'Liabilities', NULL),
(45, 23, 'ar', 'الالتزامات المتداولة', NULL),
(46, 23, 'en', 'Current Liabilities', NULL),
(47, 24, 'ar', 'الموردون والدائنون التجاريون', NULL),
(48, 24, 'en', 'Accounts Payable (Vendors)', NULL),
(49, 25, 'ar', 'تأمينات الإيجار المحتفظ بها للمستأجرين', NULL),
(50, 25, 'en', 'Tenants Security Deposits', NULL),
(51, 26, 'ar', 'إيرادات إيجارات وعقود محصلة مقدماً', NULL),
(52, 26, 'en', 'Unearned Rent & Advance Contracts Revenue', NULL),
(53, 27, 'ar', 'الرواتب والأجور ومستحقات الموظفين', NULL),
(54, 27, 'en', 'Accrued Salaries and Employee Benefits', NULL),
(55, 28, 'ar', 'رسوم وتراخيص حكومية مستحقة', NULL),
(56, 28, 'en', 'Accrued Government Fees & Licenses', NULL),
(57, 29, 'ar', 'حقوق الملكية', NULL),
(58, 29, 'en', 'Equity', NULL),
(59, 30, 'ar', 'رأس المال المدفوع', NULL),
(60, 30, 'en', 'Paid-in Capital', NULL),
(61, 31, 'ar', 'الأرباح المحتجزة / المدورة', NULL),
(62, 31, 'en', 'Retained Earnings', NULL),
(63, 32, 'ar', 'جاري الشركاء / المالك', NULL),
(64, 32, 'en', 'Owner / Partners Current Account', NULL),
(65, 33, 'ar', 'الإيرادات', NULL),
(66, 33, 'en', 'Revenue / Income', NULL),
(67, 34, 'ar', 'إيرادات النشاط العقاري والتأجير', NULL),
(68, 34, 'en', 'Real Estate & Rental Revenue', NULL),
(69, 35, 'ar', 'إيرادات تأجير وحدات سكنية', NULL),
(70, 35, 'en', 'Residential Units Rental Revenue', NULL),
(71, 36, 'ar', 'إيرادات تأجير محلات وتجاري', NULL),
(72, 36, 'en', 'Commercial Units & Shops Rental Revenue', NULL),
(73, 37, 'ar', 'إيرادات تأجير مكاتب وشركات', NULL),
(74, 37, 'en', 'Offices & Corporate Rental Revenue', NULL),
(75, 38, 'ar', 'غرامات تأخير ورسوم خدمات عقارية', NULL),
(76, 38, 'en', 'Late Fees & Property Service Charges', NULL),
(77, 39, 'ar', 'إيرادات خدمات الشركات والأسطول', NULL),
(78, 39, 'en', 'Corporate Services & Fleet Revenue', NULL),
(79, 40, 'ar', 'إيرادات تأسيس وتجديد رخص الشركات', NULL),
(80, 40, 'en', 'Company Licenses & Renewal Services', NULL),
(81, 41, 'ar', 'إيرادات عقود إدارة وتشغيل وصيانة', NULL),
(82, 41, 'en', 'Property & Corporate Management Contracts', NULL),
(83, 42, 'ar', 'إيرادات تشغيل وتأجير المركبات والآليات', NULL),
(84, 42, 'en', 'Vehicle & Fleet Operations Revenue', NULL),
(85, 43, 'ar', 'إيرادات أخرى متنوعة', NULL),
(86, 43, 'en', 'Other Miscellaneous Income', NULL),
(87, 44, 'ar', 'المصروفات', NULL),
(88, 44, 'en', 'Expenses', NULL),
(89, 45, 'ar', 'مصروفات تشغيل وصيانة العقارات', NULL),
(90, 45, 'en', 'Property Operating & Maintenance Expenses', NULL),
(91, 46, 'ar', 'صيانة وترميم المباني والوحدات', NULL),
(92, 46, 'en', 'Building & Unit Maintenance & Repairs', NULL),
(93, 47, 'ar', 'استهلاك الكهرباء والماء والمرافق للعقارات', NULL),
(94, 47, 'en', 'Building Electricity, Water & Utilities', NULL),
(95, 48, 'ar', 'خدمات الحراسة والأمن والنظافة', NULL),
(96, 48, 'en', 'Security, Guarding & Cleaning Services', NULL),
(97, 49, 'ar', 'عمولات تسويق وتأجير العقارات', NULL),
(98, 49, 'en', 'Real Estate Leasing & Brokerage Commissions', NULL),
(99, 50, 'ar', 'مصروفات أسطول السيارات والآليات', NULL),
(100, 50, 'en', 'Fleet & Vehicle Expenses', NULL),
(101, 51, 'ar', 'وقود ومحروقات المركبات', NULL),
(102, 51, 'en', 'Vehicle Fuel & Lubricants', NULL),
(103, 52, 'ar', 'صيانة دورية وقطع غيار وتواير', NULL),
(104, 52, 'en', 'Vehicle Maintenance, Tyres & Spare Parts', NULL),
(105, 53, 'ar', 'تأمين ورسوم تراخيص وفحص المرور', NULL),
(106, 53, 'en', 'Vehicle Insurance & Traffic Licensing', NULL),
(107, 54, 'ar', 'المصروفات الإدارية والعمومية', NULL),
(108, 54, 'en', 'General & Administrative Expenses', NULL),
(109, 55, 'ar', 'رواتب وأجور ومكافآت الموظفين', NULL),
(110, 55, 'en', 'Administrative Salaries & Benefits', NULL),
(111, 56, 'ar', 'إيجار مقرات ومكاتب الإدارة', NULL),
(112, 56, 'en', 'Office & Headquarters Rent', NULL),
(113, 57, 'ar', 'رسوم وتراخيص حكومية وتجديدات', NULL),
(114, 57, 'en', 'Government Licenses, Fees & Renewals', NULL),
(115, 58, 'ar', 'مصروفات دعاية وإعلان وتسويق', NULL),
(116, 58, 'en', 'Advertising & Marketing Expenses', NULL),
(117, 59, 'ar', 'مصروفات بنكية ورسوم دفع K-Net', NULL),
(118, 59, 'en', 'Bank Charges & K-Net Fees', NULL),
(119, 60, 'ar', 'إهلاك الأصول الثابتة', NULL),
(120, 60, 'en', 'Depreciation Expense', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `log_name` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `event` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `causer_type` varchar(255) DEFAULT NULL,
  `causer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `batch_uuid` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(1, 'accounting', 'تمت إضافة الكيان المحاسبي: ENT-0001', 'Modules\\Accounting\\Models\\Entity', 'created', 1, NULL, NULL, '{\"attributes\":{\"id\":1,\"company_id\":3,\"code\":\"ENT-0001\",\"type\":\"company\",\"currency\":\"KWD\",\"status\":\"active\",\"created_by\":1,\"created_at\":\"2026-11-08T14:09:57.000000Z\",\"updated_at\":\"2026-11-08T14:09:57.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:58', '2026-11-08 12:09:58'),
(2, 'accounting', 'تمت إضافة الحساب المالي: 1', 'Modules\\Accounting\\Models\\Account', 'created', 1, NULL, NULL, '{\"attributes\":{\"id\":1,\"entity_id\":1,\"parent_id\":null,\"code\":\"1\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"level\":1,\"is_group\":true,\"allow_posting\":false,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":1,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(3, 'accounting', 'تمت إضافة الحساب المالي: 11', 'Modules\\Accounting\\Models\\Account', 'created', 2, NULL, NULL, '{\"attributes\":{\"id\":2,\"entity_id\":1,\"parent_id\":1,\"code\":\"11\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"level\":2,\"is_group\":true,\"allow_posting\":false,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":11,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(4, 'accounting', 'تمت إضافة الحساب المالي: 1101', 'Modules\\Accounting\\Models\\Account', 'created', 3, NULL, NULL, '{\"attributes\":{\"id\":3,\"entity_id\":1,\"parent_id\":2,\"code\":\"1101\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"level\":3,\"is_group\":true,\"allow_posting\":false,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":1101,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(5, 'accounting', 'تمت إضافة الحساب المالي: 110101', 'Modules\\Accounting\\Models\\Account', 'created', 4, NULL, NULL, '{\"attributes\":{\"id\":4,\"entity_id\":1,\"parent_id\":3,\"code\":\"110101\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"level\":4,\"is_group\":false,\"allow_posting\":true,\"reconcile\":true,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":110101,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(6, 'accounting', 'تمت إضافة الحساب المالي: 110102', 'Modules\\Accounting\\Models\\Account', 'created', 5, NULL, NULL, '{\"attributes\":{\"id\":5,\"entity_id\":1,\"parent_id\":3,\"code\":\"110102\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"level\":4,\"is_group\":false,\"allow_posting\":true,\"reconcile\":true,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":110102,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(7, 'accounting', 'تمت إضافة الحساب المالي: 110103', 'Modules\\Accounting\\Models\\Account', 'created', 6, NULL, NULL, '{\"attributes\":{\"id\":6,\"entity_id\":1,\"parent_id\":3,\"code\":\"110103\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"level\":4,\"is_group\":false,\"allow_posting\":true,\"reconcile\":true,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":110103,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(8, 'accounting', 'تمت إضافة الحساب المالي: 110104', 'Modules\\Accounting\\Models\\Account', 'created', 7, NULL, NULL, '{\"attributes\":{\"id\":7,\"entity_id\":1,\"parent_id\":3,\"code\":\"110104\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"level\":4,\"is_group\":false,\"allow_posting\":true,\"reconcile\":true,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":110104,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(9, 'accounting', 'تمت إضافة الحساب المالي: 1102', 'Modules\\Accounting\\Models\\Account', 'created', 8, NULL, NULL, '{\"attributes\":{\"id\":8,\"entity_id\":1,\"parent_id\":2,\"code\":\"1102\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"level\":3,\"is_group\":true,\"allow_posting\":false,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":1102,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(10, 'accounting', 'تمت إضافة الحساب المالي: 110201', 'Modules\\Accounting\\Models\\Account', 'created', 9, NULL, NULL, '{\"attributes\":{\"id\":9,\"entity_id\":1,\"parent_id\":8,\"code\":\"110201\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"level\":4,\"is_group\":false,\"allow_posting\":true,\"reconcile\":true,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":110201,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(11, 'accounting', 'تمت إضافة الحساب المالي: 110202', 'Modules\\Accounting\\Models\\Account', 'created', 10, NULL, NULL, '{\"attributes\":{\"id\":10,\"entity_id\":1,\"parent_id\":8,\"code\":\"110202\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"level\":4,\"is_group\":false,\"allow_posting\":true,\"reconcile\":true,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":110202,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(12, 'accounting', 'تمت إضافة الحساب المالي: 110203', 'Modules\\Accounting\\Models\\Account', 'created', 11, NULL, NULL, '{\"attributes\":{\"id\":11,\"entity_id\":1,\"parent_id\":8,\"code\":\"110203\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"level\":4,\"is_group\":false,\"allow_posting\":true,\"reconcile\":true,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":110203,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(13, 'accounting', 'تمت إضافة الحساب المالي: 110204', 'Modules\\Accounting\\Models\\Account', 'created', 12, NULL, NULL, '{\"attributes\":{\"id\":12,\"entity_id\":1,\"parent_id\":8,\"code\":\"110204\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"level\":4,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":110204,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(14, 'accounting', 'تمت إضافة الحساب المالي: 1103', 'Modules\\Accounting\\Models\\Account', 'created', 13, NULL, NULL, '{\"attributes\":{\"id\":13,\"entity_id\":1,\"parent_id\":2,\"code\":\"1103\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"level\":3,\"is_group\":true,\"allow_posting\":false,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":1103,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(15, 'accounting', 'تمت إضافة الحساب المالي: 110301', 'Modules\\Accounting\\Models\\Account', 'created', 14, NULL, NULL, '{\"attributes\":{\"id\":14,\"entity_id\":1,\"parent_id\":13,\"code\":\"110301\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"level\":4,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":110301,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(16, 'accounting', 'تمت إضافة الحساب المالي: 110302', 'Modules\\Accounting\\Models\\Account', 'created', 15, NULL, NULL, '{\"attributes\":{\"id\":15,\"entity_id\":1,\"parent_id\":13,\"code\":\"110302\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"level\":4,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":110302,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(17, 'accounting', 'تمت إضافة الحساب المالي: 12', 'Modules\\Accounting\\Models\\Account', 'created', 16, NULL, NULL, '{\"attributes\":{\"id\":16,\"entity_id\":1,\"parent_id\":1,\"code\":\"12\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"level\":2,\"is_group\":true,\"allow_posting\":false,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":12,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(18, 'accounting', 'تمت إضافة الحساب المالي: 1201', 'Modules\\Accounting\\Models\\Account', 'created', 17, NULL, NULL, '{\"attributes\":{\"id\":17,\"entity_id\":1,\"parent_id\":16,\"code\":\"1201\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":1201,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(19, 'accounting', 'تمت إضافة الحساب المالي: 1202', 'Modules\\Accounting\\Models\\Account', 'created', 18, NULL, NULL, '{\"attributes\":{\"id\":18,\"entity_id\":1,\"parent_id\":16,\"code\":\"1202\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":1202,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(20, 'accounting', 'تمت إضافة الحساب المالي: 1203', 'Modules\\Accounting\\Models\\Account', 'created', 19, NULL, NULL, '{\"attributes\":{\"id\":19,\"entity_id\":1,\"parent_id\":16,\"code\":\"1203\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":1203,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(21, 'accounting', 'تمت إضافة الحساب المالي: 1204', 'Modules\\Accounting\\Models\\Account', 'created', 20, NULL, NULL, '{\"attributes\":{\"id\":20,\"entity_id\":1,\"parent_id\":16,\"code\":\"1204\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":1204,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(22, 'accounting', 'تمت إضافة الحساب المالي: 1205', 'Modules\\Accounting\\Models\\Account', 'created', 21, NULL, NULL, '{\"attributes\":{\"id\":21,\"entity_id\":1,\"parent_id\":16,\"code\":\"1205\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":1205,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(23, 'accounting', 'تمت إضافة الحساب المالي: 2', 'Modules\\Accounting\\Models\\Account', 'created', 22, NULL, NULL, '{\"attributes\":{\"id\":22,\"entity_id\":1,\"parent_id\":null,\"code\":\"2\",\"account_type\":\"liability\",\"normal_balance\":\"credit\",\"level\":1,\"is_group\":true,\"allow_posting\":false,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":2,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(24, 'accounting', 'تمت إضافة الحساب المالي: 21', 'Modules\\Accounting\\Models\\Account', 'created', 23, NULL, NULL, '{\"attributes\":{\"id\":23,\"entity_id\":1,\"parent_id\":22,\"code\":\"21\",\"account_type\":\"liability\",\"normal_balance\":\"credit\",\"level\":2,\"is_group\":true,\"allow_posting\":false,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":21,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(25, 'accounting', 'تمت إضافة الحساب المالي: 2101', 'Modules\\Accounting\\Models\\Account', 'created', 24, NULL, NULL, '{\"attributes\":{\"id\":24,\"entity_id\":1,\"parent_id\":23,\"code\":\"2101\",\"account_type\":\"liability\",\"normal_balance\":\"credit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":true,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":2101,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(26, 'accounting', 'تمت إضافة الحساب المالي: 2102', 'Modules\\Accounting\\Models\\Account', 'created', 25, NULL, NULL, '{\"attributes\":{\"id\":25,\"entity_id\":1,\"parent_id\":23,\"code\":\"2102\",\"account_type\":\"liability\",\"normal_balance\":\"credit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":true,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":2102,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(27, 'accounting', 'تمت إضافة الحساب المالي: 2103', 'Modules\\Accounting\\Models\\Account', 'created', 26, NULL, NULL, '{\"attributes\":{\"id\":26,\"entity_id\":1,\"parent_id\":23,\"code\":\"2103\",\"account_type\":\"liability\",\"normal_balance\":\"credit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":2103,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(28, 'accounting', 'تمت إضافة الحساب المالي: 2104', 'Modules\\Accounting\\Models\\Account', 'created', 27, NULL, NULL, '{\"attributes\":{\"id\":27,\"entity_id\":1,\"parent_id\":23,\"code\":\"2104\",\"account_type\":\"liability\",\"normal_balance\":\"credit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":2104,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(29, 'accounting', 'تمت إضافة الحساب المالي: 2105', 'Modules\\Accounting\\Models\\Account', 'created', 28, NULL, NULL, '{\"attributes\":{\"id\":28,\"entity_id\":1,\"parent_id\":23,\"code\":\"2105\",\"account_type\":\"liability\",\"normal_balance\":\"credit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":2105,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(30, 'accounting', 'تمت إضافة الحساب المالي: 3', 'Modules\\Accounting\\Models\\Account', 'created', 29, NULL, NULL, '{\"attributes\":{\"id\":29,\"entity_id\":1,\"parent_id\":null,\"code\":\"3\",\"account_type\":\"equity\",\"normal_balance\":\"credit\",\"level\":1,\"is_group\":true,\"allow_posting\":false,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":3,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(31, 'accounting', 'تمت إضافة الحساب المالي: 31', 'Modules\\Accounting\\Models\\Account', 'created', 30, NULL, NULL, '{\"attributes\":{\"id\":30,\"entity_id\":1,\"parent_id\":29,\"code\":\"31\",\"account_type\":\"equity\",\"normal_balance\":\"credit\",\"level\":2,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":31,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(32, 'accounting', 'تمت إضافة الحساب المالي: 32', 'Modules\\Accounting\\Models\\Account', 'created', 31, NULL, NULL, '{\"attributes\":{\"id\":31,\"entity_id\":1,\"parent_id\":29,\"code\":\"32\",\"account_type\":\"equity\",\"normal_balance\":\"credit\",\"level\":2,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":32,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(33, 'accounting', 'تمت إضافة الحساب المالي: 33', 'Modules\\Accounting\\Models\\Account', 'created', 32, NULL, NULL, '{\"attributes\":{\"id\":32,\"entity_id\":1,\"parent_id\":29,\"code\":\"33\",\"account_type\":\"equity\",\"normal_balance\":\"credit\",\"level\":2,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":33,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(34, 'accounting', 'تمت إضافة الحساب المالي: 4', 'Modules\\Accounting\\Models\\Account', 'created', 33, NULL, NULL, '{\"attributes\":{\"id\":33,\"entity_id\":1,\"parent_id\":null,\"code\":\"4\",\"account_type\":\"income\",\"normal_balance\":\"credit\",\"level\":1,\"is_group\":true,\"allow_posting\":false,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":4,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(35, 'accounting', 'تمت إضافة الحساب المالي: 41', 'Modules\\Accounting\\Models\\Account', 'created', 34, NULL, NULL, '{\"attributes\":{\"id\":34,\"entity_id\":1,\"parent_id\":33,\"code\":\"41\",\"account_type\":\"income\",\"normal_balance\":\"credit\",\"level\":2,\"is_group\":true,\"allow_posting\":false,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":41,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(36, 'accounting', 'تمت إضافة الحساب المالي: 4101', 'Modules\\Accounting\\Models\\Account', 'created', 35, NULL, NULL, '{\"attributes\":{\"id\":35,\"entity_id\":1,\"parent_id\":34,\"code\":\"4101\",\"account_type\":\"income\",\"normal_balance\":\"credit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":4101,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(37, 'accounting', 'تمت إضافة الحساب المالي: 4102', 'Modules\\Accounting\\Models\\Account', 'created', 36, NULL, NULL, '{\"attributes\":{\"id\":36,\"entity_id\":1,\"parent_id\":34,\"code\":\"4102\",\"account_type\":\"income\",\"normal_balance\":\"credit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":4102,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(38, 'accounting', 'تمت إضافة الحساب المالي: 4103', 'Modules\\Accounting\\Models\\Account', 'created', 37, NULL, NULL, '{\"attributes\":{\"id\":37,\"entity_id\":1,\"parent_id\":34,\"code\":\"4103\",\"account_type\":\"income\",\"normal_balance\":\"credit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":4103,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(39, 'accounting', 'تمت إضافة الحساب المالي: 4104', 'Modules\\Accounting\\Models\\Account', 'created', 38, NULL, NULL, '{\"attributes\":{\"id\":38,\"entity_id\":1,\"parent_id\":34,\"code\":\"4104\",\"account_type\":\"income\",\"normal_balance\":\"credit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":4104,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(40, 'accounting', 'تمت إضافة الحساب المالي: 42', 'Modules\\Accounting\\Models\\Account', 'created', 39, NULL, NULL, '{\"attributes\":{\"id\":39,\"entity_id\":1,\"parent_id\":33,\"code\":\"42\",\"account_type\":\"income\",\"normal_balance\":\"credit\",\"level\":2,\"is_group\":true,\"allow_posting\":false,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":42,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(41, 'accounting', 'تمت إضافة الحساب المالي: 4201', 'Modules\\Accounting\\Models\\Account', 'created', 40, NULL, NULL, '{\"attributes\":{\"id\":40,\"entity_id\":1,\"parent_id\":39,\"code\":\"4201\",\"account_type\":\"income\",\"normal_balance\":\"credit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":4201,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(42, 'accounting', 'تمت إضافة الحساب المالي: 4202', 'Modules\\Accounting\\Models\\Account', 'created', 41, NULL, NULL, '{\"attributes\":{\"id\":41,\"entity_id\":1,\"parent_id\":39,\"code\":\"4202\",\"account_type\":\"income\",\"normal_balance\":\"credit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":4202,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(43, 'accounting', 'تمت إضافة الحساب المالي: 4203', 'Modules\\Accounting\\Models\\Account', 'created', 42, NULL, NULL, '{\"attributes\":{\"id\":42,\"entity_id\":1,\"parent_id\":39,\"code\":\"4203\",\"account_type\":\"income\",\"normal_balance\":\"credit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":4203,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(44, 'accounting', 'تمت إضافة الحساب المالي: 43', 'Modules\\Accounting\\Models\\Account', 'created', 43, NULL, NULL, '{\"attributes\":{\"id\":43,\"entity_id\":1,\"parent_id\":33,\"code\":\"43\",\"account_type\":\"income\",\"normal_balance\":\"credit\",\"level\":2,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":43,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(45, 'accounting', 'تمت إضافة الحساب المالي: 5', 'Modules\\Accounting\\Models\\Account', 'created', 44, NULL, NULL, '{\"attributes\":{\"id\":44,\"entity_id\":1,\"parent_id\":null,\"code\":\"5\",\"account_type\":\"expense\",\"normal_balance\":\"debit\",\"level\":1,\"is_group\":true,\"allow_posting\":false,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":5,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(46, 'accounting', 'تمت إضافة الحساب المالي: 51', 'Modules\\Accounting\\Models\\Account', 'created', 45, NULL, NULL, '{\"attributes\":{\"id\":45,\"entity_id\":1,\"parent_id\":44,\"code\":\"51\",\"account_type\":\"expense\",\"normal_balance\":\"debit\",\"level\":2,\"is_group\":true,\"allow_posting\":false,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":51,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(47, 'accounting', 'تمت إضافة الحساب المالي: 5101', 'Modules\\Accounting\\Models\\Account', 'created', 46, NULL, NULL, '{\"attributes\":{\"id\":46,\"entity_id\":1,\"parent_id\":45,\"code\":\"5101\",\"account_type\":\"expense\",\"normal_balance\":\"debit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":5101,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(48, 'accounting', 'تمت إضافة الحساب المالي: 5102', 'Modules\\Accounting\\Models\\Account', 'created', 47, NULL, NULL, '{\"attributes\":{\"id\":47,\"entity_id\":1,\"parent_id\":45,\"code\":\"5102\",\"account_type\":\"expense\",\"normal_balance\":\"debit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":5102,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(49, 'accounting', 'تمت إضافة الحساب المالي: 5103', 'Modules\\Accounting\\Models\\Account', 'created', 48, NULL, NULL, '{\"attributes\":{\"id\":48,\"entity_id\":1,\"parent_id\":45,\"code\":\"5103\",\"account_type\":\"expense\",\"normal_balance\":\"debit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":5103,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(50, 'accounting', 'تمت إضافة الحساب المالي: 5104', 'Modules\\Accounting\\Models\\Account', 'created', 49, NULL, NULL, '{\"attributes\":{\"id\":49,\"entity_id\":1,\"parent_id\":45,\"code\":\"5104\",\"account_type\":\"expense\",\"normal_balance\":\"debit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":5104,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(51, 'accounting', 'تمت إضافة الحساب المالي: 52', 'Modules\\Accounting\\Models\\Account', 'created', 50, NULL, NULL, '{\"attributes\":{\"id\":50,\"entity_id\":1,\"parent_id\":44,\"code\":\"52\",\"account_type\":\"expense\",\"normal_balance\":\"debit\",\"level\":2,\"is_group\":true,\"allow_posting\":false,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":52,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(52, 'accounting', 'تمت إضافة الحساب المالي: 5201', 'Modules\\Accounting\\Models\\Account', 'created', 51, NULL, NULL, '{\"attributes\":{\"id\":51,\"entity_id\":1,\"parent_id\":50,\"code\":\"5201\",\"account_type\":\"expense\",\"normal_balance\":\"debit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":5201,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(53, 'accounting', 'تمت إضافة الحساب المالي: 5202', 'Modules\\Accounting\\Models\\Account', 'created', 52, NULL, NULL, '{\"attributes\":{\"id\":52,\"entity_id\":1,\"parent_id\":50,\"code\":\"5202\",\"account_type\":\"expense\",\"normal_balance\":\"debit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":5202,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(54, 'accounting', 'تمت إضافة الحساب المالي: 5203', 'Modules\\Accounting\\Models\\Account', 'created', 53, NULL, NULL, '{\"attributes\":{\"id\":53,\"entity_id\":1,\"parent_id\":50,\"code\":\"5203\",\"account_type\":\"expense\",\"normal_balance\":\"debit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":5203,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(55, 'accounting', 'تمت إضافة الحساب المالي: 53', 'Modules\\Accounting\\Models\\Account', 'created', 54, NULL, NULL, '{\"attributes\":{\"id\":54,\"entity_id\":1,\"parent_id\":44,\"code\":\"53\",\"account_type\":\"expense\",\"normal_balance\":\"debit\",\"level\":2,\"is_group\":true,\"allow_posting\":false,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":53,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(56, 'accounting', 'تمت إضافة الحساب المالي: 5301', 'Modules\\Accounting\\Models\\Account', 'created', 55, NULL, NULL, '{\"attributes\":{\"id\":55,\"entity_id\":1,\"parent_id\":54,\"code\":\"5301\",\"account_type\":\"expense\",\"normal_balance\":\"debit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":5301,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(57, 'accounting', 'تمت إضافة الحساب المالي: 5302', 'Modules\\Accounting\\Models\\Account', 'created', 56, NULL, NULL, '{\"attributes\":{\"id\":56,\"entity_id\":1,\"parent_id\":54,\"code\":\"5302\",\"account_type\":\"expense\",\"normal_balance\":\"debit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":5302,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(58, 'accounting', 'تمت إضافة الحساب المالي: 5303', 'Modules\\Accounting\\Models\\Account', 'created', 57, NULL, NULL, '{\"attributes\":{\"id\":57,\"entity_id\":1,\"parent_id\":54,\"code\":\"5303\",\"account_type\":\"expense\",\"normal_balance\":\"debit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":5303,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(59, 'accounting', 'تمت إضافة الحساب المالي: 5304', 'Modules\\Accounting\\Models\\Account', 'created', 58, NULL, NULL, '{\"attributes\":{\"id\":58,\"entity_id\":1,\"parent_id\":54,\"code\":\"5304\",\"account_type\":\"expense\",\"normal_balance\":\"debit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":5304,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(60, 'accounting', 'تمت إضافة الحساب المالي: 5305', 'Modules\\Accounting\\Models\\Account', 'created', 59, NULL, NULL, '{\"attributes\":{\"id\":59,\"entity_id\":1,\"parent_id\":54,\"code\":\"5305\",\"account_type\":\"expense\",\"normal_balance\":\"debit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":5305,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59'),
(61, 'accounting', 'تمت إضافة الحساب المالي: 5306', 'Modules\\Accounting\\Models\\Account', 'created', 60, NULL, NULL, '{\"attributes\":{\"id\":60,\"entity_id\":1,\"parent_id\":54,\"code\":\"5306\",\"account_type\":\"expense\",\"normal_balance\":\"debit\",\"level\":3,\"is_group\":false,\"allow_posting\":true,\"reconcile\":false,\"is_active\":true,\"is_deprecated\":false,\"sort_order\":5306,\"created_by\":1,\"created_at\":\"2026-11-08T14:09:59.000000Z\",\"updated_at\":\"2026-11-08T14:09:59.000000Z\",\"deleted_at\":null}}', NULL, '2026-11-08 12:09:59', '2026-11-08 12:09:59');

-- --------------------------------------------------------

--
-- Table structure for table `ai_providers`
--

CREATE TABLE `ai_providers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `driver` varchar(50) NOT NULL,
  `api_key` text DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `base_url` varchar(255) DEFAULT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_tasks`
--

CREATE TABLE `ai_tasks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(80) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ai_tasks`
--

INSERT INTO `ai_tasks` (`id`, `code`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'translation', 'Translation', 'Translate ERP labels and content.', 1, '2026-11-08 07:34:02', '2026-11-08 07:34:02'),
(2, 'text_generation', 'Text Generation', 'Generate or rewrite text.', 1, '2026-11-08 07:34:02', '2026-11-08 07:34:02'),
(3, 'report_summary', 'Report Summary', 'Summarize and explain ERP reports.', 1, '2026-11-08 07:34:02', '2026-11-08 07:34:02'),
(4, 'ocr_invoice', 'Invoice OCR', 'Extract data from invoice images or files.', 1, '2026-11-08 07:34:02', '2026-11-08 07:34:02'),
(5, 'forecasting', 'Forecasting', 'Forecast ERP metrics.', 1, '2026-11-08 07:34:02', '2026-11-08 07:34:02'),
(6, 'financial_assistant', 'Financial Smart Assistant', 'Financial Q&A and screen-by-screen guide for accounting operations.', 1, '2026-11-08 07:35:16', '2026-11-08 07:35:16');

-- --------------------------------------------------------

--
-- Table structure for table `ai_task_providers`
--

CREATE TABLE `ai_task_providers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `task_id` bigint(20) UNSIGNED NOT NULL,
  `provider_id` bigint(20) UNSIGNED NOT NULL,
  `priority` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_usage_logs`
--

CREATE TABLE `ai_usage_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `provider_code` varchar(80) NOT NULL,
  `driver` varchar(50) NOT NULL,
  `model` varchar(120) DEFAULT NULL,
  `task_code` varchar(80) DEFAULT NULL,
  `prompt_tokens` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `completion_tokens` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `total_tokens` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `characters_count` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'success',
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attachmentables`
--

CREATE TABLE `attachmentables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `url` varchar(255) NOT NULL,
  `attachmentable_id` bigint(20) UNSIGNED NOT NULL,
  `attachmentable_type` varchar(255) NOT NULL,
  `type` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `shift_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `total_working_hours` decimal(5,2) NOT NULL DEFAULT 0.00,
  `required_hours` decimal(5,2) NOT NULL DEFAULT 8.00,
  `late_minutes` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `early_leave_minutes` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `overtime_minutes` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `status` varchar(30) NOT NULL DEFAULT 'present',
  `check_in_source` varchar(30) DEFAULT NULL,
  `check_out_source` varchar(30) DEFAULT NULL,
  `is_manual_adjusted` tinyint(1) NOT NULL DEFAULT 0,
  `adjustment_reason` text DEFAULT NULL,
  `adjusted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_raw_logs`
--

CREATE TABLE `attendance_raw_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `device_user_id` varchar(50) DEFAULT NULL,
  `punch_time` datetime NOT NULL,
  `punch_type` varchar(20) NOT NULL DEFAULT 'auto',
  `source` varchar(30) NOT NULL DEFAULT 'manual',
  `device_id` bigint(20) UNSIGNED DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `location_address` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `device_info` varchar(255) DEFAULT NULL,
  `snapshot` varchar(255) DEFAULT NULL,
  `is_processed` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_log_settings`
--

CREATE TABLE `audit_log_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `is_external_db_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `driver` varchar(255) NOT NULL DEFAULT 'mysql',
  `host` varchar(255) DEFAULT NULL,
  `port` varchar(255) DEFAULT '3306',
  `database` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` text DEFAULT NULL,
  `fallback_to_local` tinyint(1) NOT NULL DEFAULT 1,
  `is_queue_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `last_tested_at` timestamp NULL DEFAULT NULL,
  `last_test_status` varchar(255) DEFAULT NULL,
  `last_test_message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `banks`
--

CREATE TABLE `banks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `swift_code` varchar(50) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bank_reconciliations`
--

CREATE TABLE `bank_reconciliations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `entity_id` bigint(20) UNSIGNED NOT NULL,
  `cash_account_id` bigint(20) UNSIGNED NOT NULL,
  `reconciliation_number` varchar(50) NOT NULL,
  `statement_date` date NOT NULL,
  `statement_balance` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `book_balance` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `reconciled_balance` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `difference` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `reconciled_at` timestamp NULL DEFAULT NULL,
  `reconciled_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bank_reconciliation_items`
--

CREATE TABLE `bank_reconciliation_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bank_reconciliation_id` bigint(20) UNSIGNED NOT NULL,
  `journal_entry_line_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(30) NOT NULL DEFAULT 'matched_entry',
  `statement_reference` varchar(100) DEFAULT NULL,
  `transaction_date` date NOT NULL,
  `debit` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `credit` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `is_cleared` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bank_translations`
--

CREATE TABLE `bank_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bank_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

CREATE TABLE `bills` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bill_type` varchar(255) NOT NULL DEFAULT 'collection',
  `contract_id` bigint(20) UNSIGNED DEFAULT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `bill_number` varchar(255) DEFAULT NULL,
  `bill_date` date DEFAULT NULL,
  `amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `due_date` date DEFAULT NULL,
  `paid_date` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'unpaid',
  `payment_status` varchar(255) NOT NULL DEFAULT 'PENDING',
  `payment_date` datetime DEFAULT NULL,
  `pay_after` varchar(255) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_mobile` varchar(255) DEFAULT NULL,
  `customer_country_code` varchar(255) NOT NULL DEFAULT '+20',
  `customer_email` varchar(255) DEFAULT NULL,
  `bill_sent_by` varchar(255) NOT NULL DEFAULT 'WHATSAPP',
  `payment_link_validity` varchar(255) NOT NULL DEFAULT 'never_expires',
  `paid_bills_count` int(11) NOT NULL DEFAULT 0,
  `bills_type` varchar(255) NOT NULL DEFAULT 'ONE_TIME_BILL',
  `period_label` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bills`
--

INSERT INTO `bills` (`id`, `bill_type`, `contract_id`, `company_id`, `user_id`, `bill_number`, `bill_date`, `amount`, `paid_amount`, `due_date`, `paid_date`, `status`, `payment_status`, `payment_date`, `pay_after`, `customer_name`, `customer_mobile`, `customer_country_code`, `customer_email`, `bill_sent_by`, `payment_link_validity`, `paid_bills_count`, `bills_type`, `period_label`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'collection', NULL, 3, 1, 'BILL-00001', '2026-02-25', 654.00, 0.00, '2026-02-25', NULL, 'unpaid', 'PENDING', NULL, NULL, NULL, NULL, '+20', NULL, 'WHATSAPP', 'never_expires', 0, 'ONE_TIME_BILL', NULL, NULL, '2026-02-25 10:58:02', '2026-02-25 10:58:02', NULL),
(5, 'collection', NULL, 3, 1, 'BILL-00005', '2026-02-26', 4498.00, 0.00, '2026-02-26', NULL, 'unpaid', 'PENDING', NULL, NULL, NULL, NULL, '+20', NULL, 'WHATSAPP', 'never_expires', 0, 'ONE_TIME_BILL', NULL, NULL, '2026-02-26 07:49:48', '2026-02-26 07:49:48', NULL),
(6, 'collection', NULL, 3, 1, 'BILL-00006', '2026-02-26', 4498.00, 0.00, '2026-02-26', NULL, 'unpaid', 'PENDING', NULL, 'NOT_EXPIRE', 'shdfks', '1090460119', '+20', 'akdja@jjoi.sds', 'WHATSAPP', 'never_expires', 0, 'ONE_TIME_BILL', NULL, NULL, '2026-02-26 08:01:25', '2026-02-26 09:09:08', NULL),
(7, 'collection', NULL, 3, 1, 'BILL-00007', '2026-03-04', 100.00, 100.00, '2026-03-04', NULL, 'paid', 'PAID', NULL, '10', 'Eum sed optio asper', NULL, '+20', 'tacebap@mailinator.com', 'WHATSAPP', 'never_expires', 0, 'ONE_TIME_BILL', NULL, NULL, '2026-03-04 19:08:25', '2026-03-04 19:08:25', NULL),
(8, 'collection', NULL, 3, 1, 'BILL-00008', '2026-03-05', 6450.00, 6450.00, '2026-03-05', NULL, 'paid', 'PAID', NULL, NULL, NULL, NULL, '+20', NULL, 'WHATSAPP', 'never_expires', 0, 'ONE_TIME_BILL', NULL, NULL, '2026-03-05 06:24:54', '2026-03-05 06:24:54', NULL),
(9, 'collection', NULL, 3, 1, 'BILL-00009', '2026-03-05', 4498.00, 0.00, '2026-03-05', NULL, 'unpaid', 'FAILED', NULL, 'NOT_EXPIRE', 'shdfks', '1090460119', '+20', 'akdja@jjoi.sds', 'WHATSAPP', 'never_expires', 0, 'ONE_TIME_BILL', NULL, NULL, '2026-03-05 06:49:30', '2026-03-05 06:49:30', NULL),
(10, 'collection', NULL, 3, 1, 'BILL-00010', '2026-03-05', 1000.00, 0.00, '2026-03-05', NULL, 'unpaid', 'RETURNED', NULL, 'NOT_EXPIRE', NULL, NULL, '+20', NULL, 'WHATSAPP', 'never_expires', 0, 'ONE_TIME_BILL', NULL, NULL, '2026-03-05 10:34:49', '2026-03-05 10:34:49', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bill_payment_dues`
--

CREATE TABLE `bill_payment_dues` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bill_id` bigint(20) UNSIGNED NOT NULL,
  `contract_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payment_type` varchar(255) NOT NULL DEFAULT 'STATIC_DAY',
  `scheduling_type` varchar(255) NOT NULL DEFAULT 'ONE_TIME_BILL',
  `scheduling_day` int(11) NOT NULL DEFAULT 1,
  `next_payment_date` date DEFAULT NULL,
  `tolerance_for_delay` int(11) NOT NULL DEFAULT 5,
  `scheduling_finish_type` varchar(255) NOT NULL DEFAULT 'NEVER',
  `finish_date` date DEFAULT NULL,
  `total_installments` int(11) DEFAULT NULL,
  `paid_installments` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bill_payment_dues`
--

INSERT INTO `bill_payment_dues` (`id`, `bill_id`, `contract_id`, `payment_type`, `scheduling_type`, `scheduling_day`, `next_payment_date`, `tolerance_for_delay`, `scheduling_finish_type`, `finish_date`, `total_installments`, `paid_installments`, `created_at`, `updated_at`) VALUES
(2, 6, NULL, 'STATIC_DAY', 'ONE_TIME_BILL', 0, '2026-02-26', 0, 'ON', NULL, 0, 0, '2026-02-26 08:01:25', '2026-02-26 09:11:02'),
(3, 7, NULL, 'STATIC_DAY', 'DAILY', 0, '2026-03-04', 10, 'ON', '2026-03-19', 0, 0, '2026-03-04 19:08:25', '2026-03-04 19:08:25'),
(4, 9, NULL, 'STATIC_DAY', 'ONE_TIME_BILL', 0, '2026-03-05', 0, 'ON', NULL, 0, 0, '2026-03-05 06:49:30', '2026-03-05 06:49:30'),
(5, 10, NULL, 'STATIC_DAY', 'ONE_TIME_BILL', 0, '2026-03-05', 0, 'ON', NULL, 0, 0, '2026-03-05 10:34:49', '2026-03-05 10:34:49');

-- --------------------------------------------------------

--
-- Table structure for table `bill_short_links`
--

CREATE TABLE `bill_short_links` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bill_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(32) NOT NULL,
  `target_url` varchar(255) NOT NULL,
  `clicks_count` int(11) NOT NULL DEFAULT 0,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bill_short_links`
--

INSERT INTO `bill_short_links` (`id`, `bill_id`, `code`, `target_url`, `clicks_count`, `expires_at`, `created_at`, `updated_at`) VALUES
(4, 6, 'CfpUlAWa6MzR2N5s01', '/bills/pdf/eyJpdiI6ImJ4U0VqZjdqL3hxRVovc1BOMFhPbnc9PSIsInZhbHVlIjoidlFLNTBtVmFydDNUMnRGbXpXYUphUT09IiwibWFjIjoiYWFkOGY0MjA2M2IzZTE1ZGRjMTdhMmJiODVhZGFiZmY5ZjkyNmM4MWFlMDY2ZDQ2ZDQ3Yjk4OTA5NDNmMDQyNiIsInRhZyI6IiJ9', 0, NULL, '2026-02-26 08:01:25', '2026-02-26 08:01:25'),
(6, 6, 'MLK1cObkVdHt9XqI01', '/bills/eyJpdiI6ImRRNndmcnVKcFBFMDJ5Y1BJd29OWVE9PSIsInZhbHVlIjoiYTVmdVlLbWFocUJsV3ZWd0IvQVo0Zz09IiwibWFjIjoiZDVkNTc0YmFmYmRlYmFkZTM5M2I1MTg2ZTgzNTVmZmMxMWZjMTRlZTk5ZTgyNDA0MWNjMzJkYmE1MTQzNmZlNCIsInRhZyI6IiJ9/quick-payment-link', 0, NULL, '2026-02-26 09:11:02', '2026-02-26 09:11:02'),
(7, 7, 'w8BYmIcSLSqRREb701', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, NULL, '2026-03-03 22:00:00', '2026-03-03 22:00:00'),
(8, 7, 'IF89KnYYokXeZJ9b11', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, NULL, '2026-03-04 22:00:00', '2026-03-04 22:00:00'),
(9, 7, 'DA5cDNyRf55zk0qe21', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, NULL, '2026-03-05 22:00:00', '2026-03-05 22:00:00'),
(10, 7, 'jbMSHQd4kUup5ymN31', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, NULL, '2026-03-06 22:00:00', '2026-03-06 22:00:00'),
(11, 7, '7CkdY3BbMYj5ja2Y41', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, NULL, '2026-03-07 22:00:00', '2026-03-07 22:00:00'),
(12, 7, 'eZ4ho4UV3WYDqnW751', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, NULL, '2026-03-08 22:00:00', '2026-03-08 22:00:00'),
(13, 7, 'RpbTieYN6gRKBKK861', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, NULL, '2026-03-09 22:00:00', '2026-03-09 22:00:00'),
(14, 7, 'oYebJkbfnGExqLDE71', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, NULL, '2026-03-10 22:00:00', '2026-03-10 22:00:00'),
(15, 7, '07OP6inXUOLZJ7Dk81', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, NULL, '2026-03-11 22:00:00', '2026-03-11 22:00:00'),
(16, 7, 'oT4ZyzrhGrSRsos791', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, NULL, '2026-03-12 22:00:00', '2026-03-12 22:00:00'),
(17, 7, 'RPr2b5dYk6QXkAmV101', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, NULL, '2026-03-13 22:00:00', '2026-03-13 22:00:00'),
(18, 7, 'avE97nweQTW2lrtA111', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, NULL, '2026-03-14 22:00:00', '2026-03-14 22:00:00'),
(19, 7, 'BSnvKD1TM52OcggN121', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, NULL, '2026-03-15 22:00:00', '2026-03-15 22:00:00'),
(20, 7, 'ro6dJPVwBvonWwz5131', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, NULL, '2026-03-16 22:00:00', '2026-03-16 22:00:00'),
(21, 7, 'gogxfalnccmFpIoO141', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, NULL, '2026-03-17 22:00:00', '2026-03-17 22:00:00'),
(22, 7, 'H7Aon51dcGWKadOs01', '/bills/pdf/eyJpdiI6IjIzNTVmRnY5YnFWR0FpZ1RzYS9TdUE9PSIsInZhbHVlIjoicGxvOE9TUmdFci9nQndlT2E4MFJLZz09IiwibWFjIjoiMjczMWY5ZmVlMjU1YmQxN2JkNjljOTdmYTBiNWQ1NGYxNTFlZWE4Zjg3ZDllZDM5ODc0MTcxMTRhNTFiOWM4YyIsInRhZyI6IiJ9', 0, NULL, '2026-03-04 19:08:25', '2026-03-04 19:08:25'),
(23, 9, '5fmunHHzF0lCb0Uz01', '/bills/eyJpdiI6ImRCNEVFanRCUFNnMUFpSXZGSzRacHc9PSIsInZhbHVlIjoiamM3L1JXVHQzUFgxSE5YVUVKc3c1UT09IiwibWFjIjoiZWVjNjcxMTI0ZmQ0NDkyYjkwN2QzNjQ1ODE2YzMyOTQ1YzVjMDIyZDU3MWNlNjAzZTdlODAwYTI5Mzc5MTQ5MCIsInRhZyI6IiJ9/quick-payment-link', 0, NULL, '2026-03-05 06:49:30', '2026-03-05 06:49:30'),
(24, 9, 'e1rsXcQdNmr3iml301', '/bills/pdf/eyJpdiI6Inl3ZEhHeFpPc0RnSU5WaFdHRUFLSEE9PSIsInZhbHVlIjoibnZTSXRGT3V4Vm9jZGJTbmdzNmszQT09IiwibWFjIjoiZDI2NzMwYzZmNTdkMTJhNjM3YjIzZTZhN2I5ZmViZDI5NTFkZWI4YTlmMWRhNGVlN2FmNGFlM2E5MzEzYzkxNiIsInRhZyI6IiJ9', 0, NULL, '2026-03-05 06:49:30', '2026-03-05 06:49:30'),
(25, 10, 'GPmnXO1GPiFcTjqh01', '/bills/eyJpdiI6Ijg0NFlaWWEwa3F0VjFzbXlmWDRyWlE9PSIsInZhbHVlIjoiOE5KeUVuU250aENDU3VKVXFMZFVUUT09IiwibWFjIjoiZjkwZTNmN2NhZGY3NDhmZGMzMjUwZGIxYzBmNTgxOTA5YzFhOWMxYTliOTJkODU4NTU1NWU2ZmMxZTQ2Zjg0MCIsInRhZyI6IiJ9/quick-payment-link', 0, NULL, '2026-03-05 10:34:49', '2026-03-05 10:34:49'),
(26, 10, 'JvXibf5sy0X9UZnr01', '/bills/pdf/eyJpdiI6Ijh1Sld5L3JxS3JBUVBDeHg4aGxQTEE9PSIsInZhbHVlIjoibS9ucTArTXVHeDhIeEpWWDRUZVVsUT09IiwibWFjIjoiMzMwZTBhNGZjY2NhZjNhMTY5NTUwZmRkZDRkOGI3ZmE5ZTY4N2FlMzIwMDFjOWMwMDRiMTI2NjZkZDljZjNmMiIsInRhZyI6IiJ9', 0, NULL, '2026-03-05 10:34:49', '2026-03-05 10:34:49');

-- --------------------------------------------------------

--
-- Table structure for table `bill_transactions`
--

CREATE TABLE `bill_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bill_id` bigint(20) UNSIGNED NOT NULL,
  `payment_id` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `reference_id` varchar(255) DEFAULT NULL,
  `amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(10) NOT NULL DEFAULT 'KWD',
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_status` varchar(255) NOT NULL DEFAULT 'SUCCESS',
  `gateway_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gateway_response`)),
  `paid_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bill_translations`
--

CREATE TABLE `bill_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bill_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(10) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bill_translations`
--

INSERT INTO `bill_translations` (`id`, `bill_id`, `locale`, `title`, `body`, `created_at`, `updated_at`) VALUES
(1, 1, 'en', 'jsdhflsdhl', 'gfhghdf', '2026-11-08 07:55:10', '2026-11-08 07:55:10'),
(2, 1, 'ar', 'يبلليبل', 'بليبلبي', '2026-11-08 07:55:10', '2026-11-08 07:55:10'),
(9, 5, 'en', 'ناعاعخ', 'شسايخ', '2026-11-08 07:55:10', '2026-11-08 07:55:10'),
(10, 5, 'ar', 'اسيخهسش', 'شاسيه', '2026-11-08 07:55:10', '2026-11-08 07:55:10'),
(11, 6, 'en', 'new', 'new', '2026-11-08 07:55:10', '2026-11-08 07:55:10'),
(12, 6, 'ar', 'new', 'new', '2026-11-08 07:55:10', '2026-11-08 07:55:10'),
(13, 7, 'en', 'Et animi doloribus', 'Adipisicing omnis pa', '2026-11-08 07:55:10', '2026-11-08 07:55:10'),
(14, 7, 'ar', 'Doloribus voluptatum', 'Sed distinctio Opti', '2026-11-08 07:55:10', '2026-11-08 07:55:10'),
(15, 8, 'en', 'fatorra', NULL, '2026-11-08 07:55:10', '2026-11-08 07:55:10'),
(16, 8, 'ar', 'فاتورة سريعة', NULL, '2026-11-08 07:55:10', '2026-11-08 07:55:10'),
(17, 9, 'en', 'new', 'Ratione ullamco exce', '2026-11-08 07:55:10', '2026-11-08 07:55:10'),
(18, 9, 'ar', 'new', 'Doloribus cillum vol', '2026-11-08 07:55:10', '2026-11-08 07:55:10'),
(19, 10, 'en', 'desc', 'desc', '2026-11-08 07:55:10', '2026-11-08 07:55:10'),
(20, 10, 'ar', 'موضوع', 'عربي', '2026-11-08 07:55:10', '2026-11-08 07:55:10');

-- --------------------------------------------------------

--
-- Table structure for table `bill_warnings`
--

CREATE TABLE `bill_warnings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bill_id` bigint(20) UNSIGNED NOT NULL,
  `channel` varchar(255) NOT NULL DEFAULT 'WHATSAPP',
  `recipient` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'SENT',
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `biometric_devices`
--

CREATE TABLE `biometric_devices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `port` int(11) NOT NULL DEFAULT 4370,
  `device_serial_number` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `protocol` varchar(50) NOT NULL DEFAULT 'zkteco',
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `last_sync_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `government_id` bigint(20) UNSIGNED DEFAULT NULL,
  `city_id` bigint(20) UNSIGNED DEFAULT NULL,
  `manager_id` bigint(20) UNSIGNED DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `lat` decimal(10,8) DEFAULT NULL,
  `lng` decimal(11,8) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branch_translations`
--

CREATE TABLE `branch_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `buildings`
--

CREATE TABLE `buildings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `building_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `buildings`
--

INSERT INTO `buildings` (`id`, `building_type_id`, `user_id`, `image`, `location`, `order`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 1, NULL, 'test', 58, 1, '2026-02-23 19:42:27', '2026-02-24 06:34:03', NULL),
(2, 3, 1, NULL, 'الكويت 2', 0, 1, '2026-03-05 11:12:41', '2026-03-05 11:12:41', NULL),
(3, 2, 1, NULL, 'عقار 3', 0, 1, '2026-03-05 11:14:01', '2026-03-05 11:14:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `building_images`
--

CREATE TABLE `building_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `building_id` bigint(20) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `building_images`
--

INSERT INTO `building_images` (`id`, `building_id`, `image_path`, `order`, `created_at`, `updated_at`) VALUES
(28, 1, '2026-02-23-09-42-27-0699cc9c35230e.webp', 0, '2026-02-24 06:34:03', '2026-02-24 06:34:03'),
(72, 2, '2026-03-05-01-13-39-069a9818303d7c.webp', 0, '2026-03-05 11:13:39', '2026-03-05 11:13:39'),
(78, 3, '2026-03-05-01-14-01-069a981992711d.webp', 0, '2026-04-20 10:23:49', '2026-04-20 10:23:49');

-- --------------------------------------------------------

--
-- Table structure for table `building_translations`
--

CREATE TABLE `building_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `building_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(10) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `building_translations`
--

INSERT INTO `building_translations` (`id`, `building_id`, `locale`, `title`, `address`, `body`, `created_at`, `updated_at`) VALUES
(1, 1, 'en', 'Nihil voluptatem Si', 'Sed nemo culpa in r', 'Et nulla quaerat eli', '2026-11-08 07:55:09', '2026-11-08 07:55:09'),
(2, 1, 'ar', 'عقار رقم 1', 'الكويت', 'Totam occaecat paria', '2026-11-08 07:55:09', '2026-11-08 07:55:09'),
(3, 2, 'en', 'build 2', 'build 2', 'build 2', '2026-11-08 07:55:09', '2026-11-08 07:55:09'),
(4, 2, 'ar', 'عقار 2', 'الكويت 2', 'الكويت 2', '2026-11-08 07:55:09', '2026-11-08 07:55:09'),
(5, 3, 'en', 'build 3', 'build 3', 'build 3', '2026-11-08 07:55:09', '2026-11-08 07:55:09'),
(6, 3, 'ar', 'عقار 3', 'عقار 3', 'عقار 3', '2026-11-08 07:55:09', '2026-11-08 07:55:09');

-- --------------------------------------------------------

--
-- Table structure for table `building_types`
--

CREATE TABLE `building_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `building_types`
--

INSERT INTO `building_types` (`id`, `user_id`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 1, 1, '2026-02-23 18:48:08', '2026-02-23 18:48:08', NULL),
(3, 1, 1, '2026-02-23 18:48:21', '2026-02-23 18:48:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `building_type_translations`
--

CREATE TABLE `building_type_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `building_type_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(10) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `building_type_translations`
--

INSERT INTO `building_type_translations` (`id`, `building_type_id`, `locale`, `title`, `body`, `created_at`, `updated_at`) VALUES
(3, 2, 'en', 'Commercial real estate', 'Commercial real estate descriptopn', '2026-11-08 07:55:09', '2026-11-08 07:55:09'),
(4, 2, 'ar', 'عقار تجاري', 'وصف عقار تجاري', '2026-11-08 07:55:09', '2026-11-08 07:55:09'),
(5, 3, 'en', 'Investment property', 'Investment property', '2026-11-08 07:55:09', '2026-11-08 07:55:09'),
(6, 3, 'ar', 'عقار استثماري', 'وصف عقار استثماري', '2026-11-08 07:55:09', '2026-11-08 07:55:09');

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
-- Table structure for table `cash_accounts`
--

CREATE TABLE `cash_accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `entity_id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `journal_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  `code` varchar(20) NOT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `iban` varchar(50) DEFAULT NULL,
  `swift_code` varchar(30) DEFAULT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'SAR',
  `opening_balance` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `current_balance` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `responsible_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cash_account_translations`
--

CREATE TABLE `cash_account_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cash_account_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `company_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `country_code` varchar(10) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `date` varchar(255) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `user_id`, `company_type_id`, `email`, `password`, `country_code`, `mobile`, `contact_person`, `date`, `sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(3, 9, 1, 'ayman@dasman.com', '$2y$10$vaQZR.WKZLyYCL4zOX5L5.f5.1n7pIpG9BSQE6hr/InVpkJ7LHrTG', '+20', '+201090460119', 'ايمن', NULL, 0, 1, '2026-04-20 06:49:34', '2026-04-20 06:49:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `company_licenses`
--

CREATE TABLE `company_licenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `license_id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `vehicle_license_number` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_license_translations`
--

CREATE TABLE `company_license_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_license_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_translations`
--

CREATE TABLE `company_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(10) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `address` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company_translations`
--

INSERT INTO `company_translations` (`id`, `company_id`, `locale`, `title`, `body`, `address`) VALUES
(5, 3, 'en', NULL, NULL, NULL),
(6, 3, 'ar', 'شركة المستقبل', NULL, 'الكويت');

-- --------------------------------------------------------

--
-- Table structure for table `company_types`
--

CREATE TABLE `company_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `sort` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company_types`
--

INSERT INTO `company_types` (`id`, `status`, `sort`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 0, '2026-11-08 07:55:09', '2026-11-08 07:55:09', NULL),
(2, 1, 0, '2026-11-08 07:55:09', '2026-11-08 07:55:09', NULL),
(3, 1, 0, '2026-11-08 07:55:09', '2026-11-08 07:55:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `company_type_translations`
--

CREATE TABLE `company_type_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_type_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(10) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company_type_translations`
--

INSERT INTO `company_type_translations` (`id`, `company_type_id`, `locale`, `title`, `body`, `created_at`, `updated_at`) VALUES
(9, 1, 'ar', 'مستأجرة', NULL, '2026-11-08 07:55:09', '2026-11-08 07:55:09'),
(10, 1, 'en', 'rented', NULL, '2026-11-08 07:55:09', '2026-11-08 07:55:09'),
(11, 2, 'ar', 'مقاولات', NULL, '2026-11-08 07:55:09', '2026-11-08 07:55:09'),
(12, 2, 'en', 'Contracting', NULL, '2026-11-08 07:55:09', '2026-11-08 07:55:09'),
(13, 3, 'ar', 'صيانة', NULL, '2026-11-08 07:55:09', '2026-11-08 07:55:09'),
(14, 3, 'en', 'Maintenance', NULL, '2026-11-08 07:55:09', '2026-11-08 07:55:09');

-- --------------------------------------------------------

--
-- Table structure for table `contracts`
--

CREATE TABLE `contracts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `contract_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `building_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `bills_type` varchar(255) NOT NULL DEFAULT 'MONTHLY',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `contract_mode` varchar(255) NOT NULL DEFAULT 'rental',
  `payment_link_validity` varchar(255) DEFAULT 'never_expires',
  `paid_bills_count` int(11) NOT NULL DEFAULT 0,
  `send_invoice_type` varchar(255) NOT NULL DEFAULT 'both',
  `scheduling_finish_on_date` date DEFAULT NULL,
  `pay_after_days` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contract_translations`
--

CREATE TABLE `contract_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contract_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(10) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contract_types`
--

CREATE TABLE `contract_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contract_types`
--

INSERT INTO `contract_types` (`id`, `user_id`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, '2026-02-24 09:37:56', '2026-04-20 06:52:30', NULL),
(2, 1, 1, '2026-04-20 06:59:22', '2026-04-20 06:59:22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `contract_type_translations`
--

CREATE TABLE `contract_type_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contract_type_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(10) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contract_type_translations`
--

INSERT INTO `contract_type_translations` (`id`, `contract_type_id`, `locale`, `title`, `body`, `created_at`, `updated_at`) VALUES
(1, 1, 'en', 'quick contract', 'test', '2026-11-08 07:55:10', '2026-11-08 07:55:10'),
(2, 1, 'ar', 'عقد سريع', 'تيست', '2026-11-08 07:55:10', '2026-11-08 07:55:10'),
(3, 2, 'en', NULL, NULL, '2026-11-08 07:55:10', '2026-11-08 07:55:10'),
(4, 2, 'ar', 'عقد ايجار', NULL, '2026-11-08 07:55:10', '2026-11-08 07:55:10');

-- --------------------------------------------------------

--
-- Table structure for table `contract_units`
--

CREATE TABLE `contract_units` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contract_id` bigint(20) UNSIGNED NOT NULL,
  `unit_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `manager_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `department_translations`
--

CREATE TABLE `department_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_code` varchar(50) NOT NULL,
  `staff_type` varchar(50) NOT NULL DEFAULT 'employee',
  `is_teacher` tinyint(1) NOT NULL DEFAULT 0,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `gender` varchar(10) NOT NULL DEFAULT 'male',
  `marital_status` varchar(20) DEFAULT NULL,
  `religion` varchar(50) DEFAULT NULL,
  `nationality` varchar(50) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `national_id` varchar(50) DEFAULT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `visa_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `visa_expiry_date` date DEFAULT NULL,
  `passport_number` varchar(50) DEFAULT NULL,
  `passport_issue_date` date DEFAULT NULL,
  `passport_expiry_date` date DEFAULT NULL,
  `primary_branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `job_title_id` bigint(20) UNSIGNED DEFAULT NULL,
  `manager_id` bigint(20) UNSIGNED DEFAULT NULL,
  `work_schedule_id` bigint(20) UNSIGNED DEFAULT NULL,
  `shift_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employment_type` varchar(20) NOT NULL DEFAULT 'full_time',
  `hire_date` date NOT NULL,
  `probation_end_date` date DEFAULT NULL,
  `contract_end_date` date DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `phone` varchar(30) NOT NULL,
  `alternate_phone` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `governorate_id` bigint(20) UNSIGNED DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `emergency_contact_name` varchar(100) DEFAULT NULL,
  `emergency_contact_relation` varchar(50) DEFAULT NULL,
  `emergency_contact_phone` varchar(30) DEFAULT NULL,
  `bank_id` bigint(20) UNSIGNED DEFAULT NULL,
  `bank_branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `account_holder_name` varchar(150) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `iban` varchar(50) DEFAULT NULL,
  `basic_salary` decimal(10,2) NOT NULL DEFAULT 0.00,
  `hourly_rate` decimal(8,2) DEFAULT NULL,
  `daily_working_hours` decimal(4,2) NOT NULL DEFAULT 8.00,
  `social_insurance_number` varchar(50) DEFAULT NULL,
  `social_insurance_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_allowances`
--

CREATE TABLE `employee_allowances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `allowance_type_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_percentage` tinyint(1) NOT NULL DEFAULT 0,
  `is_recurring` tinyint(1) NOT NULL DEFAULT 1,
  `effective_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_branches`
--

CREATE TABLE `employee_branches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_documents`
--

CREATE TABLE `employee_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `document_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `document_number` varchar(100) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `issue_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `notify_before_days` smallint(5) UNSIGNED NOT NULL DEFAULT 30,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_notification_preferences`
--

CREATE TABLE `employee_notification_preferences` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `morning_reminder_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `morning_reminder_mins` smallint(5) UNSIGNED NOT NULL DEFAULT 15,
  `checkout_reminder_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `punch_success_alert` tinyint(1) NOT NULL DEFAULT 1,
  `payroll_alert_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `loans_alert_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `rewards_alert_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `leave_requests_alert` tinyint(1) NOT NULL DEFAULT 1,
  `permission_requests_alert` tinyint(1) NOT NULL DEFAULT 1,
  `admin_memos_alert` tinyint(1) NOT NULL DEFAULT 1,
  `policy_updates_alert` tinyint(1) NOT NULL DEFAULT 1,
  `push_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `whatsapp_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `email_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `quiet_hours_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `quiet_hours_start` time NOT NULL DEFAULT '20:00:00',
  `quiet_hours_end` time NOT NULL DEFAULT '07:00:00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_translations`
--

CREATE TABLE `employee_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address_1` varchar(255) DEFAULT NULL,
  `address_2` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `entities`
--

CREATE TABLE `entities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'company',
  `currency` varchar(10) NOT NULL DEFAULT 'KWD',
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `entities`
--

INSERT INTO `entities` (`id`, `company_id`, `code`, `type`, `currency`, `status`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, 'ENT-0001', 'company', 'KWD', 'active', 1, '2026-11-08 12:09:57', '2026-11-08 12:09:57', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `entity_translations`
--

CREATE TABLE `entity_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `entity_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `entity_translations`
--

INSERT INTO `entity_translations` (`id`, `entity_id`, `locale`, `name`, `description`) VALUES
(1, 1, 'ar', 'شركة المستقبل', 'الكيان المحاسبي المالي للشركة والعقارات'),
(2, 1, 'en', 'Real Estate & Corporate Services Co.', 'Financial Accounting Entity for Real Estate & Corporate Services');

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
-- Table structure for table `financial_vouchers`
--

CREATE TABLE `financial_vouchers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `voucher_type` varchar(255) NOT NULL DEFAULT 'receipt',
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `contract_id` bigint(20) UNSIGNED DEFAULT NULL,
  `due_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `voucher_date` date DEFAULT NULL,
  `check_number` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `voucher_number` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `financial_voucher_translations`
--

CREATE TABLE `financial_voucher_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `financial_voucher_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(255) NOT NULL,
  `to_mr` varchar(255) DEFAULT NULL,
  `amount_in_words` varchar(255) DEFAULT NULL,
  `subject` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fiscal_years`
--

CREATE TABLE `fiscal_years` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `entity_id` bigint(20) UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'open',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `closed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fiscal_year_translations`
--

CREATE TABLE `fiscal_year_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fiscal_year_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `general_settings`
--

CREATE TABLE `general_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `general_settings`
--

INSERT INTO `general_settings` (`id`, `type`, `parent_id`, `value`, `image`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'government', NULL, 'KWT-CAP', NULL, 1, NULL, '2026-11-08 07:36:59', '2026-11-08 07:36:59'),
(2, 'city', 1, 'SHARQ', NULL, 1, NULL, '2026-11-08 07:36:59', '2026-11-08 07:36:59'),
(3, 'city', 1, 'DASMAN', NULL, 1, NULL, '2026-11-08 07:36:59', '2026-11-08 07:36:59'),
(4, 'city', 1, 'SALHIYA', NULL, 1, NULL, '2026-11-08 07:36:59', '2026-11-08 07:36:59'),
(5, 'city', 1, 'MIRQAB', NULL, 1, NULL, '2026-11-08 07:36:59', '2026-11-08 07:36:59'),
(6, 'city', 1, 'BNEID_QAR', NULL, 1, NULL, '2026-11-08 07:37:00', '2026-11-08 07:37:00'),
(7, 'city', 1, 'KAIFAN', NULL, 1, NULL, '2026-11-08 07:37:00', '2026-11-08 07:37:00'),
(8, 'city', 1, 'YARMOUK', NULL, 1, NULL, '2026-11-08 07:37:00', '2026-11-08 07:37:00'),
(9, 'city', 1, 'SHAMIYA', NULL, 1, NULL, '2026-11-08 07:37:00', '2026-11-08 07:37:00'),
(10, 'city', 1, 'DASMA', NULL, 1, NULL, '2026-11-08 07:37:00', '2026-11-08 07:37:00'),
(11, 'city', 1, 'RAWDA', NULL, 1, NULL, '2026-11-08 07:37:00', '2026-11-08 07:37:00'),
(12, 'city', 1, 'ADAILIYA', NULL, 1, NULL, '2026-11-08 07:37:00', '2026-11-08 07:37:00'),
(13, 'city', 1, 'KHALDIYA', NULL, 1, NULL, '2026-11-08 07:37:01', '2026-11-08 07:37:01'),
(14, 'city', 1, 'QURTUBA', NULL, 1, NULL, '2026-11-08 07:37:01', '2026-11-08 07:37:01'),
(15, 'city', 1, 'SURRA', NULL, 1, NULL, '2026-11-08 07:37:01', '2026-11-08 07:37:01'),
(16, 'government', NULL, 'KWT-HAW', NULL, 1, NULL, '2026-11-08 07:37:01', '2026-11-08 07:37:01'),
(17, 'city', 16, 'SALMIYA', NULL, 1, NULL, '2026-11-08 07:37:01', '2026-11-08 07:37:01'),
(18, 'city', 16, 'HAWALLI', NULL, 1, NULL, '2026-11-08 07:37:01', '2026-11-08 07:37:01'),
(19, 'city', 16, 'RUMAITHIYA', NULL, 1, NULL, '2026-11-08 07:37:01', '2026-11-08 07:37:01'),
(20, 'city', 16, 'JABRIYA', NULL, 1, NULL, '2026-11-08 07:37:01', '2026-11-08 07:37:01'),
(21, 'city', 16, 'SALWA', NULL, 1, NULL, '2026-11-08 07:37:01', '2026-11-08 07:37:01'),
(22, 'city', 16, 'BAYAN', NULL, 1, NULL, '2026-11-08 07:37:01', '2026-11-08 07:37:01'),
(23, 'city', 16, 'MISHREF', NULL, 1, NULL, '2026-11-08 07:37:02', '2026-11-08 07:37:02'),
(24, 'city', 16, 'SHAAB', NULL, 1, NULL, '2026-11-08 07:37:02', '2026-11-08 07:37:02'),
(25, 'city', 16, 'SALAM', NULL, 1, NULL, '2026-11-08 07:37:02', '2026-11-08 07:37:02'),
(26, 'city', 16, 'HATEEN', NULL, 1, NULL, '2026-11-08 07:37:02', '2026-11-08 07:37:02'),
(27, 'city', 16, 'SHUHADA', NULL, 1, NULL, '2026-11-08 07:37:02', '2026-11-08 07:37:02'),
(28, 'city', 16, 'ZAHRA', NULL, 1, NULL, '2026-11-08 07:37:02', '2026-11-08 07:37:02'),
(29, 'city', 16, 'SIDDEEQ', NULL, 1, NULL, '2026-11-08 07:37:02', '2026-11-08 07:37:02'),
(30, 'government', NULL, 'KWT-FAR', NULL, 1, NULL, '2026-11-08 07:37:02', '2026-11-08 07:37:02'),
(31, 'city', 30, 'FARWANIYAH', NULL, 1, NULL, '2026-11-08 07:37:02', '2026-11-08 07:37:02'),
(32, 'city', 30, 'KHAITAN', NULL, 1, NULL, '2026-11-08 07:37:02', '2026-11-08 07:37:02'),
(33, 'city', 30, 'ANDALUS', NULL, 1, NULL, '2026-11-08 07:37:03', '2026-11-08 07:37:03'),
(34, 'city', 30, 'ISHBILIYA', NULL, 1, NULL, '2026-11-08 07:37:03', '2026-11-08 07:37:03'),
(35, 'city', 30, 'JLEEB', NULL, 1, NULL, '2026-11-08 07:37:03', '2026-11-08 07:37:03'),
(36, 'city', 30, 'SABAH_NASSER', NULL, 1, NULL, '2026-11-08 07:37:03', '2026-11-08 07:37:03'),
(37, 'city', 30, 'OMARIYA', NULL, 1, NULL, '2026-11-08 07:37:03', '2026-11-08 07:37:03'),
(38, 'city', 30, 'RABIYA', NULL, 1, NULL, '2026-11-08 07:37:03', '2026-11-08 07:37:03'),
(39, 'city', 30, 'ARDIYA', NULL, 1, NULL, '2026-11-08 07:37:03', '2026-11-08 07:37:03'),
(40, 'city', 30, 'FIRDOUS', NULL, 1, NULL, '2026-11-08 07:37:03', '2026-11-08 07:37:03'),
(41, 'city', 30, 'ABDULLAH_MUBARAK', NULL, 1, NULL, '2026-11-08 07:37:04', '2026-11-08 07:37:04'),
(42, 'government', NULL, 'KWT-AHM', NULL, 1, NULL, '2026-11-08 07:37:04', '2026-11-08 07:37:04'),
(43, 'city', 42, 'FAHAHEEL', NULL, 1, NULL, '2026-11-08 07:37:04', '2026-11-08 07:37:04'),
(44, 'city', 42, 'AHMADI', NULL, 1, NULL, '2026-11-08 07:37:04', '2026-11-08 07:37:04'),
(45, 'city', 42, 'MANGAF', NULL, 1, NULL, '2026-11-08 07:37:04', '2026-11-08 07:37:04'),
(46, 'city', 42, 'MAHBOULA', NULL, 1, NULL, '2026-11-08 07:37:04', '2026-11-08 07:37:04'),
(47, 'city', 42, 'ABU_HALIFA', NULL, 1, NULL, '2026-11-08 07:37:04', '2026-11-08 07:37:04'),
(48, 'city', 42, 'SABAHIYA', NULL, 1, NULL, '2026-11-08 07:37:05', '2026-11-08 07:37:05'),
(49, 'city', 42, 'RIQQAH', NULL, 1, NULL, '2026-11-08 07:37:05', '2026-11-08 07:37:05'),
(50, 'city', 42, 'SABAH_AHMAD', NULL, 1, NULL, '2026-11-08 07:37:05', '2026-11-08 07:37:05'),
(51, 'city', 42, 'KHIRAN', NULL, 1, NULL, '2026-11-08 07:37:05', '2026-11-08 07:37:05'),
(52, 'city', 42, 'WAFRA', NULL, 1, NULL, '2026-11-08 07:37:05', '2026-11-08 07:37:05'),
(53, 'city', 42, 'EGAILA', NULL, 1, NULL, '2026-11-08 07:37:05', '2026-11-08 07:37:05'),
(54, 'government', NULL, 'KWT-MUB', NULL, 1, NULL, '2026-11-08 07:37:05', '2026-11-08 07:37:05'),
(55, 'city', 54, 'MUBARAK_KABEER', NULL, 1, NULL, '2026-11-08 07:37:05', '2026-11-08 07:37:05'),
(56, 'city', 54, 'ADAN', NULL, 1, NULL, '2026-11-08 07:37:05', '2026-11-08 07:37:05'),
(57, 'city', 54, 'QUSOOR', NULL, 1, NULL, '2026-11-08 07:37:06', '2026-11-08 07:37:06'),
(58, 'city', 54, 'QURAIN', NULL, 1, NULL, '2026-11-08 07:37:06', '2026-11-08 07:37:06'),
(59, 'city', 54, 'SABAH_SALEM', NULL, 1, NULL, '2026-11-08 07:37:06', '2026-11-08 07:37:06'),
(60, 'city', 54, 'MESSILA', NULL, 1, NULL, '2026-11-08 07:37:06', '2026-11-08 07:37:06'),
(61, 'city', 54, 'ABU_HASANIYA', NULL, 1, NULL, '2026-11-08 07:37:06', '2026-11-08 07:37:06'),
(62, 'city', 54, 'FNAITEES', NULL, 1, NULL, '2026-11-08 07:37:06', '2026-11-08 07:37:06'),
(63, 'government', NULL, 'KWT-JAH', NULL, 1, NULL, '2026-11-08 07:37:06', '2026-11-08 07:37:06'),
(64, 'city', 63, 'JAHRA', NULL, 1, NULL, '2026-11-08 07:37:06', '2026-11-08 07:37:06'),
(65, 'city', 63, 'WAHA', NULL, 1, NULL, '2026-11-08 07:37:06', '2026-11-08 07:37:06'),
(66, 'city', 63, 'OYOUN', NULL, 1, NULL, '2026-11-08 07:37:07', '2026-11-08 07:37:07'),
(67, 'city', 63, 'QASR', NULL, 1, NULL, '2026-11-08 07:37:07', '2026-11-08 07:37:07'),
(68, 'city', 63, 'NASEEM', NULL, 1, NULL, '2026-11-08 07:37:07', '2026-11-08 07:37:07'),
(69, 'city', 63, 'TAIMAA', NULL, 1, NULL, '2026-11-08 07:37:07', '2026-11-08 07:37:07'),
(70, 'city', 63, 'SULAIBIYA', NULL, 1, NULL, '2026-11-08 07:37:07', '2026-11-08 07:37:07'),
(71, 'city', 63, 'SAAD_ABDULLAH', NULL, 1, NULL, '2026-11-08 07:37:07', '2026-11-08 07:37:07'),
(72, 'city', 63, 'MUTLAA', NULL, 1, NULL, '2026-11-08 07:37:07', '2026-11-08 07:37:07'),
(73, 'city', 63, 'KABAD', NULL, 1, NULL, '2026-11-08 07:37:07', '2026-11-08 07:37:07'),
(74, 'city', 63, 'ABDALI', NULL, 1, NULL, '2026-11-08 07:37:07', '2026-11-08 07:37:07'),
(75, 'religion', NULL, 'ISLAM', NULL, 1, NULL, '2026-11-08 07:37:07', '2026-11-08 07:37:07'),
(76, 'religion', NULL, 'CHRISTIAN', NULL, 1, NULL, '2026-11-08 07:37:07', '2026-11-08 07:37:07'),
(77, 'religion', NULL, 'OTHER', NULL, 1, NULL, '2026-11-08 07:37:08', '2026-11-08 07:37:08'),
(78, 'blood_type', NULL, 'A+', NULL, 1, NULL, '2026-11-08 07:37:08', '2026-11-08 07:37:08'),
(79, 'blood_type', NULL, 'A-', NULL, 1, NULL, '2026-11-08 07:37:08', '2026-11-08 07:37:08'),
(80, 'blood_type', NULL, 'B+', NULL, 1, NULL, '2026-11-08 07:37:08', '2026-11-08 07:37:08'),
(81, 'blood_type', NULL, 'B-', NULL, 1, NULL, '2026-11-08 07:37:08', '2026-11-08 07:37:08'),
(82, 'blood_type', NULL, 'O+', NULL, 1, NULL, '2026-11-08 07:37:08', '2026-11-08 07:37:08'),
(83, 'blood_type', NULL, 'O-', NULL, 1, NULL, '2026-11-08 07:37:08', '2026-11-08 07:37:08'),
(84, 'blood_type', NULL, 'AB+', NULL, 1, NULL, '2026-11-08 07:37:09', '2026-11-08 07:37:09'),
(85, 'blood_type', NULL, 'AB-', NULL, 1, NULL, '2026-11-08 07:37:09', '2026-11-08 07:37:09'),
(86, 'gender', NULL, 'MALE', NULL, 1, NULL, '2026-11-08 07:37:09', '2026-11-08 07:37:09'),
(87, 'gender', NULL, 'FEMALE', NULL, 1, NULL, '2026-11-08 07:37:09', '2026-11-08 07:37:09'),
(88, 'education_level', NULL, 'PRESCHOOL', NULL, 1, NULL, '2026-11-08 07:37:09', '2026-11-08 07:37:09'),
(89, 'education_level', NULL, 'PRIMARY', NULL, 1, NULL, '2026-11-08 07:37:09', '2026-11-08 07:37:09'),
(90, 'education_level', NULL, 'INTERMEDIATE', NULL, 1, NULL, '2026-11-08 07:37:09', '2026-11-08 07:37:09'),
(91, 'education_level', NULL, 'SECONDARY', NULL, 1, NULL, '2026-11-08 07:37:09', '2026-11-08 07:37:09'),
(92, 'education_level', NULL, 'DIPLOMA', NULL, 1, NULL, '2026-11-08 07:37:09', '2026-11-08 07:37:09'),
(93, 'education_level', NULL, 'BACHELOR', NULL, 1, NULL, '2026-11-08 07:37:10', '2026-11-08 07:37:10'),
(94, 'education_level', NULL, 'MASTER', NULL, 1, NULL, '2026-11-08 07:37:10', '2026-11-08 07:37:10'),
(95, 'education_level', NULL, 'DOCTORATE', NULL, 1, NULL, '2026-11-08 07:37:10', '2026-11-08 07:37:10'),
(96, 'relationship', NULL, 'FATHER', NULL, 1, NULL, '2026-11-08 07:37:10', '2026-11-08 07:37:10'),
(97, 'relationship', NULL, 'MOTHER', NULL, 1, NULL, '2026-11-08 07:37:10', '2026-11-08 07:37:10'),
(98, 'relationship', NULL, 'BROTHER', NULL, 1, NULL, '2026-11-08 07:37:10', '2026-11-08 07:37:10'),
(99, 'relationship', NULL, 'SISTER', NULL, 1, NULL, '2026-11-08 07:37:10', '2026-11-08 07:37:10'),
(100, 'relationship', NULL, 'GRANDFATHER', NULL, 1, NULL, '2026-11-08 07:37:10', '2026-11-08 07:37:10'),
(101, 'relationship', NULL, 'GRANDMOTHER', NULL, 1, NULL, '2026-11-08 07:37:10', '2026-11-08 07:37:10'),
(102, 'relationship', NULL, 'UNCLE_PATERNAL', NULL, 1, NULL, '2026-11-08 07:37:10', '2026-11-08 07:37:10'),
(103, 'relationship', NULL, 'AUNT_PATERNAL', NULL, 1, NULL, '2026-11-08 07:37:10', '2026-11-08 07:37:10'),
(104, 'relationship', NULL, 'UNCLE_MATERNAL', NULL, 1, NULL, '2026-11-08 07:37:11', '2026-11-08 07:37:11'),
(105, 'relationship', NULL, 'AUNT_MATERNAL', NULL, 1, NULL, '2026-11-08 07:37:11', '2026-11-08 07:37:11'),
(106, 'relationship', NULL, 'GUARDIAN', NULL, 1, NULL, '2026-11-08 07:37:11', '2026-11-08 07:37:11'),
(107, 'marital_status', NULL, 'SINGLE', NULL, 1, NULL, '2026-11-08 07:37:11', '2026-11-08 07:37:11'),
(108, 'marital_status', NULL, 'MARRIED', NULL, 1, NULL, '2026-11-08 07:37:11', '2026-11-08 07:37:11'),
(109, 'marital_status', NULL, 'DIVORCED', NULL, 1, NULL, '2026-11-08 07:37:12', '2026-11-08 07:37:12'),
(110, 'marital_status', NULL, 'WIDOWED', NULL, 1, NULL, '2026-11-08 07:37:12', '2026-11-08 07:37:12');

-- --------------------------------------------------------

--
-- Table structure for table `general_setting_translations`
--

CREATE TABLE `general_setting_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `general_setting_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `general_setting_translations`
--

INSERT INTO `general_setting_translations` (`id`, `general_setting_id`, `locale`, `name`, `description`) VALUES
(1, 1, 'ar', 'محافظة العاصمة', NULL),
(2, 1, 'en', 'Al Asimah (Capital)', NULL),
(3, 2, 'ar', 'شرق', NULL),
(4, 2, 'en', 'Sharq', NULL),
(5, 3, 'ar', 'دسمان', NULL),
(6, 3, 'en', 'Dasman', NULL),
(7, 4, 'ar', 'الصالحية', NULL),
(8, 4, 'en', 'Al Salhiya', NULL),
(9, 5, 'ar', 'المرقاب', NULL),
(10, 5, 'en', 'Al Mirqab', NULL),
(11, 6, 'ar', 'بنيد القار', NULL),
(12, 6, 'en', 'Bneid Al Qar', NULL),
(13, 7, 'ar', 'كيفان', NULL),
(14, 7, 'en', 'Kaifan', NULL),
(15, 8, 'ar', 'اليرموك', NULL),
(16, 8, 'en', 'Yarmouk', NULL),
(17, 9, 'ar', 'الشامية', NULL),
(18, 9, 'en', 'Shamiya', NULL),
(19, 10, 'ar', 'الدسمة', NULL),
(20, 10, 'en', 'Dasma', NULL),
(21, 11, 'ar', 'الروضة', NULL),
(22, 11, 'en', 'Rawda', NULL),
(23, 12, 'ar', 'العديلية', NULL),
(24, 12, 'en', 'Adailiya', NULL),
(25, 13, 'ar', 'الخالدية', NULL),
(26, 13, 'en', 'Khaldiya', NULL),
(27, 14, 'ar', 'قرطبة', NULL),
(28, 14, 'en', 'Qurtuba', NULL),
(29, 15, 'ar', 'السرة', NULL),
(30, 15, 'en', 'Surra', NULL),
(31, 16, 'ar', 'محافظة حولي', NULL),
(32, 16, 'en', 'Hawalli', NULL),
(33, 17, 'ar', 'السالمية', NULL),
(34, 17, 'en', 'Salmiya', NULL),
(35, 18, 'ar', 'حولي', NULL),
(36, 18, 'en', 'Hawalli', NULL),
(37, 19, 'ar', 'الرميثية', NULL),
(38, 19, 'en', 'Rumaithiya', NULL),
(39, 20, 'ar', 'الجابرية', NULL),
(40, 20, 'en', 'Jabriya', NULL),
(41, 21, 'ar', 'سلوى', NULL),
(42, 21, 'en', 'Salwa', NULL),
(43, 22, 'ar', 'بيان', NULL),
(44, 22, 'en', 'Bayan', NULL),
(45, 23, 'ar', 'مشرف', NULL),
(46, 23, 'en', 'Mishref', NULL),
(47, 24, 'ar', 'الشعب', NULL),
(48, 24, 'en', 'Shaab', NULL),
(49, 25, 'ar', 'السلام', NULL),
(50, 25, 'en', 'Al Salam', NULL),
(51, 26, 'ar', 'حطين', NULL),
(52, 26, 'en', 'Hateen', NULL),
(53, 27, 'ar', 'الشهداء', NULL),
(54, 27, 'en', 'Al Shuhada', NULL),
(55, 28, 'ar', 'الزهراء', NULL),
(56, 28, 'en', 'Al Zahra', NULL),
(57, 29, 'ar', 'الصديق', NULL),
(58, 29, 'en', 'Al Siddeeq', NULL),
(59, 30, 'ar', 'محافظة الفروانية', NULL),
(60, 30, 'en', 'Al Farwaniyah', NULL),
(61, 31, 'ar', 'الفروانية', NULL),
(62, 31, 'en', 'Al Farwaniyah', NULL),
(63, 32, 'ar', 'خيطان', NULL),
(64, 32, 'en', 'Khaitan', NULL),
(65, 33, 'ar', 'الأندلس', NULL),
(66, 33, 'en', 'Al Andalus', NULL),
(67, 34, 'ar', 'إشبيلية', NULL),
(68, 34, 'en', 'Ishbiliya', NULL),
(69, 35, 'ar', 'جليب الشيوخ', NULL),
(70, 35, 'en', 'Jleeb Al-Shuyoukh', NULL),
(71, 36, 'ar', 'صباح الناصر', NULL),
(72, 36, 'en', 'Sabah Al-Nasser', NULL),
(73, 37, 'ar', 'العمرية', NULL),
(74, 37, 'en', 'Al Omariya', NULL),
(75, 38, 'ar', 'الرابية', NULL),
(76, 38, 'en', 'Al Rabiya', NULL),
(77, 39, 'ar', 'العارضية', NULL),
(78, 39, 'en', 'Al Ardiya', NULL),
(79, 40, 'ar', 'الفردوس', NULL),
(80, 40, 'en', 'Al Firdous', NULL),
(81, 41, 'ar', 'عبدالله المبارك', NULL),
(82, 41, 'en', 'Abdullah Al Mubarak', NULL),
(83, 42, 'ar', 'محافظة الأحمدي', NULL),
(84, 42, 'en', 'Al Ahmadi', NULL),
(85, 43, 'ar', 'الفحيحيل', NULL),
(86, 43, 'en', 'Fahaheel', NULL),
(87, 44, 'ar', 'الأحمدي', NULL),
(88, 44, 'en', 'Al Ahmadi', NULL),
(89, 45, 'ar', 'المنقف', NULL),
(90, 45, 'en', 'Mangaf', NULL),
(91, 46, 'ar', 'المهبولة', NULL),
(92, 46, 'en', 'Mahboula', NULL),
(93, 47, 'ar', 'أبو حليفة', NULL),
(94, 47, 'en', 'Abu Halifa', NULL),
(95, 48, 'ar', 'الصباحية', NULL),
(96, 48, 'en', 'Al Sabahiya', NULL),
(97, 49, 'ar', 'الرقة', NULL),
(98, 49, 'en', 'Al Riqqah', NULL),
(99, 50, 'ar', 'مدينة صباح الأحمد', NULL),
(100, 50, 'en', 'Sabah Al Ahmad City', NULL),
(101, 51, 'ar', 'الخيران', NULL),
(102, 51, 'en', 'Al Khiran', NULL),
(103, 52, 'ar', 'الوفرة', NULL),
(104, 52, 'en', 'Al Wafra', NULL),
(105, 53, 'ar', 'العقيلة', NULL),
(106, 53, 'en', 'Egaila', NULL),
(107, 54, 'ar', 'محافظة مبارك الكبير', NULL),
(108, 54, 'en', 'Mubarak Al-Kabeer', NULL),
(109, 55, 'ar', 'مبارك الكبير', NULL),
(110, 55, 'en', 'Mubarak Al-Kabeer', NULL),
(111, 56, 'ar', 'العدان', NULL),
(112, 56, 'en', 'Al Adan', NULL),
(113, 57, 'ar', 'القصور', NULL),
(114, 57, 'en', 'Al Qusoor', NULL),
(115, 58, 'ar', 'القرين', NULL),
(116, 58, 'en', 'Al Qurain', NULL),
(117, 59, 'ar', 'صباح السالم', NULL),
(118, 59, 'en', 'Sabah Al Salem', NULL),
(119, 60, 'ar', 'المسيلة', NULL),
(120, 60, 'en', 'Al Messila', NULL),
(121, 61, 'ar', 'أبو الحصانية', NULL),
(122, 61, 'en', 'Abu Al Hasaniya', NULL),
(123, 62, 'ar', 'الفنيطيس', NULL),
(124, 62, 'en', 'Fnaitees', NULL),
(125, 63, 'ar', 'محافظة الجهراء', NULL),
(126, 63, 'en', 'Al Jahra', NULL),
(127, 64, 'ar', 'الجهراء', NULL),
(128, 64, 'en', 'Al Jahra', NULL),
(129, 65, 'ar', 'الواحة', NULL),
(130, 65, 'en', 'Al Waha', NULL),
(131, 66, 'ar', 'العيون', NULL),
(132, 66, 'en', 'Al Oyoun', NULL),
(133, 67, 'ar', 'القصر', NULL),
(134, 67, 'en', 'Al Qasr', NULL),
(135, 68, 'ar', 'النسيم', NULL),
(136, 68, 'en', 'Al Naseem', NULL),
(137, 69, 'ar', 'تيماء', NULL),
(138, 69, 'en', 'Taimaa', NULL),
(139, 70, 'ar', 'الصليبية', NULL),
(140, 70, 'en', 'Al Sulaibiya', NULL),
(141, 71, 'ar', 'مدينة سعد العبدالله', NULL),
(142, 71, 'en', 'Saad Al Abdullah City', NULL),
(143, 72, 'ar', 'المطلاع', NULL),
(144, 72, 'en', 'Al Mutlaa', NULL),
(145, 73, 'ar', 'كبد', NULL),
(146, 73, 'en', 'Kabad', NULL),
(147, 74, 'ar', 'العبدلي', NULL),
(148, 74, 'en', 'Al Abdali', NULL),
(149, 75, 'ar', 'مسلم', NULL),
(150, 75, 'en', 'Muslim', NULL),
(151, 76, 'ar', 'مسيحي', NULL),
(152, 76, 'en', 'Christian', NULL),
(153, 77, 'ar', 'ديانة أخرى', NULL),
(154, 77, 'en', 'Other Religion', NULL),
(155, 78, 'ar', 'A إيجابي (A+)', NULL),
(156, 78, 'en', 'A Positive (A+)', NULL),
(157, 79, 'ar', 'A سلبي (A-)', NULL),
(158, 79, 'en', 'A Negative (A-)', NULL),
(159, 80, 'ar', 'B إيجابي (B+)', NULL),
(160, 80, 'en', 'B Positive (B+)', NULL),
(161, 81, 'ar', 'B سلبي (B-)', NULL),
(162, 81, 'en', 'B Negative (B-)', NULL),
(163, 82, 'ar', 'O إيجابي (O+)', NULL),
(164, 82, 'en', 'O Positive (O+)', NULL),
(165, 83, 'ar', 'O سلبي (O-)', NULL),
(166, 83, 'en', 'O Negative (O-)', NULL),
(167, 84, 'ar', 'AB إيجابي (AB+)', NULL),
(168, 84, 'en', 'AB Positive (AB+)', NULL),
(169, 85, 'ar', 'AB سلبي (AB-)', NULL),
(170, 85, 'en', 'AB Negative (AB-)', NULL),
(171, 86, 'ar', 'ذكر', NULL),
(172, 86, 'en', 'Male', NULL),
(173, 87, 'ar', 'أنثى', NULL),
(174, 87, 'en', 'Female', NULL),
(175, 88, 'ar', 'تمهيدي / روضة', NULL),
(176, 88, 'en', 'Preschool / Kindergarten', NULL),
(177, 89, 'ar', 'المرحلة الابتدائية', NULL),
(178, 89, 'en', 'Primary School', NULL),
(179, 90, 'ar', 'المرحلة المتوسطة', NULL),
(180, 90, 'en', 'Intermediate School', NULL),
(181, 91, 'ar', 'المرحلة الثانوية', NULL),
(182, 91, 'en', 'Secondary / High School', NULL),
(183, 92, 'ar', 'دبلوم', NULL),
(184, 92, 'en', 'Diploma', NULL),
(185, 93, 'ar', 'بكالوريوس / ليسانس', NULL),
(186, 93, 'en', 'Bachelor\'s Degree', NULL),
(187, 94, 'ar', 'ماجستير', NULL),
(188, 94, 'en', 'Master\'s Degree', NULL),
(189, 95, 'ar', 'دكتوراه', NULL),
(190, 95, 'en', 'Doctorate / PhD', NULL),
(191, 96, 'ar', 'أب', NULL),
(192, 96, 'en', 'Father', NULL),
(193, 97, 'ar', 'أم', NULL),
(194, 97, 'en', 'Mother', NULL),
(195, 98, 'ar', 'أخ', NULL),
(196, 98, 'en', 'Brother', NULL),
(197, 99, 'ar', 'أخت', NULL),
(198, 99, 'en', 'Sister', NULL),
(199, 100, 'ar', 'جد', NULL),
(200, 100, 'en', 'Grandfather', NULL),
(201, 101, 'ar', 'جدة', NULL),
(202, 101, 'en', 'Grandmother', NULL),
(203, 102, 'ar', 'عم', NULL),
(204, 102, 'en', 'Uncle (Paternal)', NULL),
(205, 103, 'ar', 'عمة', NULL),
(206, 103, 'en', 'Aunt (Paternal)', NULL),
(207, 104, 'ar', 'خال', NULL),
(208, 104, 'en', 'Uncle (Maternal)', NULL),
(209, 105, 'ar', 'خالة', NULL),
(210, 105, 'en', 'Aunt (Maternal)', NULL),
(211, 106, 'ar', 'وصي قانوني', NULL),
(212, 106, 'en', 'Legal Guardian', NULL),
(213, 107, 'ar', 'أعزب / عزباء', NULL),
(214, 107, 'en', 'Single', NULL),
(215, 108, 'ar', 'متزوج / متزوجة', NULL),
(216, 108, 'en', 'Married', NULL),
(217, 109, 'ar', 'مطلق / مطلقة', NULL),
(218, 109, 'en', 'Divorced', NULL),
(219, 110, 'ar', 'أرمل / أرملة', NULL),
(220, 110, 'en', 'Widowed', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hr_candidates`
--

CREATE TABLE `hr_candidates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `current_job_title` varchar(255) DEFAULT NULL,
  `current_company` varchar(255) DEFAULT NULL,
  `total_experience_years` decimal(4,1) DEFAULT NULL,
  `linkedin_url` varchar(500) DEFAULT NULL,
  `portfolio_url` varchar(500) DEFAULT NULL,
  `resume_path` varchar(500) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_eos_rules`
--

CREATE TABLE `hr_eos_rules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'resignation',
  `name_ar` varchar(255) DEFAULT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `from_years` decimal(5,2) NOT NULL DEFAULT 0.00,
  `to_years` decimal(5,2) DEFAULT NULL,
  `percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_interviews`
--

CREATE TABLE `hr_interviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_application_id` bigint(20) UNSIGNED NOT NULL,
  `job_opening_id` bigint(20) UNSIGNED DEFAULT NULL,
  `candidate_id` bigint(20) UNSIGNED DEFAULT NULL,
  `interview_round` int(11) NOT NULL DEFAULT 1,
  `title` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'technical',
  `mode` varchar(255) NOT NULL DEFAULT 'online',
  `status` varchar(255) NOT NULL DEFAULT 'scheduled',
  `scheduled_at` datetime NOT NULL,
  `duration_minutes` int(11) NOT NULL DEFAULT 45,
  `location` varchar(255) DEFAULT NULL,
  `meeting_link` varchar(500) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `overall_score` decimal(3,2) DEFAULT NULL,
  `final_recommendation` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_interview_evaluations`
--

CREATE TABLE `hr_interview_evaluations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `interview_id` bigint(20) UNSIGNED NOT NULL,
  `interviewer_id` bigint(20) UNSIGNED NOT NULL,
  `technical_score` tinyint(3) UNSIGNED DEFAULT NULL,
  `communication_score` tinyint(3) UNSIGNED DEFAULT NULL,
  `problem_solving_score` tinyint(3) UNSIGNED DEFAULT NULL,
  `cultural_fit_score` tinyint(3) UNSIGNED DEFAULT NULL,
  `experience_score` tinyint(3) UNSIGNED DEFAULT NULL,
  `average_score` decimal(3,2) NOT NULL DEFAULT 0.00,
  `recommendation` varchar(255) NOT NULL DEFAULT 'neutral',
  `strengths` text DEFAULT NULL,
  `weaknesses` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_interview_interviewers`
--

CREATE TABLE `hr_interview_interviewers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `interview_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_job_applications`
--

CREATE TABLE `hr_job_applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `application_code` varchar(30) NOT NULL,
  `candidate_id` bigint(20) UNSIGNED NOT NULL,
  `job_opening_id` bigint(20) UNSIGNED NOT NULL,
  `stage` varchar(30) NOT NULL DEFAULT 'new',
  `status` varchar(30) NOT NULL DEFAULT 'active',
  `rating` tinyint(3) UNSIGNED DEFAULT NULL,
  `expected_salary` decimal(12,2) DEFAULT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'KWD',
  `notice_period_days` smallint(5) UNSIGNED DEFAULT NULL,
  `source` varchar(50) DEFAULT NULL,
  `cover_letter` text DEFAULT NULL,
  `rejection_reason` varchar(50) DEFAULT NULL,
  `rejection_notes` text DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `hired_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_job_offers`
--

CREATE TABLE `hr_job_offers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `offer_code` varchar(255) NOT NULL,
  `job_application_id` bigint(20) UNSIGNED NOT NULL,
  `candidate_id` bigint(20) UNSIGNED NOT NULL,
  `job_opening_id` bigint(20) UNSIGNED DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `job_title_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employment_type` varchar(255) NOT NULL DEFAULT 'full_time',
  `basic_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
  `housing_allowance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `transportation_allowance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `other_allowances` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(10) NOT NULL DEFAULT 'KWD',
  `offer_date` date NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `probation_months` smallint(5) UNSIGNED NOT NULL DEFAULT 3,
  `contract_duration_months` smallint(5) UNSIGNED DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `terms_and_conditions` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL,
  `responded_at` datetime DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_job_openings`
--

CREATE TABLE `hr_job_openings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `opening_code` varchar(50) NOT NULL,
  `job_requisition_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `job_title_id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employment_type` varchar(50) NOT NULL DEFAULT 'full_time',
  `workplace_type` varchar(50) NOT NULL DEFAULT 'on_site',
  `posting_type` varchar(50) NOT NULL DEFAULT 'both',
  `open_positions` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `filled_positions` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `min_experience_years` int(10) UNSIGNED DEFAULT NULL,
  `max_experience_years` int(10) UNSIGNED DEFAULT NULL,
  `education_level` varchar(255) DEFAULT NULL,
  `min_salary` decimal(12,3) DEFAULT NULL,
  `max_salary` decimal(12,3) DEFAULT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'KWD',
  `hide_salary` tinyint(1) NOT NULL DEFAULT 0,
  `publish_date` date DEFAULT NULL,
  `closing_date` date DEFAULT NULL,
  `job_summary` text DEFAULT NULL,
  `responsibilities` longtext DEFAULT NULL,
  `requirements` longtext DEFAULT NULL,
  `benefits` longtext DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'draft',
  `views_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `applications_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `published_by` bigint(20) UNSIGNED DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_job_opening_translations`
--

CREATE TABLE `hr_job_opening_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_opening_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(5) NOT NULL,
  `title` varchar(255) NOT NULL,
  `education_level` varchar(255) DEFAULT NULL,
  `job_summary` text DEFAULT NULL,
  `responsibilities` longtext DEFAULT NULL,
  `requirements` longtext DEFAULT NULL,
  `benefits` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_job_requisitions`
--

CREATE TABLE `hr_job_requisitions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `requisition_number` varchar(50) NOT NULL,
  `workforce_plan_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_budgeted` tinyint(1) NOT NULL DEFAULT 1,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `job_title_id` bigint(20) UNSIGNED NOT NULL,
  `requested_by` bigint(20) UNSIGNED NOT NULL,
  `requisition_type` varchar(30) NOT NULL DEFAULT 'expansion',
  `replacement_for_employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `headcount_needed` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `hired_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `employment_type` varchar(30) NOT NULL DEFAULT 'full_time',
  `expected_start_date` date DEFAULT NULL,
  `min_salary` decimal(10,3) DEFAULT NULL,
  `max_salary` decimal(10,3) DEFAULT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'KWD',
  `required_experience_years` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `education_level` varchar(100) DEFAULT NULL,
  `job_description` longtext DEFAULT NULL,
  `responsibilities` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `justification` text DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'draft',
  `priority` varchar(20) NOT NULL DEFAULT 'medium',
  `rejection_reason` text DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_leave_requests`
--

CREATE TABLE `hr_leave_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `leave_type` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `days_count` decimal(5,1) NOT NULL DEFAULT 1.0,
  `replacement_employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `reason` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `emergency_contact_phone` varchar(50) DEFAULT NULL,
  `manager_approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `manager_approved_at` timestamp NULL DEFAULT NULL,
  `hr_approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `hr_approved_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_loans`
--

CREATE TABLE `hr_loans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'installment_loan',
  `amount` decimal(10,2) NOT NULL,
  `installments_count` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `monthly_installment` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `total_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `remaining_amount` decimal(10,2) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `reason` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `disbursed_at` timestamp NULL DEFAULT NULL,
  `disbursement_method` varchar(255) DEFAULT NULL,
  `disbursement_reference` varchar(255) DEFAULT NULL,
  `manager_approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `manager_approved_at` timestamp NULL DEFAULT NULL,
  `hr_approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `hr_approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_loan_installments`
--

CREATE TABLE `hr_loan_installments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `loan_id` bigint(20) UNSIGNED NOT NULL,
  `installment_number` int(10) UNSIGNED NOT NULL,
  `due_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `paid_at` date DEFAULT NULL,
  `postponed_from` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payroll_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_onboardings`
--

CREATE TABLE `hr_onboardings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `onboarding_code` varchar(255) NOT NULL,
  `candidate_id` bigint(20) UNSIGNED NOT NULL,
  `job_application_id` bigint(20) UNSIGNED DEFAULT NULL,
  `job_offer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `job_title_id` bigint(20) UNSIGNED DEFAULT NULL,
  `buddy_employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `start_date` date NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `progress_percentage` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_onboarding_tasks`
--

CREATE TABLE `hr_onboarding_tasks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `onboarding_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL DEFAULT 'documentation',
  `is_completed` tinyint(1) NOT NULL DEFAULT 0,
  `completed_at` datetime DEFAULT NULL,
  `assigned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_payrolls`
--

CREATE TABLE `hr_payrolls` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payroll_number` varchar(255) NOT NULL,
  `payroll_month` varchar(7) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `total_basic_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_allowances` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_overtime` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_rewards` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_gross` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_deductions` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_loans_deduction` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_penalties_deduction` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_absence_deduction` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_net` decimal(12,2) NOT NULL DEFAULT 0.00,
  `employees_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `paid_by` bigint(20) UNSIGNED DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `cancelled_by` bigint(20) UNSIGNED DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_payroll_items`
--

CREATE TABLE `hr_payroll_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payroll_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `basic_salary` decimal(10,2) NOT NULL,
  `allowances_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `allowances_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`allowances_breakdown`)),
  `overtime_hours` decimal(6,2) NOT NULL DEFAULT 0.00,
  `overtime_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `rewards_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `other_earnings` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gross_salary` decimal(10,2) NOT NULL DEFAULT 0.00,
  `absence_days` decimal(5,2) NOT NULL DEFAULT 0.00,
  `absence_deduction` decimal(10,2) NOT NULL DEFAULT 0.00,
  `late_minutes` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `late_deduction` decimal(10,2) NOT NULL DEFAULT 0.00,
  `loans_deduction` decimal(10,2) NOT NULL DEFAULT 0.00,
  `penalties_deduction` decimal(10,2) NOT NULL DEFAULT 0.00,
  `insurance_deduction` decimal(10,2) NOT NULL DEFAULT 0.00,
  `other_deductions` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_deductions` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_salary` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bank_name` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `iban` varchar(255) DEFAULT NULL,
  `payment_status` varchar(255) NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_penalties`
--

CREATE TABLE `hr_penalties` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `penalty_number` varchar(255) NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `penalty_type_id` bigint(20) UNSIGNED NOT NULL,
  `calculation_type` varchar(255) NOT NULL DEFAULT 'days_deduction',
  `calculation_value` decimal(10,2) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `penalty_date` date NOT NULL,
  `deduction_method` varchar(255) NOT NULL DEFAULT 'next_payroll',
  `payroll_month` varchar(7) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `reason` text NOT NULL,
  `notes` text DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `deducted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deducted_at` timestamp NULL DEFAULT NULL,
  `cancelled_by` bigint(20) UNSIGNED DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_permission_requests`
--

CREATE TABLE `hr_permission_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `type` varchar(30) NOT NULL,
  `from_time` time NOT NULL,
  `to_time` time NOT NULL,
  `hours_count` decimal(4,2) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `is_emergency` tinyint(1) NOT NULL DEFAULT 0,
  `reason` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `financial_policy` varchar(50) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `manager_approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `manager_approved_at` timestamp NULL DEFAULT NULL,
  `hr_approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `hr_approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_rewards`
--

CREATE TABLE `hr_rewards` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reward_number` varchar(255) NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `reward_type_id` bigint(20) UNSIGNED NOT NULL,
  `calculation_type` varchar(255) NOT NULL DEFAULT 'fixed_amount',
  `calculation_value` decimal(10,2) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `award_date` date NOT NULL,
  `payment_method` varchar(255) NOT NULL DEFAULT 'next_payroll',
  `payroll_month` varchar(7) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `reason` text NOT NULL,
  `notes` text DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `paid_by` bigint(20) UNSIGNED DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `rejected_by` bigint(20) UNSIGNED DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_settings`
--

CREATE TABLE `hr_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_setting_translations`
--

CREATE TABLE `hr_setting_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hr_setting_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_sick_leave_tiers`
--

CREATE TABLE `hr_sick_leave_tiers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name_ar` varchar(255) NOT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `days_count` int(11) NOT NULL DEFAULT 0,
  `salary_percentage` decimal(5,2) NOT NULL DEFAULT 100.00,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_workforce_plans`
--

CREATE TABLE `hr_workforce_plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `plan_code` varchar(50) NOT NULL,
  `title` varchar(191) NOT NULL,
  `year` smallint(5) UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'draft',
  `total_target_headcount` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `total_budgeted_cost` decimal(14,3) NOT NULL DEFAULT 0.000,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_workforce_plan_items`
--

CREATE TABLE `hr_workforce_plan_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workforce_plan_id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `job_title_id` bigint(20) UNSIGNED NOT NULL,
  `employment_type` varchar(30) NOT NULL DEFAULT 'full_time',
  `current_headcount` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `target_headcount` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `vacancies_count` int(11) NOT NULL DEFAULT 1,
  `fulfilled_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `estimated_monthly_salary` decimal(10,3) NOT NULL DEFAULT 0.000,
  `estimated_total_cost` decimal(14,3) NOT NULL DEFAULT 0.000,
  `priority` varchar(20) NOT NULL DEFAULT 'medium',
  `target_quarter` varchar(15) NOT NULL DEFAULT 'annual',
  `justification` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_work_policies`
--

CREATE TABLE `hr_work_policies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `group` varchar(50) NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `type` varchar(30) NOT NULL DEFAULT 'string',
  `label_ar` varchar(255) DEFAULT NULL,
  `label_en` varchar(255) DEFAULT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`)),
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `imageables`
--

CREATE TABLE `imageables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `url` varchar(255) NOT NULL,
  `imageable_id` bigint(20) UNSIGNED NOT NULL,
  `imageable_type` varchar(255) NOT NULL,
  `type` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `job_titles`
--

CREATE TABLE `job_titles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_title_translations`
--

CREATE TABLE `job_title_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_title_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journals`
--

CREATE TABLE `journals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `entity_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(5) NOT NULL,
  `type` varchar(30) NOT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `sequence_prefix` varchar(50) DEFAULT NULL,
  `sequence_number_next` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `refund_sequence` tinyint(1) NOT NULL DEFAULT 0,
  `payment_sequence` tinyint(1) NOT NULL DEFAULT 0,
  `default_account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `suspense_account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `profit_account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `loss_account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `allow_manual_entries` tinyint(1) NOT NULL DEFAULT 1,
  `reconcile` tinyint(1) NOT NULL DEFAULT 0,
  `restrict_mode_hash_table` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journal_entries`
--

CREATE TABLE `journal_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `entity_id` bigint(20) UNSIGNED NOT NULL,
  `fiscal_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `journal_id` bigint(20) UNSIGNED NOT NULL,
  `entry_number` varchar(50) NOT NULL,
  `entry_date` date NOT NULL,
  `accounting_date` date DEFAULT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `entry_type` varchar(30) NOT NULL DEFAULT 'manual',
  `currency` varchar(10) NOT NULL DEFAULT 'KWD',
  `exchange_rate` decimal(12,6) NOT NULL DEFAULT 1.000000,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `total_debit` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `total_credit` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `is_balanced` tinyint(1) NOT NULL DEFAULT 1,
  `narration` text NOT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `posted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reversed_entry_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_reversal` tinyint(1) NOT NULL DEFAULT 0,
  `hash` varchar(128) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journal_entry_lines`
--

CREATE TABLE `journal_entry_lines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `journal_entry_id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `cost_center_id` bigint(20) UNSIGNED DEFAULT NULL,
  `partner_id` bigint(20) UNSIGNED DEFAULT NULL,
  `partner_type` varchar(100) DEFAULT NULL,
  `partner_name` varchar(255) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `debit` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `credit` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `currency` varchar(10) NOT NULL DEFAULT 'KWD',
  `amount_currency` decimal(15,4) DEFAULT NULL,
  `reconciled` tinyint(1) NOT NULL DEFAULT 0,
  `matching_number` varchar(50) DEFAULT NULL,
  `line_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journal_translations`
--

CREATE TABLE `journal_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `journal_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `licenses`
--

CREATE TABLE `licenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `licenses`
--

INSERT INTO `licenses` (`id`, `user_id`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, '2026-02-26', '2030-02-20', '1', '2026-02-26 10:08:05', '2026-02-26 10:08:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `license_translations`
--

CREATE TABLE `license_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `license_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `license_translations`
--

INSERT INTO `license_translations` (`id`, `license_id`, `locale`, `name`, `description`) VALUES
(1, 1, 'en', 'Accusantium ab est', 'Sed ea minim magnam'),
(2, 1, 'ar', 'Dolore omnis qui ad', 'Architecto ut omnis');

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
(4, '2026_09_15_114744_add_details_and_soft_deletes_to_users_table', 1),
(5, '2026_09_15_122500_create_settings_table', 1),
(6, '2026_09_15_122501_create_setting_translations_table', 1),
(7, '2026_09_15_125500_add_primary_color_to_settings_table', 1),
(8, '2026_09_16_101500_create_general_settings_table', 1),
(9, '2026_09_16_101501_create_general_setting_translations_table', 1),
(10, '2026_09_16_115000_create_imageables_and_attachmentables_tables', 1),
(11, '2026_09_16_120000_create_companies_table', 1),
(12, '2026_09_16_125000_create_company_types_table', 1),
(13, '2026_09_16_130000_create_branches_table', 1),
(14, '2026_09_16_140000_create_departments_table', 1),
(15, '2026_09_16_140001_create_department_translations_table', 1),
(16, '2026_09_16_141100_create_job_titles_table', 1),
(17, '2026_09_16_141101_create_job_title_translations_table', 1),
(18, '2026_09_16_152500_create_banks_table', 1),
(19, '2026_09_16_152501_create_bank_translations_table', 1),
(20, '2026_09_17_120000_create_hr_settings_table', 1),
(21, '2026_09_17_120001_create_hr_setting_translations_table', 1),
(22, '2026_09_17_140000_create_shifts_table', 1),
(23, '2026_09_17_140001_create_shift_translations_table', 1),
(24, '2026_09_17_150000_create_work_schedules_table', 1),
(25, '2026_09_17_150001_create_work_schedule_translations_table', 1),
(26, '2026_09_17_150002_create_work_schedule_days_table', 1),
(27, '2026_09_17_160000_create_official_holidays_table', 1),
(28, '2026_09_17_160001_create_official_holiday_translations_table', 1),
(29, '2026_09_17_170000_create_employees_table', 1),
(30, '2026_09_17_170001_create_employee_translations_table', 1),
(31, '2026_09_17_170002_create_employee_branches_table', 1),
(32, '2026_09_17_170003_create_employee_allowances_table', 1),
(33, '2026_09_17_170004_create_employee_documents_table', 1),
(34, '2026_09_17_170005_add_religion_to_employees_table', 1),
(35, '2026_09_18_180000_create_biometric_devices_table', 1),
(36, '2026_09_18_180001_create_attendance_raw_logs_table', 1),
(37, '2026_09_18_180002_create_attendances_table', 1),
(38, '2026_09_19_090000_create_hr_work_policies_table', 1),
(39, '2026_09_19_090001_create_hr_sick_leave_tiers_table', 1),
(40, '2026_09_19_090002_create_hr_eos_rules_table', 1),
(41, '2026_09_19_100000_create_hr_leave_requests_table', 1),
(42, '2026_09_19_100000_create_hr_permission_requests_table', 1),
(43, '2026_09_19_120000_create_hr_loans_table', 1),
(44, '2026_09_19_120001_create_hr_loan_installments_table', 1),
(45, '2026_09_19_140000_create_hr_rewards_table', 1),
(46, '2026_09_19_150000_create_hr_penalties_table', 1),
(47, '2026_09_19_160000_create_hr_payrolls_table', 1),
(48, '2026_09_19_160001_create_hr_payroll_items_table', 1),
(49, '2026_09_19_170000_create_hr_workforce_plans_table', 1),
(50, '2026_09_19_170001_create_hr_workforce_plan_items_table', 1),
(51, '2026_09_19_170002_create_hr_job_requisitions_table', 1),
(52, '2026_09_21_000001_create_permission_tables', 1),
(53, '2026_09_21_130000_create_hr_job_openings_table', 1),
(54, '2026_09_21_130001_create_hr_job_opening_translations_table', 1),
(55, '2026_09_21_140000_create_hr_candidates_and_applications_tables', 1),
(56, '2026_09_21_150000_create_hr_interviews_and_evaluations_tables', 1),
(57, '2026_09_21_160000_create_hr_job_offers_table', 1),
(58, '2026_09_21_170000_create_hr_onboardings_table', 1),
(59, '2026_09_22_000001_create_audit_log_settings_table', 1),
(60, '2026_09_22_000001_create_settings_print_templates_table', 1),
(61, '2026_09_22_101740_create_activity_log_table', 1),
(62, '2026_09_22_101741_add_event_column_to_activity_log_table', 1),
(63, '2026_09_22_101742_add_batch_uuid_column_to_activity_log_table', 1),
(64, '2026_09_22_150000_create_entities_table', 1),
(65, '2026_09_22_150001_create_entity_translations_table', 1),
(66, '2026_09_22_160000_create_accounts_table', 1),
(67, '2026_09_22_160001_create_account_translations_table', 1),
(68, '2026_09_23_095726_add_is_teacher_to_employees_table', 1),
(69, '2026_09_23_130000_add_staff_type_to_employees_table', 1),
(70, '2026_09_23_180000_create_ai_providers_table', 1),
(71, '2026_09_23_180001_create_ai_tasks_table', 1),
(72, '2026_09_23_180002_create_ai_task_providers_table', 1),
(73, '2026_09_23_180003_create_ai_usage_logs_table', 1),
(74, '2026_09_23_190000_update_gemini_default_model', 1),
(75, '2026_09_24_100000_create_fiscal_years_table', 1),
(76, '2026_09_24_100001_create_fiscal_year_translations_table', 1),
(77, '2026_09_24_110000_create_accounting_lock_dates_table', 1),
(78, '2026_09_24_110001_create_accounting_lock_exceptions_table', 1),
(79, '2026_09_24_120000_create_accounting_cost_centers_table', 1),
(80, '2026_09_24_120001_create_accounting_cost_center_translations_table', 1),
(81, '2026_09_26_100000_create_journals_table', 1),
(82, '2026_09_26_100001_create_journal_translations_table', 1),
(83, '2026_09_26_110000_create_journal_entries_table', 1),
(84, '2026_09_26_110001_create_journal_entry_lines_table', 1),
(85, '2026_09_27_000000_create_cash_accounts_table', 1),
(86, '2026_09_27_000001_add_name_ar_and_name_en_to_roles_table', 1),
(87, '2026_09_27_000001_create_cash_account_translations_table', 1),
(88, '2026_09_27_010000_create_bank_reconciliations_table', 1),
(89, '2026_09_27_010001_create_bank_reconciliation_items_table', 1),
(90, '2026_09_27_020000_create_accounting_payment_matchings_table', 1),
(91, '2026_09_27_021000_add_financial_assistant_task_to_ai_tasks', 1),
(92, '2026_10_04_143500_create_settings_email_templates_table', 1),
(93, '2026_10_07_120000_create_notification_templates_tables', 1),
(94, '2026_10_07_120100_create_notification_delivery_tables', 1),
(95, '2026_10_07_120200_add_payload_to_notification_batches_table', 1),
(96, '2026_10_12_160000_add_description_and_status_to_general_settings_tables', 1),
(97, '2026_10_18_100843_create_employee_notification_preferences_table', 1),
(98, '2026_10_20_073722_add_login_image_to_settings_table', 1),
(99, '2026_10_27_081550_create_contract_types_table', 1),
(100, '2026_10_27_091630_create_building_types_table', 1),
(101, '2026_10_27_093240_create_buildings_table', 1),
(102, '2026_10_27_100916_create_units_table', 1),
(103, '2026_10_27_104801_create_contracts_and_bills_tables', 1),
(104, '2026_10_27_122201_update_bills_and_create_bill_dues_transactions_table', 1),
(105, '2026_10_28_111109_create_vehicles_table', 1),
(106, '2026_10_28_120425_create_licenses_table', 1),
(107, '2026_10_28_121625_create_company_licenses_table', 1),
(108, '2026_10_28_123232_create_financial_vouchers_table', 1),
(109, '2026_10_29_150000_create_account_mappings_table', 1),
(110, '2026_11_08_131100_make_contract_id_nullable_on_bills_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_batches`
--

CREATE TABLE `notification_batches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `template_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `audience_type` varchar(50) NOT NULL,
  `audience_filter` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`audience_filter`)),
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `status` varchar(30) NOT NULL DEFAULT 'draft',
  `recipients_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_translations`
--

CREATE TABLE `notification_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `notification_id` char(36) NOT NULL,
  `locale` varchar(10) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `action_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `official_holidays`
--

CREATE TABLE `official_holidays` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `days_count` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `year` smallint(5) UNSIGNED NOT NULL,
  `is_recurring` tinyint(1) NOT NULL DEFAULT 0,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `color` varchar(20) NOT NULL DEFAULT '#e83e8c',
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `official_holiday_translations`
--

CREATE TABLE `official_holiday_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `official_holiday_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'nursery.view', 'web', '2026-11-08 07:36:35', '2026-11-08 07:36:35'),
(2, 'nursery.create', 'web', '2026-11-08 07:36:35', '2026-11-08 07:36:35'),
(3, 'nursery.edit', 'web', '2026-11-08 07:36:35', '2026-11-08 07:36:35'),
(4, 'nursery.delete', 'web', '2026-11-08 07:36:35', '2026-11-08 07:36:35'),
(5, 'nursery.toggle-status', 'web', '2026-11-08 07:36:35', '2026-11-08 07:36:35'),
(6, 'nursery.branches.view', 'web', '2026-11-08 07:36:35', '2026-11-08 07:36:35'),
(7, 'nursery.branches.create', 'web', '2026-11-08 07:36:36', '2026-11-08 07:36:36'),
(8, 'nursery.branches.edit', 'web', '2026-11-08 07:36:36', '2026-11-08 07:36:36'),
(9, 'nursery.branches.delete', 'web', '2026-11-08 07:36:36', '2026-11-08 07:36:36'),
(10, 'nursery.branches.toggle-status', 'web', '2026-11-08 07:36:36', '2026-11-08 07:36:36'),
(11, 'academic.years.view', 'web', '2026-11-08 07:36:36', '2026-11-08 07:36:36'),
(12, 'academic.years.create', 'web', '2026-11-08 07:36:36', '2026-11-08 07:36:36'),
(13, 'academic.years.edit', 'web', '2026-11-08 07:36:36', '2026-11-08 07:36:36'),
(14, 'academic.years.delete', 'web', '2026-11-08 07:36:36', '2026-11-08 07:36:36'),
(15, 'academic.years.toggle-status', 'web', '2026-11-08 07:36:36', '2026-11-08 07:36:36'),
(16, 'academic.years.set-current', 'web', '2026-11-08 07:36:36', '2026-11-08 07:36:36'),
(17, 'academic.stages.view', 'web', '2026-11-08 07:36:36', '2026-11-08 07:36:36'),
(18, 'academic.stages.create', 'web', '2026-11-08 07:36:36', '2026-11-08 07:36:36'),
(19, 'academic.stages.edit', 'web', '2026-11-08 07:36:36', '2026-11-08 07:36:36'),
(20, 'academic.stages.delete', 'web', '2026-11-08 07:36:36', '2026-11-08 07:36:36'),
(21, 'academic.stages.toggle-status', 'web', '2026-11-08 07:36:36', '2026-11-08 07:36:36'),
(22, 'academic.classes.view', 'web', '2026-11-08 07:36:36', '2026-11-08 07:36:36'),
(23, 'academic.classes.create', 'web', '2026-11-08 07:36:37', '2026-11-08 07:36:37'),
(24, 'academic.classes.edit', 'web', '2026-11-08 07:36:37', '2026-11-08 07:36:37'),
(25, 'academic.classes.delete', 'web', '2026-11-08 07:36:37', '2026-11-08 07:36:37'),
(26, 'academic.classes.toggle-status', 'web', '2026-11-08 07:36:37', '2026-11-08 07:36:37'),
(27, 'academic.subjects.view', 'web', '2026-11-08 07:36:37', '2026-11-08 07:36:37'),
(28, 'academic.subjects.create', 'web', '2026-11-08 07:36:37', '2026-11-08 07:36:37'),
(29, 'academic.subjects.edit', 'web', '2026-11-08 07:36:37', '2026-11-08 07:36:37'),
(30, 'academic.subjects.delete', 'web', '2026-11-08 07:36:37', '2026-11-08 07:36:37'),
(31, 'academic.subjects.toggle-status', 'web', '2026-11-08 07:36:37', '2026-11-08 07:36:37'),
(32, 'academic.subjects.clone', 'web', '2026-11-08 07:36:37', '2026-11-08 07:36:37'),
(33, 'academic.subjects.board', 'web', '2026-11-08 07:36:37', '2026-11-08 07:36:37'),
(34, 'academic.lessons.view', 'web', '2026-11-08 07:36:37', '2026-11-08 07:36:37'),
(35, 'academic.lessons.create', 'web', '2026-11-08 07:36:37', '2026-11-08 07:36:37'),
(36, 'academic.lessons.edit', 'web', '2026-11-08 07:36:37', '2026-11-08 07:36:37'),
(37, 'academic.lessons.delete', 'web', '2026-11-08 07:36:37', '2026-11-08 07:36:37'),
(38, 'academic.lessons.toggle-status', 'web', '2026-11-08 07:36:37', '2026-11-08 07:36:37'),
(39, 'academic.weekly-plans.view', 'web', '2026-11-08 07:36:37', '2026-11-08 07:36:37'),
(40, 'academic.weekly-plans.create', 'web', '2026-11-08 07:36:37', '2026-11-08 07:36:37'),
(41, 'academic.weekly-plans.edit', 'web', '2026-11-08 07:36:37', '2026-11-08 07:36:37'),
(42, 'academic.weekly-plans.delete', 'web', '2026-11-08 07:36:37', '2026-11-08 07:36:37'),
(43, 'academic.weekly-plans.toggle-status', 'web', '2026-11-08 07:36:37', '2026-11-08 07:36:37'),
(44, 'academic.monthly-plans.view', 'web', '2026-11-08 07:36:37', '2026-11-08 07:36:37'),
(45, 'academic.monthly-plans.create', 'web', '2026-11-08 07:36:37', '2026-11-08 07:36:37'),
(46, 'academic.monthly-plans.edit', 'web', '2026-11-08 07:36:38', '2026-11-08 07:36:38'),
(47, 'academic.monthly-plans.delete', 'web', '2026-11-08 07:36:38', '2026-11-08 07:36:38'),
(48, 'academic.monthly-plans.toggle-status', 'web', '2026-11-08 07:36:38', '2026-11-08 07:36:38'),
(49, 'academic.fee-types.view', 'web', '2026-11-08 07:36:38', '2026-11-08 07:36:38'),
(50, 'academic.fee-types.create', 'web', '2026-11-08 07:36:38', '2026-11-08 07:36:38'),
(51, 'academic.fee-types.edit', 'web', '2026-11-08 07:36:38', '2026-11-08 07:36:38'),
(52, 'academic.fee-types.delete', 'web', '2026-11-08 07:36:38', '2026-11-08 07:36:38'),
(53, 'academic.fee-types.toggle-status', 'web', '2026-11-08 07:36:38', '2026-11-08 07:36:38'),
(54, 'academic.fees.view', 'web', '2026-11-08 07:36:38', '2026-11-08 07:36:38'),
(55, 'academic.fees.create', 'web', '2026-11-08 07:36:38', '2026-11-08 07:36:38'),
(56, 'academic.fees.edit', 'web', '2026-11-08 07:36:38', '2026-11-08 07:36:38'),
(57, 'academic.fees.delete', 'web', '2026-11-08 07:36:38', '2026-11-08 07:36:38'),
(58, 'academic.fees.toggle-status', 'web', '2026-11-08 07:36:38', '2026-11-08 07:36:38'),
(59, 'academic.student-fees.view', 'web', '2026-11-08 07:36:38', '2026-11-08 07:36:38'),
(60, 'academic.student-fees.create', 'web', '2026-11-08 07:36:38', '2026-11-08 07:36:38'),
(61, 'academic.student-fees.payments.create', 'web', '2026-11-08 07:36:38', '2026-11-08 07:36:38'),
(62, 'hr.dashboard.view', 'web', '2026-11-08 07:36:38', '2026-11-08 07:36:38'),
(63, 'hr.employees.view', 'web', '2026-11-08 07:36:38', '2026-11-08 07:36:38'),
(64, 'hr.employees.create', 'web', '2026-11-08 07:36:38', '2026-11-08 07:36:38'),
(65, 'hr.employees.edit', 'web', '2026-11-08 07:36:38', '2026-11-08 07:36:38'),
(66, 'hr.employees.delete', 'web', '2026-11-08 07:36:39', '2026-11-08 07:36:39'),
(67, 'hr.employees.export', 'web', '2026-11-08 07:36:39', '2026-11-08 07:36:39'),
(68, 'hr.attendances.view', 'web', '2026-11-08 07:36:39', '2026-11-08 07:36:39'),
(69, 'hr.attendances.create', 'web', '2026-11-08 07:36:39', '2026-11-08 07:36:39'),
(70, 'hr.attendances.edit', 'web', '2026-11-08 07:36:39', '2026-11-08 07:36:39'),
(71, 'hr.attendances.delete', 'web', '2026-11-08 07:36:39', '2026-11-08 07:36:39'),
(72, 'hr.attendances.import', 'web', '2026-11-08 07:36:39', '2026-11-08 07:36:39'),
(73, 'hr.attendances.export', 'web', '2026-11-08 07:36:39', '2026-11-08 07:36:39'),
(74, 'hr.leaves.view', 'web', '2026-11-08 07:36:39', '2026-11-08 07:36:39'),
(75, 'hr.leaves.create', 'web', '2026-11-08 07:36:39', '2026-11-08 07:36:39'),
(76, 'hr.leaves.edit', 'web', '2026-11-08 07:36:39', '2026-11-08 07:36:39'),
(77, 'hr.leaves.delete', 'web', '2026-11-08 07:36:39', '2026-11-08 07:36:39'),
(78, 'hr.leaves.approve', 'web', '2026-11-08 07:36:39', '2026-11-08 07:36:39'),
(79, 'hr.leaves.reject', 'web', '2026-11-08 07:36:39', '2026-11-08 07:36:39'),
(80, 'hr.permissions.view', 'web', '2026-11-08 07:36:39', '2026-11-08 07:36:39'),
(81, 'hr.permissions.create', 'web', '2026-11-08 07:36:39', '2026-11-08 07:36:39'),
(82, 'hr.permissions.edit', 'web', '2026-11-08 07:36:39', '2026-11-08 07:36:39'),
(83, 'hr.permissions.delete', 'web', '2026-11-08 07:36:39', '2026-11-08 07:36:39'),
(84, 'hr.permissions.approve', 'web', '2026-11-08 07:36:39', '2026-11-08 07:36:39'),
(85, 'hr.permissions.reject', 'web', '2026-11-08 07:36:39', '2026-11-08 07:36:39'),
(86, 'hr.payrolls.view', 'web', '2026-11-08 07:36:39', '2026-11-08 07:36:39'),
(87, 'hr.payrolls.create', 'web', '2026-11-08 07:36:39', '2026-11-08 07:36:39'),
(88, 'hr.payrolls.edit', 'web', '2026-11-08 07:36:39', '2026-11-08 07:36:39'),
(89, 'hr.payrolls.delete', 'web', '2026-11-08 07:36:39', '2026-11-08 07:36:39'),
(90, 'hr.payrolls.recalculate', 'web', '2026-11-08 07:36:39', '2026-11-08 07:36:39'),
(91, 'hr.payrolls.approve', 'web', '2026-11-08 07:36:39', '2026-11-08 07:36:39'),
(92, 'hr.payrolls.disburse', 'web', '2026-11-08 07:36:40', '2026-11-08 07:36:40'),
(93, 'hr.payrolls.cancel', 'web', '2026-11-08 07:36:40', '2026-11-08 07:36:40'),
(94, 'hr.payrolls.export-wps', 'web', '2026-11-08 07:36:40', '2026-11-08 07:36:40'),
(95, 'hr.loans.view', 'web', '2026-11-08 07:36:40', '2026-11-08 07:36:40'),
(96, 'hr.loans.create', 'web', '2026-11-08 07:36:40', '2026-11-08 07:36:40'),
(97, 'hr.loans.edit', 'web', '2026-11-08 07:36:40', '2026-11-08 07:36:40'),
(98, 'hr.loans.delete', 'web', '2026-11-08 07:36:40', '2026-11-08 07:36:40'),
(99, 'hr.loans.approve', 'web', '2026-11-08 07:36:40', '2026-11-08 07:36:40'),
(100, 'hr.loans.reject', 'web', '2026-11-08 07:36:40', '2026-11-08 07:36:40'),
(101, 'hr.loans.disburse', 'web', '2026-11-08 07:36:40', '2026-11-08 07:36:40'),
(102, 'hr.loans.postpone', 'web', '2026-11-08 07:36:40', '2026-11-08 07:36:40'),
(103, 'hr.loans.pay-installment', 'web', '2026-11-08 07:36:40', '2026-11-08 07:36:40'),
(104, 'hr.rewards.view', 'web', '2026-11-08 07:36:40', '2026-11-08 07:36:40'),
(105, 'hr.rewards.create', 'web', '2026-11-08 07:36:40', '2026-11-08 07:36:40'),
(106, 'hr.rewards.edit', 'web', '2026-11-08 07:36:40', '2026-11-08 07:36:40'),
(107, 'hr.rewards.delete', 'web', '2026-11-08 07:36:40', '2026-11-08 07:36:40'),
(108, 'hr.rewards.approve', 'web', '2026-11-08 07:36:40', '2026-11-08 07:36:40'),
(109, 'hr.rewards.reject', 'web', '2026-11-08 07:36:40', '2026-11-08 07:36:40'),
(110, 'hr.rewards.pay', 'web', '2026-11-08 07:36:40', '2026-11-08 07:36:40'),
(111, 'hr.penalties.view', 'web', '2026-11-08 07:36:40', '2026-11-08 07:36:40'),
(112, 'hr.penalties.create', 'web', '2026-11-08 07:36:40', '2026-11-08 07:36:40'),
(113, 'hr.penalties.edit', 'web', '2026-11-08 07:36:40', '2026-11-08 07:36:40'),
(114, 'hr.penalties.delete', 'web', '2026-11-08 07:36:41', '2026-11-08 07:36:41'),
(115, 'hr.penalties.approve', 'web', '2026-11-08 07:36:41', '2026-11-08 07:36:41'),
(116, 'hr.penalties.deduct', 'web', '2026-11-08 07:36:41', '2026-11-08 07:36:41'),
(117, 'hr.penalties.cancel', 'web', '2026-11-08 07:36:41', '2026-11-08 07:36:41'),
(118, 'hr.workforce-plans.view', 'web', '2026-11-08 07:36:41', '2026-11-08 07:36:41'),
(119, 'hr.workforce-plans.create', 'web', '2026-11-08 07:36:41', '2026-11-08 07:36:41'),
(120, 'hr.workforce-plans.edit', 'web', '2026-11-08 07:36:41', '2026-11-08 07:36:41'),
(121, 'hr.workforce-plans.delete', 'web', '2026-11-08 07:36:41', '2026-11-08 07:36:41'),
(122, 'hr.workforce-plans.submit', 'web', '2026-11-08 07:36:41', '2026-11-08 07:36:41'),
(123, 'hr.workforce-plans.approve', 'web', '2026-11-08 07:36:41', '2026-11-08 07:36:41'),
(124, 'hr.workforce-plans.activate', 'web', '2026-11-08 07:36:41', '2026-11-08 07:36:41'),
(125, 'hr.workforce-plans.close', 'web', '2026-11-08 07:36:41', '2026-11-08 07:36:41'),
(126, 'hr.workforce-plans.recalculate', 'web', '2026-11-08 07:36:41', '2026-11-08 07:36:41'),
(127, 'hr.job-requisitions.view', 'web', '2026-11-08 07:36:41', '2026-11-08 07:36:41'),
(128, 'hr.job-requisitions.create', 'web', '2026-11-08 07:36:41', '2026-11-08 07:36:41'),
(129, 'hr.job-requisitions.edit', 'web', '2026-11-08 07:36:41', '2026-11-08 07:36:41'),
(130, 'hr.job-requisitions.delete', 'web', '2026-11-08 07:36:41', '2026-11-08 07:36:41'),
(131, 'hr.job-requisitions.submit', 'web', '2026-11-08 07:36:41', '2026-11-08 07:36:41'),
(132, 'hr.job-requisitions.approve', 'web', '2026-11-08 07:36:41', '2026-11-08 07:36:41'),
(133, 'hr.job-requisitions.reject', 'web', '2026-11-08 07:36:41', '2026-11-08 07:36:41'),
(134, 'hr.job-requisitions.start-recruitment', 'web', '2026-11-08 07:36:41', '2026-11-08 07:36:41'),
(135, 'hr.job-requisitions.cancel', 'web', '2026-11-08 07:36:41', '2026-11-08 07:36:41'),
(136, 'hr.job-openings.view', 'web', '2026-11-08 07:36:41', '2026-11-08 07:36:41'),
(137, 'hr.job-openings.create', 'web', '2026-11-08 07:36:41', '2026-11-08 07:36:41'),
(138, 'hr.job-openings.edit', 'web', '2026-11-08 07:36:42', '2026-11-08 07:36:42'),
(139, 'hr.job-openings.delete', 'web', '2026-11-08 07:36:42', '2026-11-08 07:36:42'),
(140, 'hr.job-openings.publish', 'web', '2026-11-08 07:36:42', '2026-11-08 07:36:42'),
(141, 'hr.job-openings.close', 'web', '2026-11-08 07:36:42', '2026-11-08 07:36:42'),
(142, 'hr.job-applications.view', 'web', '2026-11-08 07:36:42', '2026-11-08 07:36:42'),
(143, 'hr.job-applications.create', 'web', '2026-11-08 07:36:42', '2026-11-08 07:36:42'),
(144, 'hr.job-applications.edit', 'web', '2026-11-08 07:36:42', '2026-11-08 07:36:42'),
(145, 'hr.job-applications.delete', 'web', '2026-11-08 07:36:42', '2026-11-08 07:36:42'),
(146, 'hr.job-applications.update-stage', 'web', '2026-11-08 07:36:42', '2026-11-08 07:36:42'),
(147, 'hr.job-applications.reject', 'web', '2026-11-08 07:36:42', '2026-11-08 07:36:42'),
(148, 'hr.job-applications.reopen', 'web', '2026-11-08 07:36:42', '2026-11-08 07:36:42'),
(149, 'hr.job-applications.download-resume', 'web', '2026-11-08 07:36:42', '2026-11-08 07:36:42'),
(150, 'hr.interviews.view', 'web', '2026-11-08 07:36:42', '2026-11-08 07:36:42'),
(151, 'hr.interviews.create', 'web', '2026-11-08 07:36:42', '2026-11-08 07:36:42'),
(152, 'hr.interviews.edit', 'web', '2026-11-08 07:36:42', '2026-11-08 07:36:42'),
(153, 'hr.interviews.delete', 'web', '2026-11-08 07:36:42', '2026-11-08 07:36:42'),
(154, 'hr.interviews.evaluate', 'web', '2026-11-08 07:36:42', '2026-11-08 07:36:42'),
(155, 'hr.interviews.cancel', 'web', '2026-11-08 07:36:42', '2026-11-08 07:36:42'),
(156, 'hr.job-offers.view', 'web', '2026-11-08 07:36:42', '2026-11-08 07:36:42'),
(157, 'hr.job-offers.create', 'web', '2026-11-08 07:36:42', '2026-11-08 07:36:42'),
(158, 'hr.job-offers.edit', 'web', '2026-11-08 07:36:42', '2026-11-08 07:36:42'),
(159, 'hr.job-offers.delete', 'web', '2026-11-08 07:36:42', '2026-11-08 07:36:42'),
(160, 'hr.job-offers.submit', 'web', '2026-11-08 07:36:42', '2026-11-08 07:36:42'),
(161, 'hr.job-offers.approve', 'web', '2026-11-08 07:36:42', '2026-11-08 07:36:42'),
(162, 'hr.job-offers.send', 'web', '2026-11-08 07:36:42', '2026-11-08 07:36:42'),
(163, 'hr.job-offers.accept', 'web', '2026-11-08 07:36:42', '2026-11-08 07:36:42'),
(164, 'hr.job-offers.decline', 'web', '2026-11-08 07:36:43', '2026-11-08 07:36:43'),
(165, 'hr.job-offers.cancel', 'web', '2026-11-08 07:36:43', '2026-11-08 07:36:43'),
(166, 'hr.onboardings.view', 'web', '2026-11-08 07:36:43', '2026-11-08 07:36:43'),
(167, 'hr.onboardings.create', 'web', '2026-11-08 07:36:43', '2026-11-08 07:36:43'),
(168, 'hr.onboardings.edit', 'web', '2026-11-08 07:36:43', '2026-11-08 07:36:43'),
(169, 'hr.onboardings.delete', 'web', '2026-11-08 07:36:43', '2026-11-08 07:36:43'),
(170, 'hr.onboardings.tasks', 'web', '2026-11-08 07:36:43', '2026-11-08 07:36:43'),
(171, 'hr.onboardings.convert', 'web', '2026-11-08 07:36:43', '2026-11-08 07:36:43'),
(172, 'hr.reports.view', 'web', '2026-11-08 07:36:43', '2026-11-08 07:36:43'),
(173, 'hr.reports.attendance.view', 'web', '2026-11-08 07:36:43', '2026-11-08 07:36:43'),
(174, 'hr.reports.attendance.export', 'web', '2026-11-08 07:36:43', '2026-11-08 07:36:43'),
(175, 'hr.reports.attendance.print', 'web', '2026-11-08 07:36:43', '2026-11-08 07:36:43'),
(176, 'hr.reports.leaves.view', 'web', '2026-11-08 07:36:43', '2026-11-08 07:36:43'),
(177, 'hr.reports.leaves.export', 'web', '2026-11-08 07:36:43', '2026-11-08 07:36:43'),
(178, 'hr.reports.leaves.print', 'web', '2026-11-08 07:36:43', '2026-11-08 07:36:43'),
(179, 'hr.reports.payrolls.view', 'web', '2026-11-08 07:36:43', '2026-11-08 07:36:43'),
(180, 'hr.reports.payrolls.export', 'web', '2026-11-08 07:36:43', '2026-11-08 07:36:43'),
(181, 'hr.reports.payrolls.print', 'web', '2026-11-08 07:36:43', '2026-11-08 07:36:43'),
(182, 'hr.reports.payrolls.payslip', 'web', '2026-11-08 07:36:43', '2026-11-08 07:36:43'),
(183, 'hr.reports.employees.view', 'web', '2026-11-08 07:36:43', '2026-11-08 07:36:43'),
(184, 'hr.reports.employees.export', 'web', '2026-11-08 07:36:44', '2026-11-08 07:36:44'),
(185, 'hr.reports.employees.print', 'web', '2026-11-08 07:36:44', '2026-11-08 07:36:44'),
(186, 'hr.settings.administrations.view', 'web', '2026-11-08 07:36:44', '2026-11-08 07:36:44'),
(187, 'hr.settings.administrations.create', 'web', '2026-11-08 07:36:44', '2026-11-08 07:36:44'),
(188, 'hr.settings.administrations.edit', 'web', '2026-11-08 07:36:44', '2026-11-08 07:36:44'),
(189, 'hr.settings.administrations.delete', 'web', '2026-11-08 07:36:44', '2026-11-08 07:36:44'),
(190, 'hr.settings.departments.view', 'web', '2026-11-08 07:36:44', '2026-11-08 07:36:44'),
(191, 'hr.settings.departments.create', 'web', '2026-11-08 07:36:44', '2026-11-08 07:36:44'),
(192, 'hr.settings.departments.edit', 'web', '2026-11-08 07:36:44', '2026-11-08 07:36:44'),
(193, 'hr.settings.departments.delete', 'web', '2026-11-08 07:36:44', '2026-11-08 07:36:44'),
(194, 'hr.settings.job-titles.view', 'web', '2026-11-08 07:36:44', '2026-11-08 07:36:44'),
(195, 'hr.settings.job-titles.create', 'web', '2026-11-08 07:36:44', '2026-11-08 07:36:44'),
(196, 'hr.settings.job-titles.edit', 'web', '2026-11-08 07:36:44', '2026-11-08 07:36:44'),
(197, 'hr.settings.job-titles.delete', 'web', '2026-11-08 07:36:44', '2026-11-08 07:36:44'),
(198, 'hr.settings.allowance-types.view', 'web', '2026-11-08 07:36:44', '2026-11-08 07:36:44'),
(199, 'hr.settings.allowance-types.create', 'web', '2026-11-08 07:36:44', '2026-11-08 07:36:44'),
(200, 'hr.settings.allowance-types.edit', 'web', '2026-11-08 07:36:44', '2026-11-08 07:36:44'),
(201, 'hr.settings.allowance-types.delete', 'web', '2026-11-08 07:36:44', '2026-11-08 07:36:44'),
(202, 'hr.settings.document-types.view', 'web', '2026-11-08 07:36:44', '2026-11-08 07:36:44'),
(203, 'hr.settings.document-types.create', 'web', '2026-11-08 07:36:44', '2026-11-08 07:36:44'),
(204, 'hr.settings.document-types.edit', 'web', '2026-11-08 07:36:44', '2026-11-08 07:36:44'),
(205, 'hr.settings.document-types.delete', 'web', '2026-11-08 07:36:44', '2026-11-08 07:36:44'),
(206, 'hr.settings.reward-types.view', 'web', '2026-11-08 07:36:44', '2026-11-08 07:36:44'),
(207, 'hr.settings.reward-types.create', 'web', '2026-11-08 07:36:44', '2026-11-08 07:36:44'),
(208, 'hr.settings.reward-types.edit', 'web', '2026-11-08 07:36:44', '2026-11-08 07:36:44'),
(209, 'hr.settings.reward-types.delete', 'web', '2026-11-08 07:36:44', '2026-11-08 07:36:44'),
(210, 'hr.settings.penalty-types.view', 'web', '2026-11-08 07:36:45', '2026-11-08 07:36:45'),
(211, 'hr.settings.penalty-types.create', 'web', '2026-11-08 07:36:45', '2026-11-08 07:36:45'),
(212, 'hr.settings.penalty-types.edit', 'web', '2026-11-08 07:36:45', '2026-11-08 07:36:45'),
(213, 'hr.settings.penalty-types.delete', 'web', '2026-11-08 07:36:45', '2026-11-08 07:36:45'),
(214, 'hr.settings.shifts.view', 'web', '2026-11-08 07:36:45', '2026-11-08 07:36:45'),
(215, 'hr.settings.shifts.create', 'web', '2026-11-08 07:36:45', '2026-11-08 07:36:45'),
(216, 'hr.settings.shifts.edit', 'web', '2026-11-08 07:36:45', '2026-11-08 07:36:45'),
(217, 'hr.settings.shifts.delete', 'web', '2026-11-08 07:36:45', '2026-11-08 07:36:45'),
(218, 'hr.settings.work-schedules.view', 'web', '2026-11-08 07:36:45', '2026-11-08 07:36:45'),
(219, 'hr.settings.work-schedules.create', 'web', '2026-11-08 07:36:45', '2026-11-08 07:36:45'),
(220, 'hr.settings.work-schedules.edit', 'web', '2026-11-08 07:36:45', '2026-11-08 07:36:45'),
(221, 'hr.settings.work-schedules.delete', 'web', '2026-11-08 07:36:45', '2026-11-08 07:36:45'),
(222, 'hr.settings.official-holidays.view', 'web', '2026-11-08 07:36:45', '2026-11-08 07:36:45'),
(223, 'hr.settings.official-holidays.create', 'web', '2026-11-08 07:36:45', '2026-11-08 07:36:45'),
(224, 'hr.settings.official-holidays.edit', 'web', '2026-11-08 07:36:45', '2026-11-08 07:36:45'),
(225, 'hr.settings.official-holidays.delete', 'web', '2026-11-08 07:36:45', '2026-11-08 07:36:45'),
(226, 'hr.settings.policies.view', 'web', '2026-11-08 07:36:45', '2026-11-08 07:36:45'),
(227, 'hr.settings.policies.edit', 'web', '2026-11-08 07:36:45', '2026-11-08 07:36:45'),
(228, 'hr.settings.policies.reset', 'web', '2026-11-08 07:36:45', '2026-11-08 07:36:45'),
(229, 'users.view', 'web', '2026-11-08 07:36:45', '2026-11-08 07:36:45'),
(230, 'users.create', 'web', '2026-11-08 07:36:45', '2026-11-08 07:36:45'),
(231, 'users.edit', 'web', '2026-11-08 07:36:46', '2026-11-08 07:36:46'),
(232, 'users.delete', 'web', '2026-11-08 07:36:46', '2026-11-08 07:36:46'),
(233, 'users.toggle-status', 'web', '2026-11-08 07:36:46', '2026-11-08 07:36:46'),
(234, 'profile.view', 'web', '2026-11-08 07:36:46', '2026-11-08 07:36:46'),
(235, 'profile.edit', 'web', '2026-11-08 07:36:46', '2026-11-08 07:36:46'),
(236, 'settings.company.view', 'web', '2026-11-08 07:36:46', '2026-11-08 07:36:46'),
(237, 'settings.company.edit', 'web', '2026-11-08 07:36:46', '2026-11-08 07:36:46'),
(238, 'settings.roles.view', 'web', '2026-11-08 07:36:46', '2026-11-08 07:36:46'),
(239, 'settings.roles.create', 'web', '2026-11-08 07:36:46', '2026-11-08 07:36:46'),
(240, 'settings.roles.edit', 'web', '2026-11-08 07:36:46', '2026-11-08 07:36:46'),
(241, 'settings.roles.delete', 'web', '2026-11-08 07:36:47', '2026-11-08 07:36:47'),
(242, 'settings.roles.assign-permissions', 'web', '2026-11-08 07:36:47', '2026-11-08 07:36:47'),
(243, 'settings.translations.view', 'web', '2026-11-08 07:36:47', '2026-11-08 07:36:47'),
(244, 'settings.translations.create', 'web', '2026-11-08 07:36:47', '2026-11-08 07:36:47'),
(245, 'settings.translations.edit', 'web', '2026-11-08 07:36:47', '2026-11-08 07:36:47'),
(246, 'settings.translations.delete', 'web', '2026-11-08 07:36:47', '2026-11-08 07:36:47'),
(247, 'settings.print-templates.view', 'web', '2026-11-08 07:36:47', '2026-11-08 07:36:47'),
(248, 'settings.print-templates.create', 'web', '2026-11-08 07:36:47', '2026-11-08 07:36:47'),
(249, 'settings.print-templates.edit', 'web', '2026-11-08 07:36:47', '2026-11-08 07:36:47'),
(250, 'settings.print-templates.delete', 'web', '2026-11-08 07:36:48', '2026-11-08 07:36:48'),
(251, 'settings.general.governments.view', 'web', '2026-11-08 07:36:48', '2026-11-08 07:36:48'),
(252, 'settings.general.governments.create', 'web', '2026-11-08 07:36:48', '2026-11-08 07:36:48'),
(253, 'settings.general.governments.edit', 'web', '2026-11-08 07:36:48', '2026-11-08 07:36:48'),
(254, 'settings.general.governments.delete', 'web', '2026-11-08 07:36:48', '2026-11-08 07:36:48'),
(255, 'settings.general.nationalities.view', 'web', '2026-11-08 07:36:48', '2026-11-08 07:36:48'),
(256, 'settings.general.nationalities.create', 'web', '2026-11-08 07:36:48', '2026-11-08 07:36:48'),
(257, 'settings.general.nationalities.edit', 'web', '2026-11-08 07:36:48', '2026-11-08 07:36:48'),
(258, 'settings.general.nationalities.delete', 'web', '2026-11-08 07:36:48', '2026-11-08 07:36:48'),
(259, 'settings.general.religions.view', 'web', '2026-11-08 07:36:48', '2026-11-08 07:36:48'),
(260, 'settings.general.religions.create', 'web', '2026-11-08 07:36:48', '2026-11-08 07:36:48'),
(261, 'settings.general.religions.edit', 'web', '2026-11-08 07:36:48', '2026-11-08 07:36:48'),
(262, 'settings.general.religions.delete', 'web', '2026-11-08 07:36:48', '2026-11-08 07:36:48'),
(263, 'settings.general.marital-statuses.view', 'web', '2026-11-08 07:36:48', '2026-11-08 07:36:48'),
(264, 'settings.general.marital-statuses.create', 'web', '2026-11-08 07:36:48', '2026-11-08 07:36:48'),
(265, 'settings.general.marital-statuses.edit', 'web', '2026-11-08 07:36:48', '2026-11-08 07:36:48'),
(266, 'settings.general.marital-statuses.delete', 'web', '2026-11-08 07:36:48', '2026-11-08 07:36:48'),
(267, 'settings.general.banks.view', 'web', '2026-11-08 07:36:48', '2026-11-08 07:36:48'),
(268, 'settings.general.banks.create', 'web', '2026-11-08 07:36:48', '2026-11-08 07:36:48'),
(269, 'settings.general.banks.edit', 'web', '2026-11-08 07:36:48', '2026-11-08 07:36:48'),
(270, 'settings.general.banks.delete', 'web', '2026-11-08 07:36:48', '2026-11-08 07:36:48'),
(271, 'settings.general.bank-branches.view', 'web', '2026-11-08 07:36:48', '2026-11-08 07:36:48'),
(272, 'settings.general.bank-branches.create', 'web', '2026-11-08 07:36:48', '2026-11-08 07:36:48'),
(273, 'settings.general.bank-branches.edit', 'web', '2026-11-08 07:36:48', '2026-11-08 07:36:48'),
(274, 'settings.general.bank-branches.delete', 'web', '2026-11-08 07:36:48', '2026-11-08 07:36:48'),
(275, 'audit.view', 'web', '2026-11-08 07:36:49', '2026-11-08 07:36:49'),
(276, 'audit.export', 'web', '2026-11-08 07:36:49', '2026-11-08 07:36:49'),
(277, 'accounting.dashboard.view', 'web', '2026-11-08 07:36:49', '2026-11-08 07:36:49'),
(278, 'accounting.entities.view', 'web', '2026-11-08 07:36:49', '2026-11-08 07:36:49'),
(279, 'accounting.entities.create', 'web', '2026-11-08 07:36:49', '2026-11-08 07:36:49'),
(280, 'accounting.entities.edit', 'web', '2026-11-08 07:36:49', '2026-11-08 07:36:49'),
(281, 'accounting.entities.delete', 'web', '2026-11-08 07:36:49', '2026-11-08 07:36:49'),
(282, 'accounting.fiscal-years.view', 'web', '2026-11-08 07:36:49', '2026-11-08 07:36:49'),
(283, 'accounting.fiscal-years.create', 'web', '2026-11-08 07:36:49', '2026-11-08 07:36:49'),
(284, 'accounting.fiscal-years.edit', 'web', '2026-11-08 07:36:49', '2026-11-08 07:36:49'),
(285, 'accounting.fiscal-years.delete', 'web', '2026-11-08 07:36:49', '2026-11-08 07:36:49'),
(286, 'accounting.fiscal-years.toggle-status', 'web', '2026-11-08 07:36:49', '2026-11-08 07:36:49'),
(287, 'accounting.lock-dates.view', 'web', '2026-11-08 07:36:49', '2026-11-08 07:36:49'),
(288, 'accounting.lock-dates.edit', 'web', '2026-11-08 07:36:49', '2026-11-08 07:36:49'),
(289, 'accounting.lock-dates.exceptions.create', 'web', '2026-11-08 07:36:49', '2026-11-08 07:36:49'),
(290, 'accounting.lock-dates.exceptions.revoke', 'web', '2026-11-08 07:36:49', '2026-11-08 07:36:49'),
(291, 'accounting.cost-centers.view', 'web', '2026-11-08 07:36:49', '2026-11-08 07:36:49'),
(292, 'accounting.cost-centers.create', 'web', '2026-11-08 07:36:49', '2026-11-08 07:36:49'),
(293, 'accounting.cost-centers.edit', 'web', '2026-11-08 07:36:49', '2026-11-08 07:36:49'),
(294, 'accounting.cost-centers.delete', 'web', '2026-11-08 07:36:49', '2026-11-08 07:36:49'),
(295, 'accounting.cost-centers.toggle-status', 'web', '2026-11-08 07:36:49', '2026-11-08 07:36:49'),
(296, 'accounting.journals.view', 'web', '2026-11-08 07:36:49', '2026-11-08 07:36:49'),
(297, 'accounting.journals.create', 'web', '2026-11-08 07:36:49', '2026-11-08 07:36:49'),
(298, 'accounting.journals.edit', 'web', '2026-11-08 07:36:49', '2026-11-08 07:36:49'),
(299, 'accounting.journals.delete', 'web', '2026-11-08 07:36:49', '2026-11-08 07:36:49'),
(300, 'accounting.journals.toggle-status', 'web', '2026-11-08 07:36:49', '2026-11-08 07:36:49'),
(301, 'accounting.cash-accounts.view', 'web', '2026-11-08 07:36:49', '2026-11-08 07:36:49'),
(302, 'accounting.cash-accounts.create', 'web', '2026-11-08 07:36:50', '2026-11-08 07:36:50'),
(303, 'accounting.cash-accounts.edit', 'web', '2026-11-08 07:36:50', '2026-11-08 07:36:50'),
(304, 'accounting.cash-accounts.delete', 'web', '2026-11-08 07:36:50', '2026-11-08 07:36:50'),
(305, 'accounting.cash-accounts.toggle-status', 'web', '2026-11-08 07:36:50', '2026-11-08 07:36:50'),
(306, 'accounting.accounts.view', 'web', '2026-11-08 07:36:50', '2026-11-08 07:36:50'),
(307, 'accounting.accounts.create', 'web', '2026-11-08 07:36:50', '2026-11-08 07:36:50'),
(308, 'accounting.accounts.edit', 'web', '2026-11-08 07:36:50', '2026-11-08 07:36:50'),
(309, 'accounting.accounts.delete', 'web', '2026-11-08 07:36:50', '2026-11-08 07:36:50'),
(310, 'accounting.accounts.toggle-status', 'web', '2026-11-08 07:36:50', '2026-11-08 07:36:50'),
(311, 'accounting.accounts.export', 'web', '2026-11-08 07:36:50', '2026-11-08 07:36:50'),
(312, 'accounting.accounts.import', 'web', '2026-11-08 07:36:50', '2026-11-08 07:36:50'),
(313, 'accounting.journal-entries.view', 'web', '2026-11-08 07:36:50', '2026-11-08 07:36:50'),
(314, 'accounting.journal-entries.create', 'web', '2026-11-08 07:36:50', '2026-11-08 07:36:50'),
(315, 'accounting.journal-entries.edit', 'web', '2026-11-08 07:36:50', '2026-11-08 07:36:50'),
(316, 'accounting.journal-entries.delete', 'web', '2026-11-08 07:36:50', '2026-11-08 07:36:50'),
(317, 'accounting.journal-entries.post', 'web', '2026-11-08 07:36:50', '2026-11-08 07:36:50'),
(318, 'accounting.journal-entries.cancel', 'web', '2026-11-08 07:36:50', '2026-11-08 07:36:50'),
(319, 'accounting.journal-entries.reverse', 'web', '2026-11-08 07:36:50', '2026-11-08 07:36:50'),
(320, 'accounting.opening-balances.view', 'web', '2026-11-08 07:36:50', '2026-11-08 07:36:50'),
(321, 'accounting.opening-balances.create', 'web', '2026-11-08 07:36:50', '2026-11-08 07:36:50'),
(322, 'accounting.opening-balances.reopen', 'web', '2026-11-08 07:36:50', '2026-11-08 07:36:50'),
(323, 'accounting.opening-balances.print', 'web', '2026-11-08 07:36:50', '2026-11-08 07:36:50'),
(324, 'accounting.bank-reconciliations.view', 'web', '2026-11-08 07:36:50', '2026-11-08 07:36:50'),
(325, 'accounting.bank-reconciliations.create', 'web', '2026-11-08 07:36:50', '2026-11-08 07:36:50'),
(326, 'accounting.bank-reconciliations.edit', 'web', '2026-11-08 07:36:50', '2026-11-08 07:36:50'),
(327, 'accounting.bank-reconciliations.delete', 'web', '2026-11-08 07:36:50', '2026-11-08 07:36:50'),
(328, 'accounting.bank-reconciliations.reopen', 'web', '2026-11-08 07:36:51', '2026-11-08 07:36:51'),
(329, 'accounting.bank-reconciliations.print', 'web', '2026-11-08 07:36:51', '2026-11-08 07:36:51'),
(330, 'accounting.payment-matchings.view', 'web', '2026-11-08 07:36:51', '2026-11-08 07:36:51'),
(331, 'accounting.payment-matchings.create', 'web', '2026-11-08 07:36:51', '2026-11-08 07:36:51'),
(332, 'accounting.payment-matchings.delete', 'web', '2026-11-08 07:36:51', '2026-11-08 07:36:51'),
(333, 'accounting.reports.view', 'web', '2026-11-08 07:36:51', '2026-11-08 07:36:51'),
(334, 'accounting.reports.general-ledger', 'web', '2026-11-08 07:36:51', '2026-11-08 07:36:51'),
(335, 'accounting.reports.trial-balance', 'web', '2026-11-08 07:36:51', '2026-11-08 07:36:51'),
(336, 'accounting.reports.income-statement', 'web', '2026-11-08 07:36:51', '2026-11-08 07:36:51'),
(337, 'accounting.reports.balance-sheet', 'web', '2026-11-08 07:36:51', '2026-11-08 07:36:51'),
(338, 'accounting.reports.cash-flow', 'web', '2026-11-08 07:36:51', '2026-11-08 07:36:51'),
(339, 'accounting.reports.account-statement', 'web', '2026-11-08 07:36:51', '2026-11-08 07:36:51'),
(340, 'accounting.reports.cost-center', 'web', '2026-11-08 07:36:51', '2026-11-08 07:36:51'),
(341, 'accounting.reports.export', 'web', '2026-11-08 07:36:51', '2026-11-08 07:36:51'),
(342, 'accounting.reports.print', 'web', '2026-11-08 07:36:51', '2026-11-08 07:36:51'),
(343, 'accounting.audit-logs.view', 'web', '2026-11-08 07:36:51', '2026-11-08 07:36:51'),
(344, 'accounting.ai-assistant.view', 'web', '2026-11-08 07:36:51', '2026-11-08 07:36:51'),
(345, 'accounting.ai-assistant.ask', 'web', '2026-11-08 07:36:51', '2026-11-08 07:36:51'),
(346, 'accounting.guide.view', 'web', '2026-11-08 07:36:51', '2026-11-08 07:36:51'),
(347, 'inventory.items.view', 'web', '2026-11-08 07:36:51', '2026-11-08 07:36:51'),
(348, 'inventory.items.create', 'web', '2026-11-08 07:36:51', '2026-11-08 07:36:51'),
(349, 'inventory.items.edit', 'web', '2026-11-08 07:36:52', '2026-11-08 07:36:52'),
(350, 'inventory.items.delete', 'web', '2026-11-08 07:36:52', '2026-11-08 07:36:52'),
(351, 'inventory.items.toggle-status', 'web', '2026-11-08 07:36:52', '2026-11-08 07:36:52'),
(352, 'inventory.categories.view', 'web', '2026-11-08 07:36:52', '2026-11-08 07:36:52'),
(353, 'inventory.categories.create', 'web', '2026-11-08 07:36:52', '2026-11-08 07:36:52'),
(354, 'inventory.categories.edit', 'web', '2026-11-08 07:36:52', '2026-11-08 07:36:52'),
(355, 'inventory.categories.delete', 'web', '2026-11-08 07:36:52', '2026-11-08 07:36:52'),
(356, 'inventory.categories.toggle-status', 'web', '2026-11-08 07:36:52', '2026-11-08 07:36:52'),
(357, 'inventory.units.view', 'web', '2026-11-08 07:36:52', '2026-11-08 07:36:52'),
(358, 'inventory.units.create', 'web', '2026-11-08 07:36:52', '2026-11-08 07:36:52'),
(359, 'inventory.units.edit', 'web', '2026-11-08 07:36:52', '2026-11-08 07:36:52'),
(360, 'inventory.units.delete', 'web', '2026-11-08 07:36:52', '2026-11-08 07:36:52'),
(361, 'inventory.units.toggle-status', 'web', '2026-11-08 07:36:52', '2026-11-08 07:36:52'),
(362, 'inventory.warehouses.view', 'web', '2026-11-08 07:36:52', '2026-11-08 07:36:52'),
(363, 'inventory.warehouses.create', 'web', '2026-11-08 07:36:52', '2026-11-08 07:36:52'),
(364, 'inventory.warehouses.edit', 'web', '2026-11-08 07:36:52', '2026-11-08 07:36:52'),
(365, 'inventory.warehouses.delete', 'web', '2026-11-08 07:36:52', '2026-11-08 07:36:52'),
(366, 'inventory.warehouses.toggle-status', 'web', '2026-11-08 07:36:52', '2026-11-08 07:36:52'),
(367, 'crm.leads.view', 'web', '2026-11-08 07:36:52', '2026-11-08 07:36:52'),
(368, 'crm.leads.create', 'web', '2026-11-08 07:36:52', '2026-11-08 07:36:52'),
(369, 'crm.leads.edit', 'web', '2026-11-08 07:36:52', '2026-11-08 07:36:52'),
(370, 'crm.leads.delete', 'web', '2026-11-08 07:36:53', '2026-11-08 07:36:53'),
(371, 'crm.leads.export', 'web', '2026-11-08 07:36:53', '2026-11-08 07:36:53'),
(372, 'crm.service-requests.view', 'web', '2026-11-08 07:36:53', '2026-11-08 07:36:53'),
(373, 'crm.service-requests.create', 'web', '2026-11-08 07:36:53', '2026-11-08 07:36:53'),
(374, 'crm.service-requests.edit', 'web', '2026-11-08 07:36:53', '2026-11-08 07:36:53'),
(375, 'crm.service-requests.delete', 'web', '2026-11-08 07:36:53', '2026-11-08 07:36:53'),
(376, 'crm.service-requests.export', 'web', '2026-11-08 07:36:53', '2026-11-08 07:36:53'),
(377, 'tasks.monitoring.view', 'web', '2026-11-08 07:36:53', '2026-11-08 07:36:53'),
(378, 'tasks.task-groups.view', 'web', '2026-11-08 07:36:53', '2026-11-08 07:36:53'),
(379, 'tasks.task-groups.create', 'web', '2026-11-08 07:36:53', '2026-11-08 07:36:53'),
(380, 'tasks.task-groups.edit', 'web', '2026-11-08 07:36:53', '2026-11-08 07:36:53'),
(381, 'tasks.task-groups.delete', 'web', '2026-11-08 07:36:53', '2026-11-08 07:36:53'),
(382, 'tasks.task-groups.toggle-status', 'web', '2026-11-08 07:36:53', '2026-11-08 07:36:53'),
(383, 'tasks.view', 'web', '2026-11-08 07:36:53', '2026-11-08 07:36:53'),
(384, 'tasks.create', 'web', '2026-11-08 07:36:53', '2026-11-08 07:36:53'),
(385, 'tasks.edit', 'web', '2026-11-08 07:36:53', '2026-11-08 07:36:53'),
(386, 'tasks.delete', 'web', '2026-11-08 07:36:53', '2026-11-08 07:36:53'),
(387, 'tasks.toggle-status', 'web', '2026-11-08 07:36:53', '2026-11-08 07:36:53'),
(388, 'notifications.view', 'web', '2026-11-08 07:36:53', '2026-11-08 07:36:53'),
(389, 'notifications.broadcasts.view', 'web', '2026-11-08 07:36:53', '2026-11-08 07:36:53'),
(390, 'notifications.broadcasts.create', 'web', '2026-11-08 07:36:53', '2026-11-08 07:36:53'),
(391, 'notifications.broadcasts.send', 'web', '2026-11-08 07:36:53', '2026-11-08 07:36:53'),
(392, 'settings.notification-templates.view', 'web', '2026-11-08 07:36:53', '2026-11-08 07:36:53'),
(393, 'settings.notification-templates.edit', 'web', '2026-11-08 07:36:53', '2026-11-08 07:36:53'),
(394, 'dashboard.view', 'web', '2026-11-08 07:36:54', '2026-11-08 07:36:54'),
(395, 'academic.dashboard.view', 'web', '2026-11-08 07:36:54', '2026-11-08 07:36:54'),
(396, 'academic.fee-statistics.view', 'web', '2026-11-08 07:36:54', '2026-11-08 07:36:54'),
(397, 'inventory.dashboard.view', 'web', '2026-11-08 07:36:54', '2026-11-08 07:36:54'),
(398, 'crm.dashboard.view', 'web', '2026-11-08 07:36:54', '2026-11-08 07:36:54'),
(399, 'crm.leads.statistics.view', 'web', '2026-11-08 07:36:54', '2026-11-08 07:36:54'),
(400, 'tasks.dashboard.view', 'web', '2026-11-08 07:36:54', '2026-11-08 07:36:54'),
(401, 'academic.events.view', 'web', '2026-11-08 07:36:54', '2026-11-08 07:36:54'),
(402, 'academic.events.create', 'web', '2026-11-08 07:36:54', '2026-11-08 07:36:54'),
(403, 'academic.events.edit', 'web', '2026-11-08 07:36:54', '2026-11-08 07:36:54'),
(404, 'academic.events.delete', 'web', '2026-11-08 07:36:54', '2026-11-08 07:36:54'),
(405, 'academic.calender.view', 'web', '2026-11-08 07:36:54', '2026-11-08 07:36:54'),
(406, 'academic.calender.create', 'web', '2026-11-08 07:36:54', '2026-11-08 07:36:54'),
(407, 'academic.calender.edit', 'web', '2026-11-08 07:36:54', '2026-11-08 07:36:54'),
(408, 'academic.calender.delete', 'web', '2026-11-08 07:36:54', '2026-11-08 07:36:54'),
(409, 'academic.calendar.view', 'web', '2026-11-08 07:36:54', '2026-11-08 07:36:54'),
(410, 'academic.calendar.create', 'web', '2026-11-08 07:36:54', '2026-11-08 07:36:54'),
(411, 'academic.calendar.edit', 'web', '2026-11-08 07:36:54', '2026-11-08 07:36:54'),
(412, 'academic.calendar.delete', 'web', '2026-11-08 07:36:54', '2026-11-08 07:36:54'),
(413, 'academic.personal-calendar.view', 'web', '2026-11-08 07:36:54', '2026-11-08 07:36:54'),
(414, 'academic.personal-calendar.create', 'web', '2026-11-08 07:36:54', '2026-11-08 07:36:54'),
(415, 'academic.personal-calendar.edit', 'web', '2026-11-08 07:36:54', '2026-11-08 07:36:54'),
(416, 'academic.personal-calendar.delete', 'web', '2026-11-08 07:36:54', '2026-11-08 07:36:54'),
(417, 'academic.meeting-calendar.view', 'web', '2026-11-08 07:36:54', '2026-11-08 07:36:54'),
(418, 'academic.meeting-calendar.create', 'web', '2026-11-08 07:36:54', '2026-11-08 07:36:54'),
(419, 'academic.meeting-calendar.edit', 'web', '2026-11-08 07:36:54', '2026-11-08 07:36:54'),
(420, 'academic.meeting-calendar.delete', 'web', '2026-11-08 07:36:55', '2026-11-08 07:36:55'),
(421, 'academic.interviews_calender.view', 'web', '2026-11-08 07:36:55', '2026-11-08 07:36:55'),
(422, 'academic.interviews_calender.create', 'web', '2026-11-08 07:36:55', '2026-11-08 07:36:55'),
(423, 'academic.interviews_calender.edit', 'web', '2026-11-08 07:36:55', '2026-11-08 07:36:55'),
(424, 'academic.interviews_calender.delete', 'web', '2026-11-08 07:36:55', '2026-11-08 07:36:55'),
(425, 'academic.interviews-calendar.view', 'web', '2026-11-08 07:36:55', '2026-11-08 07:36:55'),
(426, 'academic.interviews-calendar.create', 'web', '2026-11-08 07:36:55', '2026-11-08 07:36:55'),
(427, 'academic.interviews-calendar.edit', 'web', '2026-11-08 07:36:55', '2026-11-08 07:36:55'),
(428, 'academic.interviews-calendar.delete', 'web', '2026-11-08 07:36:55', '2026-11-08 07:36:55');

-- --------------------------------------------------------

--
-- Table structure for table `push_subscriptions`
--

CREATE TABLE `push_subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `endpoint` text NOT NULL,
  `endpoint_hash` char(64) NOT NULL,
  `public_key` text NOT NULL,
  `auth_token` text NOT NULL,
  `content_encoding` varchar(30) DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `name_ar` varchar(255) DEFAULT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `name_ar`, `name_en`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'super_admin', 'السوبر ادمن', 'Super Administrator', 'web', '2026-11-08 07:36:34', '2026-11-08 07:36:34'),
(2, 'admin', 'مدير النظام', 'System Administrator', 'web', '2026-11-08 07:36:34', '2026-11-08 07:36:34'),
(3, 'employee', 'موظف إداري', 'Administrative Employee', 'web', '2026-11-08 07:36:34', '2026-11-08 07:36:34'),
(4, 'guardian', 'ولي أمر', 'Guardian', 'web', '2026-11-08 07:36:34', '2026-11-08 07:36:34'),
(5, 'student', 'طالب', 'Student', 'web', '2026-11-08 07:36:34', '2026-11-08 07:36:34'),
(6, 'teacher', 'معلم', 'Teacher', 'web', '2026-11-08 07:36:34', '2026-11-08 07:36:34'),
(7, 'supervisor', 'مشرف أكاديمي', 'Supervisor', 'web', '2026-11-08 07:36:34', '2026-11-08 07:36:34'),
(8, 'nurse', 'ممرض', 'Nurse', 'web', '2026-11-08 07:36:34', '2026-11-08 07:36:34'),
(9, 'hr_manager', 'مدير الموارد البشرية', 'HR Manager', 'web', '2026-11-08 07:36:34', '2026-11-08 07:36:35'),
(10, 'accountant_manager', 'مدير الحسابات', 'Accounting Manager', 'web', '2026-11-08 07:36:35', '2026-11-08 07:36:35'),
(11, 'head_office', 'الإدارة العامة ', 'Head Office', 'web', '2026-11-08 07:36:35', '2026-11-08 07:36:35'),
(12, 'محاسب', NULL, NULL, 'web', '2026-11-08 07:36:57', '2026-11-08 07:36:57');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 2),
(2, 1),
(2, 2),
(3, 1),
(3, 2),
(4, 1),
(4, 2),
(5, 1),
(5, 2),
(6, 1),
(6, 2),
(7, 1),
(7, 2),
(8, 1),
(8, 2),
(9, 1),
(9, 2),
(10, 1),
(10, 2),
(11, 1),
(11, 2),
(12, 1),
(12, 2),
(13, 1),
(13, 2),
(14, 1),
(14, 2),
(15, 1),
(15, 2),
(16, 1),
(16, 2),
(17, 1),
(17, 2),
(18, 1),
(18, 2),
(19, 1),
(19, 2),
(20, 1),
(20, 2),
(21, 1),
(21, 2),
(22, 1),
(22, 2),
(23, 1),
(23, 2),
(24, 1),
(24, 2),
(25, 1),
(25, 2),
(26, 1),
(26, 2),
(27, 1),
(27, 2),
(28, 1),
(28, 2),
(29, 1),
(29, 2),
(30, 1),
(30, 2),
(31, 1),
(31, 2),
(32, 1),
(32, 2),
(33, 1),
(33, 2),
(34, 1),
(34, 2),
(35, 1),
(35, 2),
(36, 1),
(36, 2),
(37, 1),
(37, 2),
(38, 1),
(38, 2),
(39, 1),
(39, 2),
(40, 1),
(40, 2),
(41, 1),
(41, 2),
(42, 1),
(42, 2),
(43, 1),
(43, 2),
(44, 1),
(44, 2),
(45, 1),
(45, 2),
(46, 1),
(46, 2),
(47, 1),
(47, 2),
(48, 1),
(48, 2),
(49, 1),
(49, 2),
(50, 1),
(50, 2),
(51, 1),
(51, 2),
(52, 1),
(52, 2),
(53, 1),
(53, 2),
(54, 1),
(54, 2),
(55, 1),
(55, 2),
(56, 1),
(56, 2),
(57, 1),
(57, 2),
(58, 1),
(58, 2),
(59, 1),
(59, 2),
(60, 1),
(60, 2),
(61, 1),
(61, 2),
(62, 1),
(62, 2),
(63, 1),
(63, 2),
(64, 1),
(64, 2),
(65, 1),
(65, 2),
(66, 1),
(66, 2),
(67, 1),
(67, 2),
(68, 1),
(68, 2),
(69, 1),
(69, 2),
(70, 1),
(70, 2),
(71, 1),
(71, 2),
(72, 1),
(72, 2),
(73, 1),
(73, 2),
(74, 1),
(74, 2),
(75, 1),
(75, 2),
(76, 1),
(76, 2),
(77, 1),
(77, 2),
(78, 1),
(78, 2),
(79, 1),
(79, 2),
(80, 1),
(80, 2),
(81, 1),
(81, 2),
(82, 1),
(82, 2),
(83, 1),
(83, 2),
(84, 1),
(84, 2),
(85, 1),
(85, 2),
(86, 1),
(86, 2),
(87, 1),
(87, 2),
(88, 1),
(88, 2),
(89, 1),
(89, 2),
(90, 1),
(90, 2),
(91, 1),
(91, 2),
(92, 1),
(92, 2),
(93, 1),
(93, 2),
(94, 1),
(94, 2),
(95, 1),
(95, 2),
(96, 1),
(96, 2),
(97, 1),
(97, 2),
(98, 1),
(98, 2),
(99, 1),
(99, 2),
(100, 1),
(100, 2),
(101, 1),
(101, 2),
(102, 1),
(102, 2),
(103, 1),
(103, 2),
(104, 1),
(104, 2),
(105, 1),
(105, 2),
(106, 1),
(106, 2),
(107, 1),
(107, 2),
(108, 1),
(108, 2),
(109, 1),
(109, 2),
(110, 1),
(110, 2),
(111, 1),
(111, 2),
(112, 1),
(112, 2),
(113, 1),
(113, 2),
(114, 1),
(114, 2),
(115, 1),
(115, 2),
(116, 1),
(116, 2),
(117, 1),
(117, 2),
(118, 1),
(118, 2),
(119, 1),
(119, 2),
(120, 1),
(120, 2),
(121, 1),
(121, 2),
(122, 1),
(122, 2),
(123, 1),
(123, 2),
(124, 1),
(124, 2),
(125, 1),
(125, 2),
(126, 1),
(126, 2),
(127, 1),
(127, 2),
(128, 1),
(128, 2),
(129, 1),
(129, 2),
(130, 1),
(130, 2),
(131, 1),
(131, 2),
(132, 1),
(132, 2),
(133, 1),
(133, 2),
(134, 1),
(134, 2),
(135, 1),
(135, 2),
(136, 1),
(136, 2),
(137, 1),
(137, 2),
(138, 1),
(138, 2),
(139, 1),
(139, 2),
(140, 1),
(140, 2),
(141, 1),
(141, 2),
(142, 1),
(142, 2),
(143, 1),
(143, 2),
(144, 1),
(144, 2),
(145, 1),
(145, 2),
(146, 1),
(146, 2),
(147, 1),
(147, 2),
(148, 1),
(148, 2),
(149, 1),
(149, 2),
(150, 1),
(150, 2),
(151, 1),
(151, 2),
(152, 1),
(152, 2),
(153, 1),
(153, 2),
(154, 1),
(154, 2),
(155, 1),
(155, 2),
(156, 1),
(156, 2),
(157, 1),
(157, 2),
(158, 1),
(158, 2),
(159, 1),
(159, 2),
(160, 1),
(160, 2),
(161, 1),
(161, 2),
(162, 1),
(162, 2),
(163, 1),
(163, 2),
(164, 1),
(164, 2),
(165, 1),
(165, 2),
(166, 1),
(166, 2),
(167, 1),
(167, 2),
(168, 1),
(168, 2),
(169, 1),
(169, 2),
(170, 1),
(170, 2),
(171, 1),
(171, 2),
(172, 1),
(172, 2),
(173, 1),
(173, 2),
(174, 1),
(174, 2),
(175, 1),
(175, 2),
(176, 1),
(176, 2),
(177, 1),
(177, 2),
(178, 1),
(178, 2),
(179, 1),
(179, 2),
(180, 1),
(180, 2),
(181, 1),
(181, 2),
(182, 1),
(182, 2),
(183, 1),
(183, 2),
(184, 1),
(184, 2),
(185, 1),
(185, 2),
(186, 1),
(186, 2),
(187, 1),
(187, 2),
(188, 1),
(188, 2),
(189, 1),
(189, 2),
(190, 1),
(190, 2),
(191, 1),
(191, 2),
(192, 1),
(192, 2),
(193, 1),
(193, 2),
(194, 1),
(194, 2),
(195, 1),
(195, 2),
(196, 1),
(196, 2),
(197, 1),
(197, 2),
(198, 1),
(198, 2),
(199, 1),
(199, 2),
(200, 1),
(200, 2),
(201, 1),
(201, 2),
(202, 1),
(202, 2),
(203, 1),
(203, 2),
(204, 1),
(204, 2),
(205, 1),
(205, 2),
(206, 1),
(206, 2),
(207, 1),
(207, 2),
(208, 1),
(208, 2),
(209, 1),
(209, 2),
(210, 1),
(210, 2),
(211, 1),
(211, 2),
(212, 1),
(212, 2),
(213, 1),
(213, 2),
(214, 1),
(214, 2),
(215, 1),
(215, 2),
(216, 1),
(216, 2),
(217, 1),
(217, 2),
(218, 1),
(218, 2),
(219, 1),
(219, 2),
(220, 1),
(220, 2),
(221, 1),
(221, 2),
(222, 1),
(222, 2),
(223, 1),
(223, 2),
(224, 1),
(224, 2),
(225, 1),
(225, 2),
(226, 1),
(226, 2),
(227, 1),
(227, 2),
(228, 1),
(228, 2),
(229, 1),
(229, 2),
(230, 1),
(230, 2),
(231, 1),
(231, 2),
(232, 1),
(232, 2),
(233, 1),
(233, 2),
(234, 1),
(234, 2),
(234, 3),
(234, 4),
(234, 5),
(234, 6),
(234, 7),
(234, 8),
(234, 9),
(234, 10),
(234, 11),
(235, 1),
(235, 2),
(235, 3),
(235, 4),
(235, 5),
(235, 6),
(235, 7),
(235, 8),
(235, 9),
(235, 10),
(235, 11),
(236, 1),
(236, 2),
(237, 1),
(237, 2),
(238, 1),
(238, 2),
(239, 1),
(239, 2),
(240, 1),
(240, 2),
(241, 1),
(241, 2),
(242, 1),
(242, 2),
(243, 1),
(243, 2),
(244, 1),
(244, 2),
(245, 1),
(245, 2),
(246, 1),
(246, 2),
(247, 1),
(247, 2),
(248, 1),
(248, 2),
(249, 1),
(249, 2),
(250, 1),
(250, 2),
(251, 1),
(251, 2),
(252, 1),
(252, 2),
(253, 1),
(253, 2),
(254, 1),
(254, 2),
(255, 1),
(255, 2),
(256, 1),
(256, 2),
(257, 1),
(257, 2),
(258, 1),
(258, 2),
(259, 1),
(259, 2),
(260, 1),
(260, 2),
(261, 1),
(261, 2),
(262, 1),
(262, 2),
(263, 1),
(263, 2),
(264, 1),
(264, 2),
(265, 1),
(265, 2),
(266, 1),
(266, 2),
(267, 1),
(267, 2),
(268, 1),
(268, 2),
(269, 1),
(269, 2),
(270, 1),
(270, 2),
(271, 1),
(271, 2),
(272, 1),
(272, 2),
(273, 1),
(273, 2),
(274, 1),
(274, 2),
(275, 1),
(275, 2),
(276, 1),
(276, 2),
(277, 1),
(277, 2),
(277, 12),
(278, 1),
(278, 2),
(278, 12),
(279, 1),
(279, 2),
(279, 12),
(280, 1),
(280, 2),
(280, 12),
(281, 1),
(281, 2),
(281, 12),
(282, 1),
(282, 2),
(282, 12),
(283, 1),
(283, 2),
(283, 12),
(284, 1),
(284, 2),
(284, 12),
(285, 1),
(285, 2),
(285, 12),
(286, 1),
(286, 2),
(286, 12),
(287, 1),
(287, 2),
(287, 12),
(288, 1),
(288, 2),
(288, 12),
(289, 1),
(289, 2),
(289, 12),
(290, 1),
(290, 2),
(290, 12),
(291, 1),
(291, 2),
(291, 12),
(292, 1),
(292, 2),
(292, 12),
(293, 1),
(293, 2),
(293, 12),
(294, 1),
(294, 2),
(294, 12),
(295, 1),
(295, 2),
(295, 12),
(296, 1),
(296, 2),
(296, 12),
(297, 1),
(297, 2),
(297, 12),
(298, 1),
(298, 2),
(298, 12),
(299, 1),
(299, 2),
(299, 12),
(300, 1),
(300, 2),
(300, 12),
(301, 1),
(301, 2),
(301, 12),
(302, 1),
(302, 2),
(302, 12),
(303, 1),
(303, 2),
(303, 12),
(304, 1),
(304, 2),
(304, 12),
(305, 1),
(305, 2),
(305, 12),
(306, 1),
(306, 2),
(306, 12),
(307, 1),
(307, 2),
(307, 12),
(308, 1),
(308, 2),
(308, 12),
(309, 1),
(309, 2),
(309, 12),
(310, 1),
(310, 2),
(310, 12),
(311, 1),
(311, 2),
(311, 12),
(312, 1),
(312, 2),
(312, 12),
(313, 1),
(313, 2),
(313, 12),
(314, 1),
(314, 2),
(314, 12),
(315, 1),
(315, 2),
(315, 12),
(316, 1),
(316, 2),
(316, 12),
(317, 1),
(317, 2),
(317, 12),
(318, 1),
(318, 2),
(318, 12),
(319, 1),
(319, 2),
(319, 12),
(320, 1),
(320, 2),
(320, 12),
(321, 1),
(321, 2),
(321, 12),
(322, 1),
(322, 2),
(322, 12),
(323, 1),
(323, 2),
(323, 12),
(324, 1),
(324, 2),
(324, 12),
(325, 1),
(325, 2),
(325, 12),
(326, 1),
(326, 2),
(326, 12),
(327, 1),
(327, 2),
(327, 12),
(328, 1),
(328, 2),
(328, 12),
(329, 1),
(329, 2),
(329, 12),
(330, 1),
(330, 2),
(330, 12),
(331, 1),
(331, 2),
(331, 12),
(332, 1),
(332, 2),
(332, 12),
(333, 1),
(333, 2),
(333, 12),
(334, 1),
(334, 2),
(334, 12),
(335, 1),
(335, 2),
(335, 12),
(336, 1),
(336, 2),
(336, 12),
(337, 1),
(337, 2),
(337, 12),
(338, 1),
(338, 2),
(338, 12),
(339, 1),
(339, 2),
(339, 12),
(340, 1),
(340, 2),
(340, 12),
(341, 1),
(341, 2),
(341, 12),
(342, 1),
(342, 2),
(342, 12),
(343, 1),
(343, 2),
(343, 12),
(344, 1),
(344, 2),
(344, 12),
(345, 1),
(345, 2),
(345, 12),
(346, 1),
(346, 2),
(346, 12),
(347, 1),
(347, 2),
(348, 1),
(348, 2),
(349, 1),
(349, 2),
(350, 1),
(350, 2),
(351, 1),
(351, 2),
(352, 1),
(352, 2),
(353, 1),
(353, 2),
(354, 1),
(354, 2),
(355, 1),
(355, 2),
(356, 1),
(356, 2),
(357, 1),
(357, 2),
(358, 1),
(358, 2),
(359, 1),
(359, 2),
(360, 1),
(360, 2),
(361, 1),
(361, 2),
(362, 1),
(362, 2),
(363, 1),
(363, 2),
(364, 1),
(364, 2),
(365, 1),
(365, 2),
(366, 1),
(366, 2),
(367, 1),
(367, 2),
(368, 1),
(368, 2),
(369, 1),
(369, 2),
(370, 1),
(370, 2),
(371, 1),
(371, 2),
(372, 1),
(372, 2),
(373, 1),
(373, 2),
(374, 1),
(374, 2),
(375, 1),
(375, 2),
(376, 1),
(376, 2),
(377, 1),
(377, 2),
(378, 1),
(378, 2),
(379, 1),
(379, 2),
(380, 1),
(380, 2),
(381, 1),
(381, 2),
(382, 1),
(382, 2),
(383, 1),
(383, 2),
(384, 1),
(384, 2),
(385, 1),
(385, 2),
(386, 1),
(386, 2),
(387, 1),
(387, 2),
(388, 1),
(388, 2),
(389, 1),
(389, 2),
(390, 1),
(390, 2),
(391, 1),
(391, 2),
(392, 1),
(392, 2),
(393, 1),
(393, 2),
(394, 1),
(394, 2),
(395, 1),
(395, 2),
(396, 1),
(396, 2),
(397, 1),
(397, 2),
(398, 1),
(398, 2),
(399, 1),
(399, 2),
(400, 1),
(400, 2),
(401, 1),
(401, 2),
(402, 1),
(402, 2),
(403, 1),
(403, 2),
(404, 1),
(404, 2),
(405, 1),
(405, 2),
(406, 1),
(406, 2),
(407, 1),
(407, 2),
(408, 1),
(408, 2),
(409, 1),
(409, 2),
(410, 1),
(410, 2),
(411, 1),
(411, 2),
(412, 1),
(412, 2),
(413, 1),
(413, 2),
(414, 1),
(414, 2),
(415, 1),
(415, 2),
(416, 1),
(416, 2),
(417, 1),
(417, 2),
(418, 1),
(418, 2),
(419, 1),
(419, 2),
(420, 1),
(420, 2),
(421, 1),
(421, 2),
(422, 1),
(422, 2),
(423, 1),
(423, 2),
(424, 1),
(424, 2),
(425, 1),
(425, 2),
(426, 1),
(426, 2),
(427, 1),
(427, 2),
(428, 1),
(428, 2);

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
('5OVLXBcWNpJuEgVftnJgKubkc0BnfXXOpymyqv7Q', NULL, '127.0.0.1', 'Symfony', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYmpXN0pUWTdjQVd3NjY1RWZwVjc4MVo2Z3lJdGdOenNBa2ZkQTViRCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo1NjoiaHR0cDovL2xvY2FsaG9zdC9jb21wYW5pZXMvZGF0YT9kcmF3PTEmbGVuZ3RoPTEwJnN0YXJ0PTAiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czo1NjoiaHR0cDovL2xvY2FsaG9zdC9jb21wYW5pZXMvZGF0YT9kcmF3PTEmbGVuZ3RoPTEwJnN0YXJ0PTAiO3M6NToicm91dGUiO3M6MTQ6ImNvbXBhbmllcy5kYXRhIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1794138180),
('A0rydWiXcwq5ukAp1nbzN34RVquvIHKHNzy5xYHs', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.19041.6456', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZW5NOGx0bjAzZGdVQTN2ZHNieHpZWmMyTVRzcEs1WjhIUmNoM3A2SSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2NvbXBhbmllcyI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjI3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1794137872),
('c0joY0FLfsWrldfXAEmKiRrKXLAAIh7LIsjMD2XT', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiaFg0YWw5TDhsam5UUUlEUHRqRXBNT3kxYjBuVlFiaUZaVHRwUDA4dSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMwOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvc2V0dGluZ3MiO3M6NToicm91dGUiO3M6MTQ6InNldHRpbmdzLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1794140773),
('ffAeFJxPj5BIMyoi6M00sao6BaZZRRUgEiiGZOUp', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.19041.6456', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiOEhqOFFuVGFHT2pzQzZXNTQ3ZmlRWEhHYjV6ek1FSE82MVJxZzhZSCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2NvbXBhbmllcyI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjI3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1794137883),
('il0vvdLJEjLqrEEl0z3MlUA1QakaLwXrVN03zBKQ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.19041.6456', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiOVo4eWJQMWNVbEMxZEphMzIzY2ZLYVZZRmZQOUp6WjJ0elMyOGhDdyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2NvbXBhbmllcyI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjI3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1794137972),
('S460U2Fdf2lY43GIPCijJAOYKTzQsylouGIo8UBt', NULL, '127.0.0.1', '', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZ09sYU44c0t5dkROd0tldmRlRnBUU1lOM1BjZmFXaTdyRzNPWUNmMCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6NjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jb21wYW5pZXMvZGF0YT9kcmF3PTEmbGVuZ3RoPTEwJnN0YXJ0PTAiO319', 1794139091),
('YqIwdoVHdoZtJYC2fyMSNnEfSe8rQwSTkMGS0F1W', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiemFabGFCZTF1MFpZOWZlWXBsTklIWHVXZ0NOU0Raa2RlSnc4TFcyMSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9zZXR0aW5ncyI7czo1OiJyb3V0ZSI7czoxNDoic2V0dGluZ3MuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1794139645);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(255) DEFAULT NULL,
  `emergency_phone` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `login_image` varchar(255) DEFAULT NULL,
  `primary_color` varchar(20) NOT NULL DEFAULT '#0b284e',
  `currency` varchar(255) NOT NULL DEFAULT 'KWD',
  `currency_symbol` varchar(255) NOT NULL DEFAULT 'د.ك',
  `timezone` varchar(255) NOT NULL DEFAULT 'Asia/Kuwait',
  `default_locale` varchar(255) NOT NULL DEFAULT 'ar',
  `tax_number` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `tiktok` varchar(255) DEFAULT NULL,
  `youtube` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `email`, `phone`, `whatsapp`, `emergency_phone`, `logo`, `favicon`, `login_image`, `primary_color`, `currency`, `currency_symbol`, `timezone`, `default_locale`, `tax_number`, `facebook`, `instagram`, `twitter`, `tiktok`, `youtube`, `created_at`, `updated_at`) VALUES
(1, 'info@happyland.com', '+965 22200000', '+965 99990000', '+965 99990001', NULL, NULL, NULL, '#0b284e', 'KWD', 'د.ك', 'Asia/Kuwait', 'ar', 'KW-12345678', 'https://facebook.com/happylandkw', 'https://instagram.com/happylandkw', 'https://twitter.com/happylandkw', 'https://tiktok.com/@happylandkw', 'https://youtube.com/@happylandkw', '2026-11-08 07:36:58', '2026-11-08 07:36:58');

-- --------------------------------------------------------

--
-- Table structure for table `settings_email_templates`
--

CREATE TABLE `settings_email_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL DEFAULT 'general',
  `name` varchar(255) NOT NULL,
  `name_ar` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `subject_ar` varchar(255) NOT NULL,
  `subject_en` varchar(255) NOT NULL,
  `body_ar` longtext NOT NULL,
  `body_en` longtext NOT NULL,
  `variables` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`variables`)),
  `action_button_text_ar` varchar(255) DEFAULT NULL,
  `action_button_text_en` varchar(255) DEFAULT NULL,
  `action_url_variable` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings_notification_templates`
--

CREATE TABLE `settings_notification_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL,
  `module` varchar(50) NOT NULL,
  `name_ar` varchar(255) NOT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `color` varchar(30) DEFAULT NULL,
  `variables` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`variables`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings_notification_templates`
--

INSERT INTO `settings_notification_templates` (`id`, `code`, `type`, `module`, `name_ar`, `name_en`, `description`, `icon`, `color`, `variables`, `is_active`, `is_system`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'leave_submitted', 'leave_submitted', 'hr', 'تقديم طلب إجازة', 'Leave Request Submitted', 'إشعار عند تقديم طلب إجازة جديد.', 'bx-calendar-plus', 'info', '[\"employee_name\",\"leave_type\",\"start_date\",\"end_date\",\"total_days\",\"action_url\"]', 1, 1, '2026-11-08 07:37:12', '2026-11-08 07:37:12', NULL),
(2, 'leave_approved', 'leave_approved', 'hr', 'قبول طلب إجازة', 'Leave Request Approved', 'إشعار عند اعتماد طلب الإجازة.', 'bx-calendar-check', 'success', '[\"employee_name\",\"leave_type\",\"start_date\",\"end_date\",\"total_days\",\"action_url\"]', 1, 1, '2026-11-08 07:37:12', '2026-11-08 07:37:12', NULL),
(3, 'leave_rejected', 'leave_rejected', 'hr', 'رفض طلب إجازة', 'Leave Request Rejected', 'إشعار عند رفض طلب الإجازة.', 'bx-calendar-x', 'danger', '[\"employee_name\",\"leave_type\",\"rejection_reason\",\"action_url\"]', 1, 1, '2026-11-08 07:37:13', '2026-11-08 07:37:13', NULL),
(4, 'task_assigned', 'task_assigned', 'tasks', 'إسناد مهمة', 'Task Assigned', 'إشعار عند إسناد مهمة إلى موظف.', 'bx-task', 'primary', '[\"employee_name\",\"task_title\",\"priority\",\"due_date\",\"action_url\"]', 1, 1, '2026-11-08 07:37:13', '2026-11-08 07:37:13', NULL),
(5, 'task_note_added', 'task_note_added', 'tasks', 'إضافة تعليق على مهمة', 'Task Note Added', 'إشعار عند إضافة تعليق جديد على مهمة.', 'bx-message-rounded-detail', 'warning', '[\"employee_name\",\"task_title\",\"author_name\",\"note_content\",\"action_url\"]', 1, 1, '2026-11-08 07:37:13', '2026-11-08 07:37:13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `settings_notification_template_translations`
--

CREATE TABLE `settings_notification_template_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `template_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(10) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `action_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings_notification_template_translations`
--

INSERT INTO `settings_notification_template_translations` (`id`, `template_id`, `locale`, `title`, `message`, `action_url`, `created_at`, `updated_at`) VALUES
(1, 1, 'ar', 'تم تقديم طلب الإجازة', 'تم تقديم طلب إجازة جديد من {employee_name} من {start_date} إلى {end_date}.', '/portal/requests', '2026-11-08 07:37:12', '2026-11-08 07:37:12'),
(2, 1, 'en', 'Leave request submitted', 'A new leave request was submitted by {employee_name} from {start_date} to {end_date}.', '/portal/requests', '2026-11-08 07:37:12', '2026-11-08 07:37:12'),
(3, 2, 'ar', 'تم قبول طلب الإجازة', 'تمت الموافقة على طلب إجازتك من {start_date} إلى {end_date}.', '/portal/requests', '2026-11-08 07:37:13', '2026-11-08 07:37:13'),
(4, 2, 'en', 'Leave request approved', 'Your leave request from {start_date} to {end_date} has been approved.', '/portal/requests', '2026-11-08 07:37:13', '2026-11-08 07:37:13'),
(5, 3, 'ar', 'تم رفض طلب الإجازة', 'تم رفض طلب الإجازة الخاص بك. السبب: {rejection_reason}', '/portal/requests', '2026-11-08 07:37:13', '2026-11-08 07:37:13'),
(6, 3, 'en', 'Leave request rejected', 'Your leave request was rejected. Reason: {rejection_reason}', '/portal/requests', '2026-11-08 07:37:13', '2026-11-08 07:37:13'),
(7, 4, 'ar', 'تم إسناد مهمة جديدة إليك', 'تم إسناد المهمة {task_title} إليك. موعد التسليم: {due_date}.', '/tasks', '2026-11-08 07:37:13', '2026-11-08 07:37:13'),
(8, 4, 'en', 'A new task was assigned to you', 'The task {task_title} was assigned to you. Due date: {due_date}.', '/tasks', '2026-11-08 07:37:13', '2026-11-08 07:37:13'),
(9, 5, 'ar', 'تمت إضافة تعليق على المهمة', 'أضاف {author_name} تعليقًا جديدًا على المهمة {task_title}.', '/tasks', '2026-11-08 07:37:13', '2026-11-08 07:37:13'),
(10, 5, 'en', 'A new note was added to a task', '{author_name} added a new note to {task_title}.', '/tasks', '2026-11-08 07:37:13', '2026-11-08 07:37:13');

-- --------------------------------------------------------

--
-- Table structure for table `settings_print_templates`
--

CREATE TABLE `settings_print_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `name_ar` varchar(255) DEFAULT NULL,
  `code` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'report',
  `description` text DEFAULT NULL,
  `page_size` varchar(20) NOT NULL DEFAULT 'A4',
  `orientation` varchar(20) NOT NULL DEFAULT 'portrait',
  `margins` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`margins`)),
  `header_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`header_settings`)),
  `footer_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`footer_settings`)),
  `signature_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`signature_settings`)),
  `custom_css` longtext DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings_print_templates`
--

INSERT INTO `settings_print_templates` (`id`, `name`, `name_ar`, `code`, `type`, `description`, `page_size`, `orientation`, `margins`, `header_settings`, `footer_settings`, `signature_settings`, `custom_css`, `is_active`, `is_default`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'General Reports Template', 'قالب التقارير العامة', 'general_report', 'report', 'القالب الأساسي المعتمد لكافة تقارير الجداول والكشوفات والبيانات الإدارية والموارد البشرية في النظام.', 'A4', 'portrait', '{\"top\":12,\"right\":12,\"bottom\":12,\"left\":12,\"unit\":\"mm\"}', '{\"show_header\":true,\"show_logo\":true,\"logo_position\":\"right\",\"show_company_name\":true,\"show_cr\":true,\"show_tax\":true,\"show_phone\":true,\"show_email\":true,\"show_report_title\":true}', '{\"show_footer\":true,\"show_page_number\":true,\"show_timestamp\":true,\"show_printed_by\":true,\"show_qr_code\":true,\"footer_text\":\"\\u062a\\u0645 \\u0625\\u0635\\u062f\\u0627\\u0631 \\u0647\\u0630\\u0627 \\u0627\\u0644\\u062a\\u0642\\u0631\\u064a\\u0631 \\u0622\\u0644\\u064a\\u0627\\u064b \\u0645\\u0646 \\u0646\\u0638\\u0627\\u0645 \\u0625\\u062f\\u0627\\u0631\\u0629 \\u0627\\u0644\\u0645\\u0648\\u0627\\u0631\\u062f \\u0627\\u0644\\u0645\\u062a\\u0643\\u0627\\u0645\\u0644 - \\u062c\\u064a\\u062a \\u062a\\u0643\"}', '[{\"title\":\"\\u0625\\u0639\\u062f\\u0627\\u062f \\/ \\u0645\\u0633\\u0624\\u0648\\u0644 \\u0627\\u0644\\u0625\\u062f\\u062e\\u0627\\u0644\",\"title_en\":\"Prepared By\",\"name\":\"\",\"show\":true},{\"title\":\"\\u0645\\u0631\\u0627\\u062c\\u0639\\u0629 \\/ \\u0631\\u0626\\u064a\\u0633 \\u0627\\u0644\\u0642\\u0633\\u0645\",\"title_en\":\"Reviewed By\",\"name\":\"\",\"show\":true},{\"title\":\"\\u0627\\u0639\\u062a\\u0645\\u0627\\u062f \\/ \\u0627\\u0644\\u0645\\u062f\\u064a\\u0631 \\u0627\\u0644\\u0639\\u0627\\u0645\",\"title_en\":\"Approved By\",\"name\":\"\",\"show\":true}]', '/* Custom Print Styles */\n.report-table th { background-color: #f1f3f5 !important; font-weight: 700; }\n.report-table td, .report-table th { padding: 6px 10px; font-size: 12px; border: 1px solid #dee2e6; }', 1, 1, '2026-11-08 07:37:12', '2026-11-08 07:37:12', NULL),
(2, 'Financial & Payslips Template', 'قالب المسيرات والسندات المالية', 'payslip_voucher', 'financial', 'مخصص لمفردات الراتب (Payslip) وسندات القبض والصرف، مع أماكن مخصصة للأختام والاعتماد المالي.', 'A4', 'portrait', '{\"top\":10,\"right\":10,\"bottom\":10,\"left\":10,\"unit\":\"mm\"}', '{\"show_header\":true,\"show_logo\":true,\"logo_position\":\"right\",\"show_company_name\":true,\"show_cr\":true,\"show_tax\":true,\"show_phone\":false,\"show_email\":false,\"show_report_title\":true}', '{\"show_footer\":true,\"show_page_number\":true,\"show_timestamp\":true,\"show_printed_by\":true,\"show_qr_code\":true,\"footer_text\":\"\\u064a\\u0639\\u062a\\u0628\\u0631 \\u0647\\u0630\\u0627 \\u0627\\u0644\\u0645\\u0633\\u062a\\u0646\\u062f \\u0631\\u0633\\u0645\\u064a\\u0627\\u064b \\u0648\\u0633\\u0627\\u0631\\u064a\\u0627\\u064b \\u0628\\u0645\\u0648\\u062c\\u0628 \\u0627\\u0644\\u0623\\u0646\\u0638\\u0645\\u0629 \\u0648\\u0627\\u0644\\u0644\\u0648\\u0627\\u0626\\u062d \\u0627\\u0644\\u0645\\u0627\\u0644\\u064a\\u0629 \\u0627\\u0644\\u0645\\u0639\\u062a\\u0645\\u062f\\u0629.\"}', '[{\"title\":\"\\u0627\\u0644\\u0645\\u062d\\u0627\\u0633\\u0628 \\u0627\\u0644\\u0645\\u0633\\u0624\\u0648\\u0644\",\"title_en\":\"Accountant\",\"name\":\"\",\"show\":true},{\"title\":\"\\u0627\\u0644\\u0645\\u062f\\u064a\\u0631 \\u0627\\u0644\\u0645\\u0627\\u0644\\u064a\",\"title_en\":\"Financial Manager\",\"name\":\"\",\"show\":true},{\"title\":\"\\u062a\\u0648\\u0642\\u064a\\u0639 \\u0627\\u0644\\u0645\\u0633\\u062a\\u0644\\u0645\",\"title_en\":\"Receiver Signature\",\"name\":\"\",\"show\":true}]', '/* Financial Slip Styles */\n.financial-card { border: 2px solid #2b3548; padding: 12px; border-radius: 6px; }\n.amount-box { background: #f8f9fa; font-weight: bold; font-size: 14px; }', 1, 0, '2026-11-08 07:37:12', '2026-11-08 07:37:12', NULL),
(3, 'Official Letters & Certificates Template', 'قالب الخطابات والشهادات الرسمية', 'official_letter', 'letter', 'مخصص لشهادات التعريف بالراتب، خطابات التعيين، إخلاء الطرف، بنمط الورق المروس مع دعم العلامة المائية.', 'A4', 'portrait', '{\"top\":18,\"right\":18,\"bottom\":18,\"left\":18,\"unit\":\"mm\"}', '{\"show_header\":true,\"show_logo\":true,\"logo_position\":\"center\",\"show_company_name\":true,\"show_cr\":true,\"show_tax\":true,\"show_phone\":true,\"show_email\":true,\"show_report_title\":true}', '{\"show_footer\":true,\"show_page_number\":true,\"show_timestamp\":true,\"show_printed_by\":false,\"show_qr_code\":true,\"footer_text\":\"\\u0647\\u0630\\u0627 \\u0627\\u0644\\u062e\\u0637\\u0627\\u0628 \\u0645\\u0648\\u062b\\u0642 \\u0631\\u0633\\u0645\\u064a\\u0627\\u064b \\u0648\\u0635\\u0627\\u062f\\u0631 \\u0625\\u0644\\u0643\\u062a\\u0631\\u0648\\u0646\\u064a\\u0627\\u064b \\u0648\\u0644\\u0627 \\u064a\\u0639\\u062a\\u062f \\u0628\\u0647 \\u062f\\u0648\\u0646 \\u0627\\u0644\\u062a\\u0648\\u0642\\u064a\\u0639 \\u0648\\u0627\\u0644\\u062e\\u062a\\u0645 \\u0627\\u0644\\u0631\\u0633\\u0645\\u064a.\"}', '[{\"title\":\"\\u0645\\u062f\\u064a\\u0631 \\u0627\\u0644\\u0645\\u0648\\u0627\\u0631\\u062f \\u0627\\u0644\\u0628\\u0634\\u0631\\u064a\\u0629\",\"title_en\":\"HR Director\",\"name\":\"\",\"show\":true},{\"title\":\"\\u0627\\u0644\\u062e\\u062a\\u0645 \\u0627\\u0644\\u0631\\u0633\\u0645\\u064a \\u0644\\u0644\\u0645\\u0646\\u0634\\u0623\\u0629\",\"title_en\":\"Official Stamp\",\"name\":\"\",\"show\":true}]', '/* Official Letterhead Styles */\n.letter-body { line-height: 2.2; font-size: 14px; text-align: justify; margin: 30px 10px; }\n.letter-watermark { position: fixed; top: 35%; left: 25%; opacity: 0.05; transform: rotate(-30deg); font-size: 80px; font-weight: bold; pointer-events: none; }', 1, 0, '2026-11-08 07:37:12', '2026-11-08 07:37:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `setting_translations`
--

CREATE TABLE `setting_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `setting_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(10) NOT NULL,
  `site_name` varchar(255) DEFAULT NULL,
  `slogan` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `footer_text` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `setting_translations`
--

INSERT INTO `setting_translations` (`id`, `setting_id`, `locale`, `site_name`, `slogan`, `address`, `footer_text`) VALUES
(1, 1, 'ar', 'نظام إدارة العقارات', 'نظام متكامل لإدارة العقارات والوحدات والعقود', 'دولة الكويت - العاصمة', 'جميع الحقوق محفوظة © 2026 نظام إدارة العقارات'),
(2, 1, 'en', 'Real Estate Management System', 'Comprehensive Real Estate Management System', 'State of Kuwait - Capital City', 'All rights reserved © 2026 Real Estate Management System');

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `checkin_start` time DEFAULT NULL,
  `checkin_end` time DEFAULT NULL,
  `checkout_start` time DEFAULT NULL,
  `checkout_end` time DEFAULT NULL,
  `grace_period_in` smallint(5) UNSIGNED NOT NULL DEFAULT 15,
  `grace_period_out` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `total_hours` decimal(5,2) DEFAULT NULL,
  `is_overnight` tinyint(1) NOT NULL DEFAULT 0,
  `color` varchar(20) NOT NULL DEFAULT '#7367f0',
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shift_translations`
--

CREATE TABLE `shift_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `shift_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `building_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'available',
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `building_id`, `user_id`, `image`, `status`, `order`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, NULL, 'rented', 0, '2026-02-24 09:02:54', '2026-03-05 07:22:30', NULL),
(2, 1, 1, NULL, 'rented', 0, '2026-02-24 10:28:34', '2026-03-05 07:22:30', NULL),
(4, 1, 1, NULL, 'rented', 0, '2026-03-05 09:17:57', '2026-03-05 10:33:36', NULL),
(5, 2, 1, NULL, 'available', 0, '2026-03-05 11:14:38', '2026-03-05 11:14:38', NULL),
(6, 2, 1, NULL, 'unavailable', 0, '2026-03-05 11:14:54', '2026-03-05 11:15:12', NULL),
(7, 2, 1, NULL, 'available', 0, '2026-03-05 11:15:04', '2026-03-05 11:15:04', NULL),
(8, 3, 1, NULL, 'available', 0, '2026-03-05 11:15:47', '2026-03-05 11:15:47', NULL),
(9, 3, 1, NULL, 'unavailable', 0, '2026-03-05 11:15:58', '2026-03-05 11:15:58', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `unit_images`
--

CREATE TABLE `unit_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `unit_id` bigint(20) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `unit_images`
--

INSERT INTO `unit_images` (`id`, `unit_id`, `image_path`, `order`, `created_at`, `updated_at`) VALUES
(29, 1, '2026-02-24-11-02-54-0699d855e25314.webp', 0, '2026-02-24 09:02:54', '2026-02-24 09:02:54'),
(67, 4, '2026-03-05-11-17-57-069a96665cfa7d.webp', 0, '2026-03-05 09:48:22', '2026-03-05 09:48:22'),
(68, 4, '2026-03-05-11-17-57-069a96665d3597.webp', 1, '2026-03-05 09:48:22', '2026-03-05 09:48:22'),
(74, 5, '2026-03-05-01-14-38-069a981bea5771.webp', 0, '2026-03-05 11:14:38', '2026-03-05 11:14:38'),
(75, 6, '2026-03-05-01-15-12-069a981e06d276.', 0, '2026-03-05 11:15:12', '2026-03-05 11:15:12');

-- --------------------------------------------------------

--
-- Table structure for table `unit_translations`
--

CREATE TABLE `unit_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `unit_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(10) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `unit_translations`
--

INSERT INTO `unit_translations` (`id`, `unit_id`, `locale`, `title`, `body`, `created_at`, `updated_at`) VALUES
(1, 1, 'en', 'unit 1', 'unit 1', '2026-11-08 07:55:09', '2026-11-08 07:55:09'),
(2, 1, 'ar', 'وحدة سكنيه قم 1', 'وحدة سكنيه قم 1', '2026-11-08 07:55:09', '2026-11-08 07:55:09'),
(3, 2, 'en', 'unit 5', 'test', '2026-11-08 07:55:09', '2026-11-08 07:55:09'),
(4, 2, 'ar', 'وحده 5', 'تيست', '2026-11-08 07:55:09', '2026-11-08 07:55:09'),
(5, 4, 'en', 'sdfsdf', 'cvxcvxcv', '2026-11-08 07:55:09', '2026-11-08 07:55:09'),
(6, 4, 'ar', 'Facere animi natus 222', 'cxvxcvxcv', '2026-11-08 07:55:09', '2026-11-08 07:55:09'),
(7, 5, 'en', 'unit build 2', 'unit build 2', '2026-11-08 07:55:09', '2026-11-08 07:55:09'),
(8, 5, 'ar', 'وحدة عقار 2', 'وحدة عقار 2', '2026-11-08 07:55:09', '2026-11-08 07:55:09'),
(9, 6, 'en', 'Consectetur reprehen', 'Officia cumque venia', '2026-11-08 07:55:09', '2026-11-08 07:55:09'),
(10, 6, 'ar', 'Enim officia rerum e', 'Praesentium cupidita', '2026-11-08 07:55:09', '2026-11-08 07:55:09'),
(11, 7, 'en', 'Facilis eum dolor si', 'Et totam qui omnis n', '2026-11-08 07:55:09', '2026-11-08 07:55:09'),
(12, 7, 'ar', 'Deleniti anim cupidi', 'Obcaecati consequunt', '2026-11-08 07:55:09', '2026-11-08 07:55:09'),
(13, 8, 'en', 'Sit aliqua Sint e', 'Est repellendus Ad', '2026-11-08 07:55:09', '2026-11-08 07:55:09'),
(14, 8, 'ar', 'Mollit nulla molesti', 'Modi rerum distincti', '2026-11-08 07:55:09', '2026-11-08 07:55:09'),
(15, 9, 'en', 'Sed quis laborum Vo', 'Laudantium autem co', '2026-11-08 07:55:09', '2026-11-08 07:55:09'),
(16, 9, 'ar', 'Praesentium nostrum', 'Cupiditate officiis', '2026-11-08 07:55:09', '2026-11-08 07:55:09');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_type` varchar(255) NOT NULL DEFAULT 'employee',
  `name` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `avatar` varchar(255) DEFAULT NULL,
  `locale` varchar(5) NOT NULL DEFAULT 'ar',
  `remember_token` varchar(100) DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_type`, `name`, `username`, `email`, `phone`, `email_verified_at`, `password`, `is_active`, `avatar`, `locale`, `remember_token`, `last_login_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'developer', 'مدير النظام', NULL, 'admin@gatetech.com', NULL, '2026-11-08 07:36:33', '$2y$12$wkIgL8OzYmrsJ6PNGcIY8.RKEWveTwK/ZU2mkYDcAfDktCPsFc966', 1, NULL, 'ar', NULL, NULL, '2026-11-08 07:36:33', '2026-11-08 07:36:33', NULL),
(2, 'company', 'amr', 'Et exercitationem co', 'jogavivef@mailinator.com', '+96551084200', NULL, '$2y$10$z1R2vjE8vKOj.BjEtyhmw.CnE4AbYqIZAnuLpAG7jyE0xWc/mi8PW', 1, NULL, 'ar', NULL, NULL, '2026-02-24 09:49:54', '2026-02-24 09:49:54', NULL),
(3, 'company', 'Cillum quis nihil ut', 'Rerum delectus face', 'zery@mailinator.com', '+13319442632', NULL, '$2y$10$E1BrVOP.d/Bc2JKgaMY0ceNlWtAsYd2cPhk.RpY5GdKeQ3ETQHY7q', 1, NULL, 'ar', NULL, NULL, '2026-02-26 09:12:51', '2026-02-26 09:12:51', NULL),
(4, 'employee', 'employee', NULL, 'employee@site.com', '2012821542', NULL, '$2y$10$3sXwOW826iE008l0S846dukoGFLZvXFTHYwW4umQXvS0RcWMM7JHq', 1, NULL, 'ar', NULL, NULL, '2026-03-05 06:20:41', '2026-03-05 06:20:41', NULL),
(5, 'employee', 'employee', NULL, 'employee2@site.com', '2012821522342', NULL, '$2y$10$G8fA.w3oO3O7iMBU43EkoOinW6H250ViXAN10yDJ4.Z51KQ05G4Ye', 1, NULL, 'ar', NULL, NULL, '2026-03-05 06:20:58', '2026-03-05 06:20:58', NULL),
(6, 'employee', 'employee', NULL, 'employee5566@site.com', '2012571522342', NULL, '$2y$10$iCzYjeu6eN0drDtl4siwI.CkLjt6781IrdHPA58RxkJ/0Ytta2i1G', 1, NULL, 'ar', NULL, NULL, '2026-03-05 06:21:10', '2026-03-05 06:21:10', NULL),
(7, 'employee', 'employee', NULL, 'employee788@site.com', '2012571232342', NULL, '$2y$10$eeein/RJRkXFbcV1jrF2MOoYLV.VYS5WS6rU8uizvBJHW41LE7NBq', 1, NULL, 'ar', NULL, NULL, '2026-03-05 06:33:42', '2026-03-05 06:33:42', NULL),
(8, 'company', 'mohamed elfeky', 'mohamed elfeky', 'mohamedwaged1@gmail.com', '+201011320299', NULL, '$2y$10$vJk/RMUdi2PsN4TTpGjapuU8A9TUIyShG9TUOQWzQcpqpW2cDYyh6', 1, NULL, 'ar', NULL, NULL, '2026-03-05 10:20:43', '2026-03-05 10:20:43', NULL),
(9, 'company', 'شركة المستقبل', NULL, 'ayman@dasman.com', '+201090460119', NULL, '$2y$10$vaQZR.WKZLyYCL4zOX5L5.f5.1n7pIpG9BSQE6hr/InVpkJ7LHrTG', 1, NULL, 'ar', NULL, NULL, '2026-04-20 06:49:34', '2026-04-20 06:49:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `plate_number` varchar(255) DEFAULT NULL,
  `owner_name` varchar(255) DEFAULT NULL,
  `receiver_name` varchar(255) DEFAULT NULL,
  `registration_expiry_date` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_translations`
--

CREATE TABLE `vehicle_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `work_schedules`
--

CREATE TABLE `work_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'fixed',
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `working_days_count` tinyint(3) UNSIGNED NOT NULL DEFAULT 5,
  `total_weekly_hours` decimal(5,2) NOT NULL DEFAULT 0.00,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `color` varchar(20) NOT NULL DEFAULT '#7367f0',
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `work_schedule_days`
--

CREATE TABLE `work_schedule_days` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `work_schedule_id` bigint(20) UNSIGNED NOT NULL,
  `day_of_week` tinyint(3) UNSIGNED NOT NULL,
  `is_off_day` tinyint(1) NOT NULL DEFAULT 0,
  `shift_id` bigint(20) UNSIGNED DEFAULT NULL,
  `second_shift_id` bigint(20) UNSIGNED DEFAULT NULL,
  `work_hours` decimal(4,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `work_schedule_translations`
--

CREATE TABLE `work_schedule_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `work_schedule_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounting_cost_centers`
--
ALTER TABLE `accounting_cost_centers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `accounting_cost_centers_entity_id_code_unique` (`entity_id`,`code`),
  ADD KEY `accounting_cost_centers_branch_id_foreign` (`branch_id`),
  ADD KEY `accounting_cost_centers_created_by_foreign` (`created_by`),
  ADD KEY `accounting_cost_centers_entity_id_status_index` (`entity_id`,`status`),
  ADD KEY `accounting_cost_centers_entity_id_type_index` (`entity_id`,`type`),
  ADD KEY `accounting_cost_centers_parent_id_index` (`parent_id`),
  ADD KEY `accounting_cost_centers_type_index` (`type`),
  ADD KEY `accounting_cost_centers_status_index` (`status`);

--
-- Indexes for table `accounting_cost_center_translations`
--
ALTER TABLE `accounting_cost_center_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `accounting_cost_center_translations_cost_center_id_locale_unique` (`cost_center_id`,`locale`),
  ADD KEY `accounting_cost_center_translations_locale_index` (`locale`);

--
-- Indexes for table `accounting_lock_dates`
--
ALTER TABLE `accounting_lock_dates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `accounting_lock_dates_entity_id_unique` (`entity_id`),
  ADD KEY `accounting_lock_dates_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `accounting_lock_exceptions`
--
ALTER TABLE `accounting_lock_exceptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `accounting_lock_exceptions_created_by_foreign` (`created_by`),
  ADD KEY `accounting_lock_exceptions_revoked_by_foreign` (`revoked_by`),
  ADD KEY `accounting_lock_exceptions_entity_id_lock_type_index` (`entity_id`,`lock_type`),
  ADD KEY `accounting_lock_exceptions_user_id_valid_from_valid_until_index` (`user_id`,`valid_from`,`valid_until`),
  ADD KEY `accounting_lock_exceptions_revoked_at_index` (`revoked_at`);

--
-- Indexes for table `accounting_payment_matchings`
--
ALTER TABLE `accounting_payment_matchings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `accounting_payment_matchings_debit_line_id_foreign` (`debit_line_id`),
  ADD KEY `accounting_payment_matchings_credit_line_id_foreign` (`credit_line_id`),
  ADD KEY `accounting_payment_matchings_matched_by_foreign` (`matched_by`),
  ADD KEY `accounting_payment_matchings_entity_id_debit_line_id_index` (`entity_id`,`debit_line_id`),
  ADD KEY `accounting_payment_matchings_entity_id_credit_line_id_index` (`entity_id`,`credit_line_id`);

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `accounts_entity_id_code_unique` (`entity_id`,`code`),
  ADD KEY `accounts_parent_id_foreign` (`parent_id`),
  ADD KEY `accounts_created_by_foreign` (`created_by`),
  ADD KEY `accounts_entity_id_parent_id_index` (`entity_id`,`parent_id`),
  ADD KEY `accounts_entity_id_level_index` (`entity_id`,`level`),
  ADD KEY `accounts_account_type_index` (`account_type`),
  ADD KEY `accounts_is_active_index` (`is_active`);

--
-- Indexes for table `account_mappings`
--
ALTER TABLE `account_mappings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `entity_operation_unique` (`entity_id`,`operation_key`),
  ADD KEY `account_mappings_debit_account_id_foreign` (`debit_account_id`),
  ADD KEY `account_mappings_credit_account_id_foreign` (`credit_account_id`),
  ADD KEY `account_mappings_fee_account_id_foreign` (`fee_account_id`),
  ADD KEY `account_mappings_journal_id_foreign` (`journal_id`),
  ADD KEY `account_mappings_operation_key_index` (`operation_key`);

--
-- Indexes for table `account_translations`
--
ALTER TABLE `account_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `account_translations_account_id_locale_unique` (`account_id`,`locale`),
  ADD KEY `account_translations_locale_index` (`locale`);

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject` (`subject_type`,`subject_id`),
  ADD KEY `causer` (`causer_type`,`causer_id`),
  ADD KEY `activity_log_log_name_index` (`log_name`);

--
-- Indexes for table `ai_providers`
--
ALTER TABLE `ai_providers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ai_providers_code_unique` (`code`),
  ADD KEY `ai_providers_is_active_index` (`is_active`);

--
-- Indexes for table `ai_tasks`
--
ALTER TABLE `ai_tasks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ai_tasks_code_unique` (`code`),
  ADD KEY `ai_tasks_is_active_index` (`is_active`);

--
-- Indexes for table `ai_task_providers`
--
ALTER TABLE `ai_task_providers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ai_task_providers_task_id_provider_id_unique` (`task_id`,`provider_id`),
  ADD KEY `ai_task_providers_provider_id_foreign` (`provider_id`),
  ADD KEY `ai_task_providers_task_id_is_default_priority_index` (`task_id`,`is_default`,`priority`);

--
-- Indexes for table `ai_usage_logs`
--
ALTER TABLE `ai_usage_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ai_usage_logs_user_id_foreign` (`user_id`),
  ADD KEY `ai_usage_logs_model_created_at_index` (`model`,`created_at`),
  ADD KEY `ai_usage_logs_driver_created_at_index` (`driver`,`created_at`),
  ADD KEY `ai_usage_logs_provider_code_index` (`provider_code`),
  ADD KEY `ai_usage_logs_driver_index` (`driver`),
  ADD KEY `ai_usage_logs_model_index` (`model`),
  ADD KEY `ai_usage_logs_task_code_index` (`task_code`);

--
-- Indexes for table `attachmentables`
--
ALTER TABLE `attachmentables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attachmentables_attachmentable_type_attachmentable_id_index` (`attachmentable_type`,`attachmentable_id`);

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attendances_employee_id_date_unique` (`employee_id`,`date`),
  ADD KEY `attendances_shift_id_foreign` (`shift_id`),
  ADD KEY `attendances_adjusted_by_foreign` (`adjusted_by`),
  ADD KEY `attendances_created_by_foreign` (`created_by`),
  ADD KEY `attendances_date_index` (`date`),
  ADD KEY `attendances_status_index` (`status`);

--
-- Indexes for table `attendance_raw_logs`
--
ALTER TABLE `attendance_raw_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendance_raw_logs_employee_id_foreign` (`employee_id`),
  ADD KEY `attendance_raw_logs_device_id_foreign` (`device_id`),
  ADD KEY `attendance_raw_logs_created_by_foreign` (`created_by`),
  ADD KEY `attendance_raw_logs_device_user_id_index` (`device_user_id`),
  ADD KEY `attendance_raw_logs_punch_time_index` (`punch_time`),
  ADD KEY `attendance_raw_logs_source_index` (`source`),
  ADD KEY `attendance_raw_logs_is_processed_index` (`is_processed`);

--
-- Indexes for table `audit_log_settings`
--
ALTER TABLE `audit_log_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banks`
--
ALTER TABLE `banks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `banks_parent_id_foreign` (`parent_id`),
  ADD KEY `banks_created_by_foreign` (`created_by`),
  ADD KEY `banks_code_index` (`code`),
  ADD KEY `banks_swift_code_index` (`swift_code`),
  ADD KEY `banks_status_index` (`status`);

--
-- Indexes for table `bank_reconciliations`
--
ALTER TABLE `bank_reconciliations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bank_reconciliations_entity_id_reconciliation_number_unique` (`entity_id`,`reconciliation_number`),
  ADD KEY `bank_reconciliations_cash_account_id_foreign` (`cash_account_id`),
  ADD KEY `bank_reconciliations_reconciled_by_foreign` (`reconciled_by`),
  ADD KEY `bank_reconciliations_created_by_foreign` (`created_by`),
  ADD KEY `bank_reconciliations_updated_by_foreign` (`updated_by`),
  ADD KEY `bank_rec_entity_acc_date_idx` (`entity_id`,`cash_account_id`,`statement_date`),
  ADD KEY `bank_reconciliations_status_index` (`status`);

--
-- Indexes for table `bank_reconciliation_items`
--
ALTER TABLE `bank_reconciliation_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bank_reconciliation_items_journal_entry_line_id_foreign` (`journal_entry_line_id`),
  ADD KEY `bank_reconciliation_items_created_by_foreign` (`created_by`),
  ADD KEY `rec_items_rec_line_idx` (`bank_reconciliation_id`,`journal_entry_line_id`),
  ADD KEY `bank_reconciliation_items_type_index` (`type`),
  ADD KEY `bank_reconciliation_items_is_cleared_index` (`is_cleared`);

--
-- Indexes for table `bank_translations`
--
ALTER TABLE `bank_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bank_translations_bank_id_locale_unique` (`bank_id`,`locale`),
  ADD KEY `bank_translations_locale_index` (`locale`);

--
-- Indexes for table `bills`
--
ALTER TABLE `bills`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bills_bill_number_unique` (`bill_number`),
  ADD KEY `bills_company_id_foreign` (`company_id`),
  ADD KEY `bills_user_id_foreign` (`user_id`),
  ADD KEY `bills_contract_id_foreign` (`contract_id`);

--
-- Indexes for table `bill_payment_dues`
--
ALTER TABLE `bill_payment_dues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bill_payment_dues_bill_id_foreign` (`bill_id`),
  ADD KEY `bill_payment_dues_contract_id_foreign` (`contract_id`);

--
-- Indexes for table `bill_short_links`
--
ALTER TABLE `bill_short_links`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bill_short_links_code_unique` (`code`),
  ADD KEY `bill_short_links_bill_id_foreign` (`bill_id`);

--
-- Indexes for table `bill_transactions`
--
ALTER TABLE `bill_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bill_transactions_bill_id_foreign` (`bill_id`),
  ADD KEY `bill_transactions_transaction_id_index` (`transaction_id`),
  ADD KEY `bill_transactions_reference_id_index` (`reference_id`);

--
-- Indexes for table `bill_translations`
--
ALTER TABLE `bill_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bill_translations_bill_id_locale_unique` (`bill_id`,`locale`),
  ADD KEY `bill_translations_locale_index` (`locale`);

--
-- Indexes for table `bill_warnings`
--
ALTER TABLE `bill_warnings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bill_warnings_bill_id_foreign` (`bill_id`);

--
-- Indexes for table `biometric_devices`
--
ALTER TABLE `biometric_devices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `biometric_devices_branch_id_foreign` (`branch_id`),
  ADD KEY `biometric_devices_created_by_foreign` (`created_by`),
  ADD KEY `biometric_devices_status_index` (`status`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branches_code_unique` (`code`),
  ADD KEY `branches_company_id_foreign` (`company_id`),
  ADD KEY `branches_government_id_foreign` (`government_id`),
  ADD KEY `branches_city_id_foreign` (`city_id`),
  ADD KEY `branches_manager_id_foreign` (`manager_id`),
  ADD KEY `branches_created_by_foreign` (`created_by`),
  ADD KEY `branches_status_index` (`status`);

--
-- Indexes for table `branch_translations`
--
ALTER TABLE `branch_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branch_translations_branch_id_locale_unique` (`branch_id`,`locale`),
  ADD KEY `branch_translations_locale_index` (`locale`);

--
-- Indexes for table `buildings`
--
ALTER TABLE `buildings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `buildings_building_type_id_foreign` (`building_type_id`),
  ADD KEY `buildings_user_id_foreign` (`user_id`);

--
-- Indexes for table `building_images`
--
ALTER TABLE `building_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `building_images_building_id_foreign` (`building_id`);

--
-- Indexes for table `building_translations`
--
ALTER TABLE `building_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `building_translations_building_id_locale_unique` (`building_id`,`locale`),
  ADD KEY `building_translations_locale_index` (`locale`);

--
-- Indexes for table `building_types`
--
ALTER TABLE `building_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `building_types_user_id_foreign` (`user_id`);

--
-- Indexes for table `building_type_translations`
--
ALTER TABLE `building_type_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `building_type_translations_building_type_id_locale_unique` (`building_type_id`,`locale`),
  ADD KEY `building_type_translations_locale_index` (`locale`);

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
-- Indexes for table `cash_accounts`
--
ALTER TABLE `cash_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cash_accounts_entity_id_code_unique` (`entity_id`,`code`),
  ADD KEY `cash_accounts_account_id_foreign` (`account_id`),
  ADD KEY `cash_accounts_journal_id_foreign` (`journal_id`),
  ADD KEY `cash_accounts_responsible_user_id_foreign` (`responsible_user_id`),
  ADD KEY `cash_accounts_created_by_foreign` (`created_by`),
  ADD KEY `cash_accounts_entity_id_type_index` (`entity_id`,`type`),
  ADD KEY `cash_accounts_type_index` (`type`),
  ADD KEY `cash_accounts_is_active_index` (`is_active`);

--
-- Indexes for table `cash_account_translations`
--
ALTER TABLE `cash_account_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cash_account_translations_cash_account_id_locale_unique` (`cash_account_id`,`locale`),
  ADD KEY `cash_account_translations_locale_index` (`locale`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `companies_user_id_foreign` (`user_id`),
  ADD KEY `companies_company_type_id_foreign` (`company_type_id`);

--
-- Indexes for table `company_licenses`
--
ALTER TABLE `company_licenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_licenses_user_id_foreign` (`user_id`),
  ADD KEY `company_licenses_license_id_foreign` (`license_id`),
  ADD KEY `company_licenses_company_id_foreign` (`company_id`);

--
-- Indexes for table `company_license_translations`
--
ALTER TABLE `company_license_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `comp_lic_trans_unique` (`company_license_id`,`locale`),
  ADD KEY `company_license_translations_locale_index` (`locale`);

--
-- Indexes for table `company_translations`
--
ALTER TABLE `company_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `company_translations_company_id_locale_unique` (`company_id`,`locale`),
  ADD KEY `company_translations_locale_index` (`locale`);

--
-- Indexes for table `company_types`
--
ALTER TABLE `company_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company_type_translations`
--
ALTER TABLE `company_type_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `company_type_translations_company_type_id_locale_unique` (`company_type_id`,`locale`),
  ADD KEY `company_type_translations_locale_index` (`locale`);

--
-- Indexes for table `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contracts_company_id_foreign` (`company_id`),
  ADD KEY `contracts_user_id_foreign` (`user_id`),
  ADD KEY `contracts_contract_type_id_foreign` (`contract_type_id`),
  ADD KEY `contracts_building_id_foreign` (`building_id`);

--
-- Indexes for table `contract_translations`
--
ALTER TABLE `contract_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `contract_translations_contract_id_locale_unique` (`contract_id`,`locale`),
  ADD KEY `contract_translations_locale_index` (`locale`);

--
-- Indexes for table `contract_types`
--
ALTER TABLE `contract_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contract_types_user_id_foreign` (`user_id`);

--
-- Indexes for table `contract_type_translations`
--
ALTER TABLE `contract_type_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `contract_type_translations_contract_type_id_locale_unique` (`contract_type_id`,`locale`),
  ADD KEY `contract_type_translations_locale_index` (`locale`);

--
-- Indexes for table `contract_units`
--
ALTER TABLE `contract_units`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `contract_units_contract_id_unit_id_unique` (`contract_id`,`unit_id`),
  ADD KEY `contract_units_unit_id_foreign` (`unit_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `departments_code_unique` (`code`),
  ADD KEY `departments_company_id_foreign` (`company_id`),
  ADD KEY `departments_branch_id_foreign` (`branch_id`),
  ADD KEY `departments_parent_id_foreign` (`parent_id`),
  ADD KEY `departments_manager_id_foreign` (`manager_id`),
  ADD KEY `departments_created_by_foreign` (`created_by`),
  ADD KEY `departments_status_index` (`status`);

--
-- Indexes for table `department_translations`
--
ALTER TABLE `department_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `department_translations_department_id_locale_unique` (`department_id`,`locale`),
  ADD KEY `department_translations_locale_index` (`locale`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employees_employee_code_unique` (`employee_code`),
  ADD UNIQUE KEY `employees_national_id_unique` (`national_id`),
  ADD KEY `employees_user_id_foreign` (`user_id`),
  ADD KEY `employees_visa_type_id_foreign` (`visa_type_id`),
  ADD KEY `employees_primary_branch_id_foreign` (`primary_branch_id`),
  ADD KEY `employees_department_id_foreign` (`department_id`),
  ADD KEY `employees_job_title_id_foreign` (`job_title_id`),
  ADD KEY `employees_manager_id_foreign` (`manager_id`),
  ADD KEY `employees_work_schedule_id_foreign` (`work_schedule_id`),
  ADD KEY `employees_shift_id_foreign` (`shift_id`),
  ADD KEY `employees_governorate_id_foreign` (`governorate_id`),
  ADD KEY `employees_bank_id_foreign` (`bank_id`),
  ADD KEY `employees_bank_branch_id_foreign` (`bank_branch_id`),
  ADD KEY `employees_created_by_foreign` (`created_by`),
  ADD KEY `employees_hire_date_index` (`hire_date`),
  ADD KEY `employees_status_index` (`status`),
  ADD KEY `employees_phone_index` (`phone`),
  ADD KEY `employees_is_teacher_index` (`is_teacher`);

--
-- Indexes for table `employee_allowances`
--
ALTER TABLE `employee_allowances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_allowances_employee_id_foreign` (`employee_id`),
  ADD KEY `employee_allowances_allowance_type_id_foreign` (`allowance_type_id`);

--
-- Indexes for table `employee_branches`
--
ALTER TABLE `employee_branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `emp_branches_emp_branch_unique` (`employee_id`,`branch_id`),
  ADD KEY `employee_branches_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `employee_documents`
--
ALTER TABLE `employee_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_documents_employee_id_foreign` (`employee_id`),
  ADD KEY `employee_documents_document_type_id_foreign` (`document_type_id`),
  ADD KEY `employee_documents_expiry_date_index` (`expiry_date`);

--
-- Indexes for table `employee_notification_preferences`
--
ALTER TABLE `employee_notification_preferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_notification_preferences_user_id_unique` (`user_id`),
  ADD KEY `employee_notification_preferences_employee_id_index` (`employee_id`),
  ADD KEY `employee_notification_preferences_user_id_index` (`user_id`);

--
-- Indexes for table `employee_translations`
--
ALTER TABLE `employee_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `emp_trans_emp_id_locale_unique` (`employee_id`,`locale`),
  ADD KEY `employee_translations_locale_index` (`locale`);

--
-- Indexes for table `entities`
--
ALTER TABLE `entities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `entities_code_unique` (`code`),
  ADD UNIQUE KEY `entities_company_id_unique` (`company_id`),
  ADD KEY `entities_created_by_foreign` (`created_by`),
  ADD KEY `entities_type_index` (`type`),
  ADD KEY `entities_status_index` (`status`);

--
-- Indexes for table `entity_translations`
--
ALTER TABLE `entity_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `entity_translations_entity_id_locale_unique` (`entity_id`,`locale`),
  ADD KEY `entity_translations_locale_index` (`locale`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `financial_vouchers`
--
ALTER TABLE `financial_vouchers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `financial_vouchers_user_id_foreign` (`user_id`),
  ADD KEY `financial_vouchers_company_id_foreign` (`company_id`),
  ADD KEY `financial_vouchers_contract_id_foreign` (`contract_id`);

--
-- Indexes for table `financial_voucher_translations`
--
ALTER TABLE `financial_voucher_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fin_vouch_trans_unique` (`financial_voucher_id`,`locale`),
  ADD KEY `financial_voucher_translations_locale_index` (`locale`);

--
-- Indexes for table `fiscal_years`
--
ALTER TABLE `fiscal_years`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fiscal_years_entity_id_start_date_end_date_unique` (`entity_id`,`start_date`,`end_date`),
  ADD KEY `fiscal_years_created_by_foreign` (`created_by`),
  ADD KEY `fiscal_years_closed_by_foreign` (`closed_by`),
  ADD KEY `fiscal_years_entity_id_start_date_end_date_index` (`entity_id`,`start_date`,`end_date`),
  ADD KEY `fiscal_years_status_index` (`status`);

--
-- Indexes for table `fiscal_year_translations`
--
ALTER TABLE `fiscal_year_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fiscal_year_translations_fiscal_year_id_locale_unique` (`fiscal_year_id`,`locale`),
  ADD KEY `fiscal_year_translations_locale_index` (`locale`);

--
-- Indexes for table `general_settings`
--
ALTER TABLE `general_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `general_settings_parent_id_foreign` (`parent_id`),
  ADD KEY `general_settings_created_by_foreign` (`created_by`),
  ADD KEY `general_settings_type_index` (`type`);

--
-- Indexes for table `general_setting_translations`
--
ALTER TABLE `general_setting_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `general_setting_translations_general_setting_id_locale_unique` (`general_setting_id`,`locale`),
  ADD KEY `general_setting_translations_locale_index` (`locale`);

--
-- Indexes for table `hr_candidates`
--
ALTER TABLE `hr_candidates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_candidates_created_by_foreign` (`created_by`),
  ADD KEY `hr_candidates_full_name_index` (`full_name`),
  ADD KEY `hr_candidates_email_index` (`email`);

--
-- Indexes for table `hr_eos_rules`
--
ALTER TABLE `hr_eos_rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_eos_rules_type_index` (`type`);

--
-- Indexes for table `hr_interviews`
--
ALTER TABLE `hr_interviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_interviews_job_opening_id_foreign` (`job_opening_id`),
  ADD KEY `hr_interviews_candidate_id_foreign` (`candidate_id`),
  ADD KEY `hr_interviews_created_by_foreign` (`created_by`),
  ADD KEY `hr_interviews_job_application_id_status_index` (`job_application_id`,`status`),
  ADD KEY `hr_interviews_scheduled_at_index` (`scheduled_at`);

--
-- Indexes for table `hr_interview_evaluations`
--
ALTER TABLE `hr_interview_evaluations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_interview_evaluations_interview_id_interviewer_id_unique` (`interview_id`,`interviewer_id`),
  ADD KEY `hr_interview_evaluations_interviewer_id_foreign` (`interviewer_id`);

--
-- Indexes for table `hr_interview_interviewers`
--
ALTER TABLE `hr_interview_interviewers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_interview_interviewers_interview_id_employee_id_unique` (`interview_id`,`employee_id`),
  ADD KEY `hr_interview_interviewers_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `hr_job_applications`
--
ALTER TABLE `hr_job_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_job_applications_application_code_unique` (`application_code`),
  ADD KEY `hr_job_applications_candidate_id_foreign` (`candidate_id`),
  ADD KEY `hr_job_applications_created_by_foreign` (`created_by`),
  ADD KEY `hr_job_applications_job_opening_id_stage_index` (`job_opening_id`,`stage`),
  ADD KEY `hr_job_applications_job_opening_id_status_index` (`job_opening_id`,`status`),
  ADD KEY `hr_job_applications_stage_index` (`stage`),
  ADD KEY `hr_job_applications_status_index` (`status`);

--
-- Indexes for table `hr_job_offers`
--
ALTER TABLE `hr_job_offers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_job_offers_offer_code_unique` (`offer_code`),
  ADD KEY `hr_job_offers_job_application_id_foreign` (`job_application_id`),
  ADD KEY `hr_job_offers_job_opening_id_foreign` (`job_opening_id`),
  ADD KEY `hr_job_offers_department_id_foreign` (`department_id`),
  ADD KEY `hr_job_offers_job_title_id_foreign` (`job_title_id`),
  ADD KEY `hr_job_offers_approved_by_foreign` (`approved_by`),
  ADD KEY `hr_job_offers_created_by_foreign` (`created_by`),
  ADD KEY `hr_job_offers_candidate_id_status_index` (`candidate_id`,`status`),
  ADD KEY `hr_job_offers_offer_date_index` (`offer_date`);

--
-- Indexes for table `hr_job_openings`
--
ALTER TABLE `hr_job_openings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_job_openings_opening_code_unique` (`opening_code`),
  ADD KEY `hr_job_openings_job_requisition_id_foreign` (`job_requisition_id`),
  ADD KEY `hr_job_openings_job_title_id_foreign` (`job_title_id`),
  ADD KEY `hr_job_openings_department_id_foreign` (`department_id`),
  ADD KEY `hr_job_openings_branch_id_foreign` (`branch_id`),
  ADD KEY `hr_job_openings_created_by_foreign` (`created_by`),
  ADD KEY `hr_job_openings_published_by_foreign` (`published_by`);

--
-- Indexes for table `hr_job_opening_translations`
--
ALTER TABLE `hr_job_opening_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_job_opening_translations_job_opening_id_locale_unique` (`job_opening_id`,`locale`),
  ADD KEY `hr_job_opening_translations_locale_index` (`locale`);

--
-- Indexes for table `hr_job_requisitions`
--
ALTER TABLE `hr_job_requisitions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_job_requisitions_requisition_number_unique` (`requisition_number`),
  ADD KEY `hr_job_requisitions_workforce_plan_item_id_foreign` (`workforce_plan_item_id`),
  ADD KEY `hr_job_requisitions_department_id_foreign` (`department_id`),
  ADD KEY `hr_job_requisitions_branch_id_foreign` (`branch_id`),
  ADD KEY `hr_job_requisitions_job_title_id_foreign` (`job_title_id`),
  ADD KEY `hr_job_requisitions_requested_by_foreign` (`requested_by`),
  ADD KEY `hr_job_requisitions_replacement_for_employee_id_foreign` (`replacement_for_employee_id`),
  ADD KEY `hr_job_requisitions_approved_by_foreign` (`approved_by`),
  ADD KEY `hr_job_requisitions_created_by_foreign` (`created_by`),
  ADD KEY `hr_job_requisitions_status_department_id_index` (`status`,`department_id`),
  ADD KEY `hr_job_requisitions_is_budgeted_status_index` (`is_budgeted`,`status`);

--
-- Indexes for table `hr_leave_requests`
--
ALTER TABLE `hr_leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_leave_requests_employee_id_foreign` (`employee_id`),
  ADD KEY `hr_leave_requests_replacement_employee_id_foreign` (`replacement_employee_id`),
  ADD KEY `hr_leave_requests_manager_approved_by_foreign` (`manager_approved_by`),
  ADD KEY `hr_leave_requests_hr_approved_by_foreign` (`hr_approved_by`),
  ADD KEY `hr_leave_requests_created_by_foreign` (`created_by`),
  ADD KEY `hr_leave_requests_updated_by_foreign` (`updated_by`),
  ADD KEY `hr_leave_requests_leave_type_index` (`leave_type`),
  ADD KEY `hr_leave_requests_status_index` (`status`);

--
-- Indexes for table `hr_loans`
--
ALTER TABLE `hr_loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_loans_created_by_foreign` (`created_by`),
  ADD KEY `hr_loans_manager_approved_by_foreign` (`manager_approved_by`),
  ADD KEY `hr_loans_hr_approved_by_foreign` (`hr_approved_by`),
  ADD KEY `hr_loans_employee_id_status_index` (`employee_id`,`status`),
  ADD KEY `hr_loans_start_date_index` (`start_date`);

--
-- Indexes for table `hr_loan_installments`
--
ALTER TABLE `hr_loan_installments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_loan_installments_loan_id_status_index` (`loan_id`,`status`),
  ADD KEY `hr_loan_installments_due_date_index` (`due_date`);

--
-- Indexes for table `hr_onboardings`
--
ALTER TABLE `hr_onboardings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_onboardings_onboarding_code_unique` (`onboarding_code`),
  ADD KEY `hr_onboardings_candidate_id_foreign` (`candidate_id`),
  ADD KEY `hr_onboardings_job_application_id_foreign` (`job_application_id`),
  ADD KEY `hr_onboardings_job_offer_id_foreign` (`job_offer_id`),
  ADD KEY `hr_onboardings_employee_id_foreign` (`employee_id`),
  ADD KEY `hr_onboardings_department_id_foreign` (`department_id`),
  ADD KEY `hr_onboardings_job_title_id_foreign` (`job_title_id`),
  ADD KEY `hr_onboardings_buddy_employee_id_foreign` (`buddy_employee_id`),
  ADD KEY `hr_onboardings_created_by_foreign` (`created_by`),
  ADD KEY `hr_onboardings_status_start_date_index` (`status`,`start_date`);

--
-- Indexes for table `hr_onboarding_tasks`
--
ALTER TABLE `hr_onboarding_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_onboarding_tasks_assigned_to_foreign` (`assigned_to`),
  ADD KEY `hr_onboarding_tasks_onboarding_id_is_completed_index` (`onboarding_id`,`is_completed`);

--
-- Indexes for table `hr_payrolls`
--
ALTER TABLE `hr_payrolls`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_payrolls_payroll_number_unique` (`payroll_number`),
  ADD KEY `hr_payrolls_approved_by_foreign` (`approved_by`),
  ADD KEY `hr_payrolls_paid_by_foreign` (`paid_by`),
  ADD KEY `hr_payrolls_cancelled_by_foreign` (`cancelled_by`),
  ADD KEY `hr_payrolls_created_by_foreign` (`created_by`),
  ADD KEY `hr_payrolls_payroll_month_index` (`payroll_month`),
  ADD KEY `hr_payrolls_status_index` (`status`),
  ADD KEY `hr_payrolls_payroll_month_status_index` (`payroll_month`,`status`),
  ADD KEY `hr_payrolls_branch_id_index` (`branch_id`);

--
-- Indexes for table `hr_payroll_items`
--
ALTER TABLE `hr_payroll_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_payroll_items_payroll_id_employee_id_unique` (`payroll_id`,`employee_id`),
  ADD KEY `hr_payroll_items_employee_id_payment_status_index` (`employee_id`,`payment_status`);

--
-- Indexes for table `hr_penalties`
--
ALTER TABLE `hr_penalties`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_penalties_penalty_number_unique` (`penalty_number`),
  ADD KEY `hr_penalties_approved_by_foreign` (`approved_by`),
  ADD KEY `hr_penalties_deducted_by_foreign` (`deducted_by`),
  ADD KEY `hr_penalties_cancelled_by_foreign` (`cancelled_by`),
  ADD KEY `hr_penalties_created_by_foreign` (`created_by`),
  ADD KEY `hr_penalties_employee_id_status_index` (`employee_id`,`status`),
  ADD KEY `hr_penalties_penalty_type_id_status_index` (`penalty_type_id`,`status`),
  ADD KEY `hr_penalties_penalty_date_index` (`penalty_date`),
  ADD KEY `hr_penalties_payroll_month_index` (`payroll_month`);

--
-- Indexes for table `hr_permission_requests`
--
ALTER TABLE `hr_permission_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_permission_requests_employee_id_foreign` (`employee_id`),
  ADD KEY `hr_permission_requests_created_by_foreign` (`created_by`),
  ADD KEY `hr_permission_requests_manager_approved_by_foreign` (`manager_approved_by`),
  ADD KEY `hr_permission_requests_hr_approved_by_foreign` (`hr_approved_by`),
  ADD KEY `hr_permission_requests_date_index` (`date`),
  ADD KEY `hr_permission_requests_type_index` (`type`),
  ADD KEY `hr_permission_requests_status_index` (`status`),
  ADD KEY `hr_permission_requests_is_emergency_index` (`is_emergency`);

--
-- Indexes for table `hr_rewards`
--
ALTER TABLE `hr_rewards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_rewards_reward_number_unique` (`reward_number`),
  ADD KEY `hr_rewards_approved_by_foreign` (`approved_by`),
  ADD KEY `hr_rewards_paid_by_foreign` (`paid_by`),
  ADD KEY `hr_rewards_rejected_by_foreign` (`rejected_by`),
  ADD KEY `hr_rewards_created_by_foreign` (`created_by`),
  ADD KEY `hr_rewards_employee_id_status_index` (`employee_id`,`status`),
  ADD KEY `hr_rewards_reward_type_id_status_index` (`reward_type_id`,`status`),
  ADD KEY `hr_rewards_award_date_index` (`award_date`),
  ADD KEY `hr_rewards_payroll_month_index` (`payroll_month`);

--
-- Indexes for table `hr_settings`
--
ALTER TABLE `hr_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_settings_parent_id_foreign` (`parent_id`),
  ADD KEY `hr_settings_created_by_foreign` (`created_by`),
  ADD KEY `hr_settings_type_index` (`type`),
  ADD KEY `hr_settings_status_index` (`status`);

--
-- Indexes for table `hr_setting_translations`
--
ALTER TABLE `hr_setting_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_setting_translations_hr_setting_id_locale_unique` (`hr_setting_id`,`locale`),
  ADD KEY `hr_setting_translations_locale_index` (`locale`);

--
-- Indexes for table `hr_sick_leave_tiers`
--
ALTER TABLE `hr_sick_leave_tiers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hr_workforce_plans`
--
ALTER TABLE `hr_workforce_plans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_workforce_plans_plan_code_unique` (`plan_code`),
  ADD KEY `hr_workforce_plans_created_by_foreign` (`created_by`),
  ADD KEY `hr_workforce_plans_approved_by_foreign` (`approved_by`),
  ADD KEY `hr_workforce_plans_year_status_index` (`year`,`status`);

--
-- Indexes for table `hr_workforce_plan_items`
--
ALTER TABLE `hr_workforce_plan_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_workforce_plan_items_branch_id_foreign` (`branch_id`),
  ADD KEY `hr_workforce_plan_items_job_title_id_foreign` (`job_title_id`),
  ADD KEY `hr_workforce_plan_items_workforce_plan_id_department_id_index` (`workforce_plan_id`,`department_id`),
  ADD KEY `hr_workforce_plan_items_department_id_job_title_id_index` (`department_id`,`job_title_id`);

--
-- Indexes for table `hr_work_policies`
--
ALTER TABLE `hr_work_policies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_work_policies_key_unique` (`key`),
  ADD KEY `hr_work_policies_group_index` (`group`);

--
-- Indexes for table `imageables`
--
ALTER TABLE `imageables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `imageables_imageable_type_imageable_id_index` (`imageable_type`,`imageable_id`);

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
-- Indexes for table `job_titles`
--
ALTER TABLE `job_titles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `job_titles_code_unique` (`code`),
  ADD KEY `job_titles_department_id_foreign` (`department_id`),
  ADD KEY `job_titles_created_by_foreign` (`created_by`),
  ADD KEY `job_titles_status_index` (`status`);

--
-- Indexes for table `job_title_translations`
--
ALTER TABLE `job_title_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `job_title_translations_job_title_id_locale_unique` (`job_title_id`,`locale`),
  ADD KEY `job_title_translations_locale_index` (`locale`);

--
-- Indexes for table `journals`
--
ALTER TABLE `journals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `journals_entity_id_code_unique` (`entity_id`,`code`),
  ADD KEY `journals_default_account_id_foreign` (`default_account_id`),
  ADD KEY `journals_suspense_account_id_foreign` (`suspense_account_id`),
  ADD KEY `journals_profit_account_id_foreign` (`profit_account_id`),
  ADD KEY `journals_loss_account_id_foreign` (`loss_account_id`),
  ADD KEY `journals_created_by_foreign` (`created_by`),
  ADD KEY `journals_entity_id_type_index` (`entity_id`,`type`),
  ADD KEY `journals_type_index` (`type`),
  ADD KEY `journals_is_active_index` (`is_active`);

--
-- Indexes for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `journal_entries_entity_id_entry_number_unique` (`entity_id`,`entry_number`),
  ADD KEY `journal_entries_fiscal_year_id_foreign` (`fiscal_year_id`),
  ADD KEY `journal_entries_posted_by_foreign` (`posted_by`),
  ADD KEY `journal_entries_reversed_entry_id_foreign` (`reversed_entry_id`),
  ADD KEY `journal_entries_created_by_foreign` (`created_by`),
  ADD KEY `journal_entries_updated_by_foreign` (`updated_by`),
  ADD KEY `journal_entries_entity_id_entry_date_index` (`entity_id`,`entry_date`),
  ADD KEY `journal_entries_journal_id_status_index` (`journal_id`,`status`),
  ADD KEY `journal_entries_status_index` (`status`);

--
-- Indexes for table `journal_entry_lines`
--
ALTER TABLE `journal_entry_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_entry_lines_journal_entry_id_index` (`journal_entry_id`),
  ADD KEY `journal_entry_lines_account_id_index` (`account_id`),
  ADD KEY `journal_entry_lines_cost_center_id_index` (`cost_center_id`),
  ADD KEY `journal_entry_lines_partner_type_partner_id_index` (`partner_type`,`partner_id`);

--
-- Indexes for table `journal_translations`
--
ALTER TABLE `journal_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `journal_translations_journal_id_locale_unique` (`journal_id`,`locale`),
  ADD KEY `journal_translations_locale_index` (`locale`);

--
-- Indexes for table `licenses`
--
ALTER TABLE `licenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `licenses_user_id_foreign` (`user_id`);

--
-- Indexes for table `license_translations`
--
ALTER TABLE `license_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `license_translations_license_id_locale_unique` (`license_id`,`locale`),
  ADD KEY `license_translations_locale_index` (`locale`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `notification_batches`
--
ALTER TABLE `notification_batches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notification_batches_template_id_foreign` (`template_id`),
  ADD KEY `notification_batches_created_by_foreign` (`created_by`),
  ADD KEY `notification_batches_audience_type_index` (`audience_type`),
  ADD KEY `notification_batches_status_index` (`status`);

--
-- Indexes for table `notification_translations`
--
ALTER TABLE `notification_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `notification_translation_locale_unique` (`notification_id`,`locale`),
  ADD KEY `notification_translations_notification_id_index` (`notification_id`);

--
-- Indexes for table `official_holidays`
--
ALTER TABLE `official_holidays`
  ADD PRIMARY KEY (`id`),
  ADD KEY `official_holidays_branch_id_foreign` (`branch_id`),
  ADD KEY `official_holidays_created_by_foreign` (`created_by`),
  ADD KEY `official_holidays_start_date_index` (`start_date`),
  ADD KEY `official_holidays_end_date_index` (`end_date`),
  ADD KEY `official_holidays_year_index` (`year`),
  ADD KEY `official_holidays_is_recurring_index` (`is_recurring`),
  ADD KEY `official_holidays_status_index` (`status`);

--
-- Indexes for table `official_holiday_translations`
--
ALTER TABLE `official_holiday_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `oh_trans_oh_id_locale_unique` (`official_holiday_id`,`locale`),
  ADD KEY `official_holiday_translations_locale_index` (`locale`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `push_subscriptions`
--
ALTER TABLE `push_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `push_subscriptions_endpoint_hash_unique` (`endpoint_hash`),
  ADD KEY `push_subscriptions_user_id_index` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings_email_templates`
--
ALTER TABLE `settings_email_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_email_templates_code_unique` (`code`);

--
-- Indexes for table `settings_notification_templates`
--
ALTER TABLE `settings_notification_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_notification_templates_code_unique` (`code`),
  ADD KEY `settings_notification_templates_type_index` (`type`),
  ADD KEY `settings_notification_templates_module_index` (`module`),
  ADD KEY `settings_notification_templates_is_active_index` (`is_active`);

--
-- Indexes for table `settings_notification_template_translations`
--
ALTER TABLE `settings_notification_template_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_ntt_template_locale_unique` (`template_id`,`locale`);

--
-- Indexes for table `settings_print_templates`
--
ALTER TABLE `settings_print_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_print_templates_code_unique` (`code`);

--
-- Indexes for table `setting_translations`
--
ALTER TABLE `setting_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_translations_setting_id_locale_unique` (`setting_id`,`locale`),
  ADD KEY `setting_translations_locale_index` (`locale`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `shifts_code_unique` (`code`),
  ADD KEY `shifts_created_by_foreign` (`created_by`),
  ADD KEY `shifts_status_index` (`status`);

--
-- Indexes for table `shift_translations`
--
ALTER TABLE `shift_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `shift_translations_shift_id_locale_unique` (`shift_id`,`locale`),
  ADD KEY `shift_translations_locale_index` (`locale`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`),
  ADD KEY `units_building_id_foreign` (`building_id`),
  ADD KEY `units_user_id_foreign` (`user_id`);

--
-- Indexes for table `unit_images`
--
ALTER TABLE `unit_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `unit_images_unit_id_foreign` (`unit_id`);

--
-- Indexes for table `unit_translations`
--
ALTER TABLE `unit_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unit_translations_unit_id_locale_unique` (`unit_id`,`locale`),
  ADD KEY `unit_translations_locale_index` (`locale`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_phone_unique` (`phone`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicles_user_id_foreign` (`user_id`);

--
-- Indexes for table `vehicle_translations`
--
ALTER TABLE `vehicle_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicle_translations_vehicle_id_locale_unique` (`vehicle_id`,`locale`),
  ADD KEY `vehicle_translations_locale_index` (`locale`);

--
-- Indexes for table `work_schedules`
--
ALTER TABLE `work_schedules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `work_schedules_code_unique` (`code`),
  ADD KEY `work_schedules_branch_id_foreign` (`branch_id`),
  ADD KEY `work_schedules_department_id_foreign` (`department_id`),
  ADD KEY `work_schedules_created_by_foreign` (`created_by`),
  ADD KEY `work_schedules_type_index` (`type`),
  ADD KEY `work_schedules_is_default_index` (`is_default`),
  ADD KEY `work_schedules_status_index` (`status`);

--
-- Indexes for table `work_schedule_days`
--
ALTER TABLE `work_schedule_days`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ws_days_ws_id_day_unique` (`work_schedule_id`,`day_of_week`),
  ADD KEY `work_schedule_days_shift_id_foreign` (`shift_id`),
  ADD KEY `work_schedule_days_second_shift_id_foreign` (`second_shift_id`);

--
-- Indexes for table `work_schedule_translations`
--
ALTER TABLE `work_schedule_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ws_trans_ws_id_locale_unique` (`work_schedule_id`,`locale`),
  ADD KEY `work_schedule_translations_locale_index` (`locale`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounting_cost_centers`
--
ALTER TABLE `accounting_cost_centers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `accounting_cost_center_translations`
--
ALTER TABLE `accounting_cost_center_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `accounting_lock_dates`
--
ALTER TABLE `accounting_lock_dates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `accounting_lock_exceptions`
--
ALTER TABLE `accounting_lock_exceptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `accounting_payment_matchings`
--
ALTER TABLE `accounting_payment_matchings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `account_mappings`
--
ALTER TABLE `account_mappings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `account_translations`
--
ALTER TABLE `account_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `ai_providers`
--
ALTER TABLE `ai_providers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_tasks`
--
ALTER TABLE `ai_tasks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ai_task_providers`
--
ALTER TABLE `ai_task_providers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_usage_logs`
--
ALTER TABLE `ai_usage_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attachmentables`
--
ALTER TABLE `attachmentables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance_raw_logs`
--
ALTER TABLE `attendance_raw_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_log_settings`
--
ALTER TABLE `audit_log_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `banks`
--
ALTER TABLE `banks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_reconciliations`
--
ALTER TABLE `bank_reconciliations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_reconciliation_items`
--
ALTER TABLE `bank_reconciliation_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_translations`
--
ALTER TABLE `bank_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bills`
--
ALTER TABLE `bills`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `bill_payment_dues`
--
ALTER TABLE `bill_payment_dues`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `bill_short_links`
--
ALTER TABLE `bill_short_links`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `bill_transactions`
--
ALTER TABLE `bill_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bill_translations`
--
ALTER TABLE `bill_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `bill_warnings`
--
ALTER TABLE `bill_warnings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `biometric_devices`
--
ALTER TABLE `biometric_devices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branch_translations`
--
ALTER TABLE `branch_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `buildings`
--
ALTER TABLE `buildings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `building_images`
--
ALTER TABLE `building_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `building_translations`
--
ALTER TABLE `building_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `building_types`
--
ALTER TABLE `building_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `building_type_translations`
--
ALTER TABLE `building_type_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `cash_accounts`
--
ALTER TABLE `cash_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cash_account_translations`
--
ALTER TABLE `cash_account_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `company_licenses`
--
ALTER TABLE `company_licenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_license_translations`
--
ALTER TABLE `company_license_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_translations`
--
ALTER TABLE `company_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `company_types`
--
ALTER TABLE `company_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `company_type_translations`
--
ALTER TABLE `company_type_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `contract_translations`
--
ALTER TABLE `contract_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contract_types`
--
ALTER TABLE `contract_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `contract_type_translations`
--
ALTER TABLE `contract_type_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `contract_units`
--
ALTER TABLE `contract_units`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `department_translations`
--
ALTER TABLE `department_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_allowances`
--
ALTER TABLE `employee_allowances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_branches`
--
ALTER TABLE `employee_branches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_documents`
--
ALTER TABLE `employee_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_notification_preferences`
--
ALTER TABLE `employee_notification_preferences`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_translations`
--
ALTER TABLE `employee_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `entities`
--
ALTER TABLE `entities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `entity_translations`
--
ALTER TABLE `entity_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `financial_vouchers`
--
ALTER TABLE `financial_vouchers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `financial_voucher_translations`
--
ALTER TABLE `financial_voucher_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fiscal_years`
--
ALTER TABLE `fiscal_years`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fiscal_year_translations`
--
ALTER TABLE `fiscal_year_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `general_settings`
--
ALTER TABLE `general_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `general_setting_translations`
--
ALTER TABLE `general_setting_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=221;

--
-- AUTO_INCREMENT for table `hr_candidates`
--
ALTER TABLE `hr_candidates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_eos_rules`
--
ALTER TABLE `hr_eos_rules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_interviews`
--
ALTER TABLE `hr_interviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_interview_evaluations`
--
ALTER TABLE `hr_interview_evaluations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_interview_interviewers`
--
ALTER TABLE `hr_interview_interviewers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_job_applications`
--
ALTER TABLE `hr_job_applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_job_offers`
--
ALTER TABLE `hr_job_offers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_job_openings`
--
ALTER TABLE `hr_job_openings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_job_opening_translations`
--
ALTER TABLE `hr_job_opening_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_job_requisitions`
--
ALTER TABLE `hr_job_requisitions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_leave_requests`
--
ALTER TABLE `hr_leave_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_loans`
--
ALTER TABLE `hr_loans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_loan_installments`
--
ALTER TABLE `hr_loan_installments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_onboardings`
--
ALTER TABLE `hr_onboardings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_onboarding_tasks`
--
ALTER TABLE `hr_onboarding_tasks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_payrolls`
--
ALTER TABLE `hr_payrolls`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_payroll_items`
--
ALTER TABLE `hr_payroll_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_penalties`
--
ALTER TABLE `hr_penalties`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_permission_requests`
--
ALTER TABLE `hr_permission_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_rewards`
--
ALTER TABLE `hr_rewards`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_settings`
--
ALTER TABLE `hr_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_setting_translations`
--
ALTER TABLE `hr_setting_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_sick_leave_tiers`
--
ALTER TABLE `hr_sick_leave_tiers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_workforce_plans`
--
ALTER TABLE `hr_workforce_plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_workforce_plan_items`
--
ALTER TABLE `hr_workforce_plan_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_work_policies`
--
ALTER TABLE `hr_work_policies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `imageables`
--
ALTER TABLE `imageables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_titles`
--
ALTER TABLE `job_titles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_title_translations`
--
ALTER TABLE `job_title_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journals`
--
ALTER TABLE `journals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_entries`
--
ALTER TABLE `journal_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_entry_lines`
--
ALTER TABLE `journal_entry_lines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_translations`
--
ALTER TABLE `journal_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `licenses`
--
ALTER TABLE `licenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `license_translations`
--
ALTER TABLE `license_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `notification_batches`
--
ALTER TABLE `notification_batches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_translations`
--
ALTER TABLE `notification_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `official_holidays`
--
ALTER TABLE `official_holidays`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `official_holiday_translations`
--
ALTER TABLE `official_holiday_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=429;

--
-- AUTO_INCREMENT for table `push_subscriptions`
--
ALTER TABLE `push_subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `settings_email_templates`
--
ALTER TABLE `settings_email_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings_notification_templates`
--
ALTER TABLE `settings_notification_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `settings_notification_template_translations`
--
ALTER TABLE `settings_notification_template_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `settings_print_templates`
--
ALTER TABLE `settings_print_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `setting_translations`
--
ALTER TABLE `setting_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shift_translations`
--
ALTER TABLE `shift_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `unit_images`
--
ALTER TABLE `unit_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `unit_translations`
--
ALTER TABLE `unit_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vehicle_translations`
--
ALTER TABLE `vehicle_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `work_schedules`
--
ALTER TABLE `work_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `work_schedule_days`
--
ALTER TABLE `work_schedule_days`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `work_schedule_translations`
--
ALTER TABLE `work_schedule_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounting_cost_centers`
--
ALTER TABLE `accounting_cost_centers`
  ADD CONSTRAINT `accounting_cost_centers_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `accounting_cost_centers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `accounting_cost_centers_entity_id_foreign` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `accounting_cost_centers_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `accounting_cost_centers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `accounting_cost_center_translations`
--
ALTER TABLE `accounting_cost_center_translations`
  ADD CONSTRAINT `accounting_cost_center_translations_cost_center_id_foreign` FOREIGN KEY (`cost_center_id`) REFERENCES `accounting_cost_centers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `accounting_lock_dates`
--
ALTER TABLE `accounting_lock_dates`
  ADD CONSTRAINT `accounting_lock_dates_entity_id_foreign` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `accounting_lock_dates_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `accounting_lock_exceptions`
--
ALTER TABLE `accounting_lock_exceptions`
  ADD CONSTRAINT `accounting_lock_exceptions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `accounting_lock_exceptions_entity_id_foreign` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `accounting_lock_exceptions_revoked_by_foreign` FOREIGN KEY (`revoked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `accounting_lock_exceptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `accounting_payment_matchings`
--
ALTER TABLE `accounting_payment_matchings`
  ADD CONSTRAINT `accounting_payment_matchings_credit_line_id_foreign` FOREIGN KEY (`credit_line_id`) REFERENCES `journal_entry_lines` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `accounting_payment_matchings_debit_line_id_foreign` FOREIGN KEY (`debit_line_id`) REFERENCES `journal_entry_lines` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `accounting_payment_matchings_entity_id_foreign` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `accounting_payment_matchings_matched_by_foreign` FOREIGN KEY (`matched_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `accounts_entity_id_foreign` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `accounts_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `account_mappings`
--
ALTER TABLE `account_mappings`
  ADD CONSTRAINT `account_mappings_credit_account_id_foreign` FOREIGN KEY (`credit_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `account_mappings_debit_account_id_foreign` FOREIGN KEY (`debit_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `account_mappings_entity_id_foreign` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `account_mappings_fee_account_id_foreign` FOREIGN KEY (`fee_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `account_mappings_journal_id_foreign` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `account_translations`
--
ALTER TABLE `account_translations`
  ADD CONSTRAINT `account_translations_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ai_task_providers`
--
ALTER TABLE `ai_task_providers`
  ADD CONSTRAINT `ai_task_providers_provider_id_foreign` FOREIGN KEY (`provider_id`) REFERENCES `ai_providers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ai_task_providers_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `ai_tasks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ai_usage_logs`
--
ALTER TABLE `ai_usage_logs`
  ADD CONSTRAINT `ai_usage_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_adjusted_by_foreign` FOREIGN KEY (`adjusted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `attendances_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `attendances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendances_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `attendance_raw_logs`
--
ALTER TABLE `attendance_raw_logs`
  ADD CONSTRAINT `attendance_raw_logs_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `attendance_raw_logs_device_id_foreign` FOREIGN KEY (`device_id`) REFERENCES `biometric_devices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `attendance_raw_logs_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `banks`
--
ALTER TABLE `banks`
  ADD CONSTRAINT `banks_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `banks_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `banks` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `bank_reconciliations`
--
ALTER TABLE `bank_reconciliations`
  ADD CONSTRAINT `bank_reconciliations_cash_account_id_foreign` FOREIGN KEY (`cash_account_id`) REFERENCES `cash_accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bank_reconciliations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bank_reconciliations_entity_id_foreign` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bank_reconciliations_reconciled_by_foreign` FOREIGN KEY (`reconciled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bank_reconciliations_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `bank_reconciliation_items`
--
ALTER TABLE `bank_reconciliation_items`
  ADD CONSTRAINT `bank_reconciliation_items_bank_reconciliation_id_foreign` FOREIGN KEY (`bank_reconciliation_id`) REFERENCES `bank_reconciliations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bank_reconciliation_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bank_reconciliation_items_journal_entry_line_id_foreign` FOREIGN KEY (`journal_entry_line_id`) REFERENCES `journal_entry_lines` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `bank_translations`
--
ALTER TABLE `bank_translations`
  ADD CONSTRAINT `bank_translations_bank_id_foreign` FOREIGN KEY (`bank_id`) REFERENCES `banks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bills`
--
ALTER TABLE `bills`
  ADD CONSTRAINT `bills_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bills_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bills_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `bill_payment_dues`
--
ALTER TABLE `bill_payment_dues`
  ADD CONSTRAINT `bill_payment_dues_bill_id_foreign` FOREIGN KEY (`bill_id`) REFERENCES `bills` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bill_payment_dues_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bill_short_links`
--
ALTER TABLE `bill_short_links`
  ADD CONSTRAINT `bill_short_links_bill_id_foreign` FOREIGN KEY (`bill_id`) REFERENCES `bills` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bill_transactions`
--
ALTER TABLE `bill_transactions`
  ADD CONSTRAINT `bill_transactions_bill_id_foreign` FOREIGN KEY (`bill_id`) REFERENCES `bills` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bill_translations`
--
ALTER TABLE `bill_translations`
  ADD CONSTRAINT `bill_translations_bill_id_foreign` FOREIGN KEY (`bill_id`) REFERENCES `bills` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bill_warnings`
--
ALTER TABLE `bill_warnings`
  ADD CONSTRAINT `bill_warnings_bill_id_foreign` FOREIGN KEY (`bill_id`) REFERENCES `bills` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `biometric_devices`
--
ALTER TABLE `biometric_devices`
  ADD CONSTRAINT `biometric_devices_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `biometric_devices_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `branches`
--
ALTER TABLE `branches`
  ADD CONSTRAINT `branches_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `general_settings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `branches_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `branches_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `branches_government_id_foreign` FOREIGN KEY (`government_id`) REFERENCES `general_settings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `branches_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `branch_translations`
--
ALTER TABLE `branch_translations`
  ADD CONSTRAINT `branch_translations_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `buildings`
--
ALTER TABLE `buildings`
  ADD CONSTRAINT `buildings_building_type_id_foreign` FOREIGN KEY (`building_type_id`) REFERENCES `building_types` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `buildings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `building_images`
--
ALTER TABLE `building_images`
  ADD CONSTRAINT `building_images_building_id_foreign` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `building_translations`
--
ALTER TABLE `building_translations`
  ADD CONSTRAINT `building_translations_building_id_foreign` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `building_types`
--
ALTER TABLE `building_types`
  ADD CONSTRAINT `building_types_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `building_type_translations`
--
ALTER TABLE `building_type_translations`
  ADD CONSTRAINT `building_type_translations_building_type_id_foreign` FOREIGN KEY (`building_type_id`) REFERENCES `building_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cash_accounts`
--
ALTER TABLE `cash_accounts`
  ADD CONSTRAINT `cash_accounts_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cash_accounts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cash_accounts_entity_id_foreign` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cash_accounts_journal_id_foreign` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cash_accounts_responsible_user_id_foreign` FOREIGN KEY (`responsible_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `cash_account_translations`
--
ALTER TABLE `cash_account_translations`
  ADD CONSTRAINT `cash_account_translations_cash_account_id_foreign` FOREIGN KEY (`cash_account_id`) REFERENCES `cash_accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `companies_company_type_id_foreign` FOREIGN KEY (`company_type_id`) REFERENCES `company_types` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `companies_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `company_licenses`
--
ALTER TABLE `company_licenses`
  ADD CONSTRAINT `company_licenses_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `company_licenses_license_id_foreign` FOREIGN KEY (`license_id`) REFERENCES `licenses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `company_licenses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `company_license_translations`
--
ALTER TABLE `company_license_translations`
  ADD CONSTRAINT `company_license_translations_company_license_id_foreign` FOREIGN KEY (`company_license_id`) REFERENCES `company_licenses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `company_translations`
--
ALTER TABLE `company_translations`
  ADD CONSTRAINT `company_translations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `company_type_translations`
--
ALTER TABLE `company_type_translations`
  ADD CONSTRAINT `company_type_translations_company_type_id_foreign` FOREIGN KEY (`company_type_id`) REFERENCES `company_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contracts`
--
ALTER TABLE `contracts`
  ADD CONSTRAINT `contracts_building_id_foreign` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `contracts_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `contracts_contract_type_id_foreign` FOREIGN KEY (`contract_type_id`) REFERENCES `contract_types` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `contracts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `contract_translations`
--
ALTER TABLE `contract_translations`
  ADD CONSTRAINT `contract_translations_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contract_types`
--
ALTER TABLE `contract_types`
  ADD CONSTRAINT `contract_types_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `contract_type_translations`
--
ALTER TABLE `contract_type_translations`
  ADD CONSTRAINT `contract_type_translations_contract_type_id_foreign` FOREIGN KEY (`contract_type_id`) REFERENCES `contract_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contract_units`
--
ALTER TABLE `contract_units`
  ADD CONSTRAINT `contract_units_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contract_units_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `departments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `departments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `departments_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `departments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `department_translations`
--
ALTER TABLE `department_translations`
  ADD CONSTRAINT `department_translations_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_bank_branch_id_foreign` FOREIGN KEY (`bank_branch_id`) REFERENCES `banks` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_bank_id_foreign` FOREIGN KEY (`bank_id`) REFERENCES `banks` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_governorate_id_foreign` FOREIGN KEY (`governorate_id`) REFERENCES `general_settings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_job_title_id_foreign` FOREIGN KEY (`job_title_id`) REFERENCES `job_titles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_primary_branch_id_foreign` FOREIGN KEY (`primary_branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_visa_type_id_foreign` FOREIGN KEY (`visa_type_id`) REFERENCES `hr_settings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_work_schedule_id_foreign` FOREIGN KEY (`work_schedule_id`) REFERENCES `work_schedules` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employee_allowances`
--
ALTER TABLE `employee_allowances`
  ADD CONSTRAINT `employee_allowances_allowance_type_id_foreign` FOREIGN KEY (`allowance_type_id`) REFERENCES `hr_settings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_allowances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_branches`
--
ALTER TABLE `employee_branches`
  ADD CONSTRAINT `employee_branches_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_branches_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_documents`
--
ALTER TABLE `employee_documents`
  ADD CONSTRAINT `employee_documents_document_type_id_foreign` FOREIGN KEY (`document_type_id`) REFERENCES `hr_settings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_documents_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_translations`
--
ALTER TABLE `employee_translations`
  ADD CONSTRAINT `employee_translations_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `entities`
--
ALTER TABLE `entities`
  ADD CONSTRAINT `entities_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `entities_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `entity_translations`
--
ALTER TABLE `entity_translations`
  ADD CONSTRAINT `entity_translations_entity_id_foreign` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `financial_vouchers`
--
ALTER TABLE `financial_vouchers`
  ADD CONSTRAINT `financial_vouchers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `financial_vouchers_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `financial_vouchers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `financial_voucher_translations`
--
ALTER TABLE `financial_voucher_translations`
  ADD CONSTRAINT `financial_voucher_translations_financial_voucher_id_foreign` FOREIGN KEY (`financial_voucher_id`) REFERENCES `financial_vouchers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fiscal_years`
--
ALTER TABLE `fiscal_years`
  ADD CONSTRAINT `fiscal_years_closed_by_foreign` FOREIGN KEY (`closed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fiscal_years_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fiscal_years_entity_id_foreign` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fiscal_year_translations`
--
ALTER TABLE `fiscal_year_translations`
  ADD CONSTRAINT `fiscal_year_translations_fiscal_year_id_foreign` FOREIGN KEY (`fiscal_year_id`) REFERENCES `fiscal_years` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `general_settings`
--
ALTER TABLE `general_settings`
  ADD CONSTRAINT `general_settings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `general_settings_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `general_settings` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `general_setting_translations`
--
ALTER TABLE `general_setting_translations`
  ADD CONSTRAINT `general_setting_translations_general_setting_id_foreign` FOREIGN KEY (`general_setting_id`) REFERENCES `general_settings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_candidates`
--
ALTER TABLE `hr_candidates`
  ADD CONSTRAINT `hr_candidates_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hr_interviews`
--
ALTER TABLE `hr_interviews`
  ADD CONSTRAINT `hr_interviews_candidate_id_foreign` FOREIGN KEY (`candidate_id`) REFERENCES `hr_candidates` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_interviews_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_interviews_job_application_id_foreign` FOREIGN KEY (`job_application_id`) REFERENCES `hr_job_applications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_interviews_job_opening_id_foreign` FOREIGN KEY (`job_opening_id`) REFERENCES `hr_job_openings` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hr_interview_evaluations`
--
ALTER TABLE `hr_interview_evaluations`
  ADD CONSTRAINT `hr_interview_evaluations_interview_id_foreign` FOREIGN KEY (`interview_id`) REFERENCES `hr_interviews` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_interview_evaluations_interviewer_id_foreign` FOREIGN KEY (`interviewer_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_interview_interviewers`
--
ALTER TABLE `hr_interview_interviewers`
  ADD CONSTRAINT `hr_interview_interviewers_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_interview_interviewers_interview_id_foreign` FOREIGN KEY (`interview_id`) REFERENCES `hr_interviews` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_job_applications`
--
ALTER TABLE `hr_job_applications`
  ADD CONSTRAINT `hr_job_applications_candidate_id_foreign` FOREIGN KEY (`candidate_id`) REFERENCES `hr_candidates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_job_applications_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_job_applications_job_opening_id_foreign` FOREIGN KEY (`job_opening_id`) REFERENCES `hr_job_openings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_job_offers`
--
ALTER TABLE `hr_job_offers`
  ADD CONSTRAINT `hr_job_offers_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_job_offers_candidate_id_foreign` FOREIGN KEY (`candidate_id`) REFERENCES `hr_candidates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_job_offers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_job_offers_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_job_offers_job_application_id_foreign` FOREIGN KEY (`job_application_id`) REFERENCES `hr_job_applications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_job_offers_job_opening_id_foreign` FOREIGN KEY (`job_opening_id`) REFERENCES `hr_job_openings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_job_offers_job_title_id_foreign` FOREIGN KEY (`job_title_id`) REFERENCES `job_titles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hr_job_openings`
--
ALTER TABLE `hr_job_openings`
  ADD CONSTRAINT `hr_job_openings_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_job_openings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_job_openings_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_job_openings_job_requisition_id_foreign` FOREIGN KEY (`job_requisition_id`) REFERENCES `hr_job_requisitions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_job_openings_job_title_id_foreign` FOREIGN KEY (`job_title_id`) REFERENCES `job_titles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_job_openings_published_by_foreign` FOREIGN KEY (`published_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hr_job_opening_translations`
--
ALTER TABLE `hr_job_opening_translations`
  ADD CONSTRAINT `hr_job_opening_translations_job_opening_id_foreign` FOREIGN KEY (`job_opening_id`) REFERENCES `hr_job_openings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_job_requisitions`
--
ALTER TABLE `hr_job_requisitions`
  ADD CONSTRAINT `hr_job_requisitions_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_job_requisitions_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_job_requisitions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_job_requisitions_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_job_requisitions_job_title_id_foreign` FOREIGN KEY (`job_title_id`) REFERENCES `job_titles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_job_requisitions_replacement_for_employee_id_foreign` FOREIGN KEY (`replacement_for_employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_job_requisitions_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_job_requisitions_workforce_plan_item_id_foreign` FOREIGN KEY (`workforce_plan_item_id`) REFERENCES `hr_workforce_plan_items` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hr_leave_requests`
--
ALTER TABLE `hr_leave_requests`
  ADD CONSTRAINT `hr_leave_requests_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_leave_requests_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_leave_requests_hr_approved_by_foreign` FOREIGN KEY (`hr_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_leave_requests_manager_approved_by_foreign` FOREIGN KEY (`manager_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_leave_requests_replacement_employee_id_foreign` FOREIGN KEY (`replacement_employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_leave_requests_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hr_loans`
--
ALTER TABLE `hr_loans`
  ADD CONSTRAINT `hr_loans_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_loans_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_loans_hr_approved_by_foreign` FOREIGN KEY (`hr_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_loans_manager_approved_by_foreign` FOREIGN KEY (`manager_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hr_loan_installments`
--
ALTER TABLE `hr_loan_installments`
  ADD CONSTRAINT `hr_loan_installments_loan_id_foreign` FOREIGN KEY (`loan_id`) REFERENCES `hr_loans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_onboardings`
--
ALTER TABLE `hr_onboardings`
  ADD CONSTRAINT `hr_onboardings_buddy_employee_id_foreign` FOREIGN KEY (`buddy_employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_onboardings_candidate_id_foreign` FOREIGN KEY (`candidate_id`) REFERENCES `hr_candidates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_onboardings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_onboardings_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_onboardings_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_onboardings_job_application_id_foreign` FOREIGN KEY (`job_application_id`) REFERENCES `hr_job_applications` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_onboardings_job_offer_id_foreign` FOREIGN KEY (`job_offer_id`) REFERENCES `hr_job_offers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_onboardings_job_title_id_foreign` FOREIGN KEY (`job_title_id`) REFERENCES `job_titles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hr_onboarding_tasks`
--
ALTER TABLE `hr_onboarding_tasks`
  ADD CONSTRAINT `hr_onboarding_tasks_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_onboarding_tasks_onboarding_id_foreign` FOREIGN KEY (`onboarding_id`) REFERENCES `hr_onboardings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_payrolls`
--
ALTER TABLE `hr_payrolls`
  ADD CONSTRAINT `hr_payrolls_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_payrolls_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_payrolls_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_payrolls_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_payrolls_paid_by_foreign` FOREIGN KEY (`paid_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hr_payroll_items`
--
ALTER TABLE `hr_payroll_items`
  ADD CONSTRAINT `hr_payroll_items_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_payroll_items_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `hr_payrolls` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_penalties`
--
ALTER TABLE `hr_penalties`
  ADD CONSTRAINT `hr_penalties_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_penalties_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_penalties_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_penalties_deducted_by_foreign` FOREIGN KEY (`deducted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_penalties_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_penalties_penalty_type_id_foreign` FOREIGN KEY (`penalty_type_id`) REFERENCES `hr_settings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_permission_requests`
--
ALTER TABLE `hr_permission_requests`
  ADD CONSTRAINT `hr_permission_requests_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_permission_requests_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_permission_requests_hr_approved_by_foreign` FOREIGN KEY (`hr_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_permission_requests_manager_approved_by_foreign` FOREIGN KEY (`manager_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hr_rewards`
--
ALTER TABLE `hr_rewards`
  ADD CONSTRAINT `hr_rewards_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_rewards_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_rewards_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_rewards_paid_by_foreign` FOREIGN KEY (`paid_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_rewards_rejected_by_foreign` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_rewards_reward_type_id_foreign` FOREIGN KEY (`reward_type_id`) REFERENCES `hr_settings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_settings`
--
ALTER TABLE `hr_settings`
  ADD CONSTRAINT `hr_settings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_settings_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `hr_settings` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hr_setting_translations`
--
ALTER TABLE `hr_setting_translations`
  ADD CONSTRAINT `hr_setting_translations_hr_setting_id_foreign` FOREIGN KEY (`hr_setting_id`) REFERENCES `hr_settings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_workforce_plans`
--
ALTER TABLE `hr_workforce_plans`
  ADD CONSTRAINT `hr_workforce_plans_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_workforce_plans_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_workforce_plan_items`
--
ALTER TABLE `hr_workforce_plan_items`
  ADD CONSTRAINT `hr_workforce_plan_items_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_workforce_plan_items_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_workforce_plan_items_job_title_id_foreign` FOREIGN KEY (`job_title_id`) REFERENCES `job_titles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_workforce_plan_items_workforce_plan_id_foreign` FOREIGN KEY (`workforce_plan_id`) REFERENCES `hr_workforce_plans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_titles`
--
ALTER TABLE `job_titles`
  ADD CONSTRAINT `job_titles_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_titles_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `job_title_translations`
--
ALTER TABLE `job_title_translations`
  ADD CONSTRAINT `job_title_translations_job_title_id_foreign` FOREIGN KEY (`job_title_id`) REFERENCES `job_titles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `journals`
--
ALTER TABLE `journals`
  ADD CONSTRAINT `journals_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `journals_default_account_id_foreign` FOREIGN KEY (`default_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `journals_entity_id_foreign` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `journals_loss_account_id_foreign` FOREIGN KEY (`loss_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `journals_profit_account_id_foreign` FOREIGN KEY (`profit_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `journals_suspense_account_id_foreign` FOREIGN KEY (`suspense_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD CONSTRAINT `journal_entries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `journal_entries_entity_id_foreign` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `journal_entries_fiscal_year_id_foreign` FOREIGN KEY (`fiscal_year_id`) REFERENCES `fiscal_years` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `journal_entries_journal_id_foreign` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `journal_entries_posted_by_foreign` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `journal_entries_reversed_entry_id_foreign` FOREIGN KEY (`reversed_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `journal_entries_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `journal_entry_lines`
--
ALTER TABLE `journal_entry_lines`
  ADD CONSTRAINT `journal_entry_lines_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `journal_entry_lines_cost_center_id_foreign` FOREIGN KEY (`cost_center_id`) REFERENCES `accounting_cost_centers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `journal_entry_lines_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `journal_translations`
--
ALTER TABLE `journal_translations`
  ADD CONSTRAINT `journal_translations_journal_id_foreign` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `licenses`
--
ALTER TABLE `licenses`
  ADD CONSTRAINT `licenses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `license_translations`
--
ALTER TABLE `license_translations`
  ADD CONSTRAINT `license_translations_license_id_foreign` FOREIGN KEY (`license_id`) REFERENCES `licenses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notification_batches`
--
ALTER TABLE `notification_batches`
  ADD CONSTRAINT `notification_batches_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `notification_batches_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `settings_notification_templates` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `official_holidays`
--
ALTER TABLE `official_holidays`
  ADD CONSTRAINT `official_holidays_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `official_holidays_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `official_holiday_translations`
--
ALTER TABLE `official_holiday_translations`
  ADD CONSTRAINT `official_holiday_translations_official_holiday_id_foreign` FOREIGN KEY (`official_holiday_id`) REFERENCES `official_holidays` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `push_subscriptions`
--
ALTER TABLE `push_subscriptions`
  ADD CONSTRAINT `push_subscriptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `settings_notification_template_translations`
--
ALTER TABLE `settings_notification_template_translations`
  ADD CONSTRAINT `settings_notification_template_translations_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `settings_notification_templates` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `setting_translations`
--
ALTER TABLE `setting_translations`
  ADD CONSTRAINT `setting_translations_setting_id_foreign` FOREIGN KEY (`setting_id`) REFERENCES `settings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shifts`
--
ALTER TABLE `shifts`
  ADD CONSTRAINT `shifts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `shift_translations`
--
ALTER TABLE `shift_translations`
  ADD CONSTRAINT `shift_translations_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `units`
--
ALTER TABLE `units`
  ADD CONSTRAINT `units_building_id_foreign` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `units_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `unit_images`
--
ALTER TABLE `unit_images`
  ADD CONSTRAINT `unit_images_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `unit_translations`
--
ALTER TABLE `unit_translations`
  ADD CONSTRAINT `unit_translations_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `vehicle_translations`
--
ALTER TABLE `vehicle_translations`
  ADD CONSTRAINT `vehicle_translations_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `work_schedules`
--
ALTER TABLE `work_schedules`
  ADD CONSTRAINT `work_schedules_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `work_schedules_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `work_schedules_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `work_schedule_days`
--
ALTER TABLE `work_schedule_days`
  ADD CONSTRAINT `work_schedule_days_second_shift_id_foreign` FOREIGN KEY (`second_shift_id`) REFERENCES `shifts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `work_schedule_days_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `work_schedule_days_work_schedule_id_foreign` FOREIGN KEY (`work_schedule_id`) REFERENCES `work_schedules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `work_schedule_translations`
--
ALTER TABLE `work_schedule_translations`
  ADD CONSTRAINT `work_schedule_translations_work_schedule_id_foreign` FOREIGN KEY (`work_schedule_id`) REFERENCES `work_schedules` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
