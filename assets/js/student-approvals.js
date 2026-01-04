// Global variables
let pendingStudents = [];
let summaryData = {
    pending: 0,
    approved_today: 0,
    rejected_today: 0
};

// Helper function for year level suffix
function getYearSuffix(year) {
    const suffixes = {
        '1': 'st',
        '2': 'nd',
        '3': 'rd',
        '4': 'th'
    };
    return suffixes[year] || 'th';
}

// Helper function to format full name, handling null/empty middle names
function formatFullName(firstName, middleName, lastName) {
    const nameParts = [firstName, middleName, lastName].filter(part => part && part !== 'null' && part.trim() !== '');
    return nameParts.join(' ');
}

// Function to load student registrations from API or use mock data
async function loadStudentRegistrations() {
    // Check if we're in development mode (no database) - use mock data
    const useMockData = true; // Set to false when database is ready
    
    if (useMockData) {
        // Mock data for front-end development
        pendingStudents = [
            {
                id: 'ST2024001',
                firstName: 'Maria',
                middleName: 'C.',
                lastName: 'Garcia',
                course: 'BSIT',
                yearLevel: '1',
                section: 'A',
                age: 18,
                gender: 'female',
                status: 'pending',
                submittedAt: '2024-12-17 10:30:00'
            },
            {
                id: 'ST2024002',
                firstName: 'John',
                middleName: 'M.',
                lastName: 'Santos',
                course: 'BSCS',
                yearLevel: '2',
                section: 'B',
                age: 19,
                gender: 'male',
                status: 'pending',
                submittedAt: '2024-12-17 11:15:00'
            },
            {
                id: 'ST2024003',
                firstName: 'Ana',
                middleName: 'R.',
                lastName: 'Cruz',
                course: 'BSIT',
                yearLevel: '3',
                section: 'A',
                age: 20,
                gender: 'female',
                status: 'approved',
                submittedAt: '2024-12-17 09:45:00'
            },
            {
                id: 'ST2024004',
                firstName: 'Pedro',
                middleName: 'L.',
                lastName: 'Reyes',
                course: 'BSCS',
                yearLevel: '1',
                section: 'C',
                age: 18,
                gender: 'male',
                status: 'rejected',
                submittedAt: '2024-12-17 08:20:00'
            }
        ];
        
        // Mock summary data
        summaryData = {
            pending: 2,
            approved_today: 1,
            rejected_today: 1
        };
        
        updateSummaryCards();
        renderApprovalsTable();
        return;
    }
    
    // Production API call (when database is ready)
    try {
        const response = await fetch('../api/get_pending_students.php');
        const data = await response.json();
        
        if (data.status === 'success') {
            // Transform API data to match frontend format
            pendingStudents = data.students.map(student => ({
                id: student.id,
                firstName: student.first_name,
                middleName: student.middle_name,
                lastName: student.last_name,
                course: student.course,
                yearLevel: student.year_level,
                section: student.section,
                age: student.age,
                gender: student.gender,
                status: student.approval_status,
                submittedAt: student.created_at
            }));
            
            summaryData = data.summary;
            updateSummaryCards();
            renderApprovalsTable();
        } else {
            console.error('Failed to load student registrations:', data.message);
            alert('Failed to load student registrations');
        }
    } catch (error) {
        console.error('Error loading student registrations:', error);
        alert('Error loading student registrations');
    }
}

// Function to update summary cards
function updateSummaryCards() {
    document.getElementById('pendingCount').textContent = summaryData.pending;
    document.getElementById('approvedCount').textContent = summaryData.approved_today;
    document.getElementById('rejectedCount').textContent = summaryData.rejected_today;
}

