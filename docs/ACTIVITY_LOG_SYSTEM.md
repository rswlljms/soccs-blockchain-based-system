# Activity Log System

## Overview
The Activity Log System tracks all user activities in the system. Only Adviser and Dean roles have access to view activity logs.

## Database Setup

Run the SQL migration file to create the activity logs table:

```sql
-- Run this file:
sql/activity_logs_table.sql
```

This creates the `activity_logs` table with the following structure:
- `id` - Primary key
- `user_id` - Foreign key to users table
- `activity_type` - Type of activity (e.g., 'create', 'update', 'delete', 'view', 'login')
- `activity_description` - Detailed description of the activity
- `module` - Module where activity occurred (optional)
- `created_at` - Timestamp of the activity

## Usage

### Logging Activities

The activity logger provides both a general function and module-specific helper functions for easier logging.

#### General Function

```php
require_once __DIR__ . '/includes/activity_logger.php';

logActivity($userId, $activityType, $activityDescription, $module);
```

#### Module-Specific Helper Functions

**Financial Module (Funds & Expenses)**
```php
// Funds
logFundActivity($userId, 'create', 'Created new fund: ' . $fundName);
logFundActivity($userId, 'update', 'Updated fund: ' . $fundName);
logFundActivity($userId, 'delete', 'Deleted fund: ' . $fundName);

// Expenses
logExpenseActivity($userId, 'create', 'Created expense: ₱' . $amount . ' - ' . $description);
logExpenseActivity($userId, 'update', 'Updated expense ID: ' . $expenseId);
logExpenseActivity($userId, 'delete', 'Deleted expense ID: ' . $expenseId);
```

**Membership Module**
```php
logMembershipActivity($userId, 'update_status', 'Updated membership status for student: ' . $studentId);
logMembershipActivity($userId, 'upload_receipt', 'Uploaded receipt for student: ' . $studentId);
```

**Student Management**
```php
logStudentActivity($userId, 'approve', 'Approved student registration: ' . $studentId);
logStudentActivity($userId, 'reject', 'Rejected student registration: ' . $studentId);
logStudentActivity($userId, 'archive', 'Archived student: ' . $studentId);
logStudentActivity($userId, 'restore', 'Restored student: ' . $studentId);
logStudentActivity($userId, 'update', 'Updated student record: ' . $studentId);
```

**Events Module**
```php
logEventActivity($userId, 'create', 'Created event: ' . $eventTitle);
logEventActivity($userId, 'update', 'Updated event: ' . $eventTitle);
logEventActivity($userId, 'delete', 'Deleted event: ' . $eventTitle);
logEventActivity($userId, 'archive', 'Archived event: ' . $eventTitle);
```

**Elections Module**
```php
logElectionActivity($userId, 'create', 'Created election: ' . $electionTitle);
logElectionActivity($userId, 'start', 'Started election: ' . $electionTitle);
logElectionActivity($userId, 'end', 'Ended election: ' . $electionTitle);
logElectionActivity($userId, 'update', 'Updated election: ' . $electionTitle);
logElectionActivity($userId, 'delete', 'Deleted election: ' . $electionTitle);
```

**Candidates**
```php
logCandidateActivity($userId, 'register', 'Registered candidate: ' . $candidateName);
logCandidateActivity($userId, 'update', 'Updated candidate: ' . $candidateName);
logCandidateActivity($userId, 'delete', 'Deleted candidate: ' . $candidateName);
logCandidateActivity($userId, 'approve', 'Approved candidate: ' . $candidateName);
```

**Positions**
```php
logPositionActivity($userId, 'create', 'Created position: ' . $positionName);
logPositionActivity($userId, 'update', 'Updated position: ' . $positionName);
logPositionActivity($userId, 'delete', 'Deleted position: ' . $positionName);
```

**User Management**
```php
logUserActivity($userId, 'create', 'Created user account: ' . $email);
logUserActivity($userId, 'update', 'Updated user: ' . $email);
logUserActivity($userId, 'deactivate', 'Deactivated user: ' . $email);
logUserActivity($userId, 'reactivate', 'Reactivated user: ' . $email);
logUserActivity($userId, 'permissions_updated', 'Updated permissions for user: ' . $email);
```

**Reports**
```php
logReportActivity($userId, 'generate_financial', 'Generated financial report');
logReportActivity($userId, 'generate_membership', 'Generated membership report');
logReportActivity($userId, 'generate_event', 'Generated event report');
logReportActivity($userId, 'generate_election', 'Generated election report');
logReportActivity($userId, 'export_pdf', 'Exported report to PDF');
```

**Authentication**
```php
logAuthActivity($userId, 'login');
logAuthActivity($userId, 'logout');
logAuthActivity($userId, 'password_change');
logAuthActivity($userId, 'password_reset');
```

**Settings**
```php
logSettingsActivity($userId, 'update', 'Updated system settings');
```

