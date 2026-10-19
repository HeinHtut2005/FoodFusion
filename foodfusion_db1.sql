-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 18, 2026 at 11:57 AM
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
-- Database: `foodfusion_db1`
--

-- --------------------------------------------------------

--
-- Table structure for table `community_comments`
--

CREATE TABLE `community_comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `community_comments`
--

INSERT INTO `community_comments` (`id`, `post_id`, `user_id`, `comment`, `created_at`) VALUES
(1, 1, 5, 'woew!!', '2026-10-15 02:36:02'),
(2, 3, 7, 'Wow!! That\'s great!!!', '2026-10-17 19:35:52');

-- --------------------------------------------------------

--
-- Table structure for table `community_likes`
--

CREATE TABLE `community_likes` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `community_likes`
--

INSERT INTO `community_likes` (`id`, `post_id`, `user_id`, `created_at`) VALUES
(1, 1, 5, '2026-10-15 02:35:57'),
(2, 2, 7, '2026-10-17 19:25:28'),
(3, 3, 7, '2026-10-17 19:35:16');

-- --------------------------------------------------------

--
-- Table structure for table `community_posts`
--

CREATE TABLE `community_posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `community_posts`
--

INSERT INTO `community_posts` (`id`, `user_id`, `title`, `content`, `image`, `created_at`) VALUES
(1, 5, 'Herbs, Spices and Flavor Pairing', 'Discover how herbs, spices, and seasonings work together to enhance the taste and aroma of dishes from different culinary traditions.', '1792029538_1b103c0c8e8d91cfa1bafa22c435fe04.jpg', '2026-10-15 01:58:58'),
(2, 7, 'Thai Tom Yum Soup', 'A traditional Thai soup combining spicy chili, sour lime, fragrant lemongrass, mushrooms, and fresh shrimp for a bold and refreshing flavor.', '1792265114_c9285f6cea90c7d15589744e4785342b.jpg', '2026-10-17 19:25:14'),
(3, 7, 'Chocolate Lava Cake', 'A rich chocolate dessert with a soft cake exterior and a warm, gooey chocolate center. Serve with fresh berries or vanilla ice cream for extra indulgence.', '1792265212_7098aef89d194ab014df97c118e4fe18.jpg', '2026-10-17 19:26:52');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(1, 'Hein htut Kyaw', 'heinhtutkyaw8@gmail.com', 'Recipe Submission', 'There is an issue', '2026-10-15 01:59:43'),
(2, 'Hein htut Kyaw', 'heinhtutkyaw8@gmail.com', 'General Question', 'There is an issue\r\n', '2026-10-15 02:36:56'),
(3, 'Myo Min', 'myomin@gmail.com', 'Recipe Submission', 'There is an issue on recipe submission!! Please fix it!', '2026-10-17 19:40:05');

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `id` int(11) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `cuisine_type` varchar(100) DEFAULT NULL,
  `dietary_preference` varchar(100) DEFAULT NULL,
  `difficulty` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`id`, `title`, `cuisine_type`, `dietary_preference`, `difficulty`, `description`, `image`, `created_at`) VALUES
(1, 'Creamy Garlic Chicken Pasta', 'Mexican', 'Halal', 'Medium', 'A delicious pasta dish made with tender chicken, fresh garlic, creamy sauce, and Parmesan cheese. Perfect for a quick and satisfying dinner.', '1b103c0c8e8d91cfa1bafa22c435fe04.jpg', '2026-10-15 01:41:13'),
(2, 'Traditional Burmese Chicken Curry', 'Asian', 'Halal', 'Easy', 'A flavorful Myanmar-style chicken curry cooked with aromatic spices, onions, garlic, and ginger. Best served with steamed rice and fresh vegetables.', '9f3581f90dcef39420d9c451c7650e92.jpg', '2026-10-15 01:42:25'),
(3, 'Classic Margherita Pizza', 'Italian', 'Vegan', 'Medium', 'A homemade Italian pizza topped with tomato sauce, melted mozzarella cheese, and fresh basil. A simple and tasty choice for sharing with family and friends.', '8dc42f6e4a2dac5379649c69ca7ce571.jpg', '2026-10-15 01:44:46');

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `id` int(11) NOT NULL,
  `resource_category` varchar(50) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`id`, `resource_category`, `title`, `description`, `file_name`, `file_type`, `created_at`) VALUES
