-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Aug 12, 2025 at 01:02 PM
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
-- Database: `gadgetshare`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `renter_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('pending','accepted','rejected','cancelled','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `actual_return_date` date DEFAULT NULL,
  `returned_at` datetime DEFAULT NULL,
  `closed_by_owner` tinyint(1) DEFAULT 0,
  `total_amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `item_id`, `renter_id`, `owner_id`, `start_date`, `end_date`, `status`, `created_at`, `actual_return_date`, `returned_at`, `closed_by_owner`, `total_amount`) VALUES
(1, 1, 2, 1, '2025-08-12', '2025-08-13', 'completed', '2025-08-11 15:25:16', '2025-08-11', NULL, 0, NULL),
(2, 2, 2, 1, '2025-08-09', '2025-08-10', 'completed', '2025-08-11 17:59:09', '2025-08-11', NULL, 0, NULL),
(3, 11, 3, 2, '2025-08-13', '2025-08-31', 'completed', '2025-08-12 05:11:55', '2025-08-12', NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Camera'),
(2, 'Trekking'),
(3, 'Gaming'),
(4, 'Riding'),
(5, 'Camping'),
(6, 'Audio/AV'),
(7, 'Creator Gear');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `specs` text DEFAULT NULL,
  `price_per_month` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `user_id`, `category_id`, `title`, `specs`, `price_per_month`, `created_at`) VALUES
(1, 1, 1, 'Canon EOS 3000D 18MP Digital SLR Camera', 'Self-Timer, Type C and Mini HDMI, 9 Auto Focus Points, 3x Optical Zoom, WiFi, Full HD, Video Recording at 1080 p on 30fps, APS-C CMOS sensor-which is 25 times larger than a typical Smartphone sensor.\r\nEffective Pixels: 18 MP\r\nSensor Type: CMOS\r\nWiFi Available\r\nFull HD', 5000.00, '2025-08-11 15:23:27'),
(2, 1, 3, 'Lenovo LOQ 12th Gen Gaming Laptop', 'This Lenovo LOQ Essential laptop comes with a low-power design, featuring 12th Gen Intel Core processors and 20 NVIDIA GeForce RTX graphics. Crafted for students and first-time gamers, this laptop is powered by AI-based DLSS 3. Furthermore, you can enjoy a 16:9 FHD display, 144 Hz refresh rate, and 300 nits of brightness. Besides, this laptop offers an ergonomically designed keypad for maximum typing comfort, an efficient cooling system, a 57 Whr fast-charging battery, and a Lenovo Vantage.', 7000.00, '2025-08-11 17:58:22'),
(3, 1, 7, 'SanDisk Portable SSD', 'SanDisk E30 / 800 Mbs / Window,Mac OS,Android / Portable,Type C Enabled / USB 3.2 1 TB External Solid State Drive (SSD)  (Black)', 500.00, '2025-08-12 04:28:41'),
(4, 3, 7, 'Samsung T7 USB 3.2 External SSD', 'Brand: Samsung\r\nModel Name: MU-PC1T0H/WW\r\nType: SSD\r\nCapacity: 1 TB\r\nColor: Blue\r\nConnectivity: USB 2.0, USB 3.0\r\nSystem Requirements: Windows, Mac\r\nForm Factor: Portable \r\nCloud Backup: No', 1000.00, '2025-08-12 04:34:50'),
(5, 3, 7, 'Apple 2025 iPad Air (M3)', '1 TB ROM\r\n27.94 cm (11.0 inch) Display\r\n12 MP Primary Camera | 12 MP Front\r\niPadOS 18 | Battery: Lithium Polymer Battery\r\nVoice Call (eSIM)\r\nProcessor: Apple M3 Chip', 6000.00, '2025-08-12 04:39:53'),
(10, 2, 1, 'LG 29WQ600-W', '29-Inch FHD UltraWide IPS 100Hz 5ms Monitor with AMD FreeSync', 1000.00, '2025-08-12 04:51:44'),
(11, 2, 1, 'Apple iPhone 13', 'SIM Card: Nano SIM, eSIM\r\nButtons: Power button, Volume buttons\r\nSIM Slots: 2: SIM SupportDual SIM, eSIM\r\nSensors: Accelerometer, Ambient light sensor, Barometer, Gyroscope, Proximity sensor\r\nFront Cam Res: 12 MP Front Cam Video Res 4K at 60 fps Front Cam Aperture f/2.2\r\nMain Cam Res: 12 MP Rear Cam Zoom 5x Still Image Resolution 12 MP\r\nUltrawide Cam Res: 12 MP Rear Cam Video Res 4K at 60 fps Focus Adjustment Autofocus Rear Cam Aperture\r\nf/1.6 Camera Light Source\r\nDual LED Flash\r\nLens Type: Ultrawide, Wide-angle Opt Sensor CMOS Rear Cam Lens Count 2\r\nMin Focal Len: 26 mm\r\nRear Cam Highlights: Optical Image Stabilization (OIS) Supported OS\r\niOS: OS Version iOS 15\r\nDisp Size: 6.1 in (15.5 cm)\r\nDisp Resolution: 2532 x 1170 pixels\r\nDisp Pixel Density: 460 ppi', 2000.00, '2025-08-12 04:58:23'),
(12, 4, 7, 'Whirlpool 235 L Frost Free Double Door 2 Star Refrigerator', 'Product Dimensions	65.5D x 56.4W x 158.7H Centimeters\r\nBrand	Whirlpool\r\nCapacity	235 litres\r\nConfiguration	Double\r\nBEE Star Rating	2 Star', 1500.00, '2025-08-12 05:02:12'),
(14, 4, 1, 'Samsung 7 KG Top Load Fully Automatic Washing Machine WA70A4002GS/TL', 'Load Type: Top Load, Medium\r\nAppliance Type: Washer\r\nAppliance Subtype: Top Load Washer\r\nWasher Cap: 7.0 kg (7 kg)\r\nDepth: 560 mm\r\nHeight: 850 mm\r\nWidth: 540 mm\r\nPower Source: Electric', 2000.00, '2025-08-12 05:09:24');

