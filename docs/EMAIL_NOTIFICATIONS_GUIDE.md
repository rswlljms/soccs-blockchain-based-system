# Email Notifications Guide

## Overview

The SOCCS Financial Management System now automatically sends email notifications to all registered students when important events occur. This ensures students are always informed about new events, filing of candidacy periods, and active elections.

## Features

### 1. Event Notifications
- **Trigger**: When a new event is created via the admin panel
- **Recipients**: All active registered students
- **Email Subject**: "New SOCCS Event: [Event Title]"
- **Content**: Event details including title, date, time, location, category, and description

### 2. Filing of Candidacy Notifications
- **Trigger**: When a filing of candidacy period is created or activated
- **Recipients**: All active registered students
- **Email Subject**: "Filing of Candidacy Now Open: [Period Title]"
- **Content**: Filing period details, dates, announcement text, and direct link to application form
- **Note**: Notification is sent when:
  - A new filing period is created with `is_active = 1`
  - An existing filing period is updated and activated (changed from inactive to active)

### 3. Election Notifications
- **Trigger**: When an election status is changed to 'active'
- **Recipients**: All active registered students
- **Email Subject**: "Election Now Active: [Election Title]"
- **Content**: Election details, start/end dates, description, and link to voting page

## Technical Implementation

### Email Service Methods

The `EmailService` class in `includes/email_config.php` includes the following new methods:

#### `getActiveStudentEmails()`
Retrieves all active student email addresses from the database.

```php
$students = $emailService->getActiveStudentEmails();
// Returns: [['email' => 'student@example.com', 'first_name' => 'John'], ...]
```

#### `sendBulkEmail($emails, $subject, $htmlMessage, $plainMessage = '')`
Sends emails to multiple recipients with rate limiting.

```php
$result = $emailService->sendBulkEmail($students, $subject, $htmlContent);
// Returns: ['success' => 10, 'failed' => 0, 'total' => 10]
```

#### `sendEventNotification($eventTitle, $eventDate, $eventLocation, $eventDescription, $eventCategory)`
Sends event notification to all active students.

#### `sendFilingCandidacyNotification($title, $announcementText, $formLink, $startDate, $endDate, $screeningDate = null)`
Sends filing of candidacy notification to all active students.

#### `sendElectionNotification($electionTitle, $electionDescription, $startDate, $endDate)`
Sends election notification to all active students.

### API Integration Points

#### Events
- **File**: `api/events/create.php`
- **Trigger**: After successful event creation
- **Action**: Automatically sends notification to all active students

#### Filing of Candidacy
- **Files**: 
  - `api/filing-candidacy/create.php` - When creating with `is_active = 1`
  - `api/filing-candidacy/update.php` - When updating and activating (inactive → active)
- **Action**: Sends notification only when period becomes active

#### Elections
- **File**: `api/elections/update_status.php`
- **Trigger**: When election status is changed to 'active'
- **Action**: Automatically sends notification to all active students

## Email Templates

All email notifications follow a consistent design pattern:

- **Header**: SOCCS logo and organization name
- **Banner**: SOCCS banner image
- **Content**: 
  - Personalized greeting
  - Event-specific information box with color coding:
    - Events: Blue theme (#2196F3)
    - Filing of Candidacy: Yellow/Amber theme (#ffc107)
    - Elections: Green theme (#28a745)
  - Call-to-action button (where applicable)
  - Footer with contact information
- **Footer**: Organization name and copyright

## Email Delivery

### Rate Limiting
- Emails are sent with a 100ms delay between each recipient to prevent overwhelming the SMTP server
- This ensures reliable delivery and prevents being flagged as spam

### Error Handling
- Email sending failures are logged but do not prevent the main operation from completing
- Errors are logged to PHP error log for monitoring
- Returns success/failure counts for tracking

### Logging
All email notification attempts are logged:
```
Event notification emails sent: {"success":10,"failed":0,"total":10}
Filing candidacy notification emails sent: {"success":10,"failed":0,"total":10}
Election notification emails sent: {"success":10,"failed":0,"total":10}
```

## Configuration

### SMTP Settings
Email notifications use the same SMTP configuration as other system emails:
- Configured in `includes/app_config.php`
- Uses PHPMailer for reliable email delivery
- Supports Gmail and other SMTP providers

### Student Selection Criteria
Only students meeting these criteria receive notifications:
- `is_active = 1` in the `students` table
- Has a valid email address (`email IS NOT NULL AND email != ''`)

## Testing

### Test Event Notification
1. Log in as admin
2. Navigate to Events page
3. Create a new event
4. Check email inboxes of registered students
5. Verify email contains correct event details

### Test Filing of Candidacy Notification
1. Log in as COMELEC or Adviser
2. Navigate to Filing of Candidacy page
3. Create a new filing period with "Activate" checked
4. OR update an existing period and activate it
5. Check email inboxes of registered students
6. Verify email contains form link and dates

### Test Election Notification
1. Log in as admin with election management permissions
2. Navigate to Elections page
3. Create an election (status: upcoming)
4. Click "Start" to activate the election
5. Check email inboxes of registered students
6. Verify email contains voting link and election details

## Troubleshooting

### Emails Not Sending
1. Check SMTP configuration in `includes/app_config.php`
2. Verify Gmail App Password is set correctly
3. Check PHP error logs for email sending errors
4. Ensure students have valid email addresses in database

### Partial Email Delivery
- Check SMTP server rate limits
- Verify email addresses are valid
- Check spam folders
- Review error logs for specific failures

### Email Format Issues
- Verify email templates in `includes/email_config.php`
- Check that all required data is passed to notification methods
- Test with a single recipient first

## Best Practices

1. **Monitor Email Logs**: Regularly check error logs for email delivery issues
2. **Test Before Production**: Always test email notifications in development first
3. **Keep Email Lists Updated**: Ensure student email addresses are current
4. **Respect Rate Limits**: The system includes rate limiting, but be aware of SMTP provider limits
5. **Verify SMTP Credentials**: Keep SMTP credentials secure and up-to-date

## Future Enhancements

Potential improvements for email notifications:
- Email preferences (allow students to opt-out of certain notifications)
- Scheduled email reminders
- Email templates customization
- Delivery status tracking
- Bounce handling and email validation

## Support

For issues or questions regarding email notifications:
- Check PHP error logs: `error_log()` entries
- Review SMTP configuration
- Contact system administrator
- Email: lspuscc.soccs@gmail.com

