-- Add transaction_hash column to elections table
-- This stores the blockchain transaction hash when an election is finalized
-- Run this only if the column doesn't exist

ALTER TABLE `elections` 
ADD COLUMN `transaction_hash` VARCHAR(255) NULL DEFAULT NULL AFTER `status`;

