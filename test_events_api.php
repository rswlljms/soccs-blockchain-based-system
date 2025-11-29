<?php
/**
 * Test file to verify events API and date handling
 * Access: http://localhost/soccs-financial-management/test_events_api.php
 */

require_once 'includes/database.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events API Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-section {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #4B0082;
        }
        h2 {
            color: #9933ff;
            border-bottom: 2px solid #9933ff;
            padding-bottom: 10px;
        }
        .success {
            color: #28a745;
            font-weight: bold;
        }
        .error {
            color: #dc3545;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background: #4B0082;
            color: white;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .info {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 10px;
            margin: 10px 0;
        }
        .code {
            background: #f4f4f4;
            padding: 10px;
            border-radius: 4px;
            font-family: monospace;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <h1>🧪 Events API Test Suite</h1>
    
    <div class="test-section">
        <h2>1. Database Connection Test</h2>
        <?php
        try {
            $database = new Database();
            $conn = $database->getConnection();
            echo '<p class="success">✓ Database connection successful!</p>';
        } catch (Exception $e) {
            echo '<p class="error">✗ Database connection failed: ' . htmlspecialchars($e->getMessage()) . '</p>';
            exit;
        }
        ?>
    </div>

    <div class="test-section">
        <h2>2. Events Table Check</h2>
        <?php
        try {
            $stmt = $conn->query("SHOW TABLES LIKE 'events'");
            if ($stmt->rowCount() > 0) {
                echo '<p class="success">✓ Events table exists!</p>';
                
                $stmt = $conn->query("SELECT COUNT(*) as count FROM events");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo '<p>Total events in database: <strong>' . $result['count'] . '</strong></p>';
            } else {
                echo '<p class="error">✗ Events table does not exist!</p>';
                echo '<div class="info">Please run: <code>sql/create_events_table.sql</code></div>';
            }
        } catch (Exception $e) {
            echo '<p class="error">✗ Error checking table: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        ?>
    </div>

    <div class="test-section">
        <h2>3. Current Server Date/Time</h2>
        <?php
        $serverTime = new DateTime();
        echo '<p>Server Date: <strong>' . $serverTime->format('Y-m-d H:i:s') . '</strong></p>';
        echo '<p>Timezone: <strong>' . date_default_timezone_get() . '</strong></p>';
        ?>
    </div>

    <div class="test-section">
        <h2>4. Events from Database (Raw)</h2>
        <?php
        try {
            $query = "SELECT id, title, date, location, category, status 
                      FROM events 
                      ORDER BY date ASC";
            $stmt = $conn->query($query);
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($events) > 0) {
                echo '<table>';
                echo '<tr><th>ID</th><th>Title</th><th>Date (DB)</th><th>Location</th><th>Category</th><th>Status</th></tr>';
                foreach ($events as $event) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($event['id']) . '</td>';
                    echo '<td>' . htmlspecialchars($event['title']) . '</td>';
                    echo '<td>' . htmlspecialchars($event['date']) . '</td>';
                    echo '<td>' . htmlspecialchars($event['location']) . '</td>';
                    echo '<td>' . htmlspecialchars($event['category']) . '</td>';
                    echo '<td>' . htmlspecialchars($event['status']) . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<p class="error">✗ No events found in database</p>';
            }
        } catch (Exception $e) {
            echo '<p class="error">✗ Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        ?>
    </div>

    <div class="test-section">
        <h2>5. Events from API (Formatted)</h2>
        <div class="code">
            API Endpoint: /api/events/read.php?status=all
        </div>
        <div id="apiResponse"></div>
    </div>

    <div class="test-section">
        <h2>6. Date Format Comparison</h2>
        <?php
        if (isset($events) && count($events) > 0) {
            $sampleEvent = $events[0];
            $eventDate = new DateTime($sampleEvent['date']);
            
            echo '<p><strong>Sample Event:</strong> ' . htmlspecialchars($sampleEvent['title']) . '</p>';
            echo '<table>';
            echo '<tr><th>Format</th><th>Output</th></tr>';
            echo '<tr><td>Database Format</td><td>' . htmlspecialchars($sampleEvent['date']) . '</td></tr>';
            echo '<tr><td>Y-m-d (API)</td><td>' . $eventDate->format('Y-m-d') . '</td></tr>';
            echo '<tr><td>H:i (Time)</td><td>' . $eventDate->format('H:i') . '</td></tr>';
            echo '<tr><td>Full Display</td><td>' . $eventDate->format('F d, Y g:i A') . '</td></tr>';
            echo '</table>';
        }
        ?>
    </div>

    <div class="test-section">
        <h2>7. Upcoming Events Filter Test</h2>
        <?php
        try {
            $now = date('Y-m-d H:i:s');
            $query = "SELECT COUNT(*) as count 
                      FROM events 
                      WHERE date >= :now AND status = 'upcoming'";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':now', $now);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo '<p>Events scheduled for today or later: <strong>' . $result['count'] . '</strong></p>';
            echo '<p class="info">If this is 0, you need to add events with future dates.</p>';
        } catch (Exception $e) {
            echo '<p class="error">✗ Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        ?>
    </div>

    <script>
        // Test API call
        fetch('/soccs-financial-management/api/events/read.php?status=all')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('apiResponse');
                if (data.success) {
                    let html = '<p class="success">✓ API Response successful!</p>';
                    html += '<p>Events returned: <strong>' + data.data.length + '</strong></p>';
                    
                    if (data.data.length > 0) {
                        html += '<table>';
                        html += '<tr><th>ID</th><th>Name</th><th>Date</th><th>Time</th><th>Category</th></tr>';
                        data.data.forEach(event => {
                            html += '<tr>';
                            html += '<td>' + event.id + '</td>';
                            html += '<td>' + event.name + '</td>';
                            html += '<td>' + event.date + '</td>';
                            html += '<td>' + event.time + '</td>';
                            html += '<td>' + event.category + '</td>';
                            html += '</tr>';
                        });
                        html += '</table>';
                    }
                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<p class="error">✗ API Error: ' + data.error + '</p>';
                }
            })
            .catch(error => {
                document.getElementById('apiResponse').innerHTML = 
                    '<p class="error">✗ Fetch Error: ' + error.message + '</p>';
            });
    </script>

    <div class="test-section">
        <h2>📋 Quick Actions</h2>
        <ul>
            <li><a href="pages/events.php">→ Go to Admin Event Management</a></li>
            <li><a href="pages/student-events.php">→ Go to Student Calendar</a></li>
            <li><a href="api/events/read.php?status=all" target="_blank">→ View Raw API Response</a></li>
        </ul>
    </div>

    <div class="test-section">
        <h2>🔧 Troubleshooting</h2>
        <div class="info">
            <strong>If dates are not showing correctly:</strong>
            <ol>
                <li>Check that events table has data with future dates</li>
                <li>Verify server timezone matches your location</li>
                <li>Clear browser cache (Ctrl+Shift+Delete)</li>
                <li>Check browser console (F12) for JavaScript errors</li>
                <li>Ensure date format in database is 'YYYY-MM-DD HH:MM:SS'</li>
            </ol>
        </div>
    </div>
</body>
</html>

