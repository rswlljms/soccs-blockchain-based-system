-- Remove student_id_image column from student_registrations table
-- This migration removes the Student ID upload requirement from registration
-- Run this SQL in your database to update the schema

-- Check if column exists before dropping (MySQL/MariaDB compatible)
SET @dbname = DATABASE();
SET @tablename = 'student_registrations';
SET @columnname = 'student_id_image';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (TABLE_SCHEMA = @dbname)
      AND (TABLE_NAME = @tablename)
      AND (COLUMN_NAME = @columnname)
  ) > 0,
  CONCAT('ALTER TABLE ', @tablename, ' DROP COLUMN ', @columnname),
  'SELECT "Column does not exist, skipping..."'
));
PREPARE alterIfExists FROM @preparedStatement;
EXECUTE alterIfExists;
DEALLOCATE PREPARE alterIfExists;

