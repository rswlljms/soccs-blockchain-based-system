<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    
    $query = "SELECT 
                id,
                title,
                content,
                type,
                is_active,
                target_audience,
                created_at,
                CASE 
                    WHEN created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR) THEN 1
                    ELSE 0
                END as is_new
              FROM announcements 
              WHERE is_active = 1 
                AND (target_audience = 'all' OR target_audience = 'students')
              ORDER BY created_at DESC 
              LIMIT :limit";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    $announcements = [];
    $newCount = 0;
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $createdAt = new DateTime($row['created_at']);
        $now = new DateTime();
        $interval = $now->diff($createdAt);
        
        // Calculate time ago
        if ($interval->d > 0) {
            $timeAgo = $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
        } elseif ($interval->h > 0) {
            $timeAgo = $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
        } elseif ($interval->i > 0) {
            $timeAgo = $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
        } else {
            $timeAgo = 'Just now';
        }
        
        $isNew = (bool) $row['is_new'];
        if ($isNew) {
            $newCount++;
        }
        
        $announcements[] = [
            'id' => (int) $row['id'],
            'title' => $row['title'],
            'content' => $row['content'],
            'type' => $row['type'],
            'is_new' => $isNew,
            'target_audience' => $row['target_audience'],
            'time_ago' => $timeAgo,
            'created_at' => $row['created_at']
        ];
    }
    
    echo json_encode([
        'status' => 'success',
        'data' => $announcements,
        'new_count' => $newCount
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unable to fetch announcements',
        'error' => $e->getMessage()
    ]);
}
?>
