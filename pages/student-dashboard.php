<?php
session_start();

// For now, we'll simulate a student session - in production this would be handled by proper authentication
// Always set demo student to ensure consistent display during development
$_SESSION['student'] = [
  'id' => '0122-1141', 
  'firstName' => 'Roswell James',
  'middleName' => 'Democrito',
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
          <div class="blockchain-badge">
            <i class="fas fa-shield-alt"></i>
            <span>Blockchain Secured</span>
          </div>
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
          <div class="voting-status">
            <div class="status-indicator pending">
              <i class="fas fa-clock"></i>
              <span>Voting Available</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Dashboard Grid -->
      <div class="dashboard-grid">
        
        <!-- Elections Section -->
        <div class="card elections-card" id="electionCard">
          <div class="card-header">
            <h3><i class="fas fa-vote-yea"></i> Student Elections</h3>
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

        <!-- Governance Transparency -->
        <div class="card governance-card">
          <div class="card-header">
            <h3><i class="fas fa-chart-pie"></i> Financial Transparency</h3>
            <div class="blockchain-verified">
              <i class="fas fa-shield-alt"></i>
              <span>Blockchain Verified</span>
            </div>
          </div>
          <div class="card-content">
            <div class="fund-summary">
              <div class="fund-item">
                <div class="fund-icon total">
                  <i class="fas fa-wallet"></i>
                </div>
                <div class="fund-info">
                  <h4>Total Funds</h4>
                  <p class="amount">₱85,450.00</p>
                </div>
              </div>
              
              <div class="fund-item">
                <div class="fund-icon expenses">
                  <i class="fas fa-receipt"></i>
                </div>
                <div class="fund-info">
                  <h4>Total Expenses</h4>
                  <p class="amount">₱32,180.50</p>
                </div>
              </div>
              
              <div class="fund-item">
                <div class="fund-icon balance">
                  <i class="fas fa-piggy-bank"></i>
                </div>
                <div class="fund-info">
                  <h4>Available Balance</h4>
                  <p class="amount balance-positive">₱53,269.50</p>
                </div>
              </div>
            </div>
            
            <div class="recent-transactions">
              <h4>Recent Transactions</h4>
              <div class="transaction-list">
                <div class="transaction-item">
                  <div class="transaction-info">
                    <span class="transaction-name">Event Supplies Purchase</span>
                    <span class="transaction-date">Dec 10, 2024</span>
                  </div>
                  <div class="transaction-amount expense">-₱2,450.00</div>
                  <div class="blockchain-hash" title="Blockchain Hash: 0x1a2b3c...">
                    <i class="fas fa-link"></i>
                  </div>
                </div>
                
                <div class="transaction-item">
                  <div class="transaction-info">
                    <span class="transaction-name">Membership Fees Collection</span>
                    <span class="transaction-date">Dec 8, 2024</span>
                  </div>
                  <div class="transaction-amount income">+₱15,000.00</div>
                  <div class="blockchain-hash" title="Blockchain Hash: 0x4d5e6f...">
                    <i class="fas fa-link"></i>
                  </div>
                </div>
                
                <div class="transaction-item">
                  <div class="transaction-info">
                    <span class="transaction-name">Office Supplies</span>
                    <span class="transaction-date">Dec 5, 2024</span>
                  </div>
                  <div class="transaction-amount expense">-₱890.75</div>
                  <div class="blockchain-hash" title="Blockchain Hash: 0x7g8h9i...">
                    <i class="fas fa-link"></i>
                  </div>
                </div>
              </div>
              
              <button class="btn-view-all" onclick="viewFinancialReports()">
                <i class="fas fa-chart-line"></i>
                View Detailed Reports
              </button>
            </div>
          </div>
        </div>

        <!-- Announcements -->
        <div class="card announcements-card">
          <div class="card-header">
            <h3><i class="fas fa-bullhorn"></i> Announcements</h3>
            <span class="new-badge">2 New</span>
          </div>
          <div class="card-content">
            <div class="announcements-list">
              <div class="announcement-item new">
                <div class="announcement-icon important">
                  <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="announcement-content">
                  <h4>Election Voting Extended</h4>
                  <p>Due to technical issues, voting period has been extended by 24 hours.</p>
                  <span class="announcement-time">2 hours ago</span>
                </div>
              </div>
              
              <div class="announcement-item new">
                <div class="announcement-icon info">
                  <i class="fas fa-info-circle"></i>
                </div>
                <div class="announcement-content">
                  <h4>New Academic Calendar</h4>
                  <p>Updated academic calendar for next semester is now available.</p>
                  <span class="announcement-time">1 day ago</span>
                </div>
              </div>
              
              <div class="announcement-item">
                <div class="announcement-icon success">
                  <i class="fas fa-check-circle"></i>
                </div>
                <div class="announcement-content">
                  <h4>System Maintenance Complete</h4>
                  <p>All systems are now running normally after scheduled maintenance.</p>
                  <span class="announcement-time">3 days ago</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Voting History -->
        <div class="card voting-history-card">
          <div class="card-header">
            <h3><i class="fas fa-history"></i> My Voting History</h3>
            <div class="blockchain-verified">
              <i class="fas fa-shield-alt"></i>
              <span>Verified</span>
            </div>
          </div>
          <div class="card-content">
            <div class="history-list">
              <div class="history-item">
                <div class="history-info">
                  <h4>SOCCS Elections 2025</h4>
                  <p>Cast vote for 9 positions</p>
                  <span class="history-date">May 15, 2023</span>
                </div>
                <div class="history-status completed">
                  <i class="fas fa-check-circle"></i>
                  <span>Completed</span>
                </div>
                <div class="blockchain-hash" title="View on Blockchain">
                  <i class="fas fa-external-link-alt"></i>
                </div>
              </div>
              
              <div class="history-item">
                <div class="history-info">
                  <h4>SOCCS Elections 2025</h4>
                  <p>You Already Voted</p>
                  <span class="history-date">September 8, 2025</span>
                </div>
                <div class="history-status completed">
                  <i class="fas fa-check-circle"></i>
                  <span>Completed</span>
                </div>
                <div class="blockchain-hash" title="View on Blockchain">
                  <i class="fas fa-external-link-alt"></i>
                </div>
              </div>
            </div>
            
            <div class="voting-stats">
              <div class="stat-item">
                <span class="stat-number">2</span>
                <span class="stat-label">Elections Participated</span>
              </div>
              <div class="stat-item">
                <span class="stat-number">100%</span>
                <span class="stat-label">Participation Rate</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Security & Verification -->
        <div class="card security-card">
          <div class="card-header">
            <h3><i class="fas fa-shield-alt"></i> Security & Verification</h3>
          </div>
          <div class="card-content">
            <div class="security-info">
              <div class="security-item">
                <div class="security-icon blockchain">
                  <i class="fas fa-cube"></i>
                </div>
                <div class="security-details">
                  <h4>Blockchain Protection</h4>
                  <p>All your transactions and votes are secured on the blockchain</p>
                  <span class="status-badge active">Active</span>
                </div>
              </div>
              
              <div class="security-item">
                <div class="security-icon encryption">
                  <i class="fas fa-lock"></i>
                </div>
                <div class="security-details">
                  <h4>End-to-End Encryption</h4>
                  <p>Your personal data is encrypted and protected</p>
                  <span class="status-badge active">Active</span>
                </div>
              </div>
              
              <div class="security-item">
                <div class="security-icon verification">
                  <i class="fas fa-certificate"></i>
                </div>
                <div class="security-details">
                  <h4>Identity Verification</h4>
                  <p>Your student identity has been verified</p>
                  <span class="status-badge verified">Verified</span>
                </div>
              </div>
            </div>
            
            <div class="blockchain-explorer">
              <h4>Blockchain Explorer</h4>
              <p>View all organization transactions on the blockchain</p>
              <button class="btn-explorer" onclick="openBlockchainExplorer()">
                <i class="fas fa-external-link-alt"></i>
                Open Explorer
              </button>
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
            <i class="fas fa-shield-alt"></i>
            <span>Blockchain Secured Voting</span>
          </div>
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
