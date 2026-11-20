-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2026 at 02:44 PM
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
-- Database: `astromaze_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `game_records`
--

CREATE TABLE `game_records` (
  `game_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `score` int(11) NOT NULL DEFAULT 0,
  `orbs_collected` int(11) NOT NULL DEFAULT 0,
  `fuel_remaining` int(11) NOT NULL DEFAULT 0,
  `game_status` enum('won','lost') NOT NULL,
  `played_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `game_records`
--

INSERT INTO `game_records` (`game_id`, `user_id`, `score`, `orbs_collected`, `fuel_remaining`, `game_status`, `played_at`) VALUES
(1, 2, 0, 0, 0, 'lost', '2026-11-06 09:32:08'),
(2, 2, 100, 10, 60, 'won', '2026-11-06 09:34:31'),
(3, 2, 100, 10, 74, 'won', '2026-11-06 10:12:36'),
(18, 2, 100, 10, 36, 'won', '2026-11-16 01:34:11'),
(19, 2, 80, 8, 0, 'lost', '2026-11-16 01:35:03'),
(20, 2, 0, 0, 0, 'lost', '2026-11-20 13:43:24');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `role`, `created_at`, `is_active`, `profile_picture`) VALUES
(1, 'yanna', 's2400638@usls.edu.ph', '$2y$10$azKl87zmOZ1az3uNZljLJOcxlLvy5ybchVzCBVTZikg14Kuze9nOu', 'admin', '2026-11-05 06:05:02', 1, NULL),
(2, 'Test1', 's2400317@usls.edu.com', '$2y$10$U6xTygn2.QE0rpA65RT3DuMRVvBpM1M.aoxyxNtKZNrYXjQdfhSby', 'user', '2026-11-05 07:37:58', 1, 'uploads/profile_pictures/user_2_7510abf07a62ec26.png'),
(5, 'test2', '2402410@gmail.com', '$2y$10$OJbb.t18.bSxkiFptscGXeS/SKhgozCUZdW9VJjY3WTBlhfhTZiNa', 'user', '2026-11-20 11:05:45', 1, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `game_records`
--
ALTER TABLE `game_records`
  ADD PRIMARY KEY (`game_id`),
  ADD KEY `fk_game_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `game_records`
--
ALTER TABLE `game_records`
  MODIFY `game_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `game_records`
--
ALTER TABLE `game_records`
  ADD CONSTRAINT `fk_game_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
