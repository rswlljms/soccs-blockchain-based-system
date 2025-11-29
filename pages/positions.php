<?php include('../components/sidebar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Election Positions</title>
  <link rel="stylesheet" href="../assets/css/sidebar.css">
  <link rel="stylesheet" href="../assets/css/admin-table-styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    :root {
      --primary-gradient: linear-gradient(135deg, #4B0082, #9933ff);
      --primary-hover: linear-gradient(135deg, #3a0066, #7a29cc);
      --text-primary: #1f2937;
      --text-secondary: #4b5563;
      --border-color: #e5e7eb;
      --secondary-color: #f9fafb;
      --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
      --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
      --radius-sm: 8px;
      --radius-md: 12px;
    }
    
    body {
      font-family: 'Work Sans', sans-serif;
      background-color: #f6f7fb;
      margin: 0;
      padding: 0;
      color: var(--text-primary);
    }
    
    .main-content {
      margin-left: 280px !important;
      width: calc(100% - 280px) !important;
      padding: 40px 32px 32px 32px;
      min-height: 100vh;
      background-color: #f6f7fb;
      position: relative;
      box-sizing: border-box;
    }
    
    .page-title {
      font-size: 32px;
      font-weight: 700;
      margin-bottom: 32px;
      color: var(--text-primary);
      position: relative;
      padding-bottom: 14px;
    }
    
    .page-title::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 60px;
      height: 4px;
      background: var(--primary-gradient);
      border-radius: 2px;
    }

    .header-controls {
      background: white;
      padding: 20px 24px;
      border-radius: 12px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      display: flex;
      justify-content: flex-end;
      align-items: center;
      margin-bottom: 24px;
    }

    .btn-new {
      background: var(--primary-gradient);
      color: white;
      border: none;
      border-radius: 8px;
      padding: 12px 24px;
      font-weight: 600;
      font-size: 14px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: all 0.2s;
      box-shadow: 0 2px 4px rgba(153, 51, 255, 0.2);
    }

    .btn-new:hover {
      background: var(--primary-hover);
      transform: translateY(-1px);
      box-shadow: 0 4px 8px rgba(153, 51, 255, 0.3);
    }

    .btn-new:active {
      transform: translateY(0);
    }

    .controls-section {
      display: flex;
      gap: 16px;
      align-items: center;
    }

    .search-box {
      position: relative;
      display: flex;
      align-items: center;
    }

    .search-box::before {
      content: '\f002';
      font-family: 'Font Awesome 6 Free';
      font-weight: 900;
      position: absolute;
      left: 14px;
      color: #9ca3af;
      font-size: 14px;
      pointer-events: none;
      z-index: 1;
    }

    .search-box input {
      padding: 12px 16px 12px 40px;
      border: 2px solid #e5e7eb;
      border-radius: 8px;
      font-size: 14px;
      width: 280px;
      transition: all 0.2s;
      background: white;
      color: var(--text-primary);
    }

    .search-box input:focus {
      outline: none;
      border-color: #9933ff;
      box-shadow: 0 0 0 4px rgba(153, 51, 255, 0.1);
    }

    .search-box input::placeholder {
      color: #9ca3af;
    }
    
    .table-container {
      background: #fff;
      padding: 0;
      border-radius: 12px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      margin-bottom: 32px;
      overflow: hidden;
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 15px;
    }
    
    th {
      text-align: left;
      padding: 16px 20px;
      color: #9333EA;
      font-weight: 700;
      border-bottom: 2px solid #ede9fe;
      white-space: nowrap;
      letter-spacing: 0.3px;
      background: linear-gradient(135deg, #faf5ff, #f3f0fa);
      font-size: 13px;
      text-transform: uppercase;
      position: relative;
    }

    th:last-child {
      text-align: right;
    }
    
    td {
      padding: 18px 20px;
      color: #1f2937;
      border-bottom: 1px solid #f3f0fa;
      background: #fff;
      font-weight: 500;
    }
    
    tr:hover td {
      background: linear-gradient(135deg, #faf5ff, #f8f4ff);
      transition: background 0.15s cubic-bezier(.4,0,.2,1);
    }

    tr:last-child td {
      border-bottom: none;
    }
    
    .empty-message {
      color: #6B7280;
      font-style: italic;
      text-align: center;
      padding: 60px 16px;
      font-size: 15px;
      background: #fafafa;
    }

    /* Action Buttons */
    .action-buttons {
      display: flex;
      gap: 8px;
      align-items: center;
      justify-content: flex-end;
    }
    
    .btn-approve {
      padding: 8px 16px;
      background: linear-gradient(135deg, #10b981, #059669);
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 6px;
      transition: all 0.2s;
      box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
    }
    
    .btn-reject {
      padding: 8px 16px;
      background: linear-gradient(135deg, #ef4444, #dc2626);
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 6px;
      transition: all 0.2s;
      box-shadow: 0 2px 4px rgba(239, 68, 68, 0.2);
    }
    
    .btn-approve:hover {
      background: linear-gradient(135deg, #059669, #047857);
      transform: translateY(-1px);
      box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
    }
    
    .btn-reject:hover {
      background: linear-gradient(135deg, #dc2626, #b91c1c);
      transform: translateY(-1px);
      box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
    }

    .btn-approve:active,
    .btn-reject:active {
      transform: translateY(0);
    }

    .pagination.centered {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 12px;
      padding: 20px;
      border-top: 1px solid var(--border-color);
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
      transition: all 0.2s;
      pointer-events: auto;
      font-family: 'Work Sans', sans-serif;
      font-weight: 500;
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

    .page-btn:focus {
      outline: none;
    }

    .page-indicator {
      font-size: 14px;
      color: var(--text-secondary);
      padding: 0 16px;
    }

    /* Modal Styles */
    .modal-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.5);
      z-index: 1000;
      backdrop-filter: blur(3px);
    }

    .modal {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: white;
      border-radius: 16px;
      z-index: 1001;
      width: 90%;
      max-width: 520px;
      max-height: 90vh;
      overflow: hidden;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }

    .modal.show,
    .modal-overlay.show {
      display: block;
    }

    .modal-header {
      background: var(--primary-gradient);
      padding: 24px 32px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-radius: 16px 16px 0 0;
    }

    .modal-title {
      font-size: 22px;
      font-weight: 600;
      color: white;
      margin: 0;
      letter-spacing: -0.02em;
    }

    .modal-close {
      background: rgba(255, 255, 255, 0.2);
      border: none;
      font-size: 18px;
      color: white;
      cursor: pointer;
      padding: 8px;
      border-radius: 50%;
      width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s;
      flex-shrink: 0;
    }

    .modal-close:hover {
      background: rgba(255, 255, 255, 0.3);
      transform: rotate(90deg);
    }

    .position-form-modal {
      padding: 32px;
      max-height: calc(90vh - 120px);
      overflow-y: auto;
    }

    .position-form-modal::-webkit-scrollbar {
      width: 8px;
    }

    .position-form-modal::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 4px;
    }

    .position-form-modal::-webkit-scrollbar-thumb {
      background: #cbd5e1;
      border-radius: 4px;
    }

    .position-form-modal::-webkit-scrollbar-thumb:hover {
      background: #9933ff;
    }

    .form-group {
      margin-bottom: 24px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: var(--text-primary);
      font-size: 14px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .form-group label i {
      color: #9933ff;
      font-size: 15px;
      width: 18px;
      text-align: center;
    }

    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 14px 18px;
      border: 2px solid var(--border-color);
      border-radius: var(--radius-sm);
      font-size: 15px;
      box-sizing: border-box;
      transition: all 0.2s;
      background: white;
      color: var(--text-primary);
      font-family: 'Work Sans', sans-serif;
    }

    .form-group input:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: #9933ff;
      box-shadow: 0 0 0 4px rgba(153, 51, 255, 0.1);
    }

    .form-group textarea {
      resize: vertical;
      min-height: 100px;
      line-height: 1.5;
    }

    .form-group input::placeholder,
    .form-group textarea::placeholder {
      color: #9ca3af;
    }

    .modal-footer {
      padding: 24px 32px;
      border-top: 1px solid var(--border-color);
      display: flex;
      justify-content: flex-end;
      gap: 14px;
      background: #fafbfc;
      border-radius: 0 0 16px 16px;
    }

    .btn-cancel {
      padding: 14px 32px;
      background: white;
      border: 2px solid var(--border-color);
      color: var(--text-primary);
      font-weight: 600;
      font-size: 15px;
      border-radius: var(--radius-sm);
      cursor: pointer;
      transition: all 0.2s;
      min-width: 120px;
    }

    .btn-cancel:hover {
      border-color: #cbd5e1;
      background: var(--secondary-color);
      transform: translateY(-1px);
    }

    .btn-cancel:active {
      transform: translateY(0);
    }

    .btn-save {
      padding: 14px 36px;
      background: var(--primary-gradient);
      border: none;
      color: white;
      font-weight: 600;
      font-size: 15px;
      border-radius: var(--radius-sm);
      cursor: pointer;
      transition: all 0.2s;
      min-width: 120px;
    }

    .btn-save:hover {
      background: var(--primary-hover);
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(153, 51, 255, 0.3);
    }

    .btn-save:active {
      transform: translateY(0);
    }

    @media (max-width: 768px) {
      .modal {
        width: 95%;
      }

      .modal-header {
        padding: 20px 24px;
      }

      .position-form-modal {
        padding: 24px;
      }

      .modal-footer {
        padding: 20px 24px;
        flex-direction: column;
      }

      .btn-cancel,
      .btn-save {
        width: 100%;
      }
    }

    .notification-modal {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) scale(0.7);
      background: white;
      border-radius: 16px;
      z-index: 2000;
      width: 90%;
      max-width: 420px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      opacity: 0;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .notification-modal.show {
      display: block;
      opacity: 1;
      transform: translate(-50%, -50%) scale(1);
    }

    .notification-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.4);
      z-index: 1999;
      backdrop-filter: blur(2px);
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .notification-overlay.show {
      display: block;
      opacity: 1;
    }

    .notification-header {
      padding: 32px 32px 24px 32px;
      text-align: center;
      border-radius: 16px 16px 0 0;
    }

    .notification-icon {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      font-size: 40px;
    }

    .notification-icon.success {
      background: linear-gradient(135deg, #10b981, #059669);
      color: white;
      animation: successPulse 0.6s ease;
    }

    .notification-icon.error {
      background: linear-gradient(135deg, #ef4444, #dc2626);
      color: white;
      animation: errorShake 0.6s ease;
    }

    @keyframes successPulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.1); }
    }

    @keyframes errorShake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-10px); }
      75% { transform: translateX(10px); }
    }

    .notification-title {
      font-size: 24px;
      font-weight: 700;
      margin: 0 0 12px 0;
      color: var(--text-primary);
    }

    .notification-message {
      font-size: 15px;
      color: var(--text-secondary);
      line-height: 1.6;
      margin: 0;
      padding: 0 16px;
    }

    .notification-footer {
      padding: 24px 32px 32px 32px;
      display: flex;
      justify-content: center;
    }

    .btn-notification-close {
      padding: 14px 40px;
      background: var(--primary-gradient);
      border: none;
      color: white;
      font-weight: 600;
      font-size: 15px;
      border-radius: var(--radius-sm);
      cursor: pointer;
      transition: all 0.2s;
      min-width: 140px;
    }

    .btn-notification-close:hover {
      background: var(--primary-hover);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(153, 51, 255, 0.3);
    }

    .btn-notification-close:active {
      transform: translateY(0);
    }
  </style>
