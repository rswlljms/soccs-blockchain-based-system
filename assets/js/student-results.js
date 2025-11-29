// Student Results JavaScript Functionality
document.addEventListener('DOMContentLoaded', function() {
    initializeResultsPage();
    // Mobile menu is already initialized by student-dashboard.js
    animateProgressBars();
});

// Initialize results page functionality
function initializeResultsPage() {
    updateLastRefresh();
    initializeCounters();
    setupAutoRefresh();
    addInteractiveFeatures();
}

// Animate progress bars on load
function animateProgressBars() {
    const progressBars = document.querySelectorAll('.progress-fill');
    
    progressBars.forEach((bar, index) => {
        const targetWidth = bar.style.width;
        bar.style.width = '0%';
        
        setTimeout(() => {
            bar.style.width = targetWidth;
        }, 500 + (index * 200));
    });
}

// Initialize vote counters with animation
function initializeCounters() {
    const voteNumbers = document.querySelectorAll('.votes-number');
    
    voteNumbers.forEach(counter => {
        const target = parseInt(counter.textContent.replace(/,/g, ''));
        animateCounter(counter, target);
    });
    
    // Animate stat numbers
    const statNumbers = document.querySelectorAll('.stat-number');
    statNumbers.forEach(counter => {
        const text = counter.textContent;
        const target = parseInt(text.replace(/[,%]/g, ''));
        const isPercentage = text.includes('%');
        
        animateCounter(counter, target, isPercentage);
    });
}

// Animate counter numbers
function animateCounter(element, target, isPercentage = false) {
    let current = 0;
    const increment = target / 100;
    const duration = 2000; // 2 seconds
    const stepTime = duration / 100;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        
        const displayValue = Math.floor(current);
        if (isPercentage) {
            element.textContent = displayValue + '%';
        } else {
            element.textContent = displayValue.toLocaleString();
        }
    }, stepTime);
}

// Blockchain verification functions
function verifyOnBlockchain() {
    showNotification('Opening blockchain explorer...', 'info');
    
    // Simulate blockchain verification
    setTimeout(() => {
        const modal = createVerificationModal();
        document.body.appendChild(modal);
        modal.style.display = 'flex';
    }, 1000);
}

function createVerificationModal() {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal verification-modal">
            <div class="modal-header">
                <h3><i class="fas fa-cube"></i> Blockchain Verification</h3>
                <button class="modal-close" onclick="this.closest('.modal-overlay').remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <div class="verification-content">
                    <div class="verification-status success">
                        <i class="fas fa-check-circle"></i>
                        <h4>All Votes Verified</h4>
                        <p>Every vote has been successfully verified on the blockchain</p>
                    </div>
                    
                    <div class="blockchain-details">
                        <div class="detail-item">
                            <span class="label">Block Height:</span>
                            <span class="value">#${Math.floor(Math.random() * 1000000) + 500000}</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Transaction Hash:</span>
                            <span class="value">0x${generateRandomHash()}</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Confirmation Time:</span>
                            <span class="value">${new Date().toLocaleString()}</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Network:</span>
                            <span class="value">SOCCS Voting Chain</span>
                        </div>
                    </div>
                    
                    <div class="verification-actions">
                        <button class="btn-explorer" onclick="openExternalExplorer()">
                            <i class="fas fa-external-link-alt"></i>
                            View on Explorer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    return modal;
}

function generateRandomHash() {
    const chars = '0123456789abcdef';
    let hash = '';
    for (let i = 0; i < 64; i++) {
        hash += chars[Math.floor(Math.random() * chars.length)];
    }
    return hash;
}

function openExternalExplorer() {
    showNotification('Opening external blockchain explorer...', 'info');
    // In real implementation, would open actual blockchain explorer
}

// Audit log functions
function viewAuditLog() {
    showNotification('Loading audit log...', 'info');
    
    setTimeout(() => {
        const modal = createAuditModal();
        document.body.appendChild(modal);
        modal.style.display = 'flex';
    }, 800);
}

