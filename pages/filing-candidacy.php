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
  <title>Filing of Candidacy Management</title>
  <link rel="stylesheet" href="../assets/css/sidebar.css">
  <link rel="stylesheet" href="../assets/css/admin-table-styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../assets/css/elections.css">
</head>

<body>
  <div class="main-content">
    <h1 class="page-title">Filing of Candidacy Management</h1>
    
    <?php if (hasPermission('manage_election_status') || isAdviser()): ?>
    <div class="header-controls">
      <button class="btn-new" onclick="openFilingModal()">
        <i class="fas fa-plus"></i> New Filing Period
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
        <tbody id="filing-table-body">
        </tbody>
      </table>
      
      <div class="pagination centered">
        <button type="button" class="page-btn prev-btn" onclick="goToPage('prev')">&laquo; Prev</button>
        <span class="page-indicator">Page 1 of 1</span>
        <button type="button" class="page-btn next-btn" onclick="goToPage('next')">Next &raquo;</button>
      </div>
    </div>
  </div>

  <div class="modal-overlay" id="filingModalOverlay"></div>
  <div class="modal large" id="filingModal">
    <div class="modal-header">
      <h2 class="modal-title" id="modalTitle">Add Filing Period</h2>
      <button type="button" class="modal-close" onclick="closeFilingModal()">
        <i class="fas fa-times"></i>
      </button>
    </div>
    
    <form id="filingForm" class="election-form-modal">
      <div class="form-group">
        <label for="filingTitle">
          <i class="fas fa-file-alt"></i> Title *
        </label>
        <input type="text" id="filingTitle" name="title" placeholder="e.g., Filing of Candidacy for AY 2025-2026" required>
      </div>
      
      <div class="form-group">
        <label for="filingAnnouncement">
          <i class="fas fa-bullhorn"></i> Announcement Text *
        </label>
        <textarea id="filingAnnouncement" name="announcement_text" rows="12" placeholder="Enter the announcement text that will be displayed to students..." required></textarea>
        <small>You can include information about positions, deadlines, screening dates, etc.</small>
      </div>
      
      <div class="form-group">
        <label for="filingFormLink">
          <i class="fas fa-link"></i> Form Link (JotForm URL) *
        </label>
        <input type="url" id="filingFormLink" name="form_link" placeholder="https://form.jotform.com/..." required>
      </div>
      
      <div class="form-row">
        <div class="form-group">
          <label for="filingStartDate">
            <i class="fas fa-calendar-alt"></i> Start Date & Time *
          </label>
          <input type="datetime-local" id="filingStartDate" name="start_date" required>
        </div>
        
        <div class="form-group">
          <label for="filingEndDate">
            <i class="fas fa-calendar-check"></i> End Date & Time *
          </label>
          <input type="datetime-local" id="filingEndDate" name="end_date" required>
        </div>
      </div>
      
      <div class="form-group">
        <label for="filingScreeningDate">
          <i class="fas fa-calendar-day"></i> Screening Date (Optional)
        </label>
        <input type="text" id="filingScreeningDate" name="screening_date" placeholder="e.g., September 1-2, 2025">
      </div>
      
      <div class="form-group">
        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
          <input type="checkbox" id="filingIsActive" name="is_active" value="1">
          <span>Activate this filing period (only one can be active at a time)</span>
        </label>
      </div>
    </form>
    
    <div class="modal-footer">
      <button type="button" class="btn-cancel" onclick="closeFilingModal()">Cancel</button>
      <button type="button" class="btn-save" onclick="saveFilingPeriod()">
        <i class="fas fa-save"></i> Save Filing Period
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

  <script src="../assets/js/filing-candidacy.js"></script>
</body>
</html>

