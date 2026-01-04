<?php
session_start();
require_once '../includes/page_access.php';
checkPageAccess(['view_events', 'manage_events']);
include('../components/sidebar.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management</title>
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/students.css">
    <link rel="stylesheet" href="../assets/css/admin-table-styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/events-management.css">
    <style>
        /* Modal styling */
        .modal { 
            padding: 0; 
            overflow: hidden; 
            display: flex; 
            flex-direction: column; 
            max-height: 90vh;
        }
        .modal-header { 
            background: linear-gradient(135deg, #4B0082, #9933ff); 
            padding: 24px 32px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border-radius: 16px 16px 0 0;
            flex-shrink: 0;
        }
        .modal-title { font-size: 22px; font-weight: 600; color: #fff; margin: 0; letter-spacing: -0.02em; }
        .modal-close { background: rgba(255,255,255,0.2); border: none; font-size: 18px; color: #fff; cursor: pointer; padding: 8px; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; transition: all .2s; }
        .modal-close:hover { background: rgba(255,255,255,0.3); transform: rotate(90deg); }
        .candidate-form-modal { 
            padding: 32px; 
            overflow-y: auto; 
            flex: 1;
            max-height: calc(90vh - 180px);
        }
        .modal-footer { 
            padding: 24px 32px; 
            border-top: 1px solid #e5e7eb; 
            display: flex; 
            justify-content: flex-end; 
            gap: 14px; 
            background: #fafbfc; 
            border-radius: 0 0 16px 16px;
            flex-shrink: 0;
        }
        .modal-btn-secondary { background: #fff; border: 2px solid #e5e7eb; color: #1f2937; }
        .modal-btn-primary { 
            background: linear-gradient(135deg, #4B0082, #9933ff); 
            color: #fff; 
            border: none; 
            padding: 12px 24px; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: 600;
            transition: all 0.2s;
        }
        .modal-btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
        .input-group { position: relative; margin-bottom: 16px; }
        .form-row { 
            display: flex; 
            gap: 12px; 
            margin-bottom: 16px;
        }
        .form-row .input-group { 
            flex: 1; 
            margin-bottom: 0; 
        }
        .form-row .input-group.flex-2 { flex: 2; }
        .form-row .input-group.flex-1 { flex: 1; }
        #endDateRow { transition: all 0.3s ease; }
        .input-group textarea {
            width: 100%;
            padding: 12px 16px 12px 48px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.2s;
            background: #fff;
            resize: vertical;
            min-height: 100px;
        }
        .input-group textarea:focus {
            outline: none;
            border-color: #9933ff;
            box-shadow: 0 0 0 3px rgba(153, 51, 255, 0.1);
        }
        .contests-section {
            margin-top: 24px;
            padding: 20px;
            background: #f9fafb;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }
        .contests-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        .contests-section-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .contests-section-header h3 i {
            color: #f59e0b;
        }
        .add-contest-btn {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        .add-contest-btn:hover {
            background: linear-gradient(135deg, #d97706, #b45309);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(245, 158, 11, 0.3);
        }
        .contests-container {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .contest-item {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 2px solid #e5e7eb;
            transition: all 0.2s;
        }
        .contest-item:hover {
            border-color: #9933ff;
            box-shadow: 0 2px 8px rgba(153, 51, 255, 0.1);
        }
        .contest-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        .contest-item-header h4 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .contest-item-header h4 i {
            color: #f59e0b;
        }
        .remove-contest-btn {
            background: #ef4444;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        .remove-contest-btn:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }
    </style>
    <script>
        const userPermissions = {
            canManageEvents: <?php echo hasPermission('manage_events') ? 'true' : 'false'; ?>
        };
        
        function toggleDateInputs() {
            const durationType = document.getElementById('eventDurationType').value;
            const endDateRow = document.getElementById('endDateRow');
            const endDateInput = document.getElementById('eventEndDate');
            
            if (durationType === 'multiple') {
                endDateRow.style.display = 'flex';
                endDateInput.required = true;
            } else {
                endDateRow.style.display = 'none';
                endDateInput.required = false;
                endDateInput.value = '';
            }
        }
        
        window.contestCounter = 0;
        
        window.contestCounter = 0;
        
        window.addContest = function() {
            window.contestCounter++;
            const contestsContainer = document.getElementById('contestsContainer');
            const contestItem = document.createElement('div');
            contestItem.className = 'contest-item';
            contestItem.dataset.contestId = window.contestCounter;
            contestItem.innerHTML = `
                <div class="contest-item-header">
                    <h4><i class="fas fa-trophy"></i> Contest</h4>
                    <button type="button" class="remove-contest-btn" onclick="removeContest(${window.contestCounter})">
                        <i class="fas fa-times"></i> Remove
                    </button>
                </div>
                <div class="input-group">
                    <i class="fas fa-file-alt"></i>
                    <textarea name="contest_details[]" class="contest-details-input" placeholder="Contest Details" rows="4" required></textarea>
                </div>
                <div class="input-group">
                    <i class="fas fa-link"></i>
                    <input type="url" name="registration_link[]" class="registration-link-input" placeholder="Registration Link (e.g., Google Forms, etc.)" required>
                </div>
            `;
            contestsContainer.appendChild(contestItem);
        };
        
        window.removeContest = function(id) {
            const contestItem = document.querySelector(`.contest-item[data-contest-id="${id}"]`);
            if (contestItem) {
                contestItem.remove();
            }
        };
    </script>
</head>
<body>
    <div class="main-content">
        <div class="dashboard-wrapper">
            <div class="header-section">
                <h1 class="page-title">Event Management</h1>
                <!-- Summary Cards -->
                <div class="summary-cards">
                    <div class="summary-card">
                        <div class="card-icon" style="background: rgba(153, 51, 255, 0.1);">
                            <i class="fas fa-calendar-alt" style="color: #9933ff;"></i>
                        </div>
                        <div class="card-info">
                            <h3>Total Events</h3>
                            <p id="totalEvents">7</p>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="card-icon" style="background: rgba(40, 167, 69, 0.1);">
                            <i class="fas fa-calendar-plus" style="color: #28a745;"></i>
                        </div>
                        <div class="card-info">
                            <h3>Upcoming Events</h3>
                            <p id="upcomingEvents">1</p>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="card-icon" style="background: rgba(108, 117, 125, 0.1);">
                            <i class="fas fa-calendar-check" style="color: #6c757d;"></i>
                        </div>
                        <div class="card-info">
                            <h3>Past Events</h3>
                            <p id="pastEvents">6</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filter/Search Toolbar -->
            <div class="event-filters">
                <select id="filter-status">
                    <option value="all">All Statuses</option>
                    <option value="upcoming">Upcoming</option>
                    <option value="completed">Completed</option>
                    <option value="archived">Archived</option>
                </select>
                
                <input type="date" id="filter-date">
                
                <input type="text" id="search-event" placeholder="Search event name...">
                
                <?php if (hasPermission('manage_events')): ?>
                <button class="btn-add-event" id="addEventBtn">
                    <i class="fas fa-plus"></i> Add Event
                </button>
                <?php endif; ?>
            </div>
            
            <!-- Event Table -->
            <div class="table-container">
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="event-table-body">
                        <!-- JS will populate events here -->
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
    
    <!-- Add/Edit Event Modal -->
    <div class="modal-overlay" id="eventModalOverlay">
        <div class="modal" id="eventModal">
        <div class="modal-header">
            <h2 class="modal-title" id="eventModalTitle">Add Event</h2>
            <button type="button" class="modal-close" id="closeEventModal"><i class="fas fa-times"></i></button>
        </div>
        <form id="eventForm" class="candidate-form-modal">
            <div class="input-group">
                <i class="fas fa-calendar-day"></i>
                <input type="text" name="name" id="eventName" placeholder="Event Name" required>
            </div>
            
            <div class="form-row" id="dateTimeRow">
                <div class="input-group flex-2">
                    <i class="fas fa-calendar"></i>
                    <input type="date" name="date" id="eventDate" required>
                </div>
                <div class="input-group flex-1">
                    <i class="fas fa-clock"></i>
                    <input type="time" name="time" id="eventTime" required>
                </div>
            </div>
            
            <div class="form-row" id="endDateRow" style="display: none;">
                <div class="input-group">
                    <i class="fas fa-calendar-check"></i>
                    <input type="date" name="end_date" id="eventEndDate">
                </div>
            </div>
            
            <div class="input-group">
                <i class="fas fa-redo"></i>
                <select name="duration_type" id="eventDurationType" onchange="toggleDateInputs()">
                    <option value="single">Single Day Event</option>
                    <option value="multiple">Multiple Days Event</option>
                </select>
            </div>
            
            <div class="input-group">
                <i class="fas fa-map-marker-alt"></i>
                <input type="text" name="location" id="eventLocation" placeholder="Location" required>
            </div>
            
            <div class="input-group">
                <i class="fas fa-align-left"></i>
                <input type="text" name="description" id="eventDescription" placeholder="Description" required>
            </div>
            
            <div class="input-group">
                <i class="fas fa-tag"></i>
                <select name="category" id="eventCategory" required>
                    <option value="">Select Category</option>
                    <option value="academic">Academic</option>
                    <option value="competition">Competition</option>
                    <option value="social">Social</option>
                    <option value="workshop">Workshop</option>
                </select>
            </div>
            
            <div class="input-group">
                <i class="fas fa-info-circle"></i>
                <select name="status" id="eventStatus" required>
                    <option value="upcoming">Upcoming</option>
                    <option value="completed">Completed</option>
                    <option value="archived">Archived</option>
                </select>
            </div>
            
            <div class="contests-section">
                <div class="contests-section-header">
                    <h3><i class="fas fa-trophy"></i> Contests</h3>
                    <button type="button" class="add-contest-btn" onclick="window.addContest()">
                        <i class="fas fa-plus"></i> Add Contest
                    </button>
                </div>
                <div id="contestsContainer" class="contests-container">
                    <!-- Contest items will be added here dynamically -->
                </div>
            </div>
        </form>
        <div class="modal-footer">
            <button type="button" class="modal-btn modal-btn-secondary" id="cancelEventBtn">Cancel</button>
            <button type="submit" form="eventForm" class="modal-btn modal-btn-primary">Save</button>
        </div>
        </div>
    </div>
    
    <!-- Success Modal -->
    <div class="modal-overlay" id="successOverlay" style="z-index: 1100;">
        <div class="modal success-modal" id="successModal" style="max-width: 400px;">
            <div class="modal-header">
                <h2 class="modal-title">Success!</h2>
                <button type="button" class="modal-close" id="closeSuccessModal"><i class="fas fa-times"></i></button>
            </div>
            <div style="padding: 32px; text-align: center;">
                <i class="fas fa-check-circle" style="font-size: 48px; color: #10b981; margin-bottom: 16px;"></i>
                <p style="color:#666; margin:0;" id="successMessage">Event has been saved successfully</p>
            </div>
            <div class="modal-footer" style="justify-content: center;">
                <button class="modal-btn modal-btn-primary" id="successOk">OK</button>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/events.js"></script>
</body>
</html> 