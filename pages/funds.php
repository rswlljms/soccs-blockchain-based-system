<?php
session_start();
require_once '../includes/page_access.php';
require_once '../includes/auth_check.php';
checkPageAccess(['view_funds', 'manage_funds', 'view_financial_records']);
include '../components/sidebar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Budget</title>
  <link rel="stylesheet" href="../assets/css/funds.css"> 
  <link rel="stylesheet" href="../assets/css/sidebar.css">
  <link rel="stylesheet" href="../assets/css/admin-table-styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<div class="main-content">
  <div class="dashboard-wrapper">
    <div class="header-section">
      <h1 class="page-title">Budget</h1>
      <div class="summary-cards">
        <div class="summary-card">
          <div class="card-icon">
            <i class="fas fa-wallet"></i>
          </div>
          <div class="card-info">
            <h3>Total Budget</h3>
            <p id="totalFunds">₱0.00</p>
          </div>
        </div>
        <div class="summary-card">
          <div class="card-icon">
            <i class="fas fa-plus-circle"></i>
          </div>
          <div class="card-info">
            <h3>Total Records</h3>
            <p id="totalRecords">0</p>
          </div>
        </div>
        <div class="summary-card">
          <div class="card-icon">
            <i class="fas fa-calendar-check"></i>
          </div>
          <div class="card-info">
            <h3>This Month</h3>
            <p id="monthlyTotal">₱0.00</p>
          </div>
        </div>
      </div>
      </div>

    <!-- Filter Toolbar -->
    <div class="funds-toolbar">
        <div class="filter-section">
          <label for="filter-date">Filter by Date:</label>
          <select id="filter-date">
            <option value="All">All Time</option>
            <option value="Today">Today</option>
            <option value="Week">This Week</option>
            <option value="Month">This Month</option>
            <option value="Year">This Year</option>
          </select>
        </div>
      <div class="toolbar-actions">
        <?php if (hasPermission('manage_funds')): ?>
        <button type="button" class="btn-add-funds" id="openFundModal">
          <i class="fas fa-plus"></i> Add Budget
        </button>
        <?php endif; ?>
        <button class="btn-print" onclick="printFundsReport()">
          <i class="fas fa-print"></i> Print Report
        </button>
      </div>
    </div>

    <!-- Records Section -->
    <div class="records-section">
      <div class="section-header">
        <h2>Budget Records</h2>
      </div>

      <!-- Table Container -->
      <div class="table-container">
        <table class="styled-table">
          <thead>
            <tr>
              <th>Amount</th>
              <th>Description</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody id="funds-table-body">
            <tr>
              <td class="amount-cell">₱10,000.00</td>
              <td>Initial Funding</td>
              <td>2025-04-22</td>
            </tr>
            <tr>
              <td class="amount-cell">₱5,000.00</td>
              <td>Sponsor Contribution</td>
              <td>2025-04-23</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="pagination centered">
        <a href="#" class="page-btn prev-btn">&laquo; Prev</a>
        <span class="page-indicator">Page 1 of 1</span>
        <a href="#" class="page-btn next-btn">Next &raquo;</a>
      </div>
    </div>
  </div>
</div>

<!-- Add Budget Modal -->
<div class="modal-overlay" id="fundModalOverlay"></div>
<div class="modal fund-modal" id="fundModal">
  <div class="modal-header">
    <h2 class="modal-title">Add New Budget</h2>
    <button type="button" class="modal-close" id="closeFundModal">
      <i class="fas fa-times"></i>
    </button>
  </div>
  <form id="fund-form" class="fund-form-modal" method="POST">
    <div class="form-grid">
      <div class="form-group">
        <label for="fund-amount">
          <i class="fas fa-money-bill-wave"></i> Amount
        </label>
        <div class="amount-input-wrapper">
          <span class="currency-prefix">₱</span>
          <input type="number" id="fund-amount" name="amount" step="0.01" min="0" placeholder="0.00" required>
        </div>
        <span class="error-message"></span>
      </div>

      <div class="form-group">
        <label for="fund-date">
          <i class="fas fa-calendar-alt"></i> Date
        </label>
        <input type="date" id="fund-date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
        <span class="error-message"></span>
      </div>

      <div class="form-group form-group-full">
        <label for="fund-description">
          <i class="fas fa-align-left"></i> Description
        </label>
        <textarea id="fund-description" name="description" rows="3" placeholder="Enter description" required></textarea>
        <span class="error-message"></span>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn-cancel" id="cancelFundForm">Cancel</button>
      <button type="submit" class="btn-save">
        <i class="fas fa-save"></i> Add Budget
      </button>
    </div>
  </form>
