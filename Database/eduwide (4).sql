-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 19, 2025 at 03:51 PM
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
-- Table structure for table `about`
--

CREATE TABLE `about` (
  `user_id` int(11) NOT NULL,
  `about_text` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about`
--

INSERT INTO `about` (`user_id`, `about_text`) VALUES
(9, 'test hello.'),
(14, 'test bio');

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
  `status` varchar(20) DEFAULT 'pending',
  `last_login` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `nic`, `email`, `mobile`, `password`, `profile_picture`, `facebook`, `github`, `linkedin`, `blog`, `created_at`, `status`, `last_login`) VALUES
(2, 'Malitha Tishamal', 'No Data Found !', 'malithatishamal@gmail.com', '785530992', '$2y$10$/62hdNg8q8XxoL3FIs..CeJ2YKN6fXpHIQ3RSqYjxFbdkV07BV1ne', 'uploads/profile_pictures/67d2ddfb6f751-411001152_1557287805017611_3900716309730349802_n.jpg', 'https://www.facebook.com/malitha.tishamal', '', '', '', '2025-03-08 14:00:46', 'approved', '2025-05-09 18:49:16'),
(6, 'Amandi Kaushalya', '200370912329', 'dewmikaushalya1234@gmail.com', '0788167038', '$2y$10$xgNjt6h20gEiulpGjE.nhetLHADzKYaaPm8T10xUItb.pKpBLca02', 'uploads/profile_pictures/67d53d2856fc3-amandi.jpg', 'https://www.facebook.com/profile.php?id=100090649864805&mibextid=ZbWKwL', 'Amandi-Kaushalya-Dewmini	', 'https://www.facebook.com/profile.php?id=100090649864805&mibextid=ZbWKwLwww.linkedin.com/in/amandi-kaushalya-dewmini-4059b5352	', '', '2025-03-15 08:37:02', 'disabled', ''),
(7, 'Malith Sandeepa', '200315813452', 'malithsandeepa1081@gmail.com', '0763279285	', '$2y$10$Zyvk/dSOi1dHdANEVn7U/OK5zpHUcQW/6TKtjgn.Ygj8.6nQcFc4S', 'uploads/profile_pictures/67d53d10d7189-sandeepa.jpg', 'https://www.facebook.com/share/1646sJb2gb/	', 'https://github.com/KVMSANDEEPA	', 'https://www.linkedin.com/in/malith-sandeepa	', '', '2025-03-15 08:39:36', 'approved', ''),
(8, 'Matheesha Nihari', '200374300868', 'matheenihari13@gmail.com', '0775751107', '$2y$10$2WugusGnWNorfagraUGan.sRr0SFF9h5ScXcOVQVVWR7HWFngufi2', 'uploads/profile_pictures/67e819ce33004-67d568d3e277b-nihari.jpg', 'https://www.facebook.com/share/12KZGoMHc3H/?mibextid=LQQJ4d	', 'https://github.com/Matheesha-Nihari', 'linkedin.com/in/matheesha-nihari-4a6913350', '', '2025-03-15 11:42:54', 'approved', '');

-- --------------------------------------------------------

--
-- Table structure for table `education`
--

CREATE TABLE `education` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `school` varchar(255) NOT NULL,
  `degree` varchar(255) DEFAULT NULL,
  `field_of_study` varchar(255) DEFAULT NULL,
  `start_month` varchar(20) DEFAULT NULL,
  `start_year` int(11) DEFAULT NULL,
  `end_month` varchar(20) DEFAULT NULL,
  `end_year` int(11) DEFAULT NULL,
  `grade` varchar(50) DEFAULT NULL,
  `activities` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `education`
--

INSERT INTO `education` (`id`, `user_id`, `school`, `degree`, `field_of_study`, `start_month`, `start_year`, `end_month`, `end_year`, `grade`, `activities`, `description`) VALUES
(4, 14, 'SLIATE', 'HNDIT', 'Infromation Technology', 'August', 2024, NULL, NULL, '', '', ''),
(5, 14, 'SLIATE', 'HNDE', 'Electronic Engineering', 'August', 2024, NULL, NULL, '', '', ''),
(12, 9, 'SLIATE', 'HNDIT', 'Infromation Technology', 'January', 2024, NULL, NULL, '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `experiences`
--

CREATE TABLE `experiences` (
  `id` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `employment_type` varchar(100) DEFAULT NULL,
  `company` varchar(255) NOT NULL,
  `currently_working` tinyint(1) DEFAULT 0,
  `start_month` varchar(20) DEFAULT NULL,
  `start_year` int(11) DEFAULT NULL,
  `end_month` varchar(20) DEFAULT NULL,
  `end_year` int(11) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `location_type` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `job_source` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `skills` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `experiences`
--

INSERT INTO `experiences` (`id`, `user_id`, `title`, `employment_type`, `company`, `currently_working`, `start_month`, `start_year`, `end_month`, `end_year`, `location`, `location_type`, `description`, `job_source`, `created_at`, `skills`) VALUES
(6, '14', 'Software Engineering', 'Internship', 'TestCompanyName', 1, 'January', 2025, '0', 0, '', 'On-site', 'test Discription', 'Referral', '2025-04-06 02:41:26', ''),
(7, '14', 'Network Engineering', 'Full-time', 'TestCompanyName', 1, 'January', 2020, '', 0, '', 'Remote', 'test', 'LinkedIn', '2025-04-06 03:19:47', '');

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
  `nowstatus` enum('study','work','intern','free') NOT NULL,
  `facebook` varchar(200) NOT NULL,
  `github` varchar(200) NOT NULL,
  `blog` varchar(200) NOT NULL,
  `linkedin` varchar(200) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `former_students`
--

INSERT INTO `former_students` (`id`, `username`, `reg_id`, `nic`, `email`, `profile_picture`, `mobile`, `study_year`, `nowstatus`, `facebook`, `github`, `blog`, `linkedin`, `password`, `status`, `created_at`, `updated_at`, `last_login`) VALUES
(9, 'testuser', 'tst', '200205526251', 'malithatishamal2005@gmail.com', 'uploads/profile_pictures/default.png', '771000400', 2016, 'study', 'https://www.facebook.com/', 'https://github.com/', 'https://github.com/', 'https://www.linkedin.com/', '$2y$10$iiq5FbEkkSPCP1zUMhxPXue5mhLQ1W52JguJ3Gu9OJJZ/Yy65kmku', 'disabled', '2025-03-08 15:21:06', '2025-05-09 03:50:24', ''),
(14, 'testuser', 'gal-it-2023-f-0010', '200202222620', 'testuser@gmail.com', 'uploads/profile_pictures/default.png', '771000000', 2016, 'study', '', '', '', '', '$2y$10$PwtrJEZDntO6VAKyonl51OG3cd7bnDgIfnpPSOBYZXb91k/daGIqW', 'approved', '2025-04-05 04:37:32', '2025-05-08 04:09:39', '2025-05-08 09:39:39');

-- --------------------------------------------------------

--
-- Table structure for table `former_students_achievements`
--

CREATE TABLE `former_students_achievements` (
  `id` int(11) NOT NULL,
  `former_student_id` int(11) NOT NULL,
  `event_title` varchar(255) NOT NULL,
  `event_description` text NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `event_name` varchar(255) NOT NULL,
  `organized_by` varchar(255) NOT NULL,
  `event_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `former_students_achievements`
--

INSERT INTO `former_students_achievements` (`id`, `former_student_id`, `event_title`, `event_description`, `image_path`, `created_at`, `event_name`, `organized_by`, `event_date`) VALUES
(17, 14, 'test event name', 'test', 'uploads/achievements/681ac8f8ddd410.47490342.jpg', '2025-05-03 15:01:33', 'Introva', 'test', '2025-05-03');

-- --------------------------------------------------------

--
-- Table structure for table `former_students_certifications`
--

CREATE TABLE `former_students_certifications` (
  `id` int(11) NOT NULL,
  `former_student_id` int(11) NOT NULL,
  `certification_name` varchar(255) NOT NULL,
  `issued_by` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `certification_description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `former_students_certifications`
--

INSERT INTO `former_students_certifications` (`id`, `former_student_id`, `certification_name`, `issued_by`, `date`, `link`, `certification_description`, `image_path`) VALUES
(1, 14, 'test', 'test', '2025-05-07', 'https://www.figma.com/', 'test', 'uploads/certifications/681ae4650d3795.32709308.jpg');

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
  `status` varchar(20) DEFAULT 'pending',
  `last_login` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lectures`
--

INSERT INTO `lectures` (`id`, `username`, `nic`, `email`, `mobile`, `linkedin`, `blog`, `github`, `facebook`, `password`, `profile_picture`, `created_at`, `status`, `last_login`) VALUES
(1, 'test lecturer', '200202222625', 'malithatishamal2002@gmail.com', '771000000', '', '', '', '', '$2y$10$nAqaq8RBfoo27.k8lXCXoeddeIdMbgxGOZBdcb0uyfYSzYX/TOY9m', 'uploads/profile_pictures/67d339351d57b-PXL_20250110_080305123.jpg', '2025-03-08 14:06:37', 'approved', '2025-05-06 20:51:46'),
(2, 'demo test name', '200202222620', 'demo3@gmail.com', '771000001', '', '', '', '', '$2y$10$o1blitQ6SNQ27paWgBo6V.Gsk4dg0oH7Hw0CTR85h.Ub9sRVX0K2C', 'uploads/profile_pictures/default.png', '2025-03-12 13:54:39', 'disabled', ''),
(3, 'testlecture', '200302226258', 'testlecture@gmail.com', '771000005', '', '', '', '', '$2y$10$s1zvba.qlfjtKyf8CukmEuX2BOIML6u7XljVpMbH5RxQkMNgy.Qsq', 'uploads/profile_pictures/default.png', '2025-04-06 14:40:37', 'approved', '2025-05-09 18:48:40');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `entered_by_id` int(11) DEFAULT NULL,
  `entered_by_role` enum('admin','lecturer') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `marks`
--

INSERT INTO `marks` (`id`, `student_id`, `year`, `subject`, `semester`, `practical_marks`, `paper_marks`, `special_notes`, `created_at`, `entered_by_id`, `entered_by_role`) VALUES
(1, 'gal-it-2023-f-0011', '2023', 'Web Design', 'Semester I', 80, 50, '', '2025-05-03 06:38:19', 1, 'lecturer'),
(2, 'gal-it-2023-f-0011', '2023', 'Visual Application Programming', 'Semester I', 50, 20, '', '2025-05-03 15:13:28', 2, 'admin'),
(3, 'gal-it-2023-f-0000', '2023', 'Communication Skills', 'Semester I', 50, 80, '', '2025-05-06 16:11:59', 1, 'lecturer');

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
  `status` varchar(20) DEFAULT 'pending',
  `last_login` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `username`, `reg_id`, `nic`, `study_year`, `email`, `mobile`, `facebook`, `github`, `blog`, `linkedin`, `password`, `profile_picture`, `created_at`, `status`, `last_login`) VALUES
(2, 'testnamenew', 'gal-it-2023-f-0000', '200202226251', 2023, 'malithatishamal2003@gmail.com', '771000001', 'test', '', '', '', '$2y$10$gGb6Ubgv92I8Negpm0I1cOMt.CIviySPLLmOOTBkCC9lwbiP8E9BW', 'uploads/profile_pictures/default.png', '2025-03-08 14:28:05', 'approved', '2025-05-06 20:52:15'),
(4, 'malitha', 'gal-it-2023-f-0009', '200202222620', 2023, 'demo@gmail.com', '771000001', '', '', '', '', '$2y$10$12qvgV6sFTQCRld5kmFjKexu1E/v7CwlWVx5iqh4Z2ppBSIwoiPz.', 'uploads/profile_pictures/default.png', '2025-03-12 15:05:01', 'pending', ''),
(5, 'testname', 'gal-it-2022-f-0009', '200202222625', 2022, 'demo3@gmail.com', '771000004', '', '', '', '', '$2y$10$WDbnrOQK7WBWAQPayrPB6OAT6IY/RTSn8zzNgxpBGGZ1vR/Lk2NPi', 'uploads/profile_pictures/default.png', '2025-03-15 05:15:35', 'approved', ''),
(6, 'Malitha', 'gal-it-2023-f-0010', '200202222621', 2024, 'demo2@gmail.com', '771000005', '', '', '', '', '$2y$10$oWqdPj119osZq/kBx.l6su1bK8uncy4dYyG7wKn56SWtiBzbOu4Aq', 'uploads/profile_pictures/default.png', '2025-03-15 05:21:09', 'approved', ''),
(7, 'testuser', 'gal-it-2023-f-0011', '2001025658', 2023, 'teststu@gmail.com', '783356888', '', '', '', '', '$2y$10$Tk7s5AFNYLrk1QE43WlqKev2McB.xZoEe5SaQ/Y2GsVya8/oLNlhe', 'uploads/profile_pictures/default.png', '2025-04-06 14:43:20', 'approved', '2025-05-08 19:13:44');

-- --------------------------------------------------------

--
-- Table structure for table `students_achievements`
--

CREATE TABLE `students_achievements` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `event_title` varchar(255) NOT NULL,
  `event_description` text NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `event_name` varchar(255) NOT NULL,
  `organized_by` varchar(255) NOT NULL,
  `event_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students_achievements`
--

INSERT INTO `students_achievements` (`id`, `student_id`, `event_title`, `event_description`, `image_path`, `created_at`, `event_name`, `organized_by`, `event_date`) VALUES
(17, 7, 'test event name', '', 'uploads/achievements/681c7f5d3fd769.27028832.jpg', '2025-05-08 09:54:37', 'test', 'test', '2025-05-08');

-- --------------------------------------------------------

--
-- Table structure for table `students_certifications`
--

CREATE TABLE `students_certifications` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `certification_name` varchar(255) NOT NULL,
  `issued_by` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `certification_description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students_certifications`
--

INSERT INTO `students_certifications` (`id`, `student_id`, `certification_name`, `issued_by`, `date`, `link`, `certification_description`, `image_path`) VALUES
(1, 7, 'testname', 'test', '2025-05-08', 'https://www.figma.com/', 'test', 'uploads/certifications/681c81da227a53.02025742.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `students_education`
--

CREATE TABLE `students_education` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `school` varchar(255) NOT NULL,
  `degree` varchar(255) DEFAULT NULL,
  `field_of_study` varchar(255) DEFAULT NULL,
  `start_month` varchar(20) DEFAULT NULL,
  `start_year` int(11) DEFAULT NULL,
  `end_month` varchar(20) DEFAULT NULL,
  `end_year` int(11) DEFAULT NULL,
  `grade` varchar(50) DEFAULT NULL,
  `activities` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students_education`
--

INSERT INTO `students_education` (`id`, `user_id`, `school`, `degree`, `field_of_study`, `start_month`, `start_year`, `end_month`, `end_year`, `grade`, `activities`, `description`) VALUES
(14, 7, 'test', 'test', 'test', 'February', 2024, NULL, NULL, '', '', '');

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

-- --------------------------------------------------------

--
-- Table structure for table `summaries`
--

CREATE TABLE `summaries` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `summary` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `summaries`
--

INSERT INTO `summaries` (`id`, `user_id`, `summary`, `created_at`) VALUES
(1, 9, 'test test/', '2025-04-04 03:26:29'),
(2, 14, 'test.', '2025-04-05 10:08:49');

-- --------------------------------------------------------

--
-- Table structure for table `user_profile`
--

CREATE TABLE `user_profile` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `about` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about`
--
ALTER TABLE `about`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nic` (`nic`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `education`
--
ALTER TABLE `education`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `experiences`
--
ALTER TABLE `experiences`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `former_students`
--
ALTER TABLE `former_students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `former_students_achievements`
--
ALTER TABLE `former_students_achievements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `former_student_id` (`former_student_id`);

--
-- Indexes for table `former_students_certifications`
--
ALTER TABLE `former_students_certifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `former_student_id` (`former_student_id`);

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_subject_semester` (`student_id`,`subject`,`semester`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reg_id` (`reg_id`),
  ADD UNIQUE KEY `nic` (`nic`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `students_achievements`
--
ALTER TABLE `students_achievements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `students_certifications`
--
ALTER TABLE `students_certifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `students_education`
--
ALTER TABLE `students_education`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `summaries`
--
ALTER TABLE `summaries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_profile`
--
ALTER TABLE `user_profile`
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
-- AUTO_INCREMENT for table `education`
--
ALTER TABLE `education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `experiences`
--
ALTER TABLE `experiences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `former_students`
--
ALTER TABLE `former_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `former_students_achievements`
--
ALTER TABLE `former_students_achievements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `former_students_certifications`
--
ALTER TABLE `former_students_certifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lectures`
--
ALTER TABLE `lectures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `lectures_assignment`
--
ALTER TABLE `lectures_assignment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `marks`
--
ALTER TABLE `marks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `students_achievements`
--
ALTER TABLE `students_achievements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `students_certifications`
--
ALTER TABLE `students_certifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `students_education`
--
ALTER TABLE `students_education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `summaries`
--
ALTER TABLE `summaries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_profile`
--
ALTER TABLE `user_profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `about`
--
ALTER TABLE `about`
  ADD CONSTRAINT `about_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `former_students` (`id`);

--
-- Constraints for table `education`
--
ALTER TABLE `education`
  ADD CONSTRAINT `education_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `former_students` (`id`);

--
-- Constraints for table `former_students_achievements`
--
ALTER TABLE `former_students_achievements`
  ADD CONSTRAINT `former_students_achievements_ibfk_1` FOREIGN KEY (`former_student_id`) REFERENCES `former_students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `former_students_certifications`
--
ALTER TABLE `former_students_certifications`
  ADD CONSTRAINT `former_students_certifications_ibfk_1` FOREIGN KEY (`former_student_id`) REFERENCES `former_students` (`id`);

--
-- Constraints for table `lectures_assignment`
--
ALTER TABLE `lectures_assignment`
  ADD CONSTRAINT `lectures_assignment_ibfk_1` FOREIGN KEY (`lecturer_id`) REFERENCES `lectures` (`id`),
  ADD CONSTRAINT `lectures_assignment_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- Constraints for table `students_achievements`
--
ALTER TABLE `students_achievements`
  ADD CONSTRAINT `students_achievements_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students_certifications`
--
ALTER TABLE `students_certifications`
  ADD CONSTRAINT `students_certifications_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `summaries`
--
ALTER TABLE `summaries`
  ADD CONSTRAINT `summaries_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `former_students` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
