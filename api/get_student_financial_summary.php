<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Get total funds
    $fundsQuery = "SELECT COALESCE(SUM(amount), 0) as total_funds FROM funds";
    $fundsStmt = $conn->prepare($fundsQuery);
    $fundsStmt->execute();
    $fundsResult = $fundsStmt->fetch(PDO::FETCH_ASSOC);
    $totalFunds = (float) $fundsResult['total_funds'];
    
    // Get total expenses
    $expensesQuery = "SELECT COALESCE(SUM(amount), 0) as total_expenses FROM expenses";
    $expensesStmt = $conn->prepare($expensesQuery);
    $expensesStmt->execute();
    $expensesResult = $expensesStmt->fetch(PDO::FETCH_ASSOC);
    $totalExpenses = (float) $expensesResult['total_expenses'];
    
    // Calculate available balance
    $availableBalance = $totalFunds - $totalExpenses;
    
    // Get recent transactions count
    $recentQuery = "SELECT COUNT(*) as recent_count FROM (
        SELECT date as transaction_date FROM expenses WHERE date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        UNION ALL
        SELECT date_received as transaction_date FROM funds WHERE date_received >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ) as recent_transactions";
    $recentStmt = $conn->prepare($recentQuery);
    $recentStmt->execute();
    $recentResult = $recentStmt->fetch(PDO::FETCH_ASSOC);
    $recentTransactions = (int) $recentResult['recent_count'];
    
    $response = [
        'status' => 'success',
        'data' => [
            'totalFunds' => $totalFunds,
            'totalExpenses' => $totalExpenses,
            'availableBalance' => $availableBalance,
            'recentTransactions' => $recentTransactions,
            'lastUpdated' => date('Y-m-d H:i:s')
        ]
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unable to fetch financial summary',
        'error' => $e->getMessage()
    ]);
}
?>
