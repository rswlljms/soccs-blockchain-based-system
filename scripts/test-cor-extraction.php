<?php
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/app_config.php';
define('EXTRACT_STUDENT_INFO_INCLUDED', true);
require_once __DIR__ . '/../api/extract-student-info.php';

$database = new Database();
$conn = $database->getConnection();

echo "=== TESTING COR EXTRACTION FOR RECENT REGISTRATIONS ===\n\n";

$query = "SELECT id, cor_file FROM student_registrations WHERE cor_file IS NOT NULL ORDER BY created_at DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->execute();
$reg = $stmt->fetch(PDO::FETCH_ASSOC);

if ($reg) {
    $corFile = $reg['cor_file'];
    $corPath = __DIR__ . '/../' . $corFile;
    
    echo "Student ID: " . $reg['id'] . "\n";
    echo "COR File: " . $corFile . "\n";
    echo "Full Path: " . $corPath . "\n";
    echo "File Exists: " . (file_exists($corPath) ? 'YES' : 'NO') . "\n\n";
    
    if (file_exists($corPath)) {
        $realPath = realpath($corPath);
        echo "Real Path: " . $realPath . "\n\n";
        
        echo "=== EXTRACTING ===\n";
        $extractedInfo = extractInformationFromCOR($realPath);
        
        echo "Success: " . ($extractedInfo['success'] ? 'YES' : 'NO') . "\n";
        
        if ($extractedInfo['success'] && isset($extractedInfo['data'])) {
            echo "Academic Year: " . ($extractedInfo['data']['academicYear'] ?: 'NOT FOUND') . "\n";
            echo "Semester: " . ($extractedInfo['data']['semester'] ?: 'NOT FOUND') . "\n";
            echo "\nFull data:\n";
            print_r($extractedInfo['data']);
        } else {
            echo "Error: " . ($extractedInfo['message'] ?? 'Unknown error') . "\n";
        }
    } else {
        echo "ERROR: COR file does not exist!\n";
    }
} else {
    echo "No registrations found with COR files.\n";
}

