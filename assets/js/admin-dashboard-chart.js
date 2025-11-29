document.addEventListener('DOMContentLoaded', function () {
  var el = document.querySelector('#monthlyChart');
  if (!el) return;

  var monthLabels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  var cachedFundsData = [];
  var cachedExpensesData = [];
  var isDataLoaded = false;
  var currentMode = 'monthly'; // 'monthly' | 'weekly' | 'daily'

  var options = {
    chart: {
      type: 'line',
      height: '100%',
      toolbar: { show: false },
      foreColor: '#4b5563',
      animations: {
        enabled: true,
        easing: 'easeinout',
        speed: 800
      }
    },
    stroke: {
      width: 3,
      curve: 'smooth'
    },
    grid: {
      borderColor: '#e5e7eb',
      strokeDashArray: 4
    },
    series: [
      { name: 'Monthly Incoming Funds', data: Array(12).fill(0), color: '#7a29cc' },
      { name: 'Monthly Expenses', data: Array(12).fill(0), color: '#ef4444' }
    ],
    xaxis: {
      categories: monthLabels,
      axisBorder: { color: '#e5e7eb' },
      axisTicks: { color: '#e5e7eb' }
    },
    yaxis: {
      labels: {
        formatter: function (val) {
          return '₱' + Number(val).toLocaleString();
        }
      }
    },
    markers: {
      size: 3,
      hover: { size: 5 }
    },
    legend: {
      position: 'bottom',
      horizontalAlign: 'center',
      fontFamily: 'Work Sans',
      fontSize: '14px'
    },
    tooltip: {
      y: {
        formatter: function (val) {
          return '₱' + Number(val).toLocaleString();
        }
      }
    }
  };

  var chart = new ApexCharts(el, options);
  chart.render();

  function monthIndexFromDate(dateStr) {
    if (!dateStr) return null;
    var d = new Date(dateStr);
    if (isNaN(d.getTime())) return null;
    return d.getMonth();
  }

  function getYearFromDate(dateStr) {
    if (!dateStr) return null;
    var d = new Date(dateStr);
    if (isNaN(d.getTime())) return null;
    return d.getFullYear();
  }

  function aggregateByMonth(items, amountKey, dateKeys) {
    var sums = Array(12).fill(0);
    var now = new Date();
    var currentYear = now.getFullYear();
    items.forEach(function (item) {
      var dateVal = null;
      for (var i = 0; i < dateKeys.length; i++) {
        if (item[dateKeys[i]]) { dateVal = item[dateKeys[i]]; break; }
      }
      var d = new Date(dateVal);
      if (d.getFullYear() !== currentYear) return;
      var idx = monthIndexFromDate(dateVal);
      var amount = Number(item[amountKey] || 0);
      if (idx !== null && idx >= 0 && idx < 12 && !isNaN(amount)) {
        sums[idx] += amount;
      }
    });
    return sums;
  }

  function fetchJson(url) {
    return fetch(url, { headers: { 'Accept': 'application/json' } })
      .then(function (r) { return r.json(); })
      .catch(function () { return null; });
  }

  function getStartOfWeek(date) {
    var d = new Date(date);
    var day = d.getDay(); // 0=Sun ... 6=Sat
    var diff = (day === 0 ? -6 : 1) - day; // shift to Monday
    d.setDate(d.getDate() + diff);
    d.setHours(0,0,0,0);
    return d;
  }

  function sameDay(a, b) {
    return a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b.getDate();
  }

  function aggregateDailyCurrentWeek(items, amountKey, dateKeys) {
    var labels = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
    var sums = Array(7).fill(0);
    var today = new Date();
    var weekStart = getStartOfWeek(today);
    var weekDates = labels.map(function(_, i){
      var d = new Date(weekStart);
      d.setDate(weekStart.getDate() + i);
      return d;
    });
    items.forEach(function(item){
      var dateVal = null;
      for (var i=0;i<dateKeys.length;i++){ if (item[dateKeys[i]]) { dateVal = item[dateKeys[i]]; break; } }
      if (!dateVal) return;
      var d = new Date(dateVal);
      for (var j=0;j<7;j++){
        if (sameDay(d, weekDates[j])){
          var amt = Number(item[amountKey] || 0);
          if (!isNaN(amt)) sums[j] += amt;
        }
      }
    });
    return { labels: labels, data: sums, weekStart: weekStart };
  }

  function weekOfMonth(d) {
    var date = new Date(d);
    var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
    var day = firstDay.getDay() || 7; // 1..7 with Monday as 1
    return Math.ceil((firstDay.getDate() - 1 + (7 - (day - 1)) + date.getDate()) / 7);
  }

  function aggregateWeeklyCurrentMonth(items, amountKey, dateKeys) {
    var labels = ['Week 1','Week 2','Week 3','Week 4','Week 5'];
    var sums = Array(5).fill(0);
    var now = new Date();
    var targetYear = now.getFullYear();
    var targetMonth = now.getMonth();
    items.forEach(function(item){
      var dateVal = null;
      for (var i=0;i<dateKeys.length;i++){ if (item[dateKeys[i]]) { dateVal = item[dateKeys[i]]; break; } }
      if (!dateVal) return;
      var d = new Date(dateVal);
      if (d.getFullYear() !== targetYear || d.getMonth() !== targetMonth) return;
      var w = Math.min(5, Math.max(1, weekOfMonth(d)));
      var amt = Number(item[amountKey] || 0);
      if (!isNaN(amt)) sums[w-1] += amt;
    });
    return { labels: labels, data: sums, month: targetMonth, year: targetYear };
  }

  function formatMonthName(monthIndex) {
    var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    return months[monthIndex];
  }

  function formatDateRange(startDate, endDate) {
    var monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    var startMonth = monthNames[startDate.getMonth()];
    var endMonth = monthNames[endDate.getMonth()];
    var startDay = startDate.getDate();
    var endDay = endDate.getDate();
    return startMonth + ' ' + startDay + ' - ' + endMonth + ' ' + endDay;
  }

  function updateChartTitle(mode) {
    var chartTitle = document.getElementById('chartTitle');
    var chartSubtitle = document.getElementById('chartSubtitle');
    if (!chartTitle || !chartSubtitle) return;

    var now = new Date();
    var currentYear = now.getFullYear();

    if (mode === 'daily') {
      var weekStart = getStartOfWeek(now);
      var weekEnd = new Date(weekStart);
      weekEnd.setDate(weekStart.getDate() + 6);
      chartTitle.innerHTML = '<i class="fas fa-chart-line"></i> Weekly Financial Overview (' + formatDateRange(weekStart, weekEnd) + ')';
      chartSubtitle.textContent = 'Monday to Sunday of current week';
    } else if (mode === 'weekly') {
      var monthName = formatMonthName(now.getMonth());
      chartTitle.innerHTML = '<i class="fas fa-chart-line"></i> Monthly Financial Overview (' + monthName + ' ' + currentYear + ')';
      chartSubtitle.textContent = 'Weeks within current month';
    } else {
      chartTitle.innerHTML = '<i class="fas fa-chart-line"></i> Yearly Financial Overview (' + currentYear + ')';
      chartSubtitle.textContent = 'Months of current year';
    }
  }

  function updateChart() {
    if (!isDataLoaded) {
      Promise.all([
        fetchJson('../api/get_funds.php?limit=1000&page=1'),
        fetchJson('../api/get_expenses.php?limit=1000&page=1')
      ]).then(function (responses) {
        var fundsRes = responses[0];
        var expensesRes = responses[1];

        cachedFundsData = Array.isArray(fundsRes && fundsRes.data) ? fundsRes.data : [];
        cachedExpensesData = Array.isArray(expensesRes && expensesRes.data) ? expensesRes.data : [];
        isDataLoaded = true;
        
        updateChartData();
      });
    } else {
      updateChartData();
    }
  }

  function updateChartData() {
    updateChartTitle(currentMode);

    var xCats = monthLabels;
    var fundsSeries = [];
    var expensesSeries = [];

    if (currentMode === 'daily') {
      var f = aggregateDailyCurrentWeek(cachedFundsData, 'amount', ['date_received', 'date', 'created_at']);
      var e = aggregateDailyCurrentWeek(cachedExpensesData, 'amount', ['date', 'created_at']);
      xCats = f.labels;
      fundsSeries = f.data;
      expensesSeries = e.data;
    } else if (currentMode === 'weekly') {
      var fw = aggregateWeeklyCurrentMonth(cachedFundsData, 'amount', ['date_received', 'date', 'created_at']);
      var ew = aggregateWeeklyCurrentMonth(cachedExpensesData, 'amount', ['date', 'created_at']);
      xCats = fw.labels;
      fundsSeries = fw.data;
      expensesSeries = ew.data;
    } else {
      var fm = aggregateByMonth(cachedFundsData, 'amount', ['date_received', 'date', 'created_at']);
      var em = aggregateByMonth(cachedExpensesData, 'amount', ['date', 'created_at']);
      xCats = monthLabels;
      fundsSeries = fm;
      expensesSeries = em;
    }

    chart.updateOptions({ xaxis: { categories: xCats } });
    chart.updateSeries([
      { name: 'Monthly Incoming Funds', data: fundsSeries, color: '#7a29cc' },
      { name: 'Monthly Expenses', data: expensesSeries, color: '#ef4444' }
    ], true);
  }

  var toggleGroup = document.getElementById('timeToggle');
  if (toggleGroup) {
    toggleGroup.addEventListener('click', function (e) {
      var btn = e.target.closest('.toggle-btn');
      if (!btn) return;
      var mode = btn.getAttribute('data-mode');
      if (mode && mode !== currentMode) {
        currentMode = mode;
        Array.from(toggleGroup.querySelectorAll('.toggle-btn')).forEach(function(b){ b.classList.toggle('active', b === btn); });
        updateChart();
      }
    });
  }

  updateChart('');
});


