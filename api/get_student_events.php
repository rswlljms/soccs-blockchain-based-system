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
    $status = isset($_GET['status']) ? $_GET['status'] : 'upcoming';
    
    $query = "SELECT 
                id,
                title,
                description,
                date,
                location,
                category,
                status,
                created_at
              FROM events 
              WHERE status = :status 
                AND date >= NOW() 
              ORDER BY date ASC 
              LIMIT :limit";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    $events = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $eventDate = new DateTime($row['date']);
        
        $events[] = [
            'id' => (int) $row['id'],
            'title' => $row['title'],
            'description' => $row['description'],
            'date' => $row['date'],
            'location' => $row['location'],
            'category' => $row['category'],
            'status' => $row['status'],
            'formatted_date' => $eventDate->format('M d'),
            'formatted_time' => $eventDate->format('g:i A'),
            'day' => $eventDate->format('d'),
            'month' => $eventDate->format('M'),
            'created_at' => $row['created_at']
        ];
    }
    
    echo json_encode([
        'status' => 'success',
        'data' => $events
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unable to fetch events',
        'error' => $e->getMessage()
    ]);
}
?>
