<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../includes/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $status = isset($_GET['status']) ? $_GET['status'] : 'all';
    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
    $dateFilter = isset($_GET['date']) ? $_GET['date'] : '';
    
    $query = "SELECT 
                id,
                title,
                description,
                date,
                location,
                category,
                status,
                created_by,
                created_at
              FROM events 
              WHERE 1=1";
    
    $params = [];
    
    if ($status !== 'all') {
        $query .= " AND status = :status";
        $params[':status'] = $status;
    }
    
    if ($searchTerm !== '') {
        $query .= " AND (title LIKE :search OR description LIKE :search OR location LIKE :search)";
        $params[':search'] = "%$searchTerm%";
    }
    
    if ($dateFilter !== '') {
        $query .= " AND DATE(date) = :date";
        $params[':date'] = $dateFilter;
    }
    
    $query .= " ORDER BY date DESC";
    
    $stmt = $conn->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    
    $events = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $eventDate = new DateTime($row['date']);
        
        $events[] = [
            'id' => (int)$row['id'],
            'name' => $row['title'],
            'date' => $eventDate->format('Y-m-d'),
            'time' => $eventDate->format('H:i'),
            'location' => $row['location'],
            'description' => $row['description'],
            'category' => $row['category'],
            'status' => $row['status'],
            'created_by' => $row['created_by'],
            'created_at' => $row['created_at']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $events
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

