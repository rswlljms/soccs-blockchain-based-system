-- Events table for SOCCS Event Management
-- This table stores all events visible to both admin and students

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(255) NOT NULL,
  `description` text,
  `date` datetime NOT NULL,
  `end_date` datetime DEFAULT NULL,
  `is_multi_day` boolean DEFAULT FALSE,
  `location` varchar(255),
  `category` varchar(50) DEFAULT 'general',
  `status` enum('upcoming','ongoing','completed','cancelled','archived') DEFAULT 'upcoming',
  `created_by` varchar(255),
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_date` (`date`),
  INDEX `idx_status` (`status`),
  INDEX `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample events for demonstration (November 2025 - February 2026)
INSERT INTO `events` (`title`, `description`, `date`, `location`, `category`, `status`, `created_by`) VALUES
('SOCCS General Meeting', 'Monthly organizational meeting for all members', '2025-11-30 14:00:00', 'Main Auditorium', 'academic', 'upcoming', 'admin'),
('Annual Tech Summit', 'Technology showcase and presentations from industry experts', '2025-12-05 09:00:00', 'Main Auditorium', 'academic', 'upcoming', 'admin'),
('Programming Competition', 'Annual coding competition for all year levels', '2025-12-10 13:00:00', 'Computer Laboratory', 'competition', 'upcoming', 'admin'),
('Year-End Social', 'Social gathering for all SOCCS members', '2025-12-15 18:00:00', 'Function Hall', 'social', 'upcoming', 'admin'),
('Web Development Workshop', 'Hands-on web development training session', '2025-12-20 14:00:00', 'Computer Laboratory', 'workshop', 'upcoming', 'admin'),
('Leadership Training', 'Leadership development for SOCCS officers', '2026-01-15 10:00:00', 'Conference Room A', 'workshop', 'upcoming', 'admin'),
('SOCCS General Assembly', 'Monthly general assembly meeting', '2026-02-05 14:00:00', 'Main Auditorium', 'academic', 'upcoming', 'admin'),
('Hackathon 2026', '24-hour coding challenge', '2026-02-20 08:00:00', 'Computer Laboratory', 'competition', 'upcoming', 'admin');

