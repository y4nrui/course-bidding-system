-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 21, 2019 at 08:28 AM
-- Server version: 5.7.19
-- PHP Version: 7.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `g7t3`
--
CREATE DATABASE IF NOT EXISTS `g7t3` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `g7t3`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `username` char(5) NOT NULL,
  `password` varchar(128) NOT NULL,
  `round` int(1) DEFAULT '1',
  `status` int(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bid`
--

DROP TABLE IF EXISTS `bid`;
CREATE TABLE IF NOT EXISTS `bid` (
  `userid` varchar(128) NOT NULL,
  `amount` float(5,2) NOT NULL,
  `code` varchar(10) NOT NULL,
  `section` varchar(3) NOT NULL,
  `result` varchar(20) DEFAULT 'Pending',
  PRIMARY KEY (`userid`,`code`,`section`),
  KEY `bid_fk2` (`code`,`section`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

DROP TABLE IF EXISTS `course`;
CREATE TABLE IF NOT EXISTS `course` (
  `course` varchar(10) NOT NULL,
  `school` text NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `exam date` date DEFAULT NULL,
  `exam start` time DEFAULT NULL,
  `exam end` time DEFAULT NULL,
  PRIMARY KEY (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `course_completed`
--

DROP TABLE IF EXISTS `course_completed`;
CREATE TABLE IF NOT EXISTS `course_completed` (
  `userid` varchar(128) NOT NULL,
  `code` varchar(10) NOT NULL,
  PRIMARY KEY (`userid`,`code`),
  KEY `cc_fk2` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `enrolment`
--

DROP TABLE IF EXISTS `enrolment`;
CREATE TABLE IF NOT EXISTS `enrolment` (
  `userid` varchar(128) NOT NULL,
  `code` varchar(10) NOT NULL,
  `section` varchar(3) NOT NULL,
  `amount` float(5,2) NOT NULL,
  `round` int(11) NOT NULL,
  PRIMARY KEY (`userid`,`code`,`section`),
  KEY `enrol_fk2` (`code`,`section`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `prerequisite`
--

DROP TABLE IF EXISTS `prerequisite`;
CREATE TABLE IF NOT EXISTS `prerequisite` (
  `course` varchar(10) NOT NULL,
  `prerequisite` varchar(10) NOT NULL,
  PRIMARY KEY (`course`,`prerequisite`),
  KEY `prerequisite_fk2` (`prerequisite`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `round_clear`
--

DROP TABLE IF EXISTS `round_clear`;
CREATE TABLE IF NOT EXISTS `round_clear` (
  `course` varchar(128) NOT NULL,
  `section` varchar(3) NOT NULL,
  `min_bid` decimal(5,2) DEFAULT '10.00',
  `vacancy` int(11) NOT NULL,
  PRIMARY KEY (`course`,`section`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

DROP TABLE IF EXISTS `section`;
CREATE TABLE IF NOT EXISTS `section` (
  `course` varchar(10) NOT NULL,
  `section` varchar(3) NOT NULL,
  `day` char(1) NOT NULL,
  `start` time NOT NULL,
  `end` time NOT NULL,
  `instructor` varchar(100) NOT NULL,
  `venue` varchar(100) NOT NULL,
  `size` int(11) NOT NULL,
  PRIMARY KEY (`course`,`section`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
CREATE TABLE IF NOT EXISTS `student` (
  `userid` varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  `name` varchar(100) NOT NULL,
  `school` text NOT NULL,
  `edollar` float(5,2) NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bid`
--
ALTER TABLE `bid`
  ADD CONSTRAINT `bid_fk1` FOREIGN KEY (`userid`) REFERENCES `student` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bid_fk2` FOREIGN KEY (`code`,`section`) REFERENCES `section` (`course`, `section`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `course_completed`
--
ALTER TABLE `course_completed`
  ADD CONSTRAINT `cc_fk1` FOREIGN KEY (`userid`) REFERENCES `student` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cc_fk2` FOREIGN KEY (`code`) REFERENCES `course` (`course`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `enrolment`
--
ALTER TABLE `enrolment`
  ADD CONSTRAINT `enrol_fk1` FOREIGN KEY (`userid`) REFERENCES `student` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `enrol_fk2` FOREIGN KEY (`code`,`section`) REFERENCES `section` (`course`, `section`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `prerequisite`
--
ALTER TABLE `prerequisite`
  ADD CONSTRAINT `prerequisite_fk1` FOREIGN KEY (`course`) REFERENCES `course` (`course`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prerequisite_fk2` FOREIGN KEY (`prerequisite`) REFERENCES `course` (`course`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `round_clear`
--
ALTER TABLE `round_clear`
  ADD CONSTRAINT `rc_fk` FOREIGN KEY (`course`,`section`) REFERENCES `section` (`course`, `section`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `section`
--
ALTER TABLE `section`
  ADD CONSTRAINT `section_fk` FOREIGN KEY (`course`) REFERENCES `course` (`course`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
