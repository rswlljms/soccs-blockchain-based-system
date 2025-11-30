<?php
session_start();
require_once '../includes/page_access.php';
checkPageAccess(['view_users', 'create_accounts', 'demote_accounts', 'manage_users']);
include('../components/sidebar.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/user-management.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="main-content">
        <div class="user-management-wrapper">
            <div class="header-section">
                <h1 class="page-title">User Management</h1>
                <p class="page-subtitle">Manage organization users, roles, and permissions</p>
                
                <div class="summary-cards">
                    <div class="summary-card">
                        <div class="card-icon"><i class="fas fa-users"></i></div>
                        <div class="card-info">
                            <h3>Total Users</h3>
                            <p id="totalUsers">0</p>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="card-icon"><i class="fas fa-user-check"></i></div>
                        <div class="card-info">
                            <h3>Active Users</h3>
                            <p id="activeUsers">0</p>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="card-icon"><i class="fas fa-user-shield"></i></div>
                        <div class="card-info">
                            <h3>Admins</h3>
                            <p id="adminUsers">0</p>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="card-icon"><i class="fas fa-user-slash"></i></div>
                        <div class="card-info">
                            <h3>Inactive</h3>
                            <p id="inactiveUsers">0</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="toolbar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchUser" placeholder="Search by name or email...">
                </div>
                
                <select class="filter-select" id="filterRole">
                    <option value="all">All Roles</option>
                    <option value="adviser">SOCCS Adviser</option>
                    <option value="dean">CCS Dean</option>
                    <option value="president">SOCCS President</option>
                    <option value="treasurer">SOCCS Treasurer</option>
                    <option value="auditor">SOCCS Auditor</option>
                    <option value="secretary">Secretary</option>
                    <option value="comelec">COMELEC</option>
                    <option value="event_coordinator">Event Coordinator</option>
                    <option value="officer">Officer</option>
                </select>
                
                <button class="btn btn-secondary" id="toggleInactive">
                    <i class="fas fa-eye"></i>
                    <span>Show Inactive</span>
                </button>
                
                <button class="btn btn-primary" id="addUserBtn">
                    <i class="fas fa-plus"></i>
                    Add User
                </button>
            </div>

            <div class="table-container">
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Permissions</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        <tr>
                            <td colspan="6">
                                <div class="loading-spinner">
                                    <div class="spinner"></div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="pagination">
                    <button class="page-btn" id="prevPage">&laquo; Prev</button>
                    <span class="page-indicator" id="pageIndicator">Page 1 of 1</span>
                    <button class="page-btn" id="nextPage">Next &raquo;</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit User Modal -->
    <div class="modal-overlay" id="userModalOverlay"></div>
    <div class="modal" id="userModal">
        <div class="modal-header">
            <h2 id="userModalTitle">Add New User</h2>
            <button class="modal-close" id="closeUserModal"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <form id="userForm">
                <input type="hidden" id="userId" name="id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">First Name <span class="required">*</span></label>
                        <input type="text" id="firstName" name="first_name" placeholder="Enter first name" required>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name <span class="required">*</span></label>
                        <input type="text" id="lastName" name="last_name" placeholder="Enter last name" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address <span class="required">*</span></label>
                    <input type="email" id="email" name="email" placeholder="Enter email address" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password <span class="required" id="passwordRequired">*</span></label>
                        <input type="password" id="password" name="password" placeholder="Enter password" minlength="8">
                        <p class="form-hint" id="passwordHint">Minimum 8 characters</p>
                    </div>
                    <div class="form-group">
                        <label for="userRole">Role <span class="required">*</span></label>
                        <select id="userRole" name="role" required>
                            <option value="officer">Officer</option>
                            <option value="secretary">Secretary</option>
                            <option value="event_coordinator">Event Coordinator</option>
                            <option value="comelec">COMELEC</option>
                            <option value="auditor">SOCCS Auditor</option>
                            <option value="treasurer">SOCCS Treasurer</option>
                            <option value="president">SOCCS President</option>
                            <option value="dean">CCS Dean</option>
                            <option value="adviser">SOCCS Adviser</option>
                        </select>
                        <p class="form-hint" id="roleDescription">Default permissions will be assigned based on role</p>
                    </div>
                </div>
                
                <div class="form-group" id="statusGroup" style="display: none;">
                    <label for="userStatus">Status</label>
                    <select id="userStatus" name="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelUserModal">Cancel</button>
            <button class="btn btn-primary" id="saveUserBtn">
                <i class="fas fa-save"></i> Save User
            </button>
        </div>
    </div>

    <!-- Permissions Modal -->
    <div class="modal-overlay" id="permissionsModalOverlay"></div>
    <div class="modal large" id="permissionsModal">
        <div class="modal-header">
            <h2 id="permissionsModalTitle">Manage Permissions</h2>
            <button class="modal-close" id="closePermissionsModal"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="permissionsUserId">
            <div class="permissions-container" id="permissionsContainer">
                <div class="loading-spinner">
                    <div class="spinner"></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelPermissionsModal">Cancel</button>
            <button class="btn btn-primary" id="savePermissionsBtn">
                <i class="fas fa-save"></i> Save Permissions
            </button>
        </div>
    </div>

    <!-- Confirm Delete Modal -->
    <div class="modal-overlay" id="deleteModalOverlay"></div>
    <div class="modal" id="deleteModal">
        <div class="modal-header">
            <h2>Confirm Action</h2>
            <button class="modal-close" id="closeDeleteModal"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body" style="text-align: center;">
            <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #f59e0b; margin-bottom: 16px;"></i>
            <p id="deleteMessage" style="font-size: 16px; color: #374151; margin-bottom: 8px;"></p>
            <p style="color: #6b7280; font-size: 14px;">This action can be reversed by reactivating the user.</p>
            <input type="hidden" id="deleteUserId">
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelDeleteModal">Cancel</button>
            <button class="btn" id="confirmDeleteBtn" style="background: #ef4444; color: white;">
                <i class="fas fa-trash"></i> Deactivate
            </button>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <script src="../assets/js/user-management.js"></script>
</body>
</html>

