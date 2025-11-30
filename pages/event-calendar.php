<?php
session_start();
require_once '../includes/page_access.php';
checkPageAccess(['view_events']);
include('../components/sidebar.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Calendar</title>
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/student-events.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="main-content">
        <div class="dashboard-wrapper">
            <div class="dashboard-header">
                <h1 class="page-title">Event Calendar</h1>
            </div>

            <div class="calendar-container">
                <div class="calendar-navigation">
                    <button class="nav-btn" id="prevMonth">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="current-month" id="currentMonth">December 2025</div>
                    <button class="nav-btn" id="nextMonth">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>

                <div class="calendar-grid">
                    <div class="calendar-header">
                        <div class="day-header">Sun</div>
                        <div class="day-header">Mon</div>
                        <div class="day-header">Tue</div>
                        <div class="day-header">Wed</div>
                        <div class="day-header">Thu</div>
                        <div class="day-header">Fri</div>
                        <div class="day-header">Sat</div>
                    </div>
                    <div class="calendar-days" id="calendarDays"></div>
                </div>

                <div class="selected-date-events" id="selectedDateEvents">
                    <div class="no-events-message">
                        <i class="fas fa-calendar-day"></i>
                        <p>Select a date to view events</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/student-events.js"></script>
</body>
</html>
