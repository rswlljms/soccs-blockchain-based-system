-- User Management Tables Migration
-- Run this SQL to add role-based access control to the system

-- Add new columns to users table
ALTER TABLE `users` 
ADD COLUMN `first_name` varchar(100) NOT NULL DEFAULT '' AFTER `id`,
ADD COLUMN `last_name` varchar(100) NOT NULL DEFAULT '' AFTER `first_name`,
ADD COLUMN `role` ENUM('adviser', 'dean', 'president', 'treasurer', 'auditor', 'secretary', 'comelec', 'event_coordinator', 'officer') NOT NULL DEFAULT 'officer' AFTER `password`,
ADD COLUMN `status` ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active' AFTER `role`,
ADD COLUMN `last_login` datetime DEFAULT NULL AFTER `status`,
ADD COLUMN `created_by` int(11) DEFAULT NULL AFTER `last_login`,
ADD COLUMN `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() AFTER `created_at`;

-- Create permissions table
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `module` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_module` (`module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create user_permissions junction table
CREATE TABLE IF NOT EXISTS `user_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `granted_by` int(11) DEFAULT NULL,
  `granted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_permission` (`user_id`, `permission_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_permission` (`permission_id`),
  CONSTRAINT `fk_user_permissions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_user_permissions_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create role_default_permissions table for preset permissions per role
CREATE TABLE IF NOT EXISTS `role_default_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(50) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_role_permission` (`role`, `permission_id`),
  KEY `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create activity log for audit trail
CREATE TABLE IF NOT EXISTS `user_activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `target_user_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert permissions based on use case diagrams
INSERT INTO `permissions` (`name`, `slug`, `description`, `module`) VALUES
-- Dashboard
('View Dashboard', 'view_dashboard', 'Access to view the main dashboard', 'dashboard'),

-- Financial Module
('View Funds', 'view_funds', 'View funds records', 'financial'),
('View Expenses', 'view_expenses', 'View expense records', 'financial'),
('Manage Funds', 'manage_funds', 'Create, edit, delete fund records', 'financial'),
('Manage Expenses', 'manage_expenses', 'Create, edit, delete expense records', 'financial'),
('View Financial Records', 'view_financial_records', 'View all financial records (read-only)', 'financial'),

-- Membership Fee Module
('View Membership Fee', 'view_membership_fee', 'View membership fee records', 'membership'),
('Modify Membership Fee', 'modify_membership_fee', 'Update membership fee status and receipts', 'membership'),

-- Student Management
('View Students', 'view_students', 'View student records', 'students'),
('Manage Students', 'manage_students', 'Archive and manage student records', 'students'),
('Verify Students', 'verify_students', 'Approve or reject student registrations', 'students'),

-- Events Module
('View Events', 'view_events', 'View events', 'events'),
('Manage Events', 'manage_events', 'Create, edit, delete events', 'events'),

-- Reports Module
('Generate Reports', 'generate_reports', 'Generate and view reports', 'reports'),
('Generate Financial Reports', 'generate_financial_reports', 'Generate financial reports', 'reports'),
('Generate Membership Reports', 'generate_membership_reports', 'Generate membership fee reports (paid/unpaid)', 'reports'),
('Generate Event Reports', 'generate_event_reports', 'Generate event reports', 'reports'),
('Export Reports', 'export_reports', 'Export reports to PDF', 'reports'),

-- Election Module
('View Election', 'view_election', 'View election data and results', 'elections'),
('Start/End Election', 'manage_election_status', 'Start, stop, and end elections', 'elections'),
('Register Candidates', 'register_candidates', 'Add and manage election candidates', 'elections'),
('Manage Positions', 'manage_positions', 'Create and manage election positions', 'elections'),
('View Election Results', 'view_election_results', 'View election results', 'elections'),
('Generate Election Reports', 'generate_election_reports', 'Generate report of election results', 'elections'),

-- User Management Module
('View Users', 'view_users', 'View user accounts', 'users'),
('Create Accounts', 'create_accounts', 'Create new user accounts', 'users'),
('Demote Accounts', 'demote_accounts', 'Change user roles (demote/promote)', 'users'),
('Manage Users', 'manage_users', 'Full user management (create, edit, deactivate)', 'users'),


-- Set up default permissions for each role based on use case diagrams

-- SOCCS ADVISER (Super Admin - Full Access)
INSERT INTO `role_default_permissions` (`role`, `permission_id`)
SELECT 'adviser', id FROM `permissions`;

-- CCS DEAN
INSERT INTO `role_default_permissions` (`role`, `permission_id`)
SELECT 'dean', id FROM `permissions` WHERE slug IN (
    'view_dashboard',
    'view_financial_records',
    'view_funds',
    'view_expenses',
    'view_events',
    'view_election',
    'view_election_results',
    'view_students',
    'view_users',
    'create_accounts',
    'demote_accounts',
    'manage_users'
);

-- SOCCS PRESIDENT
INSERT INTO `role_default_permissions` (`role`, `permission_id`)
SELECT 'president', id FROM `permissions` WHERE slug IN (
    'view_dashboard',
    'view_funds',
    'view_expenses',
    'modify_membership_fee',
    'view_membership_fee',
    'view_events',
    'generate_reports',
    'generate_financial_reports',
    'generate_membership_reports',
    'generate_event_reports',
    'export_reports'
);

-- SOCCS TREASURER
INSERT INTO `role_default_permissions` (`role`, `permission_id`)
SELECT 'treasurer', id FROM `permissions` WHERE slug IN (
    'view_dashboard',
    'view_funds',
    'manage_funds',
    'view_expenses',
    'manage_expenses',
    'view_membership_fee',
    'modify_membership_fee',
    'view_events',
    'generate_reports',
    'generate_financial_reports',
    'export_reports'
);

-- SOCCS AUDITOR
INSERT INTO `role_default_permissions` (`role`, `permission_id`)
SELECT 'auditor', id FROM `permissions` WHERE slug IN (
    'view_dashboard',
    'view_funds',
    'view_expenses',
    'view_membership_fee',
    'modify_membership_fee',
    'view_events',
    'generate_reports',
    'generate_financial_reports',
    'export_reports'
);

-- COMELEC
INSERT INTO `role_default_permissions` (`role`, `permission_id`)
SELECT 'comelec', id FROM `permissions` WHERE slug IN (
    'view_dashboard',
    'view_election',
    'manage_election_status',
    'view_election_results',
    'generate_election_reports',
    'export_reports'
);

-- SOCCS EVENT COORDINATOR
INSERT INTO `role_default_permissions` (`role`, `permission_id`)
SELECT 'event_coordinator', id FROM `permissions` WHERE slug IN (
    'view_dashboard',
    'view_events',
    'manage_events',
    'view_financial_records',
    'view_funds',
    'view_expenses',
    'generate_event_reports',
    'export_reports'
);

-- SECRETARY
INSERT INTO `role_default_permissions` (`role`, `permission_id`)
SELECT 'secretary', id FROM `permissions` WHERE slug IN (
    'view_dashboard',
    'view_students',
    'manage_students',
    'view_events',
    'generate_reports',
    'export_reports'
);

-- OFFICER (Basic access)
INSERT INTO `role_default_permissions` (`role`, `permission_id`)
SELECT 'officer', id FROM `permissions` WHERE slug IN (
    'view_dashboard',
    'view_events',
    'view_election',
    'view_election_results'
);

-- Update existing super admin user
UPDATE `users` SET 
  `first_name` = 'SOCCS',
  `last_name` = 'Adviser',
  `role` = 'adviser',
  `status` = 'active'
WHERE `email` = 'lspuscc.soccs@gmail.com';

-- Grant all permissions to existing adviser (user id 3)
INSERT INTO `user_permissions` (`user_id`, `permission_id`, `granted_by`)
SELECT 3, id, 3 FROM `permissions`
ON DUPLICATE KEY UPDATE granted_by = 3;
