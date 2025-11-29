# Admin Event Management Workflow

## 📋 Table of Contents
1. [Initial Setup](#initial-setup)
2. [Daily Operations](#daily-operations)
3. [Step-by-Step Guides](#step-by-step-guides)
4. [Best Practices](#best-practices)

## Initial Setup

### First Time Setup (Do Once)

1. **Import Database Schema**
   ```bash
   # Open phpMyAdmin
   # Navigate to: http://localhost/phpmyadmin
   # Select database: soccs_financial_management
   # Click Import tab
   # Choose file: sql/create_events_table.sql
   # Click Go
   ```

2. **Verify Installation**
   - Open: `http://localhost/soccs-financial-management/pages/events.php`
   - You should see sample events in the table
   - Verify statistics cards show correct counts

3. **Test Student View**
   - Open: `http://localhost/soccs-financial-management/pages/student-events.php`
   - Calendar should display with event indicators
   - Click on dates to see events

## Daily Operations

### Common Tasks

#### 1. Adding a New Event (3-5 minutes)

**When to do**: When you need to announce a new event to students

**Steps**:
1. Navigate to Event Management page
2. Click **"Add Event"** button (top right)
3. Fill in the form:
   - Event Name (required)
   - Date (required)
   - Time (required)
   - Location (required)
   - Description (required)
   - Category (required - choose one):
     - Academic - for seminars, lectures
     - Competition - for contests, tournaments
     - Social - for gatherings, parties
     - Workshop - for training sessions
   - Status (required):
     - **Upcoming** - for future events
     - **Completed** - for finished events
     - **Archived** - for old/cancelled events
4. Click **"Save"**
5. Success message will appear
6. Event immediately visible to students

#### 2. Editing an Event (2-3 minutes)

**When to do**: When event details change (time, location, etc.)

**Steps**:
1. Find the event in the table
2. Click **"Edit"** button
3. Modify the necessary fields
4. Click **"Save"**
5. Changes are immediately visible to students

#### 3. Archiving an Event (30 seconds)

**When to do**: After event is finished or cancelled

**Steps**:
1. Find the event in the table
2. Click **"Archive"** button
3. Confirmation message appears
4. Event moved to archived status
5. To view: Change filter dropdown to "Archived"

#### 4. Finding Events (Quick Search)

**Use the filters**:
- **Status Filter**: Show only upcoming/completed/archived
- **Date Filter**: Show events on specific date
- **Search Box**: Type event name or location

## Step-by-Step Guides

### Guide 1: Planning a Workshop

```
Scenario: You're organizing a "Web Development Workshop"
         scheduled for next Friday at 2:00 PM in Computer Lab
```

**Steps**:
1. Click **Add Event**
2. Fill in:
   - Name: "Web Development Workshop"
   - Date: [Select next Friday]
   - Time: 14:00 (2:00 PM)
   - Location: "Computer Laboratory"
   - Description: "Learn HTML, CSS, and JavaScript basics"
   - Category: "workshop"
   - Status: "upcoming"
3. Click **Save**
4. Done! Students can now see it on their calendar

**After the Event**:
1. Find the workshop in your event list
2. Click **Archive**
3. Event is now archived

### Guide 2: Monthly Planning

```
Scenario: It's the start of the month, you need to add
         all events for the next 30 days
```

**Best Practice**:
1. Prepare a list of all events with details
2. Add events chronologically (earliest first)
3. Use consistent naming:
   - "SOCCS Meeting" (not "Meeting", "Soccs meet", etc.)
4. Double-check dates and times
5. Add all events in one session (20-30 minutes)

**Sample Monthly Events**:
```
Week 1:
- SOCCS General Meeting (Monday, 2:00 PM, Auditorium)
- Programming Practice (Wednesday, 3:00 PM, Lab)

Week 2:
- Guest Speaker Series (Tuesday, 10:00 AM, Auditorium)
- Code Review Session (Thursday, 4:00 PM, Lab)

Week 3:
- Hackathon (Saturday, 8:00 AM - 8:00 PM, Lab)

Week 4:
- Social Night (Friday, 6:00 PM, Function Hall)
```

### Guide 3: Handling Event Changes

```
Scenario: Workshop moved from Lab to Auditorium
         Time changed from 2:00 PM to 3:30 PM
```

**Steps**:
1. Click **Edit** on the workshop
2. Update:
   - Location: "Main Auditorium"
   - Time: 15:30 (3:30 PM)
3. Click **Save**
4. Consider announcing the change through other channels
5. Students will see updated info on calendar

### Guide 4: Cancelling an Event

```
Scenario: Event must be cancelled (speaker unavailable)
```

**Option 1 - Archive It**:
1. Click **Archive** button
2. Event removed from upcoming list
3. Students won't see it anymore

**Option 2 - Change Status**:
1. Click **Edit** button
2. Change Status to "cancelled"
3. Click **Save**
4. Students can still see it but marked as cancelled

## Best Practices

### Naming Conventions

**Good Names** ✅:
- "Annual Tech Summit 2025"
- "Programming Competition - Round 1"
- "Web Development Workshop: HTML Basics"
- "SOCCS General Assembly"

**Bad Names** ❌:
- "meeting" (too generic)
- "event1" (not descriptive)
- "thing tomorrow" (unclear)
- "URGENT!!!!" (unprofessional)

### Timing Guidelines

**Add Events**:
- Minimum 1 week in advance
- Ideal: 2-3 weeks in advance
- Major events: 1-2 months in advance

**Update Times Carefully**:
- Use 24-hour format in system (14:00 for 2:00 PM)
- System automatically converts to 12-hour for display
- Double-check AM/PM

**Archive Promptly**:
- Archive within 1 week after event
- Keep system clean
- Easier to find active events

### Category Selection Guide

| Choose | When Event Is |
|--------|--------------|
| **Academic** | Lecture, seminar, assembly, general meeting, orientation |
| **Competition** | Contest, tournament, hackathon, quiz bowl, any competitive event |
| **Social** | Party, gathering, social night, celebration, informal meetup |
| **Workshop** | Training, hands-on session, tutorial, skill development |

### Description Writing

**Good Descriptions** ✅:
- "Learn web development fundamentals including HTML, CSS, and JavaScript"
- "Monthly general assembly to discuss upcoming activities and budget"
- "24-hour coding competition with prizes for top 3 teams"

**Bad Descriptions** ❌:
- "Event" (says nothing)
- "See you there!" (not informative)
- [Too long - 500+ words]

**Formula**:
- What: Brief description of activity
- Why: Purpose or benefit
- Who: Target audience (if specific)
- Limit: 1-2 sentences, max 150 characters

### Bulk Operations

**Adding Multiple Events**:
1. Keep a spreadsheet with event details
2. Copy data to clipboard
3. Add events one by one (takes ~2 minutes each)
4. Review calendar after batch add

**Weekly Review** (Recommended):
- Every Monday morning
- Review upcoming week's events
- Verify all details correct
- Add any missing events
- Archive past events

### Troubleshooting Tips

**Event Not Showing for Students**:
- Check event status (not archived)
- Verify date is correct
- Refresh student page (Ctrl+F5)

**Cannot Save Event**:
- Check all required fields filled
- Verify date format is correct
- Check database connection

**Wrong Event Time Display**:
- System uses 24-hour format internally
- Displays as 12-hour with AM/PM
- Example: 14:00 → 2:00 PM

## Workflow Checklist

### Weekly Checklist
- [ ] Review upcoming events
- [ ] Add any new events
- [ ] Update changed events
- [ ] Archive past events
- [ ] Verify student view displays correctly

### Before Each Event
- [ ] Verify event details in system
- [ ] Confirm location is correct
- [ ] Check time is accurate
- [ ] Ensure students can see it

### After Each Event
- [ ] Archive the event
- [ ] Update statistics if needed
- [ ] Gather feedback for future events

## Quick Reference

### Keyboard Shortcuts
- None currently - use mouse/click

### Time Format Conversion
| 24-Hour | 12-Hour |
|---------|---------|
| 09:00 | 9:00 AM |
| 12:00 | 12:00 PM |
| 14:00 | 2:00 PM |
| 18:00 | 6:00 PM |
| 20:00 | 8:00 PM |

### Status Meanings
- **Upcoming**: Event hasn't happened yet
- **Completed**: Event finished successfully
- **Archived**: Old event or cancelled

### Access URLs
- **Admin**: `/pages/events.php`
- **Student**: `/pages/student-events.php`
- **API**: `/api/events/`

## Getting Help

**Issue**: Something not working?
1. Check browser console (F12)
2. Review `docs/EVENT_MANAGEMENT_GUIDE.md`
3. Verify database connection
4. Contact system administrator

**Training**: New admin?
1. Read this workflow guide
2. Try adding a test event
3. View it on student portal
4. Archive the test event
5. Practice with real events

---

**Remember**: Events added here are immediately visible to ALL students on their calendar!

