<?php
require_once __DIR__ . '/../includes/database.php';

$database = new Database();
$conn = $database->getConnection();

echo "=== DIRECT SEMESTER FIX ===\n\n";
echo "This will update semester for students based on their COR files.\n";
echo "Enter student ID to fix (or 'all' for all students): ";
$handle = fopen("php://stdin", "r");
$input = trim(fgets($handle));
fclose($handle);

if ($input === 'all') {
    $query = "SELECT id, cor_file FROM students WHERE (semester IS NULL OR semester = '') AND cor_file IS NOT NULL";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $query = "SELECT id, cor_file FROM students WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$input]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (empty($students)) {
    echo "No students found.\n";
    exit;
}

echo "\nFound " . count($students) . " student(s).\n";
echo "For each student, enter:\n";
echo "  - Academic Year (e.g., 2025-2026) or press Enter to skip\n";
echo "  - Semester (e.g., First (1st) Semester) or press Enter to skip\n\n";

foreach ($students as $student) {
    echo "Student: " . $student['id'] . "\n";
    echo "COR File: " . ($student['cor_file'] ?: 'N/A') . "\n";
    
    echo "Academic Year: ";
    $handle = fopen("php://stdin", "r");
    $academicYear = trim(fgets($handle));
    fclose($handle);
    
    echo "Semester: ";
    $handle = fopen("php://stdin", "r");
    $semester = trim(fgets($handle));
    fclose($handle);
    
    if (!empty($academicYear) || !empty($semester)) {
        $updates = [];
        $params = [];
        
        if (!empty($academicYear)) {
            $updates[] = "academic_year = ?";
            $params[] = $academicYear;
        }
        
        if (!empty($semester)) {
            $updates[] = "semester = ?";
            $params[] = $semester;
        }
        
        $params[] = $student['id'];
        
        $updateQuery = "UPDATE students SET " . implode(', ', $updates) . " WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        
        if ($updateStmt->execute($params)) {
            echo "✓ Updated successfully!\n\n";
        } else {
            echo "✗ Update failed!\n\n";
        }
    } else {
        echo "Skipped (no values entered)\n\n";
    }
}

echo "Done!\n";

