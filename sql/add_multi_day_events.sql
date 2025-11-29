-- Add support for multi-day events
-- Run this to update your existing events table

ALTER TABLE `events` 
ADD COLUMN `end_date` datetime DEFAULT NULL AFTER `date`,
ADD COLUMN `is_multi_day` boolean DEFAULT FALSE AFTER `end_date`;

-- Update existing events to set is_multi_day flag
UPDATE `events` SET `is_multi_day` = FALSE WHERE `end_date` IS NULL;

-- Example: Update an existing event to be multi-day
-- UPDATE `events` SET `end_date` = '2025-12-17 18:00:00', `is_multi_day` = TRUE WHERE id = 1;

