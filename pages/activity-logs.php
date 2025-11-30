<?php
session_start();
require_once '../includes/auth_check.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../templates/login.php");
    exit;
}

if (!isAdviser() && !isDean()) {
    header("Location: access-denied.php");
    exit;
}

include('../components/sidebar.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Activity Logs</title>
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/activity-logs.css">
    <link rel="stylesheet" href="../assets/css/admin-table-styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="main-content">
        <div class="activity-logs-wrapper">
            <div class="header-section">
                <h1 class="page-title">Activity Logs</h1>
                <p class="page-subtitle">View system activity and user actions</p>
            </div>

            <div class="toolbar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchLogs" placeholder="Search activities, users...">
                </div>
                
                <select class="filter-select" id="filterActivityType">
                    <option value="">All Activity Types</option>
                </select>
                
                <select class="filter-select" id="filterModule">
                    <option value="">All Modules</option>
                </select>
                
                <select class="filter-select" id="filterDate">
                    <option value="All">All Time</option>
                    <option value="Today">Today</option>
                    <option value="Week">This Week</option>
                    <option value="Month">This Month</option>
                    <option value="Year">This Year</option>
                </select>
                
                <button class="btn btn-secondary" id="clearFilters">
                    <i class="fas fa-times"></i>
                    Clear Filters
                </button>
            </div>

            <div class="table-container">
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>User</th>
                            <th>Activity Type</th>
                            <th>Activity Description</th>
                            <th>Module</th>
                        </tr>
                    </thead>
                    <tbody id="logsTableBody">
                        <tr>
                            <td colspan="5">
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

    <script src="../assets/js/activity-logs.js"></script>
</body>
</html>

