-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Jun 27, 2025 at 01:34 PM
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
-- Database: `memberdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_cm`
--

CREATE TABLE `admin_cm` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `pass` varchar(255) CHARACTER SET armscii8 COLLATE armscii8_bin NOT NULL,
  `by` varchar(255) CHARACTER SET armscii8 COLLATE armscii8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_cm`
--

INSERT INTO `admin_cm` (`id`, `username`, `pass`, `by`) VALUES
(1, 'admin', '@dmin', 'Nawee');

-- --------------------------------------------------------

--
-- Table structure for table `event_cm`
--

CREATE TABLE `event_cm` (
  `id` int(11) NOT NULL,
  `event` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `phonenum` varchar(255) NOT NULL,
  `guests` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `time` varchar(255) NOT NULL,
  `status` varchar(255) DEFAULT 'กำลังรอดำเนินการ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_cm`
--

INSERT INTO `event_cm` (`id`, `event`, `username`, `phonenum`, `guests`, `date`, `time`, `status`) VALUES
(6, 'Shooting Studios', 'potato015', '', '2', '2025-05-24', '16:00 PM -18:00 PM', 'ยืนยันแล้ว'),
(9, 'Shooting Studios', 'potato015', '096 810 8480', '20', '2025-05-29', '12:00 AM -15:00 PM', 'กำลังรอดำเนินการ'),
(10, 'Meeting', 'potato015', '096 810 8480', '10', '2025-05-29', '13:00 AM -18:00 PM', 'กำลังรอดำเนินการ'),
(11, 'Private-Meeting', 'BabaYaga', '096 810 8480', '20', '2025-06-01', 'ทั้งวัน', 'กำลังรอดำเนินการ'),
(12, 'Shooting Studios', 'PukNv', '0878305123213', '500', '0000-00-00', 'ทั้งวัน', 'ยกเลิก'),
(13, 'Shooting photo Studios no.2', 'PukNv', '08123123213', '45', '2025-06-27', '13:00 AM -18:00 PM', 'กำลังรอดำเนินการ');

-- --------------------------------------------------------

--
-- Table structure for table `member_cm`
--

