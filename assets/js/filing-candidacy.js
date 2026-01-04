let filingPeriods = [];
let editingFilingId = null;
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
  const overlay = document.getElementById(modalId + 'Overlay');
  if (modal && overlay) {
    overlay.classList.add('show');
    setTimeout(() => modal.classList.add('show'), 10);
  } else {
    console.error('Modal or overlay not found:', { modalId, overlayId: modalId + 'Overlay', modal: !!modal, overlay: !!overlay });
  }
}

function hideModal(modalId) {
  const modal = document.getElementById(modalId);
  const overlay = document.getElementById(modalId + 'Overlay');
  if (modal) modal.classList.remove('show');
  if (overlay) {
    setTimeout(() => overlay.classList.remove('show'), 300);
  }
}

async function loadFilingPeriods() {
  try {
    const response = await fetch('../api/filing-candidacy/read.php');
    const result = await response.json();
    
    if (result.success) {
      filingPeriods = result.data;
      renderFilingPeriods();
    } else {
      showNotification('error', 'Error', result.error || 'Failed to load filing periods');
    }
  } catch (error) {
    showNotification('error', 'Error', 'Failed to load filing periods: ' + error.message);
  }
}

function renderFilingPeriods() {
  const tbody = document.getElementById('filing-table-body');
  
  if (filingPeriods.length === 0) {
    tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 40px; color: #6b7280;">No filing periods found. Click "New Filing Period" to create one.</td></tr>';
    return;
  }
  
  const startIndex = (currentPage - 1) * itemsPerPage;
  const endIndex = startIndex + itemsPerPage;
  const paginatedPeriods = filingPeriods.slice(startIndex, endIndex);
  
  tbody.innerHTML = paginatedPeriods.map(period => {
    const startDate = new Date(period.start_date);
    const endDate = new Date(period.end_date);
    const now = new Date();
    
    let statusClass = 'inactive';
    let statusText = 'Inactive';
    
    if (period.is_active == 1) {
      if (now >= startDate && now <= endDate) {
        statusClass = 'active';
        statusText = 'Active';
      } else if (now < startDate) {
        statusClass = 'upcoming';
        statusText = 'Upcoming';
      } else {
        statusClass = 'completed';
        statusText = 'Expired';
      }
    }
    
    return `
      <tr>
        <td><strong>${escapeHtml(period.title)}</strong></td>
        <td>${formatDateTime(period.start_date)}</td>
        <td>${formatDateTime(period.end_date)}</td>
        <td><span class="status-badge ${statusClass}">${statusText}</span></td>
        <td>
          <div class="action-buttons">
            <button class="action-btn edit" onclick="editFilingPeriod(${period.id})" title="Edit">
              <i class="fas fa-edit"></i>
            </button>
            <button class="action-btn toggle ${period.is_active == 1 ? 'active' : 'inactive'}" onclick="toggleFilingStatus(${period.id}, ${period.is_active})" title="${period.is_active == 1 ? 'Deactivate' : 'Activate'}">
              <i class="fas fa-${period.is_active == 1 ? 'toggle-on' : 'toggle-off'}"></i>
            </button>
            <button class="action-btn delete" onclick="deleteFilingPeriod(${period.id})" title="Delete">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </td>
      </tr>
    `;
  }).join('');
  
  updatePagination();
}

function updatePagination() {
  const totalPages = Math.ceil(filingPeriods.length / itemsPerPage);
  const pageIndicator = document.querySelector('.page-indicator');
  const prevBtn = document.querySelector('.prev-btn');
  const nextBtn = document.querySelector('.next-btn');
  
  if (pageIndicator) {
    pageIndicator.textContent = `Page ${currentPage} of ${totalPages || 1}`;
  }
  
  if (prevBtn) {
    prevBtn.disabled = currentPage === 1;
    prevBtn.classList.toggle('disabled', currentPage === 1);
  }
  
  if (nextBtn) {
    nextBtn.disabled = currentPage >= totalPages;
    nextBtn.classList.toggle('disabled', currentPage >= totalPages);
  }
}

function goToPage(direction) {
  const totalPages = Math.ceil(filingPeriods.length / itemsPerPage);
  
  if (direction === 'prev' && currentPage > 1) {
    currentPage--;
    renderFilingPeriods();
  } else if (direction === 'next' && currentPage < totalPages) {
    currentPage++;
    renderFilingPeriods();
  }
}

function openFilingModal(id = null) {
  editingFilingId = id;
  const modal = document.getElementById('filingModal');
  const title = document.getElementById('modalTitle');
  const form = document.getElementById('filingForm');
  
  if (id) {
    const period = filingPeriods.find(p => p.id == id);
    if (period) {
      title.textContent = 'Edit Filing Period';
      document.getElementById('filingTitle').value = period.title;
      document.getElementById('filingAnnouncement').value = period.announcement_text;
      document.getElementById('filingFormLink').value = period.form_link;
      document.getElementById('filingStartDate').value = formatDateTimeLocal(period.start_date);
      document.getElementById('filingEndDate').value = formatDateTimeLocal(period.end_date);
      document.getElementById('filingScreeningDate').value = period.screening_date || '';
      document.getElementById('filingIsActive').checked = period.is_active == 1;
    }
  } else {
    title.textContent = 'Add Filing Period';
    form.reset();
    document.getElementById('filingIsActive').checked = false;
  }
  
  showModal('filingModal');
}

