-- Create cleanup event for temporary uploaded files
-- This event runs every hour to delete files older than 2 hours from the temp directory

DELIMITER $$

CREATE EVENT IF NOT EXISTS cleanup_temp_uploads
ON SCHEDULE EVERY 1 HOUR
DO
BEGIN
    -- Note: Actual file deletion must be done via PHP script
    -- This is just a placeholder for the event structure
    -- Use a PHP cron job to delete files from uploads/temp/ older than 2 hours
END$$

DELIMITER ;

-- Enable event scheduler
SET GLOBAL event_scheduler = ON;
