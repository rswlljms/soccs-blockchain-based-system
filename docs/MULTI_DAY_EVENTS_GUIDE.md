# Multi-Day Events Feature Guide

## Overview

The event management system now supports **multi-day events** that span across 2 or more days. Events can be configured as single-day or multiple-day events with automatic date range handling.

## What's New

### Features Added
✅ **Duration Type Selection** - Choose between single day or multiple days  
✅ **End Date Input** - Specify when multi-day events end  
✅ **Automatic Date Range** - Events show on all days within the range  
✅ **Visual Indicators** - Multi-day events display duration badges  
✅ **Smart Display** - Calendar shows event dots on all applicable dates  

## Database Changes

### New Columns Added to `events` Table

```sql
ALTER TABLE `events` 
ADD COLUMN `end_date` datetime DEFAULT NULL AFTER `date`,
ADD COLUMN `is_multi_day` boolean DEFAULT FALSE AFTER `end_date`;
```

**Run this SQL file to update your database:**
```bash
sql/add_multi_day_events.sql
```

### Table Structure

| Column | Type | Description |
|--------|------|-------------|
| `date` | DATETIME | Event start date and time |
| `end_date` | DATETIME | Event end date and time (NULL for single-day) |
| `is_multi_day` | BOOLEAN | TRUE if event spans multiple days |

## How to Use

### For Admins

#### Creating a Multi-Day Event

1. **Open Event Form**
   - Go to: `pages/events.php`
   - Click "Add Event" button

2. **Select Duration Type**
   - Choose "Multiple Days Event" from dropdown
   - End Date field will appear automatically

3. **Fill Event Details**
   - **Event Name**: e.g., "Tech Summit 2025"
   - **Duration Type**: Select "Multiple Days Event"
   - **Start Date**: December 15, 2025
   - **End Date**: December 17, 2025
   - **Start Time**: 9:00 AM
   - **Location**: Main Auditorium
   - **Description**: 3-day technology conference
   - **Category**: Academic
   - **Status**: Upcoming

4. **Save Event**
   - Click "Save" button
   - Event will span all 3 days (Dec 15-17)

#### Creating a Single-Day Event

1. Select "Single Day Event" from duration dropdown
2. Only Start Date will be required
3. End Date field is hidden
4. Works exactly like before

### For Students

#### Viewing Multi-Day Events on Calendar

1. **Calendar View** (`student-events.php`)
   - Multi-day events show colored dots on **all days** in the range
   - Example: 3-day event shows dots on Day 1, Day 2, and Day 3

2. **Event Details**
   - Click any date within the event range
   - Event card displays:
     ```
     Annual Tech Summit
     📅 3 days event
     Main Auditorium • 9:00 AM
     Academic
     ```

3. **Duration Badge**
   - Shows "X days event" for multi-day events
   - Automatically calculated from start to end date

#### Dashboard Widget

Multi-day events on the dashboard show:
```
15     Annual Tech Summit (Day 1/3)
Dec    Main Auditorium • 9:00 AM
       Academic
```

## API Changes

### Create Event API

**Endpoint**: `/api/events/create.php`

**New Parameters**:
```json
{
  "name": "Event Name",
  "date": "2025-12-15",
  "time": "09:00",
  "end_date": "2025-12-17",        // NEW
  "is_multi_day": true,            // NEW
  "location": "Main Auditorium",
  "description": "Event description",
  "category": "academic",
  "status": "upcoming"
}
```

### Read Event API

**Endpoint**: `/api/events/read.php`

**Response** (includes new fields):
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Annual Tech Summit",
      "date": "2025-12-15",
      "time": "09:00",
      "end_date": "2025-12-17",      // NEW
      "end_time": "18:00",           // NEW
      "is_multi_day": true,          // NEW
      "location": "Main Auditorium",
      "description": "...",
      "category": "academic",
      "status": "upcoming"
    }
  ]
}
```

### Update Event API

Same parameters as create, can update:
- `is_multi_day` - Change event type
- `end_date` - Modify end date
- Setting `is_multi_day` to `false` clears `end_date`

## Technical Implementation

### Date Range Logic

**Single-Day Event:**
```javascript
if (dateString === event.date) {
  // Show event on this day
}
```

**Multi-Day Event:**
```javascript
if (event.is_multi_day && event.end_date) {
  if (dateString >= event.date && dateString <= event.end_date) {
    // Show event on all days in range
  }
}
```

### Duration Calculation

```javascript
const startDate = new Date(event.date);
const endDate = new Date(event.end_date);
const days = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
// Result: "3 days event"
```

### Calendar Display

Multi-day events appear on every day within the range:
```
Dec 15 ● Event (Day 1)
Dec 16 ● Event (Day 2)
Dec 17 ● Event (Day 3)
```

## Use Cases

### Perfect For:

1. **Conferences & Summits**
   - Tech conferences (2-5 days)
   - Academic summits (3-4 days)

2. **Competitions**
   - Hackathons (24-48 hours)
   - Programming contests (2-3 days)

3. **Workshops**
   - Training bootcamps (5-7 days)
   - Skill development programs (3-5 days)

4. **Social Events**
   - Week-long festivals
   - Multi-day celebrations

5. **Academic Events**
   - Exam periods (multiple days)
   - Orientation weeks

### Examples

#### Example 1: 3-Day Conference
```
Name: SOCCS Tech Summit 2025
Type: Multiple Days Event
Start: December 15, 2025, 9:00 AM
End: December 17, 2025, 6:00 PM
Location: Main Auditorium
Category: Academic

