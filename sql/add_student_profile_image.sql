-- Add profile_image column to students table
-- This allows students to upload and store their profile pictures

ALTER TABLE `students` 
ADD COLUMN `profile_image` VARCHAR(255) DEFAULT NULL 
COMMENT 'Path to student profile image' 
AFTER `gender`;

-- Create index for faster lookups
CREATE INDEX idx_students_profile_image ON students(profile_image);
