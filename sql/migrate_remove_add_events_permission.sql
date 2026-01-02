-- Migration Script: Remove 'add_events' permission and consolidate to 'manage_events'
-- Date: 2025-12-14
-- Description: Consolidates 'add_events' and 'manage_events' into a single 'manage_events' permission

-- Step 1: Grant 'manage_events' permission to all users who have 'add_events'
INSERT INTO `user_permissions` (`user_id`, `permission_id`, `granted_by`)
SELECT DISTINCT up.user_id, 
       (SELECT id FROM permissions WHERE slug = 'manage_events' LIMIT 1) as permission_id,
       up.granted_by
FROM `user_permissions` up
INNER JOIN `permissions` p ON up.permission_id = p.id
WHERE p.slug = 'add_events'
AND NOT EXISTS (
    SELECT 1 FROM `user_permissions` up2
    INNER JOIN `permissions` p2 ON up2.permission_id = p2.id
    WHERE up2.user_id = up.user_id
    AND p2.slug = 'manage_events'
);

-- Step 2: Remove 'add_events' permission from user_permissions
DELETE up FROM `user_permissions` up
INNER JOIN `permissions` p ON up.permission_id = p.id
WHERE p.slug = 'add_events';

-- Step 3: Remove 'add_events' permission from role_default_permissions
DELETE rdp FROM `role_default_permissions` rdp
INNER JOIN `permissions` p ON rdp.permission_id = p.id
WHERE p.slug = 'add_events';

-- Step 4: Delete the 'add_events' permission record
DELETE FROM `permissions` WHERE slug = 'add_events';

-- Step 5: Update permission IDs to maintain sequential order (optional, only if needed)
-- Note: This step may be skipped if you don't need sequential IDs

