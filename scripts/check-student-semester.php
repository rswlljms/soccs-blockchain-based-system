<?php
require_once __DIR__ . '/../includes/database.php';

$database = new Database();
$conn = $database->getConnection();

echo "=== CHECKING RECENT STUDENT REGISTRATIONS ===\n\n";

$query = "SELECT id, first_name, last_name, academic_year, semester, created_at 
          FROM students 
          ORDER BY created_at DESC 
          LIMIT 5";
$stmt = $conn->prepare($query);
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($students as $student) {
    echo "Student ID: " . $student['id'] . "\n";
    echo "Name: " . $student['first_name'] . " " . $student['last_name'] . "\n";
    echo "Academic Year: " . ($student['academic_year'] ?: 'NULL/EMPTY') . "\n";
    echo "Semester: " . ($student['semester'] ?: 'NULL/EMPTY') . "\n";
    echo "Created: " . $student['created_at'] . "\n";
    echo "---\n\n";
}

echo "\n=== CHECKING RECENT REGISTRATIONS ===\n\n";

$query2 = "SELECT id, first_name, last_name, academic_year, semester, approval_status, created_at 
          FROM student_registrations 
          ORDER BY created_at DESC 
          LIMIT 5";
$stmt2 = $conn->prepare($query2);
$stmt2->execute();
$registrations = $stmt2->fetchAll(PDO::FETCH_ASSOC);

foreach ($registrations as $reg) {
    echo "Student ID: " . $reg['id'] . "\n";
    echo "Name: " . $reg['first_name'] . " " . $reg['last_name'] . "\n";
    echo "Academic Year: " . ($reg['academic_year'] ?: 'NULL/EMPTY') . "\n";
    echo "Semester: " . ($reg['semester'] ?: 'NULL/EMPTY') . "\n";
    echo "Status: " . $reg['approval_status'] . "\n";
    echo "Created: " . $reg['created_at'] . "\n";
    echo "---\n\n";
}

