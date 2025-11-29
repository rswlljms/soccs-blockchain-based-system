<?php
session_start();
require_once '../includes/database.php';

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

// Check for any election (active or completed)
try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $query = "SELECT * FROM elections WHERE status IN ('active', 'completed') ORDER BY created_at DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $election = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($election) {
        $electionStatus = $election['status'];
        $electionTitle = $election['title'];
        $electionId = $election['id'];
    } else {
        $electionStatus = 'no_election';
        $electionTitle = '';
        $electionId = null;
    }
} catch (Exception $e) {
    $electionStatus = 'no_election';
    $electionTitle = '';
    $electionId = null;
}

// Fetch real election results from database
$electionResults = [];

if ($electionStatus !== 'no_election' && $electionId) {
    try {
        // Get all positions
        $posQuery = "SELECT * FROM positions ORDER BY id ASC";
        $posStmt = $conn->prepare($posQuery);
        $posStmt->execute();
        $positions = $posStmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($positions as $position) {
            // Get candidates for each position
            $candQuery = "SELECT 
                c.id,
                CONCAT(c.firstname, ' ', c.lastname) as name,
                c.partylist,
                c.photo,
                COALESCE(COUNT(v.id), 0) as votes
            FROM candidates c
            LEFT JOIN votes v ON c.id = v.candidate_id AND v.election_id = :election_id
            WHERE c.position_id = :position_id
            GROUP BY c.id
            ORDER BY votes DESC, c.lastname ASC";
            
            $candStmt = $conn->prepare($candQuery);
            $candStmt->bindParam(':election_id', $electionId);
            $candStmt->bindParam(':position_id', $position['id']);
            $candStmt->execute();
            $candidates = $candStmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($candidates)) {
                $electionResults[$position['description']] = $candidates;
            }
        }
        
        // Get total votes statistics
        $statsQuery = "SELECT 
            COUNT(DISTINCT voter_id) as total_votes_cast,
            (SELECT COUNT(*) FROM students WHERE is_active = 1) as total_eligible_voters
        FROM votes 
        WHERE election_id = :election_id";
        
        $statsStmt = $conn->prepare($statsQuery);
        $statsStmt->bindParam(':election_id', $electionId);
        $statsStmt->execute();
        $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
        
        $totalVotesCast = $stats['total_votes_cast'] ?? 0;
        $totalEligibleVoters = $stats['total_eligible_voters'] ?? 1;
        
    } catch (Exception $e) {
        error_log("Error fetching election results: " . $e->getMessage());
        $electionResults = [];
        $totalVotesCast = 0;
        $totalEligibleVoters = 1;
    }
} else {
    $totalVotesCast = 0;
    $totalEligibleVoters = 1;
}

// Calculate total votes per position for percentage calculation
function calculateTotalVotes($candidates) {
  return array_sum(array_column($candidates, 'votes'));
}

// Calculate voter turnout
$voterTurnout = $totalEligibleVoters > 0 ? round(($totalVotesCast / $totalEligibleVoters) * 100, 1) : 0;
?>

