-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 14, 2025 at 03:33 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tattoo`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `artist_id` int(11) DEFAULT NULL,
  `service_type` varchar(50) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `notes` text DEFAULT NULL,
  `is_home_service` tinyint(1) DEFAULT 0,
  `address` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `user_id`, `artist_id`, `service_type`, `appointment_date`, `appointment_time`, `notes`, `is_home_service`, `address`, `price`, `status`, `created_at`) VALUES
(31, 37, 36, 'Custom Design', '2025-04-27', '15:00:00', 'tattoo karde ttt', 1, '001, shriya sharli apt, near school, belavali, badlapur (W)', 0.00, 'confirmed', '2025-04-19 19:53:34'),
(32, 37, NULL, 'Home Service', '2025-04-26', '16:00:00', 'tattoo kaede yaar mari', 1, '001, shriya sharli apt, near school, belvali, badlapur (W)', 0.00, '', '2025-04-19 20:40:17'),
(33, 37, 36, 'Home Service', '2025-04-26', '13:00:00', 'tatttoo karde', 1, '001, shriya shali, mumbai', 0.00, '', '2025-04-19 20:56:24'),
(34, 37, 36, 'Home Service', '2025-04-30', '16:00:00', 'kardu na... tattoo', 1, '001, shriya sharli apt, mumbai (w)', 0.00, 'confirmed', '2025-04-19 21:06:09'),
(35, 37, 36, 'Home Service', '2025-04-27', '16:00:00', 'I want an tattoo', 1, '001, shriya sharli apt, belavali, badalapur (w)', 0.00, 'confirmed', '2025-04-22 16:42:06'),
(36, 37, NULL, 'Home Service', '2025-04-27', '13:00:00', 'karde bhai tattoo', 1, '001, shriya sharli apt, belavali, badalapur (w)', 0.00, 'confirmed', '2025-04-22 17:17:45'),
(37, 37, 36, 'Home Service', '2025-04-27', '13:00:00', 'tattooo karde yaar', 1, '001, shriya sharli apt, belavali, badalapur (w)', 0.00, 'confirmed', '2025-04-22 17:20:31'),
(38, 37, 36, 'Home Service', '2025-05-03', '16:00:00', 'tattoo karde bhai', 1, '001, jk society, harbour, diva (w)', 0.00, 'confirmed', '2025-04-23 17:32:01'),
(39, 37, NULL, 'Home Service', '2025-04-27', '15:00:00', 'tattoo karde bhai', 1, '001, jjk society, near kk school, hk, diva (W)', 0.00, 'confirmed', '2025-04-23 18:22:48'),
(40, 37, 36, 'Home Service', '2025-05-02', '15:00:00', 'tattoo karde yaar', 1, 'Ground Floor, PHOENIX MARKETCITY, G - 65, Lal Bahadur Shastri Marg, Ashok Nagar, Kurla, Mumbai, Maharashtra 400070, India', 0.00, 'confirmed', '2025-04-23 18:45:57'),
(41, 37, 36, 'Home Service', '2025-06-14', '17:00:00', 'tatttttttttttooo', 1, 'Ground Floor, PHOENIX MARKETCITY, G - 65, Lal Bahadur Shastri Marg, Ashok Nagar, Kurla, Mumbai, Maharashtra 400070, India', 0.00, '', '2025-04-23 19:02:34'),
(42, 37, 36, 'Cover Up', '2025-04-26', '16:00:00', '', 0, '', 0.00, '', '2025-04-23 19:06:00'),
(43, 37, NULL, 'Traditional', '2025-04-27', '14:00:00', '', 0, '', 0.00, '', '2025-04-23 19:19:30'),
(44, 37, NULL, 'Traditional', '2025-04-25', '15:00:00', '', 0, '', 0.00, 'confirmed', '2025-04-23 19:25:55'),
(45, 38, 36, 'Home Service', '2025-04-30', '16:00:00', 'tattoo karde bhai', 1, 'Shankar Rao Lohane Marg, Kadolkar Colony, Mukund Nagar, Pune, Maharashtra 411037, India', 0.00, 'confirmed', '2025-04-24 17:07:46'),
(46, 38, NULL, 'Geometric', '2025-05-11', '16:00:00', '', 0, '', 0.00, '', '2025-04-24 17:08:01'),
(47, 37, NULL, 'Home Service', '2025-05-03', '17:00:00', 'tattoo karde', 1, '001, m8mbai', 0.00, 'confirmed', '2025-04-25 15:30:59'),
(48, 38, 36, 'Home Service', '2025-04-27', '17:00:00', 'Tattoo kar dijeye', 1, '001, jjk society, near LK school, diva (W)', 0.00, 'confirmed', '2025-04-25 17:28:58'),
(49, 40, 36, 'Custom Design', '2025-05-13', '11:00:00', 'i need a tattoo', 0, '', 0.00, 'confirmed', '2025-05-04 10:45:32'),
(50, 41, 36, 'Home Service', '2025-05-06', '12:00:00', 'i need tattoo', 1, 'Juhu Tara Rd, opposite JW Marriott, Juhu Tara, Juhu, Mumbai, Maharashtra 400049, India', 0.00, 'confirmed', '2025-05-04 15:18:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('client','artist','admin') NOT NULL DEFAULT 'client',
  `specialization` varchar(100) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `specialization`, `image_path`, `profile_image`, `created_at`) VALUES
(34, 'decepticon', 'admin@example.com', '$2y$10$3qpg7ur5fC/pcK4uWnNbZu4bhJq7yb62lcB/6kPKMy3zki.er26qa', 'admin', NULL, NULL, NULL, '2025-04-18 17:29:44'),
(36, 'Tulsidas Khan', 'tul@gmail.com', '$2y$10$0Md8B/UT0.A8EM.H3h0FieKMO5t7xfvpilLq29KpcIRGBeZFHEW0S', 'artist', 'Modern', 'uploads/artists/artist_68028d42978d90.34286305.jpg', NULL, '2025-04-18 17:34:58'),
(37, 'q', 'q@gmail.com', '$2y$10$mAANSv.Rhbbanrz6.vBKV.TGGamBJfhK.ymtzDj5arU6w.uHMccOa', 'client', NULL, NULL, NULL, '2025-04-18 18:38:26'),
(38, 'p', 'p@gmail.com', '$2y$10$LQAJ6/9E5pEZ7ZDWwRuxIeyc3fyafFjhDJWwXuz.HT6ymDQgiLi32', 'client', NULL, NULL, NULL, '2025-04-24 17:06:29'),
(39, 'pp', 'op@gmail.com', '$2y$10$.tGA3Qn2MEn8tzV867MsGu427EGeojFrYercGIwzmXo/mcB8cCrHe', 'client', NULL, NULL, NULL, '2025-04-25 15:29:49'),
(40, 'sagark', 'kotasagar2005@gmail.com', '$2y$10$.e5QDeCGWF9wY777Bqx7aOR/wmd.WX2.F6UUpzpIsDUOvyiJP90cm', 'client', NULL, NULL, NULL, '2025-05-04 10:43:11'),
(41, 'ashwini', 'kotaashwini@gmail.com', '$2y$10$cGVE7zwTrjHWp.CqrGRkv.S3PB2/UlsuhX.D6BODatDGcdd5.7/3C', 'client', NULL, NULL, NULL, '2025-05-04 15:16:11'),
(43, 'Adarsh', 'Adarsh@gmail.com', '$2y$10$ca5o.fCRsNYcnSfxQtjCXOoUFqKSurIufNzZQS/kM3ABPFwwm3uky', 'artist', 'Tribal tattoo', 'uploads/artists/artist_6819e3608ed7b5.90285990.jpg', NULL, '2025-05-06 10:24:32'),
(44, 'Kiran', 'kiran@gmail.com', '$2y$10$9XKOa5MfAoUXLx5mV0S/VuJmgVitqf8sIqtmXaZZs2.074UEwCnLq', 'artist', 'Realistic Portraits', 'uploads/artists/artist_6819e4243100b7.38506429.jpeg', NULL, '2025-05-06 10:27:48'),
(45, 'rahul', 'rahulkonda@gmail.com', '$2y$10$JAblZUp5surxYdJHeqBczON.P9YQnE9Kb7zLFdoECpL3SzBdL75be', 'client', NULL, NULL, NULL, '2025-05-12 07:02:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `artist_id` (`artist_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`artist_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
