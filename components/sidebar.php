<?php 
$currentPage = basename($_SERVER['PHP_SELF']);

require_once __DIR__ . '/../includes/auth_check.php';

$userRole = $_SESSION['user_role'] ?? 'officer';
?>

<link rel="stylesheet" href="../assets/css/sidebar.css">
<link rel="icon" href="../assets/img/favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* Ensure main-content spacing is applied globally for all admin pages */
@media (min-width: 769px) {
  .main-content {
    margin-left: 280px !important;
    width: calc(100% - 280px) !important;
    position: relative !important;
    box-sizing: border-box;
  }
}
</style>

<div class="sidebar">
  <div class="sidebar-header">
    <img src="../assets/img/logo.png" alt="SOCCS Logo">
    <h1>Student Organization of the<br>College of Computer Studies</h1>
  </div>

  <ul class="nav-links">
    <?php if (hasPermission('view_dashboard')): ?>
    <li><a href="../pages/dashboard.php" class="<?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">
      <i class="fas fa-home"></i><span>Dashboard</span>
    </a></li>
    <?php endif; ?>
    
    <?php if (hasAnyPermission(['view_funds', 'view_expenses', 'view_membership_fee', 'manage_funds', 'manage_expenses', 'modify_membership_fee'])): ?>
    <li class="dropdown">
      <a href="javascript:void(0);" class="dropdown-toggle <?= ($currentPage == 'expenses.php' || $currentPage == 'funds.php' || $currentPage == 'membership-fee.php') ? 'active' : '' ?>">
        <i class="fas fa-wallet"></i>
        <span>Financial Management</span>
        <i class="fas fa-chevron-down dropdown-icon"></i>
      </a>
      <ul class="dropdown-menu">
        <?php if (hasAnyPermission(['view_expenses', 'manage_expenses'])): ?>
        <li><a href="../pages/expenses.php" class="<?= $currentPage == 'expenses.php' ? 'active' : '' ?>">
          <i class="fas fa-receipt"></i><span>Expenses</span>
        </a></li>
        <?php endif; ?>
        <?php if (hasAnyPermission(['view_funds', 'manage_funds'])): ?>
        <li><a href="../pages/funds.php" class="<?= $currentPage == 'funds.php' ? 'active' : '' ?>">
          <i class="fas fa-money-bill"></i><span>Budget</span>
        </a></li>
        <?php endif; ?>
        <?php if (hasAnyPermission(['view_membership_fee', 'modify_membership_fee'])): ?>
        <li><a href="../pages/membership-fee.php" class="<?= $currentPage == 'membership-fee.php' ? 'active' : '' ?>">
          <i class="fas fa-id-card"></i><span>Membership Fee</span>
        </a></li>
        <?php endif; ?>
      </ul>
    </li>
    <?php endif; ?>
    
    <?php if (hasAnyPermission(['view_students', 'manage_students', 'verify_students'])): ?>
    <li class="dropdown">
      <a href="javascript:void(0);" class="dropdown-toggle <?= ($currentPage == 'students.php' || $currentPage == 'student-approvals.php' || $currentPage == 'masterlist-upload.php') ? 'active' : '' ?>">
        <i class="fas fa-users"></i>
        <span>Student Management</span>
        <i class="fas fa-chevron-down dropdown-icon"></i>
      </a>
      <ul class="dropdown-menu">
        <?php if (hasAnyPermission(['view_students', 'manage_students'])): ?>
        <li><a href="../pages/students.php" class="<?= $currentPage == 'students.php' ? 'active' : '' ?>">
          <i class="fas fa-users"></i><span>Active Students</span>
        </a></li>
        <?php endif; ?>
        <?php if (hasAnyPermission(['view_students', 'manage_students'])): ?>
        <li><a href="../pages/masterlist-upload.php" class="<?= $currentPage == 'masterlist-upload.php' ? 'active' : '' ?>">
          <i class="fas fa-file-upload"></i><span>Masterlist Upload</span>
        </a></li>
        <?php endif; ?>
        <?php if (hasPermission('verify_students')): ?>
        <li><a href="../pages/student-approvals.php" class="<?= $currentPage == 'student-approvals.php' ? 'active' : '' ?>">
          <i class="fas fa-user-check"></i><span>Registration Approvals</span>
        </a></li>
        <?php endif; ?>
      </ul>
    </li>
    <?php endif; ?>
    
    <?php if (hasAnyPermission(['view_events', 'manage_events'])): ?>
    <?php if (hasPermission('manage_events')): ?>
    <li><a href="../pages/events.php" class="<?= $currentPage == 'events.php' ? 'active' : '' ?>">
      <i class="fas fa-calendar-alt"></i><span>Event Management</span>
    </a></li>
    <?php elseif (hasPermission('view_events')): ?>
    <li><a href="../pages/event-calendar.php" class="<?= $currentPage == 'event-calendar.php' ? 'active' : '' ?>">
      <i class="fas fa-calendar"></i><span>Event Calendar</span>
    </a></li>
    <?php endif; ?>
    <?php endif; ?>

    <?php if (hasAnyPermission(['view_election', 'manage_election_status', 'register_candidates', 'manage_positions', 'view_election_results'])): ?>
    <li class="dropdown">
      <a href="javascript:void(0);" class="dropdown-toggle <?= ($currentPage == 'add-candidate.php' || $currentPage == 'positions.php' || $currentPage == 'elections.php' || $currentPage == 'admin-election-overview.php') ? 'active' : '' ?>">
        <i class="fas fa-vote-yea"></i>
        <span>Election</span>
        <i class="fas fa-chevron-down dropdown-icon"></i>
      </a>
      <ul class="dropdown-menu">
        <?php if (hasPermission('manage_election_status')): ?>
        <li><a href="../pages/elections.php" class="<?= $currentPage == 'elections.php' ? 'active' : '' ?>"><i class="fas fa-cog"></i><span>Manage Elections</span></a></li>
        <?php endif; ?>
        <?php if (hasAnyPermission(['view_election', 'view_election_results'])): ?>
        <li><a href="../pages/admin-election-overview.php" class="<?= $currentPage == 'admin-election-overview.php' ? 'active' : '' ?>"><i class="fas fa-chart-pie"></i><span>Election Overview</span></a></li>
        <?php endif; ?>
        <?php if (hasPermission('register_candidates')): ?>
        <li><a href="../pages/add-candidate.php" class="<?= $currentPage == 'add-candidate.php' ? 'active' : '' ?>"><i class="fas fa-user-plus"></i><span>Candidates</span></a></li>
        <?php endif; ?>
        <?php if (hasPermission('manage_positions')): ?>
        <li><a href="../pages/positions.php" class="<?= $currentPage == 'positions.php' ? 'active' : '' ?>"><i class="fas fa-list"></i><span>Positions</span></a></li>
        <?php endif; ?>
      </ul>
    </li>
    <?php endif; ?>

    <?php if (hasAnyPermission(['view_users', 'create_accounts', 'demote_accounts', 'manage_users'])): ?>
    <li><a href="../pages/user-management.php" class="<?= $currentPage == 'user-management.php' ? 'active' : '' ?>">
      <i class="fas fa-user-cog"></i><span>User Management</span>
    </a></li>
    <?php endif; ?>

    <?php if (isAdviser() || isDean()): ?>
    <li><a href="../pages/activity-logs.php" class="<?= $currentPage == 'activity-logs.php' ? 'active' : '' ?>">
      <i class="fas fa-history"></i><span>Activity Logs</span>
    </a></li>
    <?php endif; ?>
  </ul>

  <div class="sidebar-footer">
    <div class="logout-section">
      <a href="../templates/login.php" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
      </a>
    </div>
  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const toggles = document.querySelectorAll(".dropdown-toggle");

    toggles.forEach(toggle => {
      toggle.addEventListener("click", function () {
        const parent = this.closest(".dropdown");
        parent.classList.toggle("open");
      });
    });

    // Auto-open dropdown if child page is active
    const activeDropdownItem = document.querySelector(".dropdown-menu a.active");
    if (activeDropdownItem) {
      activeDropdownItem.closest(".dropdown").classList.add("open");
    }
  });
</script>
