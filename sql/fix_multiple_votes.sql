-- Fix to allow multiple votes per position (based on max_votes setting)
-- Run this SQL in phpMyAdmin

-- Step 1: Drop the existing unique constraint that only allows 1 vote per position
ALTER TABLE votes DROP INDEX unique_vote_per_position;

-- Step 2: Add new constraint that prevents voting for the same candidate twice
-- (but allows voting for multiple different candidates in the same position)
ALTER TABLE votes ADD UNIQUE KEY unique_vote_per_candidate (election_id, voter_id, candidate_id);

