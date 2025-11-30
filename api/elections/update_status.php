<?php
session_start();
header('Content-Type: application/json');
require_once '../../includes/database.php';
require_once '../../includes/activity_logger.php';

$database = new Database();
$conn = $database->getConnection();

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id']) || !isset($data['status'])) {
    echo json_encode([
        'success' => false,
        'error' => 'ID and status are required'
    ]);
    exit;
}

$id = (int)$data['id'];
$status = $data['status'];

$validStatuses = ['upcoming', 'active', 'completed', 'cancelled'];
if (!in_array($status, $validStatuses)) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid status value'
    ]);
    exit;
}

try {
    $getElectionQuery = "SELECT title FROM elections WHERE id = :id";
    $getElectionStmt = $conn->prepare($getElectionQuery);
    $getElectionStmt->bindParam(':id', $id);
    $getElectionStmt->execute();
    $election = $getElectionStmt->fetch(PDO::FETCH_ASSOC);
    $electionTitle = $election['title'] ?? 'Election #' . $id;
    
    $transactionHash = null;
    
    if ($status === 'completed') {
        $totalVotesQuery = "SELECT COUNT(*) as total FROM votes WHERE election_id = :id";
        $totalVotesStmt = $conn->prepare($totalVotesQuery);
        $totalVotesStmt->bindParam(':id', $id);
        $totalVotesStmt->execute();
        $totalVotesResult = $totalVotesStmt->fetch(PDO::FETCH_ASSOC);
        $totalVotes = (int)($totalVotesResult['total'] ?? 0);
        
        try {
            $blockchainUrl = 'http://localhost:3001/confirm-election';
            $electionData = [
                'electionId' => $id,
                'electionTitle' => $electionTitle,
                'totalVotes' => $totalVotes,
                'method' => 'ELECTION_CONFIRMED'
            ];
            
            $ch = curl_init($blockchainUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($electionData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
            
            $blockchainResponse = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200) {
                $blockchainResult = json_decode($blockchainResponse, true);
                if (isset($blockchainResult['status']) && $blockchainResult['status'] === 'success') {
                    $transactionHash = $blockchainResult['txHash'];
                }
            }
        } catch (Exception $e) {
            error_log("Blockchain election confirmation error: " . $e->getMessage());
        }
    }
    
    $query = "UPDATE elections SET status = :status";
    if ($status === 'completed') {
        $query .= ", transaction_hash = :transaction_hash";
    }
    $query .= " WHERE id = :id";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':status', $status);
    if ($status === 'completed') {
        $stmt->bindParam(':transaction_hash', $transactionHash);
    }
    
    if ($stmt->execute()) {
        
        if (isset($_SESSION['user_id'])) {
            $action = ($status === 'active') ? 'start' : (($status === 'completed') ? 'end' : 'update');
            $description = ucfirst($action) . ' election: ' . $electionTitle . ' (ID: ' . $id . ')';
            if ($transactionHash) {
                $description .= ' - Blockchain TX: ' . $transactionHash;
            }
            logElectionActivity($_SESSION['user_id'], $action, $description);
        }
        
        $response = [
            'success' => true,
            'message' => 'Election status updated successfully'
        ];
        
        if ($transactionHash) {
            $response['transaction_hash'] = $transactionHash;
        }
        
        echo json_encode($response);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to update election status'
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}