<?php include('../components/student-sidebar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Election Overview | SOCCS</title>
  <link rel="stylesheet" href="../assets/css/sidebar.css">
  <link rel="stylesheet" href="../assets/css/student-mobile-first.css">
  <link rel="stylesheet" href="../assets/css/student-dashboard.css">
  <link rel="stylesheet" href="../assets/css/student-results.css">
  <link rel="stylesheet" href="../assets/css/hide-timestamp.css">
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
    <div class="results-wrapper">
      
      <?php if ($electionStatus === 'no_election'): ?>
      <!-- No Election Message -->
      <div class="no-election-container">
        <div class="no-election-card">
          <div class="no-election-icon">
            <i class="fas fa-calendar-times"></i>
          </div>
          <h2>No Election Data Available</h2>
          <p class="message">There are currently no active or completed elections to display. Please check back later when an election has been scheduled.</p>
          
          <div class="election-status-info">
            <div class="status-item">
              <i class="fas fa-info-circle"></i>
              <span>Election results will appear here once voting begins</span>
            </div>
            <div class="status-item">
              <i class="fas fa-clock"></i>
              <span>You will be notified when elections start</span>
            </div>
          </div>
          
          <a href="student-dashboard.php" class="btn-back">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
          </a>
        </div>
      </div>
      
      <style>
        .no-election-container {
          min-height: calc(100vh - 200px);
          display: flex;
          align-items: center;
          justify-content: center;
          padding: 40px 20px;
        }
        
        .no-election-card {
          background: white;
          border-radius: 16px;
          padding: 60px 40px;
          max-width: 600px;
          width: 100%;
          text-align: center;
          box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .no-election-icon {
          width: 120px;
          height: 120px;
          background: linear-gradient(135deg, #4B0082, #9933ff);
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          margin: 0 auto 30px;
          font-size: 60px;
          color: white;
          opacity: 0.9;
        }
        
        .no-election-card h2 {
          font-size: 32px;
          font-weight: 700;
          color: #1f2937;
          margin: 0 0 16px 0;
        }
        
        .no-election-card .message {
          font-size: 16px;
          color: #6b7280;
          line-height: 1.6;
          margin: 0 0 40px 0;
        }
        
        .election-status-info {
          background: #f9fafb;
          border-radius: 12px;
          padding: 24px;
          margin-bottom: 32px;
        }
        
        .status-item {
          display: flex;
          align-items: center;
          justify-content: center;
          gap: 12px;
          padding: 12px 0;
          color: #4b5563;
          font-size: 15px;
        }
        
        .status-item i {
          color: #9933ff;
          font-size: 18px;
        }
        
        .btn-back {
          display: inline-flex;
          align-items: center;
          gap: 10px;
          background: linear-gradient(135deg, #4B0082, #9933ff);
          color: white;
          padding: 14px 32px;
          border-radius: 8px;
          text-decoration: none;
          font-weight: 600;
          font-size: 15px;
          transition: all 0.2s;
          box-shadow: 0 2px 4px rgba(153, 51, 255, 0.2);
        }
        
        .btn-back:hover {
          background: linear-gradient(135deg, #3a0066, #7a29cc);
          transform: translateY(-2px);
          box-shadow: 0 4px 8px rgba(153, 51, 255, 0.3);
        }
      </style>
      
      <?php else: ?>
      
      <!-- Results Header -->
      <div class="results-header">
        <h1><i class="fas fa-chart-bar"></i> Election Overview</h1>
        <div class="election-status <?= $electionStatus === 'active' ? 'live' : $electionStatus ?>">
          <span class="status-dot"></span>
          <?= $electionStatus === 'active' ? 'Live' : ucfirst($electionStatus) ?>
        </div>
      </div>

      <!-- Election Title -->
      <div class="election-title">
        <h2><?= htmlspecialchars($electionTitle) ?></h2>
        <div class="election-stats">
          <div class="stat-item">
            <span class="stat-number"><?= number_format($totalVotesCast) ?></span>
            <span class="stat-label">Total Votes Cast</span>
          </div>
          <div class="stat-item">
            <span class="stat-number"><?= number_format($totalEligibleVoters) ?></span>
            <span class="stat-label">Eligible Voters</span>
          </div>
          <div class="stat-item">
            <span class="stat-number"><?= $voterTurnout ?>%</span>
            <span class="stat-label">Voter Turnout</span>
          </div>
        </div>
      </div>

      <!-- Results Container -->
      <div class="results-container">
        
        <?php foreach ($electionResults as $position => $candidates): ?>
        <?php 
          $totalPositionVotes = calculateTotalVotes($candidates);
          $winner = $candidates[0]; // First candidate (highest votes)
        ?>
        
        <!-- Position Results Section -->
        <div class="position-results">
          <div class="position-header">
            <h3><?= htmlspecialchars($position) ?></h3>
            <div class="position-stats">
              <span class="total-votes"><?= number_format($totalPositionVotes) ?> total votes</span>
              <?php if ($electionStatus === 'completed'): ?>
              <span class="winner-badge">
                <i class="fas fa-crown"></i>
                Winner: <?= htmlspecialchars($winner['name']) ?>
              </span>
              <?php elseif ($electionStatus === 'active'): ?>
              <span class="live-badge">
                <i class="fas fa-circle"></i>
                Live Count
              </span>
              <?php endif; ?>
            </div>
          </div>

          <div class="candidates-results">
            <?php foreach ($candidates as $index => $candidate): ?>
            <?php 
              $percentage = $totalPositionVotes > 0 ? round(($candidate['votes'] / $totalPositionVotes) * 100, 1) : 0;
              $isWinner = $index === 0 && $electionStatus === 'completed';
            ?>
            
            <div class="candidate-result <?= $isWinner ? 'winner' : '' ?>">
              <div class="candidate-info">
                <div class="candidate-photo">
                  <?php if ($isWinner): ?>
                  <div class="winner-crown">
                    <i class="fas fa-crown"></i>
                  </div>
                  <?php endif; ?>
                  <div class="photo-circle">
                    <img src="<?= $candidate['photo'] ?>" 
                         alt="<?= htmlspecialchars($candidate['name']) ?>" 
                         onerror="this.src='../assets/img/logo.png'">
                  </div>
                </div>
                <div class="candidate-details">
                  <h4 class="candidate-name"><?= htmlspecialchars($candidate['name']) ?></h4>
                  <span class="candidate-party"><?= htmlspecialchars($candidate['partylist']) ?></span>
                </div>
              </div>
              
              <div class="vote-count">
                <div class="votes-number"><?= number_format($candidate['votes']) ?></div>
                <div class="votes-label">votes</div>
              </div>
              
              <div class="vote-progress">
                <div class="progress-bar">
                  <div class="progress-fill" style="width: <?= $percentage ?>%"></div>
                </div>
                <span class="percentage"><?= $percentage ?>%</span>
              </div>
            </div>
            
            <?php endforeach; ?>
          </div>
        </div>
        
        <?php endforeach; ?>

      </div>

      
      <?php endif; ?>

    </div>
  </div>

  <script src="../assets/js/student-dashboard.js"></script>
  <script src="../assets/js/student-results.js"></script>
  
  <!-- Enhanced mobile menu initialization for results page -->
  <script>
    // Ensure mobile menu works on results page
    document.addEventListener('DOMContentLoaded', function() {
      console.log('Results page: Initializing mobile menu...');
      
      // Multiple attempts to ensure initialization
      function initializeMobileMenu() {
        const mobileToggle = document.getElementById('mobileMenuToggle');
        const mobileOverlay = document.getElementById('mobileOverlay');
        const sidebar = document.querySelector('.sidebar');
        
        console.log('Elements found:', {
          toggle: !!mobileToggle,
          overlay: !!mobileOverlay,
          sidebar: !!sidebar
        });
        
        if (mobileToggle && mobileOverlay && sidebar) {
          // Remove any existing handlers
          mobileToggle.replaceWith(mobileToggle.cloneNode(true));
          const newToggle = document.getElementById('mobileMenuToggle');
          
          // Add click handler
          newToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Mobile toggle clicked on results page!');
            
            if (sidebar.classList.contains('mobile-open')) {
              sidebar.classList.remove('mobile-open');
              mobileOverlay.classList.remove('active');
              newToggle.innerHTML = '<i class="fas fa-bars"></i>';
              document.body.style.overflow = '';
            } else {
              sidebar.classList.add('mobile-open');
              mobileOverlay.classList.add('active');
              newToggle.innerHTML = '<i class="fas fa-times"></i>';
              document.body.style.overflow = 'hidden';
            }
          });
          
          // Overlay click to close
          mobileOverlay.addEventListener('click', function() {
            sidebar.classList.remove('mobile-open');
            mobileOverlay.classList.remove('active');
            newToggle.innerHTML = '<i class="fas fa-bars"></i>';
            document.body.style.overflow = '';
          });
          
          // Force show toggle button
          newToggle.style.display = 'flex';
          console.log('Results page mobile menu initialized successfully!');
          return true;
        }
        return false;
      }
      
      // Try immediately
      if (!initializeMobileMenu()) {
        // Try after 100ms
        setTimeout(function() {
          if (!initializeMobileMenu()) {
            // Try after 500ms
            setTimeout(initializeMobileMenu, 500);
          }
        }, 100);
      }
    });
  </script>
</body>
</html>
