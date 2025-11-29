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
    
    // Check if student already voted in this election
    $stmt = $db->prepare("SELECT COUNT(*) as vote_count FROM votes WHERE election_id = ? AND voter_id = ?");
    $stmt->execute([$electionId, $studentId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['vote_count'] > 0) {
        throw new Exception('You have already voted in this election');
    }

    $votes = [];
    $db->beginTransaction();
    
    foreach ($data as $key => $candidateId) {
        if (empty($candidateId)) continue;
        
        // Get position info from candidate
        $stmt = $db->prepare("SELECT position_id FROM candidates WHERE id = ?");
        $stmt->execute([$candidateId]);
        $candidate = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($candidate) {
            // Insert vote (note: using voted_at instead of created_at)
            $stmt = $db->prepare("INSERT INTO votes (election_id, voter_id, candidate_id, position_id, voted_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$electionId, $studentId, $candidateId, $candidate['position_id']]);
            
            $votes[] = [
                'candidate_id' => $candidateId,
                'position_id' => $candidate['position_id']
            ];
        }
    }
    
    if (empty($votes)) {
        $db->rollBack();
        throw new Exception('No valid votes found');
    }

    $db->commit();
    
    // Generate mock blockchain hash for future integration
    $transactionHash = '0x' . bin2hex(random_bytes(32));

    echo json_encode([
        'success' => true,
        'message' => 'Vote successfully recorded',
        'data' => [
            'student_id' => $studentId,
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
