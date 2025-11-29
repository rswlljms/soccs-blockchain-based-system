<?php
header('Content-Type: application/json');
require_once '../../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $partylist = trim($_POST['partylist'] ?? '');
    $positionId = intval($_POST['position_id'] ?? 0);
    $platform = trim($_POST['platform'] ?? '');
    
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
    
    $checkStmt = $pdo->prepare("SELECT id FROM positions WHERE id = ?");
    $checkStmt->execute([$positionId]);
    if (!$checkStmt->fetch()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Position does not exist']);
        exit;
    }
    
    $photoPath = null;
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
            $photoPath = '../uploads/candidates/' . $filename;
        }
    }
    
    $stmt = $pdo->prepare(
        "INSERT INTO candidates (firstname, lastname, partylist, position_id, platform, photo) 
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([$firstname, $lastname, $partylist, $positionId, $platform, $photoPath]);
    
    $newId = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'data' => [
            'id' => $newId,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'partylist' => $partylist,
            'position_id' => $positionId,
            'platform' => $platform,
            'photo' => $photoPath ?? '../assets/img/logo.png'
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Candidate create error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to create candidate']);
}

