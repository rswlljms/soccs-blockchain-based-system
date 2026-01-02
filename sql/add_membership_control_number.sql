-- Add control number column for membership fee payments
ALTER TABLE `students` 
ADD COLUMN `membership_control_number` VARCHAR(10) DEFAULT NULL COMMENT 'Sequential control number for membership fee payment (e.g., 001, 002, 003)' 
AFTER `membership_fee_receipt`,
ADD COLUMN `membership_processed_by` VARCHAR(255) DEFAULT NULL COMMENT 'Name of admin who processed the payment'
AFTER `membership_control_number`;

-- Add index for better query performance
ALTER TABLE `students` 
ADD INDEX `idx_membership_control_number` (`membership_control_number`);
