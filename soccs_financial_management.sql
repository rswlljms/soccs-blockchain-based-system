-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 02, 2026 at 06:51 PM
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

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `activity_type`, `activity_description`, `module`, `created_at`) VALUES
(32, 3, 'election_created', 'Created election: SOCCS Election (ID: 19)', 'elections', '2025-11-30 19:15:45'),
(33, 3, 'election_ended', 'End election: SOCCS Election (ID: 19)', 'elections', '2025-11-30 19:24:23'),
(34, 3, 'fund_created', 'Created fund: Budget in bank - Amount: ₱25,675.65', 'financial', '2025-11-30 19:28:59'),
(35, 3, 'expense_created', 'Created expense: ₱3,500.00 - Teacher’s Day Expenses (FOOD AND DRINKS)', 'financial', '2025-11-30 19:31:36'),
(36, 3, 'election_deleted', 'Deleted election: Election #19 (ID: 19)', 'elections', '2025-11-30 19:33:22'),
(37, 3, 'election_created', 'Created election: SOCCS Election (ID: 20)', 'elections', '2025-11-30 19:33:42'),
(38, 3, 'election_ended', 'End election: SOCCS Election (ID: 20) - Blockchain TX: 0x89ea7df1e8c7533f14f3fa4fee386fce7212490f0a6ed2b7cb382061d922f389', 'elections', '2025-11-30 19:38:12'),
(39, 3, 'election_created', 'Created election: SOCCS (ID: 21)', 'elections', '2025-11-30 19:41:56'),
(40, 3, 'election_ended', 'End election: SOCCS (ID: 21)', 'elections', '2025-11-30 19:42:09'),
(41, 3, 'election_deleted', 'Deleted election: Election #21 (ID: 21)', 'elections', '2025-11-30 19:42:22'),
(42, 3, 'election_created', 'Created election: SOCCS (ID: 22)', 'elections', '2025-11-30 19:42:36'),
(43, 3, 'election_ended', 'End election: SOCCS (ID: 22)', 'elections', '2025-11-30 19:42:44'),
(44, 3, 'election_deleted', 'Deleted election: Election #22 (ID: 22)', 'elections', '2025-11-30 19:49:16'),
(45, 3, 'election_created', 'Created election: SOCCS (ID: 23)', 'elections', '2025-11-30 19:50:34'),
(46, 3, 'election_ended', 'End election: SOCCS (ID: 23)', 'elections', '2025-11-30 19:50:42'),
(47, 3, 'election_deleted', 'Deleted election: Election #23 (ID: 23)', 'elections', '2025-11-30 19:50:55'),
(48, 3, 'election_created', 'Created election: SOCCS (ID: 24)', 'elections', '2025-11-30 19:51:32'),
(49, 3, 'election_ended', 'End election: SOCCS (ID: 24)', 'elections', '2025-11-30 19:51:51'),
(50, 3, 'election_created', 'Created election: SOCCS (ID: 25)', 'elections', '2025-11-30 20:02:51'),
(51, 3, 'election_ended', 'End election: SOCCS (ID: 25)', 'elections', '2025-11-30 20:03:00'),
(52, 3, 'event_updated', 'Updated event: Research Colloquium / FOD', 'events', '2025-11-30 20:10:10'),
(53, 6, 'user_login', 'User logged into the system', 'authentication', '2025-11-30 20:30:04'),
(54, 3, 'user_login', 'User logged into the system', 'authentication', '2025-11-30 21:05:38'),
(55, 4, 'user_login', 'User logged into the system', 'authentication', '2025-11-30 21:27:26'),
(56, 6, 'user_login', 'User logged into the system', 'authentication', '2025-11-30 21:28:14'),
(57, 5, 'user_login', 'User logged into the system', 'authentication', '2025-11-30 21:37:59'),
(58, 3, 'user_login', 'User logged into the system', 'authentication', '2025-11-30 21:49:27'),
(59, 3, 'user_login', 'User logged into the system', 'authentication', '2025-12-09 05:15:58'),
(60, 3, 'user_login', 'User logged into the system', 'authentication', '2025-12-14 15:25:55'),
(61, 3, 'event_updated', 'Updated event: Research Colloquium / FOD', 'events', '2025-12-14 15:42:12'),
(62, 3, 'event_updated', 'Updated event: Research Colloquium / FOD', 'events', '2025-12-14 15:42:18'),
(63, 4, 'user_login', 'User logged into the system', 'authentication', '2025-12-14 15:52:25'),
(64, 5, 'user_login', 'User logged into the system', 'authentication', '2025-12-14 16:48:02'),
(65, 3, 'user_login', 'User logged into the system', 'authentication', '2025-12-14 16:56:59'),
(66, 3, 'user_login', 'User logged into the system', 'authentication', '2025-12-14 17:13:44'),
(67, 3, 'user_updated', 'Updated user: ID 7 - first_name: Comelec, last_name: Officer, email: comelec.test@gmail.com, role: {\"from\":\"comelec\",\"to\":\"comelec\"}, status: active, password: changed', 'users', '2025-12-14 17:14:18'),
(68, 7, 'user_login', 'User logged into the system', 'authentication', '2025-12-14 17:14:55'),
(69, 7, 'user_login', 'User logged into the system', 'authentication', '2025-12-14 17:18:59'),
(70, 7, 'election_created', 'Created election: SOCCS Officer Election 2025 (ID: 26)', 'elections', '2025-12-14 17:23:07'),
(71, 7, 'election_created', 'Created election: SOCCS Officer Election 2025 (ID: 27)', 'elections', '2025-12-14 17:30:34'),
(72, 7, 'election_started', 'Start election: SOCCS Officer Election 2025 (ID: 27)', 'elections', '2025-12-14 17:30:37'),
(73, 7, 'election_created', 'Created election: SOCCS Election Officer 2025 (ID: 28)', 'elections', '2025-12-14 17:50:35'),
(74, 7, 'election_created', 'Created election: SOCCS Election Officer 2025 (ID: 29)', 'elections', '2025-12-14 17:56:27'),
(75, 7, 'election_created', 'Created election: SOCCS Officer Election 2025 (ID: 30)', 'elections', '2025-12-14 18:22:09'),
(76, 3, 'user_login', 'User logged into the system', 'authentication', '2025-12-14 18:38:30'),
(77, 3, 'event_created', 'Created event: Xmas party (social)', 'events', '2025-12-14 18:39:05'),
(78, 3, 'event_updated', 'Updated event: Research Colloquium / FOD', 'events', '2025-12-14 18:39:40'),
(79, 3, 'expense_created', 'Created expense: ₱500.00 - Buffet  (FOOD AND DRINKS)', 'financial', '2025-12-14 18:42:24'),
(80, 7, 'user_login', 'User logged into the system', 'authentication', '2025-12-14 18:52:11'),
(81, 7, 'election_created', 'Created election: SOCCS Election 2025 (ID: 31)', 'elections', '2025-12-14 18:52:31'),
(82, 3, 'user_login', 'User logged into the system', 'authentication', '2025-12-19 17:10:58'),
(83, 3, 'membership_action', 'Recorded membership payment for Roswell James Vitaliz (ID: 0122-1141) - Amount: ₱250.00 - Control No: 001', 'membership', '2025-12-19 17:21:59'),
(84, 3, 'membership_status_updated', 'Updated membership status to unpaid for student: 0122-1141 (Roswell James Vitaliz)', 'membership', '2025-12-19 17:22:51'),
(85, 3, 'membership_action', 'Recorded membership payment for Roswell James Vitaliz (ID: 0122-1141) - Amount: ₱250.00 - Control No: 001', 'membership', '2025-12-19 17:22:59'),
(86, 3, 'membership_status_updated', 'Updated membership status to unpaid for student: 0122-1141 (Roswell James Vitaliz)', 'membership', '2025-12-19 17:23:36'),
(87, 3, 'membership_action', 'Recorded membership payment for Roswell James Vitaliz (ID: 0122-1141) - Amount: ₱250.00 - Control No: 001', 'membership', '2025-12-19 17:23:55'),
(88, 3, 'membership_status_updated', 'Updated membership status to unpaid for student: 0122-1141 (Roswell James Vitaliz)', 'membership', '2025-12-19 17:24:22'),
(89, 3, 'membership_action', 'Recorded membership payment for Roswell James Vitaliz (ID: 0122-1141) - Amount: ₱250.00 - Control No: 001', 'membership', '2025-12-19 17:29:41'),
(90, 3, 'membership_status_updated', 'Updated membership status to unpaid for student: 0122-1141 (Roswell James Vitaliz)', 'membership', '2025-12-19 17:30:06'),
(91, 3, 'membership_action', 'Recorded membership payment for Roswell James Vitaliz (ID: 0122-1141) - Amount: ₱250.00 - Control No: 001', 'membership', '2025-12-19 17:42:26'),
(92, 4, 'user_login', 'User logged into the system', 'authentication', '2025-12-19 17:45:30'),
(93, 4, 'membership_status_updated', 'Updated membership status to unpaid for student: 0122-1141 (Roswell James Vitaliz)', 'membership', '2025-12-19 17:45:49'),
(94, 4, 'membership_action', 'Recorded membership payment for Roswell James Vitaliz (ID: 0122-1141) - Amount: ₱250.00 - Control No: 001', 'membership', '2025-12-19 17:46:45'),
(95, 4, 'membership_action', 'Recorded membership payment for Roswell James Vitaliz (ID: 0122-1141) - Amount: ₱250.00 - Control No: 001', 'membership', '2025-12-19 17:54:01'),
(96, 4, 'membership_status_updated', 'Updated membership status to unpaid for student: 0122-1141 (Roswell James Vitaliz)', 'membership', '2025-12-19 17:56:31'),
(97, 4, 'membership_action', 'Recorded membership payment for Roswell James Vitaliz (ID: 0122-1141) - Amount: ₱250.00 - Control No: 001', 'membership', '2025-12-19 17:56:40'),
(98, 4, 'membership_status_updated', 'Updated membership status to unpaid for student: 0122-1141 (Roswell James Vitaliz)', 'membership', '2025-12-19 18:07:24'),
(99, 4, 'membership_action', 'Recorded membership payment for Roswell James Vitaliz (ID: 0122-1141) - Amount: ₱250.00 - Control No: 001', 'membership', '2025-12-19 18:07:28'),
(100, 3, 'user_login', 'User logged into the system', 'authentication', '2025-12-21 14:59:54'),
(101, 5, 'user_login', 'User logged into the system', 'authentication', '2025-12-21 15:36:30'),
(102, 5, 'user_login', 'User logged into the system', 'authentication', '2025-12-21 15:38:00'),
(103, 5, 'user_updated', 'Updated user: comelec.test@gmail.com - permissions: updated', 'users', '2025-12-21 15:55:06'),
(104, 5, 'user_updated', 'Updated user: comelec.test@gmail.com - permissions: updated', 'users', '2025-12-21 15:56:31'),
(105, 5, 'user_updated', 'Updated user: comelec.test@gmail.com - permissions: updated', 'users', '2025-12-21 15:56:39'),
(106, 7, 'user_login', 'User logged into the system', 'authentication', '2025-12-21 15:56:55'),
(107, 5, 'user_updated', 'Updated user: comelec.test@gmail.com - permissions: updated', 'users', '2025-12-21 16:02:01'),
(108, 7, 'user_login', 'User logged into the system', 'authentication', '2025-12-21 16:02:13'),
(109, 3, 'user_login', 'User logged into the system', 'authentication', '2025-12-21 16:07:43'),
(110, 3, 'user_login', 'User logged into the system', 'authentication', '2025-12-21 16:20:12'),
(111, 3, 'election_created', 'Created election: TEST (ID: 32)', 'elections', '2025-12-21 16:22:38'),
(112, 3, 'election_started', 'Start election: TEST (ID: 32)', 'elections', '2025-12-21 16:22:42'),
(113, 3, 'user_login', 'User logged into the system', 'authentication', '2025-12-21 16:25:42'),
(114, 3, 'user_login', 'User logged into the system', 'authentication', '2025-12-21 16:34:30'),
(115, 3, 'election_created', 'Created election: TEST 1 (ID: 33)', 'elections', '2025-12-21 16:36:06'),
(116, 3, 'election_updated', 'Update election: TEST (ID: 32)', 'elections', '2025-12-21 16:36:15'),
(117, 3, 'election_started', 'Start election: TEST 1 (ID: 33)', 'elections', '2025-12-21 16:36:45'),
(118, 3, 'user_login', 'User logged into the system', 'authentication', '2025-12-21 16:41:46'),
(119, 6, 'user_login', 'User logged into the system', 'authentication', '2025-12-21 16:43:45'),
(120, 3, 'user_login', 'User logged into the system', 'authentication', '2025-12-21 16:50:10'),
(121, 3, 'user_updated', 'Updated user: treasurer.test@gmail.com - permissions: updated', 'users', '2025-12-21 16:50:22'),
(122, 4, 'user_login', 'User logged into the system', 'authentication', '2025-12-21 16:50:38'),
(123, 3, 'user_login', 'User logged into the system', 'authentication', '2025-12-21 16:56:59'),
(124, 4, 'user_login', 'User logged into the system', 'authentication', '2025-12-21 16:57:09'),
(125, 3, 'user_updated', 'Updated user: treasurer.test@gmail.com - permissions: updated', 'users', '2025-12-21 16:57:54'),
(126, 4, 'user_login', 'User logged into the system', 'authentication', '2025-12-21 16:58:25'),
(127, 3, 'user_updated', 'Updated user: treasurer.test@gmail.com - permissions: updated', 'users', '2025-12-21 16:59:34'),
(128, 4, 'user_login', 'User logged into the system', 'authentication', '2025-12-21 16:59:46'),
(129, 3, 'user_updated', 'Updated user: treasurer.test@gmail.com - permissions: updated', 'users', '2025-12-21 17:00:13'),
(130, 4, 'user_login', 'User logged into the system', 'authentication', '2025-12-21 17:01:18'),
(131, 3, 'user_login', 'User logged into the system', 'authentication', '2026-01-02 15:56:53'),
(132, 3, 'user_deactivated', 'Deactivated user: treasurer.test@gmail.com (Role: treasurer)', 'users', '2026-01-02 15:57:27'),
(133, 3, 'user_updated', 'Updated user: treasurer.test@gmail.com - status: active', 'users', '2026-01-02 15:58:10'),
(134, 3, 'user_login', 'User logged into the system', 'authentication', '2026-01-02 16:05:29'),
(135, 3, 'user_login', 'User logged into the system', 'authentication', '2026-01-02 16:29:02');

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
(3, 'Ross Cedric', 'Nazareno', 'Nexus Partylist', 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '../uploads/candidates/candidate_692bcfe7e5086.png', '2025-11-30 05:02:31', '2025-11-30 05:02:31'),
(4, 'Avegail', 'Sadiasa', 'Grid Partylist', 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '../uploads/candidates/candidate_692c2b912755b.png', '2025-11-30 05:04:16', '2025-11-30 11:33:37'),
(5, 'Zaren', 'Gellido', 'Nexus Partylist', 2, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '../uploads/candidates/candidate_692bd09763c40.png', '2025-11-30 05:05:27', '2025-11-30 05:05:27'),
(6, 'Jayvee', 'Aguila', 'Nexus Partylist', 4, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '../uploads/candidates/candidate_692bd0c6642c2.png', '2025-11-30 05:06:14', '2025-11-30 05:06:14'),
(7, 'Jake', 'Jaqueza', 'Nexus Partylist', 5, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '../uploads/candidates/candidate_692bd0eff1499.png', '2025-11-30 05:06:55', '2025-11-30 05:06:55'),
(8, 'Biann Chris', 'Evangelista', 'Nexus Partylist', 7, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '../uploads/candidates/candidate_692bd12b2cc9f.png', '2025-11-30 05:07:55', '2025-11-30 05:07:55'),
(9, 'Amor', 'Valmonte', 'Nexus Partylist', 8, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '../uploads/candidates/candidate_692bd1590fd90.png', '2025-11-30 05:08:41', '2025-11-30 05:08:41'),
(10, 'Nheil James', 'San Juan', 'Nexus Partylist', 8, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '../uploads/candidates/candidate_692bd18f0794f.png', '2025-11-30 05:09:35', '2025-11-30 05:09:35'),
(11, 'Cheyenne Lei', 'De Ramos', 'Nexus Partylist', 6, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '../uploads/candidates/candidate_692bd1c980d80.png', '2025-11-30 05:10:33', '2025-11-30 05:10:33'),
(12, 'Eliandre', 'Jeriel', 'Nexus Partylist', 6, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '../uploads/candidates/candidate_692bd1fe10f7e.png', '2025-11-30 05:11:26', '2025-11-30 05:11:26'),
(13, 'Kenshin', 'Lim', 'Nexus Partylist', 9, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '../uploads/candidates/candidate_692bd21f48b17.png', '2025-11-30 05:11:59', '2025-11-30 05:11:59'),
(14, 'Chlouie', 'Cabot', 'Nexus Partylist', 10, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '../uploads/candidates/candidate_692bd2484a2c0.png', '2025-11-30 05:12:40', '2025-11-30 05:12:40'),
(15, 'Cel Rick', 'Almario', 'Nexus Partylist', 12, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '../uploads/candidates/candidate_692bd29e05613.png', '2025-11-30 05:14:06', '2025-11-30 05:14:06'),
(16, 'Rashed', 'Dizon', 'Grid Partylist', 2, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '../uploads/candidates/candidate_692bd2ec6513d.png', '2025-11-30 05:15:24', '2025-11-30 05:15:24'),
(22, 'Jillian', 'Gutierrez', 'Grid Partylist', 4, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '../uploads/candidates/candidate_692bd7f5a0445.png', '2025-11-30 05:36:53', '2025-11-30 05:36:53'),
(23, 'Amaru Jay', 'Balmes', 'Grid Partylist', 5, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '../uploads/candidates/candidate_692bd8358f3b0.png', '2025-11-30 05:37:57', '2025-11-30 05:38:12'),
(24, 'Jazaira', 'Alias', 'Grid Partylist', 6, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '../uploads/candidates/candidate_692bd869714c8.png', '2025-11-30 05:38:49', '2025-11-30 05:38:49'),
(25, 'Moises', 'Maristela', 'Grid Partylist', 6, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '../uploads/candidates/candidate_692bd945bea16.png', '2025-11-30 05:42:29', '2025-11-30 05:42:29'),
(26, 'Christine', 'Arroyo', 'Grid Partylist', 7, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '../uploads/candidates/candidate_692bd95d2510d.png', '2025-11-30 05:42:53', '2025-11-30 05:42:53'),
(27, 'Imac', 'Uy', 'Grid Partylist', 7, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '../uploads/candidates/candidate_692bd96d57214.png', '2025-11-30 05:43:09', '2025-11-30 05:43:09'),
(28, 'Mark', 'Trilles', 'Grid Partylist', 8, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '../uploads/candidates/candidate_692bd98d8273a.png', '2025-11-30 05:43:41', '2025-11-30 05:43:41'),
(29, 'Rebecca', 'Cabanit', 'Grid Partylist', 10, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '../uploads/candidates/candidate_692bd9a3d30d2.png', '2025-11-30 05:44:03', '2025-11-30 05:44:03'),
(30, 'Justine', 'Sandoval', 'Grid Partylist', 11, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '../uploads/candidates/candidate_692bd9b6e2e70.png', '2025-11-30 05:44:22', '2025-11-30 05:44:22'),
(31, 'Justin', 'Peralta', 'Grid Partylis', 12, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '../uploads/candidates/candidate_692bd9c9a2b4b.png', '2025-11-30 05:44:41', '2025-11-30 05:44:41');

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

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `date`, `end_date`, `is_multi_day`, `location`, `category`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(2, 'Research Colloquium / FOD', 'Defense', '2025-12-01 07:00:00', '2025-12-05 07:00:00', 1, 'CCS Building', 'academic', 'completed', 'admin', '2025-11-29 12:02:51', '2025-12-14 18:39:40');

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
(25, 'Teacher’s Day Expenses', 136.00, 'EVENT EXPENSES', 'Materials for appreciation wall.', 'Adorable School Supplies', '692c8d1085196_Expense_proof.png', '2025-11-30', '0x807f8161f4032467de1436c08308b6edcaa4d1dfb483d4270b73789c567dfb18', '2025-11-30 18:29:36'),
(27, 'Teacher’s Day Expenses', 3500.00, 'FOOD AND DRINKS', 'Catering Service', 'Thatalicious - Food Packages And Catering Services', '692c9b98809b9_food and drinks.png', '2025-11-30', '0x50a54665ecab60a4a578884c800e1a034a4b040ee621b6dedfa297806ea916e7', '2025-11-30 19:31:36');

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

