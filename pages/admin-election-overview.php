<?php 
session_start();
require_once '../includes/page_access.php';
checkPageAccess(['view_election', 'view_election_results']);
include('../components/sidebar.php');
require_once '../includes/database.php';

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

$electionResults = [];
$totalVotes = 0;
$totalVoters = 0;
$eligibleVoters = 0;

$positionMaxVotes = [];

if ($electionStatus !== 'no_election' && $electionId) {
    try {
        $posQuery = "SELECT * FROM positions ORDER BY id ASC";
        $posStmt = $conn->prepare($posQuery);
        $posStmt->execute();
        $positions = $posStmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($positions as $position) {
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
                $positionMaxVotes[$position['description']] = (int)$position['max_votes'];
            }
        }
        
        $statsQuery = "SELECT 
            COUNT(DISTINCT voter_id) as total_voters,
            COUNT(*) as total_votes
        FROM votes 
        WHERE election_id = :election_id";
        $statsStmt = $conn->prepare($statsQuery);
        $statsStmt->bindParam(':election_id', $electionId);
        $statsStmt->execute();
        $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
        
        $totalVotes = $stats['total_votes'];
        $totalVoters = $stats['total_voters'];
        
        $voterQuery = "SELECT COUNT(*) as count FROM students WHERE is_active = 1";
        $voterStmt = $conn->prepare($voterQuery);
        $voterStmt->execute();
        $voterData = $voterStmt->fetch(PDO::FETCH_ASSOC);
        $eligibleVoters = $voterData['count'];
        
    } catch (Exception $e) {
        error_log("Error fetching election results: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Election Overview - Admin</title>
  <link rel="stylesheet" href="../assets/css/sidebar.css">
  <link rel="stylesheet" href="../assets/css/admin-election-overview.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
  <div class="main-content">
    <div class="page-header">
      <div class="header-left">
        <h1 class="page-title">Election Overview</h1>
      </div>
    </div>

    <?php if ($electionStatus === 'no_election'): ?>
      <div class="no-election-container">
        <div class="no-election-icon">
          <i class="fas fa-vote-yea"></i>
        </div>
        <h2>No Election Data Available</h2>
        <p>There are currently no active or completed elections to display.</p>
        <a href="elections.php" class="btn-create-election">
          <i class="fas fa-plus"></i> Create New Election
        </a>
      </div>
    <?php else: ?>
      <div class="election-header">
        <div class="election-title-section">
          <h2><?= htmlspecialchars($electionTitle) ?></h2>
          <?php if ($electionStatus === 'active'): ?>
            <span class="status-live">
              <i class="fas fa-circle"></i> Live Count
            </span>
          <?php else: ?>
            <span class="status-completed">
              <i class="fas fa-check-circle"></i> Completed
            </span>
          <?php endif; ?>
        </div>
        
        <div class="election-stats">
          <div class="stat-box">
            <div class="stat-icon">
              <i class="fas fa-users"></i>
            </div>
            <div class="stat-details">
              <span class="stat-value"><?= $eligibleVoters ?></span>
              <span class="stat-label">Eligible Voters</span>
            </div>
          </div>
          
          <div class="stat-box">
            <div class="stat-icon">
              <i class="fas fa-chart-bar"></i>
            </div>
            <div class="stat-details">
              <span class="stat-value"><?= $totalVoters ?></span>
              <span class="stat-label">Voters Participated</span>
            </div>
          </div>
          
          <div class="stat-box">
            <div class="stat-icon">
              <i class="fas fa-percentage"></i>
            </div>
            <div class="stat-details">
              <span class="stat-value"><?= $eligibleVoters > 0 ? round(($totalVoters / $eligibleVoters) * 100, 1) : 0 ?>%</span>
              <span class="stat-label">Turnout Rate</span>
            </div>
          </div>
        </div>
      </div>

      <?php if (!empty($electionResults)): ?>
        <?php foreach ($electionResults as $position => $candidates): ?>
          <?php $maxWinners = $positionMaxVotes[$position] ?? 1; ?>
          <div class="position-section">
            <h3 class="position-title">
              <i class="fas fa-crown"></i> <?= htmlspecialchars($position) ?>
            </h3>
            
            <div class="candidates-grid">
              <?php 
              $topVotes = !empty($candidates) ? $candidates[0]['votes'] : 0;
              foreach ($candidates as $index => $candidate): 
                $isWinner = ($electionStatus === 'completed' && $index < $maxWinners && $candidate['votes'] > 0);
                $votePercentage = $totalVotes > 0 ? ($candidate['votes'] / $totalVotes * 100) : 0;
              ?>
                <div class="candidate-card <?= $isWinner ? 'winner' : '' ?>">
                  <?php if ($isWinner): ?>
                    <div class="winner-badge">
                      <i class="fas fa-trophy"></i> Winner
                    </div>
                  <?php endif; ?>
                  
                  <div class="candidate-photo">
                    <?php 
                    $photoPath = $candidate['photo'];
                    if (!str_starts_with($photoPath, '../')) {
                        $photoPath = '../uploads/candidates/' . $photoPath;
                    }
                    ?>
                    <img src="<?= htmlspecialchars($photoPath) ?>" 
                         alt="<?= htmlspecialchars($candidate['name']) ?>"
                         onerror="this.src='../assets/img/logo.png'">
                  </div>
                  
                  <div class="candidate-info">
                    <h4><?= htmlspecialchars($candidate['name']) ?></h4>
                    <p class="partylist"><?= htmlspecialchars($candidate['partylist']) ?></p>
                  </div>
                  
                  <div class="vote-stats">
                    <div class="vote-count">
                      <span class="count"><?= $candidate['votes'] ?></span>
                      <span class="label">votes</span>
                    </div>
                    <div class="vote-percentage">
                      <?= number_format($votePercentage, 1) ?>%
                    </div>
                  </div>
                  
                  <div class="vote-bar">
                    <div class="vote-bar-fill" style="width: <?= $votePercentage ?>%"></div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="no-results">
          <i class="fas fa-inbox"></i>
          <p>No results available yet</p>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>

</body>
</html>

