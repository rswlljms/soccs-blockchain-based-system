<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/database.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    $search = $_GET['search'] ?? '';
    $course = $_GET['course'] ?? '';
    $year = $_GET['year'] ?? '';
    $section = $_GET['section'] ?? '';
    $status = $_GET['status'] ?? '';
    $showArchived = $_GET['show_archived'] ?? 'false';
    
    $whereConditions = [];
    $params = [];
    
    // Build WHERE clause
    if (!empty($search)) {
        $whereConditions[] = "(CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) LIKE ?)";
        $params[] = '%' . $search . '%';
    }
    
    if (!empty($course) && $course !== 'All') {
        $whereConditions[] = 'course = ?';
        $params[] = $course;
    }
    
    if (!empty($year) && $year !== 'All') {
        $whereConditions[] = 'year_level = ?';
        $params[] = $year;
    }
    
    if (!empty($section)) {
        $whereConditions[] = 'section = ?';
        $params[] = strtoupper($section);
    }
    
    if (!empty($status) && $status !== 'All') {
        $whereConditions[] = 'membership_fee_status = ?';
        $params[] = $status;
    }
    
    if ($showArchived === 'true') {
        $whereConditions[] = 'is_archived = 1';
    } else {
        $whereConditions[] = 'is_archived = 0';
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    $sql = "
        SELECT 
            id,
            CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) as full_name,
            first_name,
            middle_name,
            last_name,
            course,
            year_level,
            section,
            COALESCE(membership_fee_status, 'unpaid') as payment_status,
            membership_fee_receipt as receipt_file,
            membership_control_number,
            membership_fee_paid_at,
            is_archived,
            created_at
        FROM students 
        $whereClause
        ORDER BY created_at DESC
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get summary statistics
    // Summary for student management
    $summaryActiveSql = "
        SELECT 
            COUNT(*) as total_students
        FROM students 
        WHERE is_archived = 0
    ";
    $summaryActiveStmt = $pdo->prepare($summaryActiveSql);
    $summaryActiveStmt->execute();
    $summaryActive = $summaryActiveStmt->fetch(PDO::FETCH_ASSOC);

    $summaryArchivedSql = "
        SELECT COUNT(*) as archived_students
        FROM students 
        WHERE is_archived = 1
    ";
    $summaryArchivedStmt = $pdo->prepare($summaryArchivedSql);
    $summaryArchivedStmt->execute();
    $summaryArchived = $summaryArchivedStmt->fetch(PDO::FETCH_ASSOC);

    // Get membership fee statistics
    $membershipStatsSql = "
        SELECT 
            COUNT(CASE WHEN membership_fee_status = 'paid' THEN 1 END) as paid_students,
            SUM(CASE WHEN membership_fee_status = 'paid' THEN 250.00 ELSE 0 END) as total_collected
        FROM students 
        WHERE is_archived = 0
    ";
    $membershipStatsStmt = $pdo->prepare($membershipStatsSql);
    $membershipStatsStmt->execute();
    $membershipStats = $membershipStatsStmt->fetch(PDO::FETCH_ASSOC);

    $summary = [
        'total_students' => (int)($summaryActive['total_students'] ?? 0),
        'archived_students' => (int)($summaryArchived['archived_students'] ?? 0),
        'paid_students' => (int)($membershipStats['paid_students'] ?? 0),
        'total_collected' => (float)($membershipStats['total_collected'] ?? 0)
    ];
    
    echo json_encode([
        'success' => true,
        'students' => $students,
        'summary' => $summary
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}