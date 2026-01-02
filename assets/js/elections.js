let elections = [];
let editingElectionId = null;
let currentPage = 1;
const itemsPerPage = 6;

function showNotification(type, title, message) {
  const modal = document.getElementById('notificationModal');
  const overlay = document.getElementById('notificationOverlay');
  const icon = document.getElementById('notificationIcon');
  const titleEl = document.getElementById('notificationTitle');
  const messageEl = document.getElementById('notificationMessage');

  icon.className = `notification-icon ${type}`;
  icon.innerHTML = type === 'success' ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>';

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

function showConfirmModal(type, title, message, details, onConfirm) {
  const modal = document.getElementById('confirmModal');
  const overlay = document.getElementById('confirmOverlay');
  const icon = document.getElementById('confirmIcon');
  const titleEl = document.getElementById('confirmTitle');
  const messageEl = document.getElementById('confirmMessage');
  const detailsEl = document.getElementById('confirmDetails');
  const okBtn = document.getElementById('confirmOkBtn');

  icon.className = `confirm-icon ${type}`;
  if (type === 'success') {
    icon.innerHTML = '<i class="fas fa-check-circle"></i>';
  } else if (type === 'danger') {
    icon.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
  } else {
    icon.innerHTML = '<i class="fas fa-info-circle"></i>';
  }

  titleEl.textContent = title;
  messageEl.textContent = message;
  detailsEl.innerHTML = details;

  okBtn.className = `btn-confirm-ok ${type}`;
  
  okBtn.onclick = () => {
    closeConfirmModal();
    if (onConfirm) onConfirm();
  };

  overlay.classList.add('show');
  setTimeout(() => modal.classList.add('show'), 10);
}

function closeConfirmModal() {
  const modal = document.getElementById('confirmModal');
  const overlay = document.getElementById('confirmOverlay');
  
  modal.classList.remove('show');
  setTimeout(() => overlay.classList.remove('show'), 300);
}

function showModal(modalId) {
  const modal = document.getElementById(modalId);
  const overlay = document.getElementById(modalId.replace('Modal', 'Overlay'));
  if (modal && overlay) {
    overlay.classList.add('show');
    setTimeout(() => modal.classList.add('show'), 10);
  }
}

function hideModal(modalId) {
  const modal = document.getElementById(modalId);
  const overlay = document.getElementById(modalId.replace('Modal', 'Overlay'));
  if (modal) modal.classList.remove('show');
  if (overlay) {
    setTimeout(() => overlay.classList.remove('show'), 300);
  }
}

function closeSuccessModal() {
  hideModal('successModal');
  loadElections();
}

async function loadElections() {
  try {
    const response = await fetch('../api/elections/read.php');
    const result = await response.json();
    
    if (result.success) {
      elections = result.data;
      currentPage = 1;
      renderElectionsTable();
    } else {
      console.error('Failed to load elections:', result.error);
    }
  } catch (error) {
    console.error('Error loading elections:', error);
  }
}

function formatDateTime(dateTimeString) {
  const date = new Date(dateTimeString);
  const options = { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric', 
    hour: '2-digit', 
    minute: '2-digit' 
  };
  return date.toLocaleDateString('en-US', options);
}

function getStatusBadge(election) {
  const now = new Date();
  const startDate = new Date(election.start_date);
  const endDate = new Date(election.end_date);
  
  let status = election.status;
  let displayStatus = status;
  
  if (status === 'active' && now > endDate) {
    displayStatus = 'completed';
  } else if (status === 'upcoming' && now >= startDate && now <= endDate) {
    displayStatus = 'active';
  }
  
  return `<span class="status-badge ${displayStatus}">${displayStatus}</span>`;
}

function getActionButtons(election) {
  const now = new Date();
  const startDate = new Date(election.start_date);
  const endDate = new Date(election.end_date);
  
  let buttons = '<div class="action-buttons">';
  
  if (election.status === 'upcoming') {
    buttons += `
      <button class="action-btn approve" onclick="startElection(${election.id})" title="Start Election">
        <i class="fas fa-play"></i>
      </button>
      <button class="action-btn edit" onclick="editElection(${election.id})" title="Edit">
        <i class="fas fa-edit"></i>
      </button>
      <button class="action-btn delete" onclick="deleteElection(${election.id})" title="Delete">
        <i class="fas fa-trash"></i>
      </button>
    `;
  } else if (election.status === 'active') {
    buttons += `
      <button class="action-btn reject" onclick="stopElection(${election.id})" title="Stop Election Immediately">
        <i class="fas fa-stop-circle"></i>
      </button>
      <button class="action-btn approve" onclick="closeElection(${election.id})" title="Close & Finalize Results">
        <i class="fas fa-check-circle"></i>
      </button>
    `;
  } else if (election.status === 'completed') {
    buttons += `
      <button class="action-btn view" onclick="viewElectionResults(${election.id})" title="View & Print Results">
        <i class="fas fa-print"></i>
      </button>
      <button class="action-btn delete" onclick="deleteElection(${election.id})" title="Delete">
        <i class="fas fa-trash"></i>
      </button>
    `;
  } else if (election.status === 'cancelled') {
    buttons += `
      <button class="action-btn delete" onclick="deleteElection(${election.id})" title="Delete">
        <i class="fas fa-trash"></i>
      </button>
    `;
  }
  
  buttons += '</div>';
  return buttons;
}

function renderElectionsTable(electionsList = elections) {
  const tbody = document.getElementById('elections-table-body');
  tbody.innerHTML = '';

  if (electionsList.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="5" class="empty-message">No elections found. Create your first election to get started.</td>
      </tr>
    `;
    updatePagination(1, 1);
    return;
  }

  const totalPages = Math.ceil(electionsList.length / itemsPerPage);
  const startIndex = (currentPage - 1) * itemsPerPage;
  const endIndex = startIndex + itemsPerPage;
  const paginatedElections = electionsList.slice(startIndex, endIndex);

  paginatedElections.forEach(election => {
    const row = document.createElement('tr');
    row.innerHTML = `
      <td><strong>${election.title}</strong></td>
      <td>${formatDateTime(election.start_date)}</td>
      <td>${formatDateTime(election.end_date)}</td>
      <td>${getStatusBadge(election)}</td>
      <td>${getActionButtons(election)}</td>
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
  const totalPages = Math.ceil(elections.length / itemsPerPage);
  
  if (direction === 'prev' && currentPage > 1) {
    currentPage--;
    renderElectionsTable();
  } else if (direction === 'next' && currentPage < totalPages) {
    currentPage++;
    renderElectionsTable();
  }
}

function openElectionModal(election = null) {
  const modal = document.getElementById('electionModal');
  const overlay = document.getElementById('electionModalOverlay');
  const title = document.getElementById('modalTitle');
  const form = document.getElementById('electionForm');

  if (election) {
    title.textContent = 'Edit Election';
    document.getElementById('electionTitle').value = election.title;
    document.getElementById('electionDescription').value = election.description || '';
    
    const startDate = new Date(election.start_date);
    const endDate = new Date(election.end_date);
    document.getElementById('electionStartDate').value = formatDateForInput(startDate);
    document.getElementById('electionEndDate').value = formatDateForInput(endDate);
    
    editingElectionId = election.id;
  } else {
    title.textContent = 'Add New Election';
    form.reset();
    editingElectionId = null;
  }

  modal.classList.add('show');
  overlay.classList.add('show');
  document.body.style.overflow = 'hidden';
}

function closeElectionModal() {
  const modal = document.getElementById('electionModal');
  const overlay = document.getElementById('electionModalOverlay');
  const form = document.getElementById('electionForm');
  
  modal.classList.remove('show');
  overlay.classList.remove('show');
  document.body.style.overflow = '';
  form.reset();
  editingElectionId = null;
}

function formatDateForInput(date) {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  return `${year}-${month}-${day}T${hours}:${minutes}`;
}

function editElection(id) {
  const election = elections.find(e => e.id === id);
  if (election) {
    openElectionModal(election);
  }
}

function viewElectionResults(id) {
  window.open(`print-election-results-pdf.php?election_id=${id}`, '_blank');
}

async function startElection(id) {
  const election = elections.find(e => e.id === id);
  if (!election) return;
  
  const details = `
    <p><strong>Election:</strong> ${election.title}</p>
    <p><strong>Action:</strong> Students will be able to vote immediately</p>
  `;
  
  showConfirmModal(
    'success',
    'Start Election',
    'Are you sure you want to start this election?',
    details,
    async () => {
      try {
        const response = await fetch('../api/elections/update_status.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id, status: 'active' })
        });
        
        const result = await response.json();
        
        if (result.success) {
          showNotification('success', 'Election Started!', `"${election.title}" is now active. Students can cast their votes.`);
          loadElections();
        } else {
          showNotification('error', 'Failed to Start', result.error || 'Could not start the election.');
        }
      } catch (error) {
        console.error('Error starting election:', error);
        showNotification('error', 'Error', 'An unexpected error occurred.');
      }
    }
  );
}

async function stopElection(id) {
  const election = elections.find(e => e.id === id);
  if (!election) return;
  
  const details = `
    <p><strong>Election:</strong> ${election.title}</p>
    <p><strong>This will:</strong></p>
    <p>- Immediately stop all voting</p>
    <p>- Mark the election as cancelled</p>
    <p>- Students will no longer be able to vote</p>
    <p style="margin-top: 12px; color: #dc2626;"><strong>Note:</strong> Use "Close" instead if you want to finalize the results.</p>
  `;
  
  showConfirmModal(
    'danger',
    'Stop Election',
    'Are you sure you want to STOP this election?',
    details,
    async () => {
      try {
        const response = await fetch('../api/elections/update_status.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id, status: 'cancelled' })
        });
        
        const result = await response.json();
        
        if (result.success) {
          showNotification('success', 'Election Stopped!', `"${election.title}" has been cancelled. Voting is now closed.`);
          loadElections();
        } else {
          showNotification('error', 'Failed to Stop', result.error || 'Could not stop the election.');
        }
      } catch (error) {
        console.error('Error stopping election:', error);
        showNotification('error', 'Error', 'An unexpected error occurred.');
      }
    }
  );
}

