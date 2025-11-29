<?php
header('Content-Type: application/json');
require_once '../../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    $id = intval($_POST['id'] ?? 0);
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $partylist = trim($_POST['partylist'] ?? '');
    $positionId = intval($_POST['position_id'] ?? 0);
    $platform = trim($_POST['platform'] ?? '');
    
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid candidate ID']);
        exit;
    }
    
    if (empty($firstname) || empty($lastname) || empty($partylist) || empty($platform)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'All fields are required']);
        exit;
    }
    
    if ($positionId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid position']);
        exit;
    }
    
    $stmt = $pdo->prepare("SELECT photo FROM candidates WHERE id = ?");
    $stmt->execute([$id]);
    $currentCandidate = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$currentCandidate) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Candidate not found']);
        exit;
    }
    
    $photoPath = $currentCandidate['photo'];
    
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        $maxSize = 5 * 1024 * 1024;
        
        if (!in_array($_FILES['photo']['type'], $allowedTypes)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid file type']);
            exit;
        }
        
        if ($_FILES['photo']['size'] > $maxSize) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'File size exceeds 5MB']);
            exit;
        }
        
        $uploadDir = '../../uploads/candidates/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('candidate_') . '.' . $extension;
        $targetPath = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
            if ($photoPath && file_exists('../../' . $photoPath)) {
                unlink('../../' . $photoPath);
            }
            $photoPath = '../uploads/candidates/' . $filename;
        }
    }
    
    $updateStmt = $pdo->prepare(
        "UPDATE candidates 
         SET firstname = ?, lastname = ?, partylist = ?, position_id = ?, platform = ?, photo = ?
         WHERE id = ?"
    );
    $updateStmt->execute([$firstname, $lastname, $partylist, $positionId, $platform, $photoPath, $id]);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'id' => $id,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'partylist' => $partylist,
            'position_id' => $positionId,
            'platform' => $platform,
            'photo' => $photoPath ?? '../assets/img/logo.png'
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Candidate update error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to update candidate']);
}