</div>

<!-- Confirmation Modal -->
<div class="modal-overlay" id="confirmOverlay"></div>
<div class="modal confirm-modal" id="confirmModal">
  <div class="modal-header">
    <h2 class="modal-title">Confirm Budget Addition</h2>
    <button type="button" class="modal-close" id="closeConfirm"><i class="fas fa-times"></i></button>
  </div>
  <div class="form-body">
    <div class="modal-content">
      <div class="modal-icon">
        <i class="fas fa-question-circle"></i>
      </div>
      <p>Are you sure you want to add this budget?</p>
    </div>
  </div>
  <div class="modal-footer">
    <button class="modal-btn modal-btn-secondary" id="cancelFund">Cancel</button>
    <button class="modal-btn modal-btn-primary" id="confirmFund">Confirm</button>
  </div>
</div>

<!-- Loading Modal -->
<div class="modal-overlay" id="loadingOverlay"></div>
<div class="modal loading-modal" id="loadingModal">
  <div class="modal-header">
    <h2 class="modal-title">Processing</h2>
  </div>
  <div class="form-body">
    <div class="modal-content">
    <div class="spinner"></div>
      <p>Recording your budget...</p>
    </div>
  </div>
</div>

<!-- Success Modal -->
<div class="modal-overlay" id="successOverlay"></div>
<div class="modal success-modal" id="successModal">
  <div class="modal-header">
    <h2 class="modal-title">Success!</h2>
    <button type="button" class="modal-close" id="closeSuccess"><i class="fas fa-times"></i></button>
  </div>
  <div class="form-body">
    <div class="modal-content">
      <div class="modal-icon">
        <i class="fas fa-check-circle"></i>
      </div>
      <p>Budget has been recorded successfully</p>
    <div class="transaction-details">
      <p><strong>Method:</strong> Budget Addition</p>
      <p><strong>Transaction Hash:</strong></p>
      <div class="transaction-hash" id="txHash"></div>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button class="modal-btn modal-btn-primary" id="successOk">OK</button>
  </div>
</div>

<script>
  function printFundsReport() {
    const filterDate = document.getElementById('filter-date').value;
    const params = new URLSearchParams();
    
    if (filterDate && filterDate !== 'All') {
      params.append('date_filter', filterDate);
    }
    
    window.open(`print-funds-report-pdf.php?${params.toString()}`, '_blank');
  }

  document.addEventListener('DOMContentLoaded', function() {

    const fundModal = document.getElementById('fundModal');
    const fundModalOverlay = document.getElementById('fundModalOverlay');
    const openFundModal = document.getElementById('openFundModal');
    const closeFundModal = document.getElementById('closeFundModal');
    const cancelFundForm = document.getElementById('cancelFundForm');

    function openModal() {
      fundModal.classList.add('show');
      fundModalOverlay.classList.add('show');
      document.body.style.overflow = 'hidden';
    }

    function closeModal() {
      fundModal.classList.remove('show');
      fundModalOverlay.classList.remove('show');
      document.body.style.overflow = '';
      document.getElementById('fund-form').reset();
    }

    if (openFundModal) {
      openFundModal.addEventListener('click', openModal);
    }
    if (closeFundModal) {
      closeFundModal.addEventListener('click', closeModal);
    }
    if (cancelFundForm) {
      cancelFundForm.addEventListener('click', closeModal);
    }
    if (fundModalOverlay) {
      fundModalOverlay.addEventListener('click', closeModal);
    }
  });
</script>
<script src="../assets/js/config.js"></script>
<script src="../assets/js/funds.js"></script>
</body>
</html>
