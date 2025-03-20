-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 20, 2025 at 11:07 AM
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
-- Database: `eduwide`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `nic` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT 'uploads/profile_pictures/default.png',
  `facebook` varchar(200) NOT NULL,
  `github` varchar(200) NOT NULL,
  `linkedin` varchar(200) NOT NULL,
  `blog` varchar(200) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `nic`, `email`, `mobile`, `password`, `profile_picture`, `facebook`, `github`, `linkedin`, `blog`, `created_at`, `status`) VALUES
(2, 'Malitha Tishamal', '200202226251', 'malithatishamal@gmail.com', '785530992', '$2y$10$/62hdNg8q8XxoL3FIs..CeJ2YKN6fXpHIQ3RSqYjxFbdkV07BV1ne', 'uploads/profile_pictures/67d2ddfb6f751-411001152_1557287805017611_3900716309730349802_n.jpg', 'https://www.facebook.com/malitha.tishamal', '', '', '', '2025-03-08 14:00:46', 'approved'),
(6, 'Amandi Kaushalya', '200302226252', 'dewmikaushalya1234@gmail.com', '788167038', '$2y$10$xgNjt6h20gEiulpGjE.nhetLHADzKYaaPm8T10xUItb.pKpBLca02', 'uploads/profile_pictures/67d53d2856fc3-amandi.jpg', '', '', '', '', '2025-03-15 08:37:02', 'approved'),
(7, 'Malith Sandeepa', '200302222621', 'malithsandeepa1081@gmail.com', '763279285', '$2y$10$Zyvk/dSOi1dHdANEVn7U/OK5zpHUcQW/6TKtjgn.Ygj8.6nQcFc4S', 'uploads/profile_pictures/67d53d10d7189-sandeepa.jpg', '', '', '', '', '2025-03-15 08:39:36', 'approved'),
(8, 'Matheesha Nihari', '200350222621', 'matheenihari13@gmail.com', '775751107', '$2y$10$2WugusGnWNorfagraUGan.sRr0SFF9h5ScXcOVQVVWR7HWFngufi2', 'uploads/profile_pictures/67d568d3e277b-nihari.jpg', '', '', '', '', '2025-03-15 11:42:54', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `former_students`
--

CREATE TABLE `former_students` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `reg_id` varchar(255) NOT NULL,
  `nic` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT 'uploads/profile_pictures/default.png',
  `mobile` varchar(15) NOT NULL,
  `study_year` int(11) NOT NULL,
  `nowstatus` enum('study','work') NOT NULL,
  `university` varchar(255) DEFAULT NULL,
  `course_name` varchar(255) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `job_type` varchar(100) DEFAULT NULL,
  `facebook` varchar(200) NOT NULL,
  `github` varchar(200) NOT NULL,
  `blog` varchar(200) NOT NULL,
  `linkedin` varchar(200) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `former_students`
--

INSERT INTO `former_students` (`id`, `username`, `reg_id`, `nic`, `email`, `profile_picture`, `mobile`, `study_year`, `nowstatus`, `university`, `course_name`, `country`, `company_name`, `position`, `job_type`, `facebook`, `github`, `blog`, `linkedin`, `password`, `status`, `created_at`, `updated_at`) VALUES
(8, 'malitha', 'test', '200202226251', 'malithatishamal2004@gmail.com', 'uploads/profile_pictures/default.png', '771000000', 2016, 'work', '', '', '', 'test', 'test', 'test', '', '', '', '', '$2y$10$zZ1O90SstwpxKQ9GZnN2Q.bel0soNxe5HnlLr.HKhEem.vJ1ymxrO', 'approved', '2025-03-08 15:18:33', '2025-03-11 14:40:54'),
(9, 'malitha2', 'tst', '200205526251', 'malithatishamal2005@gmail.com', 'uploads/profile_pictures/default.png', '771000400', 2016, 'study', 'test', 'test', 'test', '', '', '', '', '', '', '', '$2y$10$iiq5FbEkkSPCP1zUMhxPXue5mhLQ1W52JguJ3Gu9OJJZ/Yy65kmku', 'approved', '2025-03-08 15:21:06', '2025-03-14 16:26:24');

-- --------------------------------------------------------

--
-- Table structure for table `lectures`
--

CREATE TABLE `lectures` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `nic` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `linkedin` varchar(200) NOT NULL,
  `blog` varchar(200) NOT NULL,
  `github` varchar(200) NOT NULL,
  `facebook` varchar(200) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT 'uploads/profile_pictures/default.png',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lectures`
--

INSERT INTO `lectures` (`id`, `username`, `nic`, `email`, `mobile`, `linkedin`, `blog`, `github`, `facebook`, `password`, `profile_picture`, `created_at`, `status`) VALUES
(1, 'Malitha Tishamal', '200202222625', 'malithatishamal2002@gmail.com', '771000000', '', '', '', '', '$2y$10$nAqaq8RBfoo27.k8lXCXoeddeIdMbgxGOZBdcb0uyfYSzYX/TOY9m', 'uploads/profile_pictures/67d339351d57b-PXL_20250110_080305123.jpg', '2025-03-08 14:06:37', 'approved'),
(2, 'demo test name', '200202222620', 'demo3@gmail.com', '771000001', '', '', '', '', '$2y$10$o1blitQ6SNQ27paWgBo6V.Gsk4dg0oH7Hw0CTR85h.Ub9sRVX0K2C', 'uploads/profile_pictures/default.png', '2025-03-12 13:54:39', 'disabled');

-- --------------------------------------------------------

--
-- Table structure for table `lectures_assignment`
--

CREATE TABLE `lectures_assignment` (
  `id` int(11) NOT NULL,
  `lecturer_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lectures_assignment`
--

INSERT INTO `lectures_assignment` (`id`, `lecturer_id`, `subject_id`) VALUES
(3, 1, 2),
(4, 2, 4),
(5, 2, 8),
(6, 2, 12),
(11, 1, 6);

-- --------------------------------------------------------

--
-- Table structure for table `marks`
--

CREATE TABLE `marks` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `year` varchar(4) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `semester` varchar(50) NOT NULL,
  `practical_marks` int(11) NOT NULL,
  `paper_marks` int(11) NOT NULL,
  `special_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `marks`
--

INSERT INTO `marks` (`id`, `student_id`, `year`, `subject`, `semester`, `practical_marks`, `paper_marks`, `special_notes`, `created_at`) VALUES
(1, 'gal-it-2023-f-0000', '2023', 'Operating Systems', 'Semester III', 60, 70, 'test', '2025-03-15 11:11:28'),
(2, 'gal-it-2023-f-0000', '2023', 'Fundamentals of Programming', 'Semester II', 50, 20, '', '2025-03-15 11:24:20'),
(3, 'gal-it-2023-f-0000', '2023', 'Data Structures and Algorithms', 'Semester III', 0, 10, '', '2025-03-15 11:26:20'),
(4, 'gal-it-2023-f-0000', '2023', 'Statistics for IT', 'Semester III', 20, 50, '', '2025-03-15 11:31:17'),
(5, 'gal-it-2023-f-0009', '2023', 'Data communication and Computer Networks', 'Semester II', 50, 90, '', '2025-03-15 11:52:02'),
(6, 'gal-it-2023-f-0009', '2023', 'Web Design', 'Semester I', 70, 30, '', '2025-03-16 10:57:55');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `reg_id` varchar(100) NOT NULL,
  `nic` varchar(20) NOT NULL,
  `study_year` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `facebook` varchar(200) NOT NULL,
  `github` varchar(200) NOT NULL,
  `blog` varchar(200) NOT NULL,
  `linkedin` varchar(200) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT 'uploads/profile_pictures/default.png',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `username`, `reg_id`, `nic`, `study_year`, `email`, `mobile`, `facebook`, `github`, `blog`, `linkedin`, `password`, `profile_picture`, `created_at`, `status`) VALUES
