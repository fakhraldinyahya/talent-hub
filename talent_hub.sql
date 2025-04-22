-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 21, 2025 at 09:41 PM
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
-- Database: `talent_hub`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `description`, `created_by`, `created_at`, `updated_at`, `image`) VALUES
(1, 'مجموعة البرمجة', 'مساحة تجمع المبرمجين لتبادل المعرفة، طرح الأسئلة، ومشاركة التجارب، مما يساعدهم على تطوير مهاراتهم بشكل جماعي.\n', 1, '2025-04-17 21:59:32', '2025-04-20 20:11:29', 'group_6804208d2bb727.55280290.jpg'),
(2, 'مجموعة الرسم', 'ملتقى للفنانين لعرض أعمالهم، تبادل الآراء، وتطوير مهاراتهم من خلال الحوار والنقد البنّاء.\n', 1, '2025-04-17 22:10:34', '2025-04-20 20:12:07', 'group_68042453995f13.92549859.jpg'),
(3, 'مجموعة التصوير الفوتوغرافي', 'تجمع المصورين لتبادل الخبرات، عرض الصور، والتعلم من بعض في كل ما يخص الزوايا والإضاءة والتحرير', 1, '2025-04-18 22:10:08', '2025-04-20 20:13:11', 'group_68042453945f13.92549159.jpg'),
(4, ' مجموعة تصميم الأزياء ', 'هنا يلتقي عشاق الموضة لتبادل الأفكار، عرض التصاميم، وتعلّم أسرار التنسيق والإبداع في عالم الأزياء.\n', 1, '2025-04-18 22:35:16', '2025-04-20 20:11:52', 'group_68042453945f13.92589859.jpg'),
(5, 'مجموعة الإلقاء والشعر', 'بيئة محفّزة لمحبي الإلقاء وكتّاب الشعر، يتشاركون فيها النصوص والتجارب لتطوير الأداء والتعبير الإبداعي.\n', 1, '2025-04-19 15:38:53', '2025-04-20 20:12:31', 'group_68042453945f13.92549879.jpg'),
(6, 'مجموعة القراءة', 'يقدر القراء هنا يناقشوا الكتب، يشاركوا ملخصات وآراء، ويتبادلوا ترشيحات تنمّي حب المعرفة.\n', 1, '2025-04-19 15:39:17', '2025-04-20 20:12:46', 'group_68042453945f13.92549859.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `group_members`
--

CREATE TABLE `group_members` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('member','admin') DEFAULT 'member',
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_read` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group_members`
--

INSERT INTO `group_members` (`id`, `group_id`, `user_id`, `role`, `joined_at`, `last_read`) VALUES
(1, 1, 2, 'member', '2025-04-17 21:59:32', '2025-04-21 20:03:44'),
(2, 1, 1, 'admin', '2025-04-17 22:00:14', '2025-04-21 00:00:11'),
(3, 2, 2, 'member', '2025-04-17 22:10:34', '2025-04-18 01:37:19'),
(6, 1, 6, 'member', '2025-04-18 19:50:19', '2025-04-19 00:15:59'),
(7, 3, 1, 'admin', '2025-04-18 22:10:08', '2025-04-20 03:20:25'),
(8, 3, 9, 'member', '2025-04-18 22:30:49', '2025-04-19 01:31:27'),
(9, 3, 8, 'member', '2025-04-18 22:31:21', '2025-04-19 01:31:21'),
(10, 4, 1, 'admin', '2025-04-18 22:35:16', '2025-04-19 01:35:16'),
(13, 6, 2, 'member', '2025-04-20 20:00:48', '2025-04-20 23:01:49'),
(14, 6, 1, 'admin', '2025-04-20 20:03:32', '2025-04-20 23:03:32'),
(15, 2, 1, 'member', '2025-04-20 21:05:37', '2025-04-21 00:05:46');

-- --------------------------------------------------------

--
-- Table structure for table `group_messages`
--

CREATE TABLE `group_messages` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `media_type` enum('text','image','video','audio') DEFAULT 'text',
  `media_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `media_type` enum('text','image','video') DEFAULT 'text',
  `media_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `private_messages`
--

CREATE TABLE `private_messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `media_type` enum('text','image','video','audio') DEFAULT 'text',
  `media_url` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `bio` text DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT 'default.jpg',
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_online` tinyint(1) DEFAULT 0,
  `category` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `bio`, `profile_picture`, `role`, `created_at`, `updated_at`, `is_online`, `category`, `is_active`, `reset_token`, `token_expiry`) VALUES
