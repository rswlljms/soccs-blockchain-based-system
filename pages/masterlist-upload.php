<?php
session_start();
require_once '../includes/page_access.php';
checkPageAccess(['view_students', 'manage_students']);
include('../components/sidebar.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Masterlist Upload</title>
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/masterlist-upload.css">
    <link rel="stylesheet" href="../assets/css/admin-table-styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="main-content">
        <div class="dashboard-wrapper">
            <div class="header-section">
                <h1 class="page-title">Masterlist Upload</h1>
            </div>
            
            <div class="upload-toolbar">
                <form id="masterlistUploadForm" enctype="multipart/form-data" class="upload-form">
                    <div class="file-upload-wrapper">
                        <div class="file-upload-area" id="fileUploadArea">
                            <input type="file" id="masterlistFile" name="masterlistFile" accept=".pdf,.jpg,.jpeg,.png,.heic,.heif,.webp" required>
                            <div class="upload-placeholder">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <div class="upload-text">
                                    <span class="upload-main-text">Click to select file or drag and drop</span>
                                    <span class="file-types">PDF, JPG, PNG, HEIC, WEBP (Max 10MB)</span>
                                </div>
                            </div>
                            <div class="file-selected" id="fileSelected" style="display: none;">
                                <i class="fas fa-file-pdf"></i>
                                <span id="fileName"></span>
                                <button type="button" class="remove-file" id="removeFile">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary" id="uploadBtn">
                        <i class="fas fa-upload"></i>
                        <span>Upload and Extract</span>
                    </button>
                </form>
            </div>
            
            <div class="results-container" id="resultsContainer">
                <div class="results-header">
                    <h2>Extracted Students</h2>
                    <div class="results-actions">
                        <span class="results-count" id="resultsCount"></span>
                        <button type="button" class="btn-secondary" id="clearResults">
                            <i class="fas fa-times"></i>
                            Clear Results
                        </button>
                    </div>
                </div>
                
                <div class="table-container">
                    <table class="admin-table" id="extractedTable">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>ID Number</th>
                                <th>Name</th>
                                <th>Course</th>
                                <th>Section</th>
                            </tr>
                        </thead>
                        <tbody id="extractedTableBody">
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 60px 20px; color: #6b7280;">
                                    <i class="fas fa-file-upload" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px; display: block;"></i>
                                    <p style="font-size: 16px; font-weight: 500; margin-bottom: 8px;">No data extracted yet</p>
                                    <p style="font-size: 14px; color: #9ca3af;">Upload a masterlist document to extract student information</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal" id="loadingModal">
        <div class="modal-content modal-small">
            <div class="modal-body">
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Processing document...</p>
                    <span class="loading-text">This may take a few moments</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal" id="errorModal">
        <div class="modal-content modal-small">
            <div class="modal-header">
                <h3>Error</h3>
                <button type="button" class="modal-close" onclick="closeModal('errorModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p id="errorMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-primary" onclick="closeModal('errorModal')">OK</button>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/masterlist-upload.js"></script>
</body>
</html>

