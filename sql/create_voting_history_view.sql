-- Create voting history view for students
-- This view groups votes by election and shows when a student participated in each election
-- Run this SQL in phpMyAdmin

CREATE OR REPLACE VIEW `voting_history` AS
SELECT DISTINCT
    v.voter_id,
    e.id AS election_id,
    e.title AS election_title,
    DATE(MIN(v.voted_at)) AS vote_date,
    COUNT(DISTINCT v.position_id) AS positions_voted,
    COUNT(v.id) AS total_votes,
    MAX(e.transaction_hash) AS transaction_hash
FROM votes v
INNER JOIN elections e ON v.election_id = e.id
GROUP BY v.voter_id, e.id, e.title
ORDER BY vote_date DESC;

-- Create index for better performance
CREATE INDEX IF NOT EXISTS `idx_votes_voter_election` ON `votes` (`voter_id`, `election_id`);

-- Example query to get voting history for a specific student:
-- SELECT * FROM voting_history WHERE voter_id = '0122-1141' ORDER BY vote_date DESC;