(1, 'Mashael', 'mashaelalmari@gmail.com', '$2y$10$kpY9nIxT5G4l3lqnMX8AHeK5Uqn2b3NXKuIOMffBqmH7v9Q/CV9S6', 'Mashael aljohani\n', '', 'default.jpeg', 'admin', '2025-04-15 00:59:36', '2025-04-21 17:21:06', 1, 'programming', 1, NULL, '2025-04-21 04:47:02'),
(2, 'Waad', 'Waad@gmail.com', '$2y$10$kpY9nIxT5G4l3lqnMX8AHeK5Uqn2b3NXKuIOMffBqmH7v9Q/CV9S6', 'Waad aljohan', NULL, 'default.jpeg', 'user', '2025-04-15 01:16:08', '2025-04-21 19:35:45', 1, 'programming', 1, NULL, '2025-04-21 22:04:33'),
(3, 'Maha', 'Maha@gmail.com', '$2y$10$kpY9nIxT5G4l3lqnMX8AHeK5Uqn2b3NXKuIOMffBqmH7v9Q/CV9S6', 'Maha aljohani\n', NULL, 'default.jpeg', 'user', '2025-04-18 14:50:28', '2025-04-20 21:46:18', 0, NULL, 1, NULL, NULL),
(4, 'Rami', 'Rami@gmail.com', '$2y$10$kpY9nIxT5G4l3lqnMX8AHeK5Uqn2b3NXKuIOMffBqmH7v9Q/CV9S6', 'Rami alharbi\n', NULL, 'default.jpeg', 'user', '2025-04-18 15:25:03', '2025-04-21 19:36:28', 0, NULL, 1, NULL, NULL),
(5, 'Osama', 'Osama@gmail.com', '$2y$10$kpY9nIxT5G4l3lqnMX8AHeK5Uqn2b3NXKuIOMffBqmH7v9Q/CV9S6', 'Osama alblwi\n', NULL, 'default.jpeg', 'user', '2025-04-18 15:27:38', '2025-04-21 19:36:23', 1, 'writing', 1, NULL, NULL),
(6, 'Abdullah', 'Abdullah@gmail.com', '$2y$10$kpY9nIxT5G4l3lqnMX8AHeK5Uqn2b3NXKuIOMffBqmH7v9Q/CV9S6', 'Abdullah alshammri', NULL, 'default.jpeg', 'user', '2025-04-18 19:47:26', '2025-04-21 19:36:20', 0, 'programming', 1, NULL, NULL),
(7, 'Hala', 'Hala@gmail.com', '$2y$10$kpY9nIxT5G4l3lqnMX8AHeK5Uqn2b3NXKuIOMffBqmH7v9Q/CV9S6', 'Hala alharbi', NULL, 'default.jpeg', 'user', '2025-04-18 19:53:07', '2025-04-21 19:36:17', 0, 'engineering', 1, NULL, NULL),
(8, 'Raghad', 'Raghad@gmail.com', '$2y$10$kpY9nIxT5G4l3lqnMX8AHeK5Uqn2b3NXKuIOMffBqmH7v9Q/CV9S6', 'Raghad ahmed', NULL, 'default.jpeg', 'user', '2025-04-18 21:33:46', '2025-04-21 19:36:10', 1, 'artificial_intelligence', 1, NULL, NULL),
(9, 'Dania', 'Dania@gmail.com', '$2y$10$kpY9nIxT5G4l3lqnMX8AHeK5Uqn2b3NXKuIOMffBqmH7v9Q/CV9S6', 'Dania khalid \n', NULL, 'default.jpeg', 'user', '2025-04-18 22:21:53', '2025-04-21 19:36:07', 1, 'design', 1, NULL, NULL),
(10, 'Rana', 'Rana@gmail.com', '$2y$10$kpY9nIxT5G4l3lqnMX8AHeK5Uqn2b3NXKuIOMffBqmH7v9Q/CV9S6', 'Rana aljohn', NULL, 'default.jpeg', 'user', '2025-04-19 23:14:33', '2025-04-19 23:14:33', 0, 'drawing', 1, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `group_members`
--
ALTER TABLE `group_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `group_user_unique` (`group_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `group_messages`
--
ALTER TABLE `group_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_post_unique` (`user_id`,`post_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `private_messages`
--
ALTER TABLE `private_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `group_members`
--
ALTER TABLE `group_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `group_messages`
--
ALTER TABLE `group_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `private_messages`
--
ALTER TABLE `private_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `group_members`
--
ALTER TABLE `group_members`
  ADD CONSTRAINT `group_members_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `group_messages`
--
ALTER TABLE `group_messages`
  ADD CONSTRAINT `group_messages_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_messages_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `private_messages`
--
ALTER TABLE `private_messages`
  ADD CONSTRAINT `private_messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `private_messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
