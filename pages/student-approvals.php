<?php
session_start();
require_once '../includes/page_access.php';
checkPageAccess(['verify_students']);
include('../components/sidebar.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Approvals</title>
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/students.css">
    <link rel="stylesheet" href="../assets/css/admin-table-styles.css">
    <link rel="stylesheet" href="../assets/css/user-management.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4B0082, #9933ff);
            --primary-hover: linear-gradient(135deg, #3a0066, #7a29cc);
            --secondary-color: #f8f9fa;
            --text-primary: #1f2937;
            --text-secondary: #4b5563;
            --border-color: #e5e7eb;
            --success-color: #10b981;
            --error-color: #ef4444;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --radius-sm: 8px;
            --radius-md: 12px;
            --transition: all 0.2s ease-in-out;
        }

        .main-content {
            margin-left: 280px;
            padding: 32px;
            width: calc(100% - 280px);
            min-height: 100vh;
        }

        .content-wrapper {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .header-section {
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 8px;
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
            background: var(--primary-gradient);
            border-radius: 2px;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: white;
            border-radius: var(--radius-md);
            padding: 28px;
            display: flex;
            align-items: center;
            gap: 18px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            border: 1px solid transparent;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: rgba(153, 51, 255, 0.1);
        }

        .stat-card .card-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }

        .stat-card:nth-child(1) .card-icon {
            background: linear-gradient(135deg, #f3e9ff 0%, #e9d5ff 100%);
        }
        .stat-card:nth-child(1) .card-icon i { color: #9933ff; }

        .stat-card.approved .card-icon {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        }
        .stat-card.approved .card-icon i { color: #10b981; }

        .stat-card.rejected .card-icon {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        }
        .stat-card.rejected .card-icon i { color: #ef4444; }

        .stat-card .card-info {
            flex: 1;
        }

        .stat-card .card-info h3 {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
            margin: 0 0 8px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card .card-info p {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }

        .filters-container {
            display: flex;
            gap: 16px;
            margin: 0 0 24px 0;
            flex-wrap: wrap;
            align-items: center;
            background: white;
            padding: 24px;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
        }

        .filters-container select {
            height: 44px;
            border: 2px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 12px 36px 12px 18px;
            background-color: white;
            min-width: 180px;
            font-size: 14px;
            font-family: 'Work Sans', sans-serif;
            color: var(--text-primary);
            cursor: pointer;
            transition: var(--transition);
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23666' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 12px 12px;
        }

        .filters-container select:hover {
            border-color: #9933ff;
        }

        .filters-container select:focus {
            outline: none;
            border-color: #9933ff;
            box-shadow: 0 0 0 4px rgba(153, 51, 255, 0.1);
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-pending {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }
        .status-approved {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }
        .status-rejected {
            background: rgba(239, 68, 68, 0.1);
            color: var(--error-color);
        }
        /* Action Buttons - Using standardized styles from admin-table-styles.css */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 1000;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.show {
            display: block;
            opacity: 1;
        }

        .modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.95);
            background: white;
            border-radius: var(--radius-md);
            padding: 0;
            max-width: 500px;
            width: 90%;
            z-index: 1001;
            display: none;
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .modal.show {
            display: block;
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 28px 32px;
            background: var(--primary-gradient);
            border-radius: var(--radius-md) var(--radius-md) 0 0;
        }

        .modal-title {
            font-size: 24px;
            font-weight: 600;
            color: white;
            margin: 0;
            letter-spacing: -0.02em;
        }

        .close-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: white;
            padding: 8px;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .close-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }
        .modal-body {
            padding: 32px;
            max-height: calc(90vh - 140px);
            overflow-y: auto;
        }

        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
            color: var(--text-primary);
        }
        .form-control {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid var(--border-color);
            border-radius: var(--radius-sm);
            font-size: 15px;
            font-family: 'Work Sans', sans-serif;
            color: var(--text-primary);
            background: white;
            transition: var(--transition);
        }
        .form-control:focus {
            outline: none;
            border-color: #9933ff;
            box-shadow: 0 0 0 4px rgba(153, 51, 255, 0.1);
        }
        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 24px;
        }
        .btn-cancel {
            background: white;
            border: 2px solid var(--border-color);
            color: var(--text-primary);
            font-weight: 600;
            padding: 14px 32px;
            min-width: 120px;
        }
        .btn-cancel:hover {
            border-color: #cbd5e1;
            background: var(--secondary-color);
            transform: translateY(-1px);
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
        
        @media (min-width: 769px) {
            .main-content {
                margin-left: 280px !important;
                width: calc(100% - 280px) !important;
            }
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 20px 16px;
            }

            .content-wrapper {
                padding: 0;
            }

            .filters-container {
                flex-direction: column;
                align-items: stretch;
            }
            .filters-container select {
                width: 100%;
                min-width: 100%;
            }
            .stats-cards {
                grid-template-columns: 1fr;
            }
            .table-container {
                overflow-x: auto;
            }
            .styled-table {
                min-width: 800px;
            }
            .action-buttons {
                flex-wrap: wrap;
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
                    <div class="card-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="card-info">
                        <h3>Pending Approvals</h3>
                        <p id="pendingCount">0</p>
                    </div>
                        </div>
                <div class="stat-card approved">
                    <div class="card-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="card-info">
                        <h3>Approved</h3>
                        <p id="approvedCount">0</p>
                    </div>
                        </div>
                <div class="stat-card rejected">
                    <div class="card-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="card-info">
                        <h3>Rejected</h3>
                        <p id="rejectedCount">0</p>
                    </div>
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
                <table class="styled-table">
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
                    <tbody id="registrationsTable">
                        <tr>
                            <td colspan="7">
                    <div class="loading">
                        <i class="fas fa-spinner fa-spin"></i> Loading registrations...
                    </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
        <div class="modal-body">
            <p style="margin-bottom: 1.5rem; color: var(--text-secondary);">Are you sure you want to approve this student registration?</p>
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
        <div class="modal-body">
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
    </div>

    <!-- View Details Modal -->
    <div class="modal-overlay" id="viewOverlay"></div>
    <div class="modal" id="viewModal" style="max-width: 800px; max-height: 90vh; overflow-y: auto;">
        <div class="modal-header">
            <h3 class="modal-title">Registration Details</h3>
            <button class="close-btn" onclick="closeModal('viewModal')">&times;</button>
        </div>
        <div class="modal-body" id="viewContent">
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
                    <tr>
                        <td colspan="7">
                    <div class="no-data">
                        <i class="fas fa-inbox"></i>
                        <p>No registrations found</p>
                    </div>
                        </td>
                    </tr>
                `;
                return;
            }

            const tableHTML = registrations.map(reg => `
                            <tr>
                                <td><strong>${reg.id}</strong></td>
                                <td>${reg.first_name} ${reg.middle_name} ${reg.last_name}</td>
                                <td>${reg.course}</td>
                                <td>${reg.year_level}</td>
                                <td><span class="status-badge status-${reg.approval_status}">${reg.approval_status}</span></td>
                                <td>${new Date(reg.created_at).toLocaleDateString()}</td>
                                <td>
                                    <div class="action-buttons">
                            <button class="action-btn view" data-action="view" data-id="${reg.id}" title="View Details">
                                <i class="fas fa-eye"></i>
                                        </button>
                                        ${reg.approval_status === 'pending' ? `
                                <button class="action-btn approve" data-action="approve" data-id="${reg.id}" title="Approve">
                                    <i class="fas fa-check"></i>
                                            </button>
                                <button class="action-btn reject" data-action="reject" data-id="${reg.id}" title="Reject">
                                    <i class="fas fa-times"></i>
                                            </button>
                                        ` : reg.approval_status === 'rejected' ? `
                                <button class="action-btn approve" data-action="approve" data-id="${reg.id}" title="Approve">
                                    <i class="fas fa-check"></i>
                                            </button>
                                        ` : ''}
                                    </div>
                                </td>
                            </tr>
            `).join('');
            
            container.innerHTML = tableHTML;
            
            // Attach event listeners to buttons
            container.querySelectorAll('.action-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const action = this.getAttribute('data-action');
                    const studentId = this.getAttribute('data-id');
                    
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
                                <div style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); padding: 1.75rem; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 2px 4px rgba(0,0,0,0.04);">
                                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #4B0082, #9933ff); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-file-pdf" style="color: white; font-size: 18px;"></i>
                                        </div>
                                        <h4 style="margin: 0; color: #1f2937; font-size: 18px; font-weight: 600;">Certificate of Registration (COR)</h4>
                                    </div>
                                    <div style="border-radius: 10px; overflow: hidden; border: 2px solid #e5e7eb;">
                                        <embed src="../${reg.cor_file}" type="application/pdf" width="100%" height="500px" style="display: block;">
                                    </div>
                                    <a href="../${reg.cor_file}" target="_blank" class="btn btn-view" style="margin-top: 1rem; display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #4B0082, #9933ff); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 500; font-size: 14px; transition: all 0.3s ease;">
                                        <i class="fas fa-external-link-alt"></i> Open in New Tab
                                    </a>
                                </div>
                            `;
                        } else {
                            corDisplay = `
                                <div style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); padding: 1.75rem; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 2px 4px rgba(0,0,0,0.04);">
                                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #4B0082, #9933ff); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-file-alt" style="color: white; font-size: 18px;"></i>
                                        </div>
                                        <h4 style="margin: 0; color: #1f2937; font-size: 18px; font-weight: 600;">Certificate of Registration (COR)</h4>
                                    </div>
                                    <div style="position: relative; border-radius: 10px; overflow: hidden; border: 2px solid #e5e7eb; cursor: pointer; transition: all 0.3s ease;" 
                                         onclick="window.open('../${reg.cor_file}', '_blank')" 
                                         onmouseover="this.style.borderColor='#9933ff'; this.style.boxShadow='0 4px 12px rgba(153, 51, 255, 0.2)'" 
                                         onmouseout="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
                                        <img src="../${reg.cor_file}" style="width: 100%; height: auto; display: block;" alt="COR">
                                        <div style="position: absolute; top: 10px; right: 10px; background: rgba(75, 0, 130, 0.9); color: white; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 6px;">
                                            <i class="fas fa-external-link-alt"></i>
                                            <span>Click to enlarge</span>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
                    }
                    
                    const content = `
                        <div style="display: grid; gap: 1.5rem;">
                            <div style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); padding: 1.75rem; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 2px 4px rgba(0,0,0,0.04);">
                                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #4B0082, #9933ff); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-user" style="color: white; font-size: 18px;"></i>
                                    </div>
                                    <h4 style="margin: 0; color: #1f2937; font-size: 18px; font-weight: 600;">Personal Information</h4>
                                </div>
                                <div style="display: grid; gap: 1rem; padding-left: 0.5rem;">
                                    <div style="display: flex; align-items: start; gap: 0.75rem;">
                                        <span style="color: #6b7280; font-size: 14px; min-width: 100px; font-weight: 500;">Student ID:</span>
                                        <span style="color: #1f2937; font-weight: 600; font-size: 15px;">${reg.id}</span>
                                    </div>
                                    <div style="display: flex; align-items: start; gap: 0.75rem;">
                                        <span style="color: #6b7280; font-size: 14px; min-width: 100px; font-weight: 500;">Name:</span>
                                        <span style="color: #1f2937; font-weight: 600; font-size: 15px;">${reg.first_name} ${reg.middle_name} ${reg.last_name}</span>
                                    </div>
                                    <div style="display: flex; align-items: start; gap: 0.75rem;">
                                        <span style="color: #6b7280; font-size: 14px; min-width: 100px; font-weight: 500;">Email:</span>
                                        <span style="color: #1f2937; font-size: 14px;">${reg.email}</span>
                                    </div>
                                    <div style="display: flex; align-items: start; gap: 0.75rem;">
                                        <span style="color: #6b7280; font-size: 14px; min-width: 100px; font-weight: 500;">Gender:</span>
                                        <span style="color: #1f2937; font-size: 14px; text-transform: capitalize;">${reg.gender}</span>
                                    </div>
                                </div>
                            </div>

                            <div style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); padding: 1.75rem; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 2px 4px rgba(0,0,0,0.04);">
                                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #4B0082, #9933ff); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-graduation-cap" style="color: white; font-size: 18px;"></i>
                                    </div>
                                    <h4 style="margin: 0; color: #1f2937; font-size: 18px; font-weight: 600;">Academic Information</h4>
                                </div>
                                <div style="display: grid; gap: 1rem; padding-left: 0.5rem;">
                                    <div style="display: flex; align-items: start; gap: 0.75rem;">
                                        <span style="color: #6b7280; font-size: 14px; min-width: 100px; font-weight: 500;">Course:</span>
                                        <span style="color: #1f2937; font-weight: 600; font-size: 15px;">${reg.course}</span>
                                    </div>
                                    <div style="display: flex; align-items: start; gap: 0.75rem;">
                                        <span style="color: #6b7280; font-size: 14px; min-width: 100px; font-weight: 500;">Year Level:</span>
                                        <span style="color: #1f2937; font-size: 14px;">${reg.year_level}</span>
                                    </div>
                                    <div style="display: flex; align-items: start; gap: 0.75rem;">
                                        <span style="color: #6b7280; font-size: 14px; min-width: 100px; font-weight: 500;">Section:</span>
                                        <span style="color: #1f2937; font-size: 14px;">${reg.section}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); padding: 1.75rem; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 2px 4px rgba(0,0,0,0.04);">
                                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #4B0082, #9933ff); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-info-circle" style="color: white; font-size: 18px;"></i>
                                    </div>
                                    <h4 style="margin: 0; color: #1f2937; font-size: 18px; font-weight: 600;">Registration Status</h4>
                                </div>
                                <div style="display: grid; gap: 1rem; padding-left: 0.5rem;">
                                    <div style="display: flex; align-items: start; gap: 0.75rem;">
                                        <span style="color: #6b7280; font-size: 14px; min-width: 100px; font-weight: 500;">Status:</span>
                                        <span class="status-badge status-${reg.approval_status}">${reg.approval_status}</span>
                                    </div>
                                    <div style="display: flex; align-items: start; gap: 0.75rem;">
                                        <span style="color: #6b7280; font-size: 14px; min-width: 100px; font-weight: 500;">Registered:</span>
                                        <span style="color: #1f2937; font-size: 14px;">${new Date(reg.created_at).toLocaleString()}</span>
                                    </div>
                                    ${reg.rejection_reason ? `
                                    <div style="display: flex; align-items: start; gap: 0.75rem;">
                                        <span style="color: #6b7280; font-size: 14px; min-width: 100px; font-weight: 500;">Reason:</span>
                                        <span style="color: #ef4444; font-size: 14px;">${reg.rejection_reason}</span>
                                    </div>` : ''}
                                </div>
                            </div>

                            <div style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); padding: 1.75rem; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 2px 4px rgba(0,0,0,0.04);">
                                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #4B0082, #9933ff); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-id-card" style="color: white; font-size: 18px;"></i>
                                    </div>
                                    <h4 style="margin: 0; color: #1f2937; font-size: 18px; font-weight: 600;">Student ID Image</h4>
                                </div>
                                <div style="position: relative; border-radius: 10px; overflow: hidden; border: 2px solid #e5e7eb; cursor: pointer; transition: all 0.3s ease;" 
                                     onclick="window.open('../${reg.student_id_image}', '_blank')" 
                                     onmouseover="this.style.borderColor='#9933ff'; this.style.boxShadow='0 4px 12px rgba(153, 51, 255, 0.2)'" 
                                     onmouseout="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
                                    <img src="../${reg.student_id_image}" 
                                         style="width: 100%; height: auto; display: block;" 
                                         alt="Student ID">
                                    <div style="position: absolute; top: 10px; right: 10px; background: rgba(75, 0, 130, 0.9); color: white; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 6px;">
                                        <i class="fas fa-external-link-alt"></i>
                                        <span>Click to enlarge</span>
                                    </div>
                                </div>
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
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
            if (overlay) {
                overlay.classList.add('show');
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            const overlay = document.getElementById(modalId.replace('Modal', 'Overlay'));
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = '';
            }
            if (overlay) {
                overlay.classList.remove('show');
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