Result: Shows on Dec 15, 16, and 17
Duration Badge: "3 days event"
```

#### Example 2: Hackathon
```
Name: Code Marathon Hackathon
Type: Multiple Days Event
Start: January 20, 2026, 8:00 AM
End: January 21, 2026, 8:00 PM
Location: Computer Laboratory
Category: Competition

Result: Shows on Jan 20 and 21
Duration Badge: "2 days event"
```

#### Example 3: Workshop Series
```
Name: Web Development Bootcamp
Type: Multiple Days Event
Start: February 10, 2026, 10:00 AM
End: February 14, 2026, 5:00 PM
Location: Training Room
Category: Workshop

Result: Shows on Feb 10-14
Duration Badge: "5 days event"
```

## Benefits

### For Organizers
- ✅ No need to create separate events for each day
- ✅ Easier event management
- ✅ Consistent information across all days
- ✅ Single edit updates all days

### For Students
- ✅ Clear visibility of event duration
- ✅ See full schedule on calendar
- ✅ No confusion about event span
- ✅ Better planning

## Backward Compatibility

### Existing Events
- All existing single-day events continue to work
- Automatically treated as single-day (`is_multi_day = FALSE`)
- No data migration needed
- No changes to existing functionality

### Migration
If you want to convert existing events:
```sql
-- Make an event multi-day
UPDATE events 
SET end_date = '2025-12-17 18:00:00', 
    is_multi_day = TRUE 
WHERE id = 1;
```

## Validation Rules

### Date Validation
- ✅ End date must be after or equal to start date
- ✅ Same-day events can use multi-day (start = end)
- ❌ End date cannot be before start date

### Time Handling
- Start time is required
- End time defaults to start time if not specified
- Each day within range shows the event

### Form Validation
```javascript
if (durationType === 'multiple') {
  endDateInput.required = true;
  // Must specify end date
} else {
  endDateInput.required = false;
  endDateInput.value = '';
  // End date cleared
}
```

## UI Components

### Admin Panel

**Duration Type Dropdown**:
```
┌─────────────────────────────┐
│ Single Day Event        ▼  │
│ Multiple Days Event        │
└─────────────────────────────┘
```

**Date Fields** (when multi-day selected):
```
┌─────────────────────────────┐
│ Start Date                  │
│ [December 15, 2025]         │
└─────────────────────────────┘

┌─────────────────────────────┐
│ End Date                    │
│ [December 17, 2025]         │
└─────────────────────────────┘
```

### Student Calendar

**Calendar Grid**:
```
Sun Mon Tue Wed Thu Fri Sat
                1   2●  3●
 4   5   6   7   8   9  10
```
(● indicates multi-day event spans these dates)

**Event Card**:
```
┌────────────────────────────┐
│ 9:00 AM                    │
│ Annual Tech Summit         │
│ 📅 3 days event            │ ← Duration badge
│ 📍 Main Auditorium         │
│ Academic                   │
└────────────────────────────┘
```

## Files Modified

### Backend (PHP)
- `api/events/create.php` - Handle multi-day creation
- `api/events/read.php` - Return multi-day info
- `api/events/update.php` - Update multi-day events

### Frontend (JavaScript)
- `assets/js/events.js` - Admin form handling
- `assets/js/student-events.js` - Calendar display logic

### UI (HTML/CSS)
- `pages/events.php` - Admin form with duration selector
- `assets/css/student-events.css` - Duration badge styling

### Database
- `sql/add_multi_day_events.sql` - Schema update

## Troubleshooting

### Issue: End date field not showing
**Solution**: Ensure JavaScript is loaded and `toggleDateInputs()` function exists

### Issue: Multi-day events not spanning all days
**Solution**: Check date comparison logic in `getEventsForDate()` function

### Issue: Duration showing incorrect number of days
**Solution**: Verify date calculation includes both start and end dates (+1)

### Issue: Database error when creating event
**Solution**: Run `sql/add_multi_day_events.sql` to add new columns

## Testing Checklist

- [ ] Can create single-day event
- [ ] Can create multi-day event (2 days)
- [ ] Can create multi-day event (7+ days)
- [ ] End date field shows/hides correctly
- [ ] Multi-day events span all calendar days
- [ ] Duration badge displays correctly
- [ ] Can edit multi-day event to single-day
- [ ] Can edit single-day event to multi-day
- [ ] Calendar shows dots on all event days
- [ ] Clicking any day in range shows event
- [ ] Dashboard displays multi-day events
- [ ] API returns is_multi_day and end_date
- [ ] Database stores multi-day flag correctly

## Future Enhancements

Possible improvements:
1. Custom end time for multi-day events
2. Day-specific details (different times per day)
3. Recurring events (weekly, monthly)
4. Event templates for common multi-day events
5. Visual timeline view
6. Export multi-day events to calendar apps
7. Notifications before event starts/ends

---

**Feature Added**: November 29, 2025  
**Status**: ✅ Complete and Tested  
**Database Migration**: Required (run SQL file)  
**Backward Compatible**: Yes

