-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 06, 2017 at 09:29 PM
-- Server version: 5.5.54-0+deb8u1
-- PHP Version: 7.0.16-1~dotdeb+8.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dotrox_test_tanhit`
--

-- --------------------------------------------------------

--
-- Table structure for table `wp_instagram`
--

DROP TABLE IF EXISTS `wp_instagram`;
CREATE TABLE IF NOT EXISTS `wp_instagram` (
  `inst_id` bigint(20) unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `inst_user_id` bigint(20) unsigned NOT NULL,
  `inst_username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `full_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `profile_picture` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `inst_link_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `inst_link` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `images` text COLLATE utf8_unicode_ci NOT NULL,
  `caption` text COLLATE utf8_unicode_ci NOT NULL,
  `tags` text COLLATE utf8_unicode_ci,
  `comments_count` int(10) unsigned NOT NULL,
  `comments_data` text COLLATE utf8_unicode_ci,
  `likes` int(10) unsigned NOT NULL,
  `videos` text COLLATE utf8_unicode_ci,
  `created_time` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `wp_instagram`
--
ALTER TABLE `wp_instagram`
 ADD PRIMARY KEY (`inst_id`), ADD UNIQUE KEY `link_id` (`inst_link_id`), ADD KEY `user_id` (`inst_user_id`,`inst_username`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
