-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 22, 2025 at 08:41 AM
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
-- Table structure for table `administrator`
--

CREATE TABLE `administrator` (
  `adminID` varchar(10) NOT NULL,
  `adminName` varchar(100) NOT NULL,
  `adminEmail` varchar(20) NOT NULL,
  `adminPassword` varchar(20) NOT NULL,
  `staffID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `administrator`
--

INSERT INTO `administrator` (`adminID`, `adminName`, `adminEmail`, `adminPassword`, `staffID`) VALUES
('admin1', 'NOORAINA LAILATIE', 'admin@petakom.my', 'admin123', 'staff2');

-- --------------------------------------------------------

--
-- Table structure for table `advisor`
--

CREATE TABLE `advisor` (
  `advisorID` varchar(10) NOT NULL,
  `advisorName` varchar(100) NOT NULL,
  `advisorEmail` varchar(20) NOT NULL,
  `advisorPassword` varchar(20) NOT NULL,
  `staffID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `advisor`
--

INSERT INTO `advisor` (`advisorID`, `advisorName`, `advisorEmail`, `advisorPassword`, `staffID`) VALUES
('advisor1', 'MUHAMMAD SYAHMI DANIEL', 'advisor@petakom.my', 'advisor123', 'staff1');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance` varchar(10) NOT NULL,
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
  `attandanceDate` date NOT NULL,
  `eventID` varchar(10) NOT NULL,
  `advisorID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `committee`
--

CREATE TABLE `committee` (
  `committeeID` varchar(10) NOT NULL,
  `position` varchar(100) NOT NULL,
  `eventD` varchar(10) NOT NULL,
  `studentID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `qr_code` varchar(100) NOT NULL,
  `advisorID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `membership`
--

CREATE TABLE `membership` (
  `memebershipID` varchar(10) NOT NULL,
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
  `updated_at` date NOT NULL,
  `studentID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staffID` varchar(10) NOT NULL,
  `staffName` varchar(100) NOT NULL,
  `staffEmail` varchar(20) NOT NULL,
  `staffPassword` varchar(20) NOT NULL,
  `staffRole` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staffID`, `staffName`, `staffEmail`, `staffPassword`, `staffRole`) VALUES
('staff1', 'MUHAMMAD SYAHMI DANIEL', 'advisor@petakom.my', 'advisor123', 'advisor'),
('staff2', 'NOORAINA LAILATIE', 'admin@petakom.my', 'admin123', 'petakom coordinator');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `studentID` varchar(10) NOT NULL,
  `studentName` varchar(80) NOT NULL,
  `studentEmail` varchar(100) NOT NULL,
  `studentPassword` varchar(20) NOT NULL,
  `studentCard` varchar(100) NOT NULL,
  `verify` varchar(20) NOT NULL,
  `qr_code` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`studentID`, `studentName`, `studentEmail`, `studentPassword`, `studentCard`, `verify`, `qr_code`) VALUES
('CA22057', 'ANIS AYU SYAFIQAH BINTI MOHAMAD NABZHAM', 'anisayu@gmail.com', 'anis123', '', '', ''),
('CA22074', 'ISMA IWANI BINTI ISMAIL', 'ismaiwani@gmail.com', 'isma123', '', '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `administrator`
--
ALTER TABLE `administrator`
  ADD PRIMARY KEY (`adminID`),
  ADD KEY `staffID` (`staffID`);

--
-- Indexes for table `advisor`
--
ALTER TABLE `advisor`
  ADD PRIMARY KEY (`advisorID`),
  ADD KEY `staffID` (`staffID`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance`),
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
  ADD KEY `eventD` (`eventD`),
  ADD KEY `studentID` (`studentID`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`eventID`),
  ADD KEY `advisorID` (`advisorID`);

--
-- Indexes for table `membership`
--
ALTER TABLE `membership`
  ADD PRIMARY KEY (`memebershipID`),
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
  ADD PRIMARY KEY (`scoreID`),
  ADD KEY `studentID` (`studentID`);

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
-- Constraints for dumped tables
--

--
-- Constraints for table `administrator`
--
ALTER TABLE `administrator`
  ADD CONSTRAINT `administrator_ibfk_1` FOREIGN KEY (`staffID`) REFERENCES `staff` (`staffID`);

--
-- Constraints for table `advisor`
--
ALTER TABLE `advisor`
  ADD CONSTRAINT `advisor_ibfk_1` FOREIGN KEY (`staffID`) REFERENCES `staff` (`staffID`);

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`slotID`) REFERENCES `attendanceslot` (`slotID`),
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`studentID`) REFERENCES `student` (`studentID`);

--
-- Constraints for table `attendanceslot`
--
ALTER TABLE `attendanceslot`
  ADD CONSTRAINT `attendanceslot_ibfk_1` FOREIGN KEY (`eventID`) REFERENCES `event` (`eventID`),
  ADD CONSTRAINT `attendanceslot_ibfk_2` FOREIGN KEY (`advisorID`) REFERENCES `advisor` (`advisorID`);

--
-- Constraints for table `committee`
--
ALTER TABLE `committee`
  ADD CONSTRAINT `committee_ibfk_1` FOREIGN KEY (`eventD`) REFERENCES `event` (`eventID`),
  ADD CONSTRAINT `committee_ibfk_2` FOREIGN KEY (`studentID`) REFERENCES `student` (`studentID`);

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `event_ibfk_1` FOREIGN KEY (`advisorID`) REFERENCES `advisor` (`advisorID`);

--
-- Constraints for table `membership`
--
ALTER TABLE `membership`
  ADD CONSTRAINT `membership_ibfk_1` FOREIGN KEY (`studentID`) REFERENCES `student` (`studentID`);

--
-- Constraints for table `merit`
--
ALTER TABLE `merit`
  ADD CONSTRAINT `merit_ibfk_1` FOREIGN KEY (`eventID`) REFERENCES `event` (`eventID`),
  ADD CONSTRAINT `merit_ibfk_2` FOREIGN KEY (`studentID`) REFERENCES `student` (`studentID`);

--
-- Constraints for table `meritapplication`
--
ALTER TABLE `meritapplication`
  ADD CONSTRAINT `meritapplication_ibfk_1` FOREIGN KEY (`eventID`) REFERENCES `event` (`eventID`);

--
-- Constraints for table `meritclaim`
--
ALTER TABLE `meritclaim`
  ADD CONSTRAINT `meritclaim_ibfk_1` FOREIGN KEY (`eventID`) REFERENCES `event` (`eventID`),
  ADD CONSTRAINT `meritclaim_ibfk_2` FOREIGN KEY (`studentID`) REFERENCES `student` (`studentID`);

--
-- Constraints for table `meritscore`
--
ALTER TABLE `meritscore`
  ADD CONSTRAINT `meritscore_ibfk_1` FOREIGN KEY (`studentID`) REFERENCES `student` (`studentID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
