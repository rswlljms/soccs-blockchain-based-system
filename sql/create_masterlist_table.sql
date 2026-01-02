-- Masterlist table for storing uploaded student masterlist data
-- Used for registration validation

CREATE TABLE IF NOT EXISTS `masterlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `student_id` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `course` varchar(10) DEFAULT NULL,
  `section` varchar(1) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uploaded_by` varchar(255) DEFAULT NULL,
  UNIQUE KEY `unique_student_id` (`student_id`),
  INDEX `idx_student_id` (`student_id`),
  INDEX `idx_name` (`name`),
  INDEX `idx_course_section` (`course`, `section`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

