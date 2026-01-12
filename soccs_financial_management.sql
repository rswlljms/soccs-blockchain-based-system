-- SOCCSChain Database Schema
-- Version: 1.0.0
-- Database: soccs_financial_management

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
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `activity_type` varchar(100) NOT NULL,
  `activity_description` text NOT NULL,
  `module` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `transaction_hash` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `event_contests`
--

CREATE TABLE `event_contests` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `contest_details` text NOT NULL,
  `registration_link` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `filing_candidacy_periods`
--

CREATE TABLE `filing_candidacy_periods` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `announcement_text` text NOT NULL,
  `form_link` varchar(500) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `screening_date` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `funds`
--

CREATE TABLE `funds` (
  `id` int(11) NOT NULL,
  `source` varchar(255) DEFAULT 'Manual Entry',
  `amount` decimal(10,2) NOT NULL,
  `description` text NOT NULL,
  `date_received` date NOT NULL,
  `transaction_hash` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `masterlist`
--

CREATE TABLE `masterlist` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `course` varchar(10) DEFAULT NULL,
  `section` varchar(1) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `uploaded_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `module` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Default permissions data
--

INSERT INTO `permissions` (`id`, `name`, `slug`, `description`, `module`) VALUES
(1, 'View Dashboard', 'view_dashboard', 'Access to view the main dashboard', 'dashboard'),
(2, 'View Funds', 'view_funds', 'View funds records', 'financial'),
(3, 'View Expenses', 'view_expenses', 'View expense records', 'financial'),
(4, 'Manage Funds', 'manage_funds', 'Create, edit, delete fund records', 'financial'),
(5, 'Manage Expenses', 'manage_expenses', 'Create, edit, delete expense records', 'financial'),
(6, 'View Financial Records', 'view_financial_records', 'View all financial records (read-only)', 'financial'),
(7, 'View Membership Fee', 'view_membership_fee', 'View membership fee records', 'membership'),
(8, 'Modify Membership Fee', 'modify_membership_fee', 'Update membership fee status and receipts', 'membership'),
(9, 'View Students', 'view_students', 'View student records', 'students'),
(10, 'Manage Students', 'manage_students', 'Archive and manage student records', 'students'),
(11, 'Verify Students', 'verify_students', 'Approve or reject student registrations', 'students'),
(12, 'View Events', 'view_events', 'View events', 'events'),
(14, 'Manage Events', 'manage_events', 'Create, edit, delete events', 'events'),
(15, 'Generate Reports', 'generate_reports', 'Generate and view reports', 'reports'),
(16, 'Generate Financial Reports', 'generate_financial_reports', 'Generate financial reports', 'reports'),
(17, 'Generate Membership Reports', 'generate_membership_reports', 'Generate membership fee reports', 'reports'),
(18, 'Generate Event Reports', 'generate_event_reports', 'Generate event reports', 'reports'),
(19, 'Export Reports', 'export_reports', 'Export reports to PDF', 'reports'),
(20, 'View Election', 'view_election', 'View election data and results', 'elections'),
(21, 'Start/End Election', 'manage_election_status', 'Start, stop, and end elections', 'elections'),
(22, 'Register Candidates', 'register_candidates', 'Add and manage election candidates', 'elections'),
(23, 'Manage Positions', 'manage_positions', 'Create and manage election positions', 'elections'),
(24, 'View Election Results', 'view_election_results', 'View election results', 'elections'),
(25, 'Generate Election Reports', 'generate_election_reports', 'Generate report of election results', 'elections'),
(26, 'View Users', 'view_users', 'View user accounts', 'users'),
(27, 'Create Accounts', 'create_accounts', 'Create new user accounts', 'users'),
(28, 'Demote Accounts', 'demote_accounts', 'Change user roles', 'users'),
(29, 'Manage Users', 'manage_users', 'Full user management', 'users'),
(30, 'View Settings', 'view_settings', 'View system settings', 'settings'),
(31, 'Manage Settings', 'manage_settings', 'Modify system settings', 'settings');

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
-- Default positions data
--

INSERT INTO `positions` (`id`, `description`, `max_votes`) VALUES
(1, 'President', 1),
(2, 'Vice President', 1),
(3, 'Secretary', 1),
(4, 'Treasurer', 1),
(5, 'Auditor', 1),
(6, 'Public Information Officer', 2),
(7, 'Event Coordinator', 2),
(8, 'Business Manager', 2),
(9, '1st Year Representative', 1),
(10, '2nd Year Representative', 1),
(11, '3rd Year Representative', 1),
(12, '4th Year Representative', 1);

-- --------------------------------------------------------

--
-- Table structure for table `role_default_permissions`
--

CREATE TABLE `role_default_permissions` (
  `id` int(11) NOT NULL,
  `role` varchar(50) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Default role permissions
--

INSERT INTO `role_default_permissions` (`role`, `permission_id`) VALUES
('adviser', 1), ('adviser', 2), ('adviser', 3), ('adviser', 4), ('adviser', 5),
('adviser', 6), ('adviser', 7), ('adviser', 8), ('adviser', 9), ('adviser', 10),
('adviser', 11), ('adviser', 12), ('adviser', 14), ('adviser', 15), ('adviser', 16),
('adviser', 17), ('adviser', 18), ('adviser', 19), ('adviser', 20), ('adviser', 21),
('adviser', 22), ('adviser', 23), ('adviser', 24), ('adviser', 25), ('adviser', 26),
('adviser', 27), ('adviser', 28), ('adviser', 29), ('adviser', 30), ('adviser', 31),
('dean', 1), ('dean', 2), ('dean', 3), ('dean', 6), ('dean', 9), ('dean', 12),
('dean', 20), ('dean', 24), ('dean', 26), ('dean', 27), ('dean', 28), ('dean', 29),
('president', 1), ('president', 2), ('president', 3), ('president', 7), ('president', 8),
('president', 12), ('president', 15), ('president', 16), ('president', 17), ('president', 18), ('president', 19),
('treasurer', 1), ('treasurer', 2), ('treasurer', 3), ('treasurer', 4), ('treasurer', 5),
('treasurer', 7), ('treasurer', 8), ('treasurer', 12), ('treasurer', 15), ('treasurer', 16), ('treasurer', 19),
('auditor', 1), ('auditor', 2), ('auditor', 3), ('auditor', 7), ('auditor', 8),
('auditor', 12), ('auditor', 15), ('auditor', 16), ('auditor', 19),
('comelec', 1), ('comelec', 19), ('comelec', 20), ('comelec', 21), ('comelec', 22),
('comelec', 23), ('comelec', 24), ('comelec', 25),
('event_coordinator', 1), ('event_coordinator', 2), ('event_coordinator', 3),
('event_coordinator', 6), ('event_coordinator', 12), ('event_coordinator', 14),
('event_coordinator', 18), ('event_coordinator', 19),
('secretary', 1), ('secretary', 9), ('secretary', 10), ('secretary', 12), ('secretary', 15), ('secretary', 19),
('officer', 1), ('officer', 12), ('officer', 20), ('officer', 24);

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
  `gender` enum('male','female','other') NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
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
  `membership_control_number` varchar(10) DEFAULT NULL,
  `membership_processed_by` varchar(255) DEFAULT NULL,
  `membership_fee_paid_at` timestamp NULL DEFAULT NULL,
  `academic_year` varchar(20) DEFAULT NULL,
  `semester` varchar(50) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `academic_year` varchar(20) DEFAULT NULL,
  `semester` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL DEFAULT '',
  `last_name` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('adviser','dean','president','treasurer','auditor','secretary','comelec','event_coordinator','officer') NOT NULL DEFAULT 'officer',
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Default admin user (password: soccslspu)
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `role`, `status`) VALUES
(1, 'SOCCS', 'Admin', 'admin@soccs.edu.ph', '$2y$10$R731fPWbB7cv6RyNF3aU6OptcMzwaiW9bPrtsmJvpM3.jiRtim3gm', 'adviser', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `user_activity_log`
--

CREATE TABLE `user_activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `target_user_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE `user_permissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `granted_by` int(11) DEFAULT NULL,
  `granted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Default admin permissions (all permissions)
--

INSERT INTO `user_permissions` (`user_id`, `permission_id`, `granted_by`) VALUES
(1, 1, 1), (1, 2, 1), (1, 3, 1), (1, 4, 1), (1, 5, 1), (1, 6, 1), (1, 7, 1), (1, 8, 1),
(1, 9, 1), (1, 10, 1), (1, 11, 1), (1, 12, 1), (1, 14, 1), (1, 15, 1), (1, 16, 1), (1, 17, 1),
(1, 18, 1), (1, 19, 1), (1, 20, 1), (1, 21, 1), (1, 22, 1), (1, 23, 1), (1, 24, 1), (1, 25, 1),
(1, 26, 1), (1, 27, 1), (1, 28, 1), (1, 29, 1), (1, 30, 1), (1, 31, 1);

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

-- --------------------------------------------------------

--
-- Structure for view `voting_history`
--

CREATE VIEW `voting_history` AS 
SELECT DISTINCT 
  `v`.`voter_id` AS `voter_id`, 
  `e`.`id` AS `election_id`, 
  `e`.`title` AS `election_title`, 
  cast(min(`v`.`voted_at`) as date) AS `vote_date`, 
  count(distinct `v`.`position_id`) AS `positions_voted`, 
  count(`v`.`id`) AS `total_votes`, 
  max(`e`.`transaction_hash`) AS `transaction_hash` 
FROM (`votes` `v` join `elections` `e` on(`v`.`election_id` = `e`.`id`)) 
GROUP BY `v`.`voter_id`, `e`.`id`, `e`.`title` 
ORDER BY cast(min(`v`.`voted_at`) as date) DESC;

-- --------------------------------------------------------

--
-- Indexes for dumped tables
--

ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_activity_type` (`activity_type`),
  ADD KEY `idx_module` (`module`),
  ADD KEY `idx_created_at` (`created_at`);

ALTER TABLE `candidates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_position` (`position_id`),
  ADD KEY `idx_name` (`lastname`,`firstname`),
  ADD KEY `idx_partylist` (`partylist`);

ALTER TABLE `elections`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_date` (`date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_category` (`category`);

ALTER TABLE `event_contests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_event_id` (`event_id`);

ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `filing_candidacy_periods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_dates` (`start_date`,`end_date`);

ALTER TABLE `funds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_date_received` (`date_received`),
  ADD KEY `idx_created_at` (`created_at`);

ALTER TABLE `masterlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_id` (`student_id`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_course_section` (`course`,`section`);

ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`email`),
  ADD KEY `idx_password_resets_expires_at` (`expires_at`);

ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_module` (`module`);

ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `description` (`description`),
  ADD KEY `idx_description` (`description`);

ALTER TABLE `role_default_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_role_permission` (`role`,`permission_id`),
  ADD KEY `idx_role` (`role`);

ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_students_payment_status` (`payment_status`),
  ADD KEY `idx_students_is_archived` (`is_archived`),
  ADD KEY `idx_students_course_year_section` (`course`,`year_level`,`section`),
  ADD KEY `idx_students_membership_status` (`membership_fee_status`),
  ADD KEY `idx_membership_control_number` (`membership_control_number`);

