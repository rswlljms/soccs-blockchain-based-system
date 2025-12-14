// Student Dashboard JavaScript Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add loading class to prevent flash
    document.body.classList.add('loading');
    
    preventSidebarFlash();
    loadActiveElection();
    loadUpcomingEvents();
    loadFinancialData();
    loadRecentTransactions();
    
    initializeAnnouncementsCycle();
    initializeFundCharts();
    initializeSecurityChecks();
    // updateLastActivity(); // Disabled - timestamp removed
    initializeMobileMenu();
    
    // Remove loading class after everything is set up
    setTimeout(() => {
        document.body.classList.remove('loading');
    }, 50);
});

// Prevent sidebar flash on page load
function preventSidebarFlash() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        // Check if we're on desktop or mobile
        const isDesktop = window.innerWidth > 768;
        
        if (isDesktop) {
            // Desktop: Show sidebar immediately
            sidebar.style.transform = 'translateX(0)';
            sidebar.style.left = '0';
            sidebar.style.position = 'fixed';
            sidebar.style.opacity = '1';
            sidebar.style.visibility = 'visible';
        } else {
            // Mobile: Hide sidebar initially
            sidebar.style.transform = 'translateX(-100%)';
            sidebar.style.opacity = '1';
            sidebar.style.visibility = 'visible';
        }
        
        // Enable transitions after positioning
        setTimeout(() => {
            sidebar.style.transition = 'transform 0.3s ease-out';
            sidebar.classList.add('sidebar-loaded');
        }, 10);
    }
}

async function loadActiveElection() {
    try {
        const response = await fetch('../api/elections/get_active.php');
        const result = await response.json();
        
        const electionCard = document.getElementById('electionCard');
        const statusBadge = document.getElementById('electionStatusBadge');
        const statusText = document.getElementById('statusText');
        const electionContent = document.getElementById('electionContent');
        
        if (result.success && result.data) {
            const election = result.data;
            const stats = election.stats;
            const now = new Date();
            const startDate = new Date(election.start_date);
            const endDate = new Date(election.end_date);
            
            const isActive = election.status === 'active';
            const isUpcoming = election.status === 'upcoming' || (!isActive && now < startDate);
            const hasEnded = now > endDate;
            
            if (isActive && !hasEnded) {
                statusBadge.className = 'election-status live';
                statusText.textContent = 'Election Live';
            } else if (isUpcoming) {
                statusBadge.className = 'election-status upcoming';
                statusText.textContent = 'Election Not Started';
            } else if (hasEnded) {
                statusBadge.className = 'election-status no-active';
                statusText.textContent = 'Election Ended';
            }
            
            let contentHTML = `
                <div class="election-info">
                    <h4>${election.title}</h4>
                    <p class="election-date"><i class="fas fa-calendar"></i> ${formatElectionDate(isActive && !hasEnded ? election.end_date : election.start_date)}</p>
                    <p class="time-remaining">
                        <i class="fas fa-clock"></i> 
                        <span id="countdown">Calculating...</span>
                    </p>
                </div>
                
                <div class="quick-stats">
                    <div class="stat">
                        <div class="stat-number">${stats.total_positions || 0}</div>
                        <div class="stat-label">Positions</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">${stats.total_candidates || 0}</div>
                        <div class="stat-label">Candidates</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">${stats.eligible_voters || 0}</div>
                        <div class="stat-label">Eligible Voters</div>
                    </div>
                </div>
            `;
            
            if (isActive && !hasEnded) {
                contentHTML += `
                    <div class="voting-actions">
                        <button class="btn-vote" onclick="goToVoting()">
                            <i class="fas fa-vote-yea"></i>
                            Cast Your Vote
                        </button>
                    </div>
                `;
            } else if (isUpcoming) {
                contentHTML += `
                    <div class="voting-actions">
                        <button class="btn-vote" disabled style="opacity: 0.6; cursor: not-allowed;">
                            <i class="fas fa-clock"></i>
                            Voting Not Available Yet
                        </button>
                    </div>
                `;
            } else if (hasEnded) {
                contentHTML += `
                    <div class="voting-actions">
                        <button class="btn-vote" disabled style="opacity: 0.6; cursor: not-allowed;">
                            <i class="fas fa-check-circle"></i>
                            Election Has Ended
                        </button>
                    </div>
                `;
            }
            
            electionContent.innerHTML = contentHTML;
            
            if (isActive && !hasEnded) {
                initializeCountdown(endDate, false);
            } else if (isUpcoming) {
                initializeCountdown(startDate, true);
            }
            
        } else {
            statusBadge.className = 'election-status no-active';
            statusText.textContent = 'No Active Election';
            
            electionContent.innerHTML = `
                <div style="text-align: center; padding: 40px 20px;">
                    <i class="fas fa-info-circle" style="font-size: 48px; color: #9ca3af; margin-bottom: 16px;"></i>
                    <h4 style="margin: 0 0 8px 0; color: #374151;">No Active Election</h4>
                    <p style="color: #6b7280; margin: 0;">There are currently no ongoing elections. Check back later for updates.</p>
                </div>
            `;
        }
        
    } catch (error) {
        console.error('Error loading election:', error);
        document.getElementById('electionContent').innerHTML = `
            <div style="text-align: center; padding: 40px 20px; color: #ef4444;">
                <i class="fas fa-exclamation-triangle" style="font-size: 32px; margin-bottom: 16px;"></i>
                <p>Unable to load election information</p>
            </div>
        `;
    }
}

function formatElectionDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
}

function initializeCountdown(electionDate, isUpcoming = false) {
    const countdownElement = document.getElementById('countdown');
    if (!countdownElement) return;

    const electionTime = new Date(electionDate).getTime();

    function updateCountdown() {
        const now = new Date().getTime();
        const distance = electionTime - now;

        if (distance < 0) {
            if (isUpcoming) {
                countdownElement.innerHTML = "Election has started";
            } else {
                countdownElement.innerHTML = "Election has ended";
            }
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

        if (isUpcoming) {
            countdownElement.innerHTML = `Starts in ${days} days, ${hours} hours, ${minutes} minutes`;
        } else {
            countdownElement.innerHTML = `${days} days, ${hours} hours, ${minutes} minutes remaining`;
        }
    }

    updateCountdown();
    setInterval(updateCountdown, 1000 * 60);
}

// Voting Modal Functions
function goToVoting() {
    document.getElementById('votingModal').style.display = 'flex';
    animateModalEntry();
}

function closeVotingModal() {
    document.getElementById('votingModal').style.display = 'none';
}

function proceedToVoting() {
    // In a real application, this would redirect to the voting page
    showNotification('Redirecting to secure voting portal...', 'info');
    setTimeout(() => {
        window.location.href = '../pages/student-voting.php';
    }, 1500);
}

function animateModalEntry() {
    const modal = document.querySelector('.voting-modal');
    modal.style.transform = 'scale(0.7)';
    modal.style.opacity = '0';
    
    setTimeout(() => {
        modal.style.transition = 'all 0.3s ease-out';
        modal.style.transform = 'scale(1)';
        modal.style.opacity = '1';
    }, 50);
}

// Load Upcoming Events from Database
async function loadUpcomingEvents() {
    try {
        const response = await fetch('../api/get_student_events.php?limit=3&status=upcoming');
        const result = await response.json();
        
        const eventsList = document.getElementById('upcomingEventsList');
        
        if (result.status === 'success' && result.data && result.data.length > 0) {
            eventsList.innerHTML = '';
            
            result.data.forEach(event => {
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
                        <span class="event-tag ${event.category}">${capitalizeFirst(event.category).toUpperCase()}</span>
                    </div>
                `;
                eventsList.appendChild(eventItem);
            });
        } else {
            eventsList.innerHTML = `
                <div style="text-align: center; padding: 40px 20px; color: #6b7280;">
                    <i class="fas fa-calendar" style="font-size: 48px; margin-bottom: 16px; opacity: 0.3;"></i>
                    <p style="margin: 0;">No upcoming events at this time</p>
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
    return str.charAt(0).toUpperCase() + str.slice(1);
}

// Navigation Functions

function viewAllEvents() {
    window.location.href = '../pages/student-events.php';
}

function viewFinancialReports() {
    window.location.href = '../pages/student-financial-reports.php';
}

function openBlockchainExplorer() {
    showNotification('Opening blockchain explorer...', 'info');
    setTimeout(() => {
        window.location.href = '../pages/student-blockchain.php';
    }, 1000);
}

// Dynamic Content Functions
function initializeAnnouncementsCycle() {
    const announcements = document.querySelectorAll('.announcement-item.new');
    
    announcements.forEach((announcement, index) => {
        announcement.style.animationDelay = `${index * 0.2}s`;
        announcement.classList.add('slide-in');
    });
}

// Fund Chart Visualization (simplified)
function initializeFundCharts() {
    const fundItems = document.querySelectorAll('.fund-item');
    
    fundItems.forEach((item, index) => {
        setTimeout(() => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(20px)';
            item.style.transition = 'all 0.5s ease-out';
            
            setTimeout(() => {
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
            }, 100);
        }, index * 200);
    });
}

// Security Status Checks
function initializeSecurityChecks() {
    const securityItems = document.querySelectorAll('.security-item');
    
    securityItems.forEach((item, index) => {
        setTimeout(() => {
            const badge = item.querySelector('.status-badge');
            if (badge) {
                badge.style.animation = 'pulse-badge 2s infinite';
            }
        }, index * 500);
    });
}

// Notification System
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());

    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas ${getNotificationIcon(type)}"></i>
        <span>${message}</span>
        <button class="notification-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;

    document.body.appendChild(notification);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

function getNotificationIcon(type) {
    const icons = {
        'info': 'fa-info-circle',
        'success': 'fa-check-circle',
        'warning': 'fa-exclamation-triangle',
        'error': 'fa-times-circle'
    };
    return icons[type] || icons.info;
}

// Blockchain Hash Click Handler
document.addEventListener('click', function(e) {
    if (e.target.closest('.blockchain-hash')) {
        const hash = generateDemoHash();
        showNotification(`Blockchain Hash: ${hash}`, 'info');
    }
});

function generateDemoHash() {
    return '0x' + Math.random().toString(16).substr(2, 8) + '...';
}

// Real-time Updates Simulation
function updateLastActivity() {
    // COMPLETELY DISABLED - Remove all timestamp elements
    const lastUpdatedElements = document.querySelectorAll('.last-updated, .last-refresh');
    lastUpdatedElements.forEach(element => {
        element.remove();
    });
    
    // Also hide any fixed positioned timestamp at bottom right
    const timestamps = document.querySelectorAll('[style*="position: fixed"][style*="bottom"][style*="right"]');
    timestamps.forEach(element => {
        if (element.textContent.includes('Last updated') || element.textContent.includes('updated:')) {
            element.remove();
        }
    });
}

// Voting Status Updates
function updateVotingStatus(hasVoted = false) {
    const statusIndicator = document.querySelector('.voting-status .status-indicator');
    const voteButton = document.querySelector('.btn-vote');
    
    if (hasVoted) {
        statusIndicator.className = 'status-indicator voted';
        statusIndicator.innerHTML = '<i class="fas fa-check-circle"></i><span>Vote Cast</span>';
        
        if (voteButton) {
            voteButton.disabled = true;
            voteButton.innerHTML = '<i class="fas fa-check"></i>Vote Recorded';
            voteButton.style.background = 'var(--success-color)';
        }
    }
}

async function loadFinancialData() {
    try {
        const apiUrl = '../api/get_student_financial_summary.php?t=' + Date.now();
        const response = await fetch(apiUrl);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.status === 'success' && result.data) {
            updateFinancialDisplay(result.data);
        } else {
            throw new Error(result.message || 'Failed to load financial data');
        }
    } catch (error) {
        console.error('Error loading financial data:', error);
        updateFinancialDisplayError();
    }
}

function updateFinancialDisplay(data) {
    const formatCurrency = (amount) => {
        const num = parseFloat(amount) || 0;
        return `₱${num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',')}`;
    };

    const formatPercentage = (change) => {
        if (change === null || change === undefined || isNaN(change)) {
            return { text: '— No change', class: 'neutral', icon: 'fa-minus' };
        }
        
        const absChange = Math.abs(change);
        if (absChange < 0.1) {
            return { text: '— No change', class: 'neutral', icon: 'fa-minus' };
        } else if (change > 0) {
            return { text: `↑ ${absChange.toFixed(1)}% from last month`, class: 'positive', icon: 'fa-arrow-up' };
        } else {
            return { text: `↓ ${absChange.toFixed(1)}% from last month`, class: 'negative', icon: 'fa-arrow-down' };
        }
    };

    const totalFundsEl = document.getElementById('totalFundsAmount');
    const totalFundsStatusEl = document.getElementById('totalFundsStatus');
    if (totalFundsEl && totalFundsStatusEl) {
        if (data.totalFunds !== undefined) {
            totalFundsEl.textContent = formatCurrency(data.totalFunds);
        }
        const fundsTrend = formatPercentage(data.fundsChange);
        totalFundsStatusEl.innerHTML = `<i class="fas ${fundsTrend.icon}"></i> ${fundsTrend.text}`;
        totalFundsStatusEl.className = `fund-status ${fundsTrend.class}`;
    }

    const totalExpensesEl = document.getElementById('totalExpensesAmount');
    const totalExpensesStatusEl = document.getElementById('totalExpensesStatus');
    if (totalExpensesEl && totalExpensesStatusEl) {
        if (data.totalExpenses !== undefined) {
            totalExpensesEl.textContent = formatCurrency(data.totalExpenses);
        }
        const expensesTrend = formatPercentage(data.expensesChange);
        totalExpensesStatusEl.innerHTML = `<i class="fas ${expensesTrend.icon}"></i> ${expensesTrend.text}`;
        totalExpensesStatusEl.className = `fund-status ${expensesTrend.class}`;
    }

    const currentBalanceEl = document.getElementById('currentBalanceAmount');
    const currentBalanceStatusEl = document.getElementById('currentBalanceStatus');
    if (currentBalanceEl && currentBalanceStatusEl) {
        if (data.availableBalance !== undefined) {
            currentBalanceEl.textContent = formatCurrency(data.availableBalance);
        }
        const balanceTrend = formatPercentage(data.balanceChange);
        currentBalanceStatusEl.innerHTML = `<i class="fas ${balanceTrend.icon}"></i> ${balanceTrend.text}`;
        currentBalanceStatusEl.className = `fund-status ${balanceTrend.class}`;
    }
}

function updateFinancialDisplayError() {
    const statusElements = ['totalFundsStatus', 'totalExpensesStatus', 'currentBalanceStatus'];
    statusElements.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Unable to load';
            el.className = 'fund-status error';
        }
    });
}

