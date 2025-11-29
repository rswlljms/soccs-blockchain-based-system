<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../templates/login.php");
    exit;
}
?>
<?php include('../components/sidebar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Approvals</title>
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/students.css">
    <link rel="stylesheet" href="../assets/css/admin-table-styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .page-title {
            position: relative;
            padding-bottom: 12px;
        }
        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(to right, #9933ff, #6610f2);
            border-radius: 2px;
        }
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin: 24px 0;
        }
        .stat-card {
            background: linear-gradient(135deg, #9933ff 0%, #4B0082 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .stat-card.approved {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        .stat-card.rejected {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.95;
        }
        .filters-container {
            display: flex;
            gap: 12px;
            margin: 24px 0;
            flex-wrap: wrap;
            align-items: center;
        }
        .filters-container select {
            height: 42px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 32px 8px 12px;
            background-color: white;
            min-width: 160px;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
        }
        .table-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .table-header {
            background: linear-gradient(135deg, #9933ff 0%, #6610f2 100%);
            color: white;
            padding: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
            padding: 1rem;
            text-align: left;
            border-bottom: 2px solid #e2e8f0;
        }
        .table td {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .table tbody tr:hover {
            background: #f8f9fa;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-approved {
            background: #d4edda;
            color: #155724;
        }
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }
        .btn-approve {
            background: #28a745;
            color: white;
        }
        .btn-approve:hover {
            background: #218838;
        }
        .btn-reject {
            background: #dc3545;
            color: white;
        }
        .btn-reject:hover {
            background: #c82333;
        }
        .btn-view {
            background: #17a2b8;
            color: white;
        }
        .btn-view:hover {
            background: #138496;
        }
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            display: none;
        }
        .modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border-radius: 12px;
            padding: 2rem;
            max-width: 500px;
            width: 90%;
            z-index: 1001;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .modal-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
        }
        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6c757d;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #495057;
        }
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            font-size: 0.9rem;
        }
        .form-control:focus {
            outline: none;
            border-color: #667eea;
        }
        .modal-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 1.5rem;
        }
        .btn-cancel {
            background: #6c757d;
            color: white;
        }
        .btn-cancel:hover {
            background: #5a6268;
        }
        .loading {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
        }
        .no-data {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        .no-data i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        .loading {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
        }
        .no-data {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        .no-data i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        /* Toast Notifications */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .toast {
            min-width: 300px;
            padding: 16px 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.3s ease;
            border-left: 4px solid #333;
        }
        .toast.success {
            border-left-color: #28a745;
        }
        .toast.error {
            border-left-color: #dc3545;
        }
        .toast.info {
            border-left-color: #17a2b8;
        }
        .toast-icon {
            font-size: 20px;
            flex-shrink: 0;
        }
        .toast.success .toast-icon {
            color: #28a745;
        }
        .toast.error .toast-icon {
            color: #dc3545;
        }
        .toast.info .toast-icon {
            color: #17a2b8;
        }
        .toast-message {
            flex: 1;
            color: #333;
            font-size: 14px;
        }
        .toast-close {
            background: none;
            border: none;
            font-size: 20px;
            color: #999;
            cursor: pointer;
            padding: 0;
            margin-left: 8px;
        }
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
        
        @media (max-width: 768px) {
            .filters-container {
                flex-direction: column;
            }
            .filters-container select {
                min-width: 100%;
            }
            .table {
                font-size: 0.8rem;
            }
            .table th,
            .table td {
                padding: 0.5rem;
            }
            .action-buttons {
                flex-direction: column;
            }
            .toast-container {
                right: 10px;
                left: 10px;
                top: 10px;
            }
            .toast {
                min-width: auto;
            }
        }
    </style>
</head>
<body>
    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>
    <div class="main-content">
        <div class="content-wrapper">
            <h1 class="page-title">
                <i class="fas fa-user-check"></i> Student Approvals
            </h1>

            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-number" id="pendingCount">0</div>
                    <div class="stat-label">Pending Approvals</div>
                        </div>
                <div class="stat-card approved">
                    <div class="stat-number" id="approvedCount">0</div>
                    <div class="stat-label">Approved</div>
                        </div>
                <div class="stat-card rejected">
                    <div class="stat-number" id="rejectedCount">0</div>
                    <div class="stat-label">Rejected</div>
                </div>
            </div>

            <div class="filters-container">
                <select id="statusFilter">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
                <select id="courseFilter">
                    <option value="">All Courses</option>
                    <option value="BSIT">BSIT</option>
                    <option value="BSCS">BSCS</option>
                </select>
                <select id="yearFilter">
                    <option value="">All Years</option>
                    <option value="1">1st Year</option>
                    <option value="2">2nd Year</option>
                    <option value="3">3rd Year</option>
                    <option value="4">4th Year</option>
                </select>
            </div>

            <div class="table-container">
                <div class="table-header">
                    <i class="fas fa-list"></i> Student Registration Requests
                </div>
                <div id="registrationsTable">
                    <div class="loading">
                        <i class="fas fa-spinner fa-spin"></i> Loading registrations...
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approval Confirmation Modal -->
    <div class="modal-overlay" id="approvalOverlay"></div>
    <div class="modal" id="approvalModal">
        <div class="modal-header">
            <h3 class="modal-title">Approve Registration</h3>
            <button class="close-btn" onclick="closeModal('approvalModal')">&times;</button>
        </div>
        <div style="padding: 1rem;">
            <p style="margin-bottom: 1.5rem; color: #495057;">Are you sure you want to approve this student registration?</p>
            <div class="modal-actions">
                <button type="button" class="btn btn-cancel" onclick="closeModal('approvalModal')">Cancel</button>
                <button type="button" class="btn btn-approve" onclick="confirmApproval()">Approve</button>
            </div>
        </div>
    </div>

    <!-- Rejection Modal -->
    <div class="modal-overlay" id="rejectionOverlay"></div>
    <div class="modal" id="rejectionModal">
        <div class="modal-header">
            <h3 class="modal-title">Reject Registration</h3>
            <button class="close-btn" onclick="closeModal('rejectionModal')">&times;</button>
        </div>
        <form id="rejectionForm">
            <div class="form-group">
                <label class="form-label">Reason for Rejection</label>
                <textarea class="form-control" id="rejectionReason" rows="4" placeholder="Please provide a reason for rejecting this registration..." required></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-cancel" onclick="closeModal('rejectionModal')">Cancel</button>
                <button type="submit" class="btn btn-reject">Reject Registration</button>
            </div>
        </form>
    </div>

    <!-- View Details Modal -->
    <div class="modal-overlay" id="viewOverlay"></div>
    <div class="modal" id="viewModal" style="max-width: 800px; max-height: 90vh; overflow-y: auto;">
        <div class="modal-header">
            <h3 class="modal-title">Registration Details</h3>
            <button class="close-btn" onclick="closeModal('viewModal')">&times;</button>
        </div>
        <div id="viewContent" style="padding: 1rem;">
            <!-- Content will be loaded here -->
        </div>
            </div>

    <script>
        let currentRegistrations = [];
        let currentStudentId = null;

        document.addEventListener('DOMContentLoaded', function() {
            loadRegistrations();
            
            document.getElementById('statusFilter').addEventListener('change', filterRegistrations);
            document.getElementById('courseFilter').addEventListener('change', filterRegistrations);
            document.getElementById('yearFilter').addEventListener('change', filterRegistrations);
            
            document.getElementById('rejectionForm').addEventListener('submit', handleRejection);
            
            // Close modals when clicking overlay
            document.getElementById('viewOverlay')?.addEventListener('click', () => closeModal('viewModal'));
            document.getElementById('rejectionOverlay')?.addEventListener('click', () => closeModal('rejectionModal'));
            document.getElementById('approvalOverlay')?.addEventListener('click', () => closeModal('approvalModal'));
        });

        async function loadRegistrations() {
            try {
                const response = await fetch('../api/get_pending_students.php');
                const data = await response.json();
                
                if (data.success) {
                    currentRegistrations = data.data;
                    updateStats(data.stats);
                    displayRegistrations(currentRegistrations);
                } else {
                    showError('Failed to load registrations: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                showError('Failed to load registrations');
            }
        }

        function updateStats(stats) {
            document.getElementById('pendingCount').textContent = stats.pending || 0;
            document.getElementById('approvedCount').textContent = stats.approved || 0;
            document.getElementById('rejectedCount').textContent = stats.rejected || 0;
        }

        function displayRegistrations(registrations) {
            const container = document.getElementById('registrationsTable');
            
            if (registrations.length === 0) {
                container.innerHTML = `
                    <div class="no-data">
                        <i class="fas fa-inbox"></i>
                        <p>No registrations found</p>
                    </div>
                `;
                return;
            }

            const tableHTML = `
                <table class="table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Year</th>
                            <th>Status</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${registrations.map(reg => `
                            <tr>
                                <td><strong>${reg.id}</strong></td>
                                <td>${reg.first_name} ${reg.middle_name} ${reg.last_name}</td>
                                <td>${reg.course}</td>
                                <td>${reg.year_level}</td>
                                <td><span class="status-badge status-${reg.approval_status}">${reg.approval_status}</span></td>
                                <td>${new Date(reg.created_at).toLocaleDateString()}</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-view" data-action="view" data-id="${reg.id}">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        ${reg.approval_status === 'pending' ? `
                                            <button class="btn btn-approve" data-action="approve" data-id="${reg.id}">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                            <button class="btn btn-reject" data-action="reject" data-id="${reg.id}">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        ` : reg.approval_status === 'rejected' ? `
                                            <button class="btn btn-approve" data-action="approve" data-id="${reg.id}">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        ` : ''}
                                    </div>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
            
            container.innerHTML = tableHTML;
            
            // Attach event listeners to buttons
            container.querySelectorAll('.btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const action = this.getAttribute('data-action');
                    const studentId = this.getAttribute('data-id');
                    
                    console.log('Button clicked:', action, studentId);
                    
                    if (action === 'view') {
                        viewDetails(studentId);
                    } else if (action === 'approve') {
                        approveStudent(studentId);
                    } else if (action === 'reject') {
                        showRejectionModal(studentId);
                    }
                });
            });
        }

        function filterRegistrations() {
            const statusFilter = document.getElementById('statusFilter').value;
            const courseFilter = document.getElementById('courseFilter').value;
            const yearFilter = document.getElementById('yearFilter').value;
            
            let filtered = currentRegistrations;
            
            if (statusFilter) {
                filtered = filtered.filter(reg => reg.approval_status === statusFilter);
            }
            
            if (courseFilter) {
                filtered = filtered.filter(reg => reg.course === courseFilter);
            }
            
            if (yearFilter) {
                filtered = filtered.filter(reg => reg.year_level == yearFilter);
            }
            
            displayRegistrations(filtered);
        }

        async function viewDetails(studentId) {
            try {
                const response = await fetch(`../api/view_document.php?student_id=${studentId}`);
                const data = await response.json();
                
                if (data.success) {
                    const reg = data.data;
                    
                    // Determine COR file type
                    let corDisplay = '';
                    if (reg.cor_file) {
                        const fileExt = reg.cor_file.split('.').pop().toLowerCase();
                        if (fileExt === 'pdf') {
                            corDisplay = `
                                <div style="margin-top: 1rem;">
                                    <strong>Certificate of Registration (COR):</strong><br>
                                    <embed src="../${reg.cor_file}" type="application/pdf" width="100%" height="500px" style="border-radius: 8px; margin-top: 0.5rem;">
                                    <br>
                                    <a href="../${reg.cor_file}" target="_blank" class="btn btn-view" style="margin-top: 0.5rem;">
                                        <i class="fas fa-external-link-alt"></i> Open in New Tab
                                    </a>
            </div>
                            `;
                        } else {
                            corDisplay = `
                                <div style="margin-top: 1rem;">
                                    <strong>Certificate of Registration (COR):</strong><br>
                                    <img src="../${reg.cor_file}" style="max-width: 100%; height: auto; border-radius: 8px; margin-top: 0.5rem;" alt="COR">
        </div>
                            `;
                        }
                    }
                    
                    const content = `
                        <div style="display: grid; gap: 1rem;">
                            <div style="background: #f8f9fa; padding: 1rem; border-radius: 6px;">
                                <h4 style="margin: 0 0 0.5rem 0; color: #2c3e50;">Personal Information</h4>
                                <div><strong>Student ID:</strong> ${reg.id}</div>
                                <div><strong>Name:</strong> ${reg.first_name} ${reg.middle_name} ${reg.last_name}</div>
                                <div><strong>Email:</strong> ${reg.email}</div>
                                <div><strong>Age:</strong> ${reg.age}</div>
                                <div><strong>Gender:</strong> ${reg.gender}</div>
    </div>

                            <div style="background: #f8f9fa; padding: 1rem; border-radius: 6px;">
                                <h4 style="margin: 0 0 0.5rem 0; color: #2c3e50;">Academic Information</h4>
                                <div><strong>Course:</strong> ${reg.course}</div>
                                <div><strong>Year Level:</strong> ${reg.year_level}</div>
                                <div><strong>Section:</strong> ${reg.section}</div>
            </div>
                            
                            <div style="background: #f8f9fa; padding: 1rem; border-radius: 6px;">
                                <h4 style="margin: 0 0 0.5rem 0; color: #2c3e50;">Registration Status</h4>
                                <div><strong>Status:</strong> <span class="status-badge status-${reg.approval_status}">${reg.approval_status}</span></div>
                                <div><strong>Registered:</strong> ${new Date(reg.created_at).toLocaleString()}</div>
                                ${reg.rejection_reason ? `<div><strong>Rejection Reason:</strong> ${reg.rejection_reason}</div>` : ''}
    </div>

                            <div style="margin-top: 1rem;">
                                <strong>Student ID Image:</strong><br>
                                <img src="../${reg.student_id_image}" 
                                     onclick="window.open('../${reg.student_id_image}', '_blank')" 
                                     style="max-width: 100%; height: auto; border-radius: 8px; margin-top: 0.5rem; cursor: pointer;" 
                                     alt="Student ID" 
                                     title="Click to view full size">
    </div>

                            ${corDisplay}
    </div>
                    `;
                    
                    document.getElementById('viewContent').innerHTML = content;
                    showModal('viewModal');
                } else {
                    showError('Failed to load student details: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                showError('Failed to load student details');
            }
        }

        async function approveStudent(studentId) {
            currentStudentId = studentId;
            showModal('approvalModal');
        }

        async function confirmApproval() {
            closeModal('approvalModal');
            showLoading();
            
            try {
                const response = await fetch('../api/approve_student.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ student_id: currentStudentId })
                });
                
                const data = await response.json();
                hideLoading();
                
                if (data.success) {
                    showSuccess('Student approved successfully!');
                    setTimeout(() => loadRegistrations(), 1000);
                } else {
                    showError('Failed to approve student: ' + data.message);
                }
            } catch (error) {
                hideLoading();
                console.error('Error:', error);
                showError('Failed to approve student');
            }
        }

        function showRejectionModal(studentId) {
            currentStudentId = studentId;
            document.getElementById('rejectionReason').value = '';
            showModal('rejectionModal');
        }

        async function handleRejection(e) {
            e.preventDefault();
            
            const reason = document.getElementById('rejectionReason').value.trim();
            if (!reason) {
                showError('Please provide a reason for rejection');
                return;
            }
            
            closeModal('rejectionModal');
            showLoading();
            
            try {
                const response = await fetch('../api/approve_student.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ 
                        student_id: currentStudentId,
                        action: 'reject',
                        reason: reason
                    })
                });
                
                const data = await response.json();
                hideLoading();
                
                if (data.success) {
                    showSuccess('Student registration rejected successfully!');
                    setTimeout(() => loadRegistrations(), 1000);
                } else {
                    showError('Failed to reject student: ' + data.message);
                }
            } catch (error) {
                hideLoading();
                console.error('Error:', error);
                showError('Failed to reject student');
            }
        }

        function showModal(modalId) {
            const modal = document.getElementById(modalId);
            const overlay = document.getElementById(modalId.replace('Modal', 'Overlay'));
            if (modal) {
                modal.style.display = 'block';
                console.log('Modal opened:', modalId);
            }
            if (overlay) {
                overlay.style.display = 'block';
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            const overlay = document.getElementById(modalId.replace('Modal', 'Overlay'));
            if (modal) {
                modal.style.display = 'none';
            }
            if (overlay) {
                overlay.style.display = 'none';
            }
        }

        function showToast(message, type = 'info') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            
            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                info: 'fa-info-circle'
            };
            
            toast.innerHTML = `
                <i class="fas ${icons[type]} toast-icon"></i>
                <div class="toast-message">${message}</div>
                <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
            `;
            
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        function showSuccess(message) {
            showToast(message, 'success');
        }

        function showError(message) {
            showToast(message, 'error');
        }

        function showInfo(message) {
            showToast(message, 'info');
        }

        function showLoading() {
            let loader = document.getElementById('pageLoader');
            if (!loader) {
                loader = document.createElement('div');
                loader.id = 'pageLoader';
                loader.innerHTML = `
                    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; display: flex; align-items: center; justify-content: center;">
                        <div style="background: white; padding: 2rem; border-radius: 12px; text-align: center;">
                            <div style="width: 50px; height: 50px; border: 4px solid #f3f3f3; border-top: 4px solid #9933ff; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 1rem;"></div>
                            <p style="margin: 0; color: #333;">Processing...</p>
                        </div>
                    </div>
                `;
                document.body.appendChild(loader);
                
                const style = document.createElement('style');
                style.textContent = '@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }';
                document.head.appendChild(style);
            }
            loader.style.display = 'block';
        }

        function hideLoading() {
            const loader = document.getElementById('pageLoader');
            if (loader) {
                loader.style.display = 'none';
            }
        }
    </script>
</body>
</html>
