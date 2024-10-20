-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 20, 2024 at 07:57 AM
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
-- Database: `bottlecycle-ctu`
--

-- --------------------------------------------------------

--
-- Table structure for table `bottle_counter`
--

CREATE TABLE `bottle_counter` (
  `id` int(6) UNSIGNED NOT NULL,
  `count` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `created_at`) VALUES
(2, 'gdurano.shs@gmail.com', '$2y$10$t8CWoamQDnnYNJWtVbyPROFg4eTG.an75K2lfE326rvJE/0u0UwKu', '2024-10-18 08:53:37'),
(3, 'NoeGwapo@gmail.com', '$2y$10$sQhKjeimfkB0bIPdmY/Jh.4bd8mO2rP.k2N2tR5QBzIZs32OFMN/2', '2024-10-18 09:10:47'),
(4, 'noe1@gmail.com', '$2y$10$VH.Shbsp/dLT02D55DI9/eUYq6BIEj1lZ6LrgdSB9Hp3BTr4fBzY.', '2024-10-18 09:33:01'),
(5, 'zajk@gmail.com', '$2y$10$9sl8UG4u7sCdS1zHYsm1u..hNgdYtMePQ0xse4aEvbBF5whilu9nq', '2024-10-18 10:56:11'),
(6, 'NoeJohnTheGreat@gmail.com', '$2y$10$WsjL5brMTnupqolMq93bzeDp.J2rYuTx0.OlfU/M7IVMcLTIE56cu', '2024-10-18 11:21:54'),
(7, 'noe11@gmail.com', '$2y$10$kNrFFVy723VwmTxdMFPG5uaRUOJmd2mhp.jg0yQoGzl211LP85k4u', '2024-10-18 11:23:18'),
(8, 'npe123@gmail.com', '$2y$10$4mCQUSOpDLvIFgoFupTa0.HkZEx8yjN.VcxPpcw2sxFgIp2wKBGXq', '2024-10-18 11:23:44'),
(9, 'noe121@gmail.com', '$2y$10$O29s.CxAulcI4NzYgW4w/.4bOP.aaZwkREh4FbFyWS/AlUzTCvP6K', '2024-10-18 11:24:23'),
(10, 'noe1211@gmail.com', '$2y$10$r/aD1MeI6aXdwoVTt3Z/k.sp12PALbReEZn/6SdgatT4ksWO1f/wm', '2024-10-18 11:24:42'),
(11, 'lawskie@gmail.com', '$2y$10$VGB6DlnZ0z4jHx6l/P0AX.u8FoXViZAG5CMzf4gRDZFDclW/RkB1y', '2024-10-18 11:26:09'),
(12, 'andoy@gmail.com', '$2y$10$6OGXlo75tEvlq3JZvlmHU..i7B/y/HBkMsuFOpYjF0.Mi/UoRvrSq', '2024-10-18 11:30:51'),
(13, 'nicolastesla@gmail.com', '$2y$10$O.LWwiIH0L6zBoIak5r3RO1ocMhAVglNQ7rIYAHH/w9hznp5Ev3W2', '2024-10-18 11:37:15'),
(14, 'sean@gmail.com', '$2y$10$DhQI2ZTyUIropmq/SJNCr.rNScjJjkDTShppFopTlIOGxwo2LEyMu', '2024-10-18 11:56:42'),
(15, 'noejohngwapo@gmail.com', '$2y$10$l1TtpD13MZQAjRoWSnzDBeUAtHxslRp3Owgvt6T0Bk3POTBi6ZI1q', '2024-10-18 12:29:07'),
(16, 'noe24@gmail.com', '$2y$10$cqlxjgoeaHlVaM2C4bqr.OnGgvFj2RHTtHNQD21nn3VZFR2SD4dbC', '2024-10-20 05:39:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bottle_counter`
--
ALTER TABLE `bottle_counter`
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
-- AUTO_INCREMENT for table `bottle_counter`
--
ALTER TABLE `bottle_counter`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