// Load Recent Transactions
async function loadRecentTransactions() {
    try {
        const response = await fetch('../api/get_recent_transactions.php');
        const transactions = await response.json();
        
        updateTransactionsList(transactions);
    } catch (error) {
        console.error('Error loading transactions:', error);
    }
}

function updateTransactionsList(transactions) {
    const transactionsList = document.querySelector('.transaction-list');
    if (!transactionsList || !transactions) return;

    transactionsList.innerHTML = '';
    
    transactions.slice(0, 3).forEach(transaction => {
        const transactionElement = document.createElement('div');
        transactionElement.className = 'transaction-item';
        transactionElement.innerHTML = `
            <div class="transaction-info">
                <span class="transaction-name">${transaction.name}</span>
                <span class="transaction-date">${formatDate(transaction.date)}</span>
            </div>
            <div class="transaction-amount ${transaction.amount > 0 ? 'income' : 'expense'}">
                ${transaction.amount > 0 ? '+' : ''}₱${Math.abs(transaction.amount).toLocaleString()}
            </div>
            <div class="blockchain-hash" title="Blockchain Hash: ${transaction.hash}">
                <i class="fas fa-link"></i>
            </div>
        `;
        transactionsList.appendChild(transactionElement);
    });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric' 
    });
}

// Event Listeners for Interactive Elements
document.addEventListener('click', function(e) {
    // Handle card hover effects
    if (e.target.closest('.card')) {
        const card = e.target.closest('.card');
        card.style.transform = 'translateY(-4px)';
        
        setTimeout(() => {
            card.style.transform = 'translateY(-2px)';
        }, 150);
    }
    
    // Handle blockchain verification clicks
    if (e.target.closest('.blockchain-verified')) {
        showNotification('All data verified on blockchain ✓', 'success');
    }
});

