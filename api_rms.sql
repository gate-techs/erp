-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 08, 2026 at 09:26 AM
-- Server version: 10.11.16-MariaDB
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `api_rms`
--

-- --------------------------------------------------------

--
-- Table structure for table `abouts`
--

CREATE TABLE `abouts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sort` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `abouts`
--

INSERT INTO `abouts` (`id`, `sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 0, 1, '2026-02-24 11:32:30', '2026-02-24 11:32:30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `about_translations`
--

CREATE TABLE `about_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `about_id` bigint(20) UNSIGNED DEFAULT NULL,
  `locale` varchar(191) NOT NULL,
  `title` varchar(191) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `about_translations`
--

INSERT INTO `about_translations` (`id`, `about_id`, `locale`, `title`, `body`, `created_at`, `updated_at`) VALUES
(1, 1, 'en', 'Real estate management system', 'We aim to develop urban communities that meet the needs of individuals and investors by delivering high-quality residential, commercial, and administrative projects. We focus on strategic locations, expert designs, and flexible payment plans to ensure optimal investment value. Furthermore, we prioritize transparency in our dealings, aiming to foster urban development and contribute to the economy in alignment with key developmental visions.', NULL, NULL),
(2, 1, 'ar', 'نظام إدارة العقارات', 'نسعي لى تطوير مجتمعات عمرانية متكاملة تلبي احتياجات الأفراد والمستثمرين من خلال تقديم مشروعات سكنية وتجارية وإدارية بمعايير عالية الجودة. تركّز على اختيار مواقع استراتيجية، وتصميمات عصرية، وخطط سداد مرنة تضمن أفضل قيمة استثمارية. كما تعمل على تعزيز الثقة والشفافية في التعاملات، بهدف دعم التنمية العمرانية والمساهمة في تنشيط الاقتصاد وتحقيق رؤية تنموية مستدامة.', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `absences`
--

CREATE TABLE `absences` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `start_date` varchar(191) DEFAULT NULL,
  `end_date` varchar(191) DEFAULT NULL,
  `days` varchar(191) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `absences`
--

INSERT INTO `absences` (`id`, `user_id`, `employee_id`, `start_date`, `end_date`, `days`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, '2026-03-05', '2026-03-17', '-12', 1, '2026-03-01 08:12:32', '2026-03-01 08:12:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `absence_translations`
--

CREATE TABLE `absence_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `absence_id` bigint(20) UNSIGNED DEFAULT NULL,
  `locale` varchar(191) NOT NULL,
  `title` varchar(191) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `absence_translations`
--

INSERT INTO `absence_translations` (`id`, `absence_id`, `locale`, `title`, `body`, `created_at`, `updated_at`) VALUES
(1, 1, 'en', 'sdfiuwe', 'sdfiuwe', NULL, NULL),
(2, 1, 'ar', 'jksdf', 'jksdf', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `acc_accounting_entities`
--

CREATE TABLE `acc_accounting_entities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `legal_name` varchar(191) DEFAULT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'KWD',
  `source_type` varchar(50) DEFAULT NULL,
  `source_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `acc_accounting_entities`
--

INSERT INTO `acc_accounting_entities` (`id`, `code`, `name`, `legal_name`, `currency`, `source_type`, `source_id`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'ACC-COM-1', 'الكيان المحاسبي الرئيسي للشركة', 'الكيان المحاسبي الرئيسي للشركة', 'KWD', 'company', 1, 1, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `acc_accounting_settings`
--

CREATE TABLE `acc_accounting_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `accounting_entity_id` bigint(20) UNSIGNED NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'KWD',
  `decimal_places` tinyint(3) UNSIGNED NOT NULL DEFAULT 2,
  `date_format` varchar(20) NOT NULL DEFAULT 'Y-m-d',
  `week_start` enum('saturday','sunday','monday') NOT NULL DEFAULT 'sunday',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `default_fiscal_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `default_fiscal_period_id` bigint(20) UNSIGNED DEFAULT NULL,
  `journal_entry_workflow` enum('draft','posted') NOT NULL DEFAULT 'draft',
  `auto_post_receipt_vouchers` tinyint(1) NOT NULL DEFAULT 0,
  `auto_post_payment_vouchers` tinyint(1) NOT NULL DEFAULT 0,
  `lock_posted_entries` tinyint(1) NOT NULL DEFAULT 1,
  `lock_posted_vouchers` tinyint(1) NOT NULL DEFAULT 1,
  `allow_posting_to_closed_periods` tinyint(1) NOT NULL DEFAULT 0,
  `allow_posting_to_closed_years` tinyint(1) NOT NULL DEFAULT 0,
  `last_closing_date` date DEFAULT NULL,
  `last_closing_by` bigint(20) UNSIGNED DEFAULT NULL,
  `journal_entry_number_prefix` varchar(20) NOT NULL DEFAULT 'JE',
  `receipt_voucher_number_prefix` varchar(20) NOT NULL DEFAULT 'RV',
  `payment_voucher_number_prefix` varchar(20) NOT NULL DEFAULT 'PV',
  `number_sequence_padding` tinyint(3) UNSIGNED NOT NULL DEFAULT 5,
  `reset_number_sequence_yearly` tinyint(1) NOT NULL DEFAULT 1,
  `default_cash_account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `default_bank_account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `default_receivables_account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `default_payables_account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `default_retained_earnings_account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `acc_accounting_settings`
--

INSERT INTO `acc_accounting_settings` (`id`, `accounting_entity_id`, `currency`, `decimal_places`, `date_format`, `week_start`, `is_active`, `default_fiscal_year_id`, `default_fiscal_period_id`, `journal_entry_workflow`, `auto_post_receipt_vouchers`, `auto_post_payment_vouchers`, `lock_posted_entries`, `lock_posted_vouchers`, `allow_posting_to_closed_periods`, `allow_posting_to_closed_years`, `last_closing_date`, `last_closing_by`, `journal_entry_number_prefix`, `receipt_voucher_number_prefix`, `payment_voucher_number_prefix`, `number_sequence_padding`, `reset_number_sequence_yearly`, `default_cash_account_id`, `default_bank_account_id`, `default_receivables_account_id`, `default_payables_account_id`, `default_retained_earnings_account_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'KWD', 2, 'Y-m-d', 'sunday', 1, 1, NULL, 'draft', 0, 0, 1, 1, 0, 0, NULL, NULL, 'JE', 'RV', 'PV', 5, 1, 4, 5, 7, 15, 20, '2026-08-03 17:23:46', '2026-08-03 19:41:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `acc_accounts`
--

CREATE TABLE `acc_accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `accounting_entity_id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `type` enum('asset','liability','equity','revenue','expense') NOT NULL,
  `nature` enum('debit','credit') NOT NULL,
  `level` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `allow_posting` tinyint(1) NOT NULL DEFAULT 1,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `acc_accounts`
--

INSERT INTO `acc_accounts` (`id`, `accounting_entity_id`, `parent_id`, `code`, `name`, `type`, `nature`, `level`, `allow_posting`, `is_system`, `is_active`, `sort`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, NULL, '1', 'الاصول', 'asset', 'debit', 1, 0, 1, 1, 1, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL),
(2, 1, 1, '11', 'الاصول المتداولة', 'asset', 'debit', 2, 0, 1, 1, 2, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL),
(3, 1, 2, '111', 'النقدية وما في حكمها', 'asset', 'debit', 3, 0, 1, 1, 3, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL),
(4, 1, 3, '1111', 'الصندوق الرئيسي', 'asset', 'debit', 4, 1, 1, 1, 4, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL),
(5, 1, 3, '1112', 'الحسابات البنكية', 'asset', 'debit', 4, 1, 1, 1, 5, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL),
(6, 1, 2, '112', 'الذمم المدينة', 'asset', 'debit', 3, 0, 1, 1, 6, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL),
(7, 1, 6, '1121', 'ذمم اولياء الامور', 'asset', 'debit', 4, 1, 1, 1, 7, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL),
(8, 1, 2, '113', 'المصروفات المدفوعة مقدما', 'asset', 'debit', 3, 1, 1, 1, 8, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL),
(9, 1, 1, '12', 'الاصول غير المتداولة', 'asset', 'debit', 2, 0, 1, 1, 9, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL),
(10, 1, 9, '121', 'الاصول الثابتة', 'asset', 'debit', 3, 0, 1, 1, 10, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL),
(11, 1, 10, '1211', 'الاثاث والتجهيزات', 'asset', 'debit', 4, 1, 1, 1, 11, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL),
(12, 1, NULL, '2', 'الخصوم', 'liability', 'credit', 1, 0, 1, 1, 12, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL),
(13, 1, 12, '21', 'الخصوم المتداولة', 'liability', 'credit', 2, 0, 1, 1, 13, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL),
(14, 1, 13, '211', 'الذمم الدائنة', 'liability', 'credit', 3, 0, 1, 1, 14, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL),
(15, 1, 14, '2111', 'الموردون', 'liability', 'credit', 4, 1, 1, 1, 15, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL),
(16, 1, 13, '212', 'المصروفات المستحقة', 'liability', 'credit', 3, 1, 1, 1, 16, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL),
(17, 1, 13, '213', 'الايرادات المقدمة', 'liability', 'credit', 3, 1, 1, 1, 17, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL),
(18, 1, NULL, '3', 'حقوق الملكية', 'equity', 'credit', 1, 0, 1, 1, 18, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL),
(19, 1, 18, '31', 'راس المال', 'equity', 'credit', 2, 1, 1, 1, 19, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL),
(20, 1, 18, '32', 'الارباح المبقاة', 'equity', 'credit', 2, 1, 1, 1, 20, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL),
(21, 1, NULL, '4', 'الايرادات', 'revenue', 'credit', 1, 0, 1, 1, 21, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL),
(22, 1, 21, '41', 'ايرادات الرسوم الدراسية', 'revenue', 'credit', 2, 1, 1, 1, 22, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL),
(23, 1, 21, '42', 'ايرادات التسجيل', 'revenue', 'credit', 2, 1, 1, 1, 23, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL),
(24, 1, 21, '43', 'ايرادات اخرى', 'revenue', 'credit', 2, 1, 1, 1, 24, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL),
(25, 1, NULL, '5', 'المصروفات', 'expense', 'debit', 1, 0, 1, 1, 25, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL),
(26, 1, 25, '51', 'مصروف الرواتب والاجور', 'expense', 'debit', 2, 1, 1, 1, 26, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL),
(27, 1, 25, '52', 'مصروف الايجار', 'expense', 'debit', 2, 1, 1, 1, 27, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL),
(28, 1, 25, '53', 'مصروف المرافق والخدمات', 'expense', 'debit', 2, 1, 1, 1, 28, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL),
(29, 1, 25, '54', 'مصروف المستلزمات التعليمية', 'expense', 'debit', 2, 1, 1, 1, 29, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `acc_financial_centers`
--

CREATE TABLE `acc_financial_centers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `accounting_entity_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `type` enum('branch','cost_center','profit_center','general') NOT NULL DEFAULT 'general',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `acc_financial_centers`
--

INSERT INTO `acc_financial_centers` (`id`, `accounting_entity_id`, `branch_id`, `parent_id`, `code`, `name`, `type`, `is_active`, `sort`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, NULL, NULL, 'GEN-001', 'المركز العام', 'general', 1, 1, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `acc_fiscal_periods`
--

CREATE TABLE `acc_fiscal_periods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fiscal_year_id` bigint(20) UNSIGNED NOT NULL,
  `period_no` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('open','closed') NOT NULL DEFAULT 'open',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `acc_fiscal_years`
--

CREATE TABLE `acc_fiscal_years` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `accounting_entity_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('open','closed') NOT NULL DEFAULT 'open',
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `acc_fiscal_years`
--

INSERT INTO `acc_fiscal_years` (`id`, `accounting_entity_id`, `code`, `name`, `start_date`, `end_date`, `status`, `is_default`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'FY-2026', 'السنة المالية 2026', '2026-01-01', '2026-12-31', 'open', 1, '2026-08-03 17:23:46', '2026-08-03 17:23:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `acc_journal_entries`
--

CREATE TABLE `acc_journal_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `accounting_entity_id` bigint(20) UNSIGNED NOT NULL,
  `fiscal_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `fiscal_period_id` bigint(20) UNSIGNED DEFAULT NULL,
  `financial_center_id` bigint(20) UNSIGNED DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `entry_no` varchar(191) NOT NULL,
  `entry_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `source_type` varchar(191) DEFAULT NULL,
  `source_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('draft','posted','cancelled') NOT NULL DEFAULT 'draft',
  `total_debit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_credit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `posted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `acc_journal_entry_lines`
--

CREATE TABLE `acc_journal_entry_lines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `journal_entry_id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `financial_center_id` bigint(20) UNSIGNED DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `line_no` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `debit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `credit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `party_type` varchar(191) DEFAULT NULL,
  `party_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `acc_payment_vouchers`
--

CREATE TABLE `acc_payment_vouchers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `accounting_entity_id` bigint(20) UNSIGNED NOT NULL,
  `fiscal_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `fiscal_period_id` bigint(20) UNSIGNED DEFAULT NULL,
  `financial_center_id` bigint(20) UNSIGNED DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `journal_entry_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payment_account_id` bigint(20) UNSIGNED NOT NULL,
  `offset_account_id` bigint(20) UNSIGNED NOT NULL,
  `voucher_no` varchar(191) NOT NULL,
  `voucher_date` date NOT NULL,
  `payment_method` enum('cash','bank') NOT NULL DEFAULT 'cash',
  `amount` decimal(15,2) NOT NULL,
  `reference_no` varchar(191) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `party_type` varchar(191) DEFAULT NULL,
  `party_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('draft','posted','cancelled') NOT NULL DEFAULT 'draft',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `posted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `acc_receipt_vouchers`
--

CREATE TABLE `acc_receipt_vouchers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `accounting_entity_id` bigint(20) UNSIGNED NOT NULL,
  `fiscal_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `fiscal_period_id` bigint(20) UNSIGNED DEFAULT NULL,
  `financial_center_id` bigint(20) UNSIGNED DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `journal_entry_id` bigint(20) UNSIGNED DEFAULT NULL,
  `receipt_account_id` bigint(20) UNSIGNED NOT NULL,
  `offset_account_id` bigint(20) UNSIGNED NOT NULL,
  `voucher_no` varchar(191) NOT NULL,
  `voucher_date` date NOT NULL,
  `payment_method` enum('cash','bank') NOT NULL DEFAULT 'cash',
  `amount` decimal(15,2) NOT NULL,
  `reference_no` varchar(191) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `party_type` varchar(191) DEFAULT NULL,
  `party_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('draft','posted','cancelled') NOT NULL DEFAULT 'draft',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `posted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(191) NOT NULL,
  `icon` varchar(191) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `application_translations`
--

CREATE TABLE `application_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `application_id` bigint(20) UNSIGNED DEFAULT NULL,
  `locale` varchar(191) NOT NULL,
  `name` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attachmentables`
--

CREATE TABLE `attachmentables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `url` varchar(191) NOT NULL,
  `attachmentable_id` bigint(20) UNSIGNED NOT NULL,
  `attachmentable_type` varchar(191) NOT NULL,
  `type` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auto_equipment`
--

CREATE TABLE `auto_equipment` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `plate_no` varchar(191) DEFAULT NULL,
  `owner_name` varchar(191) DEFAULT NULL,
  `receiver_name` varchar(191) DEFAULT NULL,
  `notebook_expire_date` varchar(191) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `auto_equipment`
--

INSERT INTO `auto_equipment` (`id`, `user_id`, `plate_no`, `owner_name`, `receiver_name`, `notebook_expire_date`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, '5356846846', 'Sint illum labore', 'Animi ut ut qui est', '2026-03-07', 1, '2026-02-26 12:07:20', '2026-02-26 12:07:20', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `auto_equipment_translations`
--

CREATE TABLE `auto_equipment_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `auto_equipment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `locale` varchar(191) NOT NULL,
  `title` varchar(191) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `type` varchar(191) DEFAULT NULL,
  `model` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `auto_equipment_translations`
--

INSERT INTO `auto_equipment_translations` (`id`, `auto_equipment_id`, `locale`, `title`, `body`, `type`, `model`, `created_at`, `updated_at`) VALUES
(1, 1, 'en', 'Voluptate minima dol', 'Quam reprehenderit', 'Quas nostrud tempora', 'Laboriosam dicta fa', NULL, NULL),
(2, 1, 'ar', 'Eum expedita anim po', 'Aperiam quo in assum', 'Vel irure lorem ex v', 'Et modi mollitia sin', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

CREATE TABLE `bills` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `contract_id` bigint(20) UNSIGNED DEFAULT NULL,
  `pay_after` varchar(191) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `bill_date` varchar(191) DEFAULT NULL,
  `bill_sent_by` enum('WHATSAPP','EMAIL','BOTH') NOT NULL DEFAULT 'WHATSAPP',
  `returned_voucher` varchar(191) DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `payment_status` enum('PENDING','PAID','FAILED','RETURNED') NOT NULL DEFAULT 'PENDING',
  `customer_name` varchar(191) DEFAULT NULL,
  `customer_country_code` varchar(191) DEFAULT NULL,
  `customer_mobile` varchar(191) DEFAULT NULL,
  `customer_email` varchar(191) DEFAULT NULL,
  `archived` tinyint(4) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bills`
--

INSERT INTO `bills` (`id`, `user_id`, `contract_id`, `pay_after`, `amount`, `bill_date`, `bill_sent_by`, `returned_voucher`, `payment_date`, `payment_status`, `customer_name`, `customer_country_code`, `customer_mobile`, `customer_email`, `archived`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, 3, NULL, 654.00, '2026-02-25', 'WHATSAPP', NULL, NULL, 'PENDING', NULL, NULL, NULL, NULL, 1, '2026-02-25 12:58:02', '2026-02-25 12:58:02', NULL),
(2, NULL, NULL, NULL, 565.00, '2026-02-26', 'WHATSAPP', NULL, NULL, 'PENDING', NULL, NULL, NULL, NULL, 1, '2026-02-26 09:13:50', '2026-02-26 09:48:11', '2026-02-26 09:48:11'),
(3, NULL, NULL, NULL, 565.00, '2026-02-26', 'WHATSAPP', NULL, NULL, 'PENDING', NULL, NULL, NULL, NULL, 1, '2026-02-26 09:14:07', '2026-02-26 09:48:00', '2026-02-26 09:48:00'),
(4, NULL, NULL, 'NOT_EXPIRE', 565.00, '2026-02-26', 'WHATSAPP', NULL, NULL, 'PENDING', 'shdfks', '+20', '1090460119', 'akdja@jjoi.sds', 0, '2026-02-26 09:16:33', '2026-02-26 09:47:55', '2026-02-26 09:47:55'),
(5, NULL, NULL, NULL, 4498.00, '2026-02-26', 'WHATSAPP', NULL, NULL, 'PENDING', NULL, NULL, NULL, NULL, 1, '2026-02-26 09:49:48', '2026-02-26 09:49:48', NULL),
(6, NULL, NULL, 'NOT_EXPIRE', 4498.00, '2026-02-26', 'WHATSAPP', NULL, NULL, 'PENDING', 'shdfks', '+20', '1090460119', 'akdja@jjoi.sds', 0, '2026-02-26 10:01:25', '2026-02-26 11:09:08', NULL),
(7, NULL, NULL, '10', 100.00, '2026-03-04', 'WHATSAPP', NULL, NULL, 'PAID', 'Eum sed optio asper', '+20', NULL, 'tacebap@mailinator.com', 0, '2026-03-04 21:08:25', '2026-03-04 21:08:25', NULL),
(8, NULL, 5, NULL, 6450.00, '2026-03-05', 'WHATSAPP', NULL, NULL, 'PAID', NULL, NULL, NULL, NULL, 1, '2026-03-05 08:24:54', '2026-03-05 08:24:54', NULL),
(9, NULL, NULL, 'NOT_EXPIRE', 4498.00, '2026-03-05', 'WHATSAPP', NULL, NULL, 'FAILED', 'shdfks', '+20', '1090460119', 'akdja@jjoi.sds', 0, '2026-03-05 08:49:30', '2026-03-05 08:49:30', NULL),
(10, NULL, 13, 'NOT_EXPIRE', 1000.00, '2026-03-05', 'WHATSAPP', NULL, NULL, 'RETURNED', NULL, NULL, NULL, NULL, 0, '2026-03-05 12:34:49', '2026-03-05 12:34:49', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bill_payment_dues`
--

CREATE TABLE `bill_payment_dues` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bill_id` bigint(20) UNSIGNED DEFAULT NULL,
  `send_bill` tinyint(4) NOT NULL DEFAULT 1,
  `payment_type` enum('STATIC_DAY','TIME_PERIOD') NOT NULL DEFAULT 'STATIC_DAY',
  `last_payment_date` datetime DEFAULT NULL,
  `next_payment_date` datetime DEFAULT NULL,
  `tolerance_for_delay` int(11) NOT NULL DEFAULT 5,
  `scheduling_type` enum('ONE_TIME_BILL','DAILY','WEEKLY','MONTHLY','YEARLY') DEFAULT NULL,
  `scheduling_day` varchar(191) DEFAULT NULL,
  `scheduling_month` varchar(191) DEFAULT NULL,
  `scheduling_finish_type` enum('ON','AFTER','NEVER') DEFAULT NULL,
  `scheduling_finish_on_date` varchar(191) DEFAULT NULL,
  `scheduling_finish_after_times` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bill_payment_dues`
--

INSERT INTO `bill_payment_dues` (`id`, `bill_id`, `send_bill`, `payment_type`, `last_payment_date`, `next_payment_date`, `tolerance_for_delay`, `scheduling_type`, `scheduling_day`, `scheduling_month`, `scheduling_finish_type`, `scheduling_finish_on_date`, `scheduling_finish_after_times`, `created_at`, `updated_at`) VALUES
(1, 4, 4, 'STATIC_DAY', NULL, '2026-02-26 00:00:00', 0, 'ONE_TIME_BILL', NULL, NULL, 'ON', NULL, NULL, '2026-02-26 09:16:33', '2026-02-26 09:16:33'),
(2, 6, 4, 'STATIC_DAY', NULL, '2026-02-26 00:00:00', 0, 'ONE_TIME_BILL', NULL, NULL, 'ON', NULL, NULL, '2026-02-26 10:01:25', '2026-02-26 11:11:02'),
(3, 7, 4, 'STATIC_DAY', NULL, '2026-03-04 00:00:00', 10, 'DAILY', NULL, NULL, 'ON', '2026-03-19', NULL, '2026-03-04 21:08:25', '2026-03-04 21:08:25'),
(4, 9, 4, 'STATIC_DAY', NULL, '2026-03-05 00:00:00', 0, 'ONE_TIME_BILL', NULL, NULL, 'ON', NULL, NULL, '2026-03-05 08:49:30', '2026-03-05 08:49:30'),
(5, 10, 3, 'STATIC_DAY', NULL, '2026-03-05 00:00:00', 0, 'ONE_TIME_BILL', NULL, NULL, 'ON', NULL, NULL, '2026-03-05 12:34:49', '2026-03-05 12:34:49');

-- --------------------------------------------------------

--
-- Table structure for table `bill_short_links`
--

CREATE TABLE `bill_short_links` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `model_id` bigint(20) UNSIGNED DEFAULT NULL,
  `model_type` varchar(191) DEFAULT NULL,
  `code` varchar(191) DEFAULT NULL,
  `link` longtext DEFAULT NULL,
  `expire` tinyint(4) NOT NULL DEFAULT 0,
  `target` varchar(191) NOT NULL DEFAULT 'PAY',
  `date` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bill_short_links`
--

INSERT INTO `bill_short_links` (`id`, `user_id`, `model_id`, `model_type`, `code`, `link`, `expire`, `target`, `date`, `created_at`, `updated_at`) VALUES
(1, NULL, 4, 'bills', 'FsoJgxhcCEfNe3Tk01', '/bills/eyJpdiI6Ikhtb29MOXgzVGF1K29pUFBSblN4Ymc9PSIsInZhbHVlIjoid00vd3B3TCtGWGNkZG9hT0FYdzROdz09IiwibWFjIjoiNzYwNjZhM2E1MDVjOTBkYjJjMzZmOTQzMTViNzQ2OTU4MTI0M2Y4MjhiYjIwNzRiOGJhMTM4OTU1ZDc3ZGI1NSIsInRhZyI6IiJ9/quick-payment-link', 0, 'PAY', '2026-02-26 09:16:33', NULL, NULL),
(2, NULL, 4, 'bills', 'ErzEM16qgAqGqgFZ01', '/bills/pdf/eyJpdiI6Ik5ZbWtmUHFnbGJNaXBkRlJhemJtR2c9PSIsInZhbHVlIjoidFBzbUtpb1NmUWF1ZDJZYXlVR0hlUT09IiwibWFjIjoiM2M1MDFmYTNlZmE1NmMzMmI1ZjVjNjI1ZTdkMDAyMGNkNzliZmRhYTM4NzcxODliZTBlZTdmZTQyOGUwODc1OCIsInRhZyI6IiJ9', 0, 'BILL_PDF', '2026-02-26 09:16:33', NULL, NULL),
(4, NULL, 6, 'bills', 'CfpUlAWa6MzR2N5s01', '/bills/pdf/eyJpdiI6ImJ4U0VqZjdqL3hxRVovc1BOMFhPbnc9PSIsInZhbHVlIjoidlFLNTBtVmFydDNUMnRGbXpXYUphUT09IiwibWFjIjoiYWFkOGY0MjA2M2IzZTE1ZGRjMTdhMmJiODVhZGFiZmY5ZjkyNmM4MWFlMDY2ZDQ2ZDQ3Yjk4OTA5NDNmMDQyNiIsInRhZyI6IiJ9', 0, 'BILL_PDF', '2026-02-26 10:01:25', NULL, NULL),
(6, NULL, 6, 'bills', 'MLK1cObkVdHt9XqI01', '/bills/eyJpdiI6ImRRNndmcnVKcFBFMDJ5Y1BJd29OWVE9PSIsInZhbHVlIjoiYTVmdVlLbWFocUJsV3ZWd0IvQVo0Zz09IiwibWFjIjoiZDVkNTc0YmFmYmRlYmFkZTM5M2I1MTg2ZTgzNTVmZmMxMWZjMTRlZTk5ZTgyNDA0MWNjMzJkYmE1MTQzNmZlNCIsInRhZyI6IiJ9/quick-payment-link', 0, 'PAY', '2026-02-26 11:11:02', NULL, NULL),
(7, NULL, 7, 'bills', 'w8BYmIcSLSqRREb701', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, 'PAY', '2026-03-04', NULL, NULL),
(8, NULL, 7, 'bills', 'IF89KnYYokXeZJ9b11', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, 'PAY', '2026-03-05', NULL, NULL),
(9, NULL, 7, 'bills', 'DA5cDNyRf55zk0qe21', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, 'PAY', '2026-03-06', NULL, NULL),
(10, NULL, 7, 'bills', 'jbMSHQd4kUup5ymN31', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, 'PAY', '2026-03-07', NULL, NULL),
(11, NULL, 7, 'bills', '7CkdY3BbMYj5ja2Y41', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, 'PAY', '2026-03-08', NULL, NULL),
(12, NULL, 7, 'bills', 'eZ4ho4UV3WYDqnW751', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, 'PAY', '2026-03-09', NULL, NULL),
(13, NULL, 7, 'bills', 'RpbTieYN6gRKBKK861', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, 'PAY', '2026-03-10', NULL, NULL),
(14, NULL, 7, 'bills', 'oYebJkbfnGExqLDE71', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, 'PAY', '2026-03-11', NULL, NULL),
(15, NULL, 7, 'bills', '07OP6inXUOLZJ7Dk81', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, 'PAY', '2026-03-12', NULL, NULL),
(16, NULL, 7, 'bills', 'oT4ZyzrhGrSRsos791', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, 'PAY', '2026-03-13', NULL, NULL),
(17, NULL, 7, 'bills', 'RPr2b5dYk6QXkAmV101', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, 'PAY', '2026-03-14', NULL, NULL),
(18, NULL, 7, 'bills', 'avE97nweQTW2lrtA111', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, 'PAY', '2026-03-15', NULL, NULL),
(19, NULL, 7, 'bills', 'BSnvKD1TM52OcggN121', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, 'PAY', '2026-03-16', NULL, NULL),
(20, NULL, 7, 'bills', 'ro6dJPVwBvonWwz5131', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, 'PAY', '2026-03-17', NULL, NULL),
(21, NULL, 7, 'bills', 'gogxfalnccmFpIoO141', '/bills/eyJpdiI6ImdSTlk1aHZLSWxTM2E2VXo2YW1MYlE9PSIsInZhbHVlIjoiRzVyQ0Z5SmJ5Z3FnSDdYc1RQU0xiZz09IiwibWFjIjoiNTczOGZjZDJlMjdmY2I3YTViZmEzZTdkNmQzNGU2MjI5OWIzZjkzNjA2YmFjYzNkZmI3ZmY4NTcwNjdiY2ViMiIsInRhZyI6IiJ9/quick-payment-link', 0, 'PAY', '2026-03-18', NULL, NULL),
(22, NULL, 7, 'bills', 'H7Aon51dcGWKadOs01', '/bills/pdf/eyJpdiI6IjIzNTVmRnY5YnFWR0FpZ1RzYS9TdUE9PSIsInZhbHVlIjoicGxvOE9TUmdFci9nQndlT2E4MFJLZz09IiwibWFjIjoiMjczMWY5ZmVlMjU1YmQxN2JkNjljOTdmYTBiNWQ1NGYxNTFlZWE4Zjg3ZDllZDM5ODc0MTcxMTRhNTFiOWM4YyIsInRhZyI6IiJ9', 0, 'BILL_PDF', '2026-03-04 21:08:25', NULL, NULL),
(23, NULL, 9, 'bills', '5fmunHHzF0lCb0Uz01', '/bills/eyJpdiI6ImRCNEVFanRCUFNnMUFpSXZGSzRacHc9PSIsInZhbHVlIjoiamM3L1JXVHQzUFgxSE5YVUVKc3c1UT09IiwibWFjIjoiZWVjNjcxMTI0ZmQ0NDkyYjkwN2QzNjQ1ODE2YzMyOTQ1YzVjMDIyZDU3MWNlNjAzZTdlODAwYTI5Mzc5MTQ5MCIsInRhZyI6IiJ9/quick-payment-link', 0, 'PAY', '2026-03-05 08:49:30', NULL, NULL),
(24, NULL, 9, 'bills', 'e1rsXcQdNmr3iml301', '/bills/pdf/eyJpdiI6Inl3ZEhHeFpPc0RnSU5WaFdHRUFLSEE9PSIsInZhbHVlIjoibnZTSXRGT3V4Vm9jZGJTbmdzNmszQT09IiwibWFjIjoiZDI2NzMwYzZmNTdkMTJhNjM3YjIzZTZhN2I5ZmViZDI5NTFkZWI4YTlmMWRhNGVlN2FmNGFlM2E5MzEzYzkxNiIsInRhZyI6IiJ9', 0, 'BILL_PDF', '2026-03-05 08:49:30', NULL, NULL),
(25, NULL, 10, 'bills', 'GPmnXO1GPiFcTjqh01', '/bills/eyJpdiI6Ijg0NFlaWWEwa3F0VjFzbXlmWDRyWlE9PSIsInZhbHVlIjoiOE5KeUVuU250aENDU3VKVXFMZFVUUT09IiwibWFjIjoiZjkwZTNmN2NhZGY3NDhmZGMzMjUwZGIxYzBmNTgxOTA5YzFhOWMxYTliOTJkODU4NTU1NWU2ZmMxZTQ2Zjg0MCIsInRhZyI6IiJ9/quick-payment-link', 0, 'PAY', '2026-03-05 12:34:49', NULL, NULL),
(26, NULL, 10, 'bills', 'JvXibf5sy0X9UZnr01', '/bills/pdf/eyJpdiI6Ijh1Sld5L3JxS3JBUVBDeHg4aGxQTEE9PSIsInZhbHVlIjoibS9ucTArTXVHeDhIeEpWWDRUZVVsUT09IiwibWFjIjoiMzMwZTBhNGZjY2NhZjNhMTY5NTUwZmRkZDRkOGI3ZmE5ZTY4N2FlMzIwMDFjOWMwMDRiMTI2NjZkZDljZjNmMiIsInRhZyI6IiJ9', 0, 'BILL_PDF', '2026-03-05 12:34:49', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bill_transactions`
--

CREATE TABLE `bill_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `bill_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payment_id` varchar(191) DEFAULT NULL,
  `payment_bill_id` varchar(191) DEFAULT NULL,
  `payment_method` varchar(191) DEFAULT NULL,
  `payment_status` enum('PENDING','PAID','FAILED') NOT NULL DEFAULT 'PENDING',
  `transaction_id` varchar(191) DEFAULT NULL,
  `reference_id` varchar(191) DEFAULT NULL,
  `shorten_link_code` varchar(191) DEFAULT NULL,
  `hash_code` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bill_translations`
--

CREATE TABLE `bill_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bill_id` bigint(20) UNSIGNED DEFAULT NULL,
  `locale` varchar(191) NOT NULL,
  `title` varchar(191) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bill_translations`
--

INSERT INTO `bill_translations` (`id`, `bill_id`, `locale`, `title`, `body`, `created_at`, `updated_at`) VALUES
(1, 1, 'en', 'jsdhflsdhl', 'gfhghdf', NULL, NULL),
(2, 1, 'ar', 'يبلليبل', 'بليبلبي', NULL, NULL),
(3, 2, 'en', 'jksfjh', 'sdjkfs', NULL, NULL),
(4, 2, 'ar', 'jsfks', 'sdfsd', NULL, NULL),
(5, 3, 'en', 'jksfjh', 'sdjkfs', NULL, NULL),
(6, 3, 'ar', 'jsfks', 'sdfsd', NULL, NULL),
(7, 4, 'en', 'jksfjh', 'sdjkfs', NULL, NULL),
(8, 4, 'ar', 'jsfks', 'sdfsd', NULL, NULL),
(9, 5, 'en', 'ناعاعخ', 'شسايخ', NULL, NULL),
(10, 5, 'ar', 'اسيخهسش', 'شاسيه', NULL, NULL),
(11, 6, 'en', 'new', 'new', NULL, NULL),
(12, 6, 'ar', 'new', 'new', NULL, NULL),
(13, 7, 'en', 'Et animi doloribus', 'Adipisicing omnis pa', NULL, NULL),
(14, 7, 'ar', 'Doloribus voluptatum', 'Sed distinctio Opti', NULL, NULL),
(15, 8, 'en', 'fatorra', NULL, NULL, NULL),
(16, 8, 'ar', 'فاتورة سريعة', NULL, NULL, NULL),
(17, 9, 'en', 'new', 'Ratione ullamco exce', NULL, NULL),
(18, 9, 'ar', 'new', 'Doloribus cillum vol', NULL, NULL),
(19, 10, 'en', 'desc', 'desc', NULL, NULL),
(20, 10, 'ar', 'موضوع', 'عربي', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bill_warnings`
--

CREATE TABLE `bill_warnings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bill_id` bigint(20) UNSIGNED DEFAULT NULL,
  `shorten_link_code` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bond_types`
--

CREATE TABLE `bond_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bond_types`
--

INSERT INTO `bond_types` (`id`, `user_id`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, '2026-02-24 11:45:53', '2026-02-24 11:45:53', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bond_type_translations`
--

CREATE TABLE `bond_type_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bond_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `locale` varchar(191) NOT NULL,
  `title` varchar(191) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bond_type_translations`
--

INSERT INTO `bond_type_translations` (`id`, `bond_type_id`, `locale`, `title`, `body`, `created_at`, `updated_at`) VALUES
(1, 1, 'en', 'test', 'test', NULL, NULL),
(2, 1, 'ar', 'تيست', 'تيست', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `buildings`
--

CREATE TABLE `buildings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `building_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `location` varchar(191) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `buildings`
--

INSERT INTO `buildings` (`id`, `building_type_id`, `location`, `sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 'test', 58, 1, '2026-02-23 21:42:27', '2026-02-24 08:34:03', NULL),
(2, 3, 'الكويت 2', 0, 1, '2026-03-05 13:12:41', '2026-03-05 13:12:41', NULL),
(3, 2, 'عقار 3', 0, 1, '2026-03-05 13:14:01', '2026-03-05 13:14:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `building_translations`
--

CREATE TABLE `building_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `building_id` bigint(20) UNSIGNED DEFAULT NULL,
  `locale` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `address` varchar(191) DEFAULT NULL,
  `note` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `building_translations`
--

INSERT INTO `building_translations` (`id`, `building_id`, `locale`, `name`, `address`, `note`, `created_at`, `updated_at`) VALUES
(1, 1, 'en', 'Nihil voluptatem Si', 'Sed nemo culpa in r', 'Et nulla quaerat eli', NULL, NULL),
(2, 1, 'ar', 'عقار رقم 1', 'الكويت', 'Totam occaecat paria', NULL, NULL),
(3, 2, 'en', 'build 2', 'build 2', 'build 2', NULL, NULL),
(4, 2, 'ar', 'عقار 2', 'الكويت 2', 'الكويت 2', NULL, NULL),
(5, 3, 'en', 'build 3', 'build 3', 'build 3', NULL, NULL),
(6, 3, 'ar', 'عقار 3', 'عقار 3', 'عقار 3', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `building_types`
--

CREATE TABLE `building_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `building_types`
--

INSERT INTO `building_types` (`id`, `user_id`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, '2026-02-23 20:20:56', '2026-02-24 08:20:27', '2026-02-24 08:20:27'),
(2, 1, 1, '2026-02-23 20:48:08', '2026-02-23 20:48:08', NULL),
(3, 1, 1, '2026-02-23 20:48:21', '2026-02-23 20:48:21', NULL),
(4, 1, 1, '2026-02-23 20:48:34', '2026-02-24 08:20:20', '2026-02-24 08:20:20');

-- --------------------------------------------------------

--
-- Table structure for table `building_type_translations`
--

CREATE TABLE `building_type_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `building_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `locale` varchar(191) NOT NULL,
  `title` varchar(191) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `building_type_translations`
--

INSERT INTO `building_type_translations` (`id`, `building_type_id`, `locale`, `title`, `body`, `created_at`, `updated_at`) VALUES
(1, 1, 'en', 'building type 1', NULL, NULL, NULL),
(2, 1, 'ar', 'نوع عقار 1', NULL, NULL, NULL),
(3, 2, 'en', 'Commercial real estate', 'Commercial real estate descriptopn', NULL, NULL),
(4, 2, 'ar', 'عقار تجاري', 'وصف عقار تجاري', NULL, NULL),
(5, 3, 'en', 'Investment property', 'Investment property', NULL, NULL),
(6, 3, 'ar', 'عقار استثماري', 'وصف عقار استثماري', NULL, NULL),
(7, 4, 'en', 'Sed modi labore duis', 'Recusandae Pariatur', NULL, NULL),
(8, 4, 'ar', 'Sed est placeat num', 'Ut facere ea qui qui', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `name`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'عمرو', 1, '2026-02-24 11:17:02', '2026-02-24 11:17:02', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `contact_person` varchar(191) DEFAULT NULL,
  `date` varchar(191) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `company_type_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `user_id`, `contact_person`, `date`, `sort`, `status`, `created_at`, `updated_at`, `deleted_at`, `company_type_id`) VALUES
(3, 9, 'ايمن', NULL, 0, 1, '2026-04-20 08:49:34', '2026-04-20 08:49:34', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `company_licenses`
--

CREATE TABLE `company_licenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `license_id` bigint(20) UNSIGNED DEFAULT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `license_no` varchar(191) DEFAULT NULL,
  `license_start_date` varchar(191) DEFAULT NULL,
  `license_expire_date` varchar(191) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
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
  `company_license_id` bigint(20) UNSIGNED DEFAULT NULL,
  `locale` varchar(191) NOT NULL,
  `title` varchar(191) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_translations`
--

CREATE TABLE `company_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `locale` varchar(191) NOT NULL,
  `title` varchar(191) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `address` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company_translations`
--

INSERT INTO `company_translations` (`id`, `company_id`, `locale`, `title`, `body`, `address`, `created_at`, `updated_at`) VALUES
(5, 3, 'en', NULL, NULL, NULL, NULL, NULL),
(6, 3, 'ar', 'شركة المستقبل', NULL, 'الكويت', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `company_types`
--

CREATE TABLE `company_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `sort` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company_types`
--

INSERT INTO `company_types` (`id`, `status`, `sort`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 0, NULL, NULL, NULL),
(2, 1, 0, NULL, NULL, NULL),
(3, 1, 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `company_type_translations`
--

CREATE TABLE `company_type_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `locale` varchar(191) NOT NULL,
  `title` varchar(191) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company_type_translations`
--

INSERT INTO `company_type_translations` (`id`, `company_type_id`, `locale`, `title`, `body`, `created_at`, `updated_at`) VALUES
(9, 1, 'ar', 'مستأجرة', NULL, NULL, NULL),
(10, 1, 'en', 'rented', NULL, NULL, NULL),
(11, 2, 'ar', 'مقاولات', NULL, NULL, NULL),
(12, 2, 'en', 'Contracting', NULL, NULL, NULL),
(13, 3, 'ar', 'صيانة', NULL, NULL, NULL),
(14, 3, 'en', 'Maintenance', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `contact_us`
--

CREATE TABLE `contact_us` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(191) NOT NULL,
  `last_name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `subject` varchar(191) NOT NULL,
  `message` longtext NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_us`
--

INSERT INTO `contact_us` (`id`, `first_name`, `last_name`, `email`, `subject`, `message`, `created_at`, `updated_at`) VALUES
(1, 'Mohamed', 'Elfeky', 'email@contact.com', 'contact subject', 'Contact message', '2026-03-03 13:22:36', '2026-03-03 13:22:36'),
(2, 'Zeph', 'Cain', 'zesygopi@mailinator.com', 'Reiciendis incididun', 'Veritatis at eiusmod', '2026-03-03 19:13:47', '2026-03-03 19:13:47'),
(3, 'em', 'samm', 'emadsm@gma.com', 'good', 'hello this is message', '2026-03-03 19:14:20', '2026-03-03 19:14:20');

-- --------------------------------------------------------

--
-- Table structure for table `contracts`
--

CREATE TABLE `contracts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `contract_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `bills_type` enum('ONE_TIME_BILL','DAILY','WEEKLY','MONTHLY','YEARLY','CUSTOM') DEFAULT NULL,
  `number_of_payments` int(11) DEFAULT NULL,
  `start_date` varchar(191) DEFAULT NULL,
  `end_date` varchar(191) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `sort` int(11) NOT NULL DEFAULT 0,
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
  `contract_id` bigint(20) UNSIGNED DEFAULT NULL,
  `locale` varchar(191) NOT NULL,
  `title` longtext DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contract_translations`
--

INSERT INTO `contract_translations` (`id`, `contract_id`, `locale`, `title`, `body`, `created_at`, `updated_at`) VALUES
(5, 3, 'en', 'Enim proident ut pa', 'Dolor neque ad rerum', NULL, NULL),
(6, 3, 'ar', 'عقد تيست', 'تيست', NULL, NULL),
(9, 5, 'en', 'هخثبه', 'تسيبمس', NULL, NULL),
(10, 5, 'ar', 'عسيناب', 'تسي', NULL, NULL),
(11, 6, 'en', 'dfsdf', 'sdasd', NULL, NULL),
(12, 6, 'ar', 'sdfsdf', 'adadsasd', NULL, NULL),
(13, 7, 'en', 'fdsafsdf', 'dfsdf', NULL, NULL),
(14, 7, 'ar', 'sdfsdf', 'sdfsdf', NULL, NULL),
(21, 11, 'en', 'sdfsdf', '3434', NULL, NULL),
(22, 11, 'ar', 'sdfsdf', 'fsdfsdf', NULL, NULL),
(25, 13, 'en', 'contract', 'note', NULL, NULL),
(26, 13, 'ar', 'عقد', 'تفاصيل', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `contract_types`
--

CREATE TABLE `contract_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contract_types`
--

INSERT INTO `contract_types` (`id`, `user_id`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, '2026-02-24 11:37:56', '2026-04-20 08:52:30', NULL),
(2, 1, 1, '2026-04-20 08:59:22', '2026-04-20 08:59:22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `contract_type_translations`
--

CREATE TABLE `contract_type_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contract_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `locale` varchar(191) NOT NULL,
  `title` varchar(191) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contract_type_translations`
--

INSERT INTO `contract_type_translations` (`id`, `contract_type_id`, `locale`, `title`, `body`, `created_at`, `updated_at`) VALUES
(1, 1, 'en', 'quick contract', 'test', NULL, NULL),
(2, 1, 'ar', 'عقد سريع', 'تيست', NULL, NULL),
(3, 2, 'en', NULL, NULL, NULL, NULL),
(4, 2, 'ar', 'عقد ايجار', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `contract_units`
--

CREATE TABLE `contract_units` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `unit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `contract_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contract_units`
--

INSERT INTO `contract_units` (`id`, `unit_id`, `contract_id`, `created_at`, `updated_at`) VALUES
(3, 1, 3, '2026-02-24 12:22:17', '2026-02-24 12:22:17'),
(6, 2, 5, '2026-02-26 09:32:46', '2026-02-26 09:32:46'),
(14, 2, 6, '2026-03-05 09:14:12', '2026-03-05 09:14:12'),
(17, 2, 7, '2026-03-05 09:15:29', '2026-03-05 09:15:29'),
(18, 1, 7, '2026-03-05 09:15:29', '2026-03-05 09:15:29'),
(19, 2, 11, '2026-03-05 09:22:30', '2026-03-05 09:22:30'),
(20, 1, 11, '2026-03-05 09:22:30', '2026-03-05 09:22:30'),
(23, 4, 13, '2026-03-05 12:33:36', '2026-03-05 12:33:36');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `full_name` varchar(191) DEFAULT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `employee_category_id` varchar(191) DEFAULT NULL,
  `job_title_id` varchar(191) DEFAULT NULL,
  `nationality_id` varchar(191) DEFAULT NULL,
  `civil_no` varchar(191) DEFAULT NULL,
  `passport_no` varchar(191) DEFAULT NULL,
  `passport_expire_date` varchar(191) DEFAULT NULL,
  `hiring_date` varchar(191) DEFAULT NULL,
  `residence_expire_date` varchar(191) DEFAULT NULL,
  `start_working_date` varchar(191) DEFAULT NULL,
  `end_working_date` varchar(191) DEFAULT NULL,
  `salary` varchar(191) DEFAULT NULL,
  `salary_per_day` varchar(191) DEFAULT NULL,
  `bank_iban_no` varchar(191) DEFAULT NULL,
  `bank_name` varchar(191) DEFAULT NULL,
  `bank_branch_name` varchar(191) DEFAULT NULL,
  `bank_credit_no` varchar(191) DEFAULT NULL,
  `bank_password` varchar(191) DEFAULT NULL,
  `license_no` varchar(191) DEFAULT NULL,
  `license_expire_date` varchar(191) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `user_id`, `full_name`, `phone`, `employee_category_id`, `job_title_id`, `nationality_id`, `civil_no`, `passport_no`, `passport_expire_date`, `hiring_date`, `residence_expire_date`, `start_working_date`, `end_working_date`, `salary`, `salary_per_day`, `bank_iban_no`, `bank_name`, `bank_branch_name`, `bank_credit_no`, `bank_password`, `license_no`, `license_expire_date`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Laboris sint nemo i', '01090460119', '57', '55', '18', '6548875958458754', '53646468', '2026-03-31', '2026-03-01', '2026-03-30', '2026-03-01', '2026-03-30', '1000', '330', '5486746546546186', 'Nisi blanditiis id', 'Qui qui eveniet qui', '53584645651356', '2125', '2135468', '2026-04-10', 1, '2026-03-01 08:09:27', '2026-03-01 08:09:50', NULL);

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
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`id`, `user_id`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, '2026-02-24 11:17:52', '2026-02-24 11:17:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `faq_translations`
--

CREATE TABLE `faq_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `faq_id` bigint(20) UNSIGNED DEFAULT NULL,
  `locale` varchar(191) NOT NULL,
  `title` varchar(191) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faq_translations`
--

INSERT INTO `faq_translations` (`id`, `faq_id`, `locale`, `title`, `body`, `created_at`, `updated_at`) VALUES
(1, 1, 'en', 'why choose Real estate management system', 'helps property owners, managers, and real estate companies manage their operations more efficiently. Organizations choose a real estate management system because it centralizes information, automates routine tasks, and improves decision-making.', NULL, NULL),
(2, 1, 'ar', 'لماذا نظام إدارة العقارات', 'يساعد هذا النظام مالكي العقارات والمديرين والشركات العقارية على إدارة عملياتهم بكفاءة أكبر؛ إذ تلجأ المؤسسات إلى استخدام أنظمة إدارة العقارات لقدرتها على توحيد المعلومات في مكان واحد، وأتمتة المهام الروتينية، وتحسين عملية اتخاذ القرار.', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `financial_bonds`
--

CREATE TABLE `financial_bonds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `bond_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `contract_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `check_number` int(11) DEFAULT NULL,
  `bank_name` varchar(191) DEFAULT NULL,
  `bond_number` int(11) DEFAULT NULL,
  `date` varchar(191) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `financial_bond_translations`
--

CREATE TABLE `financial_bond_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `financial_bond_id` bigint(20) UNSIGNED DEFAULT NULL,
  `locale` varchar(191) NOT NULL,
  `title` varchar(191) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `amount_in_letters` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `galleries`
--

CREATE TABLE `galleries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `galleries`
--

INSERT INTO `galleries` (`id`, `user_id`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, '2026-03-03 12:16:54', '2026-03-03 12:16:54', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `gallery_translations`
--

CREATE TABLE `gallery_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `gallery_id` bigint(20) UNSIGNED DEFAULT NULL,
  `locale` varchar(191) NOT NULL,
  `title` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gallery_translations`
--

INSERT INTO `gallery_translations` (`id`, `gallery_id`, `locale`, `title`, `created_at`, `updated_at`) VALUES
(1, 1, 'en', 'image1', NULL, NULL),
(2, 1, 'ar', 'صورة 1', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `imageables`
--

CREATE TABLE `imageables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `url` varchar(191) NOT NULL,
  `imageable_id` bigint(20) UNSIGNED NOT NULL,
  `imageable_type` varchar(191) NOT NULL,
  `type` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `imageables`
--

INSERT INTO `imageables` (`id`, `url`, `imageable_id`, `imageable_type`, `type`, `created_at`, `updated_at`) VALUES
(26, '2026-02-23-09-37-11-0699cc8874a0b7.webp', 1, 'Modules\\Companies\\Entities\\Company', 0, '2026-02-24 08:32:52', '2026-02-24 08:32:52'),
(28, '2026-02-23-09-42-27-0699cc9c35230e.webp', 1, 'Modules\\Buildings\\Entities\\Building', 0, '2026-02-24 08:34:03', '2026-02-24 08:34:03'),
(29, '2026-02-24-11-02-54-0699d855e25314.webp', 1, 'Modules\\Units\\Entities\\Unit', 0, '2026-02-24 11:02:54', '2026-02-24 11:02:54'),
(30, '2026-02-24-11-17-02-0699d88aed37f6.webp', 1, 'Modules\\Clients\\Entities\\Client', 0, '2026-02-24 11:17:02', '2026-02-24 11:17:02'),
(31, '2026-02-24-11-19-27-0699d893f98e4e.jpeg', 1, 'Modules\\Services\\Entities\\Service', 0, '2026-02-24 11:19:27', '2026-02-24 11:19:27'),
(33, '2026-02-24-11-33-34-0699d8c8e731d7.webp', 1, 'Modules\\Teams\\Entities\\Team', 0, '2026-02-24 11:33:34', '2026-02-24 11:33:34'),
(34, '2026-02-24-11-50-22-0699d907e38db1.webp', 2, 'App\\Models\\User', 0, '2026-02-24 11:50:22', '2026-02-24 11:50:22'),
(35, '2026-02-25-08-15-52-0699f58782fb3b.webp', 8, 'Modules\\Projects\\Entities\\Project', 0, '2026-02-25 20:15:52', '2026-02-25 20:15:52'),
(40, '2026-02-26-08-57-02-069a00adeb9ec2.webp', 9, 'Modules\\Projects\\Entities\\Project', 1, '2026-02-26 09:12:51', '2026-02-26 09:12:51'),
(53, '2026-02-26-09-40-34-069a015125904c.webp', 8, 'Modules\\Projects\\Entities\\Project', 1, '2026-02-26 09:42:12', '2026-02-26 09:42:12'),
(54, '2026-02-26-12-36-15-069a03e3f47559.webp', 55, 'Modules\\Settings\\Entities\\Setting', 0, '2026-02-26 12:36:15', '2026-02-26 12:36:15'),
(55, '2026-03-01-08-09-50-069a3f44e3086d.webp', 1, 'Modules\\Employees\\Entities\\Employee', 0, '2026-03-01 08:09:50', '2026-03-01 08:09:50'),
(56, '2026-03-01-08-13-03-069a49dcfe4091.svg', 54, 'Modules\\Settings\\Entities\\Setting', 0, '2026-03-01 20:13:03', '2026-03-01 20:13:03'),
(57, '2026-03-03-12-16-54-069a6d136aa20d.webp', 1, 'Modules\\Galleries\\Entities\\Gallery', 0, '2026-03-03 12:16:54', '2026-03-03 12:16:54'),
(61, '2026-03-05-08-20-41-069a93cd9a1ecb.png', 4, 'App\\Models\\User', 0, '2026-03-05 08:20:41', '2026-03-05 08:20:41'),
(62, '2026-03-05-08-20-58-069a93cea2dcae.png', 5, 'App\\Models\\User', 0, '2026-03-05 08:20:58', '2026-03-05 08:20:58'),
(63, '2026-03-05-08-21-10-069a93cf6dfb3e.png', 6, 'App\\Models\\User', 0, '2026-03-05 08:21:10', '2026-03-05 08:21:10'),
(64, '2026-03-05-08-33-42-069a93fe69fc75.png', 7, 'App\\Models\\User', 0, '2026-03-05 08:33:42', '2026-03-05 08:33:42'),
(67, '2026-03-05-11-17-57-069a96665cfa7d.webp', 4, 'Modules\\Units\\Entities\\Unit', 0, '2026-03-05 11:48:22', '2026-03-05 11:48:22'),
(68, '2026-03-05-11-17-57-069a96665d3597.webp', 4, 'Modules\\Units\\Entities\\Unit', 1, '2026-03-05 11:48:22', '2026-03-05 11:48:22'),
(70, '2026-03-05-12-22-43-069a97593392fe.jpeg', 8, 'App\\Models\\User', 0, '2026-03-05 12:22:43', '2026-03-05 12:22:43'),
(72, '2026-03-05-01-13-39-069a9818303d7c.webp', 2, 'Modules\\Buildings\\Entities\\Building', 0, '2026-03-05 13:13:39', '2026-03-05 13:13:39'),
(74, '2026-03-05-01-14-38-069a981bea5771.webp', 5, 'Modules\\Units\\Entities\\Unit', 0, '2026-03-05 13:14:38', '2026-03-05 13:14:38'),
(75, '2026-03-05-01-15-12-069a981e06d276.', 6, 'Modules\\Units\\Entities\\Unit', 0, '2026-03-05 13:15:12', '2026-03-05 13:15:12'),
(77, '2026-02-23-09-37-31-0699cc89b7726f.webp', 2, 'Modules\\Companies\\Entities\\Company', 0, '2026-04-19 14:03:13', '2026-04-19 14:03:13'),
(78, '2026-03-05-01-14-01-069a981992711d.webp', 3, 'Modules\\Buildings\\Entities\\Building', 0, '2026-04-20 12:23:49', '2026-04-20 12:23:49'),
(80, '2026-06-25-09-53-31-06a3cfa9bc0a84.png', 1, 'Modules\\About\\Entities\\About', 0, '2026-06-25 09:53:31', '2026-06-25 09:53:31'),
(81, '2026-03-03-01-07-57-069a6dd2d19b91.webp', 1, 'Modules\\Sliders\\Entities\\Slider', 0, '2026-06-25 09:54:50', '2026-06-25 09:54:50');

-- --------------------------------------------------------

--
-- Table structure for table `licenses`
--

CREATE TABLE `licenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `start_date` varchar(191) DEFAULT NULL,
  `end_date` varchar(191) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `licenses`
--

INSERT INTO `licenses` (`id`, `user_id`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, '2026-02-26', '2030-02-20', 1, '2026-02-26 12:08:05', '2026-02-26 12:08:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `license_translations`
--

CREATE TABLE `license_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `license_id` bigint(20) UNSIGNED DEFAULT NULL,
  `locale` varchar(191) NOT NULL,
  `title` varchar(191) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `license_translations`
--

INSERT INTO `license_translations` (`id`, `license_id`, `locale`, `title`, `body`, `created_at`, `updated_at`) VALUES
(1, 1, 'en', 'Accusantium ab est', 'Sed ea minim magnam', NULL, NULL),
(2, 1, 'ar', 'Dolore omnis qui ad', 'Architecto ut omnis', NULL, NULL);

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
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2021_09_26_083250_create_imageables_table', 1),
(6, '2021_09_26_083802_create_permissions_tables', 1),
(7, '2021_09_27_074310_create_applications_table', 1),
(8, '2021_12_14_113330_create_companies_table', 1),
(9, '2021_12_15_090621_create_contract_types_table', 1),
(10, '2021_12_15_091414_create_contracts_table', 1),
(11, '2021_12_15_092604_create_bond_types_table', 1),
(12, '2021_12_15_099055_create_bills_table', 1),
(13, '2021_12_15_992617_create_financial_bonds_table', 1),
(14, '2022_01_21_100708_create_licenses_table', 1),
(15, '2022_01_21_112236_create_employees_table', 1),
(16, '2022_01_21_113853_create_vacations_table', 1),
(17, '2022_01_21_114019_create_absences_table', 1),
(18, '2022_01_21_114054_create_rewards_table', 1),
(19, '2022_01_21_114257_create_auto_equipment_table', 1),
(20, '2022_01_21_114434_create_company_licenses_table', 1),
(21, '2022_01_26_084154_create_settings_table', 1),
(22, '2022_02_10_112227_create_notifications_table', 1),
(23, '2024_08_17_134233_create_faqs_table', 1),
(24, '2024_08_18_151544_create_sliders_table', 1),
(25, '2024_10_20_151657_create_clients_table', 1),
(26, '2024_10_21_143728_create_abouts_table', 1),
(27, '2024_10_22_161703_create_services_table', 1),
(28, '2024_10_23_115814_create_teams_table', 2),
(29, '2021_12_16_090636_create_building_types_table', 3),
(30, '2024_08_24_165347_create_buildings_table', 3),
(31, '2024_08_25_123616_create_units_table', 3),
(32, '2024_08_26_000000_create_contract_units_table', 3),
(33, '2024_08_27_000000_add_building_type_id_to_buildings_table', 3),
(34, '2026_02_25_000001_create_projects_table', 4),
(35, '2026_03_03_000000_create_galleries_table', 5),
(36, '2026_03_03_000001_create_contact_us_table', 6),
(37, '2026_03_04_000000_add_discount_to_contracts_table', 7),
(38, '2026_04_16_000001_create_company_types_table', 8),
(41, '2026_04_16_130236_MoveContactPersonAndAddPasswordToCompanies', 9),
(42, '2026_04_19_122132_add_country_code_to_companies_table', 10),
(43, '2026_07_15_010000_create_accounting_entities_table', 11),
(44, '2026_07_15_010100_create_accounts_table', 11),
(45, '2026_07_15_010200_create_fiscal_years_table', 11),
(46, '2026_07_15_010300_create_fiscal_periods_table', 11),
(47, '2026_07_15_010400_create_financial_centers_table', 11),
(48, '2026_07_15_010500_create_journal_entries_tables', 11),
(49, '2026_07_15_011000_create_receipt_vouchers_table', 11),
(50, '2026_07_15_011100_create_payment_vouchers_table', 11),
(51, '2026_07_20_120000_add_branch_id_to_financial_centers_table', 11),
(52, '2026_07_27_220000_create_accounting_settings_table', 11),
(53, '2026_08_01_000001_update_accounting_entities_source_type_to_company', 11);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(2, 'App\\Models\\User', 3),
(2, 'App\\Models\\User', 8),
(4, 'App\\Models\\User', 9);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(191) DEFAULT NULL,
  `email_from` varchar(191) DEFAULT NULL,
  `email_to` varchar(191) DEFAULT NULL,
  `table_name` varchar(191) DEFAULT NULL,
  `column_name` varchar(191) DEFAULT NULL,
  `before_days` varchar(191) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `slug`, `email_from`, `email_to`, `table_name`, `column_name`, `before_days`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'contracts', 'notify@alosaimiperal.com', 'notify-info@alosaimiperal.com', 'contracts', 'end_date', '30', 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(2, 'employees_passport', 'notify@alosaimiperal.com', 'notify-info@alosaimiperal.com', 'employees', 'passport_expire_date', '30', 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(3, 'employees_residence', 'notify@alosaimiperal.com', 'notify-info@alosaimiperal.com', 'employees', 'residence_expire_date', '30', 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(4, 'employees_license', 'notify@alosaimiperal.com', 'notify-info@alosaimiperal.com', 'employees', 'license_expire_date', '30', 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(5, 'licenses', 'notify@alosaimiperal.com', 'notify-info@alosaimiperal.com', 'licenses', 'end_date', '30', 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(6, 'company_licenses', 'notify@alosaimiperal.com', 'notify-info@alosaimiperal.com', 'company_licenses', 'license_expire_date', '30', 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(7, 'autoequipment', 'notify@alosaimiperal.com', 'notify-info@alosaimiperal.com', 'auto_equipment', 'notebook_expire_date', '30', 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(8, 'employee_vacations', 'notify@alosaimiperal.com', 'notify-info@alosaimiperal.com', 'vacations', 'end_date', '30', 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(9, 'employee_absences', 'notify@alosaimiperal.com', 'notify-info@alosaimiperal.com', 'absences', 'end_date', '30', 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notification_logs`
--

CREATE TABLE `notification_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `notification_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `model_id` bigint(20) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_translations`
--

CREATE TABLE `notification_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `notification_id` bigint(20) UNSIGNED DEFAULT NULL,
  `locale` varchar(191) NOT NULL,
  `title` varchar(191) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notification_translations`
--

INSERT INTO `notification_translations` (`id`, `notification_id`, `locale`, `title`, `body`, `created_at`, `updated_at`) VALUES
(1, 1, 'en', 'Expire date of contract', 'Alert message...', NULL, NULL),
(2, 1, 'ar', 'عند إنتهاء العقد', 'رسالة التنبيه ....', NULL, NULL),
(3, 2, 'en', 'Expire date of passport', 'Alert message...', NULL, NULL),
(4, 2, 'ar', 'عند إنتهاء جواز السفر', 'رسالة التنبيه ....', NULL, NULL),
(5, 3, 'en', 'Expire date of Residence', 'Alert message...', NULL, NULL),
(6, 3, 'ar', 'عند إنتهاء الإقامة', 'رسالة التنبيه ....', NULL, NULL),
(7, 4, 'en', 'Expire date of work licesnse', 'Alert message...', NULL, NULL),
(8, 4, 'ar', 'عند إنتهاء رخصة العمل', 'رسالة التنبيه ....', NULL, NULL),
(9, 5, 'en', 'Expire date of licesnse', 'Alert message...', NULL, NULL),
(10, 5, 'ar', 'عند إنتهاء الرخصة', 'رسالة التنبيه ....', NULL, NULL),
(11, 6, 'en', 'Expire date of company licesnse', 'Alert message...', NULL, NULL),
(12, 6, 'ar', 'عند إنتهاء رخصة الشركة', 'رسالة التنبيه ....', NULL, NULL),
(13, 7, 'en', 'Expire date of notebook', 'Alert message...', NULL, NULL),
(14, 7, 'ar', 'عند إنتهاء صلاحية الدفتر', 'رسالة التنبيه ....', NULL, NULL),
(15, 8, 'en', 'Expire date of vacation', 'Alert message...', NULL, NULL),
(16, 8, 'ar', 'عند إنتهاء الأجازة', 'رسالة التنبيه ....', NULL, NULL),
(17, 9, 'en', 'Expire date of absences', 'Alert message...', NULL, NULL),
(18, 9, 'ar', 'عند إنتهاء الغياب', 'رسالة التنبيه ....', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'read_users', 'api', '2026-02-12 06:52:19', NULL),
(2, 'create_users', 'api', '2026-02-12 06:52:19', NULL),
(3, 'update_users', 'api', '2026-02-12 06:52:19', NULL),
(4, 'delete_users', 'api', '2026-02-12 06:52:19', NULL),
(5, 'read_permissions', 'api', '2026-02-12 06:52:19', NULL),
(6, 'create_permissions', 'api', '2026-02-12 06:52:19', NULL),
(7, 'update_permissions', 'api', '2026-02-12 06:52:19', NULL),
(8, 'delete_permissions', 'api', '2026-02-12 06:52:19', NULL),
(9, 'read_applications', 'api', '2026-02-12 06:52:19', NULL),
(10, 'create_applications', 'api', '2026-02-12 06:52:19', NULL),
(11, 'update_applications', 'api', '2026-02-12 06:52:19', NULL),
(12, 'delete_applications', 'api', '2026-02-12 06:52:19', NULL),
(13, 'read_companies', 'api', '2026-02-12 06:52:19', NULL),
(14, 'create_companies', 'api', '2026-02-12 06:52:19', NULL),
(15, 'update_companies', 'api', '2026-02-12 06:52:19', NULL),
(16, 'delete_companies', 'api', '2026-02-12 06:52:19', NULL),
(17, 'read_contract_types', 'api', '2026-02-12 06:52:19', NULL),
(18, 'create_contract_types', 'api', '2026-02-12 06:52:19', NULL),
(19, 'update_contract_types', 'api', '2026-02-12 06:52:19', NULL),
(20, 'delete_contract_types', 'api', '2026-02-12 06:52:19', NULL),
(21, 'read_contracts', 'api', '2026-02-12 06:52:19', NULL),
(22, 'create_contracts', 'api', '2026-02-12 06:52:19', NULL),
(23, 'update_contracts', 'api', '2026-02-12 06:52:19', NULL),
(24, 'delete_contracts', 'api', '2026-02-12 06:52:19', NULL),
(25, 'read_bond_types', 'api', '2026-02-12 06:52:19', NULL),
(26, 'create_bond_types', 'api', '2026-02-12 06:52:19', NULL),
(27, 'update_bond_types', 'api', '2026-02-12 06:52:19', NULL),
(28, 'delete_bond_types', 'api', '2026-02-12 06:52:19', NULL),
(29, 'read_bills', 'api', '2026-02-12 06:52:19', NULL),
(30, 'create_bills', 'api', '2026-02-12 06:52:19', NULL),
(31, 'update_bills', 'api', '2026-02-12 06:52:19', NULL),
(32, 'delete_bills', 'api', '2026-02-12 06:52:19', NULL),
(33, 'read_financial_bonds', 'api', '2026-02-12 06:52:19', NULL),
(34, 'create_financial_bonds', 'api', '2026-02-12 06:52:19', NULL),
(35, 'update_financial_bonds', 'api', '2026-02-12 06:52:19', NULL),
(36, 'delete_financial_bonds', 'api', '2026-02-12 06:52:19', NULL),
(37, 'read_licenses', 'api', '2026-02-12 06:52:19', NULL),
(38, 'create_licenses', 'api', '2026-02-12 06:52:19', NULL),
(39, 'update_licenses', 'api', '2026-02-12 06:52:19', NULL),
(40, 'delete_licenses', 'api', '2026-02-12 06:52:19', NULL),
(41, 'read_employees', 'api', '2026-02-12 06:52:19', NULL),
(42, 'create_employees', 'api', '2026-02-12 06:52:19', NULL),
(43, 'update_employees', 'api', '2026-02-12 06:52:19', NULL),
(44, 'delete_employees', 'api', '2026-02-12 06:52:19', NULL),
(45, 'read_vacations', 'api', '2026-02-12 06:52:19', NULL),
(46, 'create_vacations', 'api', '2026-02-12 06:52:19', NULL),
(47, 'update_vacations', 'api', '2026-02-12 06:52:19', NULL),
(48, 'delete_vacations', 'api', '2026-02-12 06:52:19', NULL),
(49, 'read_absences', 'api', '2026-02-12 06:52:19', NULL),
(50, 'create_absences', 'api', '2026-02-12 06:52:19', NULL),
(51, 'update_absences', 'api', '2026-02-12 06:52:19', NULL),
(52, 'delete_absences', 'api', '2026-02-12 06:52:19', NULL),
(53, 'read_rewards', 'api', '2026-02-12 06:52:19', NULL),
(54, 'create_rewards', 'api', '2026-02-12 06:52:19', NULL),
(55, 'update_rewards', 'api', '2026-02-12 06:52:19', NULL),
(56, 'delete_rewards', 'api', '2026-02-12 06:52:19', NULL),
(57, 'read_auto_equipment', 'api', '2026-02-12 06:52:19', NULL),
(58, 'create_auto_equipment', 'api', '2026-02-12 06:52:19', NULL),
(59, 'update_auto_equipment', 'api', '2026-02-12 06:52:19', NULL),
(60, 'delete_auto_equipment', 'api', '2026-02-12 06:52:19', NULL),
(61, 'read_company_licenses', 'api', '2026-02-12 06:52:19', NULL),
(62, 'create_company_licenses', 'api', '2026-02-12 06:52:19', NULL),
(63, 'update_company_licenses', 'api', '2026-02-12 06:52:19', NULL),
(64, 'delete_company_licenses', 'api', '2026-02-12 06:52:19', NULL),
(65, 'read_settings', 'api', '2026-02-12 06:52:19', NULL),
(66, 'create_settings', 'api', '2026-02-12 06:52:19', NULL),
(67, 'update_settings', 'api', '2026-02-12 06:52:19', NULL),
(68, 'delete_settings', 'api', '2026-02-12 06:52:19', NULL),
(69, 'read_notifications', 'api', '2026-02-12 06:52:19', NULL),
(70, 'create_notifications', 'api', '2026-02-12 06:52:19', NULL),
(71, 'update_notifications', 'api', '2026-02-12 06:52:19', NULL),
(72, 'delete_notifications', 'api', '2026-02-12 06:52:19', NULL),
(73, 'read_faqs', 'api', '2026-02-12 06:52:19', NULL),
(74, 'create_faqs', 'api', '2026-02-12 06:52:19', NULL),
(75, 'update_faqs', 'api', '2026-02-12 06:52:19', NULL),
(76, 'delete_faqs', 'api', '2026-02-12 06:52:19', NULL),
(77, 'read_sliders', 'api', '2026-02-12 06:52:19', NULL),
(78, 'create_sliders', 'api', '2026-02-12 06:52:19', NULL),
(79, 'update_sliders', 'api', '2026-02-12 06:52:19', NULL),
(80, 'delete_sliders', 'api', '2026-02-12 06:52:19', NULL),
(81, 'read_clients', 'api', '2026-02-12 06:52:19', NULL),
(82, 'create_clients', 'api', '2026-02-12 06:52:19', NULL),
(83, 'update_clients', 'api', '2026-02-12 06:52:19', NULL),
(84, 'delete_clients', 'api', '2026-02-12 06:52:19', NULL),
(85, 'read_abouts', 'api', '2026-02-12 06:52:19', NULL),
(86, 'create_abouts', 'api', '2026-02-12 06:52:19', NULL),
(87, 'update_abouts', 'api', '2026-02-12 06:52:19', NULL),
(88, 'delete_abouts', 'api', '2026-02-12 06:52:19', NULL),
(89, 'read_services', 'api', '2026-02-12 06:52:19', NULL),
(90, 'create_services', 'api', '2026-02-12 06:52:19', NULL),
(91, 'update_services', 'api', '2026-02-12 06:52:19', NULL),
(92, 'delete_services', 'api', '2026-02-12 06:52:19', NULL);

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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sort` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 0, 1, '2026-02-25 21:07:09', '2026-02-25 21:07:09', NULL),
(2, 0, 1, '2026-02-25 21:08:12', '2026-02-25 21:08:12', NULL),
(3, 0, 1, '2026-02-25 21:08:12', '2026-02-25 21:08:12', NULL),
(4, 0, 1, '2026-02-25 21:08:12', '2026-02-25 21:08:12', NULL),
(5, 0, 1, '2026-02-25 21:08:12', '2026-02-25 21:08:12', NULL),
(6, 0, 1, '2026-02-25 21:08:12', '2026-02-25 21:08:12', NULL),
(7, 0, 1, '2026-02-25 20:14:26', '2026-02-25 20:14:26', NULL),
(8, 0, 1, '2026-02-25 20:15:52', '2026-02-25 20:15:52', NULL),
(9, 37, 1, '2026-02-26 02:01:34', '2026-02-26 09:12:55', '2026-02-26 09:12:55');

-- --------------------------------------------------------

--
-- Table structure for table `project_translations`
--

CREATE TABLE `project_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED DEFAULT NULL,
  `locale` varchar(191) NOT NULL,
  `title` varchar(191) NOT NULL,
  `body` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_translations`
--

INSERT INTO `project_translations` (`id`, `project_id`, `locale`, `title`, `body`, `created_at`, `updated_at`) VALUES
(1, 1, 'ar', 'مشروع 1', 'وصف مشروع 1', '2026-02-25 21:08:12', '2026-02-25 21:08:12'),
(2, 1, 'en', 'project 1', 'project 1 description', NULL, NULL),
(3, 2, 'en', 'project 2 ', 'project 2 description', NULL, NULL),
(4, 2, 'ar', 'مشروع 2', NULL, NULL, NULL),
(5, 3, 'ar', 'مشروع 3', NULL, NULL, NULL),
(6, 3, 'en', 'project 3', 'project 3 description', NULL, NULL),
(7, 4, 'en', 'project 4', 'project 4 description', NULL, NULL),
(8, 4, 'ar', 'مشروع 4', 'وصف مشروع 4', NULL, NULL),
(9, 5, 'ar', 'مشروع 5', NULL, NULL, NULL),
(10, 5, 'en', 'project 5', 'project 5 description', NULL, NULL),
(11, 6, 'ar', 'مشروع 6', 'وصف مشروع 6', NULL, NULL),
(12, 6, 'en', 'project 6', 'project 6 description', NULL, NULL),
(13, 7, 'en', 'buildingtypes one', 'body en ...', NULL, NULL),
(14, 7, 'ar', 'التنصيف الاول', 'body ar ...', NULL, NULL),
(15, 8, 'en', 'buildingtypes one', 'body en ...', NULL, NULL),
(16, 8, 'ar', 'التنصيف الاول', 'body ar ...', NULL, NULL),
(17, 9, 'en', 'Sunt dolorem aut fa', 'Doloremque et commod', NULL, NULL),
(18, 9, 'ar', 'Culpa eu soluta temp', 'Ut et anim omnis sit', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rewards`
--

CREATE TABLE `rewards` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `date` varchar(191) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rewards`
--

INSERT INTO `rewards` (`id`, `user_id`, `employee_id`, `amount`, `date`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 5000.00, '2026-03-20', 1, '2026-03-01 08:13:02', '2026-03-01 08:13:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reward_translations`
--

CREATE TABLE `reward_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reward_id` bigint(20) UNSIGNED DEFAULT NULL,
  `locale` varchar(191) NOT NULL,
  `title` varchar(191) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reward_translations`
--

INSERT INTO `reward_translations` (`id`, `reward_id`, `locale`, `title`, `body`, `created_at`, `updated_at`) VALUES
(1, 1, 'en', 'jkwew', 'jkwew', NULL, NULL),
(2, 1, 'ar', 'jsdfk', 'jsdfk', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'root', 'api', 1, '2026-02-12 06:52:19', '2026-02-12 06:52:19'),
(2, 'member', 'api', 1, '2026-02-24 11:40:15', '2026-02-24 11:40:15'),
(3, 'hr', 'api', 1, '2026-03-04 09:56:46', '2026-03-04 09:56:46'),
(4, 'company', 'api', 1, '2026-03-04 09:56:46', '2026-03-04 09:56:46');

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
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(5, 3),
(6, 1),
(6, 3),
(7, 1),
(7, 3),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(11, 3),
(12, 1),
(13, 1),
(14, 1),
(14, 3),
(15, 1),
(15, 3),
(16, 1),
(16, 3),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(47, 1),
(48, 1),
(49, 1),
(50, 1),
(51, 1),
(52, 1),
(53, 1),
(54, 1),
(55, 1),
(56, 1),
(57, 1),
(58, 1),
(59, 1),
(60, 1),
(61, 1),
(62, 1),
(63, 1),
(64, 1),
(65, 1),
(66, 1),
(67, 1),
(68, 1),
(69, 1),
(70, 1),
(71, 1),
(72, 1),
(73, 1),
(74, 1),
(75, 1),
(76, 1),
(77, 1),
(78, 1),
(79, 1),
(80, 1),
(81, 1),
(82, 1),
(83, 1),
(84, 1),
(85, 1),
(86, 1),
(87, 1),
(88, 1),
(89, 1),
(90, 1),
(91, 1),
(92, 1);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(191) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `slug`, `sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'test', 0, 1, '2026-02-24 11:19:27', '2026-02-24 11:19:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `service_translations`
--

CREATE TABLE `service_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED DEFAULT NULL,
  `locale` varchar(191) NOT NULL,
  `title` varchar(191) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_translations`
--

INSERT INTO `service_translations` (`id`, `service_id`, `locale`, `title`, `body`, `created_at`, `updated_at`) VALUES
(1, 1, 'en', 'test', 'test', NULL, NULL),
(2, 1, 'ar', 'تاجير عقارات', 'تيست', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `slug` varchar(191) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `user_id`, `parent_id`, `slug`, `sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, NULL, 'address', 1, 1, '2026-02-12 06:52:19', '2026-02-12 06:52:19', NULL),
(2, NULL, NULL, 'mobile', 2, 1, '2026-02-12 06:52:19', '2026-02-12 06:52:19', NULL),
(3, NULL, NULL, 'fax', 3, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(4, NULL, NULL, 'email', 4, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(5, 1, NULL, 'company', 0, 1, '2026-02-12 06:52:20', '2026-06-25 09:56:38', NULL),
(6, NULL, NULL, 'mailbox', 6, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(7, NULL, NULL, 'jobtitles', 7, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(8, NULL, 7, NULL, 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(9, NULL, NULL, 'employeecategories', 8, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(10, NULL, 9, NULL, 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(11, NULL, NULL, 'nationalities', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(12, NULL, 11, 'australian', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(13, NULL, 11, 'argentinean', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(14, NULL, 11, 'brazilian', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(15, NULL, 11, 'cameroonian', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(16, NULL, 11, 'canadian', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(17, NULL, 11, 'colombian', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(18, NULL, 11, 'egyptian', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(19, NULL, 11, 'indian', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(20, NULL, 11, 'indonesian', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(21, NULL, 11, 'iranian', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(22, NULL, 11, 'italian', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(23, NULL, 11, 'jordanian', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(24, NULL, 11, 'korean', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(25, NULL, 11, 'syrian', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(26, NULL, 11, 'ukrainian', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(27, NULL, 11, 'belgian', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(28, NULL, 11, 'cuban', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(29, NULL, 11, 'german', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(30, NULL, 11, 'malaysian', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(31, NULL, 11, 'moroccan', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(32, NULL, 11, 'norwegian', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(33, NULL, 11, 'russian', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(34, NULL, 11, 'american', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(35, NULL, 11, 'english', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(36, NULL, 11, 'danish', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(37, NULL, 11, 'french', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(38, NULL, 11, 'dutch', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(39, NULL, 11, 'polish', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(40, NULL, 11, 'turkish', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(41, NULL, 11, 'japanese', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(42, NULL, 11, 'kuwaiti', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(43, NULL, 11, 'portuguese', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(44, NULL, 11, 'vietnamese', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(45, NULL, 11, 'iraqi', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(46, NULL, 11, 'pakistani', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(47, NULL, 11, 'qatari', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(48, NULL, 11, 'saudi', 0, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(49, NULL, NULL, 'statistics', 7, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(50, NULL, 49, '10-country', 7, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(51, NULL, NULL, 'banner', 7, 1, '2026-02-12 06:52:20', '2026-02-12 06:52:20', NULL),
(52, 1, NULL, 'socialMedia', 0, 1, '2026-02-12 06:52:20', '2026-02-24 19:52:47', NULL),
(54, 1, 52, 'facebook', 0, 1, '2026-02-12 06:52:20', '2026-03-01 20:13:03', NULL),
(55, 1, 7, NULL, 0, 1, '2026-02-26 12:36:15', '2026-02-26 12:36:15', NULL),
(56, 1, 7, NULL, 0, 1, '2026-02-26 12:36:43', '2026-02-26 12:36:43', NULL),
(57, 1, 9, NULL, 0, 1, '2026-02-26 12:37:21', '2026-02-26 12:37:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `setting_translations`
--

CREATE TABLE `setting_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `setting_id` bigint(20) UNSIGNED DEFAULT NULL,
  `locale` varchar(191) NOT NULL,
  `title` varchar(191) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `setting_translations`
--

INSERT INTO `setting_translations` (`id`, `setting_id`, `locale`, `title`, `body`, `created_at`, `updated_at`) VALUES
(1, 1, 'en', 'Address', 'Farwaniya - Block 6 - Habib Munwar St. <br/>Dabbous Comp. - C Tower - 3rd Floor', NULL, NULL),
(2, 1, 'ar', 'العنوان', 'الفروانية - ق٦ - شارع حبيب مناور<br/>مجمع الدبوس - برج سي - الدور التالت', NULL, NULL),
(3, 2, 'en', 'Tel', '24712005', NULL, NULL),
(4, 2, 'ar', 'تليفون', '24712005', NULL, NULL),
(5, 3, 'en', 'Fax', '24712006', NULL, NULL),
(6, 3, 'ar', 'فاكس', '24712006', NULL, NULL),
(7, 4, 'en', 'E-mail', ' info@alosaimiperal.com', NULL, NULL),
(8, 4, 'ar', 'إيميل', ' info@alosaimiperal.com', NULL, NULL),
(9, 5, 'en', 'Company Name', 'Real estate management system', NULL, NULL),
(10, 5, 'ar', 'أسم الشركة', 'نظام إدارة العقارات', NULL, NULL),
(11, 6, 'en', 'P.O. Box', '812 Souk Al-Dakhli, 15259 Kuwait', NULL, NULL),
(12, 6, 'ar', 'ص.ب', '٨١٢ السوق الداخلي ١٥٢٥٩ الكويت', NULL, NULL),
(13, 7, 'en', 'Job Titles', '', NULL, NULL),
(14, 7, 'ar', 'المسمي الوظيفي', '', NULL, NULL),
(15, 9, 'en', 'Employee Categories', '', NULL, NULL),
(16, 9, 'ar', 'فئات الموظفين', '', NULL, NULL),
(17, 11, 'en', 'Nationlities', NULL, NULL, NULL),
(18, 11, 'ar', 'الجنسيات', NULL, NULL, NULL),
(19, 12, 'en', 'Australian', NULL, NULL, NULL),
(20, 12, 'ar', 'استراليا', NULL, NULL, NULL),
(21, 13, 'en', 'Argentinean', NULL, NULL, NULL),
(22, 13, 'ar', 'ارجنتيني', NULL, NULL, NULL),
(23, 14, 'en', 'Brazilian', NULL, NULL, NULL),
(24, 14, 'ar', 'برازيلي', NULL, NULL, NULL),
(25, 15, 'en', 'Cameroonian', NULL, NULL, NULL),
(26, 15, 'ar', 'كاميروني', NULL, NULL, NULL),
(27, 16, 'en', 'Canadian', NULL, NULL, NULL),
(28, 16, 'ar', 'كندي', NULL, NULL, NULL),
(29, 17, 'en', 'Colombian', NULL, NULL, NULL),
(30, 17, 'ar', 'كولومبي', NULL, NULL, NULL),
(31, 18, 'en', 'Egyptian', NULL, NULL, NULL),
(32, 18, 'ar', 'مصري', NULL, NULL, NULL),
(33, 19, 'en', 'Indian', NULL, NULL, NULL),
(34, 19, 'ar', 'هندي', NULL, NULL, NULL),
(35, 20, 'en', 'Indonesian', NULL, NULL, NULL),
(36, 20, 'ar', 'اندونيسي', NULL, NULL, NULL),
(37, 21, 'en', 'Iranian', NULL, NULL, NULL),
(38, 21, 'ar', 'ايراني', NULL, NULL, NULL),
(39, 22, 'en', 'Italian', NULL, NULL, NULL),
(40, 22, 'ar', 'ايطالي', NULL, NULL, NULL),
(41, 23, 'en', 'Jordanian', NULL, NULL, NULL),
(42, 23, 'ar', 'اردني', NULL, NULL, NULL),
(43, 24, 'en', 'Korean', NULL, NULL, NULL),
(44, 24, 'ar', 'كوري', NULL, NULL, NULL),
(45, 25, 'en', 'Syrian', NULL, NULL, NULL),
(46, 25, 'ar', 'سوري', NULL, NULL, NULL),
(47, 26, 'en', 'Ukrainian', NULL, NULL, NULL),
(48, 26, 'ar', 'اوكراني', NULL, NULL, NULL),
(49, 27, 'en', 'Belgian', NULL, NULL, NULL),
(50, 27, 'ar', 'بلجيكي', NULL, NULL, NULL),
(51, 28, 'en', 'Cuban', NULL, NULL, NULL),
(52, 28, 'ar', 'كوبي', NULL, NULL, NULL),
(53, 29, 'en', 'German', NULL, NULL, NULL),
(54, 29, 'ar', 'الماني', NULL, NULL, NULL),
(55, 30, 'en', 'Malaysian', NULL, NULL, NULL),
(56, 30, 'ar', 'ماليزي', NULL, NULL, NULL),
(57, 31, 'en', 'Moroccan', NULL, NULL, NULL),
(58, 31, 'ar', 'مغربي', NULL, NULL, NULL),
(59, 32, 'en', 'Norwegian', NULL, NULL, NULL),
(60, 32, 'ar', 'نرويجي', NULL, NULL, NULL),
(61, 33, 'en', 'Russian', NULL, NULL, NULL),
(62, 33, 'ar', 'روسي', NULL, NULL, NULL),
(63, 34, 'en', 'American', NULL, NULL, NULL),
(64, 34, 'ar', 'امريكي', NULL, NULL, NULL),
(65, 35, 'en', 'English', NULL, NULL, NULL),
(66, 35, 'ar', 'انجليزي', NULL, NULL, NULL),
(67, 36, 'en', 'Danish', NULL, NULL, NULL),
(68, 36, 'ar', 'دانماركي', NULL, NULL, NULL),
(69, 37, 'en', 'French', NULL, NULL, NULL),
(70, 37, 'ar', 'فرنسي', NULL, NULL, NULL),
(71, 38, 'en', 'Dutch', NULL, NULL, NULL),
(72, 38, 'ar', 'هولندي', NULL, NULL, NULL),
(73, 39, 'en', 'Polish', NULL, NULL, NULL),
(74, 39, 'ar', 'بولندي', NULL, NULL, NULL),
(75, 40, 'en', 'Turkish', NULL, NULL, NULL),
(76, 40, 'ar', 'تركي', NULL, NULL, NULL),
(77, 41, 'en', 'Japanese', NULL, NULL, NULL),
(78, 41, 'ar', 'ياباني', NULL, NULL, NULL),
(79, 42, 'en', 'Kuwaiti', NULL, NULL, NULL),
(80, 42, 'ar', 'كويتي', NULL, NULL, NULL),
(81, 43, 'en', 'Portuguese', NULL, NULL, NULL),
(82, 43, 'ar', 'برتغالي', NULL, NULL, NULL),
(83, 44, 'en', 'Vietnamese', NULL, NULL, NULL),
(84, 44, 'ar', 'فيتنامي', NULL, NULL, NULL),
(85, 45, 'en', 'Iraqi', NULL, NULL, NULL),
(86, 45, 'ar', 'عراقي', NULL, NULL, NULL),
(87, 46, 'en', 'Pakistani', NULL, NULL, NULL),
(88, 46, 'ar', 'باكستاني', NULL, NULL, NULL),
(89, 47, 'en', 'Qatari', NULL, NULL, NULL),
(90, 47, 'ar', 'قطري', NULL, NULL, NULL),
(91, 48, 'en', 'Saudi', NULL, NULL, NULL),
(92, 48, 'ar', 'سعودي', NULL, NULL, NULL),
(93, 49, 'en', 'statistics', NULL, NULL, NULL),
(94, 49, 'ar', 'الاحصائيات', NULL, NULL, NULL),
(95, 50, 'en', '+10 Country', NULL, NULL, NULL),
(96, 50, 'ar', '+10 دوله', NULL, NULL, NULL),
(97, 51, 'en', 'banners', NULL, NULL, NULL),
(98, 51, 'ar', 'البانرات', NULL, NULL, NULL),
(99, 52, 'en', 'social-media', NULL, NULL, NULL),
(103, 52, 'ar', 'السوشيال ميديا', NULL, NULL, NULL),
(104, 54, 'en', 'Facebook', 'https://www.facebook.com', NULL, NULL),
(105, 54, 'ar', 'فيسبوك', 'https://www.facebook.com', NULL, NULL),
(106, 55, 'en', 'Rerum libero totam t', 'Ea laborum Quis eve', NULL, NULL),
(107, 55, 'ar', 'Architecto nemo in e', 'Ea qui et occaecat q', NULL, NULL),
(108, 56, 'en', 'Voluptatem architec', 'Sit consequatur Do', NULL, NULL),
(109, 56, 'ar', 'Dolores veniam repr', 'Repudiandae non faci', NULL, NULL),
(110, 57, 'en', 'Omnis omnis aliquid', 'Ipsam libero debitis', NULL, NULL),
(111, 57, 'ar', 'Dolor officia labore', 'Et labore labore eni', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sliders`
--

CREATE TABLE `sliders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `video_url` varchar(191) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sliders`
--

INSERT INTO `sliders` (`id`, `user_id`, `video_url`, `sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'https://www.youtube.com/watch?v=b6B6WckkK3A', 5, 1, '2026-03-03 13:07:57', '2026-03-03 13:18:38', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `slider_translations`
--

CREATE TABLE `slider_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slider_id` bigint(20) UNSIGNED DEFAULT NULL,
  `locale` varchar(191) NOT NULL,
  `title` varchar(191) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `slider_translations`
--

INSERT INTO `slider_translations` (`id`, `slider_id`, `locale`, `title`, `body`, `created_at`, `updated_at`) VALUES
(1, 1, 'en', 'Real estate management system', NULL, NULL, NULL),
(2, 1, 'ar', 'نظام إدارة العقارات', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `experience` int(11) DEFAULT NULL,
  `location` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `country_code` varchar(191) DEFAULT NULL,
  `mobile` varchar(191) DEFAULT NULL,
  `facebook_link` varchar(191) DEFAULT NULL,
  `youtube_link` varchar(191) DEFAULT NULL,
  `twitter_link` varchar(191) DEFAULT NULL,
  `linkedin_link` varchar(191) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`id`, `experience`, `location`, `email`, `country_code`, `mobile`, `facebook_link`, `youtube_link`, `twitter_link`, `linkedin_link`, `sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, '2026-02-24 11:33:34', '2026-02-24 11:33:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `team_translations`
--

CREATE TABLE `team_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED DEFAULT NULL,
  `locale` varchar(191) NOT NULL,
  `name` varchar(191) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `position` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `team_translations`
--

INSERT INTO `team_translations` (`id`, `team_id`, `locale`, `name`, `body`, `position`, `created_at`, `updated_at`) VALUES
(1, 1, 'en', 'ayman', 'test', 'frontend', NULL, NULL),
(2, 1, 'ar', 'ايمن', 'تيست', 'فرونت ايند', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `building_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('rented','available','unavailable') NOT NULL DEFAULT 'available',
  `sort` int(11) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `building_id`, `status`, `sort`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'rented', 0, NULL, '2026-02-24 11:02:54', '2026-03-05 09:22:30'),
(2, 1, 'rented', 0, NULL, '2026-02-24 12:28:34', '2026-03-05 09:22:30'),
(4, 1, 'rented', 0, NULL, '2026-03-05 11:17:57', '2026-03-05 12:33:36'),
(5, 2, 'available', 0, NULL, '2026-03-05 13:14:38', '2026-03-05 13:14:38'),
(6, 2, 'unavailable', 0, NULL, '2026-03-05 13:14:54', '2026-03-05 13:15:12'),
(7, 2, 'available', 0, NULL, '2026-03-05 13:15:04', '2026-03-05 13:15:04'),
(8, 3, 'available', 0, NULL, '2026-03-05 13:15:47', '2026-03-05 13:15:47'),
(9, 3, 'unavailable', 0, NULL, '2026-03-05 13:15:58', '2026-03-05 13:15:58');

-- --------------------------------------------------------

--
-- Table structure for table `unit_translations`
--

CREATE TABLE `unit_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `unit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `locale` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `note` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `unit_translations`
--

INSERT INTO `unit_translations` (`id`, `unit_id`, `locale`, `name`, `note`, `created_at`, `updated_at`) VALUES
(1, 1, 'en', 'unit 1', 'unit 1', NULL, NULL),
(2, 1, 'ar', 'وحدة سكنيه قم 1', 'وحدة سكنيه قم 1', NULL, NULL),
(3, 2, 'en', 'unit 5', 'test', NULL, NULL),
(4, 2, 'ar', 'وحده 5', 'تيست', NULL, NULL),
(5, 4, 'en', 'sdfsdf', 'cvxcvxcv', NULL, NULL),
(6, 4, 'ar', 'Facere animi natus 222', 'cxvxcvxcv', NULL, NULL),
(7, 5, 'en', 'unit build 2', 'unit build 2', NULL, NULL),
(8, 5, 'ar', 'وحدة عقار 2', 'وحدة عقار 2', NULL, NULL),
(9, 6, 'en', 'Consectetur reprehen', 'Officia cumque venia', NULL, NULL),
(10, 6, 'ar', 'Enim officia rerum e', 'Praesentium cupidita', NULL, NULL),
(11, 7, 'en', 'Facilis eum dolor si', 'Et totam qui omnis n', NULL, NULL),
(12, 7, 'ar', 'Deleniti anim cupidi', 'Obcaecati consequunt', NULL, NULL),
(13, 8, 'en', 'Sit aliqua Sint e', 'Est repellendus Ad', NULL, NULL),
(14, 8, 'ar', 'Mollit nulla molesti', 'Modi rerum distincti', NULL, NULL),
(15, 9, 'en', 'Sed quis laborum Vo', 'Laudantium autem co', NULL, NULL),
(16, 9, 'ar', 'Praesentium nostrum', 'Cupiditate officiis', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(191) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `country_code` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `civil_id` varchar(255) DEFAULT NULL,
  `suspend` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `email_verified_at`, `password`, `country_code`, `mobile`, `civil_id`, `suspend`, `status`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'admin', NULL, 'admin@rms.com', NULL, '$2y$10$nFNAz91mUTznI7YN/3tMRu9xL29cG4hrcN6ZeQXHuOSeQwIuLOCJ.', '+965', '51084200', NULL, 0, 1, NULL, '2026-02-12 06:52:19', '2026-04-19 14:03:13', NULL),
(2, 'amr', 'Et exercitationem co', 'jogavivef@mailinator.com', NULL, '$2y$10$z1R2vjE8vKOj.BjEtyhmw.CnE4AbYqIZAnuLpAG7jyE0xWc/mi8PW', '+965', '51084200', 'Magna autem qui id r', 0, 1, NULL, '2026-02-24 11:49:54', '2026-02-24 11:49:54', NULL),
(3, 'Cillum quis nihil ut', 'Rerum delectus face', 'zery@mailinator.com', NULL, '$2y$10$E1BrVOP.d/Bc2JKgaMY0ceNlWtAsYd2cPhk.RpY5GdKeQ3ETQHY7q', '+1', '3319442632', 'Vel non voluptas opt', 0, 1, NULL, '2026-02-26 11:12:51', '2026-02-26 11:12:51', NULL),
(4, 'employee', NULL, 'employee@site.com', NULL, '$2y$10$3sXwOW826iE008l0S846dukoGFLZvXFTHYwW4umQXvS0RcWMM7JHq', '20', '12821542', NULL, 0, 1, NULL, '2026-03-05 08:20:41', '2026-03-05 08:20:41', NULL),
(5, 'employee', NULL, 'employee2@site.com', NULL, '$2y$10$G8fA.w3oO3O7iMBU43EkoOinW6H250ViXAN10yDJ4.Z51KQ05G4Ye', '20', '12821522342', NULL, 0, 1, NULL, '2026-03-05 08:20:58', '2026-03-05 08:20:58', NULL),
(6, 'employee', NULL, 'employee5566@site.com', NULL, '$2y$10$iCzYjeu6eN0drDtl4siwI.CkLjt6781IrdHPA58RxkJ/0Ytta2i1G', '20', '12571522342', NULL, 0, 1, NULL, '2026-03-05 08:21:10', '2026-03-05 08:21:10', NULL),
(7, 'employee', NULL, 'employee788@site.com', NULL, '$2y$10$eeein/RJRkXFbcV1jrF2MOoYLV.VYS5WS6rU8uizvBJHW41LE7NBq', '20', '12571232342', NULL, 0, 1, NULL, '2026-03-05 08:33:42', '2026-03-05 08:33:42', NULL),
(8, 'mohamed elfeky', 'mohamed elfeky', 'mohamedwaged1@gmail.com', NULL, '$2y$10$vJk/RMUdi2PsN4TTpGjapuU8A9TUIyShG9TUOQWzQcpqpW2cDYyh6', '+20', '1011320299', '2980512', 0, 1, NULL, '2026-03-05 12:20:43', '2026-03-05 12:20:43', NULL),
(9, 'شركة المستقبل', NULL, 'ayman@dasman.com', NULL, '$2y$10$vaQZR.WKZLyYCL4zOX5L5.f5.1n7pIpG9BSQE6hr/InVpkJ7LHrTG', '+20', '1090460119', NULL, 0, 1, NULL, '2026-04-20 08:49:34', '2026-04-20 08:49:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vacations`
--

CREATE TABLE `vacations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `start_date` varchar(191) DEFAULT NULL,
  `end_date` varchar(191) DEFAULT NULL,
  `days` varchar(191) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vacations`
--

INSERT INTO `vacations` (`id`, `user_id`, `employee_id`, `start_date`, `end_date`, `days`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, '2026-03-01', '2026-03-31', '-30', 1, '2026-03-01 08:11:53', '2026-03-01 08:11:53', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vacation_translations`
--

CREATE TABLE `vacation_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vacation_id` bigint(20) UNSIGNED DEFAULT NULL,
  `locale` varchar(191) NOT NULL,
  `title` varchar(191) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vacation_translations`
--

INSERT INTO `vacation_translations` (`id`, `vacation_id`, `locale`, `title`, `body`, `created_at`, `updated_at`) VALUES
(1, 1, 'en', 'khsjsd', 'khsjsd', NULL, NULL),
(2, 1, 'ar', 'gfjskldj', 'gfjskldj', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `abouts`
--
ALTER TABLE `abouts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `about_translations`
--
ALTER TABLE `about_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `about_translations_about_id_locale_unique` (`about_id`,`locale`),
  ADD KEY `about_translations_locale_index` (`locale`);

--
-- Indexes for table `absences`
--
ALTER TABLE `absences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `absences_user_id_foreign` (`user_id`),
  ADD KEY `absences_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `absence_translations`
--
ALTER TABLE `absence_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `absence_translations_absence_id_locale_unique` (`absence_id`,`locale`),
  ADD KEY `absence_translations_locale_index` (`locale`);

--
-- Indexes for table `acc_accounting_entities`
--
ALTER TABLE `acc_accounting_entities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `acc_accounting_entities_code_unique` (`code`),
  ADD UNIQUE KEY `acc_accounting_entities_source_type_source_id_unique` (`source_type`,`source_id`),
  ADD KEY `acc_accounting_entities_source_type_source_id_index` (`source_type`,`source_id`);

--
-- Indexes for table `acc_accounting_settings`
--
ALTER TABLE `acc_accounting_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `acc_accounting_settings_entity_unique` (`accounting_entity_id`),
  ADD KEY `acc_set_fy_fk` (`default_fiscal_year_id`),
  ADD KEY `acc_set_fp_fk` (`default_fiscal_period_id`),
  ADD KEY `acc_set_close_by_fk` (`last_closing_by`),
  ADD KEY `acc_set_cash_acc_fk` (`default_cash_account_id`),
  ADD KEY `acc_set_bank_acc_fk` (`default_bank_account_id`),
  ADD KEY `acc_set_recv_acc_fk` (`default_receivables_account_id`),
  ADD KEY `acc_set_pay_acc_fk` (`default_payables_account_id`),
  ADD KEY `acc_set_ret_earn_acc_fk` (`default_retained_earnings_account_id`);

--
-- Indexes for table `acc_accounts`
--
ALTER TABLE `acc_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `acc_accounts_accounting_entity_id_code_unique` (`accounting_entity_id`,`code`),
  ADD KEY `acc_accounts_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `acc_financial_centers`
--
ALTER TABLE `acc_financial_centers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `acc_financial_centers_accounting_entity_id_code_unique` (`accounting_entity_id`,`code`),
  ADD KEY `acc_financial_centers_parent_id_foreign` (`parent_id`),
  ADD KEY `acc_financial_centers_entity_branch_idx` (`accounting_entity_id`,`branch_id`);

--
-- Indexes for table `acc_fiscal_periods`
--
ALTER TABLE `acc_fiscal_periods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `acc_fiscal_periods_fiscal_year_id_period_no_unique` (`fiscal_year_id`,`period_no`);

--
-- Indexes for table `acc_fiscal_years`
--
ALTER TABLE `acc_fiscal_years`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `acc_fiscal_years_accounting_entity_id_code_unique` (`accounting_entity_id`,`code`);

--
-- Indexes for table `acc_journal_entries`
--
ALTER TABLE `acc_journal_entries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `acc_journal_entries_entry_no_unique` (`entry_no`),
  ADD KEY `acc_journal_entries_accounting_entity_id_foreign` (`accounting_entity_id`),
  ADD KEY `acc_journal_entries_fiscal_year_id_foreign` (`fiscal_year_id`),
  ADD KEY `acc_journal_entries_fiscal_period_id_foreign` (`fiscal_period_id`),
  ADD KEY `acc_journal_entries_financial_center_id_foreign` (`financial_center_id`),
  ADD KEY `acc_journal_entries_created_by_foreign` (`created_by`),
  ADD KEY `acc_journal_entries_posted_by_foreign` (`posted_by`);

--
-- Indexes for table `acc_journal_entry_lines`
--
ALTER TABLE `acc_journal_entry_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `acc_journal_entry_lines_journal_entry_id_foreign` (`journal_entry_id`),
  ADD KEY `acc_journal_entry_lines_account_id_foreign` (`account_id`),
  ADD KEY `acc_journal_entry_lines_financial_center_id_foreign` (`financial_center_id`);

--
-- Indexes for table `acc_payment_vouchers`
--
ALTER TABLE `acc_payment_vouchers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `acc_payment_vouchers_voucher_no_unique` (`voucher_no`),
  ADD KEY `acc_payment_vouchers_accounting_entity_id_foreign` (`accounting_entity_id`),
  ADD KEY `acc_payment_vouchers_fiscal_year_id_foreign` (`fiscal_year_id`),
  ADD KEY `acc_payment_vouchers_fiscal_period_id_foreign` (`fiscal_period_id`),
  ADD KEY `acc_payment_vouchers_financial_center_id_foreign` (`financial_center_id`),
  ADD KEY `acc_payment_vouchers_journal_entry_id_foreign` (`journal_entry_id`),
  ADD KEY `acc_payment_vouchers_payment_account_id_foreign` (`payment_account_id`),
  ADD KEY `acc_payment_vouchers_offset_account_id_foreign` (`offset_account_id`),
  ADD KEY `acc_payment_vouchers_created_by_foreign` (`created_by`),
  ADD KEY `acc_payment_vouchers_posted_by_foreign` (`posted_by`);

--
-- Indexes for table `acc_receipt_vouchers`
--
ALTER TABLE `acc_receipt_vouchers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `acc_receipt_vouchers_voucher_no_unique` (`voucher_no`),
  ADD KEY `acc_receipt_vouchers_accounting_entity_id_foreign` (`accounting_entity_id`),
  ADD KEY `acc_receipt_vouchers_fiscal_year_id_foreign` (`fiscal_year_id`),
  ADD KEY `acc_receipt_vouchers_fiscal_period_id_foreign` (`fiscal_period_id`),
  ADD KEY `acc_receipt_vouchers_financial_center_id_foreign` (`financial_center_id`),
  ADD KEY `acc_receipt_vouchers_journal_entry_id_foreign` (`journal_entry_id`),
  ADD KEY `acc_receipt_vouchers_receipt_account_id_foreign` (`receipt_account_id`),
  ADD KEY `acc_receipt_vouchers_offset_account_id_foreign` (`offset_account_id`),
  ADD KEY `acc_receipt_vouchers_created_by_foreign` (`created_by`),
  ADD KEY `acc_receipt_vouchers_posted_by_foreign` (`posted_by`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `applications_slug_unique` (`slug`);

--
-- Indexes for table `application_translations`
--
ALTER TABLE `application_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `application_translations_application_id_locale_unique` (`application_id`,`locale`),
  ADD KEY `application_translations_locale_index` (`locale`);

--
-- Indexes for table `attachmentables`
--
ALTER TABLE `attachmentables`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `auto_equipment`
--
ALTER TABLE `auto_equipment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `auto_equipment_user_id_foreign` (`user_id`);

--
-- Indexes for table `auto_equipment_translations`
--
ALTER TABLE `auto_equipment_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `auto_equipment_translations_auto_equipment_id_locale_unique` (`auto_equipment_id`,`locale`),
  ADD KEY `auto_equipment_translations_locale_index` (`locale`);

--
-- Indexes for table `bills`
--
ALTER TABLE `bills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bills_user_id_foreign` (`user_id`),
  ADD KEY `bills_contract_id_foreign` (`contract_id`);

--
-- Indexes for table `bill_payment_dues`
--
ALTER TABLE `bill_payment_dues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bill_payment_dues_bill_id_foreign` (`bill_id`);

--
-- Indexes for table `bill_short_links`
--
ALTER TABLE `bill_short_links`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bill_short_links_user_id_foreign` (`user_id`);

--
-- Indexes for table `bill_transactions`
--
ALTER TABLE `bill_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bill_transactions_user_id_foreign` (`user_id`),
  ADD KEY `bill_transactions_bill_id_foreign` (`bill_id`);

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
-- Indexes for table `bond_types`
--
ALTER TABLE `bond_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bond_types_user_id_foreign` (`user_id`);

--
-- Indexes for table `bond_type_translations`
--
ALTER TABLE `bond_type_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bond_type_translations_bond_type_id_locale_unique` (`bond_type_id`,`locale`),
  ADD KEY `bond_type_translations_locale_index` (`locale`);

--
-- Indexes for table `buildings`
--
ALTER TABLE `buildings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `buildings_building_type_id_foreign` (`building_type_id`);

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
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

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
  ADD UNIQUE KEY `company_license_translations_company_license_id_locale_unique` (`company_license_id`,`locale`),
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
-- Indexes for table `contact_us`
--
ALTER TABLE `contact_us`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contracts_user_id_foreign` (`user_id`),
  ADD KEY `contracts_contract_type_id_foreign` (`contract_type_id`),
  ADD KEY `contracts_company_id_foreign` (`company_id`);

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
  ADD KEY `contract_units_unit_id_foreign` (`unit_id`),
  ADD KEY `contract_units_contract_id_foreign` (`contract_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employees_user_id_foreign` (`user_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faqs_user_id_foreign` (`user_id`);

--
-- Indexes for table `faq_translations`
--
ALTER TABLE `faq_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `faq_translations_faq_id_locale_unique` (`faq_id`,`locale`),
  ADD KEY `faq_translations_locale_index` (`locale`);

--
-- Indexes for table `financial_bonds`
--
ALTER TABLE `financial_bonds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `financial_bonds_user_id_foreign` (`user_id`),
  ADD KEY `financial_bonds_company_id_foreign` (`company_id`),
  ADD KEY `financial_bonds_contract_id_foreign` (`contract_id`);

--
-- Indexes for table `financial_bond_translations`
--
ALTER TABLE `financial_bond_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `financial_bond_translations_financial_bond_id_locale_unique` (`financial_bond_id`,`locale`),
  ADD KEY `financial_bond_translations_locale_index` (`locale`);

--
-- Indexes for table `galleries`
--
ALTER TABLE `galleries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `galleries_user_id_foreign` (`user_id`);

--
-- Indexes for table `gallery_translations`
--
ALTER TABLE `gallery_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gallery_translations_gallery_id_locale_unique` (`gallery_id`,`locale`),
  ADD KEY `gallery_translations_locale_index` (`locale`);

--
-- Indexes for table `imageables`
--
ALTER TABLE `imageables`
  ADD PRIMARY KEY (`id`);

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
  ADD UNIQUE KEY `notifications_slug_unique` (`slug`);

--
-- Indexes for table `notification_logs`
--
ALTER TABLE `notification_logs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `notification_logs_notification_id_employee_id_model_id_unique` (`notification_id`,`employee_id`,`model_id`),
  ADD KEY `notification_logs_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `notification_translations`
--
ALTER TABLE `notification_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `notification_translations_notification_id_locale_unique` (`notification_id`,`locale`),
  ADD KEY `notification_translations_locale_index` (`locale`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_unique` (`name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_translations`
--
ALTER TABLE `project_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_translations_project_id_locale_unique` (`project_id`,`locale`),
  ADD KEY `project_translations_locale_index` (`locale`);

--
-- Indexes for table `rewards`
--
ALTER TABLE `rewards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rewards_user_id_foreign` (`user_id`),
  ADD KEY `rewards_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `reward_translations`
--
ALTER TABLE `reward_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reward_translations_reward_id_locale_unique` (`reward_id`,`locale`),
  ADD KEY `reward_translations_locale_index` (`locale`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `services_slug_index` (`slug`);

--
-- Indexes for table `service_translations`
--
ALTER TABLE `service_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `service_translations_service_id_locale_unique` (`service_id`,`locale`),
  ADD KEY `service_translations_locale_index` (`locale`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_slug_unique` (`slug`),
  ADD KEY `settings_user_id_foreign` (`user_id`);

--
-- Indexes for table `setting_translations`
--
ALTER TABLE `setting_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_translations_setting_id_locale_unique` (`setting_id`,`locale`),
  ADD KEY `setting_translations_locale_index` (`locale`);

--
-- Indexes for table `sliders`
--
ALTER TABLE `sliders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sliders_user_id_foreign` (`user_id`);

--
-- Indexes for table `slider_translations`
--
ALTER TABLE `slider_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slider_translations_slider_id_locale_unique` (`slider_id`,`locale`),
  ADD KEY `slider_translations_locale_index` (`locale`);

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `team_translations`
--
ALTER TABLE `team_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `team_translations_team_id_locale_unique` (`team_id`,`locale`),
  ADD KEY `team_translations_locale_index` (`locale`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`),
  ADD KEY `units_building_id_foreign` (`building_id`);

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
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `vacations`
--
ALTER TABLE `vacations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vacations_user_id_foreign` (`user_id`),
  ADD KEY `vacations_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `vacation_translations`
--
ALTER TABLE `vacation_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vacation_translations_vacation_id_locale_unique` (`vacation_id`,`locale`),
  ADD KEY `vacation_translations_locale_index` (`locale`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `abouts`
--
ALTER TABLE `abouts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `about_translations`
--
ALTER TABLE `about_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `absences`
--
ALTER TABLE `absences`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `absence_translations`
--
ALTER TABLE `absence_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `acc_accounting_entities`
--
ALTER TABLE `acc_accounting_entities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `acc_accounting_settings`
--
ALTER TABLE `acc_accounting_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `acc_accounts`
--
ALTER TABLE `acc_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `acc_financial_centers`
--
ALTER TABLE `acc_financial_centers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `acc_fiscal_periods`
--
ALTER TABLE `acc_fiscal_periods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `acc_fiscal_years`
--
ALTER TABLE `acc_fiscal_years`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `acc_journal_entries`
--
ALTER TABLE `acc_journal_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `acc_journal_entry_lines`
--
ALTER TABLE `acc_journal_entry_lines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `acc_payment_vouchers`
--
ALTER TABLE `acc_payment_vouchers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `acc_receipt_vouchers`
--
ALTER TABLE `acc_receipt_vouchers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `application_translations`
--
ALTER TABLE `application_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attachmentables`
--
ALTER TABLE `attachmentables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `auto_equipment`
--
ALTER TABLE `auto_equipment`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `auto_equipment_translations`
--
ALTER TABLE `auto_equipment_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- AUTO_INCREMENT for table `bond_types`
--
ALTER TABLE `bond_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bond_type_translations`
--
ALTER TABLE `bond_type_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `buildings`
--
ALTER TABLE `buildings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `company_licenses`
--
ALTER TABLE `company_licenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `company_license_translations`
--
ALTER TABLE `company_license_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `company_translations`
--
ALTER TABLE `company_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `company_types`
--
ALTER TABLE `company_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `company_type_translations`
--
ALTER TABLE `company_type_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `contact_us`
--
ALTER TABLE `contact_us`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `contract_translations`
--
ALTER TABLE `contract_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `faq_translations`
--
ALTER TABLE `faq_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `financial_bonds`
--
ALTER TABLE `financial_bonds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `financial_bond_translations`
--
ALTER TABLE `financial_bond_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `galleries`
--
ALTER TABLE `galleries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gallery_translations`
--
ALTER TABLE `gallery_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `imageables`
--
ALTER TABLE `imageables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `notification_logs`
--
ALTER TABLE `notification_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_translations`
--
ALTER TABLE `notification_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `project_translations`
--
ALTER TABLE `project_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `rewards`
--
ALTER TABLE `rewards`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reward_translations`
--
ALTER TABLE `reward_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `service_translations`
--
ALTER TABLE `service_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `setting_translations`
--
ALTER TABLE `setting_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT for table `sliders`
--
ALTER TABLE `sliders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `slider_translations`
--
ALTER TABLE `slider_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `team_translations`
--
ALTER TABLE `team_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
-- AUTO_INCREMENT for table `vacations`
--
ALTER TABLE `vacations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vacation_translations`
--
ALTER TABLE `vacation_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `about_translations`
--
ALTER TABLE `about_translations`
  ADD CONSTRAINT `about_translations_about_id_foreign` FOREIGN KEY (`about_id`) REFERENCES `abouts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `absences`
--
ALTER TABLE `absences`
  ADD CONSTRAINT `absences_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `absences_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `absence_translations`
--
ALTER TABLE `absence_translations`
  ADD CONSTRAINT `absence_translations_absence_id_foreign` FOREIGN KEY (`absence_id`) REFERENCES `absences` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `acc_accounting_settings`
--
ALTER TABLE `acc_accounting_settings`
  ADD CONSTRAINT `acc_set_bank_acc_fk` FOREIGN KEY (`default_bank_account_id`) REFERENCES `acc_accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acc_set_cash_acc_fk` FOREIGN KEY (`default_cash_account_id`) REFERENCES `acc_accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acc_set_close_by_fk` FOREIGN KEY (`last_closing_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acc_set_entity_fk` FOREIGN KEY (`accounting_entity_id`) REFERENCES `acc_accounting_entities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `acc_set_fp_fk` FOREIGN KEY (`default_fiscal_period_id`) REFERENCES `acc_fiscal_periods` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acc_set_fy_fk` FOREIGN KEY (`default_fiscal_year_id`) REFERENCES `acc_fiscal_years` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acc_set_pay_acc_fk` FOREIGN KEY (`default_payables_account_id`) REFERENCES `acc_accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acc_set_recv_acc_fk` FOREIGN KEY (`default_receivables_account_id`) REFERENCES `acc_accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acc_set_ret_earn_acc_fk` FOREIGN KEY (`default_retained_earnings_account_id`) REFERENCES `acc_accounts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `acc_accounts`
--
ALTER TABLE `acc_accounts`
  ADD CONSTRAINT `acc_accounts_accounting_entity_id_foreign` FOREIGN KEY (`accounting_entity_id`) REFERENCES `acc_accounting_entities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `acc_accounts_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `acc_accounts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `acc_financial_centers`
--
ALTER TABLE `acc_financial_centers`
  ADD CONSTRAINT `acc_financial_centers_accounting_entity_id_foreign` FOREIGN KEY (`accounting_entity_id`) REFERENCES `acc_accounting_entities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `acc_financial_centers_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `acc_financial_centers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `acc_fiscal_periods`
--
ALTER TABLE `acc_fiscal_periods`
  ADD CONSTRAINT `acc_fiscal_periods_fiscal_year_id_foreign` FOREIGN KEY (`fiscal_year_id`) REFERENCES `acc_fiscal_years` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `acc_fiscal_years`
--
ALTER TABLE `acc_fiscal_years`
  ADD CONSTRAINT `acc_fiscal_years_accounting_entity_id_foreign` FOREIGN KEY (`accounting_entity_id`) REFERENCES `acc_accounting_entities` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `acc_journal_entries`
--
ALTER TABLE `acc_journal_entries`
  ADD CONSTRAINT `acc_journal_entries_accounting_entity_id_foreign` FOREIGN KEY (`accounting_entity_id`) REFERENCES `acc_accounting_entities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `acc_journal_entries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acc_journal_entries_financial_center_id_foreign` FOREIGN KEY (`financial_center_id`) REFERENCES `acc_financial_centers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acc_journal_entries_fiscal_period_id_foreign` FOREIGN KEY (`fiscal_period_id`) REFERENCES `acc_fiscal_periods` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acc_journal_entries_fiscal_year_id_foreign` FOREIGN KEY (`fiscal_year_id`) REFERENCES `acc_fiscal_years` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acc_journal_entries_posted_by_foreign` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `acc_journal_entry_lines`
--
ALTER TABLE `acc_journal_entry_lines`
  ADD CONSTRAINT `acc_journal_entry_lines_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `acc_accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `acc_journal_entry_lines_financial_center_id_foreign` FOREIGN KEY (`financial_center_id`) REFERENCES `acc_financial_centers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acc_journal_entry_lines_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `acc_journal_entries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `acc_payment_vouchers`
--
ALTER TABLE `acc_payment_vouchers`
  ADD CONSTRAINT `acc_payment_vouchers_accounting_entity_id_foreign` FOREIGN KEY (`accounting_entity_id`) REFERENCES `acc_accounting_entities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `acc_payment_vouchers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acc_payment_vouchers_financial_center_id_foreign` FOREIGN KEY (`financial_center_id`) REFERENCES `acc_financial_centers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acc_payment_vouchers_fiscal_period_id_foreign` FOREIGN KEY (`fiscal_period_id`) REFERENCES `acc_fiscal_periods` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acc_payment_vouchers_fiscal_year_id_foreign` FOREIGN KEY (`fiscal_year_id`) REFERENCES `acc_fiscal_years` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acc_payment_vouchers_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `acc_journal_entries` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acc_payment_vouchers_offset_account_id_foreign` FOREIGN KEY (`offset_account_id`) REFERENCES `acc_accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `acc_payment_vouchers_payment_account_id_foreign` FOREIGN KEY (`payment_account_id`) REFERENCES `acc_accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `acc_payment_vouchers_posted_by_foreign` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `acc_receipt_vouchers`
--
ALTER TABLE `acc_receipt_vouchers`
  ADD CONSTRAINT `acc_receipt_vouchers_accounting_entity_id_foreign` FOREIGN KEY (`accounting_entity_id`) REFERENCES `acc_accounting_entities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `acc_receipt_vouchers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acc_receipt_vouchers_financial_center_id_foreign` FOREIGN KEY (`financial_center_id`) REFERENCES `acc_financial_centers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acc_receipt_vouchers_fiscal_period_id_foreign` FOREIGN KEY (`fiscal_period_id`) REFERENCES `acc_fiscal_periods` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acc_receipt_vouchers_fiscal_year_id_foreign` FOREIGN KEY (`fiscal_year_id`) REFERENCES `acc_fiscal_years` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acc_receipt_vouchers_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `acc_journal_entries` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acc_receipt_vouchers_offset_account_id_foreign` FOREIGN KEY (`offset_account_id`) REFERENCES `acc_accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `acc_receipt_vouchers_posted_by_foreign` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acc_receipt_vouchers_receipt_account_id_foreign` FOREIGN KEY (`receipt_account_id`) REFERENCES `acc_accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `application_translations`
--
ALTER TABLE `application_translations`
  ADD CONSTRAINT `application_translations_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `auto_equipment`
--
ALTER TABLE `auto_equipment`
  ADD CONSTRAINT `auto_equipment_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `auto_equipment_translations`
--
ALTER TABLE `auto_equipment_translations`
  ADD CONSTRAINT `auto_equipment_translations_auto_equipment_id_foreign` FOREIGN KEY (`auto_equipment_id`) REFERENCES `auto_equipment` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bills`
--
ALTER TABLE `bills`
  ADD CONSTRAINT `bills_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bills_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bill_payment_dues`
--
ALTER TABLE `bill_payment_dues`
  ADD CONSTRAINT `bill_payment_dues_bill_id_foreign` FOREIGN KEY (`bill_id`) REFERENCES `bills` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bill_short_links`
--
ALTER TABLE `bill_short_links`
  ADD CONSTRAINT `bill_short_links_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bill_transactions`
--
ALTER TABLE `bill_transactions`
  ADD CONSTRAINT `bill_transactions_bill_id_foreign` FOREIGN KEY (`bill_id`) REFERENCES `bills` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bill_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `bond_types`
--
ALTER TABLE `bond_types`
  ADD CONSTRAINT `bond_types_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bond_type_translations`
--
ALTER TABLE `bond_type_translations`
  ADD CONSTRAINT `bond_type_translations_bond_type_id_foreign` FOREIGN KEY (`bond_type_id`) REFERENCES `bond_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `buildings`
--
ALTER TABLE `buildings`
  ADD CONSTRAINT `buildings_building_type_id_foreign` FOREIGN KEY (`building_type_id`) REFERENCES `building_types` (`id`);

--
-- Constraints for table `building_translations`
--
ALTER TABLE `building_translations`
  ADD CONSTRAINT `building_translations_building_id_foreign` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `building_types`
--
ALTER TABLE `building_types`
  ADD CONSTRAINT `building_types_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `building_type_translations`
--
ALTER TABLE `building_type_translations`
  ADD CONSTRAINT `building_type_translations_building_type_id_foreign` FOREIGN KEY (`building_type_id`) REFERENCES `building_types` (`id`);

--
-- Constraints for table `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `companies_company_type_id_foreign` FOREIGN KEY (`company_type_id`) REFERENCES `company_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `companies_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `company_licenses`
--
ALTER TABLE `company_licenses`
  ADD CONSTRAINT `company_licenses_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `company_licenses_license_id_foreign` FOREIGN KEY (`license_id`) REFERENCES `licenses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `company_licenses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `contracts_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contracts_contract_type_id_foreign` FOREIGN KEY (`contract_type_id`) REFERENCES `contract_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contracts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contract_translations`
--
ALTER TABLE `contract_translations`
  ADD CONSTRAINT `contract_translations_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contract_types`
--
ALTER TABLE `contract_types`
  ADD CONSTRAINT `contract_types_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contract_type_translations`
--
ALTER TABLE `contract_type_translations`
  ADD CONSTRAINT `contract_type_translations_contract_type_id_foreign` FOREIGN KEY (`contract_type_id`) REFERENCES `contract_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contract_units`
--
ALTER TABLE `contract_units`
  ADD CONSTRAINT `contract_units_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`),
  ADD CONSTRAINT `contract_units_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`);

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `faqs`
--
ALTER TABLE `faqs`
  ADD CONSTRAINT `faqs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `faq_translations`
--
ALTER TABLE `faq_translations`
  ADD CONSTRAINT `faq_translations_faq_id_foreign` FOREIGN KEY (`faq_id`) REFERENCES `faqs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `financial_bonds`
--
ALTER TABLE `financial_bonds`
  ADD CONSTRAINT `financial_bonds_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `financial_bonds_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `financial_bonds_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `financial_bond_translations`
--
ALTER TABLE `financial_bond_translations`
  ADD CONSTRAINT `financial_bond_translations_financial_bond_id_foreign` FOREIGN KEY (`financial_bond_id`) REFERENCES `financial_bonds` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `galleries`
--
ALTER TABLE `galleries`
  ADD CONSTRAINT `galleries_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `gallery_translations`
--
ALTER TABLE `gallery_translations`
  ADD CONSTRAINT `gallery_translations_gallery_id_foreign` FOREIGN KEY (`gallery_id`) REFERENCES `galleries` (`id`);

--
-- Constraints for table `licenses`
--
ALTER TABLE `licenses`
  ADD CONSTRAINT `licenses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `notification_logs`
--
ALTER TABLE `notification_logs`
  ADD CONSTRAINT `notification_logs_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notification_logs_notification_id_foreign` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notification_translations`
--
ALTER TABLE `notification_translations`
  ADD CONSTRAINT `notification_translations_notification_id_foreign` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_translations`
--
ALTER TABLE `project_translations`
  ADD CONSTRAINT `project_translations_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rewards`
--
ALTER TABLE `rewards`
  ADD CONSTRAINT `rewards_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rewards_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reward_translations`
--
ALTER TABLE `reward_translations`
  ADD CONSTRAINT `reward_translations_reward_id_foreign` FOREIGN KEY (`reward_id`) REFERENCES `rewards` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_translations`
--
ALTER TABLE `service_translations`
  ADD CONSTRAINT `service_translations_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `settings`
--
ALTER TABLE `settings`
  ADD CONSTRAINT `settings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `setting_translations`
--
ALTER TABLE `setting_translations`
  ADD CONSTRAINT `setting_translations_setting_id_foreign` FOREIGN KEY (`setting_id`) REFERENCES `settings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sliders`
--
ALTER TABLE `sliders`
  ADD CONSTRAINT `sliders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `slider_translations`
--
ALTER TABLE `slider_translations`
  ADD CONSTRAINT `slider_translations_slider_id_foreign` FOREIGN KEY (`slider_id`) REFERENCES `sliders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `team_translations`
--
ALTER TABLE `team_translations`
  ADD CONSTRAINT `team_translations_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `units`
--
ALTER TABLE `units`
  ADD CONSTRAINT `units_building_id_foreign` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `unit_translations`
--
ALTER TABLE `unit_translations`
  ADD CONSTRAINT `unit_translations_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vacations`
--
ALTER TABLE `vacations`
  ADD CONSTRAINT `vacations_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vacations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vacation_translations`
--
ALTER TABLE `vacation_translations`
  ADD CONSTRAINT `vacation_translations_vacation_id_foreign` FOREIGN KEY (`vacation_id`) REFERENCES `vacations` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
