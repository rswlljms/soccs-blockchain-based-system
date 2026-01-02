<?php
require_once __DIR__ . '/../includes/app_config.php';
define('EXTRACT_STUDENT_INFO_INCLUDED', true);
require_once __DIR__ . '/../api/extract-student-info.php';

$testText = "Republic of the Philippines
Laguna State Polytechnic University
Province of Laguna
Santa Cruz Campus
OFFICE OF THE UNIVERSITY REGISTRAR
CERTIFICATE OF REGISTRATION
First (1st) Semester, A.Y. 2025-2026
Student Name: Raymond Paul Valenzuela Rivarez
Course: Bachelor of Science in Information Technology";

echo "=== TESTING EXTRACTION ===\n\n";
echo "Test Text:\n" . $testText . "\n\n";

$academicYear = extractAcademicYear($testText);
$semester = extractSemester($testText);

echo "Extracted Academic Year: " . ($academicYear ?: 'NOT FOUND') . "\n";
echo "Extracted Semester: " . ($semester ?: 'NOT FOUND') . "\n\n";

if (empty($academicYear)) {
    echo "Academic Year extraction failed. Testing patterns...\n";
    if (preg_match('/a\.y\.\s*[:.\-\s,]*([0-9]{4}[\s\-]+[0-9]{4})/i', $testText, $matches)) {
        echo "Pattern matched! Found: " . print_r($matches, true) . "\n";
    } else {
        echo "Pattern did NOT match\n";
    }
}

if (empty($semester)) {
    echo "Semester extraction failed. Testing patterns...\n";
    if (preg_match('/first\s*\(?\s*1st\s*\)?\s*semester/i', $testText, $matches)) {
        echo "Pattern matched! Found: " . print_r($matches, true) . "\n";
    } else {
        echo "Pattern did NOT match\n";
    }
}

