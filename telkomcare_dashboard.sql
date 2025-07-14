-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 04, 2025 at 02:49 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `telkomcare_dashboard`
--

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `region_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `region_id`, `name`, `created_at`, `updated_at`) VALUES
(1, 1, 'Banda Aceh', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(2, 1, 'Lhokseumawe', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(3, 1, 'Medan', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(4, 1, 'Pematangsiantar', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(5, 1, 'Padang', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(6, 1, 'Bukittinggi', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(7, 1, 'Jambi', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(8, 1, 'Pekanbaru', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(9, 1, 'Dumai', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(10, 1, 'Batam', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(11, 1, 'Tanjung Pinang', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(12, 1, 'Palembang', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(13, 1, 'Lubuklinggau', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(14, 1, 'Bengkulu', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(15, 1, 'Pangkal Pinang', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(16, 1, 'Bandar Lampung', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(17, 2, 'Jakarta Pusat', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(18, 2, 'Jakarta Utara', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(19, 2, 'Jakarta Selatan', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(20, 2, 'Jakarta Barat', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(21, 2, 'Jakarta Timur', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(22, 2, 'Bekasi', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(23, 2, 'Depok', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(24, 2, 'Bogor', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(25, 2, 'Tangerang', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(26, 2, 'Tangerang Selatan', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(27, 2, 'Serang', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(28, 2, 'Cilegon', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(29, 3, 'Bandung', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(30, 3, 'Cimahi', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(31, 3, 'Sumedang', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(32, 3, 'Subang', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(33, 3, 'Sukabumi', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(34, 3, 'Cianjur', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(35, 3, 'Garut', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(36, 3, 'Tasikmalaya', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(37, 3, 'Cirebon', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(38, 3, 'Indramayu', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(39, 3, 'Karawang', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(40, 3, 'Purwakarta', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(41, 3, 'Majalengka', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(42, 3, 'Banjar', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(43, 4, 'Semarang', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(44, 4, 'Salatiga', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(45, 4, 'Surakarta (Solo)', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(46, 4, 'Yogyakarta', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(47, 4, 'Magelang', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(48, 4, 'Purwokerto', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(49, 4, 'Tegal', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(50, 4, 'Pekalongan', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(51, 4, 'Klaten', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(52, 4, 'Kudus', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(53, 4, 'Jepara', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(54, 4, 'Cilacap', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(55, 4, 'Banyumas', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(56, 4, 'Kebumen', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(57, 5, 'Surabaya', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(58, 5, 'Sidoarjo', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(59, 5, 'Malang', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(60, 5, 'Batu', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(61, 5, 'Pasuruan', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(62, 5, 'Probolinggo', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(63, 5, 'Jember', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(64, 5, 'Banyuwangi', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(65, 5, 'Madiun', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(66, 5, 'Kediri', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(67, 5, 'Blitar', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(68, 5, 'Denpasar', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(69, 5, 'Singaraja', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(70, 5, 'Mataram (NTB)', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(71, 5, 'Bima (NTB)', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(72, 5, 'Kupang (NTT)', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(73, 5, 'Maumere (NTT)', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(74, 6, 'Pontianak (Kalbar)', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(75, 6, 'Singkawang', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(76, 6, 'Palangkaraya (Kalteng)', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(77, 6, 'Banjarmasin (Kalsel)', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(78, 6, 'Banjarbaru', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(79, 6, 'Samarinda (Kaltim)', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(80, 6, 'Balikpapan', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(81, 6, 'Tarakan (Kaltara)', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(82, 7, 'Makassar', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(83, 7, 'Parepare', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(84, 7, 'Palu', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(85, 7, 'Kendari', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(86, 7, 'Gorontalo', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(87, 7, 'Manado', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(88, 7, 'Ambon', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(89, 7, 'Ternate', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(90, 7, 'Jayapura', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(91, 7, 'Sorong', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(92, 7, 'Timika', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(93, 7, 'Merauke', '2025-07-04 04:07:40', '2025-07-04 04:07:40');

-- --------------------------------------------------------

--
-- Table structure for table `dashboard_data`
--

CREATE TABLE `dashboard_data` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `city_id` bigint(20) UNSIGNED NOT NULL,
  `category` enum('K1','K2','K3') NOT NULL,
  `entry_date` date NOT NULL,
  `sid` int(11) NOT NULL DEFAULT 0,
  `comply` int(11) NOT NULL DEFAULT 0,
  `not_comply` int(11) NOT NULL DEFAULT 0,
  `total` int(11) NOT NULL DEFAULT 0,
  `target` double NOT NULL DEFAULT 0,
  `ttr_comply` int(11) NOT NULL DEFAULT 0,
  `achievement` double NOT NULL DEFAULT 0,
  `ticket_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dashboard_data`
--

INSERT INTO `dashboard_data` (`id`, `city_id`, `category`, `entry_date`, `sid`, `comply`, `not_comply`, `total`, `target`, `ttr_comply`, `achievement`, `ticket_count`, `created_at`, `updated_at`) VALUES
(1, 1, 'K1', '2025-07-04', 56, 59, 14, 0, 95, 13338, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(2, 1, 'K1', '2025-07-03', 33, 72, 11, 0, 92.5, 10552, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(3, 1, 'K2', '2025-07-04', 68, 58, 10, 0, 95, 12992, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(4, 1, 'K2', '2025-07-03', 41, 35, 6, 0, 92.5, 9223, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(5, 1, 'K3', '2025-07-04', 110, 95, 13, 0, 95, 14636, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(6, 1, 'K3', '2025-07-03', 79, 53, 3, 0, 92.5, 8238, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(7, 2, 'K1', '2025-07-04', 62, 174, 11, 0, 95, 9114, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(8, 2, 'K1', '2025-07-03', 64, 83, 10, 0, 92.5, 11423, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(9, 2, 'K2', '2025-07-04', 101, 130, 18, 0, 95, 9835, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(10, 2, 'K2', '2025-07-03', 140, 67, 11, 0, 92.5, 9292, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(11, 2, 'K3', '2025-07-04', 61, 141, 13, 0, 95, 13049, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(12, 2, 'K3', '2025-07-03', 103, 63, 13, 0, 92.5, 9722, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(13, 3, 'K1', '2025-07-04', 194, 178, 9, 0, 95, 14404, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(14, 3, 'K1', '2025-07-03', 41, 108, 3, 0, 92.5, 9987, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(15, 3, 'K2', '2025-07-04', 109, 80, 13, 0, 95, 13218, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(16, 3, 'K2', '2025-07-03', 133, 102, 14, 0, 92.5, 8131, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(17, 3, 'K3', '2025-07-04', 103, 77, 20, 0, 95, 12912, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(18, 3, 'K3', '2025-07-03', 86, 34, 8, 0, 92.5, 9336, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(19, 4, 'K1', '2025-07-04', 97, 168, 12, 0, 95, 11572, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(20, 4, 'K1', '2025-07-03', 95, 25, 12, 0, 92.5, 11430, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(21, 4, 'K2', '2025-07-04', 70, 180, 5, 0, 95, 11278, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(22, 4, 'K2', '2025-07-03', 148, 119, 3, 0, 92.5, 7421, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(23, 4, 'K3', '2025-07-04', 52, 109, 8, 0, 95, 13565, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(24, 4, 'K3', '2025-07-03', 108, 36, 11, 0, 92.5, 11377, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(25, 5, 'K1', '2025-07-04', 143, 152, 10, 0, 95, 12148, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(26, 5, 'K1', '2025-07-03', 120, 106, 9, 0, 92.5, 9615, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(27, 5, 'K2', '2025-07-04', 51, 60, 14, 0, 95, 12220, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(28, 5, 'K2', '2025-07-03', 147, 43, 15, 0, 92.5, 7186, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(29, 5, 'K3', '2025-07-04', 145, 146, 20, 0, 95, 12596, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(30, 5, 'K3', '2025-07-03', 41, 41, 8, 0, 92.5, 9367, 0, 1, '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(31, 6, 'K1', '2025-07-04', 1, 2, 3, 5, 1, 2, 40, 1, '2025-07-04 04:08:44', '2025-07-04 04:08:44'),
(32, 17, 'K1', '2025-07-04', 2, 2, 2, 4, 2, 2, 50, 1, '2025-07-04 05:30:25', '2025-07-04 05:30:25'),
(33, 23, 'K2', '2025-07-04', 141, 141, 141, 282, 22, 141, 50, 1, '2025-07-04 05:30:59', '2025-07-04 05:30:59'),
(34, 29, 'K3', '2025-07-04', 11, 11, 11, 22, 11, 11, 50, 1, '2025-07-04 05:31:35', '2025-07-04 05:31:35');

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
(5, '2025_07_04_105927_create_regions_table', 1),
(6, '2025_07_04_105945_create_cities_table', 1),
(7, '2025_07_04_110006_create_dashboard_data_table', 1),
(8, '2025_07_04_110032_create_sessions_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `regions`
--

CREATE TABLE `regions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `regions`
--

