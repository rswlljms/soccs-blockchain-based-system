# Filing of Candidacy Management Guide

## Overview

The Filing of Candidacy feature allows COMELEC and Advisers to create and manage filing periods for student elections. Students can see a floating button on their dashboard when a filing period is active, which opens a modal with information and a link to the application form (e.g., JotForm).

## Features

### Admin Features (COMELEC/Adviser)
1. **Create Filing Periods** - Set up new filing periods with title, announcement text, form link, and dates
2. **Edit Filing Periods** - Modify existing filing period information
3. **Activate/Deactivate** - Toggle filing period visibility to students
4. **Delete Filing Periods** - Remove filing periods that are no longer needed

### Student Features
1. **Floating Button** - Appears in the bottom-right corner when a filing period is active
2. **Information Modal** - Displays announcement text, dates, and available positions
3. **Direct Form Link** - Opens the application form (JotForm) in a new tab

## Database Setup

1. **Run the SQL script**:
   ```sql
   -- Located in: sql/create_filing_candidacy_table.sql
   ```

2. **Verify the table**:
   - Table name: `filing_candidacy_periods`
   - Check that all columns are created correctly

## How to Use

### Creating a Filing Period

1. **Login as COMELEC or Adviser**
   - Navigate to: `Election > Filing of Candidacy`

2. **Click "New Filing Period"**

3. **Fill in the form**:
   - **Title**: e.g., "Filing of Candidacy for AY 2025-2026"
   - **Announcement Text**: The full announcement that will be shown to students
   - **Form Link**: The JotForm URL (e.g., `https://form.jotform.com/252161495612051`)
   - **Start Date & Time**: When the filing period begins
   - **End Date & Time**: When the filing period ends
   - **Screening Date** (Optional): e.g., "September 1-2, 2025"
   - **Activate**: Check this box to make it visible to students

4. **Click "Save Filing Period"**

### Example Announcement Text

```
The Student Organization of the College of Computer Studies (SOCCS) – Commission on Elections (COMELEC) is pleased to announce that the Filing of Candidacy for SOCCS Officers for the Academic Year 2025–2026 IS NOW OFFICIALLY OPEN.

Aspiring student leaders are invited to submit their Application Form via the provided link or QR code.

The deadline for filing is August 29, 2025, with the screening scheduled on September 1–2, 2025. Please ensure to bring a hard copy of your completed form.

Available positions include:
* President
* Vice President
* Secretary
* Treasurer
* Auditor
* Public Information Officers (2)
* Event Coordinator (2)
* Business Manager (2)
* Year Representatives (1st to 4th Year)

Take the first step toward becoming a catalyst for change and leadership within the CCS community.

Apply now and be the next leader who will shape the future of our college.
```

### Activating/Deactivating a Filing Period

1. **Find the filing period** in the table
2. **Click the toggle button** (on/off icon)
3. **Confirm the action**
4. Only one filing period can be active at a time

### Editing a Filing Period

1. **Click the edit button** (pencil icon) next to the filing period
2. **Modify the information** as needed
3. **Click "Save Filing Period"**

### Deleting a Filing Period

1. **Click the delete button** (trash icon) next to the filing period
2. **Confirm the deletion**
3. Note: This action cannot be undone

## Student Experience

### When Filing Period is Active

1. **Floating Button Appears**:
   - Located in the bottom-right corner of the student dashboard
   - Shows "File Candidacy" text (hidden on mobile, icon only)

2. **Clicking the Button**:
   - Opens a modal with:
     - Title of the filing period
     - Full announcement text
     - Filing period dates
     - Screening date (if provided)
     - List of available positions
     - Link to the application form

3. **Accessing the Form**:
   - Click "Open Application Form" button
   - Opens in a new tab
   - Students can complete and submit the form

### When Filing Period is Inactive or Expired

- The floating button is hidden
- Students cannot access filing information

## Important Notes

1. **Only One Active Period**: Only one filing period can be active at a time. Activating a new period will automatically deactivate others.

2. **Date-Based Visibility**: The button only appears when:
   - The period is marked as active (`is_active = 1`)
   - Current date is between start and end dates

3. **Form Link Validation**: The system validates that the form link is a valid URL.

4. **Permissions**: 
   - COMELEC role: Can manage filing periods
   - Adviser role: Full access (can manage filing periods)

## API Endpoints

- `GET api/filing-candidacy/read.php` - Get all filing periods
- `GET api/filing-candidacy/get_active.php` - Get currently active filing period
- `POST api/filing-candidacy/create.php` - Create new filing period
- `POST api/filing-candidacy/update.php` - Update existing filing period
- `POST api/filing-candidacy/delete.php` - Delete filing period
- `POST api/filing-candidacy/toggle_status.php` - Activate/deactivate filing period

## File Structure

```
pages/
  └── filing-candidacy.php          # Admin management page

api/filing-candidacy/
  ├── create.php                    # Create endpoint
  ├── read.php                      # Read all endpoint
  ├── get_active.php                # Get active endpoint
  ├── update.php                    # Update endpoint
  ├── delete.php                    # Delete endpoint
  └── toggle_status.php             # Toggle status endpoint

assets/
  ├── css/
  │   └── student-dashboard.css     # Student dashboard styles (includes filing button & modal)
  └── js/
      ├── filing-candidacy.js       # Admin page JavaScript
      └── student-dashboard.js      # Student dashboard JavaScript (includes filing functionality)

sql/
  └── create_filing_candidacy_table.sql  # Database table creation script
```

## Troubleshooting

### Button Not Appearing
- Check that a filing period is marked as active
- Verify current date is between start and end dates
- Check browser console for JavaScript errors

### Modal Not Opening
- Check browser console for errors
- Verify API endpoint is accessible
- Check that filing period data is valid

### Form Link Not Working
- Verify the URL is correct and accessible
- Check that the link opens in a new tab (target="_blank")
- Ensure the form is publicly accessible

