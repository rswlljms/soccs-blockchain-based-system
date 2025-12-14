<?php
session_start();
require_once '../includes/page_access.php';
checkPageAccess(['view_election', 'manage_election_status']);
require_once '../includes/auth_check.php';
include('../components/sidebar.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Election Management</title>
  <link rel="stylesheet" href="../assets/css/sidebar.css">
  <link rel="stylesheet" href="../assets/css/admin-table-styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../assets/css/elections.css">
</head>

<body>
  <div class="main-content">
    <h1 class="page-title">Election Management</h1>
    
    <?php if (hasPermission('manage_election_status')): ?>
    <div class="header-controls">
      <button class="btn-new" onclick="openElectionModal()">
        <i class="fas fa-plus"></i> New Election
      </button>
    </div>
    <?php endif; ?>
    
    <div class="table-container">
      <table class="styled-table">
        <thead>
          <tr>
            <th>Title</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="elections-table-body">
        </tbody>
      </table>
      
      <div class="pagination centered">
        <button type="button" class="page-btn prev-btn" onclick="goToPage('prev')">&laquo; Prev</button>
        <span class="page-indicator">Page 1 of 1</span>
        <button type="button" class="page-btn next-btn" onclick="goToPage('next')">Next &raquo;</button>
      </div>
    </div>
  </div>

  <div class="modal-overlay" id="electionModalOverlay"></div>
  <div class="modal" id="electionModal">
    <div class="modal-header">
      <h2 class="modal-title" id="modalTitle">Add Election</h2>
      <button type="button" class="modal-close" onclick="closeElectionModal()">
        <i class="fas fa-times"></i>
      </button>
    </div>
    
    <form id="electionForm" class="election-form-modal">
      <div class="form-group">
        <label for="electionTitle">
          <i class="fas fa-vote-yea"></i> Election Title *
        </label>
        <input type="text" id="electionTitle" name="title" placeholder="e.g., SOCCS Officer Election 2025" required>
      </div>
      
      <div class="form-group">
        <label for="electionDescription">
          <i class="fas fa-info-circle"></i> Description
        </label>
        <textarea id="electionDescription" name="description" placeholder="Enter election description (optional)"></textarea>
      </div>
      
      <div class="form-row">
        <div class="form-group">
          <label for="electionStartDate">
            <i class="fas fa-calendar-alt"></i> Start Date & Time *
          </label>
          <input type="datetime-local" id="electionStartDate" name="start_date" required>
        </div>
        
        <div class="form-group">
          <label for="electionEndDate">
            <i class="fas fa-calendar-check"></i> End Date & Time *
          </label>
          <input type="datetime-local" id="electionEndDate" name="end_date" required>
        </div>
      </div>
    </form>
    
    <div class="modal-footer">
      <button type="button" class="btn-cancel" onclick="closeElectionModal()">Cancel</button>
      <button type="button" class="btn-save" onclick="document.getElementById('electionForm').dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }))">
        <i class="fas fa-save"></i> Save Election
      </button>
    </div>
  </div>

  <div class="confirm-overlay" id="confirmOverlay"></div>
  <div class="confirm-modal" id="confirmModal">
    <div class="confirm-icon" id="confirmIcon">
      <i class="fas fa-exclamation-triangle"></i>
    </div>
    <h3 class="confirm-title" id="confirmTitle">Confirm Action</h3>
    <p class="confirm-message" id="confirmMessage">Are you sure you want to proceed?</p>
    <div class="confirm-details" id="confirmDetails"></div>
    <div class="confirm-footer">
      <button type="button" class="btn-confirm-cancel" onclick="closeConfirmModal()">Cancel</button>
      <button type="button" class="btn-confirm-ok" id="confirmOkBtn">OK</button>
    </div>
  </div>

  <div class="notification-overlay" id="notificationOverlay"></div>
  <div class="notification-modal" id="notificationModal">
    <div class="notification-header">
      <div class="notification-icon" id="notificationIcon">
        <i class="fas fa-check"></i>
      </div>
      <h3 class="notification-title" id="notificationTitle">Success!</h3>
      <p class="notification-message" id="notificationMessage">Operation completed successfully.</p>
    </div>
    <div class="notification-footer">
      <button type="button" class="btn-notification-close" onclick="closeNotification()">
        Got it
      </button>
    </div>
  </div>

  <div class="modal-overlay" id="loadingOverlay"></div>
  <div class="modal loading-modal" id="loadingModal">
    <div class="modal-header">
      <h2 class="modal-title">Processing</h2>
    </div>
    <div class="modal-content">
      <div class="spinner"></div>
      <p>Finalizing election and saving to blockchain...</p>
    </div>
  </div>

  <div class="modal-overlay" id="successOverlay"></div>
  <div class="modal success-modal" id="successModal">
    <div class="modal-header">
      <h2 class="modal-title">Election Finalized!</h2>
      <button type="button" class="modal-close" onclick="closeSuccessModal()">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <div class="modal-content">
      <div class="modal-icon">
        <i class="fas fa-check-circle"></i>
      </div>
      <p>Election has been finalized and saved to blockchain successfully.</p>
      <div class="transaction-details">
        <p><strong>Method:</strong> Election Finalization</p>
        <p><strong>Transaction Hash:</strong></p>
        <div class="transaction-hash" id="electionTxHash"></div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn-save" onclick="closeSuccessModal()">OK</button>
    </div>
  </div>

  <script src="../assets/js/elections.js"></script>
</body>
</html>

