<?php
session_start();
require_once '../includes/page_access.php';
checkPageAccess(['view_dashboard']);
include('../components/sidebar.php');
?>
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
        <div class="header-left">
          <h1 class="page-title">Dashboard</h1>
          <?php
          $userName = $_SESSION['user_name'] ?? 'Admin';
          ?>
          <p class="welcome-text">Welcome back, <?= htmlspecialchars(trim($userName)) ?>!</p>
        </div>
        <div class="header-right">
          <div class="datetime-display">
            <span id="currentDay" class="day-text"></span>
            <span id="currentTime" class="time-text"></span>
          </div>
        </div>
      </div>

      <div class="card-row">
        <div class="card funds-card">
          <div class="card-icon">
            <i class="fas fa-wallet"></i>
          </div>
          <div class="card-content">
            <h3>Total Funds</h3>
            <p class="amount" id="adminTotalFundsAmount">₱0.00</p>
            <span class="trend" id="adminTotalFundsTrend">
              <i class="fas fa-spinner fa-spin"></i> Loading...
            </span>
          </div>
        </div>

        <div class="card expenses-card">
          <div class="card-icon">
            <i class="fas fa-money-bill-wave"></i>
          </div>
          <div class="card-content">
            <h3>Total Expenses</h3>
            <p class="amount" id="adminTotalExpensesAmount">₱0.00</p>
            <span class="trend" id="adminTotalExpensesTrend">
              <i class="fas fa-spinner fa-spin"></i> Loading...
            </span>
          </div>
        </div>

        <div class="card balance-card">
          <div class="card-icon">
            <i class="fas fa-piggy-bank"></i>
          </div>
          <div class="card-content">
            <h3>Current Balance</h3>
            <p class="amount" id="adminCurrentBalanceAmount">₱0.00</p>
            <span class="trend" id="adminCurrentBalanceTrend">
              <i class="fas fa-spinner fa-spin"></i> Loading...
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
            <?php if (hasPermission('manage_events')): ?>
            <a href="events.php" class="btn-primary btn-sm">View All</a>
            <?php elseif (hasPermission('view_events')): ?>
            <a href="event-calendar.php" class="btn-primary btn-sm">View All</a>
            <?php endif; ?>
          </div>
          <div class="events-list" id="upcomingEventsList">
            <div class="loading-events" style="text-align: center; padding: 40px 20px; color: #6b7280;">
              <i class="fas fa-spinner fa-spin" style="font-size: 24px; margin-bottom: 12px;"></i>
              <p>Loading events...</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <script src="../assets/js/admin-dashboard-chart.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      loadFinancialData();
      loadUpcomingEvents();
      updateDateTime();
      setInterval(updateDateTime, 1000);
    });

    async function loadFinancialData() {
      try {
        const apiUrl = '../api/get_student_financial_summary.php?t=' + Date.now();
        const response = await fetch(apiUrl);
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.status === 'success' && result.data) {
          updateAdminFinancialDisplay(result.data);
        } else {
          throw new Error(result.message || 'Failed to load financial data');
        }
      } catch (error) {
        console.error('Error loading financial data:', error);
        updateAdminFinancialDisplayError();
      }
    }

    function updateAdminFinancialDisplay(data) {
      const formatCurrency = (amount) => {
        const num = parseFloat(amount) || 0;
        return `₱${num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',')}`;
      };

      const formatPercentage = (change) => {
        if (change === null || change === undefined || isNaN(change)) {
          return { text: 'No change', class: 'neutral', icon: 'fa-minus' };
        }
        
        const absChange = Math.abs(change);
        if (absChange < 0.1) {
          return { text: 'No change', class: 'neutral', icon: 'fa-minus' };
        } else if (change > 0) {
          return { text: `${absChange.toFixed(1)}% from last month`, class: 'positive', icon: 'fa-arrow-up' };
        } else {
          return { text: `${absChange.toFixed(1)}% from last month`, class: 'negative', icon: 'fa-arrow-down' };
        }
      };

      const totalFundsEl = document.getElementById('adminTotalFundsAmount');
      const totalFundsTrendEl = document.getElementById('adminTotalFundsTrend');
      if (totalFundsEl && totalFundsTrendEl) {
        if (data.totalFunds !== undefined) {
          totalFundsEl.textContent = formatCurrency(data.totalFunds);
        }
        const fundsTrend = formatPercentage(data.fundsChange);
        totalFundsTrendEl.innerHTML = `<i class="fas ${fundsTrend.icon}"></i> ${fundsTrend.text}`;
        totalFundsTrendEl.className = `trend ${fundsTrend.class}`;
      }

      const totalExpensesEl = document.getElementById('adminTotalExpensesAmount');
      const totalExpensesTrendEl = document.getElementById('adminTotalExpensesTrend');
      if (totalExpensesEl && totalExpensesTrendEl) {
        if (data.totalExpenses !== undefined) {
          totalExpensesEl.textContent = formatCurrency(data.totalExpenses);
        }
        const expensesTrend = formatPercentage(data.expensesChange);
        totalExpensesTrendEl.innerHTML = `<i class="fas ${expensesTrend.icon}"></i> ${expensesTrend.text}`;
        totalExpensesTrendEl.className = `trend ${expensesTrend.class}`;
      }

      const currentBalanceEl = document.getElementById('adminCurrentBalanceAmount');
      const currentBalanceTrendEl = document.getElementById('adminCurrentBalanceTrend');
      if (currentBalanceEl && currentBalanceTrendEl) {
        if (data.availableBalance !== undefined) {
          currentBalanceEl.textContent = formatCurrency(data.availableBalance);
        }
        const balanceTrend = formatPercentage(data.balanceChange);
        currentBalanceTrendEl.innerHTML = `<i class="fas ${balanceTrend.icon}"></i> ${balanceTrend.text}`;
        currentBalanceTrendEl.className = `trend ${balanceTrend.class}`;
      }
    }

    function updateAdminFinancialDisplayError() {
      const trendElements = ['adminTotalFundsTrend', 'adminTotalExpensesTrend', 'adminCurrentBalanceTrend'];
      trendElements.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
          el.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Unable to load';
          el.className = 'trend error';
        }
      });
    }

    function updateDateTime() {
      const now = new Date();
      const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
      const dayElement = document.getElementById('currentDay');
      const timeElement = document.getElementById('currentTime');
      
      if (dayElement) {
        dayElement.textContent = days[now.getDay()];
      }
      
      if (timeElement) {
        let hours = now.getHours();
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const seconds = now.getSeconds().toString().padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12;
        const hoursStr = hours.toString().padStart(2, '0');
        timeElement.textContent = `${hoursStr}:${minutes}:${seconds} ${ampm}`;
      }
    }

    async function loadUpcomingEvents() {
      try {
        const response = await fetch('../api/get_student_events.php');
        const result = await response.json();
        
        const eventsList = document.getElementById('upcomingEventsList');
        
        if (result.status === 'success' && result.data && result.data.length > 0) {
          eventsList.innerHTML = '';
          
          result.data.slice(0, 5).forEach(event => {
            const eventItem = document.createElement('div');
            eventItem.className = 'event-item';
            
            let dateDisplay = `
              <div class="event-date">
                <span class="day">${event.day}</span>
                <span class="month">${event.month}</span>
              </div>
            `;
            
            if (event.is_multi_day && event.end_day) {
              const startDay = parseInt(event.day);
              const endDay = parseInt(event.end_day);
              dateDisplay = `
                <div class="event-date multi-day">
                  <span class="day">${startDay}-${endDay}</span>
                  <span class="month">${event.month}</span>
                </div>
              `;
            }
            
            eventItem.innerHTML = `
              ${dateDisplay}
              <div class="event-info">
                <h4>${event.title}</h4>
                <p><i class="fas fa-clock"></i> ${event.formatted_time}</p>
                <p><i class="fas fa-map-marker-alt"></i> ${event.location || 'TBA'}</p>
                <span class="event-tag ${event.category || 'general'}">${capitalizeFirst(event.category || 'General')}</span>
              </div>
            `;
            eventsList.appendChild(eventItem);
          });
        } else {
          eventsList.innerHTML = `
            <div style="text-align: center; padding: 40px 20px; color: #6b7280;">
              <i class="fas fa-calendar-xmark" style="font-size: 32px; margin-bottom: 16px;"></i>
              <p>No upcoming events</p>
            </div>
          `;
        }
      } catch (error) {
        console.error('Error loading events:', error);
        document.getElementById('upcomingEventsList').innerHTML = `
          <div style="text-align: center; padding: 40px 20px; color: #ef4444;">
            <i class="fas fa-exclamation-triangle" style="font-size: 32px; margin-bottom: 16px;"></i>
            <p>Unable to load events</p>
          </div>
        `;
      }
    }

    function capitalizeFirst(str) {
      return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
    }
  </script>
</body>
</html>
