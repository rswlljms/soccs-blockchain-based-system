-- Migration: Remove ip_address and user_agent columns from activity_logs table
-- Run this if you already have the activity_logs table with these columns

ALTER TABLE `activity_logs` DROP COLUMN `ip_address`;
ALTER TABLE `activity_logs` DROP COLUMN `user_agent`;

