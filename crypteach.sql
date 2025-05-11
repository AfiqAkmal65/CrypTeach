-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 11, 2025 at 06:52 PM
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
-- Database: `crypteach`
--

-- --------------------------------------------------------

--
-- Table structure for table `chapters`
--

CREATE TABLE `chapters` (
  `id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `content_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chapters`
--

INSERT INTO `chapters` (`id`, `title`, `description`, `video_url`, `created_at`, `content_file`) VALUES
(1, 'Introduction to Cryptography', 'This chapter covers the basics of cryptographic principles.', NULL, '2025-05-05 05:42:27', NULL),
(3, 'CHAPTER 2', 'HELLO WORLD', 'ddd', '2025-05-05 16:33:20', 'uploads/1746462800_CLW 5 exercise.docx'),
(4, 'Chapter 3', 'this is the only the begining', 'https://youtu.be/SPizIaBPhSg?si=TJSlop7ZbaGlEJK7', '2025-05-06 11:22:12', 'uploads/1746530532_Benefits of Distributed file system.docx'),
(5, 'Chapter 6: the beginning of crypto', 'hello it me adele', '', '2025-05-10 06:40:30', 'uploads/1746859230_Assignment LPCL 2024.pptx'),
(6, 'Chapter 7: Testing', 'i want to test the system', '', '2025-05-10 06:52:43', 'uploads/1746859963_BOP_SOP_Best_Evidence_Rule.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `chapter_files`
--

CREATE TABLE `chapter_files` (
  `id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `name`, `comment`, `created_at`) VALUES
(1, 'jimie', 'good', '2025-05-06 11:55:46'),
(2, 'obi', 'its all good', '2025-05-06 13:12:51'),
(3, 'azri', 'you botty', '2025-05-06 13:20:17');

-- --------------------------------------------------------

--
-- Table structure for table `progress`
--

CREATE TABLE `progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `completed` tinyint(1) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `correct_answer` varchar(255) NOT NULL,
  `option_a` varchar(255) DEFAULT NULL,
  `option_b` varchar(255) DEFAULT NULL,
  `option_c` varchar(255) DEFAULT NULL,
  `option_d` varchar(255) DEFAULT NULL,
  `correct_option` enum('a','b','c','d') NOT NULL DEFAULT 'a'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `chapter_id`, `question`, `correct_answer`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`) VALUES
(1, 1, 'What does encryption do?', '', 'It scrambles data to hide it', 'It deletes files', 'It compresses images', 'It slows down the CPU', 'a'),
(2, 1, 'Which algorithm is symmetric?', '', 'RSA', 'ECC', 'AES', 'Diffie-Hellman', 'c');

-- --------------------------------------------------------

--
-- Table structure for table `site_visits`
--

CREATE TABLE `site_visits` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `visit_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_visits`
--

