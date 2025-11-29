<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>

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
    <li><a href="../pages/dashboard.php" class="<?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">
      <i class="fas fa-home"></i><span>Dashboard</span>
    </a></li>
    
    <li class="dropdown">
      <a href="javascript:void(0);" class="dropdown-toggle <?= ($currentPage == 'expenses.php' || $currentPage == 'funds.php' || $currentPage == 'membership-fee.php') ? 'active' : '' ?>">
        <i class="fas fa-wallet"></i>
        <span>Funds Management</span>
        <i class="fas fa-chevron-down dropdown-icon"></i>
      </a>
      <ul class="dropdown-menu">
        <li><a href="../pages/expenses.php" class="<?= $currentPage == 'expenses.php' ? 'active' : '' ?>">
          <i class="fas fa-receipt"></i><span>Expenses</span>
        </a></li>
        <li><a href="../pages/funds.php" class="<?= $currentPage == 'funds.php' ? 'active' : '' ?>">
          <i class="fas fa-money-bill"></i><span>Funds</span>
        </a></li>
        <li><a href="../pages/membership-fee.php" class="<?= $currentPage == 'membership-fee.php' ? 'active' : '' ?>">
          <i class="fas fa-id-card"></i><span>Membership Fee</span>
        </a></li>
      </ul>
    </li>
    
    <li class="dropdown">
      <a href="javascript:void(0);" class="dropdown-toggle <?= ($currentPage == 'students.php' || $currentPage == 'student-approvals.php') ? 'active' : '' ?>">
        <i class="fas fa-users"></i>
        <span>Student Management</span>
        <i class="fas fa-chevron-down dropdown-icon"></i>
      </a>
      <ul class="dropdown-menu">
        <li><a href="../pages/students.php" class="<?= $currentPage == 'students.php' ? 'active' : '' ?>">
          <i class="fas fa-users"></i><span>Active Students</span>
        </a></li>
        <li><a href="../pages/student-approvals.php" class="<?= $currentPage == 'student-approvals.php' ? 'active' : '' ?>">
          <i class="fas fa-user-check"></i><span>Registration Approvals</span>
        </a></li>
      </ul>
    </li>
    
    <li><a href="../pages/events.php" class="<?= $currentPage == 'events.php' ? 'active' : '' ?>">
      <i class="fas fa-calendar"></i><span>Event Management</span>
    </a></li>

    <li><a href="../pages/reports.php" class="<?= $currentPage == 'reports.php' ? 'active' : '' ?>">
      <i class="fas fa-file-alt"></i><span>Reports</span>
    </a></li>

    <li class="dropdown">
      <a href="javascript:void(0);" class="dropdown-toggle <?= ($currentPage == 'add-candidate.php' || $currentPage == 'positions.php' || $currentPage == 'elections.php') ? 'active' : '' ?>">
        <i class="fas fa-vote-yea"></i>
        <span>Election</span>
        <i class="fas fa-chevron-down dropdown-icon"></i>
      </a>
      <ul class="dropdown-menu">
        <li><a href="../pages/elections.php" class="<?= $currentPage == 'elections.php' ? 'active' : '' ?>"><i class="fas fa-cog"></i><span>Manage Elections</span></a></li>
        <li><a href="../pages/add-candidate.php" class="<?= $currentPage == 'add-candidate.php' ? 'active' : '' ?>"><i class="fas fa-user-plus"></i><span>Candidates</span></a></li>
        <li><a href="../pages/positions.php" class="<?= $currentPage == 'positions.php' ? 'active' : '' ?>"><i class="fas fa-list"></i><span>Positions</span></a></li>
      </ul>
    </li>

    <li><a href="../pages/settings.php" class="<?= $currentPage == 'settings.php' ? 'active' : '' ?>">
      <i class="fas fa-cog"></i><span>Settings</span>
    </a></li>
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
