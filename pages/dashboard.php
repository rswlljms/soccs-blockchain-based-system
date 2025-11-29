<?php
session_start();
if (!isset($_SESSION['user'])) {
  header("Location: ../templates/login.php");
  exit;
}
?>

<?php include('../components/sidebar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <link rel="stylesheet" href="../assets/css/sidebar.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
  <div class="main-content">
    <div class="dashboard-wrapper">
      <div class="dashboard-header">
        <h1 class="page-title">Dashboard</h1>
      </div>

      <div class="card-row">
        <div class="card funds-card">
          <div class="card-icon">
            <i class="fas fa-wallet"></i>
          </div>
          <div class="card-content">
            <h3>Total Funds</h3>
            <p class="amount">₱50,000.00</p>
            <span class="trend positive">
              <i class="fas fa-arrow-up"></i> 12% from last month
            </span>
          </div>
        </div>

        <div class="card expenses-card">
          <div class="card-icon">
            <i class="fas fa-money-bill-wave"></i>
          </div>
          <div class="card-content">
            <h3>Total Expenses</h3>
            <p class="amount">₱32,250.00</p>
            <span class="trend negative">
              <i class="fas fa-arrow-down"></i> 8% from last month
            </span>
          </div>
        </div>

        <div class="card balance-card">
          <div class="card-icon">
            <i class="fas fa-piggy-bank"></i>
          </div>
          <div class="card-content">
            <h3>Current Balance</h3>
            <p class="amount">₱17,750.00</p>
            <span class="trend neutral">
              <i class="fas fa-minus"></i> No change
            </span>
          </div>
        </div>
      </div>

      <div class="dashboard-grid">
        <div class="chart-card">
          <div class="card-header">
            <div class="chart-title-section">
              <h3 id="chartTitle"><i class="fas fa-chart-line"></i> Monthly Analytics</h3>
              <p id="chartSubtitle" class="chart-subtitle">Months of current year</p>
            </div>
            <div class="filter-section">
              <div class="toggle-group" id="timeToggle">
                <button type="button" class="toggle-btn" data-mode="daily">Daily</button>
                <button type="button" class="toggle-btn" data-mode="weekly">Weekly</button>
                <button type="button" class="toggle-btn active" data-mode="monthly">Monthly</button>
              </div>
            </div>
          </div>
          <div id="monthlyChart"></div>
        </div>

        <div class="events-card">
          <div class="card-header">
            <h3><i class="fas fa-calendar-alt"></i> Upcoming Events</h3>
            <button class="btn-primary btn-sm">View All</button>
          </div>
          <div class="events-list">
            <div class="event-item">
              <div class="event-date">
                <span class="day">05</span>
                <span class="month">MAY</span>
              </div>
              <div class="event-details">
                <h4>CCS Days</h4>
                <p><i class="fas fa-clock"></i> 8:00 AM - 5:00 PM</p>
                <p><i class="fas fa-map-marker-alt"></i> CCS Building</p>
              </div>
              <div class="event-status">
                <span class="badge upcoming">Upcoming</span>
              </div>
            </div>

            <div class="event-item">
              <div class="event-date">
                <span class="day">07</span>
                <span class="month">MAY</span>
              </div>
              <div class="event-details">
                <h4>CSS Night</h4>
                <p><i class="fas fa-clock"></i> 6:00 PM - 10:00 PM</p>
                <p><i class="fas fa-map-marker-alt"></i> Main Auditorium</p>
              </div>
              <div class="event-status">
                <span class="badge upcoming">Upcoming</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <script src="../assets/js/admin-dashboard-chart.js"></script>
</body>
</html>
