<?php
session_start();
require_once '../includes/database.php';

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
$studentId = $student['id'];

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $query = "SELECT * FROM elections WHERE status = 'active' AND NOW() BETWEEN start_date AND end_date ORDER BY start_date DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $activeElection = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $hasActiveElection = !empty($activeElection);
    
    // Check if student has already voted in this election
    $hasVoted = false;
    if ($hasActiveElection) {
        $voteCheckQuery = "SELECT COUNT(*) as vote_count FROM votes WHERE election_id = :election_id AND voter_id = :voter_id";
        $voteCheckStmt = $conn->prepare($voteCheckQuery);
        $voteCheckStmt->bindParam(':election_id', $activeElection['id']);
        $voteCheckStmt->bindParam(':voter_id', $studentId);
        $voteCheckStmt->execute();
        $voteCheck = $voteCheckStmt->fetch(PDO::FETCH_ASSOC);
        $hasVoted = $voteCheck['vote_count'] > 0;
    }
    
} catch (Exception $e) {
    $hasActiveElection = false;
    $activeElection = null;
    $hasVoted = false;
}

// Fetch real candidates from database
$candidates = [];

if ($hasActiveElection) {
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
                c.platform,
                c.photo
            FROM candidates c
            WHERE c.position_id = :position_id
            ORDER BY c.lastname ASC, c.firstname ASC";
            
            $candStmt = $conn->prepare($candQuery);
            $candStmt->bindParam(':position_id', $position['id']);
            $candStmt->execute();
            $positionCandidates = $candStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Only add position if it has candidates
            if (!empty($positionCandidates)) {
                $candidates[$position['description']] = $positionCandidates;
            }
        }
        
    } catch (Exception $e) {
        error_log("Error fetching candidates: " . $e->getMessage());
        $candidates = [];
    }
}
?>

