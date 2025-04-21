-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 20, 2025 at 03:16 PM
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

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `post_id`, `content`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'wetwetrwer', '2025-04-15 01:13:45', '2025-04-15 01:13:45'),
(2, 1, 1, 'ffff', '2025-04-18 12:45:37', '2025-04-18 12:45:37'),
(3, 5, 1, 'http://localhost/talent-hub/posts/view.php?id=1', '2025-04-18 15:55:46', '2025-04-18 15:55:46');

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
(1, 'مجموعة البرمجة', 'مجموعة البرمجة تحتوي على أدوات وموارد مخصصة لتعلم وتطوير البرمجيات بكفاءة واحترافية.', 2, '2025-04-17 21:59:32', '2025-04-19 23:43:50', 'group_6804208d2bb727.55280290.jpg'),
(2, 'مجموعة الرسم', 'مجموعة الرسم تحتوي على أدوات وألوان متنوعة لتنمية الإبداع والتعبير الفني بالرسم.', 2, '2025-04-17 22:10:34', '2025-04-19 23:45:07', 'group_68042453995f13.92549859.jpg'),
(3, 'مجموعة التصوير الفوتوغرافي', 'مجموعة التصوير الفوتوغرافي تضم معدات وإكسسوارات لالتقاط صور احترافية بجودة عالية.', 1, '2025-04-18 22:10:08', '2025-04-19 23:43:38', 'group_68042453945f13.92549159.jpg'),
(4, ' مجموعة تصميم الأزياء ', 'مجموعة تصميم الأزياء تضم أدوات وخامات لإبداع تصاميم مبتكرة وتطوير المهارات في عالم الموضة.', 1, '2025-04-18 22:35:16', '2025-04-19 23:44:51', 'group_68042453945f13.92589859.jpg'),
(5, 'مجموعة الإلقاء والشعر', 'مجموعة الإلقاء والشعر تحتوي على أدوات تعزز مهارات الأداء والتعبير الفني لمحبي الشعر والخطابة.', 1, '2025-04-19 15:38:53', '2025-04-19 23:44:34', 'group_68042453945f13.92549879.jpg'),
(6, 'مجموعة القراءة', 'مجموعة القراءة تضم كتبًا ومستلزمات تساعد على الاستمتاع بالقراءة وتنمية المعرفة.', 1, '2025-04-19 15:39:17', '2025-04-19 23:44:15', 'group_68042453945f13.92549859.jpg');

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
(1, 1, 2, 'admin', '2025-04-17 21:59:32', '2025-04-18 04:34:49'),
(2, 1, 1, 'member', '2025-04-17 22:00:14', '2025-04-18 02:27:39'),
(3, 2, 2, 'admin', '2025-04-17 22:10:34', '2025-04-18 01:37:19'),
(6, 1, 6, 'member', '2025-04-18 19:50:19', '2025-04-19 00:15:59'),
(7, 3, 1, 'admin', '2025-04-18 22:10:08', '2025-04-20 03:20:25'),
(8, 3, 9, 'member', '2025-04-18 22:30:49', '2025-04-19 01:31:27'),
(9, 3, 8, 'member', '2025-04-18 22:31:21', '2025-04-19 01:31:21'),
(10, 4, 1, 'admin', '2025-04-18 22:35:16', '2025-04-19 01:35:16');

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

--
-- Dumping data for table `group_messages`
--

INSERT INTO `group_messages` (`id`, `group_id`, `user_id`, `message`, `media_type`, `media_url`, `created_at`) VALUES
(1, 2, 2, 'ddd', 'text', '', '2025-04-17 22:11:44'),
(2, 2, 2, 'ddd', 'text', '', '2025-04-17 22:11:52'),
(3, 1, 1, 'dddd', 'text', '', '2025-04-17 22:36:32'),
(4, 2, 2, 'ddddd', 'text', '', '2025-04-17 22:37:26'),
(5, 1, 6, 'Hello', 'text', '', '2025-04-18 21:16:12'),
(6, 3, 9, 'ماذ تعرف عن ال php', 'text', '', '2025-04-18 22:31:42'),
(7, 3, 8, 'هي لغه عاليه المستوى', 'text', '', '2025-04-18 22:32:18');

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

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `user_id`, `post_id`, `created_at`) VALUES
(2, 1, 1, '2025-04-18 12:45:39');

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

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `title`, `content`, `media_type`, `media_url`, `created_at`, `updated_at`) VALUES
(1, 1, 'asdfasdfs', 'sgesgsdfg', 'text', '', '2025-04-15 01:12:22', '2025-04-15 01:12:22'),
(2, 2, 'ssssss', 'ييييي', 'image', '680189618b66c_IMG-20230218-WA0049.jpg', '2025-04-17 23:06:09', '2025-04-18 22:06:35'),
(3, 6, 'برمجة php', 'PHP (Hypertext Preprocessor) هي لغة برمجة خادم-side تُستخدم بشكل رئيسي لتطوير تطبيقات الويب. إليك بعض النقاط الأساسية حول PHP:', 'text', '', '2025-04-18 20:09:36', '2025-04-18 20:09:36'),
(4, 6, 'برمجة php صور', 'سهولة الاستخدام: تعتبر PHP سهلة التعلم، مما يجعلها مناسبة للمبتدئين.\r\n\r\nديناميكية: تُستخدم لإنشاء صفحات ويب ديناميكية، حيث يمكنها التفاعل مع قواعد البيانات وتوليد محتوى مخصص.\r\n\r\nتوافق', 'image', '6802c19a32788_bgg5.png', '2025-04-18 21:18:18', '2025-04-18 21:18:18'),
(5, 3, 'البايثون', 'البايثون لغه رائعه', 'text', '', '2025-04-18 22:13:11', '2025-04-18 22:13:11'),
(6, 9, 'تعلم c++', 'هي لغه', 'text', '', '2025-04-18 22:33:25', '2025-04-18 22:33:25');

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

