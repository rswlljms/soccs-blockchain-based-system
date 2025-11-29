<?php include('../components/sidebar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Management</title>
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/students.css">
    <link rel="stylesheet" href="../assets/css/admin-table-styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .student-form-container {
            display: flex;
            gap: 12px;
            margin: 24px 0;
            flex-wrap: wrap;
            align-items: center;
        }
        
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
        
        .student-form-container .input-group {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            flex: 1;
            min-width: 200px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        
        .student-form-container .btn-primary {
            height: 46px;
            display: flex;
            align-items: center;
            gap: 8px;
            background: #9933ff;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 0 24px;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            white-space: nowrap;
        }
        
        .filters-container {
            display: flex;
            gap: 12px;
            margin: 24px 0;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .filters-container select,
        .filters-container input {
            height: 42px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            background-color: white;
            min-width: 160px;
        }
        
        .filters-container select {
            padding-right: 32px;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 12px center;
            background-repeat: no-repeat;
            background-size: 16px;
            appearance: none;
        }
        
        .filters-container button {
            display: flex;
            align-items: center;
            gap: 8px;
            height: 42px;
            padding: 0 20px;
            background: #f8f9fa;
            color: #333;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 500;
            min-width: 96px;
            text-align: center;
        }
        
        .status-badge.paid {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .status-badge.unpaid {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }
        
        /* Make status badge look interactive */
        .status-badge.toggleable {
            cursor: pointer;
            transition: transform 0.05s ease, box-shadow 0.2s ease, background-color 0.2s ease;
            border: 1px dashed rgba(102,16,242,0.35);
        }
        .status-badge.toggleable:hover {
            box-shadow: 0 0 0 3px rgba(102, 16, 242, 0.12);
            background-color: rgba(102, 16, 242, 0.04);
        }
        .status-badge.toggleable:active {
            transform: scale(0.98);
        }

        /* Center the Membership Fee column */
        .styled-table th:nth-child(5),
        .styled-table td:nth-child(5) {
            text-align: center;
        }

        .status-toggle-icon {
            margin-right: 6px;
            font-size: 12px;
        }
        
        /* Custom icon colors for summary cards */
        .summary-card:nth-child(1) .card-icon {
            background: #f3e9ff;
        }
        
        .summary-card:nth-child(1) .card-icon i {
            color: #9933ff;
        }
        
        .summary-card:nth-child(2) .card-icon {
            background: #ffebf3;
        }
        
        .summary-card:nth-child(2) .card-icon i {
            color: #ff3399;
        }
        
        .summary-card:nth-child(3) .card-icon {
            background: #e9f5ff;
        }
        
        .summary-card:nth-child(3) .card-icon i {
            color: #3399ff;
        }
        
        /* Pagination styles */
        .pagination.centered {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            padding: 20px;
            border-top: 1px solid #e5e7eb;
        }
        
        .page-btn {
            background: white;
            border: 1px solid #e5e7eb;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            color: #1f2937;
            text-decoration: none;
            transition: all 0.2s;
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
            color: #4b5563;
            padding: 0 16px;
        }
        
        /* Section summary styles */
        .section-summary {
            margin-top: 32px;
            background: #f8f9fa;
            border-radius: 12px;
            padding: 28px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            display: none;
        }
        
        .section-summary h2 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 24px;
            color: #2c3e50;
            padding-bottom: 12px;
            border-bottom: 3px solid #9933ff;
            display: inline-block;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;
        }
        
        @media (max-width: 768px) {
            .summary-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Modal styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            display: none;
        }
        
        .modal-overlay.show {
            display: block;
        }
        
        .modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border-radius: 12px;
            padding: 0;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            z-index: 1001;
            display: none;
            overflow: hidden;
        }
        
        .modal.show {
            display: block;
        }
        .modal-header { background: linear-gradient(135deg, #4B0082, #9933ff); padding: 24px 32px; display: flex; justify-content: space-between; align-items: center; border-radius: 12px 12px 0 0; }
        .modal-title { font-size: 22px; font-weight: 600; color: #fff; margin: 0; letter-spacing: -0.02em; }
        .modal-close { background: rgba(255,255,255,0.2); border: none; font-size: 18px; color: #fff; cursor: pointer; padding: 8px; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; transition: all .2s; }
        .modal-close:hover { background: rgba(255,255,255,0.3); transform: rotate(90deg); }
        .modal-footer { padding: 20px 24px; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 12px; background: #fafbfc; border-radius: 0 0 12px 12px; }
        .form-body { padding: 24px; max-height: calc(90vh - 120px); overflow-y: auto; }
        
        .modal-content {
            max-width: 500px;
        }
        
        .modal-icon {
            font-size: 48px;
            color: #9933ff;
            text-align: center;
            margin-bottom: 1rem;
        }
        
        .modal-title {
            font-size: 24px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 1rem;
            color: #333;
        }
        
        .modal p {
            text-align: center;
            color: #666;
            margin-bottom: 1.5rem;
        }
        
        .modal-buttons {
            display: flex;
            gap: 12px;
            justify-content: center;
        }
        
        .modal-btn {
            padding: 10px 24px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #e2e8f0;
            color: #333;
        }
        
        .modal-btn:hover {
            background: #cbd5e0;
        }
        
        .modal-btn-primary {
            background: #9933ff;
            color: white;
        }
        
        .modal-btn-primary:hover {
            background: #7c2dcc;
        }
        
        /* Receipt display styles */
        .receipt-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: #f0f9ff;
            color: #0369a1;
            text-decoration: none;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .receipt-link:hover {
            background: #e0f2fe;
            color: #0284c7;
        }
        
        .no-receipt {
            color: #666;
            font-style: italic;
            font-size: 13px;
        }
        
        /* Archive toggle button */
        .btn-secondary {
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 0 20px;
            height: 42px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .btn-secondary:hover {
            background: white;
            border: 1px solid #9933ff;
            color: #9933ff;
        }
        
        .btn-secondary:hover i {
            color: #9933ff;
        }
        
        .btn-secondary.active {
            background: #9933ff;
            color: white;
        }
        
        .btn-secondary.active:hover {
            background: #7c2dcc;
        }
        
        .btn-secondary.active i {
            color: white;
        }
        
        /* Search input styles */
        #searchStudent {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            background-color: white;
            font-size: 14px;
        }
        
        #searchStudent:focus {
            outline: none;
            border-color: #9933ff;
            box-shadow: 0 0 0 3px rgba(153, 51, 255, 0.1);
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="dashboard-wrapper">
            <div class="header-section">
                <h1 class="page-title">Student Management</h1>
                <div class="summary-cards">
                    <div class="summary-card">
                        <div class="card-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="card-info">
                            <h3>Total Students</h3>
                            <p id="totalStudents">0</p>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="card-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="card-info">
                            <h3>Active Students</h3>
                            <p id="activeStudents">0</p>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="card-icon">
                            <i class="fas fa-box-archive"></i>
                        </div>
                        <div class="card-info">
                            <h3>Archived Students</h3>
                            <p id="archivedStudents">0</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student add form removed as per new requirement -->

            <!-- Filters Section -->
            <div class="filters-container">
                <input type="text" id="searchStudent" placeholder="Search by student name..." style="flex: 1; min-width: 250px;">
                
                <select id="filterCourse">
                    <option value="All">All Courses</option>
                    <option value="BSIT">BSIT</option>
                    <option value="BSCS">BSCS</option>
                </select>
                
                <select id="filterYear">
                    <option value="All">All Year Levels</option>
                    <option value="1">1st Year</option>
                    <option value="2">2nd Year</option>
                    <option value="3">3rd Year</option>
                    <option value="4">4th Year</option>
                </select>
                
                <input type="text" id="filterSection" placeholder="Section (e.g., A)" maxlength="1">
                
                <select id="filterStatus">
                    <option value="All">All Status</option>
                    <option value="paid">Paid</option>
                    <option value="unpaid">Unpaid</option>
                </select>
                
                <button id="applyFilters">
                    <i class="fas fa-filter"></i> Apply Filters
                </button>
                
                <button id="toggleArchived" class="btn-secondary">
                    <i class="fas fa-archive"></i> <span id="archiveToggleText">View Archived</span>
                </button>
            </div>

            <!-- Students Table -->
            <div class="table-container">
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Course</th>
                            <th>Year Level</th>
                            <th>Section</th>
                            
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="students-table-body">
                        <!-- Table content will be dynamically populated -->
                    </tbody>
                </table>
                
                <!-- Pagination Controls -->
                <div class="pagination centered">
                    <a href="#" class="page-btn prev-btn">&laquo; Prev</a>
                    <span class="page-indicator">Page 1 of 1</span>
                    <a href="#" class="page-btn next-btn">Next &raquo;</a>
                </div>
            </div>
            
            
        </div>
    </div>

    <!-- Confirmation Modal for status toggle -->
    <div class="modal-overlay" id="confirmOverlay"></div>
    <div class="modal confirm-modal" id="confirmModal">
        <div class="modal-header">
            <h2 class="modal-title" id="confirmTitle">Confirm Action</h2>
            <button type="button" class="modal-close" id="confirmClose"><i class="fas fa-times"></i></button>
        </div>
        <div class="form-body">
            <p id="confirmMessage" style="text-align:center; color:#666; margin:0;">Are you sure you want to proceed?</p>
        </div>
        <div class="modal-footer">
            <button class="modal-btn" id="confirmCancel">Cancel</button>
            <button class="modal-btn modal-btn-primary" id="confirmOk">Confirm</button>
        </div>
    </div>

    <!-- Receipt Upload Modal -->
    <div class="modal-overlay" id="receiptUploadOverlay"></div>
    <div class="modal" id="receiptUploadModal">
        <div class="modal-header">
            <h2 class="modal-title">Upload Membership Receipt</h2>
            <button type="button" class="modal-close" onclick="closeReceiptUploadModal()"><i class="fas fa-times"></i></button>
        </div>
        <form id="receiptUploadForm" class="form-body">
            <p style="text-align:center; color:#666;">Upload a receipt for <strong id="receiptStudentName"></strong> to mark them as paid.</p>
                <input type="hidden" id="receiptStudentId">
                
                <div style="margin-bottom: 1rem;">
                    <label for="receiptFile" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #333;">
                        Select Receipt File:
                    </label>
                    <input type="file" id="receiptFile" name="receipt" accept="image/*,application/pdf" 
                           style="width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 6px; background: white;">
                    <small style="color: #666; font-size: 12px; margin-top: 0.25rem; display: block;">
                        Supported formats: JPG, PNG, GIF, PDF (Max: 5MB)
                    </small>
                </div>
                
                <div id="receiptError" style="color: #dc3545; font-size: 14px; margin-bottom: 1rem; min-height: 20px;"></div>
        </form>
        <div class="modal-footer">
            <button type="button" class="modal-btn" onclick="closeReceiptUploadModal()">Cancel</button>
            <button type="button" class="modal-btn modal-btn-primary" onclick="uploadReceipt()"><i class="fas fa-upload"></i> Upload Receipt</button>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal-overlay" id="successOverlay"></div>
    <div class="modal" id="successModal">
        <div class="modal-header">
            <h2 class="modal-title">Success!</h2>
            <button type="button" class="modal-close" onclick="closeSuccessModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="form-body">
            <p id="successMessage" style="text-align:center; color:#666;">Receipt uploaded successfully! Student marked as paid.</p>
        </div>
        <div class="modal-footer">
            <button class="modal-btn modal-btn-primary" onclick="closeSuccessModal()"><i class="fas fa-check"></i> OK</button>
        </div>
    </div>

    <!-- Student Profile Modal -->
    <div class="modal-overlay" id="studentProfileOverlay"></div>
    <div class="modal" id="studentProfileModal">
        <div class="modal-header">
            <h2 class="modal-title">Student Profile</h2>
            <button type="button" class="modal-close" onclick="closeStudentProfileModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="form-body">
            <div id="studentProfileBody" style="margin-bottom: 1rem;"></div>
        </div>
        <div class="modal-footer">
            <button class="modal-btn modal-btn-primary" onclick="closeStudentProfileModal()"><i class="fas fa-times"></i> Close</button>
        </div>
    </div>

    <script src="../assets/js/students.js"></script>
</body>
</html> 