<?php
session_start();
require_once '../includes/database.php';

if (!isset($_SESSION['student'])) {
    header('Location: ../templates/login.php');
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $studentId = $_SESSION['student']['id'];
    
    $query = "SELECT * FROM students WHERE id = ? AND is_active = 1";
    $stmt = $conn->prepare($query);
    $stmt->execute([$studentId]);
    $studentData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$studentData) {
        session_destroy();
        header('Location: ../templates/login.php');
        exit;
    }
    
    $student = [
        'id' => $studentData['id'],
        'firstName' => $studentData['first_name'],
        'middleName' => $studentData['middle_name'] ?? '',
        'lastName' => $studentData['last_name'],
        'email' => $studentData['email'],
        'yearLevel' => $studentData['year_level'],
        'section' => $studentData['section'],
        'course' => $studentData['course'] ?? 'BSIT'
    ];
    
    $_SESSION['student'] = array_merge($_SESSION['student'], $student);
    
} catch (Exception $e) {
    error_log('Events page load error: ' . $e->getMessage());
    $student = $_SESSION['student'];
}
?>

<?php include('../components/student-sidebar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Events | SOCCS Student Portal</title>
  <link rel="stylesheet" href="../assets/css/sidebar.css">
  <link rel="stylesheet" href="../assets/css/student-mobile-first.css">
  <link rel="stylesheet" href="../assets/css/student-dashboard.css">
  <link rel="stylesheet" href="../assets/css/student-events.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
  <!-- Mobile Menu Toggle -->
  <div class="mobile-menu-toggle" id="mobileMenuToggle">
    <i class="fas fa-bars"></i>
  </div>
  
  <!-- Mobile Overlay -->
  <div class="mobile-overlay" id="mobileOverlay"></div>
  
  <div class="main-content">
    <div class="dashboard-wrapper">
      <!-- Page Header -->
      <div class="dashboard-header">
        <div class="header-left">
          <h1 class="page-title">Events Calendar</h1>
          <p class="welcome-text">Stay updated with SOCCS events and activities</p>
        </div>
      </div>

      <!-- Calendar Container -->
      <div class="calendar-container">
        <!-- Calendar Navigation -->
        <div class="calendar-navigation">
          <button class="nav-btn" id="prevMonth">
            <i class="fas fa-chevron-left"></i>
          </button>
          <div class="current-month" id="currentMonth">December 2023</div>
          <button class="nav-btn" id="nextMonth">
            <i class="fas fa-chevron-right"></i>
          </button>
        </div>

        <!-- Calendar Grid -->
        <div class="calendar-grid">
          <div class="calendar-header">
            <div class="day-header">Sun</div>
            <div class="day-header">Mon</div>
            <div class="day-header">Tue</div>
            <div class="day-header">Wed</div>
            <div class="day-header">Thu</div>
            <div class="day-header">Fri</div>
            <div class="day-header">Sat</div>
          </div>
          <div class="calendar-days" id="calendarDays">
            <!-- Calendar days will be populated by JavaScript -->
          </div>
        </div>

        <!-- Selected Date Events -->
        <div class="selected-date-events" id="selectedDateEvents">
          <div class="no-events-message">
            <i class="fas fa-calendar-day"></i>
            <p>Select a date to view events</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="../assets/js/student-dashboard.js"></script>
  <script src="../assets/js/student-events.js"></script>
</body>
</html>
