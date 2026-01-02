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
    error_log('Announcements page load error: ' . $e->getMessage());
    $student = $_SESSION['student'];
}
?>

<?php include('../components/student-sidebar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Announcements | SOCCS Student Portal</title>
  <link rel="stylesheet" href="../assets/css/sidebar.css">
  <link rel="stylesheet" href="../assets/css/student-mobile-first.css">
  <link rel="stylesheet" href="../assets/css/student-dashboard.css">
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
          <h1 class="page-title">Announcements</h1>
          <p class="welcome-text">Stay informed with the latest updates from SOCCS</p>
        </div>
        <div class="header-right">
          <div class="blockchain-badge">
            <i class="fas fa-bullhorn"></i>
            <span>Official Updates</span>
          </div>
        </div>
      </div>

      <!-- Announcements -->
      <div class="dashboard-grid">
        <div class="card announcements-card">
          <div class="card-header">
            <h3><i class="fas fa-bullhorn"></i> Latest Announcements</h3>
            <span class="new-badge">3 New</span>
          </div>
          <div class="card-content">
            <div class="announcements-list">
              <div class="announcement-item new">
                <div class="announcement-icon important">
                  <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="announcement-content">
                  <h4>Election Voting Extended</h4>
                  <p>Due to technical issues, voting period has been extended by 24 hours. Please make sure to cast your vote before the new deadline.</p>
                  <span class="announcement-time">2 hours ago</span>
                </div>
              </div>
              
              <div class="announcement-item new">
                <div class="announcement-icon info">
                  <i class="fas fa-info-circle"></i>
                </div>
                <div class="announcement-content">
                  <h4>New Academic Calendar Released</h4>
                  <p>Updated academic calendar for next semester is now available. Check your emails for detailed information.</p>
                  <span class="announcement-time">1 day ago</span>
                </div>
              </div>
              
              <div class="announcement-item new">
                <div class="announcement-icon success">
                  <i class="fas fa-check-circle"></i>
                </div>
                <div class="announcement-content">
                  <h4>System Maintenance Complete</h4>
                  <p>All systems are now running normally after scheduled maintenance. Thank you for your patience.</p>
                  <span class="announcement-time">2 days ago</span>
                </div>
              </div>

              <div class="announcement-item">
                <div class="announcement-icon info">
                  <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="announcement-content">
                  <h4>Tech Summit Registration Open</h4>
                  <p>Registration for the Annual Tech Summit is now open. Limited slots available, register early!</p>
                  <span class="announcement-time">3 days ago</span>
                </div>
              </div>

              <div class="announcement-item">
                <div class="announcement-icon warning">
                  <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="announcement-content">
                  <h4>Library Schedule Change</h4>
                  <p>Library will have modified hours during the holiday season. Please check the posted schedule.</p>
                  <span class="announcement-time">5 days ago</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="../assets/js/student-dashboard.js"></script>
</body>
</html>
