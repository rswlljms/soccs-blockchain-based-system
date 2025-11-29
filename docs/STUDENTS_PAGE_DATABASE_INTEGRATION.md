# Students Page Database Integration

## Overview
This document outlines the database integration for the Students Management page, connecting it with the Student Approvals system and adding section-based reporting functionality.

## Changes Implemented

### 1. API Endpoints

#### `api/toggle_membership_status.php` (NEW)
- Toggles membership fee payment status for students
- Updates `membership_fee_status` and `membership_fee_paid_at` fields
- Supports setting status to either 'paid' or 'unpaid'

#### `api/get_students.php` (EXISTING - VERIFIED)
- Fetches students from the `students` table
- Supports filtering by:
  - Search term (name)
  - Course (BSIT/BSCS)
  - Year level (1-4)
  - Section (A, B, C, etc.)
  - Payment status (paid/unpaid)
  - Archive status (active/archived)
- Returns summary statistics (total students, paid count, total collected)

#### `api/archive_student.php` (EXISTING - VERIFIED)
- Archives or restores students
- Updates `is_archived` and `archived_at` fields

#### `api/get_section_summary.php` (NEW)
- Gets summary statistics for a specific section
- Returns:
  - Total students per section
  - Paid students count
  - Unpaid students count
  - Detailed list of students (separated by paid/unpaid)
- Only returns data when section filter is applied

### 2. Frontend Updates

#### `assets/js/students.js` (COMPLETELY REWRITTEN)
- Removed all mock data
- Integrated with real API endpoints
- Key functions:
  - `loadStudents()`: Fetches students from database via API
  - `loadSectionSummary()`: Loads section summary when section filter is applied
  - `togglePaymentStatus()`: Updates membership fee status with confirmation
  - `archiveStudent()` / `restoreStudent()`: Archive/restore operations with API calls
  - `printSectionReport()`: Opens printable report in new window
  - `applyFilters()`: Triggers data reload with current filter settings

#### `pages/students.php` (UPDATED)
- Removed receipt upload modal (no longer needed)
- Updated modal styles for confirmation dialogs
- Cleaned up unused form elements
- Kept section summary container (only shows when section filter is applied)

### 3. Printable Report

#### `pages/print-section-report.php` (NEW)
- A4-sized printable report
- Shows section-based membership fee status
- Features:
  - Report header with organization info
  - Summary statistics (total, paid, unpaid)
  - Two separate tables:
    1. Paid students list
    2. Unpaid students list
  - Professional styling optimized for printing
  - Print button (hidden when printing)
  - Filters by course, year, and section

## Database Flow

### Student Approval → Students Table
1. Student submits registration via `auth/student-register.php`
2. Record created in `student_registrations` table with `approval_status = 'pending'`
3. Admin reviews at `pages/student-approvals.php`
4. When approved via `api/approve_student.php`:
   - `student_registrations` status updated to 'approved'
   - Student data copied to `students` table
   - `membership_fee_status` defaults to 'unpaid'
   - Email notification sent to student
5. Student appears in `pages/students.php`

## Features

### Membership Fee Management
- Click on payment status badge to toggle between paid/unpaid
- Confirmation modal before status change
- Automatic timestamp recording (`membership_fee_paid_at`)
- Real-time updates without page refresh

### Section Summary
- **Only visible when section filter is applied**
- Shows:
  - Total students in section
  - Number of paid students
  - Number of unpaid students
  - Print report button
- Automatically updates when filters change

### Printable Section Report
- Professional A4 layout
- Separate sections for paid and unpaid students
- Complete student details (ID, name, course, year, section)
- Print-optimized styling
- Can be saved as PDF from browser

### Archive Management
- Archive/restore students with confirmation
- Archived students don't appear in default view
- Toggle "View Archived" button to see archived students
- Maintains archive history with timestamps

### Search and Filtering
- Real-time search by student name
- Filter by:
  - Course (All, BSIT, BSCS)
  - Year level (All, 1-4)
  - Section (single letter)
  - Payment status (All, paid, unpaid)
  - Archive status (active/archived toggle)

## Database Schema Usage

### `students` Table
```sql
- id (VARCHAR) - Student ID from registration
- first_name, middle_name, last_name
- email (UNIQUE)
- password (hashed)
- course (BSIT/BSCS)
- year_level (1-4)
- section (A, B, C, etc.)
- age, gender
- membership_fee_status (paid/unpaid) - DEFAULT 'unpaid'
- membership_fee_receipt (VARCHAR) - Optional receipt file path
- membership_fee_paid_at (TIMESTAMP) - Payment timestamp
- is_archived (BOOLEAN) - Archive status
- archived_at, archived_by - Archive tracking
- restored_at, restored_by - Restore tracking
- is_active (BOOLEAN) - Account status
- created_at, updated_at - Record timestamps
```

### `student_registrations` Table
```sql
- id (VARCHAR) - Student ID
- first_name, middle_name, last_name
- email (UNIQUE)
- password (hashed)
- course, year_level, section, age, gender
- student_id_image, cor_file - Document uploads
- approval_status (pending/approved/rejected)
- created_at, approved_at, rejected_at
- approved_by, rejection_reason
```

## File Structure
```
api/
├── get_students.php (existing, verified)
├── toggle_membership_status.php (new)
├── get_section_summary.php (new)
└── archive_student.php (existing, verified)

pages/
├── students.php (updated)
├── student-approvals.php (existing)
├── print-section-report.php (HTML version - fallback)
└── print-section-report-pdf.php (new - uses custom PDF template)

assets/
├── js/
│   └── students.js (completely rewritten)
└── img/
    └── soccs_reporting_format.pdf (custom template)

vendor/
└── setasign/fpdi/ (PDF library)

composer.json (new)
.gitignore (new)
```

## PDF Template Feature

### Custom Template Background
The system now uses your custom PDF template (`soccs_reporting_format.pdf`) as a background for section reports.

**Libraries Used:** 
- FPDF (v1.8.2) - Base PDF library
- FPDI (v2.6.4) - PDF template overlay
- Installed via Composer

**How it works:**
1. Loads the PDF template as background
2. Overlays dynamic content (student data) on top
3. Maintains exact positioning for professional reports
4. Supports multi-page reports with template on each page

**Key Features:**
- ✅ Professional branded reports using your PDF template
- ✅ Dynamic student data overlay
- ✅ Automatic pagination when content exceeds one page
- ✅ Inline PDF viewing (opens in browser)
- ✅ Ready to print or save as PDF

**Customization:**
See `docs/PDF_TEMPLATE_SETUP.md` for detailed positioning and customization guide.

## Testing Checklist
- [x] Students appear after approval from student-approvals page
- [x] Mock data removed
- [x] Payment status toggles correctly
- [x] Section summary only shows when section filter applied
- [x] Printable report generates correctly with PDF template
- [x] Archive/restore functionality works
- [x] All filters work correctly
- [x] No linting errors
- [x] FPDI library installed via Composer
- [x] PDF template properly loaded and rendered

## Notes
- Membership fee is set to ₱250 (defined as constant in students.js)
- Section summary requires section filter to be non-empty
- Printable report opens in new window for easy printing
- All API calls include error handling and user feedback
- Modals use confirmation for destructive actions (archive, toggle status)