(2, 'testname', 'gal-it-2023-f-0000', '200202226251', 2023, 'malithatishamal2003@gmail.com', '771000001', 'test', '', '', '', '$2y$10$gGb6Ubgv92I8Negpm0I1cOMt.CIviySPLLmOOTBkCC9lwbiP8E9BW', 'uploads/profile_pictures/67d6a45387e39-msi-red-dragon-and-black-background-wallpaper-1280x1024_32.jpg', '2025-03-08 14:28:05', 'approved'),
(4, 'malitha', 'gal-it-2023-f-0009', '200202222620', 2023, 'demo@gmail.com', '771000001', '', '', '', '', '$2y$10$12qvgV6sFTQCRld5kmFjKexu1E/v7CwlWVx5iqh4Z2ppBSIwoiPz.', 'uploads/profile_pictures/default.png', '2025-03-12 15:05:01', 'pending'),
(5, 'testname', 'gal-it-2022-f-0009', '200202222625', 2022, 'demo3@gmail.com', '771000004', '', '', '', '', '$2y$10$WDbnrOQK7WBWAQPayrPB6OAT6IY/RTSn8zzNgxpBGGZ1vR/Lk2NPi', 'uploads/profile_pictures/default.png', '2025-03-15 05:15:35', 'approved'),
(6, 'Malitha', 'gal-it-2023-f-0010', '200202222621', 2024, 'demo2@gmail.com', '771000005', '', '', '', '', '$2y$10$oWqdPj119osZq/kBx.l6su1bK8uncy4dYyG7wKn56SWtiBzbOu4Aq', 'uploads/profile_pictures/default.png', '2025-03-15 05:21:09', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `semester` varchar(50) DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `semester`, `code`, `name`, `description`) VALUES
(1, 'Semester I', 'HNDIT1012', 'Visual Application Programming', 'Core | GPA'),
(2, 'Semester I', 'HNDIT1022', 'Web Design', 'Core | GPA'),
(3, 'Semester I', 'HNDIT1032', 'Computer and Network Systems', 'Core | GPA'),
(4, 'Semester I', 'HNDIT1042', 'Information Management and Information Systems', 'Core | GPA'),
(5, 'Semester I', 'HNDIT1052', 'ICT Project (Individual)', 'Core | GPA'),
(6, 'Semester I', 'HNDIT1062', 'Communication Skills', 'Core | GPA'),
(7, 'Semester II', 'HNDIT2012', 'Fundamentals of Programming', 'Core | GPA'),
(8, 'Semester II', 'HNDIT2022', 'Software Development', 'Core | GPA'),
(9, 'Semester II', 'HNDIT2032', 'System Analysis and Design', 'Core | GPA'),
(10, 'Semester II', 'HNDIT2042', 'Data communication and Computer Networks', 'Core | GPA'),
(11, 'Semester II', 'HNDIT2052', 'Principles of User Interface Design', 'Core | GPA'),
(12, 'Semester II', 'HNDIT2062', 'ICT Project (Group)', 'Core | GPA'),
(13, 'Semester II', 'HNDIT2072', 'Technical Writing', 'Core | GPA'),
(14, 'Semester II', 'HNDIT2082', 'Human Value & Professional Ethics', 'Core | NGPA'),
(15, 'Semester III', 'HNDIT3012', 'Object Oriented Programming', 'Core | GPA'),
(16, 'Semester III', 'HNDIT3022', 'Web Programming', 'Core | GPA'),
(17, 'Semester III', 'HNDIT3032', 'Data Structures and Algorithms', 'Core | GPA'),
(18, 'Semester III', 'HNDIT3042', 'Database Management Systems', 'Core | GPA'),
(19, 'Semester III', 'HNDIT3052', 'Operating Systems', 'Core | GPA'),
(20, 'Semester III', 'HNDIT3062', 'Information and Computer Security', 'Core | GPA'),
(21, 'Semester III', 'HNDIT3072', 'Statistics for IT', 'Core | GPA'),
(22, 'Semester IV', 'HNDIT4012', 'Software Engineering', 'Core | GPA'),
(23, 'Semester IV', 'HNDIT4022', 'Software Quality Assurance', 'Core | GPA'),
(24, 'Semester IV', 'HNDIT4032', 'IT Project Management', 'Core | GPA'),
(25, 'Semester IV', 'HNDIT4042', 'Professional World', 'Core | GPA'),
(26, 'Semester IV', 'HNDIT4052', 'Programming Individual Project', 'Core | GPA'),
(27, 'Semester IV', 'HNDIT4212', 'Machine Learning', 'Elective | GPA'),
(28, 'Semester IV', 'HNDIT4222', 'Business Analysis Practice', 'Elective | GPA'),
(29, 'Semester IV', 'HNDIT4232', 'Enterprise Architecture', 'Elective | GPA'),
(30, 'Semester IV', 'HNDIT4242', 'Computer Services Management', 'Elective | GPA');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nic` (`nic`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `former_students`
--
ALTER TABLE `former_students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lectures`
--
ALTER TABLE `lectures`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nic` (`nic`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `lectures_assignment`
--
ALTER TABLE `lectures_assignment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lecturer_id` (`lecturer_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `marks`
--
ALTER TABLE `marks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reg_id` (`reg_id`),
  ADD UNIQUE KEY `nic` (`nic`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `former_students`
--
ALTER TABLE `former_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `lectures`
--
ALTER TABLE `lectures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lectures_assignment`
--
ALTER TABLE `lectures_assignment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `marks`
--
ALTER TABLE `marks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `lectures_assignment`
--
ALTER TABLE `lectures_assignment`
  ADD CONSTRAINT `lectures_assignment_ibfk_1` FOREIGN KEY (`lecturer_id`) REFERENCES `lectures` (`id`),
  ADD CONSTRAINT `lectures_assignment_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
