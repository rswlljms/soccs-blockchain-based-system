    <?php
session_start();
header('Content-Type: application/json');
require_once '../../includes/database.php';
require_once '../../includes/auth_check.php';

if (!isAdviser() && !isDean()) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'error' => 'Access denied. Only Adviser and Dean can view activity logs.'
    ]);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? max(1, min(100, intval($_GET['limit']))) : 20;
    $offset = ($page - 1) * $limit;
    
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $activityType = isset($_GET['activity_type']) ? trim($_GET['activity_type']) : '';
    $module = isset($_GET['module']) ? trim($_GET['module']) : '';
    $userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
    $dateFilter = isset($_GET['date_filter']) ? trim($_GET['date_filter']) : '';

    $whereConditions = [];
    $params = [];

    if (!empty($search)) {
        $whereConditions[] = "(al.activity_description LIKE :search OR u.first_name LIKE :search OR u.last_name LIKE :search OR u.email LIKE :search)";
        $params[':search'] = "%$search%";
    }

    if (!empty($activityType)) {
        $whereConditions[] = "al.activity_type = :activity_type";
        $params[':activity_type'] = $activityType;
    }

    if (!empty($module)) {
        $whereConditions[] = "al.module = :module";
        $params[':module'] = $module;
    }

    if ($userId > 0) {
        $whereConditions[] = "al.user_id = :user_id";
        $params[':user_id'] = $userId;
    }

    if (!empty($dateFilter) && $dateFilter !== 'All') {
        switch ($dateFilter) {
            case 'Today':
                $whereConditions[] = "DATE(al.created_at) = CURDATE()";
                break;
            case 'Week':
                $whereConditions[] = "al.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                break;
            case 'Month':
                $whereConditions[] = "al.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                break;
            case 'Year':
                $whereConditions[] = "YEAR(al.created_at) = YEAR(NOW())";
                break;
        }
    }

    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

    $countQuery = "SELECT COUNT(*) as total 
                   FROM activity_logs al
                   INNER JOIN users u ON al.user_id = u.id
                   $whereClause";
    
    $countStmt = $conn->prepare($countQuery);
    foreach ($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }
    $countStmt->execute();
    $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalRecords / $limit);

    $query = "SELECT 
                al.id,
                al.user_id,
                al.activity_type,
                al.activity_description,
                al.module,
                al.created_at,
                u.first_name,
                u.last_name,
                u.email,
                u.role
              FROM activity_logs al
              INNER JOIN users u ON al.user_id = u.id
              $whereClause
              ORDER BY al.created_at DESC
              LIMIT :limit OFFSET :offset";

    $stmt = $conn->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $activityTypesQuery = "SELECT DISTINCT activity_type FROM activity_logs ORDER BY activity_type";
    $activityTypesStmt = $conn->prepare($activityTypesQuery);
    $activityTypesStmt->execute();
    $activityTypes = array_column($activityTypesStmt->fetchAll(PDO::FETCH_ASSOC), 'activity_type');

    $modulesQuery = "SELECT DISTINCT module FROM activity_logs WHERE module IS NOT NULL ORDER BY module";
    $modulesStmt = $conn->prepare($modulesQuery);
    $modulesStmt->execute();
    $modules = array_column($modulesStmt->fetchAll(PDO::FETCH_ASSOC), 'module');

    echo json_encode([
        'success' => true,
        'data' => $logs,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_records' => $totalRecords,
            'per_page' => $limit
        ],
        'filters' => [
            'activity_types' => $activityTypes,
            'modules' => $modules
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch activity logs: ' . $e->getMessage()
    ]);
}

