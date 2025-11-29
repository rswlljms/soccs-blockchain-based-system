<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    
    // Get recent transactions (both income and expenses)
    $query = "
        (SELECT 
            name,
            amount * -1 as amount,
            'expense' as type,
            date as transaction_date,
            transaction_hash as hash,
            created_at
        FROM expenses 
        ORDER BY date DESC 
        LIMIT 10)
        
        UNION ALL
        
        (SELECT 
            CONCAT(source, ' - ', description) as name,
            amount,
            'income' as type,
            date_received as transaction_date,
            transaction_hash as hash,
            created_at
        FROM funds 
        ORDER BY date_received DESC 
        LIMIT 10)
        
        ORDER BY transaction_date DESC 
        LIMIT :limit
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    $transactions = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $transactions[] = [
            'name' => $row['name'],
            'amount' => (float) $row['amount'],
            'type' => $row['type'],
            'date' => $row['transaction_date'],
            'hash' => $row['hash'] ?? '0x' . substr(md5($row['name'] . $row['transaction_date']), 0, 8) . '...',
            'created_at' => $row['created_at']
        ];
    }
    
    echo json_encode([
        'status' => 'success',
        'data' => $transactions
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unable to fetch recent transactions',
        'error' => $e->getMessage()
    ]);
}
?>