function closeFilingModal() {
  hideModal('filingModal');
  editingFilingId = null;
  document.getElementById('filingForm').reset();
}

function saveFilingPeriod() {
  const saveButton = document.querySelector('#filingModalOverlay .btn-save');
  const originalButtonText = saveButton ? saveButton.innerHTML : '';
  
  if (saveButton) {
    saveButton.disabled = true;
    saveButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
  }
  
  const form = document.getElementById('filingForm');
  const formData = new FormData(form);
  
  const data = {
    title: document.getElementById('filingTitle').value.trim(),
    announcement_text: document.getElementById('filingAnnouncement').value.trim(),
    form_link: document.getElementById('filingFormLink').value.trim(),
    start_date: document.getElementById('filingStartDate').value,
    end_date: document.getElementById('filingEndDate').value,
    screening_date: document.getElementById('filingScreeningDate').value.trim() || null,
    is_active: document.getElementById('filingIsActive').checked ? 1 : 0
  };
  
  if (!data.title || !data.announcement_text || !data.form_link || !data.start_date || !data.end_date) {
    if (saveButton) {
      saveButton.disabled = false;
      saveButton.innerHTML = originalButtonText;
    }
    showNotification('error', 'Validation Error', 'Please fill in all required fields');
    return;
  }
  
  const url = editingFilingId 
    ? '../api/filing-candidacy/update.php'
    : '../api/filing-candidacy/create.php';
  
  if (editingFilingId) {
    data.id = editingFilingId;
  }
  
  fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(data)
  })
  .then(response => response.json())
  .then(result => {
    if (result.success) {
      showNotification('success', 'Success', result.message || 'Filing period saved successfully');
      closeFilingModal();
      loadFilingPeriods();
    } else {
      showNotification('error', 'Error', result.error || 'Failed to save filing period');
    }
  })
  .catch(error => {
    showNotification('error', 'Error', 'Failed to save filing period: ' + error.message);
  })
  .finally(() => {
    if (saveButton) {
      saveButton.disabled = false;
      saveButton.innerHTML = originalButtonText;
    }
  });
}

function editFilingPeriod(id) {
  openFilingModal(id);
}

function toggleFilingStatus(id, currentStatus) {
  const newStatus = currentStatus == 1 ? 0 : 1;
  const action = newStatus == 1 ? 'activate' : 'deactivate';
  
  showConfirmModal(
    'info',
    `Confirm ${action.charAt(0).toUpperCase() + action.slice(1)}`,
    `Are you sure you want to ${action} this filing period?`,
    `<p>This will ${newStatus == 1 ? 'activate' : 'deactivate'} the filing period and ${newStatus == 1 ? 'deactivate all other active periods' : ''}.</p>`,
    () => {
      fetch('../api/filing-candidacy/toggle_status.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          id: id,
          is_active: newStatus
        })
      })
      .then(response => response.json())
      .then(result => {
        if (result.success) {
          showNotification('success', 'Success', result.message || 'Filing period status updated successfully');
          loadFilingPeriods();
        } else {
          showNotification('error', 'Error', result.error || 'Failed to update filing period status');
        }
      })
      .catch(error => {
        showNotification('error', 'Error', 'Failed to update filing period status: ' + error.message);
      });
    }
  );
}

function deleteFilingPeriod(id) {
  const period = filingPeriods.find(p => p.id == id);
  
  showConfirmModal(
    'danger',
    'Confirm Delete',
    'Are you sure you want to delete this filing period?',
    `<p><strong>${escapeHtml(period.title)}</strong></p><p>This action cannot be undone.</p>`,
    () => {
      fetch('../api/filing-candidacy/delete.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id: id })
      })
      .then(response => response.json())
      .then(result => {
        if (result.success) {
          showNotification('success', 'Success', result.message || 'Filing period deleted successfully');
          loadFilingPeriods();
        } else {
          showNotification('error', 'Error', result.error || 'Failed to delete filing period');
        }
      })
      .catch(error => {
        showNotification('error', 'Error', 'Failed to delete filing period: ' + error.message);
      });
    }
  );
}

function formatDateTime(dateString) {
  const date = new Date(dateString);
  return date.toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
}

function formatDateTimeLocal(dateString) {
  const date = new Date(dateString);
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  return `${year}-${month}-${day}T${hours}:${minutes}`;
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

document.addEventListener('DOMContentLoaded', function() {
  loadFilingPeriods();
  
  document.getElementById('filingModalOverlay').addEventListener('click', function(e) {
    if (e.target === this) {
      closeFilingModal();
    }
  });
});

