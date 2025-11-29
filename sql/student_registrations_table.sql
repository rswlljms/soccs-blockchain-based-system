-- Table for storing student registration requests before approval
CREATE TABLE IF NOT EXISTS `student_registrations` (
  `id` varchar(20) NOT NULL PRIMARY KEY,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100),
  `last_name` varchar(100) NOT NULL,
  `course` varchar(10) NOT NULL DEFAULT 'BSIT',
  `year_level` int(1) NOT NULL,
  `section` varchar(1) NOT NULL,
  `age` int(3) NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `student_id_image` varchar(255),
  `approval_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `approved_at` timestamp NULL,
  `rejected_at` timestamp NULL,
  `approved_by` varchar(255) NULL,
  `rejection_reason` text NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample data for testing
INSERT INTO `student_registrations` (`id`, `first_name`, `middle_name`, `last_name`, `course`, `year_level`, `section`, `age`, `gender`, `student_id_image`, `approval_status`) VALUES
('ST2024001', 'Maria', 'C.', 'Garcia', 'BSIT', 1, 'A', 18, 'female', 'uploads/student-ids/ST2024001.jpg', 'pending'),
('ST2024002', 'John', 'M.', 'Santos', 'BSCS', 2, 'B', 19, 'male', 'uploads/student-ids/ST2024002.jpg', 'pending'),
('ST2024003', 'Ana', 'R.', 'Cruz', 'BSIT', 3, 'A', 20, 'female', 'uploads/student-ids/ST2024003.jpg', 'approved'),
('ST2024004', 'Pedro', 'L.', 'Reyes', 'BSCS', 1, 'C', 18, 'male', 'uploads/student-ids/ST2024004.jpg', 'rejected');
