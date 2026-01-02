<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

session_start();

// Check if user is logged in
if (!isset($_SESSION['student'])) {
    http_response_code(401);
    echo json_encode(['error' => 'User not authenticated']);
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    require_once '../includes/database.php';
    
    $database = new Database();
    $db = $database->getConnection();
    
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        $data = $_POST;
    }

    $student = $_SESSION['student'];
    $studentId = $student['id'];
    
    if (empty($data)) {
        throw new Exception('No vote data provided');
    }

    // Get active election within time period
    $stmt = $db->prepare("SELECT id, end_date FROM elections WHERE status = 'active' AND NOW() BETWEEN start_date AND end_date LIMIT 1");
    $stmt->execute();
    $election = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$election) {
        throw new Exception('No active election found or election has ended');
    }
    
    $electionId = $election['id'];
    
    // Check if student already voted in this election (has at least one vote recorded)
    $stmt = $db->prepare("SELECT COUNT(DISTINCT position_id) as positions_voted FROM votes WHERE election_id = ? AND voter_id = ?");
    $stmt->execute([$electionId, $studentId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get total positions count
    $totalPositionsStmt = $db->prepare("SELECT COUNT(*) as total FROM positions");
    $totalPositionsStmt->execute();
    $totalPositions = $totalPositionsStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // If student has voted for all positions, they have completed voting
    if ($result['positions_voted'] > 0) {
        throw new Exception('You have already voted in this election');
    }

    $votes = [];
    $positionVoteCounts = [];
    $db->beginTransaction();
    
    $posStmt = $db->prepare("SELECT id, max_votes FROM positions");
    $posStmt->execute();
    $positionsMaxVotes = [];
    while ($row = $posStmt->fetch(PDO::FETCH_ASSOC)) {
        $positionsMaxVotes[$row['id']] = (int)$row['max_votes'];
    }
    
    foreach ($data as $key => $candidateId) {
        if (empty($candidateId)) continue;
        
        $stmt = $db->prepare("SELECT position_id FROM candidates WHERE id = ?");
        $stmt->execute([$candidateId]);
        $candidate = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($candidate) {
            $positionId = $candidate['position_id'];
            
            if (!isset($positionVoteCounts[$positionId])) {
                $positionVoteCounts[$positionId] = 0;
            }
            $positionVoteCounts[$positionId]++;
            
            $maxVotes = $positionsMaxVotes[$positionId] ?? 1;
            if ($positionVoteCounts[$positionId] > $maxVotes) {
                $db->rollBack();
                throw new Exception("Maximum votes exceeded for this position (max: {$maxVotes})");
            }
            
            $checkStmt = $db->prepare("SELECT id FROM votes WHERE election_id = ? AND voter_id = ? AND candidate_id = ?");
            $checkStmt->execute([$electionId, $studentId, $candidateId]);
            if ($checkStmt->fetch()) {
                continue;
            }
            
            $stmt = $db->prepare("INSERT INTO votes (election_id, voter_id, candidate_id, position_id, vote_hash, voted_at) VALUES (?, ?, ?, ?, NULL, NOW())");
            $stmt->execute([$electionId, $studentId, $candidateId, $positionId]);
            
            $voteId = $db->lastInsertId();
            
            $votes[] = [
                'vote_id' => $voteId,
                'candidate_id' => $candidateId,
                'position_id' => $positionId
            ];
        }
    }
    
    if (empty($votes)) {
        $db->rollBack();
        throw new Exception('No valid votes found');
    }

    $db->commit();
    
    require_once __DIR__ . '/../includes/app_config.php';
    $blockchainBaseUrl = AppConfig::get('BLOCKCHAIN_URL', 'http://localhost:3001');
    $blockchainUrl = rtrim($blockchainBaseUrl, '/') . '/add-batch-votes';
    $candidateIds = [];
    $positionIds = [];
    
    foreach ($votes as $vote) {
        $candidateIds[] = (int)$vote['candidate_id'];
        $positionIds[] = (int)$vote['position_id'];
    }
    
    $batchVoteData = [
        'electionId' => (int)$electionId,
        'voterId' => $studentId,
        'candidateIds' => $candidateIds,
        'positionIds' => $positionIds,
        'method' => 'BATCH_VOTE'
    ];
    
    $ch = curl_init($blockchainUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($batchVoteData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
    
    $blockchainResponse = @curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $transactionHash = null;
    
    if ($httpCode === 200 && $blockchainResponse) {
        $blockchainResult = json_decode($blockchainResponse, true);
        if (isset($blockchainResult['status']) && $blockchainResult['status'] === 'success') {
            $transactionHash = $blockchainResult['txHash'];
            
            $updateStmt = $db->prepare("UPDATE votes SET vote_hash = ? WHERE election_id = ? AND voter_id = ?");
            $updateStmt->execute([$transactionHash, $electionId, $studentId]);
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Vote successfully recorded',
        'data' => [
            'student_id' => $studentId,
            'election_id' => $electionId,
            'votes' => $votes,
            'transaction_hash' => $transactionHash,
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