--
-- Dumping data for table `funds`
--

INSERT INTO `funds` (`id`, `source`, `amount`, `description`, `date_received`, `transaction_hash`, `created_at`) VALUES
(5, 'Manual Entry', 25675.65, 'Budget in bank', '2025-11-30', '0xbad4caa1c80438e344d4bd6072ad6ef56669f998e2ccd03588e2d2863e45023a', '2025-11-30 19:28:59');

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

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`email`, `otp`, `expires_at`) VALUES
('liyaaanping@gmail.com', '392488', '2025-12-09 13:24:09');

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
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `slug`, `description`, `module`, `created_at`) VALUES
(1, 'View Dashboard', 'view_dashboard', 'Access to view the main dashboard', 'dashboard', '2025-11-30 10:07:44'),
(2, 'View Funds', 'view_funds', 'View funds records', 'financial', '2025-11-30 10:07:44'),
(3, 'View Expenses', 'view_expenses', 'View expense records', 'financial', '2025-11-30 10:07:44'),
(4, 'Manage Funds', 'manage_funds', 'Create, edit, delete fund records', 'financial', '2025-11-30 10:07:44'),
(5, 'Manage Expenses', 'manage_expenses', 'Create, edit, delete expense records', 'financial', '2025-11-30 10:07:44'),
(6, 'View Financial Records', 'view_financial_records', 'View all financial records (read-only)', 'financial', '2025-11-30 10:07:44'),
(7, 'View Membership Fee', 'view_membership_fee', 'View membership fee records', 'membership', '2025-11-30 10:07:44'),
(8, 'Modify Membership Fee', 'modify_membership_fee', 'Update membership fee status and receipts', 'membership', '2025-11-30 10:07:44'),
(9, 'View Students', 'view_students', 'View student records', 'students', '2025-11-30 10:07:44'),
(10, 'Manage Students', 'manage_students', 'Archive and manage student records', 'students', '2025-11-30 10:07:44'),
(11, 'Verify Students', 'verify_students', 'Approve or reject student registrations', 'students', '2025-11-30 10:07:44'),
(12, 'View Events', 'view_events', 'View events', 'events', '2025-11-30 10:07:44'),
(14, 'Manage Events', 'manage_events', 'Create, edit, delete events', 'events', '2025-11-30 10:07:44'),
(15, 'Generate Reports', 'generate_reports', 'Generate and view reports', 'reports', '2025-11-30 10:07:44'),
(16, 'Generate Financial Reports', 'generate_financial_reports', 'Generate financial reports', 'reports', '2025-11-30 10:07:44'),
(17, 'Generate Membership Reports', 'generate_membership_reports', 'Generate membership fee reports (paid/unpaid)', 'reports', '2025-11-30 10:07:44'),
(18, 'Generate Event Reports', 'generate_event_reports', 'Generate event reports', 'reports', '2025-11-30 10:07:44'),
(19, 'Export Reports', 'export_reports', 'Export reports to PDF', 'reports', '2025-11-30 10:07:44'),
(20, 'View Election', 'view_election', 'View election data and results', 'elections', '2025-11-30 10:07:44'),
(21, 'Start/End Election', 'manage_election_status', 'Start, stop, and end elections', 'elections', '2025-11-30 10:07:44'),
(22, 'Register Candidates', 'register_candidates', 'Add and manage election candidates', 'elections', '2025-11-30 10:07:44'),
(23, 'Manage Positions', 'manage_positions', 'Create and manage election positions', 'elections', '2025-11-30 10:07:44'),
(24, 'View Election Results', 'view_election_results', 'View election results', 'elections', '2025-11-30 10:07:44'),
(25, 'Generate Election Reports', 'generate_election_reports', 'Generate report of election results', 'elections', '2025-11-30 10:07:44'),
(26, 'View Users', 'view_users', 'View user accounts', 'users', '2025-11-30 10:07:44'),
(27, 'Create Accounts', 'create_accounts', 'Create new user accounts', 'users', '2025-11-30 10:07:44'),
(28, 'Demote Accounts', 'demote_accounts', 'Change user roles (demote/promote)', 'users', '2025-11-30 10:07:44'),
(29, 'Manage Users', 'manage_users', 'Full user management (create, edit, deactivate)', 'users', '2025-11-30 10:07:44'),
(30, 'View Settings', 'view_settings', 'View system settings', 'settings', '2025-11-30 10:07:44'),
(31, 'Manage Settings', 'manage_settings', 'Modify system settings', 'settings', '2025-11-30 10:07:44');

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
-- Table structure for table `role_default_permissions`
--

