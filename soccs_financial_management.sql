-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 29, 2025 at 01:04 PM
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
-- Database: `soccs_financial_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `id` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `partylist` varchar(100) NOT NULL,
  `position_id` int(11) NOT NULL,
  `platform` text NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`id`, `firstname`, `lastname`, `partylist`, `position_id`, `platform`, `photo`, `created_at`, `updated_at`) VALUES
(1, 'Leni', 'Robredo', 'Independent', 1, 'Leni Robredo\'s platform, primarily associated with her \"Angat Buhay\" program, centers on poverty alleviation through six key areas: food security and nutrition, health, education, rural development, women\'s empowerment, and housing and resettlement. Her broader political goals have focused on promoting good governance, transparency, accountability, and strengthening democratic institutions, often framed by her supporters as a \"pink revolution\" to uplift the nation, support farmers and businesses, and improve the economy and healthcare.', '../uploads/candidates/candidate_692a8e45683bb.png', '2025-11-29 06:10:13', '2025-11-29 06:10:13'),
(2, 'Rodrigo', 'Duterte', 'Independent', 1, 'Duterte\'s platform was centered on his \"war on drugs,\" which he portrayed as a way to fight crime and insecurity, and included the promotion of a shift to a federal system of government. Other key components included a focus on infrastructure development through the \"Build! Build! Build!\" program and the use of information and communications technology (ICT) to improve governance. His platform was also supported by economic reforms, such as tax reform, to foster inclusive growth.', '../uploads/candidates/candidate_692aaa89cf0e5.jpg', '2025-11-29 08:10:49', '2025-11-29 08:10:49');

-- --------------------------------------------------------

--
-- Table structure for table `elections`
--

CREATE TABLE `elections` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` enum('upcoming','active','completed','cancelled') DEFAULT 'upcoming',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `elections`
--

INSERT INTO `elections` (`id`, `title`, `description`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`) VALUES
(3, 'SOCCS Officer Election 2025', '', '2025-11-29 17:06:00', '2025-11-29 17:20:00', 'completed', '2025-11-29 09:06:46', '2025-11-29 09:08:32');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `date` datetime NOT NULL,
  `end_date` datetime DEFAULT NULL,
  `is_multi_day` tinyint(1) DEFAULT 0,
  `location` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT 'general',
  `status` enum('upcoming','ongoing','completed','cancelled','archived') DEFAULT 'upcoming',
  `created_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `date`, `end_date`, `is_multi_day`, `location`, `category`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(2, 'Research Colloquim', 'Defense', '2025-12-01 07:00:00', '2025-12-05 07:00:00', 1, 'CCS Building', 'academic', 'upcoming', 'admin', '2025-11-29 12:02:51', '2025-11-29 12:03:24');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `category` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `supplier` varchar(255) DEFAULT NULL,
  `document` varchar(255) DEFAULT NULL,
  `date` date NOT NULL,
  `transaction_hash` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `name`, `amount`, `category`, `description`, `supplier`, `document`, `date`, `transaction_hash`, `created_at`) VALUES
