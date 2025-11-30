<?php
session_start();
require_once '../includes/expense_operations.php';
require_once '../includes/activity_logger.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

error_log("save_expense.php started");

try {
    // Log the raw POST data
    error_log("Raw POST data: " . file_get_contents('php://input'));
    error_log("POST array: " . print_r($_POST, true));
    error_log("FILES array: " . print_r($_FILES, true));

    // Check if we're receiving form data or JSON
    if (!empty($_POST)) {
        $data = $_POST;
    } else {
        // Try to get JSON data
        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);
    }

    error_log("Processed data: " . print_r($data, true));

    if (empty($data)) {
        throw new Exception("No data received");
    }

    $expenseOps = new ExpenseOperations();
    
    $expenseData = [
        'name' => $data['name'] ?? null,
        'amount' => $data['amount'] ?? null,
        'category' => $data['category'] ?? null,
        'description' => $data['description'] ?? null,
        'supplier' => $data['supplier'] ?? null,
        'date' => $data['date'] ?? date('Y-m-d'),
        'document' => null,
        'transaction_hash' => $data['transaction_hash'] ?? null
    ];

    // Validate required fields
    $requiredFields = ['name', 'amount', 'category', 'supplier'];
    foreach ($requiredFields as $field) {
        if (empty($expenseData[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Handle file upload
    if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/documents/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = uniqid() . '_' . basename($_FILES['document']['name']);
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['document']['tmp_name'], $targetPath)) {
            $expenseData['document'] = $fileName;
        }
    }

    if ($expenseOps->addExpense($expenseData)) {
        error_log("Expense saved successfully");
        
        if (isset($_SESSION['user_id'])) {
            logExpenseActivity($_SESSION['user_id'], 'create', 'Created expense: ₱' . number_format($expenseData['amount'], 2) . ' - ' . $expenseData['name'] . ' (' . $expenseData['category'] . ')');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Expense saved successfully',
            'data' => $expenseData
        ]);
    } else {
        throw new Exception("Failed to save expense");
    }
} catch (Exception $e) {
    error_log("Error in save_expense.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 