CREATE TABLE `role_default_permissions` (
  `id` int(11) NOT NULL,
  `role` varchar(50) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_default_permissions`
--

INSERT INTO `role_default_permissions` (`id`, `role`, `permission_id`) VALUES
(1, 'adviser', 1),
(11, 'adviser', 2),
(12, 'adviser', 3),
(13, 'adviser', 4),
(14, 'adviser', 5),
(15, 'adviser', 6),
(16, 'adviser', 7),
(17, 'adviser', 8),
(25, 'adviser', 9),
(26, 'adviser', 10),
(27, 'adviser', 11),
(8, 'adviser', 12),
(10, 'adviser', 14),
(18, 'adviser', 15),
(19, 'adviser', 16),
(20, 'adviser', 17),
(21, 'adviser', 18),
(22, 'adviser', 19),
(2, 'adviser', 20),
(3, 'adviser', 21),
(4, 'adviser', 22),
(5, 'adviser', 23),
(6, 'adviser', 24),
(7, 'adviser', 25),
(28, 'adviser', 26),
(29, 'adviser', 27),
(30, 'adviser', 28),
(31, 'adviser', 29),
(23, 'adviser', 30),
(24, 'adviser', 31),
(81, 'auditor', 1),
(84, 'auditor', 2),
(83, 'auditor', 3),
(85, 'auditor', 7),
(80, 'auditor', 8),
(82, 'auditor', 12),
(79, 'auditor', 15),
(78, 'auditor', 16),
(77, 'auditor', 19),
(95, 'comelec', 1),
(92, 'comelec', 19),
(96, 'comelec', 20),
(94, 'comelec', 21),
(97, 'comelec', 24),
(93, 'comelec', 25),
(35, 'dean', 1),
(41, 'dean', 2),
(39, 'dean', 3),
(40, 'dean', 6),
(42, 'dean', 9),
(38, 'dean', 12),
(36, 'dean', 20),
(37, 'dean', 24),
(43, 'dean', 26),
(32, 'dean', 27),
(33, 'dean', 28),
(34, 'dean', 29),
(103, 'event_coordinator', 1),
(107, 'event_coordinator', 2),
(105, 'event_coordinator', 3),
(106, 'event_coordinator', 6),
(104, 'event_coordinator', 12),
(102, 'event_coordinator', 14),
(101, 'event_coordinator', 18),
(100, 'event_coordinator', 19),
(121, 'officer', 1),
(124, 'officer', 12),
(122, 'officer', 20),
(123, 'officer', 24),
(53, 'president', 1),
(56, 'president', 2),
(55, 'president', 3),
(57, 'president', 7),
(52, 'president', 8),
(54, 'president', 12),
(51, 'president', 15),
(49, 'president', 16),
(50, 'president', 17),
(48, 'president', 18),
(47, 'president', 19),
(117, 'secretary', 1),
(119, 'secretary', 9),
(116, 'secretary', 10),
(118, 'secretary', 12),
(115, 'secretary', 15),
(114, 'secretary', 19),
(68, 'treasurer', 1),
(71, 'treasurer', 2),
(70, 'treasurer', 3),
(66, 'treasurer', 4),
(65, 'treasurer', 5),
(72, 'treasurer', 7),
(67, 'treasurer', 8),
(69, 'treasurer', 12),
(64, 'treasurer', 15),
(63, 'treasurer', 16),
(62, 'treasurer', 19);

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
  `profile_image` varchar(255) DEFAULT NULL COMMENT 'Path to student profile image',
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
  `membership_control_number` varchar(10) DEFAULT NULL COMMENT 'Sequential control number for membership fee payment (e.g., 001, 002, 003)',
  `membership_processed_by` varchar(255) DEFAULT NULL COMMENT 'Name of admin who processed the payment',
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

INSERT INTO `students` (`id`, `first_name`, `middle_name`, `last_name`, `email`, `password`, `year_level`, `section`, `course`, `gender`, `profile_image`, `receipt_file`, `payment_status`, `is_archived`, `archived_at`, `archived_by`, `restored_at`, `restored_by`, `is_active`, `created_at`, `updated_at`, `membership_fee_status`, `membership_fee_receipt`, `membership_control_number`, `membership_processed_by`, `membership_fee_paid_at`, `academic_year`, `semester`, `date_of_birth`, `phone_number`, `address`) VALUES
('0122-1141', 'Roswell James', 'Democrito', 'Vitaliz', 'roswelljamesvitaliz@gmail.com', '$2y$10$vsL/s50f.RWXl.995TSz4OpAOoz26XiKLXypG35tJnNhbj35685Hm', 4, 'A', 'BSIT', 'male', 'uploads/student-profiles/profile_0122-1141_1765729381.png', NULL, 'unpaid', 0, NULL, NULL, NULL, NULL, 1, '2025-10-30 15:40:08', '2025-12-21 17:17:31', 'paid', NULL, '001', 'SOCCS Treasurer', '2025-12-18 16:00:00', '2025-2026', 'First (1st) Semester', '2001-02-21', '09212729043', 'Narra Layugan Pagsanjan, Laguna 4008');

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
  `academic_year` varchar(20) DEFAULT NULL COMMENT 'Academic Year extracted from COR (e.g., 2025-2026)',
  `semester` varchar(50) DEFAULT NULL COMMENT 'Semester extracted from COR (e.g., First (1st) Semester, Second (2nd) Semester)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_registrations`
--

INSERT INTO `student_registrations` (`id`, `first_name`, `middle_name`, `last_name`, `email`, `password`, `course`, `year_level`, `section`, `gender`, `student_id_image`, `cor_file`, `set_password_token`, `set_password_expires_at`, `approval_status`, `created_at`, `approved_at`, `rejected_at`, `approved_by`, `rejection_reason`, `academic_year`, `semester`) VALUES
('0122-1141', 'Roswell James', 'Democrito', 'Vitaliz', 'roswelljamesvitaliz@gmail.com', '$2y$10$Da3j2HmlBvw/K7xKYfOvkOp.B9lfAi6z.e1yiBYWBhw5sMmjku7mq', 'BSIT', 4, 'A', 'male', 'uploads/student-ids/0122-1141.png', 'uploads/documents/690386d1a1db9_COR_0122-1141.png', NULL, NULL, 'approved', '2025-10-30 15:40:01', '2025-10-30 15:40:08', NULL, 'System', NULL, '2025-2026', NULL);

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
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `role`, `status`, `last_login`, `created_by`, `created_at`, `updated_at`) VALUES
(3, 'SOCCS', 'Adviser', 'soccsadviser.test@gmail.com', '$2y$10$R731fPWbB7cv6RyNF3aU6OptcMzwaiW9bPrtsmJvpM3.jiRtim3gm', 'adviser', 'active', '2026-01-03 00:29:02', NULL, '2025-04-18 12:18:54', '2026-01-02 16:29:02'),
(4, 'SOCCS', 'Treasurer', 'treasurer.test@gmail.com', '$2y$10$4G4CH6xpxEpcF4MIPBSLf.n4b1IbulEMDRO8z9S41YbfHXhhgF4ri', 'treasurer', 'active', '2025-12-22 01:01:18', 3, '2025-11-30 10:30:17', '2026-01-02 15:58:10'),
(5, 'CCS', 'Dean', 'dean.test@gmail.com', '$2y$10$QSqaMWLZBGpE01ohVv7Yp.pWPPOGZ7OhKLaNq6ebp64ZDonuZXyQ6', 'dean', 'active', '2025-12-21 23:38:00', 3, '2025-11-30 15:57:11', '2025-12-21 15:38:00'),
(6, 'Event', 'Coordinator', 'event.test@gmail.com', '$2y$10$FPwqpLlHkwC4AdtZitIkIeLb3e5EpIdh4Sr6s2vP6S0rbRElnkome', 'event_coordinator', 'active', '2025-12-22 00:43:45', 3, '2025-11-30 16:00:59', '2025-12-21 16:43:45'),
(7, 'Comelec', 'Officer', 'comelec.test@gmail.com', '$2y$10$v6r8pzx6Q5LuGvaZSR9eg.Twgc1p.aFe1OGC0b8Xkpj.WzEmhfHca', 'comelec', 'active', '2025-12-22 00:02:13', 3, '2025-11-30 16:03:18', '2025-12-21 16:02:13');

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