(1, 'Event', 500.00, 'Events', 'CCS Night', 'ABC', NULL, '2025-05-07', '0xb7939f6c6525cfdf7cb5f9ed1adddabc5bf729fc4687e74958f1a29fa339f968', '2025-05-07 12:16:50'),
(2, 'Buffet ', 500.00, 'Food', 'Food for guest', 'Nanot\'s', '681b502de377e_Subject.pdf', '2025-05-07', '0x7248a4fab0886ff33947c69d73e7aa554fcb4bac115f3b4801d28010856a25a6', '2025-05-07 12:21:01'),
(3, 'Computer Set', 50000.00, 'Supplies', 'PC set for computer lab', 'FRSS', '681b5645bc5a0_black.jpg', '2025-05-07', '0x3b6909655115eb68ac149b8bc677ce7a2b999345894b58c1286a9e03b9abf2de', '2025-05-07 12:47:01'),
(4, 'Aircon', 10000.00, 'Supplies', 'Aircon for room 106', 'Ikea', '681b665b2f78a_black.jpg', '2025-05-07', '0x9fa4dca47a387228f1188d8a3c71629a004430a8c68a91a34b62ee66c75686c6', '2025-05-07 13:55:39'),
(5, 'Renovation', 10000.00, 'Supplies', 'Renovation of room 102', 'N/A', '681b677a1b665_black.jpg', '2025-05-07', '0x6d3fd639e768dfe20438043d25cafb0bdb30476c838b5cbad46b5abc49aeea69', '2025-05-07 14:00:26'),
(6, 'table', 1304.00, 'Supplies', 'table for room 203', 'N/A', NULL, '2025-05-07', '0x77189612cd1b53a7ee0f06941c37c0ef83deb293d9eb51f8d6406f9394135888', '2025-05-07 14:12:26'),
(11, 'Event', 13000.00, 'Events', 'CCS Days', 'N/A', '681b739cd1d3a_black.jpg', '2025-05-31', '0x47c8f68f37da48a385303823e852844e7a4d694e9ba7c149c078ea59ab240abb', '2025-05-07 14:52:12'),
(12, 'IT Assembly', 4400.00, 'Events', 'Speaker fee', 'N/A', '681b77b21f3f7_black.jpg', '2025-05-16', '0xe3294df17633fbcbb12d22920f0ccab5c99f85b2c350aef4462b10fcdca187db', '2025-05-07 15:09:38'),
(13, 'Seminar', 5000.00, 'Events', 'Guest Speaker Fee', 'N/A', NULL, '2025-05-14', '0x7433e8f84ac88de2574421eabcaaa65868784ebddfe0d2b2e6888a55d7c48128', '2025-05-07 15:15:49'),
(14, 'asd', 555.00, 'Transport', 'asdasd', 'asdad ', NULL, '2025-05-01', '0x64cacd924e429ae524420211c1d61168d5e16d21c7c0da1b2232894b02c1680a', '2025-05-07 15:21:49'),
(15, 'Test', 12313.00, 'Food', 'Test', 'Test', '681b7e065a479_black.jpg', '2025-05-07', '0x3376a57505fab7fb22fcb666ca518bc3c38fb54c0e3f6dd13cd90dacf8f5f4dc', '2025-05-07 15:36:38'),
(16, 'Computer Set', 50000.00, 'Supplies', 'PC set for room 206', 'FRSS', '681b814f648ea_black.jpg', '2025-06-13', '0xe2f71eb4fe6669a3ff1f28487ba481a10673bd7fc3167af9453fa40076da68ae', '2025-05-07 15:50:39'),
(17, 'Test1 ', 500.00, 'Transport', 'Test2', 'N/A', NULL, '2025-05-08', '0x04ce8c1c34c203f8192f1c2f0b95bda9d1398813cd1142953b0e9e2cf3cbe4f1', '2025-05-07 16:20:13'),
(18, 'Computer ', 15000.00, 'Supplies', 'COmputer set for room 205', 'FRSS', NULL, '2025-05-08', '0xa01838e18b6444ae893fa728b6a9ea22015620347523acafc1c3a482e8e14867', '2025-05-08 06:23:36'),
(19, 'Buffet ', 200.00, 'Food', 'For Event', 'Nanot\'s', NULL, '2025-05-08', '0x4a1c0808bb65e56599154be4a75fbe0738de7c79639dbf69dee1b2a9c178b114', '2025-05-08 08:24:24'),
(20, 'Test', 1234.00, 'Food', 'For Event', 'N/a', '681cb96868645_Subject.pdf', '2025-05-08', '0x8c043e9fefdfff05ce77d6899e942deee3cb6a50994003c512a17a74d7ba6911', '2025-05-08 14:02:16'),
(21, 'Test2', 200.00, 'Food', 'For Event', 'N/a', NULL, '2025-05-09', '0x7f0e0e5cd20a3e82370981dc8212aacc6eb2a4230866754df14c0391bcabbb2a', '2025-05-08 15:39:37'),
(22, 'Sample', 200.00, 'Transport', 'For Event', 'N/a', '685fb4a4c9b71_bb576c06c6ae2eaf03203401d9ee0a05.png', '2025-06-28', '0xaadc22e764f1d8970e4cbd7d3eed7a3dafec6bc5d24f183cb21baa1a1c94dd49', '2025-06-28 09:23:49');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`email`, `otp`, `expires_at`) VALUES
('roswelljamesvitaliz@gmail.com', '731054', '2025-11-03 17:23:51');

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `id` int(11) NOT NULL,
  `description` varchar(100) NOT NULL,
  `max_votes` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`id`, `description`, `max_votes`, `created_at`, `updated_at`) VALUES
