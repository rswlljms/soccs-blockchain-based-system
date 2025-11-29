<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once '../includes/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;
    $offset = ($page - 1) * $limit;
    
    $whereClause = '';
    $params = [];
    
    if (isset($_GET['date_filter']) && $_GET['date_filter'] !== 'All') {
        $dateFilter = $_GET['date_filter'];
        switch ($dateFilter) {
            case 'Today':
                $whereClause = "WHERE DATE(date_received) = CURDATE()";
                break;
            case 'Week':
                $whereClause = "WHERE date_received >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                break;
            case 'Month':
                $whereClause = "WHERE date_received >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                break;
            case 'Year':
                $whereClause = "WHERE YEAR(date_received) = YEAR(NOW())";
                break;
        }
    }
    
    $query = "SELECT SQL_CALC_FOUND_ROWS 
                id, 
                source, 
                amount, 
                description, 
                date_received as date,
                date_received,
                created_at,
                transaction_hash 
              FROM funds 
              $whereClause 
              ORDER BY date_received DESC, created_at DESC 
              LIMIT :limit OFFSET :offset";
    
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $funds = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $totalStmt = $conn->query("SELECT FOUND_ROWS()");
    $total = (int)$totalStmt->fetchColumn();
    
    echo json_encode([
        'success' => true,
        'data' => $funds,
        'total' => $total,
        'page' => $page,
        'limit' => $limit
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Unable to fetch funds',
        'error' => $e->getMessage()
    ]);
} 