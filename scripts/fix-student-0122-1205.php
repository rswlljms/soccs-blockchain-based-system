<?php
require_once __DIR__ . '/../includes/database.php';

$database = new Database();
$conn = $database->getConnection();

$studentId = '0122-1205';
$semester = 'First (1st) Semester';

$stmt = $conn->prepare('UPDATE students SET semester = ? WHERE id = ?');
if ($stmt->execute([$semester, $studentId])) {
    echo "✓ Successfully updated student {$studentId} semester to: {$semester}\n";
    
    $check = $conn->prepare('SELECT semester FROM students WHERE id = ?');
    $check->execute([$studentId]);
    $result = $check->fetch(PDO::FETCH_ASSOC);
    echo "Verified: Semester is now: " . ($result['semester'] ?: 'NULL') . "\n";
} else {
    echo "✗ Failed to update\n";
}

