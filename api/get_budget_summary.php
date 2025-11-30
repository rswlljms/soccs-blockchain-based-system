<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $totalBudget = 0;
    $totalRecords = 0;
    $thisMonthTotal = 0;
    
    $totalBudgetQuery = "SELECT COALESCE(SUM(amount), 0) as total_budget, COUNT(*) as total_records FROM funds";
    $totalBudgetStmt = $conn->prepare($totalBudgetQuery);
    $totalBudgetStmt->execute();
    $totalBudgetResult = $totalBudgetStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($totalBudgetResult) {
        $totalBudget = (float) $totalBudgetResult['total_budget'];
        $totalRecords = (int) $totalBudgetResult['total_records'];
    }
    
    $thisMonthQuery = "SELECT COALESCE(SUM(amount), 0) as monthly_total 
                        FROM funds 
                        WHERE YEAR(date_received) = YEAR(CURRENT_DATE) 
                        AND MONTH(date_received) = MONTH(CURRENT_DATE)";
    $thisMonthStmt = $conn->prepare($thisMonthQuery);
    $thisMonthStmt->execute();
    $thisMonthResult = $thisMonthStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($thisMonthResult) {
        $thisMonthTotal = (float) $thisMonthResult['monthly_total'];
    }
    
    $response = [
        'success' => true,
        'data' => [
            'total_amount' => $totalBudget,
            'total_count' => $totalRecords,
            'monthly_total' => $thisMonthTotal
        ]
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Unable to fetch budget summary',
        'error' => $e->getMessage()
    ]);
}
?>

