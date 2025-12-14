-- =====================================================
-- Remove Age Field from Database
-- Created: December 14, 2025
-- Description: Removes the age column from both students and student_registrations tables
-- =====================================================

USE soccs_financial_management;

-- Step 1: Remove age column from students table
ALTER TABLE `students` DROP COLUMN `age`;

-- Step 2: Remove age column from student_registrations table
ALTER TABLE `student_registrations` DROP COLUMN `age`;

-- Verification Query (Run these to verify the changes)
-- DESCRIBE students;
-- DESCRIBE student_registrations;

-- =====================================================
-- ROLLBACK (if needed)
-- =====================================================
-- If you need to add the age field back, run these commands:
-- 
-- ALTER TABLE `students` ADD COLUMN `age` int(3) DEFAULT NULL AFTER `course`;
-- ALTER TABLE `student_registrations` ADD COLUMN `age` int(3) NOT NULL AFTER `section`;
