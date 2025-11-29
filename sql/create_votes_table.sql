-- Create votes table for election system
-- Run this SQL in phpMyAdmin to create the votes table

CREATE TABLE IF NOT EXISTS `votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `election_id` int(11) NOT NULL,
  `voter_id` varchar(20) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  `vote_hash` varchar(255) DEFAULT NULL COMMENT 'Blockchain hash for vote verification',
  `voted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  FOREIGN KEY (`election_id`) REFERENCES `elections`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`voter_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`candidate_id`) REFERENCES `candidates`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`position_id`) REFERENCES `positions`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_vote_per_position` (`election_id`, `voter_id`, `position_id`),
  INDEX `idx_election` (`election_id`),
  INDEX `idx_voter` (`voter_id`),
  INDEX `idx_candidate` (`candidate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- This table stores:
-- - election_id: Which election this vote belongs to
-- - voter_id: Student ID who cast the vote
-- - candidate_id: Which candidate was voted for
-- - position_id: Which position this vote is for
-- - vote_hash: Blockchain verification hash
-- - voted_at: Timestamp when vote was cast
-- 
-- The UNIQUE KEY ensures:
-- - Each student can only vote ONCE per position in each election

