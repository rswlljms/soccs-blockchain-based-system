<?php
session_start();

// For now, we'll simulate a student session - in production this would be handled by proper authentication
$_SESSION['student'] = [
  'id' => '0122-1141', 
  'firstName' => 'Roswell James',
  'middleName' => 'D.',
  'lastName' => 'Vitaliz',
  'yearLevel' => '3',
  'section' => 'A',
  'course' => 'BSIT',
  'email' => 'roswelljamesvitaliz@gmail.com'
];

$student = $_SESSION['student'];
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
        <div class="header-right">
          <div class="blockchain-badge">
            <i class="fas fa-calendar-alt"></i>
            <span>Event Portal</span>
          </div>
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
