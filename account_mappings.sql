-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 08, 2026 at 03:28 PM
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
-- Database: `rms`
--

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

--
-- Dumping data for table `account_mappings`
--

INSERT INTO `account_mappings` (`id`, `entity_id`, `operation_key`, `debit_account_id`, `credit_account_id`, `fee_account_id`, `journal_id`, `auto_post`, `is_active`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'bill_issuance', 9, 35, NULL, 1, 1, 1, 'قيد استحقاق الفاتورة التلقائي (مديونية المستأجر والإيراد)', '2026-11-08 12:27:18', '2026-11-08 12:27:18'),
(2, 1, 'online_payment', 6, 9, 59, 5, 1, 1, 'سداد الفواتير إلكترونياً عبر بوابة الدفع مع خصم عمولة البوابة', '2026-11-08 12:27:18', '2026-11-08 12:27:18'),
(3, 1, 'cash_payment', 4, 9, NULL, 6, 1, 1, 'تحصيل الفاتورة نقداً في الخزينة الرئيسية', '2026-11-08 12:27:18', '2026-11-08 12:27:18'),
(4, 1, 'receipt_voucher', 4, 9, NULL, 6, 1, 1, 'سند قبض نقدي من العملاء والمستأجرين', '2026-11-08 12:27:18', '2026-11-08 12:27:18'),
(5, 1, 'payment_voucher', 46, 4, NULL, 6, 1, 1, 'سند صرف مصروفات نقدية وصيانة', '2026-11-08 12:27:18', '2026-11-08 12:27:18'),
(6, 1, 'contract_deposit', 4, 25, NULL, 6, 1, 1, 'استلام تأمين إيجار مسترد وتثبيته كالتزام لصالح المستأجر', '2026-11-08 12:27:18', '2026-11-08 12:27:18'),
(7, 1, 'license_renewal', 53, 5, NULL, 3, 1, 1, 'سداد وتجديد رسوم وتراخيص حكومية عبر البنك', '2026-11-08 12:27:18', '2026-11-08 12:27:18');

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account_mappings`
--
ALTER TABLE `account_mappings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `account_mappings`
--
ALTER TABLE `account_mappings`
  ADD CONSTRAINT `account_mappings_credit_account_id_foreign` FOREIGN KEY (`credit_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `account_mappings_debit_account_id_foreign` FOREIGN KEY (`debit_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `account_mappings_entity_id_foreign` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `account_mappings_fee_account_id_foreign` FOREIGN KEY (`fee_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `account_mappings_journal_id_foreign` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
