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
    <div class="user-dropdown">
      <?php
      $student = $_SESSION['student'] ?? [];
      $firstName = $student['firstName'] ?? '';
      $displayName = !empty($firstName) ? $firstName : 'Student User';
      
      $initials = 'SU';
      if (!empty($firstName)) {
        $firstNameParts = explode(' ', trim($firstName));
        
        if (count($firstNameParts) >= 2) {
          $initials = strtoupper(substr($firstNameParts[0], 0, 1) . substr($firstNameParts[1], 0, 1));
        } else {
          $initials = strtoupper(substr($firstName, 0, 2));
        }
      }
      ?>
      <div class="user-dropdown-toggle">
        <div class="user-avatar">
          <span><?= htmlspecialchars($initials) ?></span>
        </div>
        <span class="user-name"><?= htmlspecialchars($displayName) ?></span>
        <i class="fas fa-chevron-up dropdown-chevron"></i>
      </div>
      <div class="user-dropdown-menu">
        <a href="../logout.php" class="logout-option">
          <i class="fas fa-sign-out-alt"></i>
          <span>Logout</span>
        </a>
      </div>
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

    // User dropdown toggle
    const userDropdownToggle = document.querySelector(".user-dropdown-toggle");
    const userDropdown = document.querySelector(".user-dropdown");
    
    if (userDropdownToggle && userDropdown) {
      userDropdownToggle.addEventListener("click", function(e) {
        e.stopPropagation();
        userDropdown.classList.toggle("open");
      });

      // Close dropdown when clicking outside
      document.addEventListener("click", function(e) {
        if (!userDropdown.contains(e.target)) {
          userDropdown.classList.remove("open");
        }
      });
    }
  });
</script>
