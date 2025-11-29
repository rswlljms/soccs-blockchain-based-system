-- Document verification support
-- Adds columns and tables to support AI-based document verification workflow

-- 1) Extend student_registrations with document review statuses and references
ALTER TABLE `student_registrations`
  ADD COLUMN IF NOT EXISTS `email` varchar(255) NULL AFTER `last_name`,
  ADD COLUMN IF NOT EXISTS `student_id_image` varchar(255) NULL AFTER `gender`,
  ADD COLUMN IF NOT EXISTS `cor_file` varchar(255) NULL AFTER `student_id_image`,
  ADD COLUMN IF NOT EXISTS `set_password_token` varchar(255) NULL AFTER `cor_file`,
  ADD COLUMN IF NOT EXISTS `set_password_expires_at` datetime NULL AFTER `set_password_token`,
  ADD COLUMN IF NOT EXISTS `document_status` enum('under_review','verified','failed') DEFAULT 'under_review' AFTER `approval_status`,
  ADD COLUMN IF NOT EXISTS `document_review_started_at` datetime NULL AFTER `document_status`,
  ADD COLUMN IF NOT EXISTS `document_review_completed_at` datetime NULL AFTER `document_review_started_at`;

-- 2) Create verification jobs queue
CREATE TABLE IF NOT EXISTS `document_verification_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` varchar(20) NOT NULL,
  `status` enum('queued','processing','completed','failed','cancelled') NOT NULL DEFAULT 'queued',
  `min_process_after` datetime NOT NULL,
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `result` enum('valid','invalid','mismatch','tampered','low_quality') DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `retries` int unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_jobs_status_time` (`status`,`min_process_after`),
  KEY `idx_jobs_student` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3) Create verification audit table
CREATE TABLE IF NOT EXISTS `document_verification_results` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` varchar(20) NOT NULL,
  `student_id_image_path` varchar(255) DEFAULT NULL,
  `cor_file_path` varchar(255) DEFAULT NULL,
  `is_valid_id` tinyint(1) DEFAULT NULL,
  `is_valid_cor` tinyint(1) DEFAULT NULL,
  `name_match` tinyint(1) DEFAULT NULL,
  `student_number_match` tinyint(1) DEFAULT NULL,
  `course_match` tinyint(1) DEFAULT NULL,
  `year_level_match` tinyint(1) DEFAULT NULL,
  `tamper_score` decimal(5,2) DEFAULT NULL,
  `quality_score` decimal(5,2) DEFAULT NULL,
  `overall_result` enum('valid','invalid','mismatch','tampered','low_quality') DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `processing_time_seconds` int unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_results_student` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Adds columns needed for email-based set-password flow
ALTER TABLE `student_registrations`
  ADD COLUMN IF NOT EXISTS `email` varchar(255) NOT NULL AFTER `last_name`,
  ADD COLUMN IF NOT EXISTS `password` varchar(255) NULL AFTER `email`,
  ADD COLUMN IF NOT EXISTS `set_password_token` varchar(128) NULL AFTER `cor_file`,
  ADD COLUMN IF NOT EXISTS `set_password_expires_at` datetime NULL AFTER `set_password_token`;

  -- Table for OTP-based password resets
  CREATE TABLE IF NOT EXISTS `password_resets` (
    `email` varchar(255) NOT NULL,
    `otp` varchar(6) NOT NULL,
    `expires_at` datetime NOT NULL,
    PRIMARY KEY (`email`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Update students table to add receipt and archive functionality
-- Add membership fee payment status and receipt fields
ALTER TABLE `students` 
ADD COLUMN IF NOT EXISTS `membership_fee_status` ENUM('unpaid', 'paid') DEFAULT 'unpaid',
ADD COLUMN IF NOT EXISTS `membership_fee_receipt` VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `membership_fee_paid_at` TIMESTAMP NULL DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `is_archived` TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS `archived_at` TIMESTAMP NULL DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `archived_by` VARCHAR(255) DEFAULT NULL;

-- Create index for better performance (only if they don't exist)
CREATE INDEX IF NOT EXISTS idx_students_archived ON students(is_archived);
CREATE INDEX IF NOT EXISTS idx_students_membership_status ON students(membership_fee_status);