// Function to render approvals table
function renderApprovalsTable(students = pendingStudents) {
    const tbody = document.getElementById('approvals-table-body');
    tbody.innerHTML = '';

    if (students.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" style="text-align: center; padding: 2rem;">
                    No student registrations found
                </td>
            </tr>
        `;
        return;
    }

    students.forEach(student => {
        const fullName = formatFullName(student.firstName, student.middleName, student.lastName);
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${fullName}</td>
            <td>${student.id}</td>
            <td>${student.course}</td>
            <td>${student.yearLevel}${getYearSuffix(student.yearLevel)} Year</td>
            <td>${student.section}</td>
            <td>
                <span class="status-badge ${student.status}">
                    ${student.status.charAt(0).toUpperCase() + student.status.slice(1)}
                </span>
            </td>
            <td>
                ${student.status === 'pending' ? `
                    <div class="action-buttons">
                        <button class="btn-approve" onclick="approveStudent('${student.id}')">
                            <i class="fas fa-check"></i> Approve
                        </button>
                        <button class="btn-reject" onclick="rejectStudent('${student.id}')">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    </div>
                ` : '-'}
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Function to apply filters
function applyFilters() {
    const courseFilter = document.getElementById('filterCourse').value;
    const yearFilter = document.getElementById('filterYear').value;
    const sectionFilter = document.getElementById('filterSection').value.trim().toUpperCase();
    const statusFilter = document.getElementById('filterStatus').value;

    const filteredStudents = pendingStudents.filter(student => {
        return (courseFilter === 'All' || student.course === courseFilter) &&
            (yearFilter === 'All' || student.yearLevel === yearFilter) &&
            (sectionFilter === '' || student.section.toUpperCase() === sectionFilter) &&
            (statusFilter === 'All' || student.status === statusFilter);
    });

    renderApprovalsTable(filteredStudents);
}

// Event listener for filter button
document.getElementById('applyFilters').addEventListener('click', applyFilters);

// Function to show image modal
function showImageModal(imageSrc) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    modalImage.src = imageSrc;
    modal.classList.add('show');
}

// Function to hide image modal
function hideImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.remove('show');
}

// Event listener for image modal close
document.getElementById('imageModal').addEventListener('click', hideImageModal);

// Function to approve student
function approveStudent(studentId) {
    const student = pendingStudents.find(s => s.id === studentId);
    if (!student) return;

    const title = 'Approve Student Registration';
    const message = `Approve registration for ${student.firstName} ${student.lastName} (${student.id})?`;
    
    openConfirmModal(title, message, async function() {
        const useMockData = true; // Same flag as in loadStudentRegistrations
        
        if (useMockData) {
            // Mock approval for front-end development
            student.status = 'approved';
            summaryData.pending--;
            summaryData.approved_today++;
            
            updateSummaryCards();
            renderApprovalsTable();
            
            alert('Student registration approved successfully!');
            alert(`Student credentials:\nEmail: ${studentId}@student.edu\nDefault Password: ${studentId}`);
            return;
        }
        
        // Production API call (when database is ready)
        try {
            const response = await fetch('../api/approve_student.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    student_id: studentId,
                    action: 'approve'
                })
            });
            
            const data = await response.json();
            
            if (data.status === 'success') {
                alert('Student registration approved successfully!');
                if (data.student_credentials) {
                    alert(`Student credentials:\nEmail: ${data.student_credentials.email}\nDefault Password: ${data.student_credentials.default_password}`);
                }
                loadStudentRegistrations(); // Reload data
            } else {
                alert('Failed to approve registration: ' + data.message);
            }
        } catch (error) {
            console.error('Error approving student:', error);
            alert('Error approving student registration');
        }
    });
}

// Function to reject student
function rejectStudent(studentId) {
    const student = pendingStudents.find(s => s.id === studentId);
    if (!student) return;

    const title = 'Reject Student Registration';
    const message = `Reject registration for ${student.firstName} ${student.lastName} (${student.id})?`;
    
    openConfirmModal(title, message, async function() {
        const useMockData = true; // Same flag as in loadStudentRegistrations
        
        if (useMockData) {
            // Mock rejection for front-end development
            student.status = 'rejected';
            summaryData.pending--;
            summaryData.rejected_today++;
            
            updateSummaryCards();
            renderApprovalsTable();
            
            alert('Student registration rejected.');
            return;
        }
        
        // Production API call (when database is ready)
        try {
            const response = await fetch('../api/approve_student.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    student_id: studentId,
                    action: 'reject'
                })
            });
            
            const data = await response.json();
            
            if (data.status === 'success') {
                alert('Student registration rejected.');
                loadStudentRegistrations(); // Reload data
            } else {
                alert('Failed to reject registration: ' + data.message);
            }
        } catch (error) {
            console.error('Error rejecting student:', error);
            alert('Error rejecting student registration');
        }
    });
}

// Simple reusable confirmation modal
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

function closeConfirmModal() {
    const overlay = document.getElementById('confirmOverlay');
    const modal = document.getElementById('confirmModal');
    
    if (overlay) overlay.classList.remove('show');
    if (modal) modal.classList.remove('show');
}

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    const okBtn = document.getElementById('confirmOk');
    const cancelBtn = document.getElementById('confirmCancel');
    
    if (okBtn) {
        okBtn.addEventListener('click', function() {
            const cb = confirmCallback;
            confirmCallback = null;
            closeConfirmModal();
            if (cb) cb();
        });
    }
    
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            confirmCallback = null;
            closeConfirmModal();
        });
    }

    // Load data from API
    loadStudentRegistrations();
});