</head>

<body>
  <div class="main-content">
    <h1 class="page-title">Positions</h1>
    
    <div class="header-controls">
      <button class="btn-new" onclick="openPositionModal()">
        <i class="fas fa-plus"></i> New Position
      </button>
    </div>
    
    <div class="table-container">
      <table class="styled-table">
        <thead>
          <tr>
            <th>Position</th>
            <th>Maximum Vote</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="positions-table-body">
          <!-- Table content will be dynamically populated -->
        </tbody>
      </table>
      
      <div class="pagination centered">
        <button type="button" class="page-btn prev-btn" onclick="goToPage('prev')">&laquo; Prev</button>
        <span class="page-indicator">Page 1 of 1</span>
        <button type="button" class="page-btn next-btn" onclick="goToPage('next')">Next &raquo;</button>
      </div>
    </div>
  </div>

  <!-- Position Modal -->
  <div class="modal-overlay" id="positionModalOverlay"></div>
  <div class="modal" id="positionModal">
    <div class="modal-header">
      <h2 class="modal-title" id="modalTitle">Add Position</h2>
      <button type="button" class="modal-close" onclick="closePositionModal()">
        <i class="fas fa-times"></i>
      </button>
    </div>
    
    <form id="positionForm" class="position-form-modal">
      <div class="form-group">
        <label for="positionDescription">
          <i class="fas fa-briefcase"></i> Position *
        </label>
        <input type="text" id="positionDescription" name="description" placeholder="Enter position name" required>
      </div>
      
      <div class="form-group">
        <label for="positionMaxVotes">
          <i class="fas fa-vote-yea"></i> Maximum Vote *
        </label>
        <input type="number" id="positionMaxVotes" name="maxVotes" min="1" value="1" placeholder="Enter maximum votes allowed" required>
      </div>
    </form>
    
    <div class="modal-footer">
      <button type="button" class="btn-cancel" onclick="closePositionModal()">Cancel</button>
      <button type="button" class="btn-save" onclick="document.getElementById('positionForm').dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }))">
        <i class="fas fa-save"></i> Save Position
      </button>
    </div>
  </div>

  <!-- Notification Modal -->
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

  <script>
    let positions = [];
    let editingPositionId = null;
    let currentPage = 1;
    const itemsPerPage = 6;

    function showNotification(type, title, message) {
      const modal = document.getElementById('notificationModal');
      const overlay = document.getElementById('notificationOverlay');
      const icon = document.getElementById('notificationIcon');
      const titleEl = document.getElementById('notificationTitle');
      const messageEl = document.getElementById('notificationMessage');

      icon.className = `notification-icon ${type}`;
      
      if (type === 'success') {
        icon.innerHTML = '<i class="fas fa-check"></i>';
      } else if (type === 'error') {
        icon.innerHTML = '<i class="fas fa-times"></i>';
      }

      titleEl.textContent = title;
      messageEl.textContent = message;

      overlay.classList.add('show');
      setTimeout(() => modal.classList.add('show'), 10);
    }

    function closeNotification() {
      const modal = document.getElementById('notificationModal');
      const overlay = document.getElementById('notificationOverlay');
      
      modal.classList.remove('show');
      setTimeout(() => overlay.classList.remove('show'), 300);
    }

    async function loadPositions() {
      try {
        const response = await fetch('../api/positions/read.php');
        const result = await response.json();
        
        if (result.success) {
          positions = result.data;
          currentPage = 1;
          renderPositionsTable();
        } else {
          console.error('Failed to load positions:', result.error);
        }
      } catch (error) {
        console.error('Error loading positions:', error);
      }
    }

    function renderPositionsTable(positionsList = positions) {
      const tbody = document.getElementById('positions-table-body');
      tbody.innerHTML = '';

      if (positionsList.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="3" class="empty-message">No positions found.</td>
          </tr>
        `;
        updatePagination(1, 1);
        return;
      }

      const totalPages = Math.ceil(positionsList.length / itemsPerPage);
      const startIndex = (currentPage - 1) * itemsPerPage;
      const endIndex = startIndex + itemsPerPage;
      const paginatedPositions = positionsList.slice(startIndex, endIndex);

      paginatedPositions.forEach(position => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${position.description}</td>
          <td>${position.maxVotes}</td>
          <td>
            <div class="action-buttons">
              <button class="btn-approve" onclick="editPosition(${position.id})">
                <i class="fas fa-edit"></i> Edit
              </button>
              <button class="btn-reject" onclick="deletePosition(${position.id})">
                <i class="fas fa-trash"></i> Delete
              </button>
            </div>
          </td>
        `;
        tbody.appendChild(row);
      });

      updatePagination(currentPage, totalPages);
    }

    function updatePagination(page, totalPages) {
      const pageIndicator = document.querySelector('.page-indicator');
      const prevBtn = document.querySelector('.prev-btn');
      const nextBtn = document.querySelector('.next-btn');
      
      if (pageIndicator) {
        pageIndicator.textContent = `Page ${page} of ${totalPages || 1}`;
      }
      
      if (prevBtn) {
        prevBtn.classList.toggle('disabled', page <= 1);
      }
      
      if (nextBtn) {
        nextBtn.classList.toggle('disabled', page >= totalPages);
      }
    }

    function goToPage(direction) {
      const totalPages = Math.ceil(positions.length / itemsPerPage);
      
      if (direction === 'prev' && currentPage > 1) {
        currentPage--;
        renderPositionsTable();
      } else if (direction === 'next' && currentPage < totalPages) {
        currentPage++;
        renderPositionsTable();
      }
    }

    function openPositionModal(position = null) {
      const modal = document.getElementById('positionModal');
      const overlay = document.getElementById('positionModalOverlay');
      const title = document.getElementById('modalTitle');
      const form = document.getElementById('positionForm');

      if (position) {
        title.textContent = 'Edit Position';
        document.getElementById('positionDescription').value = position.description;
        document.getElementById('positionMaxVotes').value = position.maxVotes;
        editingPositionId = position.id;
      } else {
        title.textContent = 'Add Position';
        form.reset();
        editingPositionId = null;
      }

      modal.classList.add('show');
      overlay.classList.add('show');
      document.body.style.overflow = 'hidden';
    }

    function closePositionModal() {
      const modal = document.getElementById('positionModal');
      const overlay = document.getElementById('positionModalOverlay');
      const form = document.getElementById('positionForm');
      
      modal.classList.remove('show');
      overlay.classList.remove('show');
      document.body.style.overflow = '';
      form.reset();
      editingPositionId = null;
    }

    function toTitleCase(str) {
      if (!str) return '';
      return str.toLowerCase().split(' ').map(word => 
        word.charAt(0).toUpperCase() + word.slice(1)
      ).join(' ');
    }

    function editPosition(id) {
      const position = positions.find(p => p.id === id);
      if (position) {
        openPositionModal(position);
      }
    }

    async function deletePosition(id) {
      const position = positions.find(p => p.id === id);
      if (position && confirm(`Are you sure you want to delete "${position.description}"?`)) {
        try {
          const response = await fetch('../api/positions/delete.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
          });
          
          const result = await response.json();
          
          if (result.success) {
            showNotification('success', 'Deleted!', `Position "${position.description}" has been deleted successfully.`);
            loadPositions();
          } else {
            showNotification('error', 'Delete Failed', result.error || 'Failed to delete position.');
          }
        } catch (error) {
          console.error('Error deleting position:', error);
          showNotification('error', 'Error', 'An unexpected error occurred while deleting the position.');
        }
      }
    }

    // Form submission
    document.getElementById('positionForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      const description = formData.get('description');
      const maxVotes = parseInt(formData.get('maxVotes'));

      try {
        const url = editingPositionId 
          ? '../api/positions/update.php' 
          : '../api/positions/create.php';
        
        const data = editingPositionId
          ? { id: editingPositionId, description, maxVotes }
          : { description, maxVotes };

        const response = await fetch(url, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
          closePositionModal();
          loadPositions();
          
          const actionType = editingPositionId ? 'updated' : 'added';
          const title = editingPositionId ? 'Updated!' : 'Success!';
          showNotification('success', title, `Position "${description}" has been ${actionType} successfully.`);
        } else {
          showNotification('error', 'Save Failed', result.error || 'Failed to save position.');
        }
      } catch (error) {
        console.error('Error saving position:', error);
        showNotification('error', 'Error', 'An unexpected error occurred while saving the position.');
      }
    });

    // Auto-format position description to title case
    document.getElementById('positionDescription').addEventListener('input', function(e) {
      const input = e.target;
      const cursorPosition = input.selectionStart;
      const originalLength = input.value.length;
      
      input.value = toTitleCase(input.value);
      
      const newLength = input.value.length;
      const newCursorPosition = cursorPosition + (newLength - originalLength);
      input.setSelectionRange(newCursorPosition, newCursorPosition);
    });

    // Close modal when clicking overlay
    document.getElementById('positionModalOverlay').addEventListener('click', closePositionModal);

    // Initialize table on page load
    document.addEventListener('DOMContentLoaded', function() {
      loadPositions();
    });
  </script>
</body>
</html>