INSERT INTO `site_visits` (`id`, `ip_address`, `visit_time`) VALUES
(1, '::1', '2025-05-09 22:52:20'),
(2, '::1', '2025-05-09 22:53:50'),
(3, '::1', '2025-05-09 22:57:14'),
(4, '::1', '2025-05-09 22:59:44'),
(5, '::1', '2025-05-09 23:03:03'),
(6, '::1', '2025-05-09 23:05:49'),
(7, '::1', '2025-05-09 23:08:11'),
(8, '::1', '2025-05-09 23:09:26'),
(9, '::1', '2025-05-09 23:09:29'),
(10, '::1', '2025-05-09 23:10:04'),
(11, '::1', '2025-05-09 23:11:06'),
(12, '::1', '2025-05-09 23:12:02'),
(13, '::1', '2025-05-09 23:12:03'),
(14, '::1', '2025-05-09 23:13:05'),
(15, '::1', '2025-05-09 23:13:06'),
(16, '::1', '2025-05-09 23:13:06'),
(17, '::1', '2025-05-09 23:13:06'),
(18, '::1', '2025-05-09 23:16:14'),
(19, '::1', '2025-05-09 23:24:27'),
(20, '::1', '2025-05-09 23:25:08'),
(21, '::1', '2025-05-09 23:25:26'),
(22, '::1', '2025-05-09 23:29:21'),
(23, '::1', '2025-05-09 23:31:16'),
(24, '::1', '2025-05-09 23:31:24'),
(25, '::1', '2025-05-09 23:31:27'),
(26, '::1', '2025-05-09 23:34:14'),
(27, '::1', '2025-05-09 23:35:17'),
(28, '::1', '2025-05-10 00:21:18'),
(29, '::1', '2025-05-10 00:27:16'),
(30, '::1', '2025-05-10 00:40:15'),
(31, '::1', '2025-05-10 10:51:29'),
(32, '::1', '2025-05-10 14:00:04'),
(33, '::1', '2025-05-10 15:31:39'),
(34, '::1', '2025-05-10 15:31:45'),
(35, '::1', '2025-05-10 15:38:52'),
(36, '::1', '2025-05-10 15:39:17'),
(37, '::1', '2025-05-10 15:44:46'),
(38, '::1', '2025-05-10 15:45:24'),
(39, '::1', '2025-05-10 21:17:51'),
(40, '::1', '2025-05-10 21:19:59'),
(41, '::1', '2025-05-10 21:21:43'),
(42, '::1', '2025-05-10 21:26:18'),
(43, '::1', '2025-05-10 21:36:04'),
(44, '::1', '2025-05-10 21:36:08'),
(45, '::1', '2025-05-10 23:05:57'),
(46, '::1', '2025-05-10 23:06:12'),
(47, '::1', '2025-05-10 23:19:17'),
(48, '::1', '2025-05-11 10:05:39'),
(49, '::1', '2025-05-11 10:06:21'),
(50, '::1', '2025-05-11 10:07:05'),
(51, '::1', '2025-05-11 10:10:42'),
(52, '::1', '2025-05-11 12:57:47'),
(53, '::1', '2025-05-11 13:23:16'),
(54, '::1', '2025-05-11 13:23:19'),
(55, '::1', '2025-05-11 23:37:28');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `score` int(11) DEFAULT 0,
  `role` enum('admin','student') NOT NULL DEFAULT 'student',
  `login_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `score`, `role`, `login_count`) VALUES
(6, 'admin', 'admin@crypteach.com', '$2y$10$FduPqnFYUDh2jkqiFQykJu7GOg1nMKxJp9uGDVXPZEWqY0BqsUPEy', '2025-05-05 05:20:08', 0, 'admin', 6),
(7, 'adam', 'adam@crypto.com', '$2y$10$OMoOIy6zJ.34yFqjYukmgewiSdT3X1axgT4gpFa8sQXW2wFE1Opny', '2025-05-05 06:05:14', 24, 'student', 10),
(8, 'Afiq Akmal', 'afiq@crypto.com', '$2y$10$R7VQYlpH9DJ9LmzFZALFS.sPw8k8J9KBXJ6zaMnY17WMRB1TmB4vy', '2025-05-06 10:48:59', 0, 'student', 4),
(9, 'Imani', 'imani@crypto.com', '$2y$10$K3R1KlG1rxoHjsQC6Uatle/9sx/7PHvkGFOA2Czv6APVBosQv0BMG', '2025-05-06 15:02:41', 0, 'admin', 0),
(11, 'Wasim', 'wasim@crypto.com', '$2y$10$7MxUbDLuFNnUa7xZPEczWOjD72RCfruNp/qm7yT/ZYANCkek8M5Ma', '2025-05-08 16:04:51', 2, 'student', 0),
(12, 'ali', 'ali@crypto.com', '$2y$10$IufFPoU/TTz7gemCCNP7HeYB8oZNsuBIeXw0siGVuYM3h2FZ.maoW', '2025-05-09 06:12:14', 0, 'student', 0),
(18, 'azri', 'itsazri47s@gmail.com', '$2y$10$vEp1MKTiJ3KbanDFn8g.yOviT4TwZ0FFADqv6G88wnUXdpY1BAKDi', '2025-05-09 07:58:52', 0, 'student', 0),
(19, 'Uwais', 'afiqcrypteach@gmail.com', '$2y$10$5POWBwINLphhuzoX3ecDB.UNfqTdEBvsfRk80vqYdyHUiN09LLKv.', '2025-05-09 12:02:06', 0, 'student', 0),
(20, 'Aiman', 'afiq77355@gmail.com', '$2y$10$lEkqFXHOlKdAE4FgYP/ZnO77w8ze0rPqFFXNZu1hhk7h/5Knd2SzC', '2025-05-09 14:20:40', 0, 'student', 0),
(21, 'Nur Adlina', 'nuradlinahanafi@gmail.com', '$2y$10$gWGf.w5kS96QIv.q3DCn1evp2l/J3NK2CNX5zlRqSoxe6WcdVcAN2', '2025-05-09 14:32:29', 0, 'student', 0),
(23, 'Muhammad Aiman', 'aimanzaidee123@gmail.com', '$2y$10$SHrsG8c4/j5w4nOZfRRLieA4Bp7fQ4lGMy5rSsviElIssS4NGQiWW', '2025-05-10 15:20:17', 0, 'student', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_chapter_progress`
--

