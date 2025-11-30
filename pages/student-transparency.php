<?php
session_start();

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
?>

<?php include('../components/student-sidebar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Financial Transparency | SOCCS Student Portal</title>
  <link rel="stylesheet" href="../assets/css/sidebar.css">
  <link rel="stylesheet" href="../assets/css/student-mobile-first.css">
  <link rel="stylesheet" href="../assets/css/student-dashboard.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <style>
    .transparency-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      padding: 1.25rem 1.5rem;
      border-radius: 8px;
      margin-bottom: 1.5rem;
      color: white;
      text-align: center;
    }

    .transparency-header h1 {
      margin: 0 0 0.25rem 0;
      font-size: 1.5rem;
      font-weight: 700;
    }

    .transparency-header p {
      margin: 0;
      opacity: 0.95;
      font-size: 0.9rem;
    }

    .blockchain-verified-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      background: rgba(255, 255, 255, 0.2);
      padding: 0.4rem 0.8rem;
      border-radius: 50px;
      margin-top: 0.75rem;
      font-weight: 600;
      font-size: 0.85rem;
    }

    .results-section {
      background: white;
      padding: 1.25rem;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      margin-bottom: 1.5rem;
    }

    .results-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
      padding-bottom: 0.75rem;
      border-bottom: 1px solid #e5e7eb;
    }

    .results-header h2 {
      margin: 0;
      font-size: 1.1rem;
      color: #1f2937;
    }

    .results-count {
      color: #6b7280;
      font-size: 0.85rem;
    }

    .transaction-card {
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      padding: 1rem;
      margin-bottom: 0.75rem;
      transition: all 0.3s ease;
      background: white;
    }

    .transaction-card:hover {
      border-color: #667eea;
      box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
    }

    .transaction-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 0.75rem;
    }

    .transaction-title {
      font-weight: 600;
      font-size: 0.95rem;
      color: #1f2937;
      margin: 0;
    }

    .transaction-amount {
      font-size: 1.1rem;
      font-weight: 700;
      color: #ef4444;
    }

    .transaction-details {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
      margin-bottom: 0.75rem;
    }

    .detail-item {
      display: flex;
      flex-direction: column;
      gap: 0.2rem;
    }

    .detail-label {
      font-size: 0.75rem;
      color: #6b7280;
      font-weight: 600;
      text-transform: uppercase;
    }

    .detail-value {
      font-size: 0.9rem;
      color: #1f2937;
      font-weight: 500;
    }

    .blockchain-hash-section {
      background: #f9fafb;
      padding: 0.75rem;
      border-radius: 6px;
      margin-top: 0.75rem;
      border-left: 3px solid #10b981;
    }

    .blockchain-hash-label {
      display: flex;
      align-items: center;
      gap: 0.4rem;
      font-weight: 600;
      color: #1f2937;
      margin-bottom: 0.4rem;
      font-size: 0.85rem;
    }

    .blockchain-hash-label i {
      color: #10b981;
      font-size: 0.9rem;
    }

    .blockchain-hash-value {
      font-family: 'Courier New', monospace;
      font-size: 0.8rem;
      color: #4b5563;
      word-break: break-all;
      background: white;
      padding: 0.5rem;
      border-radius: 4px;
      border: 1px solid #e5e7eb;
    }

    .no-results {
      text-align: center;
      padding: 2rem;
      color: #6b7280;
    }

    .no-results i {
      font-size: 2.5rem;
      margin-bottom: 0.75rem;
      opacity: 0.5;
    }

    .no-results h3 {
      margin: 0 0 0.5rem 0;
      color: #374151;
      font-size: 1rem;
    }

    .pagination {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 0.75rem;
      margin-top: 1.5rem;
      padding-top: 1.5rem;
      border-top: 1px solid #e5e7eb;
    }

    .page-btn {
      padding: 0.5rem 1rem;
      background: white;
      border: 1px solid #e5e7eb;
      border-radius: 6px;
      color: #374151;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      font-size: 0.9rem;
    }

    .page-btn:hover:not(.disabled) {
      border-color: #667eea;
      color: #667eea;
      background: rgba(102, 126, 234, 0.05);
    }

    .page-btn.disabled {
      opacity: 0.5;
      cursor: not-allowed;
      pointer-events: none;
    }

    .page-indicator {
      font-weight: 600;
      color: #374151;
      font-size: 0.9rem;
    }


    @media (max-width: 768px) {
      .transparency-header h1 {
        font-size: 1.25rem;
      }

      .transaction-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
      }
    }
  </style>
</head>

