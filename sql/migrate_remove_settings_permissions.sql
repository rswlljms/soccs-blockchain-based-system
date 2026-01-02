-- Migration Script: Remove 'view_settings' and 'manage_settings' permissions
-- Date: 2025-12-19
-- Description: Removes Settings permissions from the system

-- Step 1: Remove Settings permissions from user_permissions
DELETE up FROM `user_permissions` up
INNER JOIN `permissions` p ON up.permission_id = p.id
WHERE p.slug IN ('view_settings', 'manage_settings');

-- Step 2: Remove Settings permissions from role_default_permissions
DELETE rdp FROM `role_default_permissions` rdp
INNER JOIN `permissions` p ON rdp.permission_id = p.id
WHERE p.slug IN ('view_settings', 'manage_settings');

-- Step 3: Delete the Settings permission records
DELETE FROM `permissions` WHERE slug IN ('view_settings', 'manage_settings');






