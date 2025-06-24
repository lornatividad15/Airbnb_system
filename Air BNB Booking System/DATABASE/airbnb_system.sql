-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 24, 2025 at 05:39 PM
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
-- Database: `airbnb_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `admin_id` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `admin_id`, `username`, `password`, `created_at`) VALUES
(1, 'admin001', 'admin', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', '2025-06-23 10:38:04');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `condo_id` int(11) NOT NULL,
  `guest_count` int(11) NOT NULL,
  `checkin` datetime NOT NULL,
  `checkout` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('confirmed','pending_cancel','cancelled','cancel_rejected') NOT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `user_hidden` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `condos`
--

CREATE TABLE `condos` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `address_details` text DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `condos`
--

INSERT INTO `condos` (`id`, `name`, `image_path`, `description`, `city`, `address_details`, `is_available`) VALUES
(1, 'Auberge Condotels ', 'Auberge1.jpg', '\"LOCATION: SMDC Hope Residences, Trece Martires Cavite We offer ✔️Long Term ✔️Short Term ✔️Staycation Check in: 2:00pm Check out: 12:00 noon Unit Details: ✨1 studio unit ✨Air-conditioned Room, electric fan ✨Fire extinguisher, smoke detector ✨unlimited WiFi, Google TV , Youtube, Netflix, HBO GO, Disney Plus, Apple TV ✨Board and card Games ✨2 Queen Size Bed ✨Refrigerator and water dispenser, Mineral Water ✨Rice Cooker, Microwave, Electric Kettle, Induction Cooker, Coffee maker, kitchen utensils ✨Towel, tissue, soap & Disposable toothbrush, hair dryer, bidet, water heater ✨Closet, Washing machine, portable iron and board, hangers, air dehumidifier 🌺Swimming pool rate🌺: 150 regular day 300 holiday\"', 'Trece Martires', 'Auberge Condotels at SMDC Hope Residences, Juanito R. Remulla Senior Rd,', 1);

-- --------------------------------------------------------

--
-- Table structure for table `condo_images`
--

CREATE TABLE `condo_images` (
  `id` int(11) NOT NULL,
  `condo_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `condo_images`
--

INSERT INTO `condo_images` (`id`, `condo_id`, `image_path`, `uploaded_at`) VALUES
(1, 1, 'Auberge1.jpg', '2025-06-24 14:00:54'),
(2, 1, 'Auberge2.jpg', '2025-06-24 14:00:54'),
(3, 1, 'Auberge3.jpg', '2025-06-24 14:00:54'),
(4, 1, 'Auberge4.jpg', '2025-06-24 14:00:54'),
(5, 1, 'Auberge5.jpg', '2025-06-24 14:00:54');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `birthdate` date NOT NULL,
  `sex` enum('Male','Female','Other','') NOT NULL,
  `age` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_picture` longblob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_adminid` (`admin_id`),
  ADD UNIQUE KEY `unique_username` (`username`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `condo_id` (`condo_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `condos`
--
ALTER TABLE `condos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `condo_images`
--
ALTER TABLE `condo_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `condo_id` (`condo_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `condos`
--
ALTER TABLE `condos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `condo_images`
--
ALTER TABLE `condo_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`condo_id`) REFERENCES `condos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `condo_images`
--
ALTER TABLE `condo_images`
  ADD CONSTRAINT `condo_images_ibfk_1` FOREIGN KEY (`condo_id`) REFERENCES `condos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
