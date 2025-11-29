<?php
require_once '../includes/expense_operations.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

error_log("Received request to get_expenses.php");

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;
$category = isset($_GET['category']) ? $_GET['category'] : null;

try {
    $expenseOps = new ExpenseOperations();
    $result = $expenseOps->getExpenses($page, $limit, $category);
    $summary = $expenseOps->getExpensesSummary($category);

    error_log("Retrieved expenses: " . count($result['expenses']));

    echo json_encode([
        'success' => true,
        'data' => $result['expenses'],
        'total' => $result['total'],
        'summary' => $summary,
        'page' => $page,
        'limit' => $limit
    ]);
} catch (Exception $e) {
    error_log("Error in get_expenses.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 