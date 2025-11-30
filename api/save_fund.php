<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once '../includes/database.php';
require_once '../includes/activity_logger.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $date = isset($_POST['date']) ? $_POST['date'] : date('Y-m-d');
    $transactionHash = isset($_POST['transaction_hash']) ? $_POST['transaction_hash'] : null;

    if ($amount <= 0 || empty($description)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Amount and description are required']);
        exit;
    }

    $database = new Database();
    $conn = $database->getConnection();

    $query = "INSERT INTO funds (source, amount, description, date_received, transaction_hash) 
              VALUES (:source, :amount, :description, :date_received, :transaction_hash)";
    
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':source', 'Manual Entry', PDO::PARAM_STR);
    $stmt->bindValue(':amount', $amount, PDO::PARAM_STR);
    $stmt->bindValue(':description', $description, PDO::PARAM_STR);
    $stmt->bindValue(':date_received', $date, PDO::PARAM_STR);
    $stmt->bindValue(':transaction_hash', $transactionHash, PDO::PARAM_STR);
    $stmt->execute();

    $fundId = $conn->lastInsertId();

    if (isset($_SESSION['user_id'])) {
        logFundActivity($_SESSION['user_id'], 'create', 'Created fund: ' . $description . ' - Amount: ₱' . number_format($amount, 2));
    }

    echo json_encode([
        'success' => true,
        'message' => 'Fund recorded successfully',
        'id' => $fundId
    ]);

} catch (Exception $e) {
    error_log("Save fund error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to save fund'
    ]);
}

