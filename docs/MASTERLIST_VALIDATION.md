# Masterlist Upload and Registration Validation

## Overview

The masterlist upload feature allows administrators to upload official student lists that are used to validate student registrations. When a student registers, their name and student ID must match an entry in the masterlist to be accepted.

## Database Schema

### Masterlist Table

```sql
CREATE TABLE `masterlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `student_id` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `course` varchar(10) DEFAULT NULL,
  `section` varchar(1) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uploaded_by` varchar(255) DEFAULT NULL,
  UNIQUE KEY `unique_student_id` (`student_id`),
  INDEX `idx_student_id` (`student_id`),
  INDEX `idx_name` (`name`),
  INDEX `idx_course_section` (`course`, `section`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

## Setup

1. **Create the masterlist table:**
   ```sql
   -- Run the SQL file
   source sql/create_masterlist_table.sql;
   ```

2. **Upload masterlist:**
   - Navigate to Student Management > Masterlist Upload
   - Upload a PDF or image file containing student names and IDs
   - The system will extract and save the data to the database

## Registration Validation Flow

When a student registers:

1. **Masterlist Check:**
   - System checks if the student ID exists in the masterlist
   - System verifies the name matches (fuzzy matching for variations)
   - If not found or doesn't match, registration is rejected

2. **Document Verification:**
   - If masterlist validation passes, document verification proceeds
   - COR (Certificate of Registration) is checked for student ID and name

3. **Approval:**
   - Only students who pass both masterlist and document verification are approved

## Validation Rules

### Student ID Matching
- Exact match required (normalized format: XXXX-XXXX)
- Case-insensitive matching

### Name Matching
- Fuzzy matching with 80% similarity threshold
- First name and last name must match
- Middle names are ignored for matching
- Handles common name variations

## API Endpoints

### Upload Masterlist
- **Endpoint:** `api/upload-masterlist.php`
- **Method:** POST
- **Parameters:** `masterlistFile` (file upload)
- **Response:** 
  ```json
  {
    "status": "success",
    "message": "Masterlist processed successfully. X student(s) saved to database.",
    "data": [...],
    "saved_count": X
  }
  ```

## Error Messages

- **"Student ID not found in masterlist"** - Student ID doesn't exist in uploaded masterlist
- **"Name does not match the masterlist"** - Name doesn't match the entry for that student ID
- **"Masterlist validation failed"** - System error during validation

## Usage

1. **Admin uploads masterlist:**
   - Go to Student Management > Masterlist Upload
   - Upload the official student list document
   - System extracts and saves all student data

2. **Student registers:**
   - Student fills out registration form
   - System automatically validates against masterlist
   - If validation fails, student sees error message
   - If validation passes, registration proceeds to document verification

## Notes

- Masterlist entries are unique by student_id
- Uploading a new masterlist will update existing entries
- Name matching is flexible to handle minor variations
- Student ID format is normalized (XXXX-XXXX) for consistent matching

