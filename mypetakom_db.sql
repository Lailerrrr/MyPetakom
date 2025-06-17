-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2025 at 02:58 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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

--
-- Dumping data for table `attendanceslot`
--

INSERT INTO `attendanceslot` (`slotID`, `slotName`, `slotTime`, `qrCodePath`, `attendanceDate`, `eventID`) VALUES
('S002', 'Hari Lahir Saya', '10:00:00', 'slot_S002.png', '2025-04-30', 'EVT6830d45'),
('S004', 'Hari Gawai', '09:00:00', 'slot_S004.png', '2025-05-13', 'EVT6831d25'),
('S005', 'Hari Gawai', '08:00:00', 'slot_S005.png', '2025-05-13', 'EVT6831d25'),
('S006', 'Ceramah', '10:06:00', 'slot_S006.png', '2025-06-10', 'EVT6851584');

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
('CMT6831d29', 'Secretary', 'EVT6831d25', 'CA22074'),
('CMT6851585', 'Chairperson', 'EVT6851584', 'CA22057'),
('CMT68515ff', 'Attendee', 'EVT68515fd', 'CA22057'),
('CMT6851602', 'Chairperson', 'EVT68515fd', 'CA22074'),
('CMT6851615', 'Chairperson', 'EVT6851584', 'CA24098'),
('CMT6851623', 'Secretary', 'EVT6851623', 'CA22057'),
('CMT6851639', 'Chairperson', 'EVT6851638', 'CA22057'),
('CMT6851644', 'Chairperson', 'EVT6851638', 'CA22074');

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `eventName` varchar(100) DEFAULT NULL,
  `eventID` varchar(10) NOT NULL,
  `eventDescription` varchar(100) NOT NULL,
  `eventDate` date NOT NULL,
  `venue` varchar(100) NOT NULL,
  `approvalLetter` varchar(255) NOT NULL,
  `approvalDate` date NOT NULL,
  `status` varchar(20) NOT NULL,
  `qrCode` varchar(100) NOT NULL,
  `eventLevel` varchar(255) NOT NULL,
  `staffID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`eventName`, `eventID`, `eventDescription`, `eventDate`, `venue`, `approvalLetter`, `approvalDate`, `status`, `qrCode`, `eventLevel`, `staffID`) VALUES
('Hari Lahir Saya', 'EVT6830d45', 'dfdfsd', '2025-04-30', 'Dataran', 'uploads/CA22011_Assignment1__1_.pdf', '2025-05-20', 'Active', '', '', ''),
('Hari Gawai', 'EVT6831d25', 'dfdsfdsfds', '2025-05-13', 'Bilik Guru', 'uploads/CA22011_Assignment__1_.pdf', '2025-05-22', 'Active', '', '', ''),
('Ceramah', 'EVT6851584', 'UAI', '2025-06-10', 'MASJID UMPSA', 'uploads/CA22050_DNS_Individual_Progress_Sheet.pdf', '2025-06-04', 'Active', '', 'UMPSA', 'staff02'),
('PETAKOM MEETING', 'EVT68515fd', 'meeting', '2025-06-18', 'bilik petakom', 'uploads/CA22050_WEBE.pdf', '2025-06-11', 'Active', '', 'UMPSA', 'staff02'),
('Sambutan Hari Raya', 'EVT6851623', 'Raya Fakulti', '2025-05-07', 'FK', 'uploads/CA22050_WEBE.pdf', '2025-05-01', 'Active', '', 'UMPSA', 'staff02'),
('ICINO', 'EVT6851638', 'fgr', '2025-06-10', 'Dewan Pekan', 'uploads/CA22050_LAILATIE_DAV.pdf', '2025-06-10', 'Active', '', 'UMPSA', 'staff02');

-- --------------------------------------------------------

--
-- Table structure for table `membership`
--

CREATE TABLE `membership` (
  `membershipID` varchar(10) NOT NULL,
  `studentCard` varchar(100) NOT NULL,
  `status` varchar(20) NOT NULL,
  `studentID` varchar(10) NOT NULL,
  `apply_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `membership`
--

INSERT INTO `membership` (`membershipID`, `studentCard`, `status`, `studentID`, `apply_at`) VALUES
('M68361146c', 'card_68361146ce8762.83616948.jpg', 'Pending', 'CA22057', '2025-05-28 03:23:50'),
('M683611b3b', 'card_683611b3bb76a0.48429624.jpg', 'Pending', 'CA22074', '2025-05-28 03:25:39');

-- --------------------------------------------------------

--
-- Table structure for table `merit`
--