async function closeElection(id) {
  const election = elections.find(e => e.id === id);
  if (!election) return;
  
  const details = `
    <p><strong>Election:</strong> ${election.title}</p>
    <p><strong>This will:</strong></p>
    <p>- Stop all voting</p>
    <p>- Finalize and publish results</p>
    <p>- Save to blockchain</p>
    <p>- Mark the election as completed</p>
    <p style="margin-top: 12px; color: #059669;"><strong>Note:</strong> Use this when the election has ended successfully.</p>
  `;
  
  showConfirmModal(
    'success',
    'Close Election',
    'Are you sure you want to CLOSE this election?',
    details,
    async () => {
      try {
        closeConfirmModal();
        showModal('loadingModal');
        
        const response = await fetch('../api/elections/update_status.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id, status: 'completed' })
        });
        
        const result = await response.json();
        
        hideModal('loadingModal');
        
        if (result.success) {
          if (result.transaction_hash) {
            document.getElementById('electionTxHash').textContent = result.transaction_hash;
            showModal('successModal');
          } else {
            showNotification('success', 'Election Closed!', `"${election.title}" has been completed. Results are now available.`);
            loadElections();
          }
        } else {
          showNotification('error', 'Failed to Close', result.error || 'Could not close the election.');
        }
      } catch (error) {
        console.error('Error closing election:', error);
        hideModal('loadingModal');
        showNotification('error', 'Error', 'An unexpected error occurred.');
      }
    }
  );
}

