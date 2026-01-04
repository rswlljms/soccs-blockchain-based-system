-- Add student_id_image column to student_registrations table
-- This allows the system to accept either Student ID image or COR

-- Check if column exists before adding (MySQL/MariaDB compatible)
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
  'SELECT "Column already exists, skipping..."',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' varchar(255) DEFAULT NULL AFTER gender')
));
PREPARE alterIfExists FROM @preparedStatement;
EXECUTE alterIfExists;
DEALLOCATE PREPARE alterIfExists;

