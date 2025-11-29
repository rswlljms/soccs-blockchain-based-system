# Event Management System Guide

## Overview

The Event Management System allows SOCCS administrators to create, update, and manage events that are automatically visible to all students through the student portal.

## Features

- **Create Events**: Add new events with details like name, date, time, location, category, and description
- **Update Events**: Modify existing event information
- **Archive Events**: Archive past or cancelled events
- **Real-time Sync**: Events added by admin are immediately visible to students
- **Student Calendar View**: Students see events in an interactive calendar interface
- **Category-based Organization**: Events categorized as academic, competition, social, or workshop

## Database Setup

### 1. Create Events Table

Run the SQL script to create the events table:

```sql
-- Run this file: sql/create_events_table.sql
```

Or manually execute in your database:

```sql
CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(255) NOT NULL,
  `description` text,
  `date` datetime NOT NULL,
  `location` varchar(255),
  `category` varchar(50) DEFAULT 'general',
  `status` enum('upcoming','ongoing','completed','cancelled','archived') DEFAULT 'upcoming',
  `created_by` varchar(255),
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_date` (`date`),
  INDEX `idx_status` (`status`),
  INDEX `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

## Admin Usage

### Accessing Event Management

1. Navigate to `pages/events.php` in the admin panel
2. View all events in the table with filtering options

### Adding a New Event

1. Click the **Add Event** button
2. Fill in the event details:
   - **Event Name**: Title of the event
   - **Date**: Event date
   - **Time**: Event start time
   - **Location**: Venue or location
   - **Description**: Brief description
   - **Category**: Select from academic, competition, social, or workshop
   - **Status**: Set as upcoming, completed, or archived
3. Click **Save** to create the event

### Updating an Event

1. Click the **Edit** button next to the event
2. Modify the desired fields
3. Click **Save** to update

### Archiving an Event

1. Click the **Archive** button next to the event
2. The event status will be changed to "archived"

### Filtering Events

- **By Status**: Filter by upcoming, completed, or archived
- **By Date**: Filter events on a specific date
- **By Search**: Search by event name, description, or location

## Student View

### Accessing Events

Students can view events in two places:

1. **Student Dashboard** (`pages/student-dashboard.php`): Shows upcoming events
2. **Events Calendar** (`pages/student-events.php`): Full calendar view

### Calendar Features

- **Monthly View**: Browse events by month
- **Event Indicators**: Colored dots show events on calendar days
- **Event Details**: Click a date to view all events scheduled
- **Category Colors**:
  - **Blue**: Academic events
  - **Orange**: Competition events
  - **Green**: Social events
  - **Purple**: Workshop events

## API Endpoints

### Create Event
- **URL**: `/api/events/create.php`
- **Method**: POST
- **Body**:
```json
{
  "name": "Event Name",
  "date": "2024-12-15",
  "time": "14:00",
  "location": "Main Auditorium",
  "description": "Event description",
  "category": "academic",
  "status": "upcoming"
}
```

### Read Events
- **URL**: `/api/events/read.php`
- **Method**: GET
- **Parameters**:
  - `status` (optional): Filter by status (all, upcoming, completed, archived)
  - `date` (optional): Filter by specific date
  - `search` (optional): Search term

### Update Event
- **URL**: `/api/events/update.php`
- **Method**: POST
- **Body**:
```json
{
  "id": 1,
  "name": "Updated Event Name",
  "date": "2024-12-16",
  "time": "15:00",
  "location": "Updated Location",
  "description": "Updated description",
  "category": "workshop",
  "status": "upcoming"
}
```

### Delete Event
- **URL**: `/api/events/delete.php`
- **Method**: POST
- **Body**:
```json
{
  "id": 1
}
```

## File Structure

```
soccs-financial-management/
├── api/
│   └── events/
│       ├── create.php        # Create new event
│       ├── read.php           # Fetch events
│       ├── update.php         # Update event
│       └── delete.php         # Delete event
├── assets/
│   └── js/
│       ├── events.js          # Admin event management
│       └── student-events.js  # Student calendar view
├── pages/
│   ├── events.php             # Admin event management page
│   └── student-events.php     # Student calendar page
├── sql/
│   └── create_events_table.sql # Database schema
└── docs/
    └── EVENT_MANAGEMENT_GUIDE.md # This file
```

## Event Status Flow

```
upcoming → ongoing → completed → archived
         ↓
    cancelled → archived
```

## Best Practices

1. **Set Accurate Dates**: Ensure event dates and times are correct
2. **Use Descriptive Names**: Make event names clear and informative
3. **Choose Appropriate Categories**: Select the most relevant category
4. **Update Status**: Change status to "completed" after events finish
5. **Archive Old Events**: Keep the system clean by archiving past events

## Troubleshooting

### Events Not Showing on Student Side

1. Check database connection in `includes/database.php`
2. Verify events table exists and has data
3. Check browser console for JavaScript errors
4. Ensure event status is not "archived" or "cancelled"

### Cannot Create Events

1. Verify database permissions
2. Check PHP error logs
3. Ensure all required fields are filled
4. Verify API endpoints are accessible

### Calendar Not Loading

1. Check browser console for errors
2. Verify `student-events.js` is loaded correctly
3. Ensure API endpoint `/api/events/read.php` is accessible
4. Check database connection

## Security Considerations

- All database queries use prepared statements (PDO)
- Input validation on both client and server side
- SQL injection protection through parameter binding
- Admin-only access to event creation/modification
- Students have read-only access to events

## Future Enhancements

- Event registration system for students
- Email notifications for upcoming events
- Event reminders
- Recurring events support
- Event attachments (flyers, documents)
- RSVP tracking
- Event capacity management
- Calendar export (iCal format)

