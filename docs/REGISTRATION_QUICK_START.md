# Two-Step Registration - Quick Start Guide

## What's New?

The student registration process now uses a **two-step flow** with automatic information extraction:

1. **Step 1**: Upload Student ID and COR
2. **Step 2**: Review auto-filled information and complete registration

## Quick Setup (5 Minutes)

### 1. Create Upload Directories

```bash
cd C:\xampp\htdocs\soccs-financial-management
mkdir uploads\temp
```

Or manually create: `uploads/temp/` folder

### 2. Set Directory Permissions

Make sure the following directories are writable:
- `uploads/temp/`
- `uploads/student-ids/`
- `uploads/documents/`

### 3. Configure OCR API Key

Edit `includes/app_config.php` and add:
```php
'OCR_SPACE_API_KEY' => 'your_api_key_here'
```

**Get Free API Key**: https://ocr.space/ocrapi (Free tier: 25,000 requests/month)

### 4. Test Registration Flow

1. Open: http://localhost/soccs-financial-management/pages/student-registration-step1.php
2. Upload sample Student ID and COR
3. Verify information extraction
4. Complete registration in Step 2

### 5. Set Up Cleanup (Optional)

#### Windows Task Scheduler:
```batch
# Create a batch file: cleanup_temp.bat
cd C:\xampp\htdocs\soccs-financial-management
php includes\cleanup_temp_uploads.php
```

Schedule to run every hour in Task Scheduler.

#### Linux Cron:
```bash
# Add to crontab
0 * * * * cd /path/to/project && php includes/cleanup_temp_uploads.php
```

## User Journey

### For Students:

1. **Click "Register"** on login page
2. **Upload Documents**:
   - Student ID (JPG, PNG - Max 5MB)
   - Certificate of Registration (PDF, JPG, PNG - Max 10MB)
3. **Review Information**: System auto-fills student ID, course, year level, gender
4. **Edit if Needed**: All fields are editable
5. **Complete Registration**: Enter name, email, age, section
6. **Wait for Approval**: Check email for confirmation

### For Admins:

No changes required. Approval process remains the same in Student Approvals page.

## Files Created

```
pages/
├── student-registration-step1.php     ← Step 1: Upload documents
└── student-registration-step2.php     ← Step 2: Complete form

auth/
└── student-register-step2.php         ← Backend processor

api/
└── extract-student-info.php           ← OCR extraction

assets/css/
└── student-registration-steps.css     ← Styles

includes/
└── cleanup_temp_uploads.php           ← Cleanup utility

docs/
├── TWO_STEP_REGISTRATION_GUIDE.md     ← Full documentation
└── REGISTRATION_QUICK_START.md        ← This file
```

## What Information is Auto-Extracted?

From the Certificate of Registration (COR):
- ✓ Student ID Number
- ✓ Course (BSIT or BSCS)
- ✓ Year Level (1-4)
- ✓ Gender

Still Required Manual Entry:
- Name (First, Middle, Last)
- Email Address
- Age
- Section

## Troubleshooting

### "OCR service not configured"
→ Add OCR_SPACE_API_KEY to `includes/app_config.php`

### "Failed to upload" errors
→ Check `uploads/temp/` directory exists and is writable

### Auto-fill not working
→ Verify OCR API key is valid and has remaining quota

### Temporary files accumulating
→ Run `php includes/cleanup_temp_uploads.php` manually

## Testing Tips

### Test with Sample Documents:

**Good Test Documents**:
- Clear, high-resolution images
- Well-lit, no shadows
- Straight orientation (not rotated)
- All text visible and readable

**Expected Results**:
- Student ID should be detected (format: YYYY-NNNNN)
- Course should show BSIT or BSCS
- Year level should be 1, 2, 3, or 4
- Gender should be detected if mentioned in COR

## Important Notes

- Old registration page (`student-registration.php`) still exists but is not used
- Login page automatically redirects to new Step 1
- All existing verification and approval systems remain unchanged
- Documents are stored permanently after approval
- Temporary files auto-delete after 2 hours

## Support

For detailed documentation, see: `docs/TWO_STEP_REGISTRATION_GUIDE.md`

## Rollback (If Needed)

To revert to old registration:

Edit `templates/login.php` line 609:
```html
<!-- Change from: -->
<a href="../pages/student-registration-step1.php" class="register-btn">

<!-- Back to: -->
<a href="../pages/student-registration.php" class="register-btn">
```

## Next Steps

1. ✓ Test with real student documents
2. ✓ Monitor OCR extraction accuracy
3. ✓ Set up automated cleanup
4. ✓ Train staff on new flow
5. ✓ Collect user feedback

## Benefits

- **Faster Registration**: Pre-filled fields reduce typing
- **Fewer Errors**: OCR extraction ensures accuracy
- **Better UX**: Modern two-step process
- **Same Security**: All existing verifications apply
- **Mobile Friendly**: Responsive design

---

**Ready to Go!** Students can now register at:
http://localhost/soccs-financial-management/pages/student-registration-step1.php
