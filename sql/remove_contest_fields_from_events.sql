-- Remove old single contest fields from events table
-- Run this after migrating to event_contests table

ALTER TABLE `events` 
DROP COLUMN IF EXISTS `has_contest`,
DROP COLUMN IF EXISTS `contest_details`,
DROP COLUMN IF EXISTS `registration_link`;

