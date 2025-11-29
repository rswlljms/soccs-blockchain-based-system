<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/database.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    $course = $_GET['course'] ?? '';
    $year = $_GET['year'] ?? '';
    $section = $_GET['section'] ?? '';
    
    if (empty($section)) {
        echo json_encode([
            'success' => false,
            'error' => 'Section filter is required'
        ]);
        exit;
    }
    
    $whereConditions = ['is_archived = 0', 'section = ?'];
    $params = [strtoupper($section)];
    
    if (!empty($course) && $course !== 'All') {
        $whereConditions[] = 'course = ?';
        $params[] = $course;
    }
    
    if (!empty($year) && $year !== 'All') {
        $whereConditions[] = 'year_level = ?';
        $params[] = $year;
    }
    
    $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
    
    $sql = "
        SELECT 
            course,
            year_level,
            section,
            COUNT(*) as total_students,
            SUM(CASE WHEN membership_fee_status = 'paid' THEN 1 ELSE 0 END) as paid_count,
            SUM(CASE WHEN membership_fee_status = 'unpaid' THEN 1 ELSE 0 END) as unpaid_count
        FROM students 
        $whereClause
        GROUP BY course, year_level, section
        ORDER BY course, year_level, section
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $summary = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $detailSql = "
        SELECT 
            id,
            CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) as full_name,
            course,
            year_level,
            section,
            membership_fee_status,
            email
        FROM students 
        $whereClause
        ORDER BY membership_fee_status DESC, last_name, first_name
    ";
    
    $detailStmt = $pdo->prepare($detailSql);
    $detailStmt->execute($params);
    $students = $detailStmt->fetchAll(PDO::FETCH_ASSOC);
    
    $paidStudents = array_filter($students, function($s) {
        return $s['membership_fee_status'] === 'paid';
    });
    
    $unpaidStudents = array_filter($students, function($s) {
        return $s['membership_fee_status'] !== 'paid';
    });
    
    echo json_encode([
        'success' => true,
        'summary' => $summary,
        'students' => [
            'paid' => array_values($paidStudents),
            'unpaid' => array_values($unpaidStudents)
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

