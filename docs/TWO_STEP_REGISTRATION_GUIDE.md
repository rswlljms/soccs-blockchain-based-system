# Two-Step Student Registration System

## Overview

The student registration process has been redesigned to use a two-step flow with automatic information extraction from uploaded documents. This improves data accuracy and reduces manual entry errors.

## Registration Flow

### Step 1: Document Upload
**File**: `pages/student-registration-step1.php`

Students upload two required documents:
1. **Student ID** (JPG, PNG, HEIC, WEBP - Max 5MB)
2. **Certificate of Registration (COR)** (PDF, JPG, PNG, HEIC, WEBP - Max 10MB)

The system:
- Validates file types and sizes
- Uploads files to temporary storage (`uploads/temp/`)
- Sends files to OCR processing API
- Extracts student information from COR
- Stores extracted data in session storage
- Redirects to Step 2

### Step 2: Complete Registration
**File**: `pages/student-registration-step2.php`

Students review and edit their information:

**Auto-filled Fields** (from OCR extraction):
- Student ID Number
- Course (BSIT or BSCS)
- Year Level (1-4)
- Gender

**Manual Entry Fields**:
- First Name
- Middle Name (optional)
- Last Name
- Suffix (optional)
- Age
- Email Address
- Section

All auto-filled fields remain **editable** to allow corrections.

## Technical Implementation

### File Structure

```
pages/
├── student-registration-step1.php    # Document upload page
└── student-registration-step2.php    # Registration form with auto-fill

auth/
├── student-register-step2.php        # Registration processing backend

api/
└── extract-student-info.php          # OCR extraction endpoint

assets/css/
└── student-registration-steps.css    # Stylesheet for registration flow

includes/
└── cleanup_temp_uploads.php          # Temporary file cleanup utility

sql/
└── temp_uploads_cleanup.sql          # Database cleanup event
```

### API Endpoints

#### 1. Extract Student Information
**Endpoint**: `api/extract-student-info.php`  
**Method**: POST  
**Content-Type**: multipart/form-data

**Request Parameters**:
- `studentIdImage` (file) - Student ID image
- `corFile` (file) - Certificate of Registration

**Response** (Success):
```json
{
  "status": "success",
  "message": "Documents uploaded successfully",
  "data": {
    "tempId": "temp_abc123",
    "studentIdPath": "../uploads/temp/temp_abc123_studentid.jpg",
    "corPath": "../uploads/temp/temp_abc123_cor.pdf",
    "extractedInfo": {
      "studentId": "2021-00001",
      "course": "BSIT",
      "yearLevel": "2",
      "gender": "male",
      "ocrText": "..."
    }
  }
}
```

**Response** (Error):
```json
{
  "status": "error",
  "message": "Error description"
}
```

#### 2. Complete Registration
**Endpoint**: `auth/student-register-step2.php`  
**Method**: POST  
**Content-Type**: multipart/form-data

**Request Parameters**:
- `tempId` - Temporary session identifier
- `studentIdPath` - Path to uploaded student ID
- `corPath` - Path to uploaded COR
- `firstName` - Student first name
- `middleName` - Student middle name (optional)
- `lastName` - Student last name
- `email` - Student email address
- `studentId` - Student ID number
- `course` - Course (BSIT or BSCS)
- `yearLevel` - Year level (1-4)
- `section` - Section letter
- `age` - Student age
- `gender` - Gender (male, female, other)

**Response** (Success):
```json
{
  "status": "success",
  "message": "Registration approved! Check your email to set your password.",
  "student_id": "2021-00001"
}
```

**Response** (Error/Rejected):
```json
{
  "status": "error",
  "message": "Registration rejected: [reason]",
  "student_id": "2021-00001"
}
```

### OCR Information Extraction

The system uses OCR.Space API to extract text from the COR document and applies pattern matching to identify:

1. **Student ID**: Matches patterns like `2021-00001`, `202100001`
2. **Course**: Detects BSIT or BSCS keywords
3. **Year Level**: Finds year indicators (1st Year, 2nd Year, etc.)
4. **Gender**: Searches for gender keywords

**Extraction Functions** (in `api/extract-student-info.php`):
- `performOCR()` - Calls OCR.Space API
- `extractStudentId()` - Pattern matching for student ID
- `extractCourse()` - Course detection
- `extractYearLevel()` - Year level extraction
- `extractGender()` - Gender identification

### Document Verification

After registration submission, the system:
1. Moves files from temporary to permanent storage
2. Runs document verification (existing `DocumentVerificationService`)
3. Auto-approves if verification passes
4. Auto-rejects if verification fails
5. Sends email notification to student

### Session Storage

Step 1 stores extracted data in browser's `sessionStorage`:
```javascript
sessionStorage.setItem('registrationData', JSON.stringify(data.data));
```

Step 2 retrieves and populates form:
```javascript
const registrationData = sessionStorage.getItem('registrationData');
const data = JSON.parse(registrationData);
```

### File Management

