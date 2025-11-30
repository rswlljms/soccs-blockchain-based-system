<?php
session_start();

// For now, we'll simulate a student session - in production this would be handled by proper authentication
// Always set demo student to ensure consistent display during development
$_SESSION['student'] = [
  'id' => '0122-1141', 
  'firstName' => 'Roswell James',
  'middleName' => 'Democrito',
  'lastName' => 'Vitaliz',
  'yearLevel' => '4',
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
  <title>Student Dashboard | SOCCS</title>
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
      <!-- Dashboard Header -->
      <div class="dashboard-header">
        <div class="header-left">
          <h1 class="page-title">Student Dashboard</h1>
           <p class="welcome-text">Welcome back, <?= htmlspecialchars($student['firstName']) ?>! 👋</p>
        </div>
        <div class="header-right">
        </div>
      </div>

      <!-- Profile Overview Card -->
      <div class="profile-overview">
        <div class="profile-card">
          <div class="profile-avatar">
            <i class="fas fa-user-graduate"></i>
          </div>
          <div class="profile-info">
            <h3><?= htmlspecialchars($student['firstName'] . ' ' . $student['middleName'] . ' ' . $student['lastName']) ?></h3>
            <p class="student-id">Student ID: <?= htmlspecialchars($student['id']) ?></p>
            <div class="academic-info">
              <span class="course-badge"><?= htmlspecialchars($student['course']) ?></span>
              <span class="year-section"><?= htmlspecialchars($student['yearLevel']) ?>-<?= htmlspecialchars($student['section']) ?></span>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Dashboard Grid -->
      <div class="dashboard-grid">
        
        <!-- Events & Announcements -->
        <div class="card events-card">
          <div class="card-header">
            <h3><i class="fas fa-calendar-alt"></i> Upcoming Events</h3>
            <button class="btn-link" onclick="viewAllEvents()">View All</button>
          </div>
          <div class="card-content">
            <div class="events-list" id="upcomingEventsList">
              <div class="loading-events">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Loading events...</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Elections Section -->
        <div class="card elections-card" id="electionCard">
          <div class="card-header">
            <h3><i class="fas fa-vote-yea"></i> SOCCS Officers Election</h3>
            <div class="election-status" id="electionStatusBadge">
              <span class="status-dot"></span>
              <span id="statusText">Loading...</span>
            </div>
          </div>
          <div class="card-content" id="electionContent">
            <div style="text-align: center; padding: 40px 20px; color: #6b7280;">
              <i class="fas fa-spinner fa-spin" style="font-size: 32px; margin-bottom: 16px;"></i>
              <p>Loading election information...</p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Voting Modal -->
  <div class="modal-overlay" id="votingModal" style="display: none;">
    <div class="modal voting-modal">
      <div class="modal-header">
        <h3>Secure Voting Portal</h3>
        <button class="modal-close" onclick="closeVotingModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="modal-content">
        <div class="voting-security">
          <div class="security-check">
            <i class="fas fa-user-check"></i>
            <span>Identity Verified</span>
          </div>
          <div class="security-check">
            <i class="fas fa-lock"></i>
            <span>Anonymous & Encrypted</span>
          </div>
        </div>
        
        <div class="voting-instructions">
          <h4>Voting Instructions</h4>
          <ol>
            <li>Review all candidates carefully</li>
            <li>Select one candidate per position</li>
            <li>Confirm your choices before submitting</li>
            <li>Your vote will be recorded on the blockchain</li>
          </ol>
        </div>
        
        <div class="modal-actions">
          <button class="btn-cancel" onclick="closeVotingModal()">Cancel</button>
          <button class="btn-proceed" onclick="proceedToVoting()">
            <i class="fas fa-vote-yea"></i>
            Proceed to Vote
          </button>
        </div>
      </div>
    </div>
  </div>

  <script src="../assets/js/student-dashboard.js"></script>
</body>
</html>