### Activity Types Reference

The system uses specific activity types based on modules:

**Financial Module:**
- `fund_created`, `fund_updated`, `fund_deleted`, `fund_viewed`
- `expense_created`, `expense_updated`, `expense_deleted`, `expense_viewed`

**Membership Module:**
- `membership_status_updated`, `membership_receipt_uploaded`, `membership_viewed`

**Students Module:**
- `student_approved`, `student_rejected`, `student_archived`, `student_restored`, `student_updated`, `student_viewed`

**Events Module:**
- `event_created`, `event_updated`, `event_deleted`, `event_archived`, `event_viewed`

**Elections Module:**
- `election_created`, `election_updated`, `election_deleted`, `election_started`, `election_ended`, `election_viewed`
- `candidate_registered`, `candidate_updated`, `candidate_deleted`, `candidate_approved`, `candidate_rejected`, `candidate_viewed`
- `position_created`, `position_updated`, `position_deleted`, `position_viewed`

**Users Module:**
- `user_created`, `user_updated`, `user_deactivated`, `user_reactivated`, `user_deleted`, `user_permissions_updated`, `user_viewed`

**Reports Module:**
- `financial_report_generated`, `membership_report_generated`, `event_report_generated`, `election_report_generated`, `report_exported_pdf`, `report_viewed`

**Authentication Module:**
- `user_login`, `user_logout`, `password_changed`, `password_reset`

**Settings Module:**
- `settings_updated`, `settings_viewed`

## Viewing Activity Logs

### Access
- Navigate to **Activity Logs** from the sidebar (visible only to Adviser and Dean)
- URL: `pages/activity-logs.php`

### Features
- **Search**: Search by activity description, user name, or email
- **Filter by Activity Type**: Filter by specific activity types
- **Filter by Module**: Filter by system module
- **Date Range**: Filter activities by date range
- **Pagination**: Navigate through pages of activity logs
- **User Information**: View user details including name, email, and role
- **Timestamps**: See exact date and time of each activity

## Files Created

1. **sql/activity_logs_table.sql** - Database migration file
2. **includes/activity_logger.php** - Utility function for logging activities
3. **api/activity-logs/read.php** - API endpoint to fetch activity logs
4. **pages/activity-logs.php** - Main page to view activity logs
5. **assets/css/activity-logs.css** - Styling for activity logs page
6. **assets/js/activity-logs.js** - JavaScript functionality for activity logs page

## Integration Examples

### Example 1: Logging in User Management

```php
// In api/users/create.php
require_once '../../includes/activity_logger.php';

// After successfully creating a user
logUserActivity(
    $_SESSION['user_id'],
    'create',
    'Created new user account: ' . $email . ' with role: ' . $role
);
```

### Example 2: Logging in Expense Management

```php
// In api/expenses/create.php
require_once '../../includes/activity_logger.php';

// After successfully creating an expense
logExpenseActivity(
    $_SESSION['user_id'],
    'create',
    'Created expense: ₱' . number_format($amount, 2) . ' - ' . $description
);
```

### Example 3: Logging in Student Approval

```php
// In api/approve_student.php
require_once '../../includes/activity_logger.php';

// After approving a student
logStudentActivity(
    $_SESSION['user_id'],
    'approve',
    'Approved student registration: ' . $studentId . ' (' . $studentName . ')'
);
```

### Example 4: Logging in Authentication

```php
// In auth.php after successful login
require_once __DIR__ . '/includes/activity_logger.php';

logAuthActivity($userId, 'login');
```

### Example 5: Logging in Election Management

```php
// In api/elections/update_status.php
require_once '../../includes/activity_logger.php';

// When starting an election
logElectionActivity(
    $_SESSION['user_id'],
    'start',
    'Started election: ' . $electionTitle . ' (ID: ' . $electionId . ')'
);

// When ending an election
logElectionActivity(
    $_SESSION['user_id'],
    'end',
    'Ended election: ' . $electionTitle . ' (ID: ' . $electionId . ')'
);
```

### Example 6: Logging in Fund Management

```php
// In api/save_fund.php
require_once '../../includes/activity_logger.php';

// After creating a fund
logFundActivity(
    $_SESSION['user_id'],
    'create',
    'Created fund: ' . $fundName . ' - Amount: ₱' . number_format($amount, 2)
);
```

### Example 7: Logging in Membership Fee Management

```php
// In api/toggle_membership_status.php
require_once '../../includes/activity_logger.php';

// After updating membership status
logMembershipActivity(
    $_SESSION['user_id'],
    'update_status',
    'Updated membership status to ' . $status . ' for student: ' . $studentId
);
```

## Security

- Only Adviser and Dean roles can view activity logs
- All activity logs are stored with IP address and user agent for security auditing
- Activity logs cannot be deleted or modified (read-only)
- Access is controlled at both page and API levels