--
-- Dumping data for table `private_messages`
--

INSERT INTO `private_messages` (`id`, `sender_id`, `receiver_id`, `message`, `media_type`, `media_url`, `is_read`, `created_at`) VALUES
(1, 2, 1, 'dddd', 'text', '', 1, '2025-04-15 01:35:36'),
(2, 1, 2, ';dt', 'text', '', 1, '2025-04-15 01:35:49'),
(3, 1, 2, 'asdfasdfasd', 'text', '', 1, '2025-04-15 01:36:32'),
(4, 1, 2, 'dddd', 'text', '', 1, '2025-04-15 01:41:12'),
(5, 1, 2, '', 'image', '67fdb946cf039_1_2_1744681286.jpg', 1, '2025-04-15 01:41:26'),
(6, 2, 1, 'dddd', 'text', '', 1, '2025-04-15 01:41:49'),
(7, 1, 2, '', 'image', '67fdb966bf3ce_1_2_1744681318.jfif', 1, '2025-04-15 01:41:58'),
(8, 1, 2, 'igh', 'text', '', 1, '2025-04-17 23:22:50'),
(9, 1, 2, 'iiii', 'text', '', 1, '2025-04-17 23:23:05'),
(10, 1, 2, 'ddddd', 'text', '', 1, '2025-04-17 23:23:27'),
(11, 1, 2, 'ddd', 'text', '', 1, '2025-04-18 00:03:34'),
(12, 1, 2, 'يييي', 'text', '', 1, '2025-04-18 00:27:29'),
(13, 1, 2, 'ييي', 'text', '', 1, '2025-04-18 00:28:10'),
(14, 2, 1, 'ييييي', 'text', '', 1, '2025-04-18 00:28:37'),
(15, 2, 1, 'صصصص', 'text', '', 1, '2025-04-18 00:28:53'),
(16, 2, 1, 'يييي', 'text', '', 1, '2025-04-18 00:30:34'),
(17, 1, 2, 'ddddd', 'text', '', 1, '2025-04-18 00:38:17'),
(18, 1, 2, 'sss', 'text', '', 1, '2025-04-18 00:38:24'),
(19, 1, 2, 'dddd', 'text', '', 1, '2025-04-18 00:43:59'),
(20, 2, 1, 'dddd', 'text', '', 1, '2025-04-18 01:06:12'),
(21, 2, 1, 'ssss', 'text', '', 1, '2025-04-18 01:06:24'),
(22, 2, 1, 'dd', 'text', '', 1, '2025-04-18 01:06:50'),
(23, 2, 1, 'ssssss', 'text', '', 1, '2025-04-18 01:08:01'),
(24, 1, 2, 'ssss', 'text', '', 1, '2025-04-18 01:08:06'),
(25, 1, 2, 'ffffff', 'text', '', 1, '2025-04-18 01:08:18'),
(26, 1, 2, 'sssss', 'text', '', 1, '2025-04-18 01:11:19'),
(27, 1, 2, 'ssssss', 'text', '', 1, '2025-04-18 01:21:43'),
(28, 1, 2, 'bbbbbbbbbbbb', 'text', '', 1, '2025-04-18 01:21:56'),
(29, 1, 2, 'سسسس', 'text', '', 1, '2025-04-18 01:29:15'),
(30, 1, 2, 'يييييييي', 'text', '', 1, '2025-04-18 01:29:50'),
(31, 1, 2, 'ييييييييي', 'text', '', 1, '2025-04-18 01:30:21'),
(32, 2, 1, 'سسسسس', 'text', '', 1, '2025-04-18 01:32:54'),
(33, 1, 2, 'ثثثثثثثثثث', 'text', '', 1, '2025-04-18 01:33:12'),
(34, 1, 2, 'سسسسسس', 'text', '', 1, '2025-04-18 01:33:21'),
(35, 6, 8, 'السلام عليكم', 'text', '', 1, '2025-04-18 21:54:40'),
(36, 8, 6, 'وعليكم السلام', 'text', '', 1, '2025-04-18 21:57:53'),
(37, 6, 8, 'الووووو', 'text', '', 1, '2025-04-18 21:58:55'),
(38, 8, 6, 'كيفك', 'text', '', 0, '2025-04-18 21:59:31'),
(39, 9, 8, 'السلام عليكم', 'text', '', 1, '2025-04-18 22:29:43'),
(40, 8, 9, 'وعليكم السلام', 'text', '', 1, '2025-04-18 22:30:05');

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
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `bio`, `profile_picture`, `role`, `created_at`, `updated_at`, `is_online`, `category`, `is_active`) VALUES
(1, 'Mashael', 'a@gmail.com', '$2y$10$kpY9nIxT5G4l3lqnMX8AHeK5Uqn2b3NXKuIOMffBqmH7v9Q/CV9S6', 'Mashael aljohani\n', '', 'default.jpeg', 'admin', '2025-04-15 00:59:36', '2025-04-19 23:20:05', 1, 'programming', 1),
(2, 'Waad', 'Waad@gmail.com', '$2y$10$kpY9nIxT5G4l3lqnMX8AHeK5Uqn2b3NXKuIOMffBqmH7v9Q/CV9S6', 'Waad aljohan', NULL, 'default.jpeg', 'user', '2025-04-15 01:16:08', '2025-04-20 00:13:18', 1, 'programming', 1),
(3, 'Maha', 'f@gmail.com', '$2y$10$qOVVQTcXi13BmMia5b1GDu0nn6wFv3N66S4a4DsUSfZeHFcXxg7UO', 'Maha aljohani\n', NULL, 'default.jpeg', 'user', '2025-04-18 14:50:28', '2025-04-19 23:06:17', 0, NULL, 1),
(4, 'Rami', 's@gmail.com', '$2y$10$JVBSGnJrpzrCKMEGsCsSvO7Hl4pbfw5s3j200SqPRq/Mt0lDzESh6', 'Rami alharbi\n', NULL, 'default.jpeg', 'user', '2025-04-18 15:25:03', '2025-04-19 23:06:40', 0, NULL, 1),
(5, 'Osama', 'c@gmail.com', '$2y$10$uwzlkA4JgcEL/lbeaIAmWe19VVqc.wy/8Z6NmxuPXMDAs4ug4/XbW', 'Osama alblwi\n', NULL, 'default.jpeg', 'user', '2025-04-18 15:27:38', '2025-04-19 23:07:25', 1, 'writing', 1),
(6, 'Abdullah', 'h@gmail.com', '$2y$10$SNCpXAa/onZ7Zm8jVY5zvuNVxn7SgOqBTIqUAhhRNvqTb3gMmoH9K', 'Abdullah alshammri', NULL, 'default.jpeg', 'user', '2025-04-18 19:47:26', '2025-04-19 23:20:00', 0, 'programming', 1),
(7, 'Hala', 'b@gmail.com', '$2y$10$5rGCEH118HBfQsZy3gCQlOMXIuerUjvqwRGsGCIGETLy5wc3Om2Ym', 'Hala alharbi', NULL, 'default.jpeg', 'user', '2025-04-18 19:53:07', '2025-04-19 23:19:53', 0, 'engineering', 1),
(8, 'Raghad', 'ma@gmail.com', '$2y$10$qOVVQTcXi13BmMia5b1GDu0nn6wFv3N66S4a4DsUSfZeHFcXxg7UO', 'Raghad ahmed', NULL, 'default.jpeg', 'user', '2025-04-18 21:33:46', '2025-04-19 23:19:48', 1, 'artificial_intelligence', 1),
(9, 'Dania', 'ww@gmail.com', '$2y$10$UznpZ/aX5ykchFedyjzHf.eh/467nTuQDLdkhKUFxpiMqflG25Z1m', 'Dania khalid \n', NULL, 'default.jpeg', 'user', '2025-04-18 22:21:53', '2025-04-19 23:19:43', 1, 'design', 1),
(10, 'Rana', 'Rana@gmail.com', '$2y$10$kpY9nIxT5G4l3lqnMX8AHeK5Uqn2b3NXKuIOMffBqmH7v9Q/CV9S6', 'Rana aljohn', NULL, 'default.jpeg', 'user', '2025-04-19 23:14:33', '2025-04-19 23:14:33', 0, 'drawing', 1);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `group_members`
--
ALTER TABLE `group_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `group_messages`
--
ALTER TABLE `group_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
