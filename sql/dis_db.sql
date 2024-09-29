-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 29, 2024 at 02:57 PM
-- Server version: 8.0.31
-- PHP Version: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dis_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_details`
--

DROP TABLE IF EXISTS `contact_details`;
CREATE TABLE IF NOT EXISTS `contact_details` (
  `contact_id` int NOT NULL AUTO_INCREMENT,
  `mobile_number` varchar(11) NOT NULL,
  `email` varchar(191) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`contact_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `contact_details`
--

INSERT INTO `contact_details` (`contact_id`, `mobile_number`, `email`, `email_verified_at`) VALUES
(1, '09754523622', 'admin@gmail.com', '0000-00-00 00:00:00'),
(3, '09123456789', 'preyl@gmail.com', '0000-00-00 00:00:00'),
(4, '09123456790', 'besh@gmail.com', '0000-00-00 00:00:00'),
(5, '09656523124', 'asjdha@gmail.com', '0000-00-00 00:00:00'),
(15, '09264003188', 'carilloaira@gmail.com', '2024-09-29 06:53:29');

-- --------------------------------------------------------

--
-- Table structure for table `credentials`
--

DROP TABLE IF EXISTS `credentials`;
CREATE TABLE IF NOT EXISTS `credentials` (
  `credential_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(191) NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `role` varchar(191) NOT NULL,
  PRIMARY KEY (`credential_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `credentials`
--

INSERT INTO `credentials` (`credential_id`, `username`, `password`, `role`) VALUES
(1, 'admin', '$2y$10$sx9nssUhIv//v2.59L6AhuCuJ2iweHbqAkSBhclFiFzfoAc7gFOnW', 'Administrator'),
(3, 'preyl', '$2y$10$DN2SzdIwTNGQtUleY2vuiuTvhshDQmDzNN5Ev.6L7YOvAqwlpmf8q', 'Inventory Manager'),
(4, 'besh', '$2y$10$wLujTIiR1G0osttxG/IWmu1oz.nSL1PN7DymUCBM5Sq81EUGsyIvy', 'Inventory Manager'),
(5, 'yi', '$2y$10$mhYgoMrkghBavM8FKY0aJ.rJDH0gKPFx0yYgwFMICXYMWFaCw3Ihi', 'Auditor'),
(15, 'aira', '$2y$10$IoI.YdcS/6wFbSLxmkn8/OacheYQWYLeu7Z.OC37o3EVS0klh7hgq', 'Auditor');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `image_url` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `contact_id` int NOT NULL,
  `credential_id` int NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `contact_idFK` (`contact_id`),
  KEY `credential_idFK` (`credential_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `first_name`, `last_name`, `image_url`, `contact_id`, `credential_id`, `created_at`, `updated_at`) VALUES
(1, 'Owner', 'Dis', '', 1, 1, '2024-09-29 11:42:38', '2024-09-29 11:42:38'),
(3, 'Preyl', 'Carillo', 'WAGON_1727612528.png', 3, 3, '2024-09-29 04:22:09', '2024-09-29 04:22:09'),
(4, 'Besh', 'Craillo', '1000001006_1727612731.png', 4, 4, '2024-09-29 04:25:31', '2024-09-29 04:25:31'),
(5, 'Yi', 'Er', 'IMG_8967 (2)_1727615202.jpg', 5, 5, '2024-09-29 05:06:44', '2024-09-29 05:06:44'),
(15, 'Aira', 'Carillo', 'WAGON_1727621278.png', 15, 15, '2024-09-29 06:47:59', '2024-09-29 06:47:59');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `contact_idFK` FOREIGN KEY (`contact_id`) REFERENCES `contact_details` (`contact_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `credential_idFK` FOREIGN KEY (`credential_id`) REFERENCES `credentials` (`credential_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
