<?php
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/app_config.php';
define('EXTRACT_STUDENT_INFO_INCLUDED', true);
require_once __DIR__ . '/../api/extract-student-info.php';

$database = new Database();
$conn = $database->getConnection();

$query = "SELECT s.id, sr.cor_file 
          FROM students s
          LEFT JOIN student_registrations sr ON s.id = sr.id
          WHERE (s.semester IS NULL OR s.semester = '') 
          AND sr.cor_file IS NOT NULL 
          AND sr.cor_file != ''
          AND sr.approval_status = 'approved'";
$stmt = $conn->prepare($query);
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Found " . count($students) . " students without semester data.\n\n";

$updated = 0;
$failed = 0;

foreach ($students as $student) {
    $studentId = $student['id'];
    $corFile = $student['cor_file'];
    $corPath = __DIR__ . '/../' . $corFile;
    
    if (!file_exists($corPath)) {
        echo "COR file not found for student $studentId: $corPath\n";
        $failed++;
        continue;
    }
    
    echo "Processing student $studentId...\n";
    
    $extractedInfo = extractInformationFromCOR($corPath);
    
    if ($extractedInfo['success'] && isset($extractedInfo['data'])) {
        $academicYear = $extractedInfo['data']['academicYear'] ?? '';
        $semester = $extractedInfo['data']['semester'] ?? '';
        
        if (!empty($semester) || !empty($academicYear)) {
            $updateQuery = "UPDATE students SET ";
            $params = [];
            $updates = [];
            
            if (!empty($semester)) {
                $updates[] = "semester = ?";
                $params[] = $semester;
            }
            
            if (!empty($academicYear)) {
                $updates[] = "academic_year = ?";
                $params[] = $academicYear;
            }
            
            if (!empty($updates)) {
                $updateQuery .= implode(', ', $updates) . " WHERE id = ?";
                $params[] = $studentId;
                
                $updateStmt = $conn->prepare($updateQuery);
                if ($updateStmt->execute($params)) {
                    echo "  ✓ Updated: ";
                    if (!empty($semester)) echo "Semester = $semester ";
                    if (!empty($academicYear)) echo "Academic Year = $academicYear ";
                    echo "\n";
                    $updated++;
                } else {
                    echo "  ✗ Failed to update database\n";
                    $failed++;
                }
            } else {
                echo "  ✗ No semester or academic year found in COR\n";
                $failed++;
            }
        } else {
            echo "  ✗ Could not extract semester or academic year from COR\n";
            $failed++;
        }
    } else {
        echo "  ✗ Extraction failed: " . ($extractedInfo['message'] ?? 'Unknown error') . "\n";
        $failed++;
    }
    
    echo "\n";
}

echo "\n=== Summary ===\n";
echo "Updated: $updated\n";
echo "Failed: $failed\n";
echo "Total: " . count($students) . "\n";