-- --------------------------------------------------------

--
-- Table structure for table `item_images`
--

CREATE TABLE `item_images` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_images`
--

INSERT INTO `item_images` (`id`, `item_id`, `filename`) VALUES
(1, 1, 'item_1_1754925807_0.jpg'),
(2, 1, 'item_1_1754925807_1.jpg'),
(3, 2, 'item_2_1754935102_0.jpg'),
(4, 2, 'item_2_1754935102_1.jpg'),
(5, 2, 'item_2_1754935102_2.jpg'),
(6, 3, 'item_3_1754972921_0.webp'),
(7, 3, 'item_3_1754972921_1.webp'),
(8, 3, 'item_3_1754972921_2.webp'),
(9, 4, 'item_4_1754973290_0.webp'),
(10, 4, 'item_4_1754973290_1.webp'),
(11, 4, 'item_4_1754973290_2.webp'),
(12, 5, 'item_5_1754973593_0.webp'),
(13, 5, 'item_5_1754973593_1.webp'),
(14, 5, 'item_5_1754973593_2.webp'),
(15, 5, 'item_5_1754973593_3.webp'),
(24, 10, 'item_10_1754974304_0.webp'),
(25, 10, 'item_10_1754974304_1.webp'),
(26, 11, 'item_11_1754974703_0.webp'),
(27, 11, 'item_11_1754974703_1.webp'),
(28, 11, 'item_11_1754974703_2.webp'),
(29, 12, 'item_12_1754974932_0.webp'),
(30, 12, 'item_12_1754974932_1.webp'),
(34, 14, 'item_14_1754975364_0.webp'),
(35, 14, 'item_14_1754975364_1.webp'),
(36, 14, 'item_14_1754975364_2.webp');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'Attadeep', 'atta@gmail.com', '$2y$10$R/qknZmqg45mNR9jtb2MUeoWP1KTR3VGgcR3AogQp3Tg1pgFv/wVK', '2025-08-11 15:20:08'),
(2, 'Aditya', 'adi@gmail.com', '$2y$10$zhXYcsR0GJjho8UxpNQFC.8/VfdjkzQ0RE1qafUflpJN51hUXBgAG', '2025-08-11 15:24:44'),
(3, 'Aryan Rajput', 'aryan@gmail.com', '$2y$10$L9pAuGWAtq7Un0Bz5BRxhuMJ7sgeYO6cPKujMWMtbXGuEw2vZ9RvC', '2025-08-12 04:29:40'),
(4, 'Ganesh Kondke', 'ganesh@gmail.com', '$2y$10$DdcD8/6noT3tDxUrE75Mi.fsEAyM3f3H7YCZAmsXQoM87jL01dX/K', '2025-08-12 04:59:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_bookings_item` (`item_id`),
  ADD KEY `fk_bookings_renter` (`renter_id`),
  ADD KEY `fk_bookings_owner` (`owner_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_items_user` (`user_id`),
  ADD KEY `fk_items_category` (`category_id`);

--
-- Indexes for table `item_images`
--
ALTER TABLE `item_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_images_item` (`item_id`);

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
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `item_images`
--
ALTER TABLE `item_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_bookings_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bookings_owner` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bookings_renter` FOREIGN KEY (`renter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `fk_items_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_items_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `item_images`
--
ALTER TABLE `item_images`
  ADD CONSTRAINT `fk_images_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
