-- Update Expense Categories Migration
-- This script updates existing expense categories to match the new naming convention
-- Run this script to update existing records in the expenses table

-- Update Food to FOOD AND DRINKS
UPDATE expenses 
SET category = 'FOOD AND DRINKS' 
WHERE category = 'Food';

-- Update Supplies to OFFICE SUPPLIES
UPDATE expenses 
SET category = 'OFFICE SUPPLIES' 
WHERE category = 'Supplies';

-- Update Events to EVENT EXPENSES
UPDATE expenses 
SET category = 'EVENT EXPENSES' 
WHERE category = 'Events';

-- Update Transport to TRANSPORT
UPDATE expenses 
SET category = 'TRANSPORT' 
WHERE category = 'Transport';

-- Note: New categories (TOKEN/GIVEAWAY and CLEANING MATERIALS) will be available
-- for new expense entries. No existing records need to be updated for these.