--
-- Dumping data for table `user_activity_log`
--

INSERT INTO `user_activity_log` (`id`, `user_id`, `action`, `target_user_id`, `details`, `ip_address`, `created_at`) VALUES
(1, 0, 'update_user', 3, '{\"first_name\":\"SOCCS\",\"last_name\":\"Adviser\",\"email\":\"soccsadviser.test@gmail.com\",\"role\":{\"from\":\"adviser\",\"to\":\"adviser\"},\"status\":\"active\",\"password\":\"changed\"}', '::1', '2025-11-30 10:10:44'),
(2, 3, 'create_user', 4, '{\"email\":\"treasurer.test@gmail.com\",\"role\":\"treasurer\"}', '::1', '2025-11-30 10:30:17'),
(3, 3, 'update_user', 4, '{\"permissions\":\"updated\"}', '::1', '2025-11-30 10:41:49'),
(4, 3, 'create_user', 5, '{\"email\":\"dean.test@gmail.com\",\"role\":\"dean\"}', '::1', '2025-11-30 15:57:11'),
(5, 3, 'create_user', 6, '{\"email\":\"event.test@gmail.com\",\"role\":\"event_coordinator\"}', '::1', '2025-11-30 16:00:59'),
(6, 3, 'create_user', 7, '{\"email\":\"comelec.test@gmail.com\",\"role\":\"comelec\"}', '::1', '2025-11-30 16:03:18'),
(7, 3, 'update_user', 7, '{\"permissions\":\"updated\"}', '::1', '2025-11-30 16:06:03');

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
-- Dumping data for table `user_permissions`
--

