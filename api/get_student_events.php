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
                end_date,
                is_multi_day,
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
        
        $event = [
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
            'created_at' => $row['created_at'],
            'is_multi_day' => (bool) $row['is_multi_day']
        ];
        
        if ($row['is_multi_day'] && $row['end_date']) {
            $endDate = new DateTime($row['end_date']);
            $event['end_date'] = $row['end_date'];
            $event['end_day'] = $endDate->format('d');
            $event['end_month'] = $endDate->format('M');
        }
        
        $events[] = $event;
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