async function deleteElection(id) {
  const election = elections.find(e => e.id === id);
  if (!election) return;
  
  const details = `
    <p><strong>Election:</strong> ${election.title}</p>
    <p style="margin-top: 12px; color: #dc2626;"><strong>Warning:</strong> This action cannot be undone.</p>
  `;
  
  showConfirmModal(
    'danger',
    'Delete Election',
    'Are you sure you want to delete this election?',
    details,
    async () => {
      try {
        const response = await fetch('../api/elections/delete.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id })
        });
        
        const result = await response.json();
        
        if (result.success) {
          showNotification('success', 'Deleted!', `Election "${election.title}" has been deleted.`);
          loadElections();
        } else {
          showNotification('error', 'Delete Failed', result.error || 'Could not delete the election.');
        }
      } catch (error) {
        console.error('Error deleting election:', error);
        showNotification('error', 'Error', 'An unexpected error occurred.');
      }
    }
  );
}

document.getElementById('electionForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  const data = {
    title: formData.get('title'),
    description: formData.get('description'),
    start_date: formData.get('start_date'),
    end_date: formData.get('end_date')
  };

  if (!data.start_date || !data.end_date) {
    showNotification('error', 'Missing Required Fields', 'Please fill in both start date and end date.');
    return;
  }

  const startDate = new Date(data.start_date);
  const endDate = new Date(data.end_date);
  const now = new Date();
  
  if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
    showNotification('error', 'Invalid Date Format', 'Please enter valid dates for start and end dates.');
    return;
  }
  
  if (startDate < now) {
    showNotification('error', 'Invalid Start Date', 'The election start date cannot be in the past. Please select a current or future date.');
    return;
  }
  
  if (endDate <= startDate) {
    showNotification('error', 'Invalid Dates', 'End date must be after start date.');
    return;
  }

  try {
    const url = editingElectionId 
      ? '../api/elections/update.php' 
      : '../api/elections/create.php';
    
    if (editingElectionId) {
      data.id = editingElectionId;
    }

    const response = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });
    
    const result = await response.json();
    
    if (result.success) {
      closeElectionModal();
      loadElections();
      
      const actionType = editingElectionId ? 'updated' : 'created';
      const title = editingElectionId ? 'Updated!' : 'Success!';
      showNotification('success', title, `Election "${data.title}" has been ${actionType} successfully.`);
    } else {
      showNotification('error', 'Save Failed', result.error || 'Failed to save election.');
    }
  } catch (error) {
    console.error('Error saving election:', error);
    showNotification('error', 'Error', 'An unexpected error occurred while saving the election.');
  }
});

document.getElementById('electionModalOverlay').addEventListener('click', closeElectionModal);

document.addEventListener('DOMContentLoaded', function() {
  loadElections();
});

