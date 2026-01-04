-- Update registration logic to accept either Student ID image or COR
-- Database already has both columns, no changes needed
-- This file documents that the system now accepts EITHER document

-- The student_registrations table already has:
-- - student_id_image varchar(255) DEFAULT NULL
-- - cor_file varchar(255) DEFAULT NULL

-- Both columns can be NULL, but at least one must be provided during registration
-- The system validates name and student ID from whichever document is provided against the masterlist

-- No database schema changes required - existing columns support this logic

