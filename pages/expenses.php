<?php
session_start();
require_once '../includes/page_access.php';
require_once '../includes/auth_check.php';
checkPageAccess(['view_expenses', 'manage_expenses', 'view_financial_records']);
include('../components/sidebar.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Expenses</title>
  <link rel="stylesheet" href="../assets/css/sidebar.css">
  <link rel="stylesheet" href="../assets/css/expenses.css">
  <link rel="stylesheet" href="../assets/css/admin-table-styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
  <div class="main-content">
    <div class="dashboard-wrapper">
      <div class="header-section">
        <h1 class="page-title">Expenses</h1>
        <div class="summary-cards">
          <div class="summary-card">
            <div class="card-icon">
              <i class="fas fa-receipt"></i>
            </div>
            <div class="card-info">
              <h3>Total Expenses</h3>
              <p id="totalExpenses">0</p>
            </div>
          </div>
          <div class="summary-card">
            <div class="card-icon">
              <i class="fas fa-coins"></i>
            </div>
            <div class="card-info">
              <h3>Total Amount</h3>
              <p id="totalAmount">₱0.00</p>
            </div>
          </div>
          <div class="summary-card">
            <div class="card-icon">
              <i class="fas fa-chart-pie"></i>
            </div>
            <div class="card-info">
              <h3>Top Category</h3>
              <p id="topCategory">-</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Filter Toolbar -->
      <div class="expense-toolbar">
        <div class="filter-section">
          <label for="filter-category">Filter by Category:</label>
          <select id="filter-category">
            <option value="All">All Categories</option>
            <option value="FOOD AND DRINKS">FOOD AND DRINKS</option>
            <option value="TRANSPORT">TRANSPORT</option>
            <option value="OFFICE SUPPLIES">OFFICE SUPPLIES</option>
            <option value="EVENT EXPENSES">EVENT EXPENSES</option>
            <option value="TOKEN/GIVEAWAY">TOKEN/GIVEAWAY</option>
            <option value="CLEANING MATERIALS">CLEANING MATERIALS</option>
          </select>
        </div>
        <div class="toolbar-actions">
          <?php if (hasPermission('manage_expenses')): ?>
          <button type="button" class="btn-add-expense" id="openExpenseModal">
            <i class="fas fa-plus"></i> Add Expense
          </button>
          <?php endif; ?>
          <button class="btn-print" onclick="printExpensesReport()">
            <i class="fas fa-print"></i> Print Report
          </button>
        </div>
      </div>

      <!-- Add Expense Modal -->
      <div class="modal-overlay" id="expenseModalOverlay"></div>
      <div class="modal expense-modal" id="expenseModal">
        <div class="modal-header">
          <h2 class="modal-title">Add New Expense</h2>
          <button type="button" class="modal-close" id="closeExpenseModal">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <form id="expense-form" class="expense-form-modal" enctype="multipart/form-data" method="POST">
          <div class="form-grid">
            <div class="form-group">
              <label for="expense-name">
                <i class="fas fa-receipt"></i> Expense Name
              </label>
              <input type="text" id="expense-name" name="name" placeholder="Enter expense name" required>
              <span class="error-message"></span>
            </div>

            <div class="form-group">
              <label for="expense-amount">
                <i class="fas fa-coins"></i> Amount
              </label>
              <div class="amount-input-wrapper">
                <span class="currency-prefix">₱</span>
                <input type="number" id="expense-amount" name="amount" step="0.01" min="0" placeholder="0.00" required>
              </div>
              <span class="error-message"></span>
            </div>

            <div class="form-group">
              <label for="expense-category">
                <i class="fas fa-tags"></i> Category
              </label>
              <select id="expense-category" name="category" required>
                <option value="" disabled selected>Select category</option>
                <option value="FOOD AND DRINKS">FOOD AND DRINKS</option>
                <option value="TRANSPORT">TRANSPORT</option>
                <option value="OFFICE SUPPLIES">OFFICE SUPPLIES</option>
                <option value="EVENT EXPENSES">EVENT EXPENSES</option>
                <option value="TOKEN/GIVEAWAY">TOKEN/GIVEAWAY</option>
                <option value="CLEANING MATERIALS">CLEANING MATERIALS</option>
              </select>
              <span class="error-message"></span>
            </div>

            <div class="form-group">
              <label for="expense-date">
                <i class="fas fa-calendar-alt"></i> Date
              </label>
              <input type="date" id="expense-date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
              <span class="error-message"></span>
            </div>

            <div class="form-group form-group-full">
              <label for="expense-description">
                <i class="fas fa-align-left"></i> Description
              </label>
              <textarea id="expense-description" name="description" rows="3" placeholder="Enter description" required></textarea>
              <span class="error-message"></span>
            </div>

            <div class="form-group form-group-full">
              <label for="expense-supplier">
                <i class="fas fa-user"></i> Supplier
              </label>
              <input type="text" id="expense-supplier" name="supplier" placeholder="Enter supplier name" required>
              <span class="error-message"></span>
            </div>

            <div class="form-group form-group-full">
              <label for="expense-document">
                <i class="fas fa-paperclip"></i> Upload Document
              </label>
              <div class="file-upload-wrapper">
                <input type="file" id="expense-document" name="document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                <label for="expense-document" class="file-upload-label">
                  <i class="fas fa-cloud-upload-alt"></i>
                  <span class="file-upload-text">Choose file or drag here</span>
                </label>
                <div class="file-preview" id="filePreview"></div>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn-cancel" id="cancelExpenseForm">Cancel</button>
            <button type="submit" class="btn-save">Save Expense</button>
          </div>
        </form>
      </div>

      <!-- Toast Notification -->
      <div class="toast" id="toast">
        <div class="toast-content">
          <i class="fas fa-check-circle toast-icon"></i>
          <span class="toast-message"></span>
        </div>
      </div>

      <!-- Expense Table -->
      <div class="table-container">
        <table class="styled-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Amount</th>
              <th>Category</th>
              <th>Description</th>
              <th>Supplier</th>
              <th>Document</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody id="expense-table-body">
            <!-- Existing rows will go here -->
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination centered">
        <a href="#" class="page-btn prev-btn">&laquo; Prev</a>
        <span class="page-indicator">Page 1 of 1</span>
        <a href="#" class="page-btn next-btn">Next &raquo;</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Add this after the body tag -->
  <div class="preloader-overlay">
    <div class="preloader">
      <div class="preloader-spinner"></div>
      <div class="preloader-text">Recording expense to blockchain...</div>
    </div>
  </div>

  <!-- Loading Modal -->
  <div class="modal-overlay" id="loadingOverlay"></div>
  <div class="modal loading-modal" id="loadingModal">
    <div class="modal-content">
      <div class="spinner"></div>
      <h3 class="modal-title">Processing</h3>
      <p>Recording your expense...</p>
    </div>
  </div>

  <!-- Confirmation Modal -->
  <div class="modal-overlay" id="confirmOverlay"></div>
  <div class="modal confirm-modal" id="confirmModal">
    <div class="modal-content">
      <i class="fas fa-question-circle modal-icon"></i>
      <h3 class="modal-title">Confirm Expense Recording</h3>
      <p>Are you sure you want to record this expense?</p>
      <div class="modal-buttons">
        <button class="modal-btn modal-btn-secondary" id="cancelExpense">Cancel</button>
        <button class="modal-btn modal-btn-primary" id="confirmExpense">Confirm</button>
      </div>
    </div>
  </div>

  <!-- Success Modal -->
  <div class="modal-overlay" id="successOverlay"></div>
  <div class="modal success-modal" id="successModal">
    <div class="modal-content">
      <i class="fas fa-check-circle modal-icon"></i>
      <h3 class="modal-title">Success!</h3>
      <p>Expense has been recorded successfully</p>
      <div class="transaction-details">
        <p><strong>Method:</strong> Expense</p>
        <p><strong>Transaction Hash:</strong></p>
        <div class="transaction-hash" id="txHash"></div>
      </div>
      <div class="modal-buttons">
        <button class="modal-btn modal-btn-primary" id="successOk">OK</button>
      </div>
    </div>
  </div>

  <style>
    .transaction-details {
      margin: 15px 0;
      padding: 10px;
      background: #f8f9fa;
      border-radius: 6px;
      text-align: left;
    }

    .transaction-details p {
      margin: 5px 0;
      color: #374151;
    }

    .transaction-details strong {
      color: #1f2937;
    }

    .transaction-hash {
      word-break: break-all;
      font-family: monospace;
      padding: 8px;
      background: #e5e7eb;
      border-radius: 4px;
      margin-top: 5px;
      font-size: 12px;
    }

    .page-btn {
      background: white;
      border: 1px solid var(--border-color);
      padding: 8px 16px;
      border-radius: var(--radius-sm);
      cursor: pointer;
      font-size: 14px;
      color: var(--text-primary);
      text-decoration: none;
      transition: var(--transition);
      pointer-events: auto;
    }

    .page-btn:hover:not(.disabled) {
      border-color: #9933ff;
      color: #9933ff;
      background-color: rgba(153, 51, 255, 0.05);
    }

    .page-btn.disabled {
      opacity: 0.5;
      cursor: not-allowed;
      pointer-events: none;
    }

    .page-indicator {
      font-size: 14px;
      color: var(--text-secondary);
      padding: 0 16px;
    }
  </style>

  <script>
    let currentPage = 1;
    const limit = 6;

    // Modal handling
    const expenseModal = document.getElementById('expenseModal');
    const expenseModalOverlay = document.getElementById('expenseModalOverlay');
    const openExpenseModalBtn = document.getElementById('openExpenseModal');
    const closeExpenseModalBtn = document.getElementById('closeExpenseModal');
    const cancelExpenseFormBtn = document.getElementById('cancelExpenseForm');
    const expenseForm = document.getElementById('expense-form');

    function openExpenseModal() {
      expenseModal.classList.add('show');
      expenseModalOverlay.classList.add('show');
      document.body.style.overflow = 'hidden';
    }

    function closeExpenseModal() {
      expenseModal.classList.remove('show');
      expenseModalOverlay.classList.remove('show');
      document.body.style.overflow = '';
      expenseForm.reset();
      clearFormErrors();
      document.getElementById('filePreview').innerHTML = '';
    }

    if (openExpenseModalBtn) {
      openExpenseModalBtn.addEventListener('click', openExpenseModal);
    }
    if (closeExpenseModalBtn) {
      closeExpenseModalBtn.addEventListener('click', closeExpenseModal);
    }
    if (cancelExpenseFormBtn) {
      cancelExpenseFormBtn.addEventListener('click', closeExpenseModal);
    }
    if (expenseModalOverlay) {
      expenseModalOverlay.addEventListener('click', closeExpenseModal);
    }

    // Handle file input styling and preview
    const fileInput = document.getElementById('expense-document');
    const filePreview = document.getElementById('filePreview');
    const fileUploadLabel = document.querySelector('.file-upload-label .file-upload-text');

    fileInput.addEventListener('change', function() {
      if (this.files.length > 0) {
        const fileName = this.files[0].name;
        fileUploadLabel.textContent = fileName;
        filePreview.innerHTML = `
          <div class="file-preview-item">
            <i class="fas fa-file-alt"></i>
            <span>${fileName}</span>
            <button type="button" class="file-remove" id="removeFileBtn">
              <i class="fas fa-times"></i>
            </button>
          </div>
        `;
        
        document.getElementById('removeFileBtn').addEventListener('click', removeFile);
      }
    });

    function removeFile() {
      fileInput.value = '';
      fileUploadLabel.textContent = 'Choose file or drag here';
      filePreview.innerHTML = '';
    }

    // Form validation
    function validateForm() {
      let isValid = true;
      const requiredFields = expenseForm.querySelectorAll('[required]');
      
      requiredFields.forEach(field => {
        const formGroup = field.closest('.form-group');
        const errorMessage = formGroup.querySelector('.error-message');
        
        if (!field.value || (field.type === 'select-one' && !field.value)) {
          field.classList.add('error');
          errorMessage.textContent = 'This field is required';
          isValid = false;
        } else {
          field.classList.remove('error');
          errorMessage.textContent = '';
        }

        // Additional validation for amount
        if (field.id === 'expense-amount' && field.value) {
          const amount = parseFloat(field.value);
          if (isNaN(amount) || amount <= 0) {
            field.classList.add('error');
            errorMessage.textContent = 'Amount must be greater than 0';
            isValid = false;
          }
        }
      });

      return isValid;
    }

    function clearFormErrors() {
      expenseForm.querySelectorAll('.error').forEach(field => {
        field.classList.remove('error');
      });
      expenseForm.querySelectorAll('.error-message').forEach(msg => {
        msg.textContent = '';
      });
    }

    expenseForm.querySelectorAll('input, select, textarea').forEach(field => {
      field.addEventListener('blur', function() {
        if (this.hasAttribute('required') && !this.value) {
          this.classList.add('error');
          this.closest('.form-group').querySelector('.error-message').textContent = 'This field is required';
        } else {
          this.classList.remove('error');
          this.closest('.form-group').querySelector('.error-message').textContent = '';
        }
      });

      field.addEventListener('input', function() {
        if (this.classList.contains('error') && this.value) {
          this.classList.remove('error');
          this.closest('.form-group').querySelector('.error-message').textContent = '';
        }
      });
    });

    // Toast notification
    function showToast(message) {
      const toast = document.getElementById('toast');
      const toastMessage = toast.querySelector('.toast-message');
      toastMessage.textContent = message;
      toast.classList.add('show');
      
      setTimeout(() => {
        toast.classList.remove('show');
      }, 3000);
    }

    function updateSummaryCards(summary) {
        if (summary) {
            document.getElementById('totalExpenses').textContent = summary.total_count || 0;
            const totalAmount = parseFloat(summary.total_amount) || 0;
            document.getElementById('totalAmount').textContent = `₱${totalAmount.toFixed(2)}`;
            document.getElementById('topCategory').textContent = summary.top_category || '-';
        }
    }

    async function loadExpenses(page = 1) {
        try {
            const requestedPage = page;
            
            const filterCategory = document.getElementById('filter-category').value;
            const url = new URL('../api/get_expenses.php', window.location.href);
            url.searchParams.set('page', requestedPage);
            url.searchParams.set('limit', limit);
            if (filterCategory !== 'All') {
                url.searchParams.set('category', filterCategory);
            }

            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            if (!result.success) {
                throw new Error(result.message || 'Failed to load expenses');
            }

            const expenses = result.data;
            const total = result.total;
            const totalPages = Math.ceil(total / limit);

            if (result.summary) {
                updateSummaryCards(result.summary);
            }

            // Update current page based on the response
            currentPage = Math.min(requestedPage, totalPages);
            if (currentPage < 1) currentPage = 1;
            
            const tableBody = document.getElementById('expense-table-body');
            tableBody.innerHTML = '';

            if (!expenses || expenses.length === 0) {
                const emptyRow = document.createElement('tr');
                emptyRow.innerHTML = `
                    <td colspan="7" style="text-align: center; padding: 20px;">
                        ${filterCategory === 'All' ? 
                            'No expenses recorded yet' : 
                            `No expenses found for category: ${filterCategory}`}
                    </td>
                `;
                tableBody.appendChild(emptyRow);
                
                // Reset pagination for empty results
                document.querySelector('.page-indicator').textContent = 'Page 1 of 1';
                document.querySelector('.prev-btn').classList.add('disabled');
                document.querySelector('.next-btn').classList.add('disabled');
                return;
            }

            expenses.forEach(expense => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${expense.name}</td>
                    <td>₱${parseFloat(expense.amount).toFixed(2)}</td>
                    <td>${expense.category}</td>
                    <td>${expense.description}</td>
                    <td>${expense.supplier}</td>
                    <td>${expense.document ? 
                        `<a href="../api/view_document.php?filename=${expense.document}" 
                            target="_blank" 
                            class="document-link">
                            <i class="fas fa-file-alt"></i> View Document
                        </a>` : 
                        '-'}
                    </td>
                    <td>${expense.date}</td>
                `;
                tableBody.appendChild(row);
            });

            // Update pagination
            document.querySelector('.page-indicator').textContent = `Page ${currentPage} of ${totalPages}`;

            // Function to handle pagination clicks
            function handlePaginationClick(direction) {
                return function(e) {
                    e.preventDefault();
                    if (direction === 'prev' && currentPage > 1) {
                        loadExpenses(currentPage - 1);
                    } else if (direction === 'next' && currentPage < totalPages) {
                        loadExpenses(currentPage + 1);
                    }
                };
            }

            // Update pagination buttons
            const prevBtn = document.querySelector('.prev-btn');
            const nextBtn = document.querySelector('.next-btn');

            // Remove old event listeners and clone
            const newPrevBtn = prevBtn.cloneNode(true);
            const newNextBtn = nextBtn.cloneNode(true);

            // Replace old buttons
            prevBtn.parentNode.replaceChild(newPrevBtn, prevBtn);
            nextBtn.parentNode.replaceChild(newNextBtn, nextBtn);

            // Add event listeners
            newPrevBtn.addEventListener('click', handlePaginationClick('prev'));
            newNextBtn.addEventListener('click', handlePaginationClick('next'));

            // Update button states
            newPrevBtn.classList.toggle('disabled', currentPage <= 1);
            newNextBtn.classList.toggle('disabled', currentPage >= totalPages);

            // Remove pointer-events: none if button should be clickable
            if (currentPage > 1) {
                newPrevBtn.style.pointerEvents = 'auto';
            }
            if (currentPage < totalPages) {
                newNextBtn.style.pointerEvents = 'auto';
            }
        } catch (error) {
            console.error('Error fetching expenses:', error);
            // Handle errors gracefully
        }
    }

    document.addEventListener('DOMContentLoaded', () => loadExpenses(currentPage));

    // Function to show/hide loading modal
    function toggleLoadingModal(show) {
      const modal = document.querySelector('.modal-loading');
      const overlay = document.querySelector('.modal-overlay');
      modal.style.display = show ? 'block' : 'none';
      overlay.style.display = show ? 'block' : 'none';
    }

    // Modal handling functions
    function showModal(modalId) {
      const modal = document.getElementById(modalId);
      const overlay = document.getElementById(modalId.replace('Modal', 'Overlay'));
      if (modal && overlay) {
        modal.classList.add('show');
        overlay.classList.add('show');
      }
    }

    function hideModal(modalId) {
      const modal = document.getElementById(modalId);
      const overlay = document.getElementById(modalId.replace('Modal', 'Overlay'));
      if (modal && overlay) {
        modal.classList.remove('show');
        overlay.classList.remove('show');
      }
    }

    // Form submission handler
    expenseForm.addEventListener('submit', async function(event) {
      event.preventDefault();
      
      if (!validateForm()) {
        showToast('Please fill in all required fields correctly');
        return;
      }
      
      try {
        const formData = new FormData(this);
        const expenseData = {
          name: formData.get('name'),
          description: formData.get('description'),
          amount: formData.get('amount'),
          category: formData.get('category'),
          supplier: formData.get('supplier'),
          date: formData.get('date') || new Date().toISOString().split('T')[0],
          method: 'EXPENSE'
        };

        // Show confirmation modal
        showModal('confirmModal');

        // Handle confirmation button click
        const confirmBtn = document.getElementById('confirmExpense');
        const handleConfirm = async () => {
          try {
            // Remove the event listener to prevent multiple submissions
            confirmBtn.removeEventListener('click', handleConfirm);
            
            hideModal('confirmModal');
            showModal('loadingModal');

            // Send to blockchain
            while (!AppConfig.initialized) {
              await new Promise(resolve => setTimeout(resolve, 50));
            }
            const blockchainResponse = await fetch(`${AppConfig.blockchainUrl}/add-expenses`, {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify(expenseData)
            });

            const blockchainResult = await blockchainResponse.json();
            console.log('Blockchain result:', blockchainResult);

            if (blockchainResult.status === 'success') {
              // Prepare form data for database
              const dbData = new FormData();
              
              // Add all expense data
              Object.keys(expenseData).forEach(key => {
                dbData.append(key, expenseData[key]);
              });
              
              // Add transaction hash
              dbData.append('transaction_hash', blockchainResult.txHash);

              // Add file if present
              const fileInput = this.querySelector('input[type="file"]');
              if (fileInput.files.length > 0) {
                dbData.append('document', fileInput.files[0]);
              }

              const dbResponse = await fetch('../api/save_expense.php', {
                method: 'POST',
                body: dbData
              });

              if (!dbResponse.ok) {
                throw new Error(`HTTP error! status: ${dbResponse.status}`);
              }

              const dbResult = await dbResponse.json();
              console.log('Database result:', dbResult);

              if (dbResult.success) {
                hideModal('loadingModal');
                document.getElementById('txHash').textContent = blockchainResult.txHash;
                showModal('successModal');
                this.reset();
                closeExpenseModal();
                await loadExpenses();
                showToast('Expense added successfully');
              } else {
                throw new Error(dbResult.message || 'Failed to save to database');
              }
            } else {
              throw new Error(blockchainResult.message || 'Blockchain transaction failed');
            }
          } catch (err) {
            console.error('Error in confirmation handler:', err);
            hideModal('loadingModal');
            alert('Error: ' + err.message);
          }
        };

        // Add event listener for confirmation
        confirmBtn.addEventListener('click', handleConfirm, { once: true });

        // Handle cancel button click
        document.getElementById('cancelExpense').onclick = () => {
          hideModal('confirmModal');
          // Re-enable confirm button for future submissions
          confirmBtn.removeEventListener('click', handleConfirm);
        };
      } catch (err) {
        console.error('Error in form submission:', err);
        alert('Error: ' + err.message);
      }
    });

    // Handle success modal close
    document.getElementById('successOk').onclick = () => hideModal('successModal');

    // Filter handling
    document.getElementById('filter-category').addEventListener('change', function() {
        currentPage = 1;
        loadExpenses(currentPage);
    });

    function printExpensesReport() {
        const filterCategory = document.getElementById('filter-category').value;
        const params = new URLSearchParams();
        
        if (filterCategory && filterCategory !== 'All') {
            params.append('category', filterCategory);
        }
        
        window.open(`print-expenses-report-pdf.php?${params.toString()}`, '_blank');
    }
  </script>

  <script src="../assets/js/config.js"></script>
  <script src="../assets/js/expenses.js"></script>
</body>
</html>
