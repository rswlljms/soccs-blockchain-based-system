const MEMBERSHIP_FEE = 250;
let studentsData = [];
let summaryData = {};
let showArchived = false;
let currentStudentId = null;

function toTitleCase(text) {
    if (!text) return text;
    return text.toLowerCase().split(' ').map(word => {
        return word.charAt(0).toUpperCase() + word.slice(1);
    }).join(' ');
}

document.addEventListener('DOMContentLoaded', function () {
    const textInputs = document.querySelectorAll('input[type="text"]');
    textInputs.forEach(input => {
        input.addEventListener('input', function (e) {
            const cursorPosition = e.target.selectionStart;
            const newValue = toTitleCase(e.target.value);
            if (newValue !== e.target.value) {
                e.target.value = newValue;
                e.target.setSelectionRange(cursorPosition, cursorPosition);
            }
        });
    });
});

function updateSummaryCards() {
    if (summaryData) {
        const total = summaryData.total_students || 0;
        const archived = summaryData.archived_students || 0;
        const active = total;
        const totalEl = document.getElementById('totalStudents');
        const activeEl = document.getElementById('activeStudents');
        const archivedEl = document.getElementById('archivedStudents');
        if (totalEl) totalEl.textContent = total;
        if (activeEl) activeEl.textContent = active;
        if (archivedEl) archivedEl.textContent = archived;
    }
}

function getYearSuffix(year) {
    const suffixes = {
        '1': 'st',
        '2': 'nd',
        '3': 'rd',
        '4': 'th'
    };
    return suffixes[year] || 'th';
}

