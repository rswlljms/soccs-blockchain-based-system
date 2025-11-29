-- Remove unused document verification tables
-- These were for the old async verification system
-- Now using instant verification during registration

DROP TABLE IF EXISTS `document_verification_jobs`;
DROP TABLE IF EXISTS `document_verification_results`;
