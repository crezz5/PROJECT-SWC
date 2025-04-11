-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 08, 2025 at 04:19 AM
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
-- Database: `merch_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `email`, `full_name`, `created_at`) VALUES
(2, 'admin', '$2y$10$F69pAPL1LMRD28NmGhutMOBQkk3PXZ0vW724Z65TzOIzupyHA01aO', 'admin@yourdomain.com', 'Administrator', '2025-03-25 17:26:30');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `payment_method` varchar(20) NOT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total`, `payment_method`, `status`, `created_at`) VALUES
(18, 1, 12.15, 'cod', 'pending', '2025-04-08 01:35:51');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(19, 18, 19, 1, 12.15);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL COMMENT 'Price in MYR',
  `discounted_price` decimal(10,2) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `game_category` varchar(50) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `discounted_price`, `image`, `category`, `game_category`, `stock`, `created_at`) VALUES
(1, 'Kamisato Ayaka Impression T-Shirt', 'Kamisato Ayaka Impression T-Shirt | Genshin Impact', 99.99, NULL, 'tshirt.webp', 'clothing', 'genshin', 100, '2025-03-25 12:52:14'),
(2, 'Honkai Start Rail Character Poster', 'High quality Honkai Star Rail character poster', 59.99, NULL, 'poster.webp', 'poster', 'starrail', 50, '2025-03-25 12:52:14'),
(3, 'Herta Figurine', 'Herta Kuru Kuru Spinning Figure', 9.99, NULL, 'figure.webp', 'figurine', 'starrail', 200, '2025-03-25 12:52:14'),
(4, 'Elemental Visions', 'Element Vision Genshin Impact', 39.99, NULL, 'visions.webp', 'collector', 'genshin', 75, '2025-03-25 12:52:14'),
(5, 'Shenhe Keycaps Set', 'Premium PBT keycap set featuring Shenhe design', 129.99, NULL, 'shenhe_keycaps.webp', 'gaming', 'genshin', 24, '2025-03-25 14:48:18'),
(6, 'Genshin Impact Mousepad', 'Large gaming mousepad with Genshin Impact artwork', 59.99, NULL, 'genshin_mousepad.webp', 'gaming', 'genshin', 75, '2025-03-25 14:48:18'),
(7, 'Honkai Star Rail Keyboard', 'Mechanical keyboard with Honkai Star Rail theme', 299.99, NULL, 'starrail_keyboard.webp', 'gaming', 'starrail', 30, '2025-03-25 14:48:18'),
(8, 'Genshin Impact Archon Statue', 'Resin collectible statue of your favorite Archon', 199.99, NULL, 'archon_statue.webp', 'collector', 'genshin', 30, '2025-03-25 14:58:21'),
(9, 'Honkai Star Rail Sunday Theme Notebook Set\r\n', 'This elegantly designed notebook set embodies a refined aesthetic with its deep navy blue cover, intricate gold foil accents, and secure strap closure.', 89.99, NULL, 'honkai_artbook.webp', 'collector', 'starrail', 40, '2025-03-25 14:58:21'),
(10, 'Acheron Figurine', 'Detailed model of the Acheron From Honkai Star Rail', 149.99, NULL, 'acheron_figurine.webp', 'figurine', 'starrail', 25, '2025-03-25 14:58:21'),
(11, 'Zenless Zone Zero Poster Set', 'Set of 3 high-quality art posters', 49.99, NULL, 'zzz_posters.webp', 'poster', 'zenless', 60, '2025-03-25 14:58:21'),
(12, 'ZZZ Official Logo Hoodie', 'Premium quality hoodie featuring the official Zenless Zone Zero logo', 89.99, NULL, 'ZZZ Official Logo Hoodie.webp', 'clothing', 'zenless', 44, '2025-03-25 16:02:50'),
(13, 'Belle & Wise Figure Set', 'Detailed PVC figures of Belle and Wise from Zenless Zone Zero (15cm tall)', 129.99, NULL, 'Belle & Wise Figure Set.webp', 'figurine', 'zenless', 24, '2025-03-25 16:02:50'),
(14, 'ZZZ Limited Edition Art Book', 'Collector\'s art book with concept art and developer commentary', 59.99, NULL, 'ZZZ Limited Edition Art Book.jpg', 'collector', 'zenless', 60, '2025-03-25 16:02:50'),
(15, 'Zenless Zone Zero Keycaps', 'Custom mechanical keyboard with Zenless Zone Zero theme keycaps', 49.99, NULL, 'ZZZ Mechanical Keyboard.webp', 'gaming', 'zenless', 14, '2025-03-25 16:02:50'),
(17, 'Genshin Theme Notebook', 'Genshin Theme Notebook', 15.00, NULL, '67e3a72af2c51.jpeg', 'collector', 'genshin', 10, '2025-03-26 07:05:14'),
(18, 'Gesnhin Impact Poster', 'hanging painting Genshin impact Mavuika Citlali Neuvillette silk cloth posters', 10.00, 9.00, 'Gesnhin Impact Poster.webp', 'poster', 'genshin', 25, '2025-04-01 03:10:06'),
(19, 'Genshin Impact Figure ', 'Genshin Impact Figure Mini Hu Tao, Yae Miko, Klee, Raiden Shogun, Ayaka, Scaramouche (Wanderer) Cute Figurine', 13.50, 12.15, 'Genshin Impact Figure.webp', 'figurine', 'genshin', 19, '2025-04-01 03:13:46'),
(20, 'Honkai star rail hoodie', 'Honkai star rail blade jacket hoodie ', 150.00, 135.00, 'Honkai star rail hoodie.webp', 'clothing', 'starrail', 13, '2025-04-01 03:16:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `address`, `created_at`) VALUES
(1, 'Afif', '$2y$10$ihMAhQRFNv08VmRVANJ9ye2br.Tl93mVd9.kQScr5TCfqpvUC2Pe.', 'rosdiafif98@gmail.com', 'Afif Farhan Bin Rosdi', 'No 7 Jalan Desa Serdang 19 Taman Desa Serdang', '2025-03-25 13:17:00');

-- --------------------------------------------------------

--
-- Table structure for table `user_carts`
--

CREATE TABLE `user_carts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `discounted_price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_carts`
--
ALTER TABLE `user_carts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_carts`
--
ALTER TABLE `user_carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `user_carts`
--
ALTER TABLE `user_carts`
  ADD CONSTRAINT `user_carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_carts_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
