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
    error_log('Voting history page load error: ' . $e->getMessage());
    $student = $_SESSION['student'];
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("
        SELECT DISTINCT
            e.id AS election_id,
            e.title AS election_title,
            DATE(MIN(v.voted_at)) AS vote_date,
            COUNT(DISTINCT v.position_id) AS positions_voted,
            COUNT(v.id) AS total_votes
        FROM votes v
        INNER JOIN elections e ON v.election_id = e.id
        WHERE v.voter_id = ?
        GROUP BY e.id, e.title
        ORDER BY vote_date DESC
    ");
    $stmt->execute([$studentId]);
    $votingHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $totalElections = count($votingHistory);
    $totalVotes = 0;
    foreach ($votingHistory as $history) {
        $totalVotes += $history['total_votes'];
    }
    $participationRate = $totalElections > 0 ? '100%' : '0%';
    
} catch (Exception $e) {
    $votingHistory = [];
    $totalElections = 0;
    $totalVotes = 0;
    $participationRate = '0%';
    error_log("Error fetching voting history: " . $e->getMessage());
}
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
  <link rel="stylesheet" href="../assets/css/admin-table-styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    .voting-history-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 24px;
    }
    
    .stat-card {
      background: white;
      border-radius: var(--radius-md);
      padding: 20px;
      box-shadow: var(--shadow-sm);
      border: 1px solid var(--border-color);
    }
    
    .stat-card .stat-number {
      font-size: 28px;
      font-weight: 700;
      color: var(--text-primary);
      display: block;
      margin-bottom: 4px;
      background: var(--primary-gradient);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .stat-card .stat-label {
      font-size: 14px;
      color: var(--text-secondary);
      font-weight: 500;
    }
    
    .table-header-controls {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      padding: 16px 24px;
      background: var(--secondary-color);
      border-bottom: 1px solid var(--border-color);
    }
    
    .search-box {
      position: relative;
      width: 300px;
    }
    
    .search-box input {
      width: 100%;
      padding: 10px 16px 10px 40px;
      border: 2px solid var(--border-color);
      border-radius: var(--radius-sm);
      font-size: 14px;
      font-family: 'Work Sans', sans-serif;
      transition: var(--transition);
      background: white;
    }
    
    .search-box input:focus {
      outline: none;
      border-color: #9933ff;
      box-shadow: 0 0 0 4px rgba(153, 51, 255, 0.1);
    }
    
    .search-box i {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-secondary);
      font-size: 14px;
    }
    
    @media (max-width: 768px) {
      .table-header-controls {
        padding: 12px 16px;
      }
      
      .search-box {
        width: 100%;
      }
      
      .voting-history-stats {
        grid-template-columns: 1fr;
      }
    }
  </style>
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
      </div>

      <!-- Statistics Cards -->
      <div class="voting-history-stats">
        <div class="stat-card">
          <span class="stat-number"><?= $totalElections ?></span>
          <span class="stat-label">Elections Participated</span>
        </div>
        <div class="stat-card">
          <span class="stat-number"><?= $participationRate ?></span>
          <span class="stat-label">Participation Rate</span>
        </div>
        <div class="stat-card">
          <span class="stat-number"><?= $totalVotes ?></span>
          <span class="stat-label">Total Votes Cast</span>
        </div>
      </div>

      <!-- Voting History Table -->
      <div class="table-container">
        <div class="table-header-controls">
          <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Search elections..." onkeyup="filterTable()">
          </div>
        </div>
        <table class="styled-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Election Title</th>
            </tr>
          </thead>
          <tbody id="voting-history-table">
            <?php if (empty($votingHistory)): ?>
              <tr>
                <td colspan="2" style="text-align: center; padding: 40px; color: var(--text-secondary);">
                  No voting history found. You haven't participated in any elections yet.
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($votingHistory as $history): ?>
                <tr>
                  <td><?= date('F j, Y', strtotime($history['vote_date'])) ?></td>
                  <td><strong><?= htmlspecialchars($history['election_title']) ?></strong></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
        
        <div class="pagination centered">
          <button type="button" class="page-btn disabled" disabled>&laquo; Prev</button>
          <span class="page-indicator">Showing <?= count($votingHistory) ?> of <?= $totalElections ?></span>
          <button type="button" class="page-btn disabled" disabled>Next &raquo;</button>
        </div>
      </div>
    </div>
  </div>

  <script src="../assets/js/student-dashboard.js"></script>
  <script>
    function filterTable() {
      const input = document.getElementById('searchInput');
      const filter = input.value.toLowerCase();
      const table = document.getElementById('voting-history-table');
      const rows = table.getElementsByTagName('tr');

      for (let i = 0; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        let found = false;
        
        for (let j = 0; j < cells.length; j++) {
          if (cells[j]) {
            const text = cells[j].textContent || cells[j].innerText;
            if (text.toLowerCase().indexOf(filter) > -1) {
              found = true;
              break;
            }
          }
        }
        
        rows[i].style.display = found ? '' : 'none';
      }
    }
  </script>
</body>
</html>