#### Temporary Files
- Location: `uploads/temp/`
- Naming: `temp_{uniqid}_studentid.{ext}`, `temp_{uniqid}_cor.{ext}`
- Lifespan: Deleted after 2 hours
- Cleanup: `includes/cleanup_temp_uploads.php`

#### Permanent Files
- Student IDs: `uploads/student-ids/{studentId}.{ext}`
- COR Files: `uploads/documents/{uniqid}_COR_{studentId}.{ext}`

### Database Schema

No schema changes required. Uses existing tables:
- `student_registrations` - Registration records
- `students` - Approved student accounts

## Configuration

### OCR.Space API Key
Configure in `includes/app_config.php`:
```php
'OCR_SPACE_API_KEY' => 'your_api_key_here'
```

Get free API key at: https://ocr.space/ocrapi

### File Upload Limits
Adjust in respective PHP files:
- Student ID: 5MB max
- COR: 10MB max

### Allowed File Types
- **Student ID**: JPG, PNG, HEIC, WEBP
- **COR**: PDF, JPG, PNG, HEIC, WEBP

## User Experience

### Progress Indicators
Both steps display a progress bar showing:
- Step 1: Upload Documents (active/completed)
- Step 2: Complete Registration (pending/active)

### Visual Feedback
- Drag-and-drop file upload zones
- File upload success indicators
- Loading spinners during processing
- Success/error modals
- Auto-fill indicators

### Mobile Responsive
- Optimized layouts for mobile devices
- Touch-friendly file upload zones
- Simplified navigation

## Error Handling

### Step 1 Errors
- Invalid file types
- File size exceeded
- OCR processing failure
- Network errors

### Step 2 Errors
- Missing session data → Redirect to Step 1
- Temporary files not found → Restart registration
- Duplicate student ID/email
- Validation errors
- Document verification failures

## Security Measures

1. **File Validation**: Type and size checks
2. **Temporary File Cleanup**: Auto-deletion after 2 hours
3. **Session Storage**: Client-side only, no sensitive data
4. **File Naming**: Randomized for COR files
5. **Input Sanitization**: All form inputs validated
6. **Document Verification**: Existing anti-fraud checks

## Maintenance

### Cleanup Temporary Files

**Manual Cleanup** (via PHP CLI):
```bash
php includes/cleanup_temp_uploads.php
```

**Automated Cleanup** (via cron):
```bash
# Add to crontab (runs every hour)
0 * * * * cd /path/to/project && php includes/cleanup_temp_uploads.php
```

**Database Event** (MySQL):
```sql
-- Enable event scheduler
SET GLOBAL event_scheduler = ON;

-- Event is defined in sql/temp_uploads_cleanup.sql
```

## Testing

### Test Step 1
1. Navigate to `pages/student-registration-step1.php`
2. Upload valid Student ID and COR files
3. Verify information extraction
4. Confirm redirect to Step 2

### Test Step 2
1. Verify auto-filled fields are populated
2. Test field editability
3. Submit registration
4. Verify email notification
5. Check approval status

### Test Error Cases
- Upload invalid file types
- Upload oversized files
- Submit incomplete form
- Test duplicate registrations
- Verify session expiry handling

## Troubleshooting

### Issue: OCR extraction fails
**Solution**: 
- Check OCR_SPACE_API_KEY configuration
- Verify API key is valid
- Check network connectivity
- Ensure document quality is sufficient

### Issue: Files not uploading
**Solution**:
- Check directory permissions (uploads/temp/)
- Verify PHP upload_max_filesize setting
- Check post_max_size in php.ini

### Issue: Auto-fill not working
**Solution**:
- Check browser console for errors
- Verify sessionStorage is enabled
- Test OCR extraction separately
- Review pattern matching in extraction functions

### Issue: Temporary files accumulating
**Solution**:
- Run cleanup script manually
- Set up cron job for automated cleanup
- Check directory permissions
- Verify cleanup script execution

## Migration from Old Registration

The old single-page registration (`pages/student-registration.php`) is still available but deprecated. To migrate users:

1. Update all registration links to point to `student-registration-step1.php`
2. Login page already updated
3. Keep old registration page for reference
4. Monitor usage analytics
5. Remove old page after transition period

## Future Enhancements

1. **Enhanced OCR**:
   - Name extraction from documents
   - Academic year and semester detection
   - Multi-language support

2. **Document Validation**:
   - QR code verification
   - Barcode scanning
   - Watermark detection

3. **User Experience**:
   - Real-time extraction preview
   - Document quality checking
   - Auto-rotation and enhancement

4. **Performance**:
   - Async OCR processing
   - Background job queue
   - CDN for document storage

## Support

For issues or questions:
1. Check error logs: `error_log()`
2. Review OCR API response
3. Verify file permissions
4. Check database records
5. Test with sample documents

## Changelog

### Version 1.0 (December 2025)
- Initial implementation
- Two-step registration flow
- OCR-based information extraction
- Auto-fill with edit capability
- Document verification integration
- Temporary file management
- Mobile responsive design
