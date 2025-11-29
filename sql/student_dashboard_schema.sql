-- Additional tables for Student Dashboard functionality
-- Add these to your existing soccs_financial_management database

-- Students table
CREATE TABLE IF NOT EXISTS `students` (
  `id` varchar(20) NOT NULL PRIMARY KEY,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100),
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) UNIQUE,
  `password` varchar(255),
  `year_level` int(1) NOT NULL,
  `section` varchar(1) NOT NULL,
  `course` varchar(10) NOT NULL DEFAULT 'BSIT',
  `age` int(3),
  `gender` enum('male','female','other') NOT NULL,
  `is_active` boolean DEFAULT true,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Elections table
CREATE TABLE IF NOT EXISTS `elections` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(255) NOT NULL,
  `description` text,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` enum('upcoming','active','completed','cancelled') DEFAULT 'upcoming',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Candidates table
CREATE TABLE IF NOT EXISTS `candidates` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `election_id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `position` varchar(100) NOT NULL,
  `partylist` varchar(100),
  `platform` text,
  `photo` varchar(255),
  `is_approved` boolean DEFAULT false,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`election_id`) REFERENCES `elections`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Votes table (blockchain-secured)
CREATE TABLE IF NOT EXISTS `votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `election_id` int(11) NOT NULL,
  `voter_id` varchar(20) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `position` varchar(100) NOT NULL,
  `vote_hash` varchar(255) NOT NULL, -- Blockchain hash
  `voted_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`election_id`) REFERENCES `elections`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`voter_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`candidate_id`) REFERENCES `candidates`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_vote` (`election_id`, `voter_id`, `position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Events table
CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(255) NOT NULL,
  `description` text,
  `date` datetime NOT NULL,
  `location` varchar(255),
  `category` varchar(50) DEFAULT 'general',
  `status` enum('upcoming','ongoing','completed','cancelled') DEFAULT 'upcoming',
  `created_by` varchar(255),
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Announcements table
CREATE TABLE IF NOT EXISTS `announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `type` enum('info','important','success','warning') DEFAULT 'info',
  `is_active` boolean DEFAULT true,
  `target_audience` enum('all','students','admin') DEFAULT 'all',
  `created_by` varchar(255),
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Funds table (read-only for students)
CREATE TABLE IF NOT EXISTS `funds` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `source` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text,
  `date_received` date NOT NULL,
  `transaction_hash` varchar(255),
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Student Sessions table for authentication
CREATE TABLE IF NOT EXISTS `student_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `student_id` varchar(20) NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  INDEX (`session_token`),
  INDEX (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample data

-- Sample students
INSERT INTO `students` (`id`, `first_name`, `middle_name`, `last_name`, `email`, `password`, `year_level`, `section`, `course`, `age`, `gender`) VALUES
('ST2021001', 'John', 'D.', 'Doe', 'john.doe@student.edu', '$2y$10$4OXZcgtKLD3UH2GMyZK09ui6mgtIPCJyex/lsmhLS.brVzIXi/h32', 3, 'A', 'BSIT', 21, 'male'),
('ST2021002', 'Jane', 'M.', 'Smith', 'jane.smith@student.edu', '$2y$10$4OXZcgtKLD3UH2GMyZK09ui6mgtIPCJyex/lsmhLS.brVzIXi/h32', 2, 'B', 'BSCS', 20, 'female'),
('ST2021003', 'Mike', 'R.', 'Johnson', 'mike.johnson@student.edu', '$2y$10$4OXZcgtKLD3UH2GMyZK09ui6mgtIPCJyex/lsmhLS.brVzIXi/h32', 4, 'A', 'BSIT', 22, 'male');

-- Sample election
INSERT INTO `elections` (`title`, `description`, `start_date`, `end_date`, `status`) VALUES
('SOCCS General Elections 2024', 'Annual student organization elections for all officer positions', '2024-12-15 08:00:00', '2024-12-15 18:00:00', 'active');

-- Sample candidates
INSERT INTO `candidates` (`election_id`, `student_id`, `position`, `partylist`, `platform`, `is_approved`) VALUES
(1, 'ST2021001', 'President', 'Tech Forward', 'Advancing technology education and student welfare', true),
(1, 'ST2021002', 'Vice President', 'Tech Forward', 'Supporting innovative learning approaches', true),
(1, 'ST2021003', 'Secretary', 'Innovation Party', 'Improving communication and transparency', true);

-- Sample events
INSERT INTO `events` (`title`, `description`, `date`, `location`, `category`, `status`) VALUES
('Annual Tech Summit', 'Technology showcase and presentations from industry experts', '2024-12-15 09:00:00', 'Main Auditorium', 'academic', 'upcoming'),
('Programming Competition', 'Annual coding competition for all year levels', '2024-12-18 13:00:00', 'Computer Laboratory', 'competition', 'upcoming'),
('Year-End Social', 'Social gathering for all SOCCS members', '2024-12-22 18:00:00', 'Function Hall', 'social', 'upcoming');

-- Sample announcements
INSERT INTO `announcements` (`title`, `content`, `type`, `target_audience`) VALUES
('Election Voting Extended', 'Due to technical issues, voting period has been extended by 24 hours.', 'important', 'students'),
('New Academic Calendar', 'Updated academic calendar for next semester is now available on the portal.', 'info', 'students'),
('System Maintenance Complete', 'All systems are now running normally after scheduled maintenance.', 'success', 'all');

-- Sample funds data
INSERT INTO `funds` (`source`, `amount`, `description`, `date_received`, `transaction_hash`) VALUES
('Membership Fees', 15000.00, 'Annual membership fees collection', '2024-12-08', '0x4d5e6f789abc...'),
('School Grant', 50000.00, 'Grant from school administration', '2024-11-15', '0x1a2b3c456def...'),
('Fundraising Event', 20450.00, 'Proceeds from tech fair fundraising', '2024-10-20', '0x7g8h9i012ghi...');

-- Sample expenses (already exists, just adding more data)
INSERT INTO `expenses` (`name`, `amount`, `category`, `description`, `supplier`, `date`, `transaction_hash`) VALUES
('Event Supplies Purchase', 2450.00, 'Events', 'Supplies for upcoming tech summit', 'Office Depot', '2024-12-10', '0x1a2b3c456def...'),
('Office Supplies', 890.75, 'Administrative', 'Stationery and office materials', 'National Bookstore', '2024-12-05', '0x7g8h9i012ghi...'),
('Equipment Maintenance', 1200.00, 'Maintenance', 'Computer lab equipment servicing', 'Tech Solutions Inc.', '2024-11-28', '0x4d5e6f789abc...');
