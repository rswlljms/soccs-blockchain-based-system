const ctx = document.getElementById('expensesChart').getContext('2d');

new Chart(ctx, {
  type: 'line',
  data: {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'],
    datasets: [{
      label: 'Monthly Expenses',
      data: [2500, 4100, 3200, 2800, 900, 1600, 1800, 2000, 3000, 500, 600, 700],
      borderColor: '#4B0082',
      backgroundColor: 'rgba(155, 89, 182, 0.2)',
      borderWidth: 2,
      tension: 0.3,
      pointBackgroundColor: '#4B0082',
      pointRadius: 4
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        labels: {
          color: '#333',
          font: {
            family: 'Work Sans',
            size: 14
          }
        }
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          color: '#333'
        }
      },
      x: {
        ticks: {
          color: '#333'
        }
      }
    }
  }
});
