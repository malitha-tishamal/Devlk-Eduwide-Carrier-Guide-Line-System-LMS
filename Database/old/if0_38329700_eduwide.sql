-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql113.infinityfree.com
-- Generation Time: Jun 24, 2025 at 02:53 AM
-- Server version: 10.6.19-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_38329700_eduwide`
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
(14, 'test bio test'),
(15, 'im malith'),
(23, 'My name is Dumindu Damsara. I am 24 years old. My mother\'s name is Nilanthi Liyanarachchi and my father\'s name is Nandasiri Jayalath. I studied at MR/ Sri Medhankara maha vidyalaya, Thelijjawila Royal College and Matara Central College. I am studing at HNDIT Galle.'),
(25, 'ðŸ‘¨â€ðŸ’» | ðŸ‡±ðŸ‡°');

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
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `nic`, `email`, `mobile`, `password`, `profile_picture`, `facebook`, `github`, `linkedin`, `blog`, `created_at`, `status`, `last_login`) VALUES
(1, 'Malitha Tishamal', '20002202615', 'malithatishamal@gmail.com', '0785530992', '$2y$10$/62hdNg8q8XxoL3FIs..CeJ2YKN6fXpHIQ3RSqYjxFbdkV07BV1ne', 'uploads/profile_pictures/67d2ddfb6f751-411001152_1557287805017611_3900716309730349802_n.jpg', 'https://www.facebook.com/malitha.tishamal', 'https://github.com/malitha-tishamal', 'https://www.linkedin.com/in/malitha-tishamal', 'https://malitha-tishamal.github.io/blog', '2025-03-08 14:00:46', 'approved', '2025-06-24 11:38:38'),
(2, 'Matheesha Nihari', '200374300868', 'admin.nihari13@gmail.com', '0775751107', '$2y$10$2WugusGnWNorfagraUGan.sRr0SFF9h5ScXcOVQVVWR7HWFngufi2', 'uploads/profile_pictures/67e819ce33004-67d568d3e277b-nihari.jpg', 'https://www.facebook.com/share/12KZGoMHc3H/?mibextid=LQQJ4d', 'https://github.com/Matheesha-Nihari', 'https://www.linkedin.com/in/matheesha-nihari-4a6913350/', '', '2025-03-15 11:42:54', 'approved', '2025-05-26 19:37:03'),
(3, 'Amandi Kaushalya', '200370912329', 'admin.kaushalya@gmail.com', '0788167038', '$2y$10$AC9vqSn40vKBhz0IMky9zuqHhcjEiyyyLPuG8dbYGioKcLJVmrJSi', 'uploads/profile_pictures/67d53d2856fc3-amandi.jpg', 'https://www.facebook.com/profile.php?id=100090649864805&mibextid=ZbWKwL', 'https://github.com/Amandi-Kaushalya-Dewmini', 'https://www.linkedin.com/in/amandi-kaushalya-dewmini-4059b5352/', '', '2025-03-15 08:37:02', 'approved', '2025-06-21 20:16:58'),
(4, 'Malith Sandeepa', '200315813452', 'admin.sandeepa@gmail.com', '0763279285', '$2y$10$AE12LuSdCT0JbuS8OTWSFOKcnjgG7OMwcysIjZL998K/ZBof0WE22', 'uploads/profile_pictures/67d53d10d7189-sandeepa.jpg', 'https://www.facebook.com/share/1646sJb2gb/', 'https://github.com/KVMSANDEEPA', 'https://www.linkedin.com/in/malith-sandeepa', '', '2025-03-15 08:39:36', 'approved', '2025-06-21 20:28:31'),
(5, 'testAdmin', '200000000000', 'testadmin@email.com', '786630888', '$2y$10$KxzEdI5zAg1OI5Udoo9Aw.KhF9aZARUjHaqWaet3GA1CUS.fN5g2i', 'uploads/profile_pictures/67ff39b00133d-WhatsApp Image 2025-02-14 at 13.15.12_19c2f491.jpg', '', '', '', '', '2025-04-16 04:54:47', 'disabled', '2025-05-29 19:50:43');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `profile_picture` varchar(255) NOT NULL DEFAULT 'uploads/profile_pictures/default.png',
  `facebook` varchar(255) NOT NULL,
  `github` varchar(200) NOT NULL,
  `blog` varchar(200) NOT NULL,
  `linkedin` varchar(200) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `username`, `email`, `address`, `mobile`, `category`, `profile_picture`, `facebook`, `github`, `blog`, `linkedin`, `password`, `status`, `last_login`, `created_at`) VALUES
(1, 'testCompanyName', 'testcompany@gmail.com', 'testAddress', '771000000', 'Software Engineering', 'uploads/profile_pictures/default.png', '', '', '', '', '$2y$10$NOCCWFamgxlt9YYMlIcg8uzwDls1P/DS6p2jv8WIZ5OYWrENDaPcy', 'approved', '2025-06-23 19:31:14', '2025-06-06 05:38:02');

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
(13, 14, 'SITEC', 'Diploma', 'Web Development', 'March', 2023, 'November', 2023, 'B', '', ''),
(15, 16, 'Mo/Ethiliwewa Secondary School', 'G.C.E. A/L', '', 'January', 2020, 'February', 2023, '', '', ''),
(18, 15, 'G/Nagoda Royal College', 'G.C.E.   ', 'Advanced Level', 'January', 2020, 'February', 2023, '', '', ''),
(21, 16, 'Sri Lanka Institute of Advanced Technological Education', 'Higher National Diploma In Information Technology', '', 'August', 2024, NULL, NULL, '', '', ''),
(22, 15, 'Sri Lanka Institute of Advanced Technological Education', 'Higher National Diploma In Information Technology', '', 'August', 2024, 'april', 2025, '', '', ''),
(23, 18, 'St.Thomas Girls\' High School Matara', '', '', '', 0, NULL, NULL, '', '', ''),
(24, 19, 'Ananda Sastralaya National School', 'A/L', '', 'January', 2022, 'December', 2023, '', '', ''),
(25, 19, 'Sri Lanka Institute of Advanced Technological Education', 'Higher National Diploma In Information Technology', 'Infromation Technology', 'August', 2024, NULL, NULL, '', '', ''),
(27, 17, 'Esoft Metro Campus', 'Diploma In Information Technology', 'Infromation Technology', 'December', 2018, 'September', 2019, 'A', '', ''),
(28, 17, 'Esoft Metro Campus', 'Diploma In English', 'English', 'December', 2018, 'August', 2019, 'C', '', ''),
(29, 17, 'Matara Central Colleage', 'Advanced Level', 'Technology', 'August', 2019, 'January', 2021, '', '', ''),
(30, 17, 'Southern Information Technology Education Center', 'Cetificate in Java', 'Java Application Development', 'March', 2023, 'October', 2023, '', '', ''),
(31, 17, 'Southern Information Technology Education Center', 'Cetificate In Web Development', 'Web Development', 'March', 2023, 'October', 2023, 'B', '', ''),
(32, 17, 'Sri Lanka Institute of Advanced Technological Education', 'Higher National Diploma In Information Technology', 'Infromation Technology', 'August', 2024, NULL, NULL, '', '', ''),
(33, 18, 'Sri Lanka Institute of Advanced Technological Education', 'Higher National Diploma In Information Technology', 'Infromation Technology', 'August', 2024, NULL, NULL, '', '', ''),
(35, 15, 'Rajarata University of Sri Lanka', 'B.Sc. degree in Applied Sciences', '', '', 2025, NULL, NULL, '', '', ''),
(36, 25, 'St.Johnâ€™s National School Panadura', 'A/L', 'Technology ', '', 2020, NULL, 2023, '', '', ''),
(37, 25, 'Aquinas College ', 'Diploma in English ', 'English ', 'January', 2025, NULL, NULL, '', '', ''),
(38, 25, 'Sri Lanka Institute of Advanced Technological Education ', 'Higher National Diploma in Information Technology ', 'Information technology ', 'August', 2024, NULL, NULL, '', '', '');

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
(8, '14', 'Tranee Developer', 'Full-time', 'TestCompanyName', 0, 'January', 2024, 'February', 2025, '', 'On-site', '', 'Other', '2025-04-18 12:30:31', ''),
(9, '16', 'Trainee Bank officer', 'Internship', 'Bank of Ceylon', 0, 'December', 2023, 'February', 2024, '', 'On-site', '', 'Other', '2025-04-21 09:21:56', ''),
(10, '16', 'clerk', 'Full-time', 'Pragdana Co-op Bank ', 0, 'April', 2024, 'August', 2024, '', 'On-site', '', 'Other', '2025-04-21 09:31:01', ''),
(11, '18', 'Worked in Apex Online learning management system', 'Full-time', 'Apex Online', 0, 'January', 2024, 'August', 2024, '', 'Hybrid', '', 'Other', '2025-04-21 18:27:38', ''),
(13, '23', 'Computer Operator', 'Full-time', 'Porathota Samurdhi Bank', 0, 'December', 2020, 'December', 2021, '', 'On-site', 'I worked as a computer operator in customer registration process.', 'Other', '2025-05-01 14:57:35', ''),
(14, '25', 'Trainee Machine Operator', 'Full-time', 'Singer (Sri Lanka) PLC Factory Complex - Regnis', 0, 'August', 2023, '0', 2024, '', 'On-site', '', '', '2025-05-02 16:34:16', '');

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
  `status` varchar(20) NOT NULL DEFAULT 'approved',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `former_students`
--

INSERT INTO `former_students` (`id`, `username`, `reg_id`, `nic`, `email`, `profile_picture`, `mobile`, `study_year`, `nowstatus`, `facebook`, `github`, `blog`, `linkedin`, `password`, `status`, `created_at`, `updated_at`, `last_login`) VALUES
(14, 'testuser', 'gal-it-2016-f-0000', '200202222620', 'testuser@gmail.com', 'uploads/profile_pictures/default.png', '771000001', 2016, 'study', '', '', '', '', '$2y$10$PwtrJEZDntO6VAKyonl51OG3cd7bnDgIfnpPSOBYZXb91k/daGIqW', 'approved', '2025-04-05 04:37:32', '2025-05-09 01:32:33', '2025-05-07 09:38:49'),
(15, 'Malith Sandeepa', 'GAL/IT/2324/F/0014', '200315813452', 'malithsandeepa1234@gmail.com', 'uploads/profile_pictures/6806625e883f6-sandeepa.jpg', '0763279285', 2023, 'study', 'https://www.facebook.com/share/1646sJb2gb/', 'https://github.com/KVMSANDEEPA', '', 'https://www.linkedin.com/in/malith-sandeepa', '$2y$10$IB.pg.k09C5Rei0LJvfiE.83jSsGuLKMunGN0kjTQ42uMJQ3jBX8u', 'approved', '2025-04-19 04:55:28', '2025-06-21 14:50:01', '2025-06-09 12:52:59'),
(16, 'Amandi kaushalya dewmini', 'GAL/IT/2324/F/0068', '200370912329', 'dewmikaushalya1234@gmail.com', 'uploads/profile_pictures/6806624525e93-amandi.jpg', '788167038', 2023, 'study', 'https://www.facebook.com/profile.php?id=100090649864805&mibextid=ZbWKwL', 'https://github.com/Amandi-Kaushalya-Dewmini', '', 'www.linkedin.com/in/amandi-kaushalya-dewmini-4059b5352', '$2y$10$Y6c.wJJ1bqLCf4Y7oa0U6eLxVyKDoTqCx.Z.utfZ1Jj31a5DXFg8a', 'approved', '2025-04-19 04:59:41', '2025-06-09 07:26:20', '2025-06-09 12:56:20'),
(17, 'Malitha Tishamal', 'GAL/IT/2324/F/0009', '20002202615', 'malithatishamal@email.com', 'uploads/profile_pictures/680c86da8e120-411001152_1557287805017611_3900716309730349802_n1.jpg', '0785530992', 2023, 'study', 'https://www.facebook.com/malitha.tishamal', 'https://github.com/malitha-tishamal', 'https://malitha-tishamal.github.io/blog', 'https://www.linkedin.com/in/malitha-tishamal', '$2y$10$fedKlhjtKvF4yU1sE7mMAe.ECdqsKzYFpTIjyYc0zbqSIYUBcXrtm', 'approved', '2025-04-26 07:06:21', '2025-06-17 15:51:46', '2025-06-17 21:21:46'),
(18, 'Matheesha Nihari', 'GAL/IT/2324/F/0035', '200374300868', 'matheenihari13@gmail.com', 'uploads/profile_pictures/68068c0d36edc-my pic.jpg', '775751107', 2023, 'study', 'https://www.facebook.com/share/12KZGoMHc3H/?mibextid=LQQJ4d', 'https://github.com/Matheesha-Nihari', '', 'linkedin.com/in/matheesha-nihari-4a6913350', '$2y$10$b9OEg6txTS.M032ZxDqRA.qIFw2onIidWzxtCtUtfmXEbpLX8SLXe', 'approved', '2025-04-21 18:16:39', '2025-06-23 13:51:31', '2025-06-16 06:01:07'),
(19, 'Thimira Savinda', 'GAL/IT/2324/F/216', '200412701219', 'thimirapost116@gmail.com', 'uploads/profile_pictures/default.png', '078784215', 2023, 'study', '', '', '', '', '$2y$10$CvO.XOed5qb.r1rxW7ft2OTGecdo38raPZWi5N38xXcNxbhbhXzHi', 'disabled', '2025-04-22 03:10:42', '2025-06-18 15:36:15', '2025-04-22 08:41:20'),
(23, 'Dumindu Damsara', 'GAL/IT/2324/F/0050', '200105602878', 'dumindudamsara60@gmail.com', 'uploads/profile_pictures/default.png', '0715594850', 2023, 'study', 'https://www.facebook.com/dumindu.sirijayalathjothirathna.9', 'https://github.com/dumindu2041329', '', 'https://www.linkedin.com/in/dumindu-damsara-0049ab246/', '$2y$10$adFOUchSio8kEct1V660yeZ/8lGRgpRubmcVs7EI3WqH6iIIwshmW', 'disabled', '2025-05-01 13:24:07', '2025-06-18 15:36:10', '2025-05-01 20:21:22'),
(25, 'Sandaru Jayasanka', 'GAL/IT/2324/F/140', '200311812660', 'sandarujayasanka27@gmail.com', 'uploads/profile_pictures/default.png', '0764793054', 2023, 'study', '', '', '', '', '$2y$10$vm3ie7FXbR6vpuqz6UGk7OBiInnyZpvzVdqla5jY.uWSSGvPWYEei', 'disabled', '2025-05-01 14:53:03', '2025-06-18 15:36:01', '2025-05-23 20:39:17'),
(33, 'hima dewindi', 'GAL/IT/2024/F/0027', '200269301725', 'himadewindi@gmail.com', 'uploads/profile_pictures/default.png', '0764682144', 2016, 'free', '', '', '', '', '$2y$10$yDWyyXMa6jvE7UhWQhVJr.iDm0S4lwAcDIBlbIJTq7xcS.TQZbPS.', 'disabled', '2025-05-09 01:27:35', '2025-06-18 10:21:14', NULL);

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
(17, 17, 'Project of The Event', 'test', 'uploads/achievements/681ad22c764d01.34005580.png', '2025-05-07 03:07:08', 'Introva', 'test', '2025-05-07'),
(18, 16, 'test', 'test', 'uploads/achievements/6836be6ad16da7.64562755.png', '2025-05-28 07:42:34', 'test1', 'sliate', '2025-05-22');

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
(1, 17, 'test', 'test', '2025-05-08', 'https://www.figma.com/', 'test', 'uploads/certifications/681c2e2a569d56.29833320.jpg');

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
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lectures`
--

