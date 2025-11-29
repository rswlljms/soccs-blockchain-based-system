<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>

<link rel="stylesheet" href="../assets/css/sidebar.css">
<link rel="icon" href="../assets/img/favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="sidebar student-sidebar">
  <div class="sidebar-header">
    <img src="../assets/img/logo.png" alt="SOCCS Logo">
    <h1>Student Organization of the<br>College of Computer Studies</h1>
  </div>

  <ul class="nav-links">
    <li><a href="../pages/student-dashboard.php" class="<?= $currentPage == 'student-dashboard.php' ? 'active' : '' ?>">
      <i class="fas fa-home"></i><span>Dashboard</span>
    </a></li>
    
    <li class="dropdown">
      <a href="javascript:void(0);" class="dropdown-toggle <?= ($currentPage == 'student-voting.php' || $currentPage == 'student-results.php') ? 'active' : '' ?>">
        <i class="fas fa-vote-yea"></i>
        <span>Elections</span>
        <i class="fas fa-chevron-down dropdown-icon"></i>
      </a>
      <ul class="dropdown-menu">
        <li><a href="../pages/student-voting.php" class="<?= $currentPage == 'student-voting.php' ? 'active' : '' ?>">
          <i class="fas fa-vote-yea"></i><span>Cast Vote</span>
        </a></li>
        <li><a href="../pages/student-results.php" class="<?= $currentPage == 'student-results.php' ? 'active' : '' ?>">
          <i class="fas fa-chart-bar"></i><span>Election Overview</span>
        </a></li>
      </ul>
    </li>
    
    <li><a href="../pages/student-events.php" class="<?= $currentPage == 'student-events.php' ? 'active' : '' ?>">
      <i class="fas fa-calendar-alt"></i><span>Events</span>
    </a></li>
    
    <li><a href="../pages/student-announcements.php" class="<?= $currentPage == 'student-announcements.php' ? 'active' : '' ?>">
      <i class="fas fa-bullhorn"></i><span>Announcements</span>
    </a></li>

    <li><a href="../pages/student-transparency.php" class="<?= $currentPage == 'student-transparency.php' ? 'active' : '' ?>">
      <i class="fas fa-shield-alt"></i><span>Transparency</span>
    </a></li>

    <li><a href="../pages/student-voting-history.php" class="<?= $currentPage == 'student-voting-history.php' ? 'active' : '' ?>">
      <i class="fas fa-history"></i><span>Voting History</span>
    </a></li>

    <li><a href="../pages/student-profile.php" class="<?= $currentPage == 'student-profile.php' ? 'active' : '' ?>">
      <i class="fas fa-user-cog"></i><span>Profile Settings</span>
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