(1, 'President', 1, '2025-11-29 06:07:41', '2025-11-29 06:07:41'),
(2, 'Vice President', 1, '2025-11-29 06:16:45', '2025-11-29 06:16:45'),
(3, 'Secretary', 1, '2025-11-29 06:24:49', '2025-11-29 06:24:49'),
(4, 'Treasurer', 1, '2025-11-29 06:25:12', '2025-11-29 06:25:12'),
(5, 'Auditor', 1, '2025-11-29 06:25:23', '2025-11-29 06:25:23'),
(6, 'Public Information Officer', 2, '2025-11-29 06:25:33', '2025-11-29 06:26:28'),
(7, 'Event Coordinator', 2, '2025-11-29 06:26:46', '2025-11-29 06:26:50'),
(8, 'Business Manager', 2, '2025-11-29 06:27:10', '2025-11-29 06:27:10'),
(9, '1st Year Representative', 1, '2025-11-29 06:27:39', '2025-11-29 06:27:39'),
(10, '2nd Year Representative', 1, '2025-11-29 06:27:47', '2025-11-29 06:27:47'),
(11, '3rd Year Representative', 1, '2025-11-29 06:27:55', '2025-11-29 06:27:55'),
(12, '4th Year Representative', 1, '2025-11-29 06:28:03', '2025-11-29 06:28:03');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` varchar(20) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `year_level` int(1) NOT NULL,
  `section` varchar(1) NOT NULL,
  `course` varchar(10) NOT NULL DEFAULT 'BSIT',
  `age` int(3) DEFAULT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `receipt_file` varchar(255) DEFAULT NULL,
  `payment_status` enum('paid','unpaid') DEFAULT 'unpaid',
  `is_archived` tinyint(1) DEFAULT 0,
  `archived_at` timestamp NULL DEFAULT NULL,
  `archived_by` varchar(255) DEFAULT NULL,
  `restored_at` timestamp NULL DEFAULT NULL,
  `restored_by` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `membership_fee_status` enum('unpaid','paid') DEFAULT 'unpaid',
  `membership_fee_receipt` varchar(255) DEFAULT NULL,
  `membership_fee_paid_at` timestamp NULL DEFAULT NULL,
  `academic_year` varchar(20) DEFAULT NULL COMMENT 'Academic Year extracted from COR (e.g., 2025-2026)',
  `semester` varchar(50) DEFAULT NULL COMMENT 'Semester extracted from COR (e.g., First (1st) Semester, Second (2nd) Semester)',
  `date_of_birth` date DEFAULT NULL COMMENT 'Date of Birth (manually entered)',
  `phone_number` varchar(20) DEFAULT NULL COMMENT 'Phone Number (manually entered)',
  `address` text DEFAULT NULL COMMENT 'Address (manually entered)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `first_name`, `middle_name`, `last_name`, `email`, `password`, `year_level`, `section`, `course`, `age`, `gender`, `receipt_file`, `payment_status`, `is_archived`, `archived_at`, `archived_by`, `restored_at`, `restored_by`, `is_active`, `created_at`, `updated_at`, `membership_fee_status`, `membership_fee_receipt`, `membership_fee_paid_at`, `academic_year`, `semester`, `date_of_birth`, `phone_number`, `address`) VALUES
('0122-1141', 'Roswell James', 'Democrito', 'Vitaliz', 'roswelljamesvitaliz@gmail.com', '$2y$10$Da3j2HmlBvw/K7xKYfOvkOp.B9lfAi6z.e1yiBYWBhw5sMmjku7mq', 4, 'A', 'BSIT', 24, 'male', NULL, 'unpaid', 0, NULL, NULL, NULL, NULL, 1, '2025-10-30 15:40:08', '2025-10-31 13:47:16', 'unpaid', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_registrations`
--