INSERT INTO `lectures` (`id`, `username`, `nic`, `email`, `mobile`, `linkedin`, `blog`, `github`, `facebook`, `password`, `profile_picture`, `created_at`, `status`, `last_login`) VALUES
(3, 'testlecture1', '200302226258', 'testlecture@email.com', '771000005', '', '', '', '', '$2y$10$s1zvba.qlfjtKyf8CukmEuX2BOIML6u7XljVpMbH5RxQkMNgy.Qsq', 'uploads/profile_pictures/default.png', '2025-04-06 14:40:37', 'approved', '2025-06-23 19:37:01'),
(4, 'testLecture', '200315813452', 'testlec@gmail.com', '0763279285', '', '', '', '', '$2y$10$IEqlkpOjxYn1NYMEf0Pw4.aZCLRPXN.XtTcyjHONBE0hfcOqKvCZi', 'uploads/profile_pictures/default.png', '2025-05-01 13:46:59', 'approved', '2025-06-09 13:00:13'),
(5, 'malith sandeepa', '200269301735', 'devilgamer167@gmail.com', '0764682144', '', '', '', '', '$2y$10$ESr/AqXcJGko8FbyVGoWa.oYCqcUSiAXqTvdvlbN3p00VK5dDb3vO', 'uploads/profile_pictures/default.png', '2025-05-02 15:02:32', 'approved', '2025-05-02 20:33:17'),
(6, 'kvmsandeepaT1', '200516813452', 'kvmsandeepaT1@gmail.com', '0763279285', '', '', '', '', '$2y$10$KDUK3yp0HUqZ0zn7b.jV5.wN0SE9nWhDYV/gpggYJ07RSbUPOfDcW', 'uploads/profile_pictures/6814f47e95768-FB_IMG_1734768172199.jpg', '2025-05-02 15:58:10', 'approved', '2025-06-13 17:47:44'),
(9, 'Amandi Kaushalya Dewmini ', '200370912329', 'lec.dewmikaushalya1234@gmail.com', '0788167038', 'https://www.linkedin.com/in/amandi-kaushalya-dewmini-4059b5352/', '', 'https://github.com/Amandi-Kaushalya-Dewmini', 'https://www.facebook.com/profile.php?id=100090649864805&mibextid=ZbWKwL', '$2y$10$MYPoH0Q11ht4QDApny03HOpGIzP4X6Jo7OxV5Y9gAXsovQK.HKi32', 'uploads/profile_pictures/6815be7ac4721-184387725.jpeg', '2025-05-03 06:46:52', 'approved', '2025-05-06 11:41:58'),
(10, 'testLecturer2', '200374300868', 'testlecturer2@gmail.com', '0712345678', '', '', '', '', '$2y$10$avBhBrhQ.y//xPXM4O2dfuHxZkRt5Q9QbowRYvGgiy1M2lb06Y486', 'uploads/profile_pictures/default.png', '2025-05-06 11:41:07', 'disabled', NULL);

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
(85, 10, 12),
(86, 10, 19),
(87, 10, 21),
(88, 10, 22),
(89, 6, 1),
(90, 6, 2),
(91, 6, 3),
(92, 6, 4),
(93, 6, 5),
(94, 6, 6),
(95, 6, 7),
(96, 6, 8),
(97, 6, 9),
(99, 6, 11),
(100, 6, 12),
(101, 6, 13),
(102, 6, 14),
(103, 6, 15),
(104, 6, 16),
(105, 6, 17),
(106, 6, 18),
(107, 6, 19),
(108, 6, 20),
(109, 6, 21),
(110, 6, 22),
(111, 6, 23),
(112, 6, 24),
(113, 6, 25),
(114, 6, 26),
(115, 6, 27),
(116, 6, 28),
(117, 6, 29),
(118, 6, 30);

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
(1, 'GAL/IT/2324/F/0014', '2023', 'Visual Application Programming', 'Semester I', 93, 100, '', '2025-05-09 06:21:57', 6, 'lecturer'),
(2, 'GAL/IT/2324/F/0014', '2023', 'Fundamentals of Programming', 'Semester II', 20, 30, '', '2025-05-09 06:22:29', 6, 'lecturer');

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
  `status` varchar(20) DEFAULT 'approved',
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `username`, `reg_id`, `nic`, `study_year`, `email`, `mobile`, `facebook`, `github`, `blog`, `linkedin`, `password`, `profile_picture`, `created_at`, `status`, `last_login`) VALUES
(1, 'Matheesha Nihari', 'GAL/IT/F/2324/0035', '200374300868', 2023, 'stu.matheenihari13@gmail.com', '775751107', 'https://www.facebook.com/share/12KZGoMHc3H/?mibextid=LQQJ4d', 'https://github.com/Matheesha-Nihari', '', 'www.linkedin.com/in/matheesha-nihari-4a6913350', '$2y$10$rl3FbcrMfCzASVNJMW1i4er7wkK9SKJveQr5iyYmkZnMoSsmkvNK2', 'uploads/profile_pictures/67e819ce33004-67d568d3e277b-nihari.jpg', '2025-04-17 02:08:46', 'disabled', '2025-04-18 12:54:49'),
(2, 'Amandi Kaushalya Dewmini', 'GAL/IT/2324/F/0068', '200370912329', 2023, 'stu.dewmikaushalya112@gmail.com', '0788167038', 'https://www.facebook.com/profile.php?id=100090649864805&mibextid=ZbWKwL', 'https://github.com/Amandi-Kaushalya-Dewmini', '', 'www.linkedin.com/in/amandi-kaushalya-dewmini-4059b5352', '$2y$10$XtDGHjK9WIKn.5nyISBGS.0AkyKC2zXlMBkxaQ4DdhH4RBBl9elty', 'uploads/profile_pictures/67d53d2856fc3-amandi.jpg', '2025-04-16 15:39:15', 'approved', '2025-05-24 19:44:01'),
(3, 'Malith Sandeepa', 'GAL/IT/2324/F/0014', '200315813452', 2023, 'stu.malithsandeepa@gmail.com', '0763279285', 'https://www.facebook.com/share/1646sJb2gb/', 'https://github.com/KVMSANDEEPA', '', 'https://www.linkedin.com/in/malith-sandeepa', '$2y$10$RlTSvjdVyMO7I7HS1fgYNee80Q3z/UKjZcMTAPIlvZz7bZnFvgHCu', 'uploads/profile_pictures/67d53d10d7189-sandeepa.jpg', '2025-04-16 15:13:10', 'approved', '2025-06-09 12:55:07'),
(4, 'testname', 'gal-it-2023-f-0000', '200202226251', 2023, 'teststu@gmail.com', '771000001', 'test', '', '', '', '$2y$10$gGb6Ubgv92I8Negpm0I1cOMt.CIviySPLLmOOTBkCC9lwbiP8E9BW', 'uploads/profile_pictures/default.png', '2025-03-08 14:28:05', 'approved', '2025-06-23 19:42:31'),
(13, 'Hima Dewindi', 'GAL/IT/2324/F/0017', '200269301735', 2023, 'stu.himadewindi@gmail.com', '764682144', '', '', '', '', '$2y$10$vOLE1dhWibKPdOeFSIOEeu6x3OeFBALN3LEkFyhEffVN/Xqaw/w3i', 'uploads/profile_pictures/default.png', '2025-05-01 13:55:39', 'disabled', NULL),
(14, 'Basitha Dinujaya ', 'GAL/IT/2324/0066', '200333111123', 2023, 'basithadinujaya75@gmail.com', '705539000', '', '', '', '', '$2y$10$60fb142yCTY9ZnhZjANSHe592kq7ekjr/cLWnAOnYCrk5XrfrFTA2', 'uploads/profile_pictures/default.png', '2025-05-01 17:39:12', 'disabled', '2025-05-01 23:26:21'),
(15, 'S.S.Godakanda', 'GAL/IT/2324/F/219', '200364412130', 2023, 'gsandanila@gmail.com', '781852852', '', '', '', '', '$2y$10$suLOaBHgL3/f9aInCcEF6u/ylzxPcEPA0WeUcazcdNe3GoTR2dpc6', 'uploads/profile_pictures/default.png', '2025-05-02 05:53:31', 'disabled', NULL),
(21, 'Thimira Savinda', 'GAL/IT/2324/F/216', '200412701219', 2024, 'thimirapostugc116@gmail.com', '764070611', 'https://www.facebook.com/share/1C395V6ERT/?mibextid=qi2Omg', 'https://github.com/Thimira116', '', '', '$2y$10$NLILMYnlkEEz09mGHoUkK.sfzZ0/HOOQrYhhXzULjKyNRqy3sfe0u', 'uploads/profile_pictures/681643ca85c11-1000110890.jpg', '2025-05-03 16:21:06', 'disabled', '2025-05-03 21:51:44'),
(22, 'K.A.Vidura Dilshan', 'GAL/IT/2023/F/0040', '200326112049', 2023, 'Vdilshan63@gmail.com', '771919634', '', '', '', '', '$2y$10$XL3kncUD9okb91Oy/GEFMOW//r0UZXBVsLICRq8yNX0bmiJIfk8sy', 'uploads/profile_pictures/default.png', '2025-05-15 08:29:11', 'disabled', '2025-05-15 13:59:38');

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
(17, 4, 'test', '', 'uploads/achievements/681c841fa11a77.50101108.jpg', '2025-05-08 10:14:55', 'test event', 'test', '2025-05-08'),
(18, 2, 'test', 'introva', 'uploads/achievements/6831d3662db385.17153177.png', '2025-05-24 14:10:46', 'test1', 'sliate', '2025-05-23');

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
(1, 4, 'test', 'test', '2025-05-08', 'https://www.figma.com/', 'test', 'uploads/certifications/681c844542a8e2.39260248.jpg'),
(2, 2, 'test', 'abc institute', '2025-05-23', '', 'test', 'uploads/certifications/6831d3bfad3a44.15035251.png');

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
(13, 4, 'SLIATE', 'HNDIT', 'Infromation Technology', 'January', 2025, NULL, NULL, '', '', ''),
(14, 3, 'Sri Lanka Institute of Advanced Technological Education', 'Higher National Diploma In Information Technology', '', 'August', 2024, 'April', 2025, '', '', ''),
(16, 2, 'Sri Lanka Institute of Advanced Technological Education', 'Higher National Diploma Information  Technology', 'Information Technology', 'August', 2023, NULL, 2025, '', '', ''),
(17, 2, 'Mo/Ethiliwewa Secondary School', 'Advance Level', 'Commerce', 'May', 2020, 'March', 2023, '', '', '');

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
(2, 14, 'test.', '2025-04-05 10:08:49'),
(3, 15, 'test', '2025-05-01 14:23:21'),
(4, 23, 'I previously worked in the computer department as a customer registration officer at Samurdhi Bank in Porathota.', '2025-05-01 14:58:23');

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
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `education`
--
ALTER TABLE `education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `experiences`
--
ALTER TABLE `experiences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `former_students`
--
ALTER TABLE `former_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `former_students_achievements`
--
ALTER TABLE `former_students_achievements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `former_students_certifications`
--
ALTER TABLE `former_students_certifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lectures`
--
ALTER TABLE `lectures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `lectures_assignment`
--
ALTER TABLE `lectures_assignment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT for table `marks`
--
ALTER TABLE `marks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `students_achievements`
--
ALTER TABLE `students_achievements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `students_certifications`
--
ALTER TABLE `students_certifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `students_education`
--
ALTER TABLE `students_education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `summaries`
--
ALTER TABLE `summaries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