function createAuditModal() {
    const auditEvents = [
        { time: '2025-01-15 14:30:25', event: 'Election started', user: 'System', type: 'info' },
        { time: '2025-01-15 14:30:30', event: 'First vote cast', user: 'Student #1001', type: 'success' },
        { time: '2025-01-15 15:45:12', event: 'Vote verification completed', user: 'Blockchain', type: 'success' },
        { time: '2025-01-15 16:20:45', event: 'Interim results generated', user: 'System', type: 'info' },
        { time: '2025-01-15 18:00:00', event: 'Election ended', user: 'System', type: 'warning' },
        { time: '2025-01-15 18:00:15', event: 'Final tally completed', user: 'System', type: 'success' }
    ];
    
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal audit-modal">
            <div class="modal-header">
                <h3><i class="fas fa-list-alt"></i> Election Audit Log</h3>
                <button class="modal-close" onclick="this.closest('.modal-overlay').remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <div class="audit-content">
                    <div class="audit-summary">
                        <p>Complete audit trail of all election activities</p>
                    </div>
                    
                    <div class="audit-log">
                        ${auditEvents.map(event => `
                            <div class="audit-item ${event.type}">
                                <div class="audit-time">${event.time}</div>
                                <div class="audit-event">${event.event}</div>
                                <div class="audit-user">${event.user}</div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
        </div>
    `;
    return modal;
}

// Security report functions
function viewSecurityReport() {
    showNotification('Generating security report...', 'info');
    
    setTimeout(() => {
        const modal = createSecurityModal();
        document.body.appendChild(modal);
        modal.style.display = 'flex';
    }, 1200);
}

function createSecurityModal() {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal security-modal">
            <div class="modal-header">
                <h3><i class="fas fa-shield-alt"></i> Security Report</h3>
                <button class="modal-close" onclick="this.closest('.modal-overlay').remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <div class="security-content">
                    <div class="security-overview">
                        <div class="security-status success">
                            <i class="fas fa-shield-check"></i>
                            <h4>All Security Checks Passed</h4>
                        </div>
                    </div>
                    
                    <div class="security-checks">
                        <div class="check-item passed">
                            <i class="fas fa-check"></i>
                            <span>Vote Encryption: AES-256</span>
                        </div>
                        <div class="check-item passed">
                            <i class="fas fa-check"></i>
                            <span>Identity Verification: Completed</span>
                        </div>
                        <div class="check-item passed">
                            <i class="fas fa-check"></i>
                            <span>Blockchain Integrity: Verified</span>
                        </div>
                        <div class="check-item passed">
                            <i class="fas fa-check"></i>
                            <span>Anonymous Voting: Ensured</span>
                        </div>
                        <div class="check-item passed">
                            <i class="fas fa-check"></i>
                            <span>Tamper Detection: Active</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    return modal;
}

// Export functions
function exportToPDF() {
    showNotification('Generating PDF report...', 'info');
    
    // Simulate PDF generation
    setTimeout(() => {
        const link = document.createElement('a');
        link.download = 'SOCCS_Election_Results_2025.pdf';
        link.href = '#'; // In real implementation, would be actual PDF URL
        showNotification('PDF report generated successfully!', 'success');
    }, 2000);
}

function exportToExcel() {
    showNotification('Generating Excel report...', 'info');
    
    // Simulate Excel generation
    setTimeout(() => {
        const link = document.createElement('a');
        link.download = 'SOCCS_Election_Results_2025.xlsx';
        link.href = '#'; // In real implementation, would be actual Excel URL
        showNotification('Excel report generated successfully!', 'success');
    }, 1500);
}

function printResults() {
    showNotification('Preparing print view...', 'info');
    
    setTimeout(() => {
        window.print();
    }, 500);
}

// Auto-refresh functionality
function setupAutoRefresh() {
    // Only auto-refresh if election is ongoing
    const status = document.querySelector('.election-status');
    if (status && status.classList.contains('ongoing')) {
        setInterval(() => {
            refreshResults();
        }, 30000); // Refresh every 30 seconds
    }
}

function refreshResults() {
    showNotification('Refreshing results...', 'info');
    
    // In real implementation, would fetch latest results from API
    setTimeout(() => {
        updateLastRefresh();
        showNotification('Results updated!', 'success');
    }, 1000);
}

function updateLastRefresh() {
    // Remove last refresh indicator if it exists
    const refreshIndicator = document.querySelector('.last-refresh');
    if (refreshIndicator) {
        refreshIndicator.remove();
    }
}

// Interactive features
function addInteractiveFeatures() {
    // Add click handlers for candidate results
    document.querySelectorAll('.candidate-result').forEach(result => {
        result.addEventListener('click', function() {
            this.style.transform = 'scale(1.02)';
            setTimeout(() => {
                this.style.transform = '';
            }, 200);
        });
    });
    
    // Add hover effects for info cards
    document.querySelectorAll('.info-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(-2px) scale(1)';
        });
    });
}

// Notification system
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

    // Show notification
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
        notification.style.opacity = '1';
    }, 100);

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

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Refresh with F5 or Ctrl+R
    if (e.key === 'F5' || (e.ctrlKey && e.key === 'r')) {
        e.preventDefault();
        refreshResults();
    }
    
    // Print with Ctrl+P
    if (e.ctrlKey && e.key === 'p') {
        e.preventDefault();
        printResults();
    }
    
    // Export with Ctrl+E
    if (e.ctrlKey && e.key === 'e') {
        e.preventDefault();
        exportToPDF();
    }
});

// Add dynamic styles
const additionalStyles = document.createElement('style');
additionalStyles.textContent = `
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
    
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 20px;
    }
    
    .modal {
        background: white;
        border-radius: 12px;
        max-width: 600px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        animation: modalSlideIn 0.3s ease-out;
    }
    
    .modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8f9fa;
        border-radius: 12px 12px 0 0;
    }
    
    .modal-content {
        padding: 24px;
    }
    
    .verification-status.success,
    .security-status.success {
        text-align: center;
        color: #10b981;
        margin-bottom: 20px;
    }
    
    .verification-status i,
    .security-status i {
        font-size: 48px;
        margin-bottom: 12px;
    }
    
    .blockchain-details {
        background: #f8f9fa;
        padding: 16px;
        border-radius: 8px;
        margin: 20px 0;
    }
    
    .detail-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 14px;
    }
    
    .label {
        font-weight: 600;
        color: #374151;
    }
    
    .value {
        color: #6b7280;
        font-family: monospace;
    }
    
    .audit-log {
        max-height: 300px;
        overflow-y: auto;
    }
    
    .audit-item {
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 12px;
        padding: 8px 0;
        border-bottom: 1px solid #e5e7eb;
        font-size: 14px;
    }
    
    .audit-time {
        color: #6b7280;
        font-family: monospace;
    }
    
    .audit-event {
        color: #374151;
    }
    
    .audit-user {
        color: #6b7280;
        font-style: italic;
    }
    
    .security-checks {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 20px;
    }
    
    .check-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 12px;
        background: #f0f9ff;
        border-radius: 6px;
        color: #10b981;
    }
    
    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: scale(0.9) translateY(-50px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
`;
document.head.appendChild(additionalStyles);