INSERT INTO `regions` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'TREG 1', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(2, 'TREG 2', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(3, 'TREG 3', '2025-07-04 04:07:39', '2025-07-04 04:07:39'),
(4, 'TREG 4', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(5, 'TREG 5', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(6, 'TREG 6', '2025-07-04 04:07:40', '2025-07-04 04:07:40'),
(7, 'TREG 7', '2025-07-04 04:07:40', '2025-07-04 04:07:40');

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
('TEiWeVOgrWx2epf0ped2WVNc0KoQF0ceefZ0Sr8o', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUXpnWVI4ZGphbkFTY1c2ZGxtdUllekg0bUYwdXVaUG9VSE11TU8zNCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hcGkvZGFzaGJvYXJkLWRhdGEiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1751633163);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cities_name_unique` (`name`),
  ADD KEY `cities_region_id_foreign` (`region_id`);

--
-- Indexes for table `dashboard_data`
--
ALTER TABLE `dashboard_data`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_dashboard_entry` (`city_id`,`category`,`entry_date`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `regions`
--
ALTER TABLE `regions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `regions_name_unique` (`name`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `dashboard_data`
--
ALTER TABLE `dashboard_data`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `regions`
--
ALTER TABLE `regions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `cities_region_id_foreign` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dashboard_data`
--
ALTER TABLE `dashboard_data`
  ADD CONSTRAINT `dashboard_data_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
