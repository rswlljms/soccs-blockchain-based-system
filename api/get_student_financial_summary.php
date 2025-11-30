<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Get total funds (all time)
    $fundsQuery = "SELECT COALESCE(SUM(amount), 0) as total_funds FROM funds";
    $fundsStmt = $conn->prepare($fundsQuery);
    $fundsStmt->execute();
    $fundsResult = $fundsStmt->fetch(PDO::FETCH_ASSOC);
    $totalFunds = (float) $fundsResult['total_funds'];
    
    // Get total funds up to end of last month (cumulative)
    $fundsLastMonthQuery = "SELECT COALESCE(SUM(amount), 0) as total_funds FROM funds 
                            WHERE date_received < DATE_FORMAT(NOW(), '%Y-%m-01')";
    $fundsLastMonthStmt = $conn->prepare($fundsLastMonthQuery);
    $fundsLastMonthStmt->execute();
    $fundsLastMonthResult = $fundsLastMonthStmt->fetch(PDO::FETCH_ASSOC);
    $totalFundsLastMonth = (float) $fundsLastMonthResult['total_funds'];
    
    // Calculate funds percentage change (comparing current total vs last month's total)
    $fundsChange = null;
    if ($totalFundsLastMonth > 0) {
        $fundsChange = (($totalFunds - $totalFundsLastMonth) / $totalFundsLastMonth) * 100;
    } else if ($totalFunds > 0 && $totalFundsLastMonth == 0) {
        $fundsChange = 100;
    }
    
    // Get total expenses (all time)
    $expensesQuery = "SELECT COALESCE(SUM(amount), 0) as total_expenses FROM expenses";
    $expensesStmt = $conn->prepare($expensesQuery);
    $expensesStmt->execute();
    $expensesResult = $expensesStmt->fetch(PDO::FETCH_ASSOC);
    $totalExpenses = (float) $expensesResult['total_expenses'];
    
    // Get total expenses up to end of last month (cumulative)
    $expensesLastMonthQuery = "SELECT COALESCE(SUM(amount), 0) as total_expenses FROM expenses 
                               WHERE date < DATE_FORMAT(NOW(), '%Y-%m-01')";
    $expensesLastMonthStmt = $conn->prepare($expensesLastMonthQuery);
    $expensesLastMonthStmt->execute();
    $expensesLastMonthResult = $expensesLastMonthStmt->fetch(PDO::FETCH_ASSOC);
    $totalExpensesLastMonth = (float) $expensesLastMonthResult['total_expenses'];
    
    // Calculate expenses percentage change
    $expensesChange = null;
    if ($totalExpensesLastMonth > 0) {
        $expensesChange = (($totalExpenses - $totalExpensesLastMonth) / $totalExpensesLastMonth) * 100;
    } else if ($totalExpenses > 0 && $totalExpensesLastMonth == 0) {
        $expensesChange = 100;
    }
    
    // Calculate available balance
    $availableBalance = $totalFunds - $totalExpenses;
    
    // Get balance from last month
    $balanceLastMonth = $totalFundsLastMonth - $totalExpensesLastMonth;
    
    // Calculate balance percentage change
    $balanceChange = null;
    if ($balanceLastMonth != 0) {
        $balanceChange = (($availableBalance - $balanceLastMonth) / abs($balanceLastMonth)) * 100;
    }
    
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
            'fundsChange' => $fundsChange !== null ? round($fundsChange, 1) : null,
            'expensesChange' => $expensesChange !== null ? round($expensesChange, 1) : null,
            'balanceChange' => $balanceChange !== null ? round($balanceChange, 1) : null,
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
