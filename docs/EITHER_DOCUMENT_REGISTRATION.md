# Either Student ID or COR Registration - Implementation

## Overview
The registration system now accepts **EITHER** Student ID image **OR** Certificate of Registration (COR). The system extracts the student name and student ID number from whichever document is provided and validates them against the uploaded masterlist before allowing registration to proceed.

## Registration Flow

### Step 1: Document Upload
- User can upload **EITHER**:
  - Student ID Image (JPG, PNG, HEIC, WEBP - Max 5MB)
  - **OR** Certificate of Registration (PDF, JPG, PNG, HEIC, WEBP - Max 10MB)
- At least one document is required
- System extracts:
  - Student ID number
  - Student name
- **Validates against masterlist immediately**
- If validation fails, user cannot proceed to Step 2

### Step 2: Complete Registration
- User fills in remaining information
- System uses extracted data from Step 1
- Registration is saved with whichever document was provided

## Masterlist Validation

The system validates:
1. **Student ID exists in masterlist**
2. **Name matches masterlist entry** (fuzzy matching, 80% similarity)

### Validation Rules:
- Student ID must be found in masterlist
- Name from document must match masterlist name
- Uses fuzzy matching to handle name variations
- Validation happens **before** allowing user to proceed to Step 2

## Database Schema

No changes required - existing columns support this:
- `student_id_image` varchar(255) DEFAULT NULL
- `cor_file` varchar(255) DEFAULT NULL

Both can be NULL, but at least one must be provided.

## Files Updated

### Frontend:
- `pages/student-registration-step1.php` - Accepts either Student ID or COR
- `pages/student-registration.php` - Accepts either Student ID or COR
- `pages/student-registration-step2.php` - Handles both file paths

### Backend:
- `api/extract-student-info.php` - Handles either file, extracts info, validates against masterlist
- `auth/student-register.php` - Processes either file, validates against masterlist
- `auth/student-register-step2.php` - Processes either file, validates against masterlist

### New Functions:
- `extractInformationFromStudentId()` - Extracts info from Student ID image
- `validateAgainstMasterlist()` - Validates name and student ID against masterlist
- `normalizeStudentIdForMasterlist()` - Normalizes student ID format
- `normalizeNameForMasterlist()` - Normalizes name for comparison
- `fuzzyNameMatch()` - Performs fuzzy name matching (80% threshold)

## Validation Process

1. **Document Upload**: User uploads either Student ID or COR
2. **OCR Extraction**: System extracts text from document
3. **Data Extraction**: Extracts student ID and name
4. **Masterlist Check**: 
   - Checks if student ID exists in masterlist
   - Checks if name matches masterlist entry
5. **Validation Result**:
   - ✅ **Pass**: User proceeds to Step 2
   - ❌ **Fail**: User sees error message, cannot proceed

## Error Messages

- "Student ID not found in masterlist" - Student ID doesn't exist in masterlist
- "Name does not match the masterlist" - Name doesn't match masterlist entry
- "Could not extract name and student ID from document" - OCR failed or document unclear
- "Student ID in document does not match the entered Student ID" - Mismatch between document and form

## Notes

- If both Student ID and COR are uploaded, COR takes priority for extraction
- Masterlist must be uploaded before students can register
- Name matching uses fuzzy logic (80% similarity) to handle variations
- Student ID format is normalized (XXXX-XXXX) for comparison

