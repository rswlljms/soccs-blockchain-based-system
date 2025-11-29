<?php
require_once 'includes/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Check if table exists
    $stmt = $conn->query("SHOW TABLES LIKE 'student_registrations'");
    $tableExists = $stmt->rowCount() > 0;
    
    echo "Table 'student_registrations' exists: " . ($tableExists ? "YES" : "NO") . "\n\n";
    
    if ($tableExists) {
        // Show table structure
        $stmt = $conn->query("DESCRIBE student_registrations");
        echo "Table structure:\n";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "- {$row['Field']} ({$row['Type']})\n";
        }
    } else {
        echo "Need to create the table! Run the SQL from soccs_financial_management.sql\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>