<?php include('../components/student-sidebar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Cast Your Vote | SOCCS</title>
  <link rel="stylesheet" href="../assets/css/sidebar.css">
  <link rel="stylesheet" href="../assets/css/student-mobile-first.css">
  <link rel="stylesheet" href="../assets/css/student-dashboard.css">
  <link rel="stylesheet" href="../assets/css/student-voting.css">
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
    <div class="voting-wrapper">
      
      <?php if ($hasActiveElection): ?>
        <?php if ($hasVoted): ?>
        <!-- Already Voted Message -->
        <div class="already-voted-container">
          <div class="already-voted-card">
            <div class="voted-icon">
              <i class="fas fa-check-circle"></i>
            </div>
            <h2>Vote Successfully Recorded!</h2>
            <p class="message">Thank you for participating in the election. Your vote has been securely recorded and verified on the blockchain.</p>
            
            <div class="vote-info">
              <div class="info-item">
                <i class="fas fa-shield-alt"></i>
                <span>Your vote is encrypted and anonymous</span>
              </div>
              <div class="info-item">
                <i class="fas fa-cube"></i>
                <span>Blockchain verified and tamper-proof</span>
              </div>
              <div class="info-item">
                <i class="fas fa-lock"></i>
                <span>Cannot be changed once submitted</span>
              </div>
            </div>
            
            <div class="action-buttons-voted">
              <a href="student-dashboard.php" class="btn-dashboard">
                <i class="fas fa-home"></i>
                Back to Dashboard
              </a>
              <a href="student-results.php" class="btn-results">
                <i class="fas fa-chart-bar"></i>
                View Live Results
              </a>
            </div>
          </div>
        </div>
        
        <style>
          .already-voted-container {
            min-height: calc(100vh - 200px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
          }
          
          .already-voted-card {
            background: white;
            border-radius: 16px;
            padding: 60px 40px;
            max-width: 600px;
            width: 100%;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
          }
          
          .voted-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            font-size: 60px;
            color: white;
            animation: scaleIn 0.5s ease-out;
          }
          
          @keyframes scaleIn {
            from { transform: scale(0); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
          }
          
          .already-voted-card h2 {
            font-size: 32px;
            font-weight: 700;
            color: #1f2937;
            margin: 0 0 16px 0;
          }
          
          .already-voted-card .message {
            font-size: 16px;
            color: #6b7280;
            line-height: 1.6;
            margin: 0 0 40px 0;
          }
          
          .vote-info {
            background: #f9fafb;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 32px;
          }
          
          .info-item {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 12px 0;
            color: #4b5563;
            font-size: 15px;
          }
          
          .info-item i {
            color: #10b981;
            font-size: 18px;
          }
          
          .action-buttons-voted {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
          }
          
          .btn-dashboard, .btn-results {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 32px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.2s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
          }
          
          .btn-dashboard {
            background: white;
            color: #4B0082;
            border: 2px solid #4B0082;
          }
          
          .btn-dashboard:hover {
            background: #4B0082;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(75, 0, 130, 0.3);
          }
          
          .btn-results {
            background: linear-gradient(135deg, #4B0082, #9933ff);
            color: white;
          }
          
          .btn-results:hover {
            background: linear-gradient(135deg, #3a0066, #7a29cc);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(153, 51, 255, 0.3);
          }
          
          @media (max-width: 768px) {
            .already-voted-card {
              padding: 40px 24px;
            }
            
            .voted-icon {
              width: 100px;
              height: 100px;
              font-size: 50px;
            }
            
            .already-voted-card h2 {
              font-size: 24px;
            }
            
            .action-buttons-voted {
              flex-direction: column;
            }
            
            .btn-dashboard, .btn-results {
              width: 100%;
            }
          }
        </style>
        
        <?php else: ?>
      <!-- Election Title -->
      <div class="election-title">
        <h2><?= htmlspecialchars($activeElection['title']) ?></h2>
        <p style="color: #6b7280; font-size: 14px; margin-top: 8px;">
          <i class="fas fa-calendar"></i> 
          Ends: <?= date('F d, Y g:i A', strtotime($activeElection['end_date'])) ?>
        </p>
      </div>

      <!-- Voting Form Container -->
      <div class="voting-container">
        <form id="votingForm" method="POST" action="../api/submit_vote.php">
          
          <?php foreach ($candidates as $position => $positionCandidates): ?>
          <!-- Position Section -->
          <div class="position-section">
            <div class="position-header">
              <h3><?= htmlspecialchars($position) ?></h3>
              <p>Select only one candidate</p>
              <button type="button" class="reset-btn" onclick="resetPosition('<?= strtolower(str_replace(' ', '_', $position)) ?>')">
                <i class="fas fa-redo"></i> Reset
              </button>
            </div>

            <div class="candidates-grid">
              <?php foreach ($positionCandidates as $candidate): ?>
              <div class="candidate-card">
                <div class="candidate-radio">
                  <input type="radio" 
                         id="candidate_<?= $candidate['id'] ?>" 
                         name="<?= strtolower(str_replace(' ', '_', $position)) ?>" 
                         value="<?= $candidate['id'] ?>"
                         onchange="selectCandidate(this)"
                         onclick="handleCandidateClick(this)">
                  <label for="candidate_<?= $candidate['id'] ?>" class="radio-label">
                    <div class="candidate-photo">
                      <img src="<?= $candidate['photo'] ?>" 
                           alt="<?= htmlspecialchars($candidate['name']) ?>" 
                           onerror="this.src='../assets/img/logo.png'">
                    </div>
                    <div class="candidate-info">
                      <div class="platform-badge">
                        <i class="fas fa-flag"></i> <?= htmlspecialchars($candidate['partylist']) ?>
                      </div>
                      <h4><?= htmlspecialchars($candidate['name']) ?></h4>
                      <button type="button" class="btn-view-platform" onclick="viewPlatform(<?= $candidate['id'] ?>, '<?= htmlspecialchars($candidate['name']) ?>', '<?= htmlspecialchars($candidate['partylist']) ?>', '<?= htmlspecialchars($position) ?>', '<?= htmlspecialchars(addslashes($candidate['platform'])) ?>')">
                        <i class="fas fa-eye"></i> View Platform
                      </button>
                    </div>
                  </label>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endforeach; ?>

          <!-- Voting Actions -->
          <div class="voting-actions">
            <div class="security-notice">
              <div class="security-item">
                <i class="fas fa-shield-alt"></i>
                <span>Blockchain Secured</span>
              </div>
              <div class="security-item">
                <i class="fas fa-lock"></i>
                <span>Anonymous & Encrypted</span>
              </div>
              <div class="security-item">
                <i class="fas fa-user-check"></i>
                <span>Identity Verified</span>
              </div>
            </div>
            
            <div class="action-buttons">
              <button type="button" class="btn-preview" onclick="previewVote()">
                <i class="fas fa-eye"></i>
                Preview Vote
              </button>
              <button type="submit" class="btn-submit" id="submitVoteBtn" disabled>
                <i class="fas fa-vote-yea"></i>
                Cast Vote
              </button>
            </div>
          </div>
        </form>
      </div>
      <?php endif; ?>
      
      <?php else: ?>
      <!-- No Active Election Message -->
      <div class="no-election-container">
        <div class="no-election-card">
          <div class="no-election-icon">
            <i class="fas fa-vote-yea"></i>
          </div>
          <h2>No Active Election</h2>
          <p class="message">There are currently no ongoing elections. The election has not started yet or has already ended. Please check back later.</p>
          
          <div class="election-status-info">
            <div class="status-item">
              <i class="fas fa-info-circle"></i>
              <span>Election is not currently active</span>
            </div>
            <div class="status-item">
              <i class="fas fa-clock"></i>
              <span>You will be notified when voting opens</span>
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
          min-height: calc(100vh - 100px);
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
        
        @media (max-width: 768px) {
          .no-election-card {
            padding: 40px 24px;
          }
          
          .no-election-icon {
            width: 100px;
            height: 100px;
            font-size: 50px;
          }
          
          .no-election-card h2 {
            font-size: 24px;
          }
        }
      </style>
      <?php endif; ?>
      
    </div>
  </div>

  <!-- Vote Preview Modal -->
  <div class="modal-overlay" id="previewModal" style="display: none;">
    <div class="modal vote-preview-modal">
      <div class="modal-header">
        <h3><i class="fas fa-eye"></i> Vote Preview</h3>
        <button class="modal-close" onclick="closePreviewModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="modal-content">
        <div class="preview-warning">
          <i class="fas fa-exclamation-triangle"></i>
          <p>Please review your selections carefully. Once submitted, your vote cannot be changed.</p>
        </div>
        
        <div id="votePreviewContent">
          <!-- Vote preview will be populated here -->
        </div>
        
        <div class="modal-actions">
          <button class="btn-cancel" onclick="closePreviewModal()">
            <i class="fas fa-arrow-left"></i>
            Back to Voting
          </button>
          <button class="btn-confirm" onclick="confirmVote()">
            <i class="fas fa-check"></i>
            Confirm & Submit
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Platform Modal -->
  <div class="modal-overlay" id="platformModal" style="display: none;">
    <div class="modal platform-modal">
      <div class="modal-header">
        <h3><i class="fas fa-flag"></i> Candidate Platform</h3>
        <button class="modal-close" onclick="closePlatformModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="modal-content">
        <div class="platform-candidate-info">
          <div class="candidate-header">
            <h4 id="platformCandidateName">Candidate Name</h4>
            <div class="candidate-details">
              <span class="position-badge" id="platformCandidatePosition">Position</span>
              <span class="party-badge" id="platformCandidatePartylist">Partylist</span>
            </div>
          </div>
        </div>
        
        <div class="platform-content">
          <h5>Platform & Advocacy:</h5>
          <p id="platformCandidateText">Platform text will appear here...</p>
        </div>
        
        <div class="modal-actions">
          <button class="btn-close-platform" onclick="closePlatformModal()">
            <i class="fas fa-times"></i>
            Close
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Success Modal -->
  <div class="modal-overlay" id="successModal" style="display: none;">
    <div class="modal success-modal">
      <div class="modal-header success">
        <h3><i class="fas fa-check-circle"></i> Vote Successfully Cast!</h3>
      </div>
      <div class="modal-content">
        <div class="success-content">
          <div class="blockchain-confirmation">
            <i class="fas fa-cube"></i>
            <p>Your vote has been securely recorded on the blockchain</p>
            <div class="transaction-hash">
              <span>Transaction Hash:</span>
              <code id="transactionHash">0x1a2b3c4d5e6f...</code>
            </div>
          </div>
          
          <div class="vote-summary">
            <h4>Vote Summary</h4>
            <div id="voteSummaryContent">
              <!-- Vote summary will be populated here -->
            </div>
          </div>
        </div>
        
        <div class="modal-actions">
          <button class="btn-dashboard" onclick="goToDashboard()">
            <i class="fas fa-home"></i>
            Back to Dashboard
          </button>
          <button class="btn-history" onclick="viewVotingHistory()">
            <i class="fas fa-history"></i>
            View Voting History
          </button>
        </div>
      </div>
    </div>
  </div>

  <script src="../assets/js/student-dashboard.js"></script>
  <script src="../assets/js/student-voting.js"></script>
  
  <!-- Enhanced mobile menu initialization for voting page -->
  <script>
    // Ensure mobile menu works on voting page
    document.addEventListener('DOMContentLoaded', function() {
      console.log('Voting page: Initializing mobile menu...');
      
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
            console.log('Mobile toggle clicked!');
            
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
          console.log('Mobile menu initialized successfully!');
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
