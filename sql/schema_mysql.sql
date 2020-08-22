-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: aching.store.d0m.de:3792
-- Generation Time: Aug 22, 2020 at 11:47 AM
-- Server version: 5.6.42-log
-- PHP Version: 7.2.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mydb`
--

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
CREATE TABLE `locations` (
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `accuracy` int(11) DEFAULT NULL,
  `altitude` int(11) DEFAULT NULL,
  `battery_level` int(11) DEFAULT NULL,
  `heading` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `event` varchar(255) DEFAULT NULL,
  `latitude` decimal(9,6) DEFAULT NULL,
  `longitude` decimal(9,6) DEFAULT NULL,
  `radius` int(11) DEFAULT NULL,
  `trig` varchar(1) DEFAULT NULL,
  `tracker_id` char(2) DEFAULT NULL,
  `epoch` int(11) DEFAULT NULL,
  `vertical_accuracy` int(11) DEFAULT NULL,
  `velocity` int(11) DEFAULT NULL,
  `pressure` decimal(9,6) DEFAULT NULL,
  `connection` varchar(1) DEFAULT NULL,
  `place_id` int(11) DEFAULT NULL,
  `osm_id` int(11) DEFAULT NULL,
  `display_name` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
