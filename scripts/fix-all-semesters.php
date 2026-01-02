<?php
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../api/extract-student-info.php';

$database = new Database();
$conn = $database->getConnection();

echo "=== FIXING ALL STUDENTS WITH MISSING SEMESTER ===\n\n";

$query = "SELECT s.id, s.academic_year, s.semester, sr.cor_file 
          FROM students s
          LEFT JOIN student_registrations sr ON s.id = sr.id
          WHERE (s.semester IS NULL OR s.semester = '') 
          AND sr.cor_file IS NOT NULL
          ORDER BY s.id DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Found " . count($students) . " students to process.\n\n";

$fixed = 0;
$failed = 0;

foreach ($students as $student) {
    $corPath = '../' . $student['cor_file'];
    
    if (!file_exists($corPath)) {
        echo "SKIP: {$student['id']} - COR file not found: {$corPath}\n";
        $failed++;
        continue;
    }
    
    echo "Processing: {$student['id']}...\n";
    
    $extractedInfo = extractInformationFromCOR($corPath);
    
    if ($extractedInfo['success'] && isset($extractedInfo['data'])) {
        $academicYear = $extractedInfo['data']['academicYear'] ?? null;
        $semester = $extractedInfo['data']['semester'] ?? null;
        
        if (empty($academicYear)) $academicYear = null;
        if (empty($semester)) $semester = null;
        
        $updates = [];
        $params = [];
        
        if (!empty($academicYear) && empty($student['academic_year'])) {
            $updates[] = "academic_year = ?";
            $params[] = $academicYear;
        }
        
        if (!empty($semester)) {
            $updates[] = "semester = ?";
            $params[] = $semester;
        }
        
        if (!empty($updates)) {
            $params[] = $student['id'];
            $updateQuery = "UPDATE students SET " . implode(', ', $updates) . " WHERE id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            
            if ($updateStmt->execute($params)) {
                echo "  ✓ Updated - Academic Year: " . ($academicYear ?: 'N/A') . ", Semester: " . ($semester ?: 'N/A') . "\n";
                $fixed++;
            } else {
                echo "  ✗ Update failed\n";
                $failed++;
            }
        } else {
            echo "  - No new data to update\n";
        }
    } else {
        echo "  ✗ Extraction failed: " . ($extractedInfo['message'] ?? 'Unknown') . "\n";
        $failed++;
    }
    
    echo "\n";
}

echo "=== SUMMARY ===\n";
echo "Fixed: {$fixed}\n";
echo "Failed: {$failed}\n";
echo "Total: " . count($students) . "\n";

