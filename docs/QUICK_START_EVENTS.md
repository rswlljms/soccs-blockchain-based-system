# Quick Start: Event Management

## 🚀 Setup in 3 Steps

### Step 1: Database Setup
```bash
# Open phpMyAdmin or MySQL command line
# Select your database: soccs_financial_management
# Import or run: sql/create_events_table.sql
```

Or run this SQL directly:
```sql
source sql/create_events_table.sql;
```

### Step 2: Verify Installation
1. Check that the `events` table exists in your database
2. Verify sample events were inserted
3. Ensure your Apache and MySQL servers are running (XAMPP)

### Step 3: Start Using

#### For Admins:
1. Navigate to: `http://localhost/soccs-financial-management/pages/events.php`
2. Click **Add Event** button
3. Fill in event details and save
4. Event will automatically appear on student portal

#### For Students:
1. Navigate to: `http://localhost/soccs-financial-management/pages/student-events.php`
2. View events in calendar format
3. Click on any date to see events scheduled

## 📝 Adding Your First Event

1. Go to admin event management page
2. Click **Add Event**
3. Fill in:
   - Name: "Welcome Orientation"
   - Date: Select tomorrow's date
   - Time: "09:00"
   - Location: "Main Auditorium"
   - Description: "Welcome new students"
   - Category: "academic"
   - Status: "upcoming"
4. Click **Save**
5. Check student portal - event will appear on calendar

## ✅ Verification Checklist

- [ ] Events table created in database
- [ ] Sample events visible in admin panel
- [ ] Can create new event from admin panel
- [ ] New event appears in admin table
- [ ] Event visible on student calendar
- [ ] Event details shown when clicking date
- [ ] Can edit existing events
- [ ] Can archive events
- [ ] Filters work correctly

## 🐛 Common Issues

**Issue**: Events not showing
- **Fix**: Run `sql/create_events_table.sql` to create the table

**Issue**: Cannot create events
- **Fix**: Check database credentials in `includes/database.php`

**Issue**: Calendar blank
- **Fix**: Open browser console (F12) and check for errors

**Issue**: API errors
- **Fix**: Verify API files exist in `api/events/` folder

## 📱 Features Overview

### Admin Features
- Create, edit, archive events
- Filter by status, date, search
- View event statistics
- Manage event categories

### Student Features
- Interactive calendar view
- Color-coded event categories
- Click dates to view details
- Navigate between months
- Mobile-responsive design

## 🎯 Next Steps

1. Customize event categories if needed
2. Add more events for your organization
3. Train admins on event management
4. Inform students about the events calendar
5. Consider adding event reminders (future enhancement)

## 📚 More Information

See full documentation: `docs/EVENT_MANAGEMENT_GUIDE.md`