INSERT INTO `user_permissions` (`id`, `user_id`, `permission_id`, `granted_by`, `granted_at`) VALUES
(1, 3, 1, 3, '2025-11-30 10:07:45'),
(2, 3, 20, 3, '2025-11-30 10:07:45'),
(3, 3, 21, 3, '2025-11-30 10:07:45'),
(4, 3, 22, 3, '2025-11-30 10:07:45'),
(5, 3, 23, 3, '2025-11-30 10:07:45'),
(6, 3, 24, 3, '2025-11-30 10:07:45'),
(7, 3, 25, 3, '2025-11-30 10:07:45'),
(8, 3, 12, 3, '2025-11-30 10:07:45'),
(10, 3, 14, 3, '2025-11-30 10:07:45'),
(11, 3, 2, 3, '2025-11-30 10:07:45'),
(12, 3, 3, 3, '2025-11-30 10:07:45'),
(13, 3, 4, 3, '2025-11-30 10:07:45'),
(14, 3, 5, 3, '2025-11-30 10:07:45'),
(15, 3, 6, 3, '2025-11-30 10:07:45'),
(16, 3, 7, 3, '2025-11-30 10:07:45'),
(17, 3, 8, 3, '2025-11-30 10:07:45'),
(18, 3, 15, 3, '2025-11-30 10:07:45'),
(19, 3, 16, 3, '2025-11-30 10:07:45'),
(20, 3, 17, 3, '2025-11-30 10:07:45'),
(21, 3, 18, 3, '2025-11-30 10:07:45'),
(22, 3, 19, 3, '2025-11-30 10:07:45'),
(23, 3, 30, 3, '2025-11-30 10:07:45'),
(24, 3, 31, 3, '2025-11-30 10:07:45'),
(25, 3, 9, 3, '2025-11-30 10:07:45'),
(26, 3, 10, 3, '2025-11-30 10:07:45'),
(27, 3, 11, 3, '2025-11-30 10:07:45'),
(28, 3, 26, 3, '2025-11-30 10:07:45'),
(29, 3, 27, 3, '2025-11-30 10:07:45'),
(30, 3, 28, 3, '2025-11-30 10:07:45'),
(31, 3, 29, 3, '2025-11-30 10:07:45'),
(58, 5, 1, 3, '2025-11-30 15:57:11'),
(59, 5, 2, 3, '2025-11-30 15:57:11'),
(60, 5, 3, 3, '2025-11-30 15:57:11'),
(61, 5, 6, 3, '2025-11-30 15:57:11'),
(62, 5, 9, 3, '2025-11-30 15:57:11'),
(63, 5, 12, 3, '2025-11-30 15:57:11'),
(64, 5, 20, 3, '2025-11-30 15:57:11'),
(65, 5, 24, 3, '2025-11-30 15:57:11'),
(66, 5, 26, 3, '2025-11-30 15:57:11'),
(67, 5, 27, 3, '2025-11-30 15:57:11'),
(68, 5, 28, 3, '2025-11-30 15:57:11'),
(69, 5, 29, 3, '2025-11-30 15:57:11'),
(73, 6, 1, 3, '2025-11-30 16:00:59'),
(74, 6, 2, 3, '2025-11-30 16:00:59'),
(75, 6, 3, 3, '2025-11-30 16:00:59'),
(76, 6, 6, 3, '2025-11-30 16:00:59'),
(77, 6, 12, 3, '2025-11-30 16:00:59'),
(79, 6, 14, 3, '2025-11-30 16:00:59'),
(80, 6, 18, 3, '2025-11-30 16:00:59'),
(81, 6, 19, 3, '2025-11-30 16:00:59'),
(129, 7, 1, 5, '2025-12-21 16:02:01'),
(130, 7, 25, 5, '2025-12-21 16:02:01'),
(131, 7, 23, 5, '2025-12-21 16:02:01'),
(132, 7, 22, 5, '2025-12-21 16:02:01'),
(133, 7, 21, 5, '2025-12-21 16:02:01'),
(134, 7, 20, 5, '2025-12-21 16:02:01'),
(135, 7, 24, 5, '2025-12-21 16:02:01'),
(136, 7, 12, 5, '2025-12-21 16:02:01'),
(137, 7, 19, 5, '2025-12-21 16:02:01'),
(138, 7, 26, 5, '2025-12-21 16:02:01'),
(177, 4, 1, 3, '2025-12-21 17:00:13'),
(178, 4, 5, 3, '2025-12-21 17:00:13'),
(179, 4, 4, 3, '2025-12-21 17:00:13'),
(180, 4, 3, 3, '2025-12-21 17:00:13'),
(181, 4, 2, 3, '2025-12-21 17:00:13'),
(182, 4, 8, 3, '2025-12-21 17:00:13'),
(183, 4, 7, 3, '2025-12-21 17:00:13'),
(184, 4, 19, 3, '2025-12-21 17:00:13'),
(185, 4, 16, 3, '2025-12-21 17:00:13'),
(186, 4, 15, 3, '2025-12-21 17:00:13');

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
-- Stand-in structure for view `voting_history`
-- (See below for the actual view)
--
CREATE TABLE `voting_history` (
`voter_id` varchar(20)
,`election_id` int(11)
,`election_title` varchar(255)
,`vote_date` date
,`positions_voted` bigint(21)
,`total_votes` bigint(21)
,`transaction_hash` varchar(255)
);

