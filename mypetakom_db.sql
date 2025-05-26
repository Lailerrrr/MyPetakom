-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 26, 2025 at 05:14 PM
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
-- Database: `mypetakom_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendanceID` varchar(10) NOT NULL,
  `checkInTime` time NOT NULL,
  `checkInDate` date NOT NULL,
  `location` varchar(100) NOT NULL,
  `slotID` varchar(10) NOT NULL,
  `studentID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendanceslot`
--

CREATE TABLE `attendanceslot` (
  `slotID` varchar(10) NOT NULL,
  `slotName` varchar(100) NOT NULL,
  `slotTime` time NOT NULL,
  `qrCodePath` varchar(255) NOT NULL,
  `attendanceDate` date NOT NULL,
  `eventID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `committee`
--

CREATE TABLE `committee` (
  `committeeID` varchar(10) NOT NULL,
  `role` varchar(100) NOT NULL,
  `eventID` varchar(10) NOT NULL,
  `studentID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `committee`
--

INSERT INTO `committee` (`committeeID`, `role`, `eventID`, `studentID`) VALUES
('CMT6831831', 'Chairperson', 'EVT6830d45', 'CA22074'),
('CMT6831d29', 'Secretary', 'EVT6831d25', 'CA22074');

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `eventName` varchar(100) NOT NULL,
  `eventID` varchar(10) NOT NULL,
  `eventDescription` varchar(100) NOT NULL,
  `eventDate` date NOT NULL,
  `venue` varchar(100) NOT NULL,
  `approvalLetter` varchar(255) NOT NULL,
  `approvalDate` date NOT NULL,
  `status` varchar(20) NOT NULL,
  `qrCode` varchar(100) NOT NULL,
  `staffID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`eventName`, `eventID`, `eventDescription`, `eventDate`, `venue`, `approvalLetter`, `approvalDate`, `status`, `qrCode`, `staffID`) VALUES
('Hari Lahir Saya', 'EVT6830d45', 'dfdfsd', '2025-04-30', 'Dataran', 'uploads/CA22011_Assignment1__1_.pdf', '2025-05-20', 'Active', '', ''),
('Hari Gawai', 'EVT6831d25', 'dfdsfdsfds', '2025-05-13', 'Bilik Guru', 'uploads/CA22011_Assignment__1_.pdf', '2025-05-22', 'Active', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `membership`
--

CREATE TABLE `membership` (
  `membershipID` varchar(10) NOT NULL,
  `studentCard` varchar(100) NOT NULL,
  `status` varchar(20) NOT NULL,
  `studentID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `merit`
--

CREATE TABLE `merit` (
  `meritID` varchar(10) NOT NULL,
  `semester` varchar(10) NOT NULL,
  `academicYear` varchar(10) NOT NULL,
  `totalMerit` int(255) NOT NULL,
  `eventID` varchar(10) NOT NULL,
  `studentID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meritapplication`
--

CREATE TABLE `meritapplication` (
  `meritApplicationID` varchar(10) NOT NULL,
  `appliedDate` date NOT NULL,
  `approvalStatus` varchar(20) NOT NULL,
  `eventID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meritclaim`
--

CREATE TABLE `meritclaim` (
  `claimID` varchar(10) NOT NULL,
  `claimStatus` varchar(20) NOT NULL,
  `claimLetter` varchar(255) NOT NULL,
  `approval_date` date NOT NULL,
  `approval_by` varchar(50) NOT NULL,
  `eventID` varchar(10) NOT NULL,
  `studentID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meritscore`
--

CREATE TABLE `meritscore` (
  `scoreID` int(20) NOT NULL,
  `event_level` varchar(20) NOT NULL,
  `commitRole` varchar(20) NOT NULL,
  `score` int(255) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registration`
--

CREATE TABLE `registration` (
  `registrationID` int(11) NOT NULL,
  `studentID` varchar(20) NOT NULL,
  `eventID` int(11) NOT NULL,
  `registrationDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staffID` varchar(10) NOT NULL,
  `staffName` varchar(100) NOT NULL,
  `staffEmail` varchar(100) NOT NULL,
  `staffPassword` varchar(255) NOT NULL,
  `staffRole` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staffID`, `staffName`, `staffEmail`, `staffPassword`, `staffRole`) VALUES
('staff01', 'NOORAINA LAILATIE BINTI MAZLAN', 'admin@petakom.my', '$2y$10$2x.m/JhhyY4kUXrfXTNy6.3Bpw5gD/fCosUGQFcKKCSAAIu16n4Qa', 'PETAKOM Coordinator'),
('staff02', 'MUHAMMAD SYAHMI DANIEL BIN SHIRMI', 'advisor@petakom.my', '$2y$10$c4rWwCoyXNSn7zkNtULBROE5qwenFxuXL9PVVqNgo8mpLvWm37IM2', 'Event Advisor');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `studentID` varchar(10) NOT NULL,
  `studentName` varchar(80) NOT NULL,
  `studentEmail` varchar(100) NOT NULL,
  `studentPassword` varchar(255) NOT NULL,
  `studentCard` varchar(100) NOT NULL,
  `verify` varchar(20) NOT NULL,
  `qr_code` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`studentID`, `studentName`, `studentEmail`, `studentPassword`, `studentCard`, `verify`, `qr_code`) VALUES
('CA22057', 'ANIS AYU SYAFIQAH BINTI NABZHAM', 'anisayu@gmail.com', '$2y$10$hWydwrNl7FI9b0oHeo4exOcTtZUrDOJeOejoOuLWySz1V1O.HN9vy', '', '', ''),
('CA22074', 'ISMA IWANI BINTI ISMAIL', 'ismaiwani@gmail.com', '$2y$10$jDXIPvaLPbUukmdvNS9fIu1RJWTlB5u3lBSSHuuO1CrG/yUm1LpEq', '', '', ''),
('CA23044', 'ALIYA MAISARA BINTI ANUAR', 'aliyamaisara@gmail.com', '$2y$10$y8eG5dXfTd7NQvkvM5Yp..ASTRb8YlRckduuCJJf2BmkkG0MaOHV6', '', '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendanceID`),
  ADD KEY `slotID` (`slotID`),
  ADD KEY `studentID` (`studentID`);

--
-- Indexes for table `attendanceslot`
--
ALTER TABLE `attendanceslot`
  ADD PRIMARY KEY (`slotID`),
  ADD KEY `eventID` (`eventID`);

--
-- Indexes for table `committee`
--
ALTER TABLE `committee`
  ADD PRIMARY KEY (`committeeID`),
  ADD KEY `eventD` (`eventID`),
  ADD KEY `studentID` (`studentID`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`eventID`),
  ADD KEY `staffID` (`staffID`);

--
-- Indexes for table `membership`
--
ALTER TABLE `membership`
  ADD PRIMARY KEY (`membershipID`),
  ADD KEY `studentID` (`studentID`);

--
-- Indexes for table `merit`
--
ALTER TABLE `merit`
  ADD PRIMARY KEY (`meritID`),
  ADD KEY `eventID` (`eventID`),
  ADD KEY `studentID` (`studentID`);

--
-- Indexes for table `meritapplication`
--
ALTER TABLE `meritapplication`
  ADD PRIMARY KEY (`meritApplicationID`),
  ADD KEY `eventID` (`eventID`);

--
-- Indexes for table `meritclaim`
--
ALTER TABLE `meritclaim`
  ADD PRIMARY KEY (`claimID`),
  ADD KEY `eventID` (`eventID`),
  ADD KEY `studentID` (`studentID`);

--
-- Indexes for table `meritscore`
--
ALTER TABLE `meritscore`
  ADD PRIMARY KEY (`scoreID`);

--
-- Indexes for table `registration`
--
ALTER TABLE `registration`
  ADD PRIMARY KEY (`registrationID`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staffID`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`studentID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `registration`
--
ALTER TABLE `registration`
  MODIFY `registrationID` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