ALTER TABLE `student_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_student_reg_set_pwd_token` (`set_password_token`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `user_activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created` (`created_at`);

ALTER TABLE `user_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_permission` (`user_id`,`permission_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_permission` (`permission_id`);

ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_vote_per_candidate` (`election_id`,`voter_id`,`candidate_id`),
  ADD KEY `position_id` (`position_id`),
  ADD KEY `idx_election` (`election_id`),
  ADD KEY `idx_voter` (`voter_id`),
  ADD KEY `idx_candidate` (`candidate_id`),
  ADD KEY `idx_votes_voter_election` (`voter_id`,`election_id`);

--
-- AUTO_INCREMENT for dumped tables
--

ALTER TABLE `activity_logs` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `candidates` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `elections` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `events` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `event_contests` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `expenses` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `filing_candidacy_periods` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `funds` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `masterlist` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `permissions` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
ALTER TABLE `positions` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
ALTER TABLE `role_default_permissions` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `users` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `user_activity_log` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `user_permissions` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `votes` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

ALTER TABLE `activity_logs`
  ADD CONSTRAINT `fk_activity_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `candidates`
  ADD CONSTRAINT `candidates_ibfk_1` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON UPDATE CASCADE;

ALTER TABLE `event_contests`
  ADD CONSTRAINT `event_contests_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

ALTER TABLE `user_permissions`
  ADD CONSTRAINT `fk_user_permissions_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_permissions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_ibfk_2` FOREIGN KEY (`voter_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_ibfk_3` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_ibfk_4` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
