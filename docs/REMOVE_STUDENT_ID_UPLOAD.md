# Remove Student ID Upload - Implementation Summary

## Overview
This document describes the changes made to remove the Student ID upload requirement from the student registration process. The system now only requires the Certificate of Registration (COR) upload, and validates student names against the masterlist.

## Changes Made

### 1. Frontend Changes

#### `pages/student-registration-step1.php`
- **Removed**: Student ID upload field and related UI elements
- **Kept**: COR upload field (required)
- **Updated**: JavaScript to only handle COR file upload

#### `pages/student-registration-step2.php`
- **Removed**: Hidden input field for `studentIdPath`
- **Updated**: Session data handling to only include COR path

#### `pages/student-approvals.php`
- **Removed**: Student ID image display section from approval modal
- **Updated**: Modal now only shows COR document

#### `assets/js/student-approvals.js`
- **Removed**: Student ID image column from approvals table
- **Removed**: `studentIdImage` property from student data mapping
- **Updated**: Table colspan from 8 to 7 columns

### 2. Backend API Changes

#### `api/extract-student-info.php`
- **Removed**: Student ID file validation and upload handling
- **Updated**: `validateDocuments()` function to only validate COR
  - Removed Student ID OCR processing
  - Only validates COR contains school name
- **Updated**: Response structure to remove `studentIdPath`

#### `auth/student-register-step2.php`
- **Removed**: Student ID file processing and storage
- **Removed**: Student ID path recovery logic
- **Added**: Masterlist validation using name extracted from COR
  - Extracts name from COR using OCR
  - Validates against masterlist using `validateAgainstMasterlist()`
  - Checks both form name and extracted name against masterlist
- **Updated**: Database insert/update queries to remove `student_id_image` column
- **Added**: Helper functions for masterlist validation:
  - `validateAgainstMasterlist()` - Validates student ID and name against masterlist
  - `normalizeStudentIdForMasterlist()` - Normalizes student ID format
  - `normalizeNameForMasterlist()` - Normalizes name for comparison
  - `fuzzyNameMatch()` - Performs fuzzy name matching (80% similarity threshold)

#### `includes/document_verification_service.php`
- **Removed**: Student ID image processing and validation
- **Updated**: `runVerification()` method to only verify COR
  - Removed Student ID OCR processing
  - Only checks name and student ID in COR document
  - Simplified verification logic
- **Updated**: `estimateTamperProbability()` to only check COR file

### 3. Database Changes

#### `sql/remove_student_id_image_column.sql`
- **Created**: SQL migration script to remove `student_id_image` column from `student_registrations` table
- **Compatible**: Works with MySQL/MariaDB
- **Safe**: Checks if column exists before dropping

## Masterlist Validation

The registration process now includes masterlist validation:

1. **Name Extraction**: System extracts student name from COR using OCR
2. **Masterlist Check**: Validates that:
   - Student ID exists in masterlist
   - Name (from form or extracted from COR) matches masterlist entry
3. **Fuzzy Matching**: Uses 80% similarity threshold for name matching
4. **Validation Failure**: Registration is rejected if:
   - Student ID not found in masterlist
   - Name doesn't match masterlist entry

## Verification Process

The document verification process now only checks COR:

1. **OCR Processing**: Extracts text from COR document
2. **Name Verification**: Checks if name (without middle name) appears in COR
3. **Student ID Verification**: Checks if student ID number appears in COR
4. **Approval Criteria**: Both name and student ID must be found in COR

## Migration Instructions

1. **Backup Database**: Always backup your database before running migrations
2. **Run SQL Migration**: Execute `sql/remove_student_id_image_column.sql` in your database
3. **Clear Cache**: Clear any application caches
4. **Test Registration**: Test the new registration flow with COR-only upload

## Testing Checklist

- [ ] Student registration with COR only works
- [ ] Masterlist validation works correctly
- [ ] Name extraction from COR works
- [ ] Registration rejection when name not in masterlist
- [ ] Registration rejection when student ID not in masterlist
- [ ] Document verification only checks COR
- [ ] Approval page no longer shows Student ID image
- [ ] Database migration runs successfully

## Notes

- Old registrations with `student_id_image` will still have the column value (NULL after migration)
- The `document_verification_results` table still has `student_id_image_path` column but it will be NULL
- Masterlist must be uploaded before students can register
- Name matching is case-insensitive and uses fuzzy matching for variations

