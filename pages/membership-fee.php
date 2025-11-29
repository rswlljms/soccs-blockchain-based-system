<?php include('../components/sidebar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Membership Fee</title>
  <link rel="stylesheet" href="../assets/css/sidebar.css">
  <link rel="stylesheet" href="../assets/css/membership-fee.css">
  <link rel="stylesheet" href="../assets/css/admin-table-styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
  <div class="main-content">
    <div class="dashboard-wrapper">
      <div class="header-section">
        <h1 class="page-title">Membership Fee</h1>
        <div class="summary-cards">
          <div class="summary-card">
            <div class="card-icon"><i class="fas fa-users"></i></div>
            <div class="card-info">
              <h3>Total Students</h3>
              <p id="totalStudents">0</p>
            </div>
          </div>
          <div class="summary-card">
            <div class="card-icon"><i class="fas fa-check-circle"></i></div>
            <div class="card-info">
              <h3>Paid</h3>
              <p id="paidStudents">0</p>
            </div>
          </div>
          <div class="summary-card">
            <div class="card-icon"><i class="fas fa-piggy-bank"></i></div>
            <div class="card-info">
              <h3>Total Collected</h3>
              <p id="totalCollected">₱0.00</p>
            </div>
          </div>
        </div>
      </div>

      <div class="filters-section">
        <div class="filters-grid">
          <div class="input-group">
            <i class="fas fa-search"></i>
            <input type="text" id="search" placeholder="Search by name">
          </div>
          <div class="input-group">
            <i class="fas fa-graduation-cap"></i>
            <select id="course">
              <option value="All">All Courses</option>
              <option value="BSIT">BSIT</option>
            </select>
          </div>
          <div class="input-group">
            <i class="fas fa-layer-group"></i>
            <select id="year">
              <option value="All">All Year Levels</option>
              <option value="1">1st Year</option>
              <option value="2">2nd Year</option>
              <option value="3">3rd Year</option>
              <option value="4">4th Year</option>
            </select>
          </div>
          <div class="input-group">
            <i class="fas fa-chalkboard"></i>
            <input type="text" id="section" placeholder="Section (A, B, ...)">
          </div>
          <div class="input-group">
            <i class="fas fa-toggle-on"></i>
            <select id="status">
              <option value="All">All Status</option>
              <option value="paid">Paid</option>
              <option value="unpaid">Unpaid</option>
            </select>
          </div>
          <div class="filter-actions">
            <button id="applyFilters" class="btn-primary"><i class="fas fa-filter"></i> Apply</button>
            <button id="clearFilters" class="btn-secondary"><i class="fas fa-eraser"></i> Clear</button>
          </div>
        </div>
      </div>

      <div class="table-container">
        <table class="styled-table">
          <thead>
            <tr>
              <th>Student ID</th>
              <th>Name</th>
              <th>Course</th>
              <th>Year</th>
              <th>Section</th>
              <th>Status</th>
              <th>Receipt</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="membership-table-body"></tbody>
        </table>

        <div class="pagination centered">
          <a href="#" class="page-btn prev-btn" id="prevPage">&laquo; Prev</a>
          <span class="page-indicator" id="pageIndicator">Page 1 of 1</span>
          <a href="#" class="page-btn next-btn" id="nextPage">Next &raquo;</a>
        </div>
      </div>

      <!-- Section Summary (shown when filtering by section) -->
      <div class="section-summary" id="sectionSummaryWrapper" style="display:none;">
        <h2>Section Summary</h2>
        <div class="summary-grid" id="sectionSummary"></div>
      </div>
    </div>
  </div>

  <!-- Mark as Paid Modal -->
  <div class="modal-overlay" id="markPaidModalOverlay"></div>
  <div class="modal mark-paid-modal" id="markPaidModal">
    <div class="modal-header">
      <h2 class="modal-title">Mark as Paid</h2>
      <button type="button" class="modal-close" onclick="closeMarkPaidModal()">
        <i class="fas fa-times"></i>
      </button>
    </div>
    
    <form id="markPaidForm" class="mark-paid-form-modal">
      <div class="form-group">
        <label>
          <i class="fas fa-user"></i> Student Name
        </label>
        <input type="text" id="modalStudentName" readonly class="readonly-input">
      </div>
      
      <div class="form-group">
        <label>
          <i class="fas fa-coins"></i> Amount Paid *
        </label>
        <div class="amount-input-wrapper">
          <span class="currency-prefix">₱</span>
          <input type="number" id="modalAmountPaid" name="amount" step="0.01" min="0" placeholder="0.00" required>
        </div>
      </div>
      
      <div class="form-group">
        <label>
          <i class="fas fa-calendar"></i> Payment Date *
        </label>
        <input type="date" id="modalPaymentDate" name="payment_date" required>
      </div>
      
      <!-- Receipt Preview Section -->
      <div class="form-group form-group-full">
        <label>
          <i class="fas fa-receipt"></i> Receipt Preview
        </label>
        <div class="receipt-preview-container" id="receiptPreview">
          <!-- Receipt will be generated here -->
        </div>
      </div>
      
      <!-- Print Button - Outside scrollable area -->
      <div class="receipt-actions" id="receiptActions" style="display: none;">
        <button type="button" class="btn-print-receipt" onclick="printReceipt()">
          <i class="fas fa-print"></i> Print Receipt
        </button>
      </div>
    </form>
    
    <div class="modal-footer">
      <button type="button" class="btn-cancel" onclick="closeMarkPaidModal()">Cancel</button>
      <button type="button" class="btn-save" onclick="submitMarkAsPaid()">
        <i class="fas fa-check"></i> Confirm Payment
      </button>
    </div>
  </div>

  <script src="../assets/js/membership-fee.js"></script>
</body>
</html>