-- --------------------------------------------------------

--
-- Structure for view `voting_history`
--
DROP TABLE IF EXISTS `voting_history`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `voting_history`  AS SELECT DISTINCT `v`.`voter_id` AS `voter_id`, `e`.`id` AS `election_id`, `e`.`title` AS `election_title`, cast(min(`v`.`voted_at`) as date) AS `vote_date`, count(distinct `v`.`position_id`) AS `positions_voted`, count(`v`.`id`) AS `total_votes`, max(`e`.`transaction_hash`) AS `transaction_hash` FROM (`votes` `v` join `elections` `e` on(`v`.`election_id` = `e`.`id`)) GROUP BY `v`.`voter_id`, `e`.`id`, `e`.`title` ORDER BY cast(min(`v`.`voted_at`) as date) DESC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_activity_type` (`activity_type`),
  ADD KEY `idx_module` (`module`),
  ADD KEY `idx_created_at` (`created_at`);

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
-- Indexes for table `funds`
--
ALTER TABLE `funds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_date_received` (`date_received`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `masterlist`
--
ALTER TABLE `masterlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_id` (`student_id`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_course_section` (`course`,`section`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`email`),
  ADD KEY `idx_password_resets_expires_at` (`expires_at`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_module` (`module`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `description` (`description`),
  ADD KEY `idx_description` (`description`);

--
-- Indexes for table `role_default_permissions`
--
ALTER TABLE `role_default_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_role_permission` (`role`,`permission_id`),
  ADD KEY `idx_role` (`role`);

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
  ADD KEY `idx_students_membership_status` (`membership_fee_status`),
  ADD KEY `idx_students_profile_image` (`profile_image`),
  ADD KEY `idx_membership_control_number` (`membership_control_number`);

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
-- Indexes for table `user_activity_log`
--
ALTER TABLE `user_activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_permission` (`user_id`,`permission_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_permission` (`permission_id`);

--
-- Indexes for table `votes`
--
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

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `elections`
--
ALTER TABLE `elections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `funds`
--
ALTER TABLE `funds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `masterlist`
--
ALTER TABLE `masterlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=155;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `role_default_permissions`
--
ALTER TABLE `role_default_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_activity_log`
--
ALTER TABLE `user_activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=187;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `fk_activity_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `candidates`
--
ALTER TABLE `candidates`
  ADD CONSTRAINT `candidates_ibfk_1` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD CONSTRAINT `fk_user_permissions_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_permissions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