function renderStudentsTable(students) {
    const tbody = document.getElementById('students-table-body');
    if (!tbody) {
        console.warn('students-table-body not found on the page. Skipping render.');
        return;
    }
    tbody.innerHTML = '';

    if (students.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" style="text-align: center; padding: 2rem;">
                    No students found
                </td>
            </tr>
        `;
        return;
    }

    students.forEach(student => {
        const row = document.createElement('tr');
        
        const actionButton = student.is_archived 
            ? `<button class="action-btn restore" onclick="restoreStudent('${student.id}')" title="Restore Student">
                 <i class="fas fa-undo"></i>
               </button>`
            : `<button class="action-btn archive" onclick="archiveStudent('${student.id}')" title="Archive Student">
                 <i class="fas fa-box-archive"></i>
               </button>`;

        const profileButton = `<button class="action-btn view" onclick="viewStudentProfile('${student.id}')" title="View Profile">
            <i class="fas fa-id-card"></i>
        </button>`;

        row.innerHTML = `
            <td>${student.full_name}</td>
            <td>${student.course}</td>
            <td>${student.year_level}${getYearSuffix(student.year_level)} Year</td>
            <td>${student.section}</td>
            <td>
                <div class="action-buttons">
                    ${actionButton}
                    ${profileButton}
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
}

async function loadStudents() {
    try {
        const searchTerm = document.getElementById('searchStudent').value.trim();
    const courseFilter = document.getElementById('filterCourse').value;
    const yearFilter = document.getElementById('filterYear').value;
    const sectionFilter = document.getElementById('filterSection').value.trim().toUpperCase();
    const statusFilter = document.getElementById('filterStatus').value;

        const params = new URLSearchParams({
            search: searchTerm,
            course: courseFilter,
            year: yearFilter,
            section: sectionFilter,
            status: statusFilter,
            show_archived: showArchived ? 'true' : 'false'
        });
        
        const response = await fetch(`../api/get_students.php?${params.toString()}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            studentsData = data.students;
            summaryData = data.summary;
            updateSummaryCards();
            renderStudentsTable(studentsData);
        } else {
            console.error('API Error:', data.error || data.message);
            alert('Error: ' + (data.error || data.message || 'Failed to load students data'));
        }
    } catch (error) {
        console.error('Error loading students:', error);
        alert('Error loading students data: ' + error.message);
    }
}

// Section Summary removed for Student Management page

function applyFilters() {
    loadStudents();
}

document.getElementById('applyFilters').addEventListener('click', applyFilters);

async function togglePaymentStatus(studentId) {
    const student = studentsData.find(s => s.id === studentId);
    if (!student) return;
    
    const currentStatus = student.payment_status;
    
    if (currentStatus === 'unpaid') {
        // Check if student already has a receipt
        if (student.receipt_file) {
            const title = 'Change Membership Status';
            const message = `Change status from UNPAID to PAID for ${student.full_name}?`;
            openConfirmModal(title, message, async function () {
                await updateMembershipStatus(studentId, 'paid');
            });
        } else {
            // Show receipt upload modal
            openReceiptUploadModal(studentId, student.full_name);
        }
    } else {
        const title = 'Change Membership Status';
        const message = `Change status from PAID to UNPAID for ${student.full_name}?`;
        openConfirmModal(title, message, async function () {
            await updateMembershipStatus(studentId, 'unpaid');
        });
    }
}

async function updateMembershipStatus(studentId, action) {
    try {
        const response = await fetch('../api/toggle_membership_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ 
                student_id: studentId,
                action: action === 'unpaid' ? 'set_unpaid' : 'toggle'
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            await loadStudents();
            showSuccessModal('Membership status updated successfully!');
        } else {
            alert('Failed to update membership status: ' + data.error);
        }
    } catch (error) {
        console.error('Error updating membership status:', error);
        alert('Error updating membership status');
    }
}

async function archiveStudent(studentId) {
    const student = studentsData.find(s => s.id === studentId);
    const studentName = student ? student.full_name : 'this student';
    
    openConfirmModal(
        'Archive Student', 
        `Are you sure you want to archive ${studentName}? This will move them to the archived list.`,
        async function() {
            try {
                const response = await fetch('../api/archive_student.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ 
                        student_id: studentId,
                        action: 'archive'
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    await loadStudents();
                    showSuccessModal('Student archived successfully!');
                } else {
                    alert('Failed to archive student: ' + data.error);
                }
            } catch (error) {
                console.error('Error archiving student:', error);
                alert('Error archiving student');
            }
        }
    );
}

async function restoreStudent(studentId) {
    const student = studentsData.find(s => s.id === studentId);
    const studentName = student ? student.full_name : 'this student';
    
    openConfirmModal(
        'Restore Student', 
        `Are you sure you want to restore ${studentName}? This will move them back to the active list.`,
        async function() {
            try {
                const response = await fetch('../api/archive_student.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ 
                        student_id: studentId,
                        action: 'restore'
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    await loadStudents();
                    showSuccessModal('Student restored successfully!');
                } else {
                    alert('Failed to restore student: ' + data.error);
                }
            } catch (error) {
                console.error('Error restoring student:', error);
                alert('Error restoring student');
            }
        }
    );
}

function toggleArchivedView() {
    showArchived = !showArchived;
    const toggleBtn = document.getElementById('toggleArchived');
    const toggleText = document.getElementById('archiveToggleText');
    
    if (showArchived) {
        toggleBtn.classList.add('active');
        toggleText.textContent = 'View Active';
    } else {
        toggleBtn.classList.remove('active');
        toggleText.textContent = 'View Archived';
    }
    
    loadStudents();
}

let confirmCallback = null;
function openConfirmModal(title, message, onConfirm) {
    const overlay = document.getElementById('confirmOverlay');
    const modal = document.getElementById('confirmModal');
    if (!overlay || !modal) {
        if (confirm(message)) onConfirm && onConfirm();
        return;
    }
    document.getElementById('confirmTitle').textContent = title || 'Confirm Action';
    document.getElementById('confirmMessage').textContent = message || 'Are you sure?';
    confirmCallback = typeof onConfirm === 'function' ? onConfirm : null;
    overlay.classList.add('show');
    modal.classList.add('show');
}

function openReceiptUploadModal(studentId, studentName) {
    const overlay = document.getElementById('receiptUploadOverlay');
    const modal = document.getElementById('receiptUploadModal');
    if (!overlay || !modal) {
        alert('Receipt upload modal not found. Please refresh the page.');
        return;
    }
    
    document.getElementById('receiptStudentName').textContent = studentName;
    document.getElementById('receiptStudentId').value = studentId;
    document.getElementById('receiptFile').value = '';
    document.getElementById('receiptError').textContent = '';
    
    overlay.classList.add('show');
    modal.classList.add('show');
}

function closeReceiptUploadModal() {
    const overlay = document.getElementById('receiptUploadOverlay');
    const modal = document.getElementById('receiptUploadModal');
    if (overlay) overlay.classList.remove('show');
    if (modal) modal.classList.remove('show');
}

function showSuccessModal(message) {
    const overlay = document.getElementById('successOverlay');
    const modal = document.getElementById('successModal');
    if (!overlay || !modal) {
        alert(message); // Fallback to alert if modal not found
        return;
    }
    
    document.getElementById('successMessage').textContent = message;
    overlay.classList.add('show');
    modal.classList.add('show');
}

function closeSuccessModal() {
    const overlay = document.getElementById('successOverlay');
    const modal = document.getElementById('successModal');
    if (overlay) overlay.classList.remove('show');
    if (modal) modal.classList.remove('show');
}

async function uploadReceipt() {
    const studentId = document.getElementById('receiptStudentId').value;
    const fileInput = document.getElementById('receiptFile');
    const errorDiv = document.getElementById('receiptError');
    
    if (!fileInput.files[0]) {
        errorDiv.textContent = 'Please select a file to upload.';
        return;
    }
    
    const formData = new FormData();
    formData.append('student_id', studentId);
    formData.append('receipt', fileInput.files[0]);
    
    try {
        errorDiv.textContent = 'Uploading...';
        errorDiv.style.color = '#007bff';
        
        const response = await fetch('../api/upload_membership_receipt.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            closeReceiptUploadModal();
            await loadStudents();
            showSuccessModal('Receipt uploaded successfully! Student marked as paid.');
        } else {
            errorDiv.textContent = data.error || 'Upload failed';
            errorDiv.style.color = '#dc3545';
        }
    } catch (error) {
        console.error('Error uploading receipt:', error);
        errorDiv.textContent = 'Error uploading receipt. Please try again.';
        errorDiv.style.color = '#dc3545';
    }
}

function closeConfirmModal() {
    const overlay = document.getElementById('confirmOverlay');
    const modal = document.getElementById('confirmModal');
    if (overlay) overlay.classList.remove('show');
    if (modal) modal.classList.remove('show');
}

document.addEventListener('DOMContentLoaded', function () {
    const okBtn = document.getElementById('confirmOk');
    const cancelBtn = document.getElementById('confirmCancel');
    if (okBtn) {
        okBtn.addEventListener('click', function () {
            const cb = confirmCallback;
            confirmCallback = null;
            closeConfirmModal();
            if (cb) cb();
        });
    }
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function () {
            confirmCallback = null;
            closeConfirmModal();
        });
    }

    const toggleArchivedBtn = document.getElementById('toggleArchived');
    if (toggleArchivedBtn) {
        toggleArchivedBtn.addEventListener('click', toggleArchivedView);
    }

    const searchInput = document.getElementById('searchStudent');
    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }

    const confirmOverlay = document.getElementById('confirmOverlay');
    if (confirmOverlay) {
        confirmOverlay.addEventListener('click', closeConfirmModal);
    }

    const confirmClose = document.getElementById('confirmClose');
    if (confirmClose) {
        confirmClose.addEventListener('click', closeConfirmModal);
    }

    const receiptUploadOverlay = document.getElementById('receiptUploadOverlay');
    if (receiptUploadOverlay) {
        receiptUploadOverlay.addEventListener('click', closeReceiptUploadModal);
    }

    const successOverlay = document.getElementById('successOverlay');
    if (successOverlay) {
        successOverlay.addEventListener('click', closeSuccessModal);
    }

    // Ensure initial render after DOM is fully ready
    setTimeout(() => loadStudents(), 0);
});

async function viewStudentProfile(studentId) {
    try {
        const res = await fetch(`../api/get_student_profile.php?id=${encodeURIComponent(studentId)}`);
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Failed to load profile');

        const s = data.data;
        const overlay = document.getElementById('studentProfileOverlay');
        const modal = document.getElementById('studentProfileModal');
        const body = document.getElementById('studentProfileBody');
        if (!overlay || !modal || !body) return;

        // Build header (icon + title)
        const headerEl = modal.querySelector('.profile-header-container');
        if (!headerEl) {
          const titleContainer = document.createElement('div');
          titleContainer.className = 'profile-header-container';
          titleContainer.innerHTML = `
            <div class="profile-header">
              <div class="badge-icon"><i class="fas fa-id-card"></i></div>
              <h3 class="profile-title">Student Profile</h3>
            </div>
            <div class="divider"></div>
          `;
          modal.querySelector('.modal-content')?.insertBefore(titleContainer, body);
        }

        body.innerHTML = `
          <div class="profile-card">
            <div class="profile-label">Student ID</div>
            <div class="profile-value">${s.id || ''}</div>
          </div>
          <div class="profile-card">
            <div class="profile-label">Name</div>
            <div class="profile-value">${[s.first_name, s.middle_name, s.last_name].filter(Boolean).join(' ')}</div>
          </div>
          <div class="profile-card">
            <div class="profile-label">Email</div>
            <div class="profile-value">${s.email || ''}</div>
          </div>
          <div class="profile-card">
            <div class="profile-label">Course/Year/Section</div>
            <div class="profile-value">${s.course || ''} / ${s.year_level || ''} / ${s.section || ''}</div>
          </div>
          <div class="profile-card">
            <div class="profile-label">Gender</div>
            <div class="profile-value">${s.gender || ''}</div>
          </div>
          <div class="profile-card">
            <div class="profile-label">Age</div>
            <div class="profile-value">${s.age ?? ''}</div>
          </div>
          <div class="profile-card">
            <div class="profile-label">Membership Status</div>
            <div class="profile-value ${((s.membership_fee_status||'unpaid')==='paid') ? '' : 'muted'}">${(s.membership_fee_status||'unpaid').toUpperCase()}</div>
          </div>
          <div class="profile-card">
            <div class="profile-label">Paid At</div>
            <div class="profile-value">${s.membership_fee_paid_at || '-'}</div>
          </div>
          <div class="profile-card" style="grid-column:1 / -1;">
            <div class="profile-label">Receipt</div>
            <div class="profile-value">${s.membership_fee_receipt ? `<a href=\"../uploads/membership-receipts/${s.membership_fee_receipt}\" target=\"_blank\">View Receipt</a>` : 'None'}</div>
          </div>
        `;

        overlay.classList.add('show');
        modal.classList.add('show');
    } catch (e) {
        alert(e.message || 'Failed to load profile');
    }
}

function closeStudentProfileModal() {
    const overlay = document.getElementById('studentProfileOverlay');
    const modal = document.getElementById('studentProfileModal');
    if (overlay) overlay.classList.remove('show');
    if (modal) modal.classList.remove('show');
}
