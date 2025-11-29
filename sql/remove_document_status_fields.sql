-- Remove unused document verification tracking fields
-- These were for the old async verification system
-- Now using instant verification during registration

ALTER TABLE `student_registrations` 
DROP COLUMN IF EXISTS `document_status`,
DROP COLUMN IF EXISTS `document_review_started_at`,
DROP COLUMN IF EXISTS `document_review_completed_at`;