CREATE TABLE `student_registrations` (
  `id` varchar(20) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `course` varchar(10) NOT NULL DEFAULT 'BSIT',
  `year_level` int(1) NOT NULL,
  `section` varchar(1) NOT NULL,
  `age` int(3) NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `student_id_image` varchar(255) DEFAULT NULL,
  `cor_file` varchar(255) DEFAULT NULL,
  `set_password_token` varchar(128) DEFAULT NULL,
  `set_password_expires_at` datetime DEFAULT NULL,
  `approval_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `approved_by` varchar(255) DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `academic_year` varchar(20) DEFAULT NULL COMMENT 'Academic Year extracted from COR (e.g., 2025-2026)',
  `semester` varchar(50) DEFAULT NULL COMMENT 'Semester extracted from COR (e.g., First (1st) Semester, Second (2nd) Semester)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_registrations`
--

INSERT INTO `student_registrations` (`id`, `first_name`, `middle_name`, `last_name`, `email`, `password`, `course`, `year_level`, `section`, `age`, `gender`, `student_id_image`, `cor_file`, `set_password_token`, `set_password_expires_at`, `approval_status`, `created_at`, `approved_at`, `rejected_at`, `approved_by`, `rejection_reason`, `academic_year`, `semester`) VALUES
('0122-1141', 'Roswell James', 'Democrito', 'Vitaliz', 'roswelljamesvitaliz@gmail.com', '$2y$10$Da3j2HmlBvw/K7xKYfOvkOp.B9lfAi6z.e1yiBYWBhw5sMmjku7mq', 'BSIT', 4, 'A', 24, 'male', 'uploads/student-ids/0122-1141.png', 'uploads/documents/690386d1a1db9_COR_0122-1141.png', NULL, NULL, 'approved', '2025-10-30 15:40:01', '2025-10-30 15:40:08', NULL, 'System', NULL, NULL, NULL),
('0122-1142', 'Liyan', '', 'Ping', 'liyaaanping@gmail.com', NULL, 'BSIT', 4, 'A', 24, 'male', 'uploads/student-ids/0122-1142.png', 'uploads/documents/6904bef8713ad_COR_0122-1142.jpg', '7455e6b5d8e1a269b8274d171ec2cf008985d7faea8e8b2b927ab7e6297f9d1e', '2025-11-01 14:51:52', 'rejected', '2025-10-31 13:51:52', NULL, '2025-10-31 13:51:59', NULL, 'Name not found in Student ID or COR; Student ID number not found in COR', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `created_at`) VALUES
(3, 'lspuscc.soccs@gmail.com', '$2y$10$4OXZcgtKLD3UH2GMyZK09ui6mgtIPCJyex/lsmhLS.brVzIXi/h32', '2025-04-18 12:18:54');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL,
  `election_id` int(11) NOT NULL,
  `voter_id` varchar(20) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  `vote_hash` varchar(255) DEFAULT NULL,
  `voted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`id`, `election_id`, `voter_id`, `candidate_id`, `position_id`, `vote_hash`, `voted_at`) VALUES
(1, 3, '0122-1141', 1, 1, NULL, '2025-11-29 09:07:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_position` (`position_id`),
  ADD KEY `idx_name` (`lastname`,`firstname`),
  ADD KEY `idx_partylist` (`partylist`);

--
-- Indexes for table `elections`
--
ALTER TABLE `elections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_date` (`date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`email`),
  ADD KEY `idx_password_resets_expires_at` (`expires_at`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `description` (`description`),
  ADD KEY `idx_description` (`description`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_students_payment_status` (`payment_status`),
  ADD KEY `idx_students_is_archived` (`is_archived`),
  ADD KEY `idx_students_course_year_section` (`course`,`year_level`,`section`),
  ADD KEY `idx_students_archived` (`is_archived`),
  ADD KEY `idx_students_membership_status` (`membership_fee_status`);

--
-- Indexes for table `student_registrations`
--
ALTER TABLE `student_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_student_reg_set_pwd_token` (`set_password_token`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_vote_per_position` (`election_id`,`voter_id`,`position_id`),
  ADD KEY `position_id` (`position_id`),
  ADD KEY `idx_election` (`election_id`),
  ADD KEY `idx_voter` (`voter_id`),
  ADD KEY `idx_candidate` (`candidate_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `elections`
--
ALTER TABLE `elections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `candidates`
--
ALTER TABLE `candidates`
  ADD CONSTRAINT `candidates_ibfk_1` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_ibfk_2` FOREIGN KEY (`voter_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_ibfk_3` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_ibfk_4` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
