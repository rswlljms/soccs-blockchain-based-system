# Student Registration System - Setup Guide

## Overview
Complete guide to set up and configure the student registration system with email notifications.

---

## Database Setup

### Step 1: Import Database Schema

1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create database if not exists:
   ```sql
   CREATE DATABASE soccs_financial_management;
   ```
3. Import the main SQL file:
   - Select `soccs_financial_management` database
   - Click "Import" tab
   - Choose file: `soccs_financial_management.sql`
   - Click "Go"

### Step 2: Verify Tables Created

Ensure these tables exist:
- ✅ `users` - Admin users
- ✅ `students` - Approved students
- ✅ `student_registrations` - Pending registrations
- ✅ `expenses` - Financial expenses
- ✅ `funds` - Financial funds

---

## File Structure

```
soccs-financial-management/
├── auth/
│   ├── student-auth.php          # Student login
│   └── student-register.php       # Registration handler
├── includes/
│   ├── database.php               # Database connection
│   └── email_config.php           # Email service
├── pages/
│   └── student-registration.php   # Registration form
├── uploads/
│   ├── student-ids/               # Student ID images
│   └── documents/                 # COR files
└── docs/
    ├── EMAIL_SETUP_GUIDE.md       # Email configuration
    └── REGISTRATION_SETUP.md      # This file
```

---

## Registration Flow

### 1. Student Registration Process

**Frontend (pages/student-registration.php):**
1. Student fills registration form:
   - Personal Info (name, age, gender, email)
   - Password (min 8 characters)
   - Academic Info (student ID, course, year, section)
   - Upload Student ID image (JPG/PNG, max 5MB)
   - Upload COR file (PDF/JPG/PNG, max 10MB)

2. Frontend validation:
   - All required fields
   - Email format
   - Password match
   - Password length (min 8 chars)
   - File types and sizes

3. Form submission to `auth/student-register.php`

**Backend (auth/student-register.php):**
1. Validates all input data
2. Checks for duplicate Student ID or email
3. Hashes password using `password_hash()`
4. Uploads files to server
5. Inserts record into `student_registrations` table
6. Sends confirmation email to student
7. Returns success response

**Modal Display:**
- Shows success message
- Instructs student to check email
- Redirects to login page on "OK"

### 2. Admin Approval Process

**Admin Dashboard (pages/student-approvals.php):**
1. Admin views pending registrations
2. Reviews student information and uploaded documents
3. Approves or rejects registration
4. On approval:
   - Student record moved from `student_registrations` to `students` table
   - Email sent to student with approval notification
   - Student can now login

---

## Email Configuration

### Quick Setup for XAMPP

1. **Edit php.ini** (`C:\xampp\php\php.ini`):
   ```ini
   [mail function]
   SMTP = smtp.gmail.com
   smtp_port = 587
   sendmail_from = lspuscc.soccs@gmail.com
   sendmail_path = "\"C:\xampp\sendmail\sendmail.exe\" -t"
   ```

2. **Edit sendmail.ini** (`C:\xampp\sendmail\sendmail.ini`):
   ```ini
   smtp_server=smtp.gmail.com
   smtp_port=587
   auth_username=lspuscc.soccs@gmail.com
   auth_password=YOUR_APP_PASSWORD_HERE
   force_sender=lspuscc.soccs@gmail.com
   ```

3. **Get Gmail App Password:**
   - Go to Google Account → Security
   - Enable 2-Step Verification
   - Create App Password for "Mail"
   - Use this password in sendmail.ini

4. **Restart Apache** in XAMPP Control Panel

📖 **Detailed guide:** See `docs/EMAIL_SETUP_GUIDE.md`

---

## Testing the Registration System

### 1. Test Registration Form

1. Navigate to: `http://localhost/soccs-financial-management/pages/student-registration.php`
2. Fill in all required fields:
   - First Name: Test
   - Middle Name: User
   - Last Name: Student
   - Age: 20
   - Gender: Male
   - Email: your-test-email@gmail.com
   - Password: TestPass123
   - Confirm Password: TestPass123
   - Student ID: TEST2024001
   - Course: BSIT
   - Year Level: 1
   - Section: A
   - Upload Student ID image (any JPG/PNG)
   - Upload COR file (any PDF/JPG/PNG)
3. Click "Register Student"
4. Verify success modal appears
5. Check email for confirmation

### 2. Verify Database Entry

```sql
SELECT * FROM student_registrations 
WHERE id = 'TEST2024001';
```

Should show:
- ✅ All student information
- ✅ Hashed password
- ✅ File paths for uploads
- ✅ Status: 'pending'
- ✅ Created timestamp

