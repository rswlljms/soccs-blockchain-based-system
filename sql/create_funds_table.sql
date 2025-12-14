-- Create funds table for managing fund records
CREATE TABLE IF NOT EXISTS `funds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source` varchar(255) DEFAULT 'Manual Entry',
  `amount` decimal(10,2) NOT NULL,
  `description` text NOT NULL,
  `date_received` date NOT NULL,
  `transaction_hash` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_date_received` (`date_received`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample data
INSERT INTO `funds` (`source`, `amount`, `description`, `date_received`) VALUES
('Manual Entry', 10000.00, 'Initial Funding', '2025-04-22'),
('Manual Entry', 5000.00, 'Sponsor Contribution', '2025-04-23');








