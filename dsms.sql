-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 27, 2026 at 10:34 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dsms`
--

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `id` int NOT NULL,
  `firstname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `lastname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `age` int NOT NULL,
  `position` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Year` varchar(50) NOT NULL,
  `contact` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `address` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `hired_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`id`, `firstname`, `lastname`, `age`, `position`, `Year`, `contact`, `email`, `address`, `hired_at`) VALUES
(78, 'Jhun Daverey', 'Parol', 26, 'BSIT', '2nd Year', '09518069771', 'jhundaverey@gmail.com', 'BLK 1 B LOT 7 SPRING VALLEY BUHANGIN DAVAO CITY', '2026-04-07 16:00:00'),
(79, 'Isha', 'Dicampong', 20, 'BSIT', '2nd Year', '091234581123', 'isha@gmail.com', 'Panacan', '2026-04-07 16:00:00'),
(80, 'Revhine', 'Parol', 20, 'PSYCHOLOGY', '3rd Year', '09518069771', 'revhine@gmail.com', 'blk 1 b lot 7 spring valley buhangin davao city', '2026-03-31 16:00:00'),
(81, 'Swaki', 'Tuyom', 24, 'BSBA', '4rth Year', '09123456789', 'swaki@gmailc.om', 'Pancan, bunawan', '2026-03-29 16:00:00'),
(84, 'Keiko', 'Keiko', 20, 'BSIT', '2nd Year', '09123456789', 'keiko@gmail.com', 'Panacan Bunawan malagamot', '2026-03-31 16:00:00'),
(86, 'Jhun Daverey', 'Parol', 26, 'BSIT', '2nd Year', '09123456789', 'jhundaverey@gmail.com', 'Blk 1-b lot 7 Marigold street, Spring Valley, Buhangin Davao City, Philippines', '2026-04-13 16:00:00'),
(87, 'BabyDesiree', 'Parol', 19, 'PSYCHOLOGY', '2nd Year', '09172758123', 'desiree@gmail.com', 'BLK 1 B LOT 7 SPRING VALLEY BUHANGIN DAVAO CITY', '2026-04-07 16:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `access_level` varchar(100) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `access_level`) VALUES
(25, 'user', 'user', 'user1'),
(27, 'admin', 'admin', 'admin1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