### 3. Check Email Delivery

- Open email inbox
- Look for "SOCCS Registration - Confirmation"
- Verify email contains:
  - Student name
  - Student ID
  - Registration status (pending)

### 4. Test Password Security

```php
// Verify password is hashed
$stmt = $pdo->prepare("SELECT password FROM student_registrations WHERE id = ?");
$stmt->execute(['TEST2024001']);
$row = $stmt->fetch();

// Should start with $2y$ (bcrypt hash)
echo $row['password']; // e.g., $2y$10$abc123...
```

---

## Security Features

### ✅ Implemented Security Measures

1. **Password Security:**
   - Minimum 8 characters required
   - Hashed using `password_hash()` with bcrypt
   - Never stored in plain text

2. **Input Validation:**
   - Server-side validation for all inputs
   - Email format validation
   - SQL injection prevention (prepared statements)
   - File type and size validation

3. **File Upload Security:**
   - Allowed file types only (JPG, PNG, PDF)
   - File size limits (5MB for images, 10MB for COR)
   - Unique filenames to prevent overwriting
   - Files stored outside web root (recommended for production)

4. **Database Security:**
   - Prepared statements with bound parameters
   - No direct SQL concatenation
   - Duplicate email/ID prevention

5. **Email Security:**
   - HTML email sanitization
   - No sensitive data in plain text
   - Secure SMTP connection (TLS/SSL)

---

## Troubleshooting

### Issue 1: Registration fails with "Student ID already exists"
**Solution:**
- Check both `students` and `student_registrations` tables
- Use different Student ID or email

### Issue 2: Files not uploading
**Solution:**
1. Check folder permissions:
   ```bash
   chmod 755 uploads/student-ids
   chmod 755 uploads/documents
   ```
2. Verify `upload_max_filesize` in php.ini:
   ```ini
   upload_max_filesize = 20M
   post_max_size = 25M
   ```

### Issue 3: Email not received
**Solution:**
1. Check spam/junk folder
2. Verify email configuration (see EMAIL_SETUP_GUIDE.md)
3. Check sendmail error logs:
   - `C:\xampp\sendmail\error.log`
4. Test with: `http://localhost/soccs-financial-management/test_email.php`

### Issue 4: Password too short error
**Solution:**
- Ensure password is at least 8 characters
- Check both frontend and backend validation

### Issue 5: Modal not appearing
**Solution:**
1. Check browser console for JavaScript errors
2. Verify modal CSS classes are loaded
3. Check if jQuery/FontAwesome is loaded

---

## File Permissions (Production)

```bash
# Set correct permissions
chmod 755 auth/
chmod 644 auth/student-register.php
chmod 755 uploads/
chmod 755 uploads/student-ids/
chmod 755 uploads/documents/
chmod 644 includes/database.php
chmod 644 includes/email_config.php
```

---

## Database Schema Reference

### student_registrations Table

| Column | Type | Description |
|--------|------|-------------|
| id | varchar(20) | Student ID (Primary Key) |
| first_name | varchar(100) | Student's first name |
| middle_name | varchar(100) | Student's middle name |
| last_name | varchar(100) | Student's last name |
| email | varchar(255) | Unique email address |
| password | varchar(255) | Hashed password |
| course | varchar(10) | BSIT or BSCS |
| year_level | int(1) | 1-4 year level |
| section | varchar(1) | Section letter |
| age | int(3) | Student's age |
| gender | enum | male, female, other |
| student_id_image | varchar(255) | Path to ID image |
| cor_file | varchar(255) | Path to COR file |
| approval_status | enum | pending, approved, rejected |
| created_at | timestamp | Registration date |
| approved_at | timestamp | Approval date |
| rejected_at | timestamp | Rejection date |
| approved_by | varchar(255) | Admin who approved |
| rejection_reason | text | Reason for rejection |

---

## Next Steps

After successful registration setup:

1. ✅ Test complete registration flow
2. ✅ Configure email service
3. ✅ Set up admin approval system
4. ✅ Implement student login
5. ✅ Create student dashboard
6. ✅ Add student portal features

---

## Support

For technical support or issues:
- 📧 Email: lspuscc.soccs@gmail.com
- 📚 Documentation: `/docs/` folder
- 🐛 Report issues to system administrator

---

## Change Log

### Version 1.0 (Current)
- ✅ Student registration form with validation
- ✅ Email and password fields
- ✅ File upload (Student ID + COR)
- ✅ Email notification system
- ✅ Success modal with confirmation message
- ✅ Password hashing and security
- ✅ Database integration

