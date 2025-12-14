# Troubleshooting Auto-Fill Issues

## Problem: Student ID and Year Level Not Auto-Filling

### Quick Diagnostics

#### 1. Test Extraction Patterns
Visit: `http://localhost/soccs-financial-management/test_ocr_extraction.php`

This will test the extraction patterns with sample data and verify your OCR API key.

#### 2. Check Browser Console
1. Open Step 2 registration page
2. Press **F12** to open Developer Tools
3. Go to **Console** tab
4. Look for messages like:
   ```
   === REGISTRATION DATA ===
   Auto-filling Student ID: 2021-00001
   Auto-filling Year Level: 2
   ```

If you see warnings like:
- `Student ID not extracted from COR` - OCR didn't find the student ID
- `Year Level not extracted from COR` - OCR didn't find the year level

#### 3. Check PHP Error Logs
Location: `C:\xampp\apache\logs\error.log` (Windows) or `/var/log/apache2/error.log` (Linux)

Look for:
```
=== EXTRACTION RESULTS ===
Student ID: 2021-00001
Year Level: 2
```

If you see `NOT FOUND`, the OCR couldn't extract the information.

### Common Causes & Solutions

#### 🔴 Cause 1: OCR API Key Not Configured
**Symptoms**: Error message "OCR service not configured"

**Solution**:
1. Edit `includes/app_config.php`
2. Add your OCR.Space API key:
   ```php
   'OCR_SPACE_API_KEY' => 'K12345678901234567890123456789'
   ```
3. Get free key at: https://ocr.space/ocrapi

#### 🔴 Cause 2: Poor Document Quality
**Symptoms**: OCR returns empty or garbled text

**Solution**:
- Use high-resolution images (300 DPI or higher)
- Ensure good lighting (no shadows)
- Document should be straight, not rotated
- Text should be clear and readable
- Avoid blurry or low-quality scans

**Test with good documents**:
- Clear Student ID photo
- Official COR from school registrar
- PDF format preferred over images

#### 🔴 Cause 3: Different Document Format
**Symptoms**: Some fields auto-fill, others don't

**Solution**: COR format varies by school. The extraction patterns look for:

**Student ID patterns**:
- `Student ID: 2021-00001`
- `ID No. 2021-00001`
- `Student Number: 202100001`
- `2021-00001` (standalone)

**Year Level patterns**:
- `Year Level: 2`
- `2nd Year`
- `Year: 2`
- `Level: 2`

If your COR uses different format, patterns need adjustment.

#### 🔴 Cause 4: OCR API Quota Exceeded
**Symptoms**: Worked before, now fails

**Solution**:
- Free tier: 25,000 requests/month
- Check usage at: https://ocr.space/ocrapi
- Wait for monthly reset or upgrade account

#### 🔴 Cause 5: Network/Firewall Issues
**Symptoms**: Upload succeeds but extraction fails

**Solution**:
- Check if server can reach `api.ocr.space`
- Verify firewall allows outbound HTTPS
- Test with: `curl https://api.ocr.space/parse/image`

### Step-by-Step Debugging

#### Step 1: Verify OCR API
```bash
# Run test script
php test_ocr_extraction.php
```

Expected output:
```
OCR API Key: K12345678... (configured ✓)

=== Testing: Sample COR #1 ===
RESULTS:
- Student ID: 2021-00001 ✓
- Course: BSIT ✓
- Year Level: 2 ✓
- Gender: male ✓
```

#### Step 2: Test with Real Document
1. Go to Step 1: Upload documents
2. Upload your Student ID and COR
3. Check browser console for API response
4. Check PHP error log for extraction results

#### Step 3: Check Session Storage
In browser console, run:
```javascript
console.log(JSON.parse(sessionStorage.getItem('registrationData')));
```

Should show:
```json
{
  "tempId": "temp_...",
  "extractedInfo": {
    "studentId": "2021-00001",
    "yearLevel": "2",
    "course": "BSIT",
    "gender": "male"
  }
}
```

### Manual Workaround

If auto-fill isn't working but you need to register:

1. **All fields are editable** - Just type the information manually
2. The system will still verify documents after submission
3. Registration will proceed normally

### Improving Extraction Accuracy

#### For Student ID:
Ensure COR contains ID in one of these formats:
- `Student ID: 2021-00001` ← Best
- `ID No.: 2021-00001`
- `Student Number: 2021-00001`

#### For Year Level:
Ensure COR contains year in one of these formats:
- `Year Level: 2` ← Best
- `2nd Year`
- `Year: 2`

#### For Course:
Must contain:
- `BSIT` or `BS IT` or `Bachelor of Science in Information Technology`
- `BSCS` or `BS CS` or `Bachelor of Science in Computer Science`

### Advanced Troubleshooting

#### Check OCR API Response
Add temporary logging in `api/extract-student-info.php`:

```php
$text = $data['ParsedResults'][0]['ParsedText'] ?? '';
error_log('FULL OCR TEXT: ' . $text); // Add this line
```

Check error log to see exactly what OCR extracted.

#### Test Specific Patterns
In `test_ocr_extraction.php`, add your actual COR text:

```php
$sampleTexts["My COR"] = "
    [Paste your actual COR text here]
";
```

Run: `php test_ocr_extraction.php`

#### Adjust Extraction Patterns
If your school uses different format, edit patterns in:
`api/extract-student-info.php`

Functions to modify:
- `extractStudentId()` - Line 204
- `extractYearLevel()` - Line 243

### Getting Help

When reporting issues, provide:

1. **Browser Console Output** (F12 → Console)
2. **PHP Error Log** (relevant lines)
3. **Test Script Results** (`php test_ocr_extraction.php`)
4. **COR Format Sample** (text only, no personal info)
5. **Screenshot of Step 2** (showing which fields are empty)

### FAQ

**Q: Do I need to upload both Student ID and COR?**  
A: Yes, both are required. Student ID for verification, COR for information extraction.

**Q: Can I edit auto-filled fields?**  
A: Yes! All fields are editable even if auto-filled.

**Q: What if extraction is always wrong?**  
A: Just edit the fields manually. Extraction is a helper, not required.

**Q: Does this affect approval?**  
A: No. Document verification happens after submission, regardless of auto-fill.

**Q: Can I skip Step 1?**  
A: No. Both steps are required. You must upload documents first.

### Success Checklist

- ✓ OCR API key configured in `app_config.php`
- ✓ Test script shows successful extractions
- ✓ Documents are clear and high-quality
- ✓ Browser console shows extraction data
- ✓ PHP error log shows extraction results
- ✓ Session storage contains extracted info
- ✓ Fields auto-fill on Step 2 page

### Still Not Working?

If auto-fill still doesn't work after all checks:

1. **Use manual entry** - All fields are editable
2. **Check document format** - Your school might use non-standard format
3. **Contact support** - Provide diagnostic information above

Remember: **Auto-fill is a convenience feature**. Manual entry works perfectly fine and produces the same result!
