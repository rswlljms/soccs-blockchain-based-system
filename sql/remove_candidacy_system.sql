-- Remove Candidacy Filing System
-- This script removes all tables and permissions related to the candidacy filing system

-- Drop tables (order matters due to foreign keys)
DROP TABLE IF EXISTS `candidacy_applications`;
DROP TABLE IF EXISTS `candidacy_form_fields`;
DROP TABLE IF EXISTS `candidacy_periods`;

-- Remove permissions
DELETE FROM `permissions` WHERE `slug` IN ('view_candidacy', 'manage_candidacy', 'review_candidacy_applications');
DELETE FROM `permissions` WHERE `module` = 'candidacy';

