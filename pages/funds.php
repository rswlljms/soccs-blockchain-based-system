<?php include '../components/sidebar.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Funds</title>
  <link rel="stylesheet" href="../assets/css/funds.css"> 
  <link rel="stylesheet" href="../assets/css/sidebar.css">
  <link rel="stylesheet" href="../assets/css/admin-table-styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    .modal{padding:0;overflow:hidden}
    .modal-header{background:linear-gradient(135deg,#4B0082,#9933ff);padding:24px 32px;display:flex;justify-content:space-between;align-items:center;border-radius:16px 16px 0 0}
    .modal-title{font-size:22px;font-weight:600;color:#fff;margin:0;letter-spacing:-.02em}
    .modal-close{background:rgba(255,255,255,.2);border:none;font-size:18px;color:#fff;cursor:pointer;padding:8px;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;transition:all .2s}
    .modal-close:hover{background:rgba(255,255,255,.3);transform:rotate(90deg)}
    .form-body{padding:24px;max-height:calc(90vh - 120px);overflow-y:auto}
    .modal-footer{padding:24px 32px;border-top:1px solid #e5e7eb;display:flex;justify-content:flex-end;gap:14px;background:#fafbfc;border-radius:0 0 16px 16px}
  </style>
</head>
<body>

<div class="main-content">
  <div class="dashboard-wrapper">
    <h1 class="page-title">Add Funds</h1>

    <!-- Fund Form -->
    <form id="fund-form" class="fund-form" method="POST">
      <div class="form-row">
        <div class="input-group">
          <i class="fas fa-money-bill-wave"></i>
          <input type="number" name="amount" placeholder="Amount (₱)" required>
        </div>
        <div class="input-group">
          <i class="fas fa-align-left"></i>
          <input type="text" name="description" placeholder="Description" required>
        </div>
        <div class="input-group">
          <i class="fas fa-calendar-alt"></i>
          <input type="date" name="date" required>
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn-primary">
          <i class="fas fa-plus"></i> Add Funds
        </button>
      </div>
    </form>

    <!-- Records Section -->
    <div class="records-section">
      <div class="section-header">
        <h2>Funds Records</h2>
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

<!-- Confirmation Modal -->
<div class="modal-overlay" id="confirmOverlay"></div>
<div class="modal confirm-modal" id="confirmModal">
  <div class="modal-header">
    <h2 class="modal-title">Confirm Fund Addition</h2>
    <button type="button" class="modal-close" id="closeConfirm"><i class="fas fa-times"></i></button>
  </div>
  <div class="form-body">
    <p style="text-align:center;color:#666;margin:0;">Are you sure you want to add these funds?</p>
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
    <div class="spinner"></div>
    <p style="text-align:center;color:#666;">Recording your funds...</p>
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
    <p style="text-align:center;color:#666;">Funds have been recorded successfully</p>
    <div class="transaction-details">
      <p><strong>Method:</strong> Fund Addition</p>
      <p><strong>Transaction Hash:</strong></p>
      <div class="transaction-hash" id="txHash"></div>
    </div>
  </div>
  <div class="modal-footer">
    <button class="modal-btn modal-btn-primary" id="successOk">OK</button>
  </div>
</div>

<script src="../assets/js/funds.js"></script>
</body>
</html>