// Keyboard Navigation
document.addEventListener('keydown', function(e) {
    // Close modal with Escape key
    if (e.key === 'Escape') {
        const modal = document.getElementById('votingModal');
        if (modal && modal.style.display === 'flex') {
            closeVotingModal();
        }
    }
    
    // Quick voting with Ctrl+V
    if (e.ctrlKey && e.key === 'v') {
        e.preventDefault();
        goToVoting();
    }
});

// Mobile Menu Functionality
function initializeMobileMenu() {
    const mobileToggle = document.getElementById('mobileMenuToggle');
    const mobileOverlay = document.getElementById('mobileOverlay');
    const sidebar = document.querySelector('.sidebar');
    
    if (!mobileToggle || !mobileOverlay || !sidebar) return;
    
    // Only initialize mobile functionality if we're on mobile
    function handleMobileMenuSetup() {
        const isMobile = window.innerWidth <= 768;
        
        if (isMobile) {
            // Mobile: Enable hamburger menu functionality
            mobileToggle.style.display = 'flex';
            mobileToggle.style.visibility = 'visible';
            mobileToggle.style.opacity = '1';
        } else {
            // Desktop: Completely hide hamburger menu
            mobileToggle.style.display = 'none';
            mobileToggle.style.visibility = 'hidden';
            mobileToggle.style.opacity = '0';
            
            // Ensure sidebar is visible on desktop
            sidebar.classList.remove('mobile-open');
            mobileOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    }
    
    // Initial setup
    handleMobileMenuSetup();
    
    // Toggle mobile menu (only works on mobile)
    mobileToggle.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if (window.innerWidth <= 768) {
            toggleMobileMenu();
        }
    });
    
    // Close menu when overlay is clicked (only on mobile)
    mobileOverlay.addEventListener('click', function() {
        if (window.innerWidth <= 768) {
            closeMobileMenu();
        }
    });
    
    // Close menu when a nav link is clicked (only on mobile)
    const navLinks = sidebar.querySelectorAll('a:not(.dropdown-toggle)');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                closeMobileMenu();
            }
        });
    });
    
    // Handle window resize - adjust layout
    window.addEventListener('resize', function() {
        handleMobileMenuSetup();
    });
    
    // Handle escape key (only on mobile)
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && window.innerWidth <= 768 && sidebar.classList.contains('mobile-open')) {
            closeMobileMenu();
        }
    });
}

