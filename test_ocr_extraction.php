<?php
require_once 'includes/app_config.php';

$apiKey = AppConfig::get('OCR_SPACE_API_KEY', '');

if (empty($apiKey)) {
    die("ERROR: OCR_SPACE_API_KEY not configured in includes/app_config.php");
}

echo "OCR API Key: " . substr($apiKey, 0, 10) . "... (configured ✓)\n\n";

$sampleTexts = [
    "Sample COR #1 (Your Exact Format)" => "
        Student No. : 0122-1141
        Course: Bachelor of Science in Information Technology
        Year Level: Fourth Year
        GENDER: MALE
    ",
    "Sample COR #2 (BSCS Format)" => "
        Student No. : 2021-00001
        Course: Bachelor of Science in Computer Science
        Year Level: Third Year
        GENDER: FEMALE
    ",
    "Sample COR #3" => "
        STUDENT ID: 2021-00002
        COURSE: BSIT
        YEAR LEVEL: 2
        GENDER: FEMALE
    ",
    "Sample COR #4" => "
        Student Number 202100003
        Bachelor of Science in Computer Science
        2nd Year
        Female
    ",
    "Sample COR #5" => "
        ID No. 2023 12345
        BS IT - Information Technology
        Year: 1
        Gender: M
    "
];

foreach ($sampleTexts as $label => $text) {
    echo "=== Testing: $label ===\n";
    echo "Input Text:\n$text\n\n";
    
    $studentId = extractStudentId($text);
    $course = extractCourse($text);
    $yearLevel = extractYearLevel($text);
    $gender = extractGender($text);
    
    echo "RESULTS:\n";
    echo "- Student ID: " . ($studentId ?: "NOT FOUND ✗") . "\n";
    echo "- Course: " . ($course ?: "NOT FOUND ✗") . "\n";
    echo "- Year Level: " . ($yearLevel ?: "NOT FOUND ✗") . "\n";
    echo "- Gender: " . ($gender ?: "NOT FOUND ✗") . "\n";
    echo "\n" . str_repeat("-", 50) . "\n\n";
}

function extractStudentId($text) {
    $patterns = [
        '/student\s*no\.?\s*[:\.\-\s]*([0-9]{4}[-\s]?[0-9]{4})/i',
        '/student\s*(?:id|no\.?|number|num|#)[\s:.\-]*([0-9]{4}[-\s.]?[0-9]{4,5})/i',
        '/(?:id|no\.?|number|num)[\s:.\-]*([0-9]{4}[-\s.]?[0-9]{4,5})/i',
        '/\b([0-9]{4}[-\s.][0-9]{4})\b/',
        '/\b([0-9]{4}[-\s.][0-9]{5})\b/',
        '/\b([0-9]{8,9})\b/',
        '/(?:student|id).*?([0-9]{4}[-\s.]?[0-9]{4,5})/i',
        '/([0-9]{4}[-\s.]*[0-9]{4,5})/'
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $text, $matches)) {
            $id = trim($matches[1]);
            $id = preg_replace('/[\s._]+/', '-', $id);
            $id = preg_replace('/[^0-9-]/', '', $id);
            $id = preg_replace('/-+/', '-', $id);
            
            if (preg_match('/^\d{4}-?\d{4}$/', $id)) {
                if (strpos($id, '-') === false && strlen($id) === 8) {
                    $id = substr($id, 0, 4) . '-' . substr($id, 4);
                }
                return $id . " ✓";
            }
            
            if (preg_match('/^\d{4}-?\d{5}$/', $id)) {
                if (strpos($id, '-') === false && strlen($id) === 9) {
                    $id = substr($id, 0, 4) . '-' . substr($id, 4);
                }
                return $id . " ✓";
            }
        }
    }
    
    return '';
}

function extractCourse($text) {
    $text = strtolower($text);
    
    if (preg_match('/\b(bsit|bs\s*it|bachelor.*information\s*technology)\b/i', $text)) {
        return 'BSIT ✓';
    }
    
    if (preg_match('/\b(bscs|bs\s*cs|bachelor.*computer\s*science)\b/i', $text)) {
        return 'BSCS ✓';
    }
    
    return 'BSIT (default)';
}

function extractYearLevel($text) {
    $patterns = [
        '/year\s*level[\s:.\-]*([1-4])/i',
        '/([1-4])(?:st|nd|rd|th)\s*(?:year|yr)/i',
        '/year[\s:.\-]*([1-4])/i',
        '/(?:level|yr|y)[\s:.\-]*([1-4])/i',
        '/\b([1-4])\s*(?:st|nd|rd|th)\b/i',
        '/(?:first|second|third|fourth)\s*year/i'
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $text, $matches)) {
            $yearText = strtolower($matches[0] ?? '');
            
            if (strpos($yearText, 'first') !== false || strpos($yearText, '1st') !== false || ($matches[1] ?? '') === '1') {
                return '1 ✓';
            }
            if (strpos($yearText, 'second') !== false || strpos($yearText, '2nd') !== false || ($matches[1] ?? '') === '2') {
                return '2 ✓';
            }
            if (strpos($yearText, 'third') !== false || strpos($yearText, '3rd') !== false || ($matches[1] ?? '') === '3') {
                return '3 ✓';
            }
            if (strpos($yearText, 'fourth') !== false || strpos($yearText, '4th') !== false || ($matches[1] ?? '') === '4') {
                return '4 ✓';
            }
            
            $year = intval($matches[1] ?? 0);
            if ($year >= 1 && $year <= 4) {
                return $year . ' ✓';
            }
        }
    }
    
    return '';
}

function extractGender($text) {
    $text = strtolower($text);
    
    if (preg_match('/\b(male|m\b)/i', $text) && !preg_match('/female/i', $text)) {
        return 'male ✓';
    }
    
    if (preg_match('/\b(female|f\b)/i', $text)) {
        return 'female ✓';
    }
    
    return '';
}

echo "\n=== INSTRUCTIONS ===\n";
echo "1. Check if OCR API Key is configured above\n";
echo "2. Review sample extraction results\n";
echo "3. If extractions work here but not in registration:\n";
echo "   - Check browser console (F12) for errors\n";
echo "   - Check PHP error logs for OCR API errors\n";
echo "   - Verify document quality and text clarity\n";
echo "4. Test actual document upload at:\n";
echo "   http://localhost/soccs-financial-management/pages/student-registration-step1.php\n";
?>