CREATE TABLE `merit` (
  `meritID` varchar(10) NOT NULL,
  `semester` varchar(10) NOT NULL,
  `academicYear` varchar(10) NOT NULL,
  `totalMerit` int(11) NOT NULL DEFAULT 0,
  `eventID` varchar(255) DEFAULT NULL,
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

--
-- Dumping data for table `meritapplication`
--

INSERT INTO `meritapplication` (`meritApplicationID`, `appliedDate`, `approvalStatus`, `eventID`) VALUES
('MA90', '2025-06-17', 'Approved', 'EVT6851584');

-- --------------------------------------------------------

--
-- Table structure for table `meritclaim`
--

CREATE TABLE `meritclaim` (
  `claimID` varchar(10) NOT NULL,
  `claimStatus` varchar(20) NOT NULL,
  `claimLetter` varchar(255) NOT NULL,
  `approval_date` datetime DEFAULT NULL,
  `approval_by` varchar(50) DEFAULT NULL,
  `eventID` varchar(10) NOT NULL,
  `studentID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meritclaim`
--

INSERT INTO `meritclaim` (`claimID`, `claimStatus`, `claimLetter`, `approval_date`, `approval_by`, `eventID`, `studentID`) VALUES
('CLM6835b9a', 'Approved', 'bukti dns.pdf', '2025-06-17 14:22:27', 'staff02', 'EVT6830d45', 'CA22036'),
('CLM6835b9e', 'Approved', 'CA22050_LAILATIE_DAV.pdf', '2025-06-17 14:22:28', 'staff02', 'EVT6831d25', 'CA22079'),
('CLM6835e22', 'Approved', 'CA22050_LAB3NETMANAGEMENT.pdf', '2025-06-17 14:22:28', 'staff02', 'EV5678', 'CA22057'),
('CLM6835e2d', 'Approved', 'CA22050_LAB3NETMANAGEMENT.pdf', '2025-06-17 14:22:29', 'staff02', 'EV5678', 'CA22057'),
('CLM6851591', 'Approved', 'CA22050_WEBE.pdf', '2025-06-17 14:01:39', 'staff02', 'EVT6851584', 'CA22057');

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

--
-- Dumping data for table `meritscore`
--

INSERT INTO `meritscore` (`scoreID`, `event_level`, `commitRole`, `score`, `created_at`, `updated_at`) VALUES
(1, 'International', 'Main Committee', 100, '2025-06-17', '2025-06-17'),
(2, 'International', 'Committee', 70, '2025-06-17', '2025-06-17'),
(3, 'International', 'Participant', 50, '2025-06-17', '2025-06-17'),
(4, 'National', 'Main Committee', 80, '2025-06-17', '2025-06-17'),
(5, 'National', 'Committee', 50, '2025-06-17', '2025-06-17'),
(6, 'National', 'Participant', 40, '2025-06-17', '2025-06-17'),
(7, 'State', 'Main Committee', 60, '2025-06-17', '2025-06-17'),
(8, 'State', 'Committee', 40, '2025-06-17', '2025-06-17'),
(9, 'State', 'Participant', 30, '2025-06-17', '2025-06-17'),
(10, 'District', 'Main Committee', 40, '2025-06-17', '2025-06-17'),
(11, 'District', 'Committee', 30, '2025-06-17', '2025-06-17'),
(12, 'District', 'Participant', 15, '2025-06-17', '2025-06-17'),
(13, 'UMPSA', 'Main Committee', 30, '2025-06-17', '2025-06-17'),
(14, 'UMPSA', 'Committee', 20, '2025-06-17', '2025-06-17'),
(15, 'UMPSA', 'Participant', 5, '2025-06-17', '2025-06-17');

-- --------------------------------------------------------

--
-- Table structure for table `registration`
--

CREATE TABLE `registration` (
  `registrationID` int(11) NOT NULL,
  `studentID` varchar(20) NOT NULL,
  `eventID` varchar(10) DEFAULT NULL,
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
('CA22057', 'ANIS AYU SYAFIQAH BINTI NABZHAM', 'anisayu@gmail.com', '$2y$10$DuNYrF4XgJkkDFBSI27oq.dtdJQRVRVuZemgAPehkjG.edJ.dro1G', '', 'Not Applied', ''),
('CA22074', 'ISMA IWANI BINTI ISMAIL', 'ismaiwani@gmail.com', '$2y$10$HZHsJrO6ASIqSwvfmtUsFuiU7Z1CqZ1T6dmJY/h04gGjfHtxBXInW', '', 'Not Applied', ''),
('CA23098', 'IZZAH ALIA BINTI ALI', 'izzah@gmail.com', '$2y$10$03zOvTair/gv68CpQyHnj.mhc52B2NL8olJkiKRbL6idM6RBYjsGm', '', 'Not Applied', ''),
('CA24098', 'ALIYA MAISARA BINTI ANUAR', 'aliyamaisara@gmail.com', '$2y$10$.Bk1KBYXgzjhbZ95qFQvyOE/i6VWaWgG.t8ARD8yZ0jlU/yyf.wZy', '', 'Not Applied', '');

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