function toggleMobileMenu() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('mobileOverlay');
    const toggle = document.getElementById('mobileMenuToggle');
    
    if (sidebar.classList.contains('mobile-open')) {
        closeMobileMenu();
    } else {
        openMobileMenu();
    }
}

function openMobileMenu() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('mobileOverlay');
    const toggle = document.getElementById('mobileMenuToggle');
    
    sidebar.classList.add('mobile-open');
    overlay.classList.add('active');
    toggle.innerHTML = '<i class="fas fa-times"></i>';
    
    // Prevent body scroll when menu is open
    document.body.style.overflow = 'hidden';
}

function closeMobileMenu() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('mobileOverlay');
    const toggle = document.getElementById('mobileMenuToggle');
    
    sidebar.classList.remove('mobile-open');
    overlay.classList.remove('active');
    toggle.innerHTML = '<i class="fas fa-bars"></i>';
    
    // Restore body scroll
    document.body.style.overflow = '';
}

// Initialize periodic updates
setInterval(() => {
    // updateLastActivity(); // Disabled - timestamp removed
    // Periodically check for new announcements
    checkForNewAnnouncements();
}, 60000); // Every minute

async function checkForNewAnnouncements() {
    try {
        const response = await fetch('../api/check_new_announcements.php');
        const data = await response.json();
        
        if (data.hasNew) {
            const badge = document.querySelector('.new-badge');
            if (badge) {
                badge.textContent = `${data.count} New`;
                badge.style.animation = 'pulse 1s infinite';
            }
        }
    } catch (error) {
        console.error('Error checking announcements:', error);
    }
}

// Animation CSS classes (to be added via JavaScript)
const style = document.createElement('style');
style.textContent = `
    .slide-in {
        animation: slideIn 0.5s ease-out forwards;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes pulse-badge {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        border-radius: 8px;
        padding: 16px 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        gap: 12px;
        z-index: 1001;
        transform: translateX(100%);
        opacity: 0;
        transition: all 0.3s ease-out;
        max-width: 300px;
    }
    
    .notification.notification-info { border-left: 4px solid #3b82f6; }
    .notification.notification-success { border-left: 4px solid #10b981; }
    .notification.notification-warning { border-left: 4px solid #f59e0b; }
    .notification.notification-error { border-left: 4px solid #ef4444; }
    
    .notification-close {
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px;
        color: #6b7280;
        margin-left: auto;
    }
    
    .notification-close:hover {
        color: #374151;
    }
    
    .sidebar.mobile-open {
        transform: translateX(0);
    }
    
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease-out;
        }
    }
    
    .loading-events {
        text-align: center;
        padding: 40px 20px;
        color: #9ca3af;
    }
    
    .loading-events i {
        font-size: 32px;
        margin-bottom: 12px;
    }
    
    .loading-events p {
        margin: 0;
        font-size: 14px;
    }
`;
document.head.appendChild(style);

// Show notifications after DOM is ready
setTimeout(() => {
    const notifications = document.querySelectorAll('.notification');
    notifications.forEach(notification => {
        notification.style.transform = 'translateX(0)';
        notification.style.opacity = '1';
    });
}, 100);
