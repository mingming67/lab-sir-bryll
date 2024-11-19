-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 11, 2024 at 09:02 PM
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
-- Database: `university_db3`
--

-- --------------------------------------------------------

--
-- Table structure for table `classroom`
--

CREATE TABLE `classroom` (
  `building` varchar(50) NOT NULL,
  `room_number` varchar(10) NOT NULL,
  `capacity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classroom`
--

INSERT INTO `classroom` (`building`, `room_number`, `capacity`) VALUES
('EMM', '51', 22),
('FGM', '22', 21);

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `course_code` varchar(10) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `credits` int(11) DEFAULT NULL,
  `dept_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `dept_name` varchar(100) NOT NULL,
  `building` varchar(100) NOT NULL,
  `budget` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`dept_name`, `building`, `budget`) VALUES
('ITE', 'FGM', 24);

-- --------------------------------------------------------

--
-- Table structure for table `instructor`
--

CREATE TABLE `instructor` (
  `ID` int(10) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `street_number` varchar(10) DEFAULT NULL,
  `street_name` varchar(100) DEFAULT NULL,
  `apt_number` varchar(10) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `province` varchar(50) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `dept_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `instructor`
--

INSERT INTO `instructor` (`ID`, `first_name`, `middle_name`, `last_name`, `street_number`, `street_name`, `apt_number`, `city`, `province`, `postal_code`, `date_of_birth`, `salary`, `dept_name`) VALUES
(2011810023, 'tanskie', 'tan', 'elevencionado', '555', 'yumang', '21', 'general santos city', 'gasc', '1231', '2002-08-22', NULL, NULL),
(2147483647, 'migueleleven', 'tan', 'eleven', '555', 'yumang', '123', 'gsc', 'gasc', '123', '2002-08-22', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `ID` bigint(10) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `street_number` varchar(20) DEFAULT NULL,
  `street_name` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `tot_credit` int(11) DEFAULT NULL,
  `dept_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `time_slot`
--

CREATE TABLE `time_slot` (
  `time_slot_id` int(11) NOT NULL,
  `day` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `time_slot`
--

INSERT INTO `time_slot` (`time_slot_id`, `day`, `start_time`, `end_time`) VALUES
(11, 'Monday', '11:00:00', '01:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `classroom`
--
ALTER TABLE `classroom`
  ADD PRIMARY KEY (`building`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`course_code`),
  ADD KEY `course_ibfk_1` (`dept_name`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`dept_name`),
  ADD KEY `building` (`building`);

--
-- Indexes for table `instructor`
--
ALTER TABLE `instructor`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `dept_name` (`dept_name`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `dept_name` (`dept_name`);

--
-- Indexes for table `time_slot`
--
ALTER TABLE `time_slot`
  ADD PRIMARY KEY (`time_slot_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `time_slot`
--
ALTER TABLE `time_slot`
  MODIFY `time_slot_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `course`
--
ALTER TABLE `course`
  ADD CONSTRAINT `course_ibfk_1` FOREIGN KEY (`dept_name`) REFERENCES `department` (`dept_name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `instructor`
--
ALTER TABLE `instructor`
  ADD CONSTRAINT `instructor_ibfk_1` FOREIGN KEY (`dept_name`) REFERENCES `department` (`dept_name`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`dept_name`) REFERENCES `department` (`dept_name`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
