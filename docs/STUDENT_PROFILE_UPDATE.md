# Student Profile Update - Setup Guide

## ✅ What Was Fixed

The student profile page was using **simulated saves** (fake data that didn't persist). Now it properly saves to the database!

### Changes Made:
1. ✅ Created API endpoint for profile updates
2. ✅ Created API endpoint for profile image upload
3. ✅ Updated JavaScript to make real API calls
4. ✅ Added database column for profile images

---

## 📋 Database Setup Required

You need to add a new column to the `students` table to store profile images.

### Step 1: Run SQL Migration

Open **phpMyAdmin** and run this SQL:

```sql
-- Add profile_image column to students table
ALTER TABLE `students` 
ADD COLUMN `profile_image` VARCHAR(255) DEFAULT NULL 
COMMENT 'Path to student profile image' 
AFTER `gender`;

-- Create index for faster lookups
CREATE INDEX idx_students_profile_image ON students(profile_image);
```

**OR** import the migration file:
- Go to phpMyAdmin → `soccs_financial_management` database
- Click **Import** tab
- Select: `sql/add_student_profile_image.sql`
- Click **Go**

---

## 🎯 Features Now Working

### 1. Profile Information Update
Students can now update:
- ✅ First Name
- ✅ Middle Name
- ✅ Last Name
- ✅ Date of Birth
- ✅ Phone Number
- ✅ Email Address
- ✅ Address

**Changes persist in the database!**

### 2. Profile Image Upload
Students can upload profile pictures:
- ✅ Supported formats: JPG, PNG, GIF
- ✅ Maximum file size: 5MB
- ✅ Auto-deletes old image when new one is uploaded
- ✅ Images stored in: `uploads/student-profiles/`

---

## 🧪 Testing

### Test Profile Update:
1. Login as a student
2. Go to Profile Settings
3. Change any field (e.g., phone number)
4. Click **"SAVE CHANGES"**
5. Should see "Saved!" message
6. Refresh page - changes should persist

### Test Image Upload:
1. Click the camera icon on profile picture
2. Select an image file
3. Image should preview immediately
4. Check database - `profile_image` column should have path
5. Image file should exist in `uploads/student-profiles/`

---

## 🔒 Security Features

1. **Authentication Check**
   - Only logged-in students can update profiles
   - Students can only update their own profile

2. **Email Validation**
   - Checks if email is already in use
   - Validates email format

3. **File Upload Security**
   - Validates file type (images only)
   - Checks file size (max 5MB)
   - Generates unique filenames
   - Stores outside web root where possible

4. **SQL Injection Prevention**
   - Uses prepared statements
   - Parameterized queries

---

## 📁 Files Created/Modified

### Created:
- `/api/update_student_profile.php` - Profile update endpoint
- `/api/upload_student_profile_image.php` - Image upload endpoint
- `/sql/add_student_profile_image.sql` - Database migration
- `/docs/STUDENT_PROFILE_UPDATE.md` - This guide

### Modified:
- `/pages/student-profile.php` - Updated JavaScript for real API calls

---

## 🐛 Troubleshooting

### Issue: "Failed to update profile"
**Check:**
- Database connection
- Student is logged in (`$_SESSION['student']` exists)
- All required fields filled

### Issue: "Failed to upload image"
**Check:**
- `uploads/student-profiles/` folder exists and is writable
- File size under 5MB
- File is valid image type
- PHP `upload_max_filesize` and `post_max_size` settings

**Fix permissions (if needed):**
```bash
mkdir -p uploads/student-profiles
chmod 755 uploads/student-profiles
```

### Issue: Changes don't persist after refresh
**Check:**
- SQL migration was run successfully
- Column names match in update query
- Session is maintained across requests

---

## ✨ Next Steps (Optional Enhancements)

Consider adding:
- [ ] Image cropping/resizing before upload
- [ ] Password change functionality
- [ ] Email verification for email changes
- [ ] Activity log for profile changes
- [ ] Profile completion percentage
- [ ] Profile picture moderation (for admin)

---

## 🎉 Summary

Your student profile system is now fully functional with:
✅ Real database saves
✅ Profile image uploads
✅ Data validation
✅ Security measures

**Students can now update their profiles and the changes will persist!**