CREATE TABLE `user_chapter_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `chapter_id` int(11) DEFAULT NULL,
  `progress` int(11) DEFAULT 0,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_chapter_progress`
--

INSERT INTO `user_chapter_progress` (`id`, `user_id`, `chapter_id`, `progress`, `last_updated`) VALUES
(1, 7, 1, 100, '2025-05-11 02:34:37'),
(3, 7, 6, 100, '2025-05-11 02:43:53'),
(4, 7, 5, 100, '2025-05-11 02:44:07'),
(5, 7, 4, 100, '2025-05-11 02:44:14'),
(6, 7, 3, 100, '2025-05-11 02:44:18');

-- --------------------------------------------------------

--
-- Table structure for table `user_game_status`
--

CREATE TABLE `user_game_status` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_name` varchar(50) NOT NULL,
  `completed` tinyint(1) DEFAULT 0,
  `badge_earned` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_game_status`
--

INSERT INTO `user_game_status` (`id`, `user_id`, `game_name`, `completed`, `badge_earned`) VALUES
(1, 8, 'caesar', 1, 1),
(2, 7, 'caesar', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `approved` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`id`, `chapter_id`, `title`, `video_url`, `created_at`, `approved`) VALUES
(1, 1, 'Introduction to Cryptography - Video', 'https://www.w3schools.com/html/mov_bbb.mp4', '2025-05-08 23:03:42', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chapters`
--
ALTER TABLE `chapters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chapter_files`
--
ALTER TABLE `chapter_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chapter_id` (`chapter_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `progress`
--
ALTER TABLE `progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `chapter_id` (`chapter_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chapter_id` (`chapter_id`);

--
-- Indexes for table `site_visits`
--
ALTER TABLE `site_visits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_chapter_progress`
--
ALTER TABLE `user_chapter_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `chapter_id` (`chapter_id`);

--
-- Indexes for table `user_game_status`
--
ALTER TABLE `user_game_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chapter_id` (`chapter_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chapters`
--
ALTER TABLE `chapters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `chapter_files`
--
ALTER TABLE `chapter_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `progress`
--
ALTER TABLE `progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `site_visits`
--
ALTER TABLE `site_visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `user_chapter_progress`
--
ALTER TABLE `user_chapter_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_game_status`
--
ALTER TABLE `user_game_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chapter_files`
--
ALTER TABLE `chapter_files`
  ADD CONSTRAINT `chapter_files_ibfk_1` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `progress`
--
ALTER TABLE `progress`
  ADD CONSTRAINT `progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `progress_ibfk_2` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_chapter_progress`
--
ALTER TABLE `user_chapter_progress`
  ADD CONSTRAINT `user_chapter_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_chapter_progress_ibfk_2` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`);

--
-- Constraints for table `videos`
--
ALTER TABLE `videos`
  ADD CONSTRAINT `videos_ibfk_1` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
