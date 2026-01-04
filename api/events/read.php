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
    $categoryFilter = isset($_GET['category']) ? $_GET['category'] : 'all';
    
    $query = "SELECT 
                e.id,
                e.title,
                e.description,
                e.date,
                e.end_date,
                e.is_multi_day,
                e.location,
                e.category,
                e.status,
                e.created_by,
                e.created_at
              FROM events e
              WHERE 1=1";
    
    $params = [];
    
    if ($status !== 'all') {
        $query .= " AND status = :status";
        $params[':status'] = $status;
    }
    
    if ($categoryFilter !== 'all') {
        $query .= " AND category = :category";
        $params[':category'] = $categoryFilter;
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
        $eventId = (int)$row['id'];
        
        // Fetch contests for this event
        $contestsQuery = "SELECT id, contest_details, registration_link FROM event_contests WHERE event_id = :event_id ORDER BY id ASC";
        $contestsStmt = $conn->prepare($contestsQuery);
        $contestsStmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
        $contestsStmt->execute();
        
        $contests = [];
        while ($contestRow = $contestsStmt->fetch(PDO::FETCH_ASSOC)) {
            $contests[] = [
                'id' => (int)$contestRow['id'],
                'contest_details' => $contestRow['contest_details'],
                'registration_link' => $contestRow['registration_link']
            ];
        }
        
        $event = [
            'id' => $eventId,
            'name' => $row['title'],
            'date' => $eventDate->format('Y-m-d'),
            'time' => $eventDate->format('H:i'),
            'location' => $row['location'],
            'description' => $row['description'],
            'category' => $row['category'],
            'status' => $row['status'],
            'contests' => $contests,
            'created_by' => $row['created_by'],
            'created_at' => $row['created_at'],
            'is_multi_day' => (bool)$row['is_multi_day']
        ];
        
        if ($row['is_multi_day'] && $row['end_date']) {
            $endDate = new DateTime($row['end_date']);
            $event['end_date'] = $endDate->format('Y-m-d');
            $event['end_time'] = $endDate->format('H:i');
        } else {
            $event['end_date'] = null;
            $event['end_time'] = null;
        }
        
        $events[] = $event;
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