(1, 'Culinary', 'Essential Cooking Techniques', 'Learn fundamental cooking methods such as boiling, steaming, sautéing, roasting, and grilling to improve your everyday cooking skills.', '1792028812_0-02-06-ba2a0816165431f9329d0249fa4d6baa58d631fdcc90fc1f6c29d68bd63f5087_befb0090b1086267.mp4', 'mp4', '2026-10-15 01:46:52'),
(2, 'Culinary', 'Healthy Eating and Nutrition', 'Discover the importance of balanced meals, essential nutrients, and fresh ingredients to support a healthy lifestyle.', '1792028849_0-02-01-c372640b9fc0bebdb85aec8b944081656b7f157cac2a55af4c0a88017deaf269_d08e8d80f84e94ae.mp4', 'mp4', '2026-10-15 01:47:29'),
(3, 'Educational', 'The Ultimate Cooking Guide', 'Explore essential cooking methods, ingredient preparation, and helpful kitchen tips to build confidence and create delicious meals at home.', '1792028917_0-02-06-ba2a0816165431f9329d0249fa4d6baa58d631fdcc90fc1f6c29d68bd63f5087_befb0090b1086267.mp4', 'mp4', '2026-10-15 01:48:37'),
(7, 'Culinary', 'Culinary Resources - Description', 'Discover step-by-step culinary tutorials designed to help you improve your cooking skills and explore new flavors.', 'resource_6ad496e5a9a177.75196329.mp4', 'mp4', '2026-10-18 09:52:37'),
(8, 'Culinary', 'Culinary Heritage', 'Explore essential cooking methods, ingredient preparation, and helpful kitchen tips to build confidence and create delicious meals at home.', 'resource_6ad497134ec1d4.56224369.mp4', 'mp4', '2026-10-18 09:53:23');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `failed_attempts` int(11) DEFAULT 0,
  `last_failed_login` datetime DEFAULT NULL,
  `role` varchar(20) DEFAULT 'member'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `created_at`, `failed_attempts`, `last_failed_login`, `role`) VALUES
(5, 'Hein Htut', 'Kyaw', 'heinhtutkyaw8@gmail.com', '$2y$10$XEtiXS498zlwOxJjCWOmEOJQkORV0RIl4E.22bj5MRBmf1cuxoHgO', '2026-10-14 15:54:21', 1, '2026-10-17 21:31:16', 'member'),
(6, 'Admin', '01', 'admin@gmail.com', '$2y$10$66VkUXFqQmMJjoIy4pIjKuqw5jvJLeU3PFRxx1jHCWK0oDKcazSg.', '2026-10-14 17:14:36', 0, NULL, 'admin'),
(7, 'Myo', 'Min', 'myomin@gmail.com', '$2y$10$1oySU6mbEz/OwFWXWKqLu.gM7YsoH6A4IVvqu0eWYH.S1lyF7HZ2u', '2026-10-17 15:02:08', 0, NULL, 'member'),
(8, 'Hsu', 'Myat', 'hsumyat@gmail.com', '$2y$10$QPCbI9VIVO6EImk.fuamau7IJDdA80YFmzrcbCuoNGmf8gvWS3oAG', '2026-10-17 15:45:35', 0, NULL, 'member'),
(9, 'Min', 'Min', 'minmin@gmail.com', '$2y$10$kwKJX2bn1VvTS59U7vg2QeQ6pqxlfN8kZaILAatIYdwWyxZoCii4m', '2026-10-18 07:48:36', 0, NULL, 'member');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `community_comments`
--
ALTER TABLE `community_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `community_likes`
--
ALTER TABLE `community_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `post_id` (`post_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `community_posts`
--
ALTER TABLE `community_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `community_comments`
--
ALTER TABLE `community_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `community_likes`
--
ALTER TABLE `community_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `community_posts`
--
ALTER TABLE `community_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `community_comments`
--
ALTER TABLE `community_comments`
  ADD CONSTRAINT `community_comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `community_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `community_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `community_likes`
--
ALTER TABLE `community_likes`
  ADD CONSTRAINT `community_likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `community_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `community_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `community_posts`
--
ALTER TABLE `community_posts`
  ADD CONSTRAINT `community_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
