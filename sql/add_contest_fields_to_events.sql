-- Add contest-related fields to events table
-- This allows events to have contests with registration links

ALTER TABLE `events` 
ADD COLUMN `has_contest` boolean DEFAULT FALSE AFTER `status`,
ADD COLUMN `contest_details` text DEFAULT NULL AFTER `has_contest`,
ADD COLUMN `registration_link` varchar(500) DEFAULT NULL AFTER `contest_details`;

