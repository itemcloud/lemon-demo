-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 03, 2019 at 11:21 PM
-- Server version: 10.0.36-MariaDB-0ubuntu0.16.04.1
-- PHP Version: 7.0.32-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `itemcloud-db`
--
CREATE DATABASE IF NOT EXISTS `itemcloud-db` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `itemcloud-db`;

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `item_id` int(11) NOT NULL,
  `class_id` int(16) NOT NULL DEFAULT '1',
  `title` varchar(240) NOT NULL,
  `info` varchar(9600) NOT NULL,
  `file` varchar(2400) CHARACTER SET latin1 NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `item_class`
--

CREATE TABLE `item_class` (
  `class_id` int(16) NOT NULL,
  `class_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `item_class`
--

INSERT INTO `item_class` (`class_id`, `class_name`) VALUES
(1, 'note'),
(2, 'link'),
(3, 'file'),
(4, 'photo'),
(5, 'audio'),
(6, 'video');

-- --------------------------------------------------------

--
-- Table structure for table `item_nodes`
--

CREATE TABLE `item_nodes` (
  `entry` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `node_name` varchar(128) CHARACTER SET latin1 NOT NULL,
  `length` int(11) NOT NULL,
  `required` int(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `item_nodes`
--

INSERT INTO `item_nodes` (`entry`, `class_id`, `node_name`, `length`, `required`) VALUES
(1, 1, 'title', 140, 1),
(2, 1, 'info', 4000, NULL),
(3, 2, 'title', 140, NULL),
(4, 2, 'info', 4000, NULL),
(5, 2, 'file', 6000, 1),
(6, 4, 'title', 140, NULL),
(7, 4, 'info', 4000, NULL),
(8, 4, 'file', 6000, 1),
(12, 3, 'title', 140, NULL),
(13, 3, 'info', 4000, NULL),
(14, 3, 'file', 6000, 1),
(15, 5, 'title', 140, NULL),
(16, 5, 'info', 4000, NULL),
(17, 5, 'file', 6000, 1);

-- --------------------------------------------------------

--
-- Table structure for table `item_type`
--

CREATE TABLE `item_type` (
  `entry` int(11) NOT NULL,
  `class_id` int(16) NOT NULL,
  `file_type` text NOT NULL,
  `ext` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `item_type`
--

INSERT INTO `item_type` (`entry`, `class_id`, `file_type`, `ext`) VALUES
(201, 3, 'application/pdf', 'pdf'),
(202, 4, 'image/jpeg', 'jpg'),
(203, 5, 'audio/mpeg', 'mp3'),
(204, 6, 'video/mpeg', 'mp4'),
(205, 4, 'image/png', 'png'),
(206, 4, 'image/gif', 'gif'),
(207, 3, 'application/zip', 'zip');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `email` varchar(100) CHARACTER SET latin1 NOT NULL,
  `password` varchar(100) CHARACTER SET latin1 NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_auth`
--

CREATE TABLE `user_auth` (
  `user_id` int(11) NOT NULL,
  `level` int(11) NOT NULL DEFAULT '3',
  `email_alerts` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_items`
--

CREATE TABLE `user_items` (
  `entry` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_labels`
--

CREATE TABLE `user_labels` (
  `entry` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `label_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `item_class`
--
ALTER TABLE `item_class`
  ADD PRIMARY KEY (`class_id`);

--
-- Indexes for table `item_nodes`
--
ALTER TABLE `item_nodes`
  ADD PRIMARY KEY (`entry`);

--
-- Indexes for table `item_type`
--
ALTER TABLE `item_type`
  ADD PRIMARY KEY (`entry`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_auth`
--
ALTER TABLE `user_auth`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_items`
--
ALTER TABLE `user_items`
  ADD PRIMARY KEY (`entry`);

--
-- Indexes for table `user_labels`
--
ALTER TABLE `user_labels`
  ADD PRIMARY KEY (`entry`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_class`
--
ALTER TABLE `item_class`
  MODIFY `class_id` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `item_nodes`
--
ALTER TABLE `item_nodes`
  MODIFY `entry` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `item_type`
--
ALTER TABLE `item_type`
  MODIFY `entry` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=208;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_items`
--
ALTER TABLE `user_items`
  MODIFY `entry` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_labels`
--
ALTER TABLE `user_labels`
  MODIFY `entry` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