<body>
  <div class="mobile-menu-toggle" id="mobileMenuToggle">
    <i class="fas fa-bars"></i>
  </div>
  
  <div class="mobile-overlay" id="mobileOverlay"></div>
  
  <div class="main-content">
    <div class="dashboard-wrapper">
      <div class="transparency-header">
        <h1><i class="fas fa-shield-alt"></i> Financial Transparency </h1>
        <p>All SOCCS Organization financial transactions are recorded and verified on the blockchain</p>
        <div class="blockchain-verified-badge">
          <i class="fas fa-check-circle"></i>
          <span>Blockchain Verified</span>
        </div>
      </div>

      <div class="results-section">
        <div class="results-header">
          <h2><i class="fas fa-list"></i> Financial Transactions</h2>
          <span class="results-count" id="resultsCount">Loading...</span>
        </div>
        
        <div id="transactionList"></div>

        <div class="pagination">
          <a href="#" class="page-btn prev-btn" id="prevBtn">
            <i class="fas fa-chevron-left"></i> Previous
          </a>
          <span class="page-indicator" id="pageIndicator">Page 1 of 1</span>
          <a href="#" class="page-btn next-btn" id="nextBtn">
            Next <i class="fas fa-chevron-right"></i>
          </a>
        </div>
      </div>

    </div>
  </div>

  <script>
    let currentPage = 1;
    const limit = 10;
    let totalPages = 1;

    async function loadTransactions(page = 1) {
      try {
        const url = new URL('../api/get_expenses.php', window.location.href);
        url.searchParams.set('page', page);
        url.searchParams.set('limit', limit);

        const response = await fetch(url);
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        if (!result.success) {
          throw new Error(result.message || 'Failed to load transactions');
        }

        const expenses = result.data;
        const total = result.total;
        totalPages = Math.ceil(total / limit);
        currentPage = Math.min(page, totalPages) || 1;

        document.getElementById('resultsCount').textContent = 
          `Showing ${expenses.length} of ${total} transactions`;

        const transactionList = document.getElementById('transactionList');
        transactionList.innerHTML = '';

        if (!expenses || expenses.length === 0) {
          transactionList.innerHTML = `
            <div class="no-results">
              <i class="fas fa-inbox"></i>
              <h3>No Transactions Found</h3>
              <p>Try adjusting your filters or search terms</p>
            </div>
          `;
          
          document.getElementById('pageIndicator').textContent = 'Page 1 of 1';
          document.getElementById('prevBtn').classList.add('disabled');
          document.getElementById('nextBtn').classList.add('disabled');
          return;
        }

        expenses.forEach(expense => {
          const card = document.createElement('div');
          card.className = 'transaction-card';
          card.innerHTML = `
            <div class="transaction-header">
              <h3 class="transaction-title">${expense.name}</h3>
              <div class="transaction-amount">-₱${parseFloat(expense.amount).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
            </div>
            
            <div class="transaction-details">
              <div class="detail-item">
                <span class="detail-label">Date</span>
                <span class="detail-value">${expense.date}</span>
              </div>
            </div>
            
            ${expense.transaction_hash ? `
            <div class="blockchain-hash-section">
              <div class="blockchain-hash-label">
                <i class="fas fa-shield-alt"></i>
                Transaction Hash
              </div>
              <div class="blockchain-hash-value">${expense.transaction_hash}</div>
            </div>
            ` : ''}
          `;
          transactionList.appendChild(card);
        });

        document.getElementById('pageIndicator').textContent = 
          `Page ${currentPage} of ${totalPages}`;
        
        document.getElementById('prevBtn').classList.toggle('disabled', currentPage <= 1);
        document.getElementById('nextBtn').classList.toggle('disabled', currentPage >= totalPages);

      } catch (error) {
        console.error('Error fetching transactions:', error);
        document.getElementById('transactionList').innerHTML = `
          <div class="no-results">
            <i class="fas fa-exclamation-triangle"></i>
            <h3>Error Loading Transactions</h3>
            <p>${error.message}</p>
          </div>
        `;
      }
    }

    document.getElementById('prevBtn').addEventListener('click', (e) => {
      e.preventDefault();
      if (currentPage > 1) {
        loadTransactions(currentPage - 1);
      }
    });

    document.getElementById('nextBtn').addEventListener('click', (e) => {
      e.preventDefault();
      if (currentPage < totalPages) {
        loadTransactions(currentPage + 1);
      }
    });

    document.addEventListener('DOMContentLoaded', () => {
      loadTransactions(currentPage);
    });
  </script>

  <script src="../assets/js/student-dashboard.js"></script>
</body>
</html>