CREATE TABLE `member_cm` (
  `id` int(11) NOT NULL,
  `firstname` varchar(255) CHARACTER SET armscii8 COLLATE armscii8_bin NOT NULL,
  `surname` varchar(255) CHARACTER SET armscii8 COLLATE armscii8_bin NOT NULL,
  `phonenum` int(11) NOT NULL,
  `username` varchar(255) CHARACTER SET armscii8 COLLATE armscii8_bin NOT NULL,
  `pass` varchar(100) CHARACTER SET armscii8 COLLATE armscii8_bin NOT NULL,
  `email` varchar(255) CHARACTER SET armscii8 COLLATE armscii8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `member_cm`
--

INSERT INTO `member_cm` (`id`, `firstname`, `surname`, `phonenum`, `username`, `pass`, `email`) VALUES
(2, 'Sam', 'Fisher', 998809080, 'splinter', '$2y$10$lxQ6igUdHDBy2tkq3CAj1.NMdRXIyMpiPJM0FUKwAOWHCgalQot9K', 'sam@gmail.com'),
(3, 'Adam', 'man', 998860870, 'antman', '$2y$10$DmeSatqRJvSBupXIMe0d9.6JRDxI5QbP95.SB1meTkItXpZV9.hLS', 'adam@gmail.com'),
(6, 'Tanadon', 'Lerakiettivanis', 809001201, 'PotatoBrain', '$2y$10$.Fg4oYRsp.Qf7R6/z66rdu.dxkMH4FM24Z1b9dTl3VK7Sb/qN95V.', 'protae1589@gmail.com'),
(8, 'Nawee', 'Pukpak', 959037625, 'PukNv', '$2y$10$TP99AbpHzwn04Y9TsvIzouRdul7VKWTUBbtyey1EjR86eJ8Ds6zwu', 'navypukpak@gmail.com'),
(9, 'puksc', 'Reborn', 2147483647, 'quiizpuk', '$2y$10$VmNfOLb2TN/C8ukUBVJnHueLGjQw6n7yWYvLncTBHs0ZKDGLUSPAy', 'awdwdw@gmail.com'),
(10, 'kub', 'pree', 874723182, 'quiizpuk', '$2y$10$.2g9nPNoOiyAYVcbZkiRPO33YLe7FnqtCG.Qs.Yz.MW5Q8jNSqxrC', 'reborn@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `menu_cm`
--

CREATE TABLE `menu_cm` (
  `id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL,
  `menu_group` varchar(255) NOT NULL,
  `menu_type` varchar(255) NOT NULL,
  `drink_type` varchar(255) DEFAULT NULL,
  `is_closed` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_cm`
--

INSERT INTO `menu_cm` (`id`, `image`, `name`, `price`, `menu_group`, `menu_type`, `drink_type`, `is_closed`) VALUES
(6, 'img_6821c24e1655b7.10178600.jpg', 'Oreo Milk', '150', 'Drink', 'Milk', 'Smoothie', 0),
(7, 'img_6821c27509fe40.97944084.jpg', 'Topical', '135', 'Drink', 'Milk', 'Smoothie', 0),
(9, 'img_6821c523425234.41386754.jpg', 'Iced Late', '100', 'Drink', 'Coffee', 'Iced', 0),
(10, 'img_6821c56c7dfc45.39835489.jpg', 'Iced Cappuccino', '100', 'Drink', 'Coffee', 'Iced', 0),
(11, 'img_6821c5d266f126.96502033.jpg', 'H Late', '95', 'Drink', 'Coffee', 'Hot', 0),
(12, 'img_6821c5fb67f970.20865332.jpg', 'H Cappuccino', '95', 'Drink', 'Coffee', 'Hot', 0),
(14, 'img_6821c68f5f69d6.02148857.jpg', 'H Mocha', '110', 'Drink', 'Coffee', 'Hot', 0),
(19, 'img_6823262c37bf42.35608628.jpg', 'Hawaiian Pizza', '290', 'Food', 'Pizza', NULL, 1),
(24, 'img_6825ac62766ab6.17851728.jpg', 'Chicky', '185', 'Dessert', 'Cake', NULL, 0),
(25, 'img_6825ac80b089f7.02340855.jpg', 'Mini Black Cat', '225', 'Dessert', 'Cake', NULL, 0),
(26, 'img_6825aca5959934.02438193.jpg', 'Puppy', '185', 'Dessert', 'Cake', NULL, 0),
(27, 'img_6825acc1e1b811.72437595.jpg', 'Panda Set', '245', 'Dessert', 'Cake', NULL, 0),
(28, 'img_6825acd00254f8.07447561.jpg', 'Teddy Set', '245', 'Dessert', 'Cake', NULL, 0),
(29, 'img_6825ad9454c1b6.37401499.jpg', 'Rib-Eye Steak', '495', 'Food', 'Main', NULL, 0),
(30, 'img_6825add40777a6.07574646.jpg', 'Kurobuta-Tenderloin Steak', '345', 'Food', 'Main', NULL, 0),
(31, 'img_6825ae153c38a1.48872887.jpg', 'Grilled Chicken Breast Steak', '295', 'Food', 'Main', NULL, 0),
(32, 'img_6825aea59aac69.18418183.jpg', 'Cafe Latte Frappe', '120', 'Drink', 'Coffee', 'Smoothie', 0),
(33, 'img_6825b9a2f31ce3.48097974.jpg', 'Chocolate Frappe', '135', 'Drink', 'Milk', 'Smoothie', 0),
(34, 'img_6825b9ebcf66b1.81923751.jpg', 'Thaitea Milk Frappe', '120', 'Drink', 'Milk', 'Smoothie', 0),
(35, 'img_6825ba1e8b10e8.61964758.jpg', 'Caramel Milk Frappe', '135', 'Drink', 'Milk', 'Smoothie', 0),
(36, 'img_6825ba4cbc1be1.11016837.jpg', 'H Chocolate Mellow', '95', 'Drink', 'Milk', 'Hot', 0),
(37, 'img_6825baa6c59c76.26904229.jpg', 'TH!NK Signature Chocolate Frappe', '195', 'Drink', 'Milk', 'Smoothie', 0),
(38, 'img_6825bae1d1f456.04937642.jpg', 'Mint Chocolate Milk', '145', 'Drink', 'Milk', 'Iced', 0),
(39, 'img_6825bb1bd355e2.37650625.jpg', 'Chocolate Orange Bomb', '185', 'Drink', 'Milk', 'Iced', 0),
(40, 'img_6825c613e93f08.63485351.jpg', 'Lemon Iced Tea', '145', 'Drink', 'Tea', 'Iced', 0),
(41, 'img_6825c87196dc56.78306768.jpg', 'Thai Tea Milk', '95', 'Drink', 'Tea', 'Iced', 0),
(42, 'img_6825c89b542263.85303065.jpg', 'Thai Tea Milk Frappe', '120', 'Drink', 'Tea', 'Smoothie', 0),
(43, 'img_6825c8f48a0dd1.59522512.jpg', 'Greentea Milk Frappe', '150', 'Drink', 'Tea', 'Smoothie', 0),
(44, 'img_6825c91fc955d5.65621533.jpg', 'Macha Greentea Latte', '115', 'Drink', 'Tea', 'Iced', 0),
(45, 'img_6825c954018f93.33104727.jpg', 'Exotic White Tea', '150', 'Drink', 'Tea', 'Iced', 0),
(46, 'img_6825c9f5d21030.71594678.jpg', 'Macha Greentea (No Milk)', '135', 'Drink', 'Tea', 'Iced', 0),
(47, 'img_6825ca2990f678.56672785.jpg', 'Paradise Yusu', '145', 'Drink', 'Fruit', 'Iced', 0),
(48, 'img_6825ca78e5ed09.91914511.jpg', 'Passion Fruit Soda', '105', 'Drink', 'Fruit', 'Iced', 0),
(49, 'img_6825cab6817143.27550526.jpg', ' Apple Peach Soda', '145', 'Drink', 'Fruit', 'Iced', 0),
(50, 'img_6825cb10105127.27986852.jpg', 'Apple Smoothie', '145', 'Drink', 'Fruit', 'Smoothie', 0),
(51, 'img_6825cb48c90867.86720372.jpg', 'Strawberry Smoothie', '120', 'Drink', 'Fruit', 'Smoothie', 0),
(52, 'img_6825cb72385d12.68119711.jpg', 'Lychee Smoothie', '120', 'Drink', 'Fruit', 'Smoothie', 0),
(53, 'img_6825cca7d38da9.10991276.jpg', 'Chocolate Ferrero Toast', '250', 'Recommend', 'Best Sale', NULL, 0),
(55, 'img_68261d7c17aa07.17573173.jpg', 'Kaki Strawberry', '245', 'Dessert', 'Kaki', NULL, 0),
(59, 'img_682eb2e909b941.46683299.jpg', 'Grilled Salmon Fillets Steak', '495', 'Food', 'Main', NULL, 0),
(61, 'img_682ed498f1f489.93815720.jpg', 'Four Cheeses Pizza', '260', 'Food', 'Pizza', NULL, 1),
(63, 'img_682ed599369b55.88733209.jpg', 'Smoked Salmon Spaghetti', '295', 'Food', 'Pasta', NULL, 1),
(64, 'img_682ed5dd039e23.47534373.jpg', 'Tuna & Mushroom Pesto Spagehtti', '195', 'Food', 'Pasta', NULL, 0),
(65, 'img_682ed5ff7b09b9.33488878.jpg', 'Chocolate Ferrero Toast', '250', 'Dessert', 'Toast', NULL, 0),
(66, 'img_682ed639ce8995.32244540.jpg', 'Chocolate Kaki', '189', 'Recommend', 'New', NULL, 0),
(67, 'img_682ed64601b3d7.98296978.jpg', 'Chocolate Kaki', '189', 'Dessert', 'Kaki', NULL, 0),
(68, 'img_682ed8211f0026.52243275.jpg', 'Bacon & Chicken Burger', '265', 'Food', 'Burger', NULL, 0),
(69, 'img_682ed8470a9e77.30961329.jpg', 'Hawaiian Kurobuta Burger', '295', 'Food', 'Burger', NULL, 0),
(70, 'img_682ed88b7cf296.41580634.jpg', 'Caramel Popcorn Toast', '245', 'Dessert', 'Toast', NULL, 1),
(71, 'img_682ed8a6b580e2.27976691.jpg', 'Cheeses Toast', '215', 'Dessert', 'Toast', NULL, 1),
(72, 'img_682ed937612338.83592303.jpg', 'Strawberry Chocolate Crepe', '225', 'Dessert', 'Crepe', NULL, 1),
(73, 'img_682ed95f9ca623.54797701.jpg', 'Banoffee Crepe', '215', 'Dessert', 'Crepe', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `order_details` text DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `table_id` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_name`, `order_details`, `total`, `status`, `created_at`, `table_id`) VALUES
(92, 'PukNv', 'Four Cheeses Pizza (Food) *1 - 260 ฿\nSmoked Salmon Spaghetti (Food) *1 - 295 ฿\nTuna & Mushroom Pesto Spagehtti (Food) *1 - 195 ฿\n', 750.00, 'Completed', '2025-06-11 12:32:22', '3'),
(98, 'PukNv', 'Tuna & Mushroom Pesto Spagehtti (Food) *1 - 195 ฿\nSmoked Salmon Spaghetti (Food) *1 - 295 ฿\nFour Cheeses Pizza (Food) *2 - 520 ฿\n', 1010.00, 'Completed', '2025-06-11 12:57:45', '5'),
(99, 'PukNv', 'Four Cheeses Pizza (Food) *2 - 520 ฿\nSmoked Salmon Spaghetti (Food) *1 - 295 ฿\nHawaiian Pizza (Food) *1 - 290 ฿\n', 1105.00, 'Completed', '2025-06-17 13:22:47', '10'),
(100, 'PukNv', 'Four Cheeses Pizza (Food) *2 - 520 ฿\nSmoked Salmon Spaghetti (Food) *1 - 295 ฿\nHawaiian Pizza (Food) *1 - 290 ฿\n', 1105.00, 'Completed', '2025-06-17 13:22:47', '10'),
(101, 'PukNv', 'Hawaiian Pizza (Food) *1 - 290 ฿\nFour Cheeses Pizza (Food) *1 - 260 ฿\nSmoked Salmon Spaghetti (Food) *2 - 590 ฿\nTuna & Mushroom Pesto Spagehtti (Food) *2 - 390 ฿\n', 1530.00, 'Completed', '2025-06-17 13:23:05', '10'),
(103, 'PukNv', 'Tuna & Mushroom Pesto Spagehtti (Food) *1 - 195 ฿\nSmoked Salmon Spaghetti (Food) *1 - 295 ฿\nFour Cheeses Pizza (Food) *1 - 260 ฿\n', 750.00, 'Completed', '2025-06-17 15:01:34', '10'),
(104, 'PukNv', 'Tuna & Mushroom Pesto Spagehtti (Food) *1 - 195 ฿\nBacon & Chicken Burger (Food) *1 - 265 ฿\nHawaiian Kurobuta Burger (Food) *1 - 295 ฿\n', 755.00, 'Completed', '2025-06-17 15:03:46', '10'),
(105, 'PukNv', 'Chocolate Orange Bomb (Drink) *2 - 370 ฿\nThaitea Milk Frappe (Drink) *1 - 120 ฿\nChocolate Frappe (Drink) *1 - 135 ฿\n', 625.00, 'Completed', '2025-06-17 15:34:54', ''),
(107, 'PukNv', 'Smoked Salmon Spaghetti (Food) *1 - 295 ฿\nFour Cheeses Pizza (Food) *1 - 260 ฿\nTuna & Mushroom Pesto Spagehtti (Food) *1 - 195 ฿\n', 750.00, 'Pending', '2025-06-17 16:35:03', ''),
(108, 'admin', 'Hawaiian Kurobuta Burger (Food) *1 - 295 ฿\nCaramel Popcorn Toast (Dessert) *1 - 245 ฿\nCheeses Toast (Dessert) *1 - 215 ฿\n', 755.00, 'Accepted', '2025-06-17 16:35:16', ''),
(109, 'admin', 'Hawaiian Kurobuta Burger (Food) *1 - 295 ฿\nCaramel Popcorn Toast (Dessert) *1 - 245 ฿\nCheeses Toast (Dessert) *1 - 215 ฿\n', 755.00, 'Accepted', '2025-06-17 16:35:23', ''),
(110, 'admin', 'Caramel Popcorn Toast (Dessert) *1 - 245 ฿\nHawaiian Kurobuta Burger (Food) *1 - 295 ฿\nCheeses Toast (Dessert) *1 - 215 ฿\n', 755.00, 'Accepted', '2025-06-17 16:48:39', ''),
(111, 'admin', 'Hawaiian Kurobuta Burger (Food) *1 - 295 ฿\nCaramel Popcorn Toast (Dessert) *1 - 245 ฿\nCheeses Toast (Dessert) *1 - 215 ฿\n', 755.00, 'Accepted', '2025-06-17 16:49:09', '12'),
(112, 'admin', 'H Cappuccino (Drink) *1 - 95 ฿\nH Late (Drink) *1 - 95 ฿\n', 190.00, 'Accepted', '2025-06-17 16:52:12', '20'),
(114, 'admin', 'Puppy (Dessert) *4 - 740 ฿\n', 740.00, 'Pending', '2025-06-17 16:53:32', '12'),
(115, 'admin', 'Panda Set (Dessert) *4 - 980 ฿\n', 980.00, 'Pending', '2025-06-17 16:53:37', '32'),
(116, 'admin', 'Caramel Popcorn Toast (Dessert) *1 - 245 ฿\nCheeses Toast (Dessert) *1 - 215 ฿\nChocolate Ferrero Toast (Dessert) *1 - 250 ฿\n', 710.00, 'Cancelled', '2025-06-17 16:53:47', '20'),
(117, 'PukNv', 'Tuna & Mushroom Pesto Spagehtti (Food) *1 - 195 ฿\nSmoked Salmon Spaghetti (Food) *1 - 295 ฿\nFour Cheeses Pizza (Food) *1 - 260 ฿\n', 750.00, 'Cancelled', '2025-06-18 13:26:57', '3'),
(118, 'admin', 'Cheeses Toast (Dessert) *1 - 215 ฿\nCaramel Popcorn Toast (Dessert) *1 - 245 ฿\nHawaiian Kurobuta Burger (Food) *1 - 295 ฿\n', 755.00, 'Completed', '2025-06-18 13:27:23', '32'),
(119, 'admin', 'Hawaiian Kurobuta Burger (Food) *1 - 295 ฿\nCaramel Popcorn Toast (Dessert) *1 - 245 ฿\nCheeses Toast (Dessert) *1 - 215 ฿\n', 755.00, 'Accepted', '2025-06-18 13:29:22', '33'),
(120, 'admin', 'Tuna & Mushroom Pesto Spagehtti (Food) *1 - 195 ฿\nSmoked Salmon Spaghetti (Food) *1 - 295 ฿\nGrilled Chicken Breast Steak (Food) *1 - 295 ฿\nGrilled Salmon Fillets Steak (Food) *1 - 495 ฿\nRib-Eye Steak (Food) *1 - 495 ฿\n', 1775.00, 'Completed', '2025-06-18 16:03:58', '30'),
(121, 'PukNv', 'Hawaiian Kurobuta Burger (Food) *1 - 295 ฿\nBacon & Chicken Burger (Food) *1 - 265 ฿\nPuppy (Dessert) *2 - 370 ฿\nMini Black Cat (Dessert) *2 - 450 ฿\n', 1380.00, 'Completed', '2025-06-18 16:04:47', ''),
(122, 'PukNv', 'H Late (Drink) *1 - 95 ฿\nPassion Fruit Soda (Drink) *1 - 105 ฿\nParadise Yusu (Drink) *1 - 145 ฿\nStrawberry Smoothie (Drink) *1 - 120 ฿\nApple Smoothie (Drink) *1 - 145 ฿\n', 610.00, 'Completed', '2025-06-18 16:07:00', ''),
(124, 'PukNv', 'H Late (Drink) *1 - 95 ฿\nH Cappuccino (Drink) *1 - 95 ฿\nH Mocha (Drink) *1 - 110 ฿\n', 300.00, 'Cancelled', '2025-06-19 15:53:06', ''),
(125, 'PukNv', 'Four Cheeses Pizza (Food) *1 - 260 ฿\nSmoked Salmon Spaghetti (Food) *1 - 295 ฿\nTuna & Mushroom Pesto Spagehtti (Food) *1 - 195 ฿\nH Cappuccino (Drink) *1 - 95 ฿\nH Late (Drink) *1 - 95 ฿\n', 940.00, 'Accepted', '2025-06-20 14:33:42', ''),
(126, 'PukNv', 'H Cappuccino (Drink) *3 - 285 ฿\n', 285.00, 'Accepted', '2025-06-20 14:38:30', ''),
(127, 'PukNv', 'Smoked Salmon Spaghetti (Food) *1 - 295 ฿\n', 295.00, 'Accepted', '2025-06-20 14:39:17', ''),
(128, 'Guest', 'H Mocha (Drink) *2 - 220 ฿\n', 220.00, 'Accepted', '2025-06-20 14:47:43', '10'),
(129, 'Guest', 'Iced Cappuccino (Drink) *2 - 200 ฿\n', 200.00, 'Accepted', '2025-06-20 14:53:04', '10'),
(130, 'Guest', 'Four Cheeses Pizza (Food) *1 [ไม่หวานเลย] - 260 ฿\nHawaiian Pizza (Food) *1 - 290 ฿\n', 550.00, 'Accepted', '2025-06-20 15:24:04', '20'),
(131, 'PukNv', 'Iced Cappuccino (Drink) *1 [ไม่หวาน] - 100 ฿\nH Chocolate Mellow (Drink) *1 [หวานน้อย] - 95 ฿\nChocolate Orange Bomb (Drink) *1 [หวานปกติ] - 185 ฿\n', 380.00, 'Accepted', '2025-06-20 15:26:13', '20'),
(132, 'PukNv', 'Smoked Salmon Spaghetti (Food) *1 [123] - 295 ฿\nFour Cheeses Pizza (Food) *1 - 260 ฿\n', 555.00, 'Completed', '2025-06-20 15:34:28', ''),
(133, 'PukNv', 'H Cappuccino (Drink) *1 [หวานร้อย] - 95 ฿\nH Late (Drink) *1 - 95 ฿\n', 190.00, 'Completed', '2025-06-20 15:35:55', ''),
(134, 'PukNv', 'H Late (Drink) *1 [ไม่ใส่นม] - 95 ฿\nH Cappuccino (Drink) *2 - 190 ฿\n', 285.00, 'Completed', '2025-06-20 15:51:39', ''),
(135, 'Guest', 'Smoked Salmon Spaghetti (Food) *1 - 295 ฿\nFour Cheeses Pizza (Food) *1 - 260 ฿\nTuna & Mushroom Pesto Spagehtti (Food) *1 - 195 ฿\n', 750.00, 'Pending', '2025-06-20 16:16:38', '');

-- --------------------------------------------------------

--
-- Table structure for table `table_cm`
--

CREATE TABLE `table_cm` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `phonenum` varchar(255) NOT NULL,
  `seats` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `time` varchar(255) NOT NULL,
  `status` varchar(255) DEFAULT 'กำลังรอดำเนินการ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `table_cm`
--

INSERT INTO `table_cm` (`id`, `username`, `phonenum`, `seats`, `date`, `time`, `status`) VALUES
(13, 'potato015', '096 810 8480', '4', '2025-05-28', '16:00 PM -18:00 PM', 'กำลังรอดำเนินการ'),
(15, 'potato015', '096 810 8480', '8+', '2025-05-30', '19:00 PM -21:00 PM', 'กำลังรอดำเนินการ'),
(16, 'PukNv', '0878305123213', '4-8', '2025-06-19', '12:00 AM -15:00 PM', 'กำลังรอดำเนินการ');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_cm`
--
ALTER TABLE `admin_cm`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_cm`
--
ALTER TABLE `event_cm`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member_cm`
--
ALTER TABLE `member_cm`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_cm`
--
ALTER TABLE `menu_cm`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `table_cm`
--
ALTER TABLE `table_cm`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `event_cm`
--
ALTER TABLE `event_cm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `member_cm`
--
ALTER TABLE `member_cm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `menu_cm`
--
ALTER TABLE `menu_cm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT for table `table_cm`
--
ALTER TABLE `table_cm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
