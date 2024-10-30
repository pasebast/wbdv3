-- phpMyAdmin SQL Dump
-- version 2.9.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Oct 30, 2024 at 06:50 PM
-- Server version: 5.0.27
-- PHP Version: 5.2.1
-- 
-- Database: `bookstore_db`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `books`
-- 

CREATE TABLE `books` (
  `id` int(11) NOT NULL auto_increment,
  `book_title` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `book_image` varchar(255) NOT NULL,
  `book_author` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

-- 
-- Dumping data for table `books`
-- 

INSERT INTO `books` (`id`, `book_title`, `price`, `book_image`, `book_author`) VALUES 
(1, 'The Things You Can See Only When You Slow Down', 659.00, 'https://m.media-amazon.com/images/I/51qXi-sZYrL._SY780_.jpg', 'Haemin Sunim'),
(2, 'Atomic Habits', 1199.00, 'https://cdn.kobo.com/book-images/24463cb4-28ad-48cb-807f-158cf6d11a92/1200/1200/False/atomic-habits-tiny-changes-remarkable-results.jpg', 'James Clear'),
(3, 'The Subtle Art of Not Giving a F*ck', 845.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQzzZW-gz_vtgxuN0f2w_HwDXjbifEdCFxhwg&s', 'Mark Manson'),
(4, 'The Mountain Is You', 1080.00, 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1590806892i/53642699.jpg', 'Brianna Wiest'),
(5, 'A Gentle Reminder', 1029.00, 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1615620038i/57393737.jpg', 'Bianca Sparacino'),
(6, 'The Strength In Our Scars', 1050.00, 'https://assets.literal.club/2/ckrt59p0c2243131esqaoo45u7t.jpg?size=200', 'Bianca Sparacino'),
(7, 'You''re Not Enough (and That''s Okay)', 1450.00, 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1565249516l/51039323.jpg', 'Allie Beth Stuckey'),
(8, 'How to Win Friends & Influence People', 599.00, 'https://i.gr-assets.com/images/S/compressed.photo.goodreads.com/books/1650470724l/59366200.jpg', 'Dale Carnegie'),
(9, 'When You''re Ready, This Is How You Heal', 1125.00, 'https://dynamic.indigoimages.ca/v1/books/books/194975944X/1.jpg?width=810&maxHeight=810&quality=85', 'Brianna Wiest');

-- --------------------------------------------------------

-- 
-- Table structure for table `email_verifications`
-- 

CREATE TABLE `email_verifications` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=28 ;

-- 
-- Dumping data for table `email_verifications`
-- 

INSERT INTO `email_verifications` (`id`, `user_id`, `token`, `created_at`, `expires_at`) VALUES 
(8, 11, '13c61d0a289470a50a9c649b0fc2fe18', '2024-10-20 01:42:19', '2024-10-20 17:42:19'),
(9, 12, '8e38404193b2459d482e3652b498f9c5', '2024-10-20 01:47:23', '2024-10-20 17:47:23'),
(10, 13, '81f3112c8e63d229c1ccd4a74830f2e3', '2024-10-20 01:55:04', '2024-10-20 17:55:04'),
(11, 14, 'e85481e97ddc83177780296c363a6c49', '2024-10-20 02:00:28', '2024-10-20 18:00:28'),
(12, 15, '923917ea297fcf9174edab469f38c577', '2024-10-20 02:03:18', '2024-10-20 18:03:17'),
(13, 16, '92edc35af0cdf6a1e02a254c263733a2', '2024-10-20 02:07:32', '2024-10-20 18:07:32'),
(14, 17, '175686f7acafd943f15b900136880827', '2024-10-20 02:14:35', '2024-10-20 18:14:35'),
(15, 18, '87b83ef4f94f51ee23f554d47c2ca886', '2024-10-20 02:15:39', '2024-10-20 18:15:39'),
(16, 19, '980a546cd3ee2cc5f7ee33e24190b563', '2024-10-20 02:16:51', '2024-10-20 18:16:51'),
(17, 20, '7538dfac1e551231c6f770d72b46a1ab', '2024-10-20 02:19:44', '2024-10-20 18:19:44'),
(18, 21, 'da1b23ccdedbe58faf42230ef044b9ee', '2024-10-20 02:21:24', '2024-10-20 18:21:24'),
(19, 22, 'e8f2ce683ea81ec09fa7555bf5aad210', '2024-10-20 02:28:04', '2024-10-20 18:28:04'),
(20, 23, '31b1a8fe31b5184cc5602b74ecd4ca59', '2024-10-20 02:33:02', '2024-10-20 18:33:02'),
(21, 10, '5bb34fec1556c58957069fa71781b3c7', '2024-10-20 02:38:02', '2024-10-20 18:38:02'),
(22, 10, '44a827c2f6f0b160ae33d52d667cdb6c', '2024-10-20 02:42:47', '2024-10-20 18:42:47'),
(23, 11, 'eaa58f9b2ea3925a58fe3f0aeaafac6b', '2024-10-26 19:38:03', '2024-10-27 11:38:03'),
(24, 12, '2526132dd1b0f1704234f1fd3d6b890a', '2024-10-26 19:40:56', '2024-10-27 11:40:56'),
(25, 12, '46cb5821b19c46779e47713ff51dd79c', '2024-10-26 19:42:11', '2024-10-27 11:42:11'),
(26, 13, '5e66d323c0de0238409e0ea6ec8ff0ad', '2024-10-30 20:33:14', '2024-10-31 12:33:14'),
(27, 14, '718d284a68700e16fc745ee19f3a687f', '2024-10-30 20:41:52', '2024-10-31 12:41:52');

-- --------------------------------------------------------

-- 
-- Table structure for table `order_items`
-- 

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL auto_increment,
  `order_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY  (`item_id`),
  KEY `order_id` (`order_id`),
  KEY `book_id` (`book_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=159 ;

-- 
-- Dumping data for table `order_items`
-- 

INSERT INTO `order_items` (`item_id`, `order_id`, `book_id`, `quantity`, `price`) VALUES 
(141, 7, 2, 3, 1199.00),
(142, 8, 5, 1, 1029.00),
(143, 8, 7, 1, 1450.00),
(144, 9, 1, 1, 659.00),
(145, 9, 2, 1, 1199.00),
(146, 9, 3, 1, 845.00),
(147, 9, 4, 1, 1080.00),
(148, 9, 5, 1, 1029.00),
(149, 9, 6, 1, 1050.00),
(150, 9, 7, 1, 1450.00),
(151, 9, 8, 1, 599.00),
(152, 9, 9, 1, 1125.00),
(153, 10, 3, 2, 845.00),
(154, 10, 5, 1, 1029.00),
(155, 10, 4, 1, 1080.00),
(156, 11, 7, 1, 1450.00),
(157, 11, 8, 1, 599.00),
(158, 11, 6, 1, 1050.00);

-- --------------------------------------------------------

-- 
-- Table structure for table `orders`
-- 

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `order_number` varchar(20) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `payment_method` varchar(20) NOT NULL,
  `order_date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `address` varchar(255) default NULL,
  PRIMARY KEY  (`order_id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- 
-- Dumping data for table `orders`
-- 

INSERT INTO `orders` (`order_id`, `user_id`, `order_number`, `total_price`, `payment_method`, `order_date`, `address`) VALUES 
(7, 14, '6722778cb0558', 3597.00, 'Gcash', '2024-10-31 02:14:36', '6969 Madilim'),
(8, 14, '672277973e2a1', 2479.00, 'Cash on Delivery', '2024-10-31 02:14:47', '6969 Madilim'),
(9, 14, '672277bb2c601', 9036.00, 'Cash on Delivery', '2024-10-31 02:15:23', '123 Tamaraw Hills'),
(10, 12, '672277e613e1f', 3799.00, 'Gcash', '2024-10-31 02:16:06', 'Quezon City'),
(11, 12, '672277ee43ba7', 3099.00, 'Cash on Delivery', '2024-10-31 02:16:14', 'Quezon City');

-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `address` varchar(50) NOT NULL,
  `gender` varchar(50) NOT NULL,
  `birthdate` date NOT NULL,
  `phone_number` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) NOT NULL,
  `account_status` enum('Active','Pending','Deactivated') default 'Pending',
  `role` enum('member','admin') NOT NULL default 'member',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

-- 
-- Dumping data for table `users`
-- 

INSERT INTO `users` (`id`, `firstname`, `lastname`, `username`, `address`, `gender`, `birthdate`, `phone_number`, `password`, `profile_picture`, `account_status`, `role`) VALUES 
(12, 'Jamier Ivan', 'Madrid', 'jmadrid3899val@student.fatima.edu.ph', 'Quezon City', 'Male', '2024-10-01', '09603081740', '81dc9bdb52d04dc20036dbd8313ed055', 'PIC_MADRID_JAMIERIVAN.jpg', 'Active', 'member'),
(14, 'Paul', 'Pharmacy', 'ptsebastian6585val@student.fatima.edu.ph', '123 Tamaraw Hills', 'Male', '1994-09-29', '09053312618', 'd77d1c8fd85502a8fe5858da6bd44446', 'phy13.jpg', 'Active', 'admin');

-- 
-- Constraints for dumped tables
-- 

-- 
-- Constraints for table `order_items`
-- 
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`);

-- 
-- Constraints for table `orders`
-- 
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
