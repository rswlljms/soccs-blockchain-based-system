# Remove Age Field - Database Update Guide

## Overview
The age field has been removed from the registration form. This guide shows what needs to be updated in the database.

## Database Changes Required

### Tables Affected:
1. **`students`** table - age column needs to be removed
2. **`student_registrations`** table - age column needs to be removed

## How to Apply Changes

### Option 1: Using phpMyAdmin (Easiest)
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Select database: `soccs_financial_management`
3. Go to **SQL** tab
4. Copy and paste the contents of `sql/remove_age_field.sql`
5. Click **Go** to execute

### Option 2: Using MySQL Command Line
```bash
# Login to MySQL
mysql -u root -p

# Select database
USE soccs_financial_management;

# Remove age from students table
ALTER TABLE students DROP COLUMN age;

# Remove age from student_registrations table
ALTER TABLE student_registrations DROP COLUMN age;

# Verify changes
DESCRIBE students;
DESCRIBE student_registrations;
```

### Option 3: Run the SQL File Directly
```bash
cd C:\xampp\htdocs\soccs-financial-management
mysql -u root -p soccs_financial_management < sql/remove_age_field.sql
```

## Files Already Updated

✅ **Frontend:**
- `pages/student-registration-step2.php` - Age field removed from form

✅ **Backend:**
- `auth/student-register-step2.php` - Age validation removed, defaults to 18

✅ **SQL Migration:**
- `sql/remove_age_field.sql` - Database migration script created

## Verification Steps

After running the migration, verify the changes:

1. **Check Database Structure:**
   ```sql
   DESCRIBE students;
   DESCRIBE student_registrations;
   ```
   The `age` column should no longer appear in either table.

2. **Test Registration:**
   - Go to registration page
   - Upload documents
   - Complete Step 2 (age field should not be visible)
   - Submit registration
   - Check if registration succeeds without age field

## Rollback (If Needed)

If you need to add the age field back:

```sql
-- Add age back to students table
ALTER TABLE students ADD COLUMN age int(3) DEFAULT NULL AFTER course;

-- Add age back to student_registrations table  
ALTER TABLE student_registrations ADD COLUMN age int(3) NOT NULL AFTER section;
```

## Current Database Schema (After Update)

### `students` table:
- `id` - Student ID (Primary Key)
- `first_name` - First name
- `middle_name` - Middle name
- `last_name` - Last name
- `email` - Email address
- `password` - Hashed password
- `year_level` - Year level (1-4)
- `section` - Section (A, B, C, etc.)
- `course` - Course (BSIT, BSCS)
- ~~`age`~~ - **REMOVED**
- `gender` - Gender (male, female, other)
- ... (other fields)

### `student_registrations` table:
- `id` - Student ID (Primary Key)
- `first_name` - First name
- `middle_name` - Middle name
- `last_name` - Last name
- `email` - Email address
- `course` - Course (BSIT, BSCS)
- `year_level` - Year level (1-4)
- `section` - Section
- ~~`age`~~ - **REMOVED**
- `gender` - Gender
- ... (other fields)

## Important Notes

⚠️ **Backup First**: It's recommended to backup your database before running any ALTER TABLE commands:
```bash
mysqldump -u root -p soccs_financial_management > backup_before_age_removal.sql
```

⚠️ **Existing Data**: The age field will be permanently removed. If you have existing student records with age data, that data will be lost.

⚠️ **Backend Code**: The backend now defaults age to 18 for database compatibility. This is only used internally and not visible to users.

## Summary

**What Changed:**
- ✅ Age field removed from registration form
- ✅ Age validation removed from backend
- ✅ Database migration script created
- 🔄 Database tables need to be updated (run SQL script)

**Action Required:**
1. Run `sql/remove_age_field.sql` in phpMyAdmin or MySQL
2. Verify tables no longer have age column
3. Test registration to ensure it works

---

**Created**: December 14, 2025  
**Migration File**: `sql/remove_age_field.sql`
