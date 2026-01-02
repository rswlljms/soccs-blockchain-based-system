<?php
require_once __DIR__ . '/../includes/database.php';

$database = new Database();
$conn = $database->getConnection();

$studentId = '0122-1205';

$query = "SELECT id, academic_year, semester FROM students WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$studentId]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

echo "=== STUDENT DATABASE VALUES ===\n";
print_r($student);

$query2 = "SELECT cor_file FROM student_registrations WHERE id = ?";
$stmt2 = $conn->prepare($query2);
$stmt2->execute([$studentId]);
$reg = $stmt2->fetch(PDO::FETCH_ASSOC);

if ($reg && !empty($reg['cor_file'])) {
    $corPath = '../' . $reg['cor_file'];
    echo "\n=== COR FILE PATH ===\n";
    echo "Path: {$corPath}\n";
    echo "Exists: " . (file_exists($corPath) ? 'YES' : 'NO') . "\n";
    
    if (file_exists($corPath)) {
        echo "\n=== TESTING EXTRACTION ===\n";
        require_once __DIR__ . '/../api/extract-student-info.php';
        $result = extractInformationFromCOR($corPath);
        
        echo "Extraction Success: " . ($result['success'] ? 'YES' : 'NO') . "\n";
        if ($result['success'] && isset($result['data'])) {
            echo "Academic Year: " . ($result['data']['academicYear'] ?: 'NOT FOUND') . "\n";
            echo "Semester: " . ($result['data']['semester'] ?: 'NOT FOUND') . "\n";
        } else {
            echo "Error: " . ($result['message'] ?? 'Unknown') . "\n";
        }
    }
}

