<?php
require_once __DIR__ . '/../includes/app_config.php';
require_once __DIR__ . '/../api/extract-student-info.php';

if ($argc < 2) {
    echo "Usage: php test-real-cor.php <path-to-cor-image>\n";
    exit(1);
}

$corPath = $argv[1];

if (!file_exists($corPath)) {
    echo "Error: File not found: {$corPath}\n";
    exit(1);
}

echo "=== TESTING REAL COR EXTRACTION ===\n\n";
echo "File: {$corPath}\n";
echo "File exists: " . (file_exists($corPath) ? 'YES' : 'NO') . "\n";
echo "File size: " . filesize($corPath) . " bytes\n\n";

$result = extractInformationFromCOR($corPath);

echo "=== EXTRACTION RESULT ===\n";
echo "Success: " . ($result['success'] ? 'YES' : 'NO') . "\n";

if ($result['success'] && isset($result['data'])) {
    $data = $result['data'];
    echo "\nExtracted Data:\n";
    echo "  Student ID: " . ($data['studentId'] ?: 'NOT FOUND') . "\n";
    echo "  Course: " . ($data['course'] ?: 'NOT FOUND') . "\n";
    echo "  Year Level: " . ($data['yearLevel'] ?: 'NOT FOUND') . "\n";
    echo "  Gender: " . ($data['gender'] ?: 'NOT FOUND') . "\n";
    echo "  Academic Year: " . ($data['academicYear'] ?: 'NOT FOUND') . "\n";
    echo "  Semester: " . ($data['semester'] ?: 'NOT FOUND') . "\n";
    
    if (isset($data['ocrText'])) {
        echo "\nOCR Text (first 1000 chars):\n";
        echo substr($data['ocrText'], 0, 1000) . "\n";
    }
    
    if (isset($data['debug'])) {
        echo "\nDebug Info:\n";
        print_r($data['debug']);
    }
} else {
    echo "Error: " . ($result['message'] ?? 'Unknown error') . "\n";
}

echo "\n=== CHECK ERROR LOG ===\n";
echo "Check error_log for detailed extraction logs.\n";

