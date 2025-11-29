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
  <title>Voting History | SOCCS Student Portal</title>
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
          <h1 class="page-title">My Voting History</h1>
          <p class="welcome-text">View all your previous voting records securely stored on blockchain</p>
        </div>
        <div class="header-right">
          <div class="blockchain-badge">
            <i class="fas fa-shield-alt"></i>
            <span>Blockchain Verified</span>
          </div>
        </div>
      </div>

      <!-- Voting History -->
      <div class="dashboard-grid">
        <div class="card voting-history-card">
          <div class="card-header">
            <h3><i class="fas fa-history"></i> Complete Voting History</h3>
            <div class="blockchain-verified">
              <i class="fas fa-shield-alt"></i>
              <span>Verified</span>
            </div>
          </div>
          <div class="card-content">
            <div class="history-list">
              <div class="history-item">
                <div class="history-info">
                  <h4>SOCCS General Elections 2024</h4>
                  <p>Cast votes for 9 positions including President, Vice President, and other officers</p>
                  <span class="history-date">December 15, 2024</span>
                </div>
                <div class="history-status completed">
                  <i class="fas fa-check-circle"></i>
                  <span>Vote Cast</span>
                </div>
                <div class="blockchain-hash" title="View on Blockchain">
                  <i class="fas fa-external-link-alt"></i>
                </div>
              </div>
              
              <div class="history-item">
                <div class="history-info">
                  <h4>SOCCS Mid-Year Elections 2024</h4>
                  <p>Special election for Secretary position</p>
                  <span class="history-date">September 8, 2024</span>
                </div>
                <div class="history-status completed">
                  <i class="fas fa-check-circle"></i>
                  <span>Vote Cast</span>
                </div>
                <div class="blockchain-hash" title="View on Blockchain">
                  <i class="fas fa-external-link-alt"></i>
                </div>
              </div>

              <div class="history-item">
                <div class="history-info">
                  <h4>SOCCS General Elections 2023</h4>
                  <p>Full election for all officer positions</p>
                  <span class="history-date">May 15, 2023</span>
                </div>
                <div class="history-status completed">
                  <i class="fas fa-check-circle"></i>
                  <span>Vote Cast</span>
                </div>
                <div class="blockchain-hash" title="View on Blockchain">
                  <i class="fas fa-external-link-alt"></i>
                </div>
              </div>
            </div>
            
            <div class="voting-stats">
              <div class="stat-item">
                <span class="stat-number">3</span>
                <span class="stat-label">Elections Participated</span>
              </div>
              <div class="stat-item">
                <span class="stat-number">100%</span>
                <span class="stat-label">Participation Rate</span>
              </div>
              <div class="stat-item">
                <span class="stat-number">18</span>
                <span class="stat-label">Total Votes Cast</span>
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
