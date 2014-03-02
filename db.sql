-- phpMyAdmin SQL Dump
-- version 3.5.2
-- http://www.phpmyadmin.net
--
-- Host: ap01-user01.c0ye1hvnkw6z.ap-southeast-1.rds.amazonaws.com
-- Generation Time: Oct 09, 2013 at 10:24 AM
-- Server version: 5.5.27-log
-- PHP Version: 5.3.10-1ubuntu3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `happy`
--
CREATE DATABASE `happy` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `happy`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `uid` int(11) NOT NULL,
  `rid` int(11) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`uid`, `rid`) VALUES
(68, 32),
(67, 31),
(63, 26),
(34, 1),
(62, 0),
(61, 0),
(69, 0),
(70, 33),
(71, 36),
(73, 37);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE IF NOT EXISTS `customer` (
  `phone` int(11) NOT NULL,
  `point` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  PRIMARY KEY (`phone`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`phone`, `point`, `uid`) VALUES
(1381234567, 0, 85),
(1360000, 0, 84),
(1868, 0, 78),
(8888, 0, 75),
(987, 0, 74),
(52, 0, 72),
(147852, 0, 66),
(2147483647, 0, 64);

-- --------------------------------------------------------

--
-- Table structure for table `queue`
--

CREATE TABLE IF NOT EXISTS `queue` (
  `qid` int(11) NOT NULL AUTO_INCREMENT,
  `rid` int(11) NOT NULL,
  `table` int(11) NOT NULL,
  `phone` bigint(20) DEFAULT NULL,
  `num` int(11) NOT NULL,
  `time` bigint(20) NOT NULL,
  `suppose_arrive_time` bigint(20) DEFAULT NULL,
  `arrive_time` bigint(20) DEFAULT NULL,
  `status` enum('queuing','smsed','arrived','finshed','quited') NOT NULL,
  PRIMARY KEY (`qid`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=100 ;

--
-- Dumping data for table `queue`
--

INSERT INTO `queue` (`qid`, `rid`, `table`, `phone`, `num`, `time`, `suppose_arrive_time`, `arrive_time`, `status`) VALUES
(99, 26, 2, 12345565, 2, 1375877842, 0, 0, 'queuing'),
(98, 26, 2, 155, 2, 1353249365, 0, 0, 'queuing'),
(97, 26, 6, 15046699047, 5, 1353148730, 0, 0, 'queuing'),
(96, 26, 2, 5680, 1, 1353118039, 0, 0, 'queuing'),
(95, 37, 4, 1868, 2, 1353117934, 0, 0, 'smsed'),
(94, 26, 6, 123, 3, 1353117839, 0, 0, 'queuing'),
(93, 1, 2, 1, 2, 1353054864, 0, 0, 'queuing'),
(92, 28, 2, 8888, 2, 1353053950, 0, 0, 'queuing'),
(91, 28, 2, 15114588070, 1, 1353053931, 0, 0, 'queuing'),
(90, 26, 2, 987, 2, 1353053734, 0, 0, 'queuing'),
(89, 1, 4, 12580, 2, 1353053094, 0, 0, 'queuing'),
(88, 37, 4, 15046699047, 3, 1353051931, 0, 0, 'arrived'),
(87, 37, 4, 15040699047, 2, 1353051654, 0, 0, 'arrived'),
(86, 37, 8, 15114588070, 6, 1353051389, 0, 0, 'arrived'),
(85, 36, 4, 18009872345, 3, 1353051291, 0, 0, 'queuing'),
(84, 36, 4, 15114588070, 3, 1353047180, 0, 0, 'arrived'),
(83, 36, 2, 525, 2, 1353047121, 0, 0, 'queuing'),
(82, 36, 2, 52, 2, 1353047022, 0, 0, 'smsed'),
(81, 33, 6, 5, 6, 1353046044, 0, 0, 'arrived'),
(80, 1, 4, 147852, 3, 1352991880, 0, 0, 'queuing'),
(79, 1, 4, 147852, 2, 1352991843, 0, 0, 'queuing'),
(78, 26, 2, 15046699047, 2, 1352988384, 0, 0, 'arrived'),
(77, 26, 2, 15114588070, 1, 1352988223, 0, 0, 'arrived'),
(76, 1, 8, 15114588070, 5, 1352988189, 0, 0, 'arrived');

-- --------------------------------------------------------

--
-- Table structure for table `restaurant`
--

CREATE TABLE IF NOT EXISTS `restaurant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(14) NOT NULL,
  `addr` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `describe` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `time` int(11) NOT NULL,
  `table` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=38 ;

--
-- Dumping data for table `restaurant`
--

INSERT INTO `restaurant` (`id`, `name`, `phone`, `addr`, `describe`, `time`, `table`) VALUES
(1, '永福小吃', '18009872345', '西大直街92号', '好吃不容错过', 10, '2:20;4:10;6:10'),
(26, '工大小吃', '18009872345', '黑龙江省', '特色', 20, '2:10;6:10;'),
(36, '迎宾快餐', '18245132376', '哈工大法院街18号', '各种快餐', 15, '2:5;4:5;'),
(37, '翠花酸菜', '18234566543', '教化街382号', '翠花，上酸菜', 10, '4:10;8:9;');

-- --------------------------------------------------------

--
-- Table structure for table `table`
--

CREATE TABLE IF NOT EXISTS `table` (
  `rid` int(11) NOT NULL,
  `capacity` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `num` int(11) NOT NULL,
  PRIMARY KEY (`rid`,`capacity`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `table`
--

INSERT INTO `table` (`rid`, `capacity`, `time`, `num`) VALUES
(1, 4, 0, 10),
(1, 2, 0, 20),
(33, 6, 0, 10),
(33, 2, 0, 10),
(32, 10, 0, 3),
(32, 5, 0, 23),
(30, 2, 0, 20),
(29, 2, 0, 20),
(28, 2, 0, 20),
(26, 6, 0, 10),
(26, 2, 0, 10),
(27, 2, 0, 20),
(34, 2, 0, 5),
(34, 4, 0, 5),
(35, 2, 0, 5),
(35, 4, 0, 5),
(36, 2, 0, 5),
(36, 4, 0, 5),
(37, 4, 0, 10),
(37, 8, 0, 9),
(1, 6, 0, 10);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `passwd` varchar(64) NOT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=86 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`uid`, `name`, `passwd`) VALUES
(34, '1', '1'),
(85, '1381234567', '1123456'),
(84, '1360000', '1'),
(76, '18645621234', '1'),
(73, '4', '1'),
(82, '15114587777', '1'),
(71, '3', '1'),
(81, '15114588888', '1'),
(80, '15114588078', '1'),
(78, '1868', '11'),
(79, '15114588070', '1'),
(63, '2', '1'),
(83, '13600008888', '1'),
(77, '18645621233', '1');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
