# Multiple Contests Feature Guide

## Overview

The event management system now supports **multiple contests per event** with individual registration links. When creating an event, administrators can add one or more contests, each with its own details and registration link (e.g., Google Forms) that students can use to register.

## Features

✅ **Multiple Contests** - Add unlimited contests to a single event  
✅ **Individual Contest Details** - Each contest has its own detailed information  
✅ **Separate Registration Links** - Each contest has its own registration link  
✅ **Dynamic Add/Remove** - Easily add or remove contests from the form  
✅ **Student View** - Students see all contests with their respective registration links  
✅ **Separate Descriptions** - Event description and contest details are displayed separately  

## Database Changes

### New Table: `event_contests`

```sql
CREATE TABLE IF NOT EXISTS `event_contests` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `event_id` int(11) NOT NULL,
  `contest_details` text NOT NULL,
  `registration_link` varchar(500) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE,
  INDEX `idx_event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

**Run these SQL files to update your database:**
```bash
sql/create_event_contests_table.sql
sql/remove_contest_fields_from_events.sql  (if you previously ran add_contest_fields_to_events.sql)
```

### Table Structure

| Column | Type | Description |
|--------|------|-------------|
| `id` | INT | Primary key |
| `event_id` | INT | Foreign key to events table |
| `contest_details` | TEXT | Detailed information about the contest |
| `registration_link` | VARCHAR(500) | URL for registration (e.g., Google Forms) |

## How to Use

### For Administrators

#### Creating an Event with Multiple Contests

1. **Open Event Form**
   - Navigate to: `pages/events.php`
   - Click "Add Event" button

2. **Fill Basic Event Information**
   - Event Name: e.g., "Programming Competition 2026"
   - Date and Time: Select event date and time
   - Location: e.g., "Computer Laboratory"
   - Description: General event description
   - Category: Select appropriate category
   - Status: Select status (usually "Upcoming")

3. **Add Contests**
   - Scroll to the "Contests" section
   - Click "Add Contest" button
   - Fill in contest details:
     - **Contest Details**: Enter detailed information about the contest
       - Example: "Open to all year levels. Prizes: 1st Place - ₱5,000, 2nd Place - ₱3,000, 3rd Place - ₱1,000. Registration deadline: December 5, 2025."
     - **Registration Link**: Enter the full URL
       - Example: "https://forms.gle/xxxxxxxxxxxxx" (Google Forms)
       - Example: "https://example.com/register" (Any registration form)
   - Click "Add Contest" again to add more contests
   - Use "Remove" button to delete a contest

4. **Save Event**
   - Click "Save" button
   - Event will be created with all contest information

#### Editing Events with Contests

1. Click the "Edit" button on an existing event
2. In the Contests section:
   - Existing contests will be displayed
   - Modify contest details or registration links as needed
   - Add more contests using "Add Contest" button
   - Remove contests using "Remove" button
3. Save changes

### For Students

#### Viewing Events with Contests

1. **Access Calendar**
   - Navigate to student events calendar
   - View events in the calendar interface

2. **View Event Details**
   - Click on a date with events
   - Event cards will display:
     - Event description (general information)
     - All contests with their details (if event has contests)
     - Individual registration buttons for each contest

3. **Register for Contests**
   - Each contest has its own "Register for Contest X" button
   - Click the button for the contest you want to join
   - Registration link opens in a new tab
   - Complete registration using the provided form
   - You can register for multiple contests from the same event

## Display Features

### Student Calendar View

When students view events in their calendar:

- **Event Description**: Always shown (if provided)
- **Contest Details**: Shown in a highlighted box with trophy icon (if `has_contest` is true)
- **Registration Button**: Prominent button with external link icon (if `registration_link` is provided)

### Visual Indicators

- Contest details are displayed in a golden/yellow highlighted box
- Registration button has a gradient orange background
- Trophy icon indicates contest-related information

## API Endpoints

### Create Event with Multiple Contests

**Endpoint:** `POST /api/events/create.php`

**Request Body:**
```json
{
  "name": "Programming Competition",
  "date": "2026-01-15",
  "time": "09:00",
  "location": "Computer Laboratory",
  "description": "Annual coding competition",
  "category": "competition",
  "status": "upcoming",
  "contests": [
    {
      "contest_details": "Open to all year levels. Prizes: 1st Place - ₱5,000.",
      "registration_link": "https://forms.gle/xxxxxxxxxxxxx"
    },
    {
      "contest_details": "Team competition. Maximum 3 members per team.",
      "registration_link": "https://forms.gle/yyyyyyyyyyyyy"
    }
  ]
}
```

### Update Event Contests

**Endpoint:** `POST /api/events/update.php`

**Request Body:**
```json
{
  "id": 1,
  "contests": [
    {
      "contest_details": "Updated contest details",
      "registration_link": "https://forms.gle/zzzzzzzzzzzzz"
    },
    {
      "contest_details": "New contest added",
      "registration_link": "https://forms.gle/wwwwwwwwwwwww"
    }
  ]
}
```

**Note:** Providing `contests` array will replace all existing contests. To remove all contests, send an empty array `[]`.

### Read Events (Includes Contests Array)

**Endpoint:** `GET /api/events/read.php`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Programming Competition",
      "description": "Annual coding competition",
      "contests": [
        {
          "id": 1,
          "contest_details": "Open to all year levels...",
          "registration_link": "https://forms.gle/xxxxxxxxxxxxx"
        },
        {
          "id": 2,
          "contest_details": "Team competition...",
          "registration_link": "https://forms.gle/yyyyyyyyyyyyy"
        }
      ],
      ...
    }
  ]
}
```

## Technical Details

### Form Validation

- Contest details and registration link are required only when `has_contest` is checked
- Registration link must be a valid URL format
- Contest details can be multi-line text

### Data Flow

1. Admin creates/edits event with contest information
2. Data is saved to database with `has_contest`, `contest_details`, and `registration_link`
3. Students view events through calendar interface
4. Contest information is displayed when `has_contest` is true
5. Registration link opens in new tab with `target="_blank"` and `rel="noopener noreferrer"`

## Best Practices

1. **Clear Contest Details**: Provide comprehensive information about:
   - Eligibility requirements
   - Prizes or rewards
   - Registration deadlines
   - Contest rules or guidelines

2. **Valid Registration Links**: Ensure registration links are:
   - Accessible and working
   - Mobile-friendly (if students use mobile devices)
   - Set to allow public access (if using Google Forms)

3. **Separate Information**: 
   - Use "Description" for general event information
   - Use "Contest Details" specifically for contest-related information

4. **Update Links**: Keep registration links updated if they change

## Troubleshooting

### Contest fields not showing
- Ensure JavaScript is enabled
- Check browser console for errors
- Verify `toggleContestFields()` function is defined

### Registration link not working
- Verify the URL is complete and correct
- Check if the link requires authentication
- Ensure the link is accessible from student devices

### Contest details not displaying
- Verify `has_contest` is set to `true` in database
- Check that `contest_details` field has content
- Ensure API is returning contest fields

## Related Files

- **SQL Migrations**: 
  - `sql/create_event_contests_table.sql`
  - `sql/remove_contest_fields_from_events.sql`
- **Admin Form**: `pages/events.php`
- **Admin JavaScript**: `assets/js/events.js`
- **Student Calendar**: `assets/js/student-events.js`
- **Student Styles**: `assets/css/student-events.css`
- **API Endpoints**: 
  - `api/events/create.php`
  - `api/events/update.php`
  - `api/events/read.php`

