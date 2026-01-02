<?php
session_start();
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/auth.php';

if (!Auth::isAuthenticated() || !Auth::hasRole('admin')) {
    header('Location: ../auth/login.php');
    exit;
}

$database = new Database();
$conn = $database->getConnection();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $studentId = $_POST['student_id'] ?? '';
    $academicYear = trim($_POST['academic_year'] ?? '');
    $semester = trim($_POST['semester'] ?? '');
    
    if (!empty($studentId)) {
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
        
        if (!empty($updates)) {
            $params[] = $studentId;
            $query = "UPDATE students SET " . implode(', ', $updates) . " WHERE id = ?";
            $stmt = $conn->prepare($query);
            
            if ($stmt->execute($params)) {
                $message = "Successfully updated student {$studentId}";
                $messageType = 'success';
            } else {
                $message = "Failed to update student {$studentId}";
                $messageType = 'error';
            }
        }
    }
}

$query = "SELECT id, first_name, last_name, academic_year, semester, cor_file 
          FROM students 
          WHERE (semester IS NULL OR semester = '' OR academic_year IS NULL OR academic_year = '')
          ORDER BY id DESC 
          LIMIT 50";
$stmt = $conn->prepare($query);
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fix Semester - Admin | SOCCS</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; margin-bottom: 20px; }
        .message { padding: 12px; margin-bottom: 20px; border-radius: 4px; }
        .message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: 600; }
        tr:hover { background: #f8f9fa; }
        input[type="text"] { padding: 8px; width: 100%; border: 1px solid #ddd; border-radius: 4px; }
        button { padding: 8px 16px; background: #9333ea; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #7e22ce; }
        .form-row { display: flex; gap: 10px; align-items: center; }
        .form-row input { flex: 1; }
        .form-row button { flex: 0 0 auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Fix Semester for Students</h1>
        
        <?php if ($message): ?>
            <div class="message <?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Academic Year</th>
                    <th>Semester</th>
                    <th>COR File</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px;">No students found with missing semester/academic year.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= htmlspecialchars($student['id']) ?></td>
                            <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                            <td><?= htmlspecialchars($student['academic_year'] ?: 'NULL') ?></td>
                            <td><?= htmlspecialchars($student['semester'] ?: 'NULL') ?></td>
                            <td><?= htmlspecialchars($student['cor_file'] ?: 'N/A') ?></td>
                            <td>
                                <form method="POST" style="margin: 0;">
                                    <input type="hidden" name="student_id" value="<?= htmlspecialchars($student['id']) ?>">
                                    <div class="form-row">
                                        <input type="text" name="academic_year" placeholder="e.g., 2025-2026" value="<?= htmlspecialchars($student['academic_year'] ?: '') ?>">
                                        <input type="text" name="semester" placeholder="e.g., First (1st) Semester" value="<?= htmlspecialchars($student['semester'] ?: '') ?>">
                                        <button type="submit" name="update">Update</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

