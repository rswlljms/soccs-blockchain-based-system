// Student Voting JavaScript Functionality
document.addEventListener('DOMContentLoaded', function() {
    initializeVotingPage();
    // Mobile menu is already initialized by student-dashboard.js
});

// Initialize voting page functionality
function initializeVotingPage() {
    updateSubmitButtonState();
    addKeyboardNavigation();
    preloadModalContent();
    setupRadioButtonBehavior();
}

// Handle candidate click to ensure proper radio behavior
function handleCandidateClick(radioElement) {
    // Force the radio button to be checked
    radioElement.checked = true;
    
    // Trigger the selection function
    selectCandidate(radioElement);
}

// Setup proper radio button behavior
function setupRadioButtonBehavior() {
    // Add click listeners to all radio buttons to ensure proper behavior
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('click', function(e) {
            // Ensure this radio is selected and others in same group are not
            const name = this.name;
            document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
                if (r !== this) {
                    r.checked = false;
                }
            });
            this.checked = true;
        });
    });
}

// Handle candidate selection
function selectCandidate(radioElement) {
    const candidateCard = radioElement.closest('.candidate-card');
    const positionSection = radioElement.closest('.position-section');
    
    // Remove selected class from all cards in this position
    positionSection.querySelectorAll('.candidate-card').forEach(card => {
        card.classList.remove('selected');
        // Remove any existing selection feedback
        const existingFeedback = card.querySelector('.selection-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }
    });
    
    // Only proceed if this radio button is actually checked
    if (radioElement.checked) {
        // Add selected class to current card
        candidateCard.classList.add('selected');
        
        // Add selection animation
        candidateCard.style.transform = 'scale(1.02)';
        setTimeout(() => {
            candidateCard.style.transform = '';
        }, 200);
        
        // Show permanent selection feedback
        showPermanentSelectionFeedback(candidateCard);
    }
    
    // Update submit button state
    updateSubmitButtonState();
}

// Reset position selection
function resetPosition(positionName) {
    const positionSection = document.querySelector(`input[name="${positionName}"]`).closest('.position-section');
    
    // Clear all selections in this position
    positionSection.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.checked = false;
    });
    
    // Remove selected visual state and feedback
    positionSection.querySelectorAll('.candidate-card').forEach(card => {
        card.classList.remove('selected');
        // Remove any existing selection feedback
        const existingFeedback = card.querySelector('.selection-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }
    });
    
    // Update submit button state
    updateSubmitButtonState();
    
    // Show reset feedback
    showNotification('Position selection reset', 'info');
}

// Update submit button state based on selections
function updateSubmitButtonState() {
    const submitBtn = document.getElementById('submitVoteBtn');
    const allPositions = document.querySelectorAll('.position-section');
    let hasSelections = false;
    
    allPositions.forEach(section => {
        const radioButtons = section.querySelectorAll('input[type="radio"]');
        const hasSelection = Array.from(radioButtons).some(radio => radio.checked);
        if (hasSelection) {
            hasSelections = true;
        }
    });
    
    submitBtn.disabled = !hasSelections;
    
    if (hasSelections) {
        submitBtn.classList.add('ready');
    } else {
        submitBtn.classList.remove('ready');
    }
}

// Preview vote before submission
function previewVote() {
    const selectedVotes = getSelectedVotes();
    const allPositions = getAllPositions();
    
    if (Object.keys(selectedVotes).length === 0) {
        showNotification('Please select at least one candidate before previewing', 'warning');
        return;
    }
    
    // Populate preview content
    const previewContent = document.getElementById('votePreviewContent');
    previewContent.innerHTML = '';
    
    // Show selected votes
    Object.entries(selectedVotes).forEach(([position, candidate]) => {
        const previewItem = document.createElement('div');
        previewItem.className = 'vote-preview-item';
        previewItem.innerHTML = `
            <div>
                <h4>${position.replace('_', ' ').toUpperCase()}</h4>
                <p>${candidate.name}</p>
            </div>
            <div class="candidate-preview-photo">
                <img src="${candidate.photo}" alt="${candidate.name}" 
                     style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;"
                     onerror="this.src='../assets/img/logo.png'">
            </div>
        `;
        previewContent.appendChild(previewItem);
    });
    
    // Show unvoted positions with disclaimer
    const unvotedPositions = allPositions.filter(pos => !selectedVotes.hasOwnProperty(pos));
    
    if (unvotedPositions.length > 0) {
        const disclaimerSection = document.createElement('div');
        disclaimerSection.className = 'unvoted-positions-disclaimer';
        disclaimerSection.style.cssText = `
            margin-top: 20px;
            padding: 15px;
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            border-radius: 6px;
        `;
        
        let disclaimerHTML = `
            <div style="display: flex; align-items: start; gap: 12px; margin-bottom: 12px;">
                <i class="fas fa-exclamation-triangle" style="color: #f59e0b; font-size: 20px; margin-top: 2px;"></i>
                <div>
                    <h4 style="margin: 0 0 8px 0; color: #92400e; font-size: 15px; font-weight: 600;">
                        You have not voted for the following position${unvotedPositions.length > 1 ? 's' : ''}:
                    </h4>
                    <ul style="margin: 8px 0; padding-left: 20px; color: #78350f;">
        `;
        
        unvotedPositions.forEach(position => {
            disclaimerHTML += `<li style="margin: 4px 0;"><strong>${position.replace('_', ' ').toUpperCase()}</strong></li>`;
        });
        
        disclaimerHTML += `
                    </ul>
                    <p style="margin: 8px 0 0 0; color: #78350f; font-size: 14px;">
                        Are you sure you want to proceed without voting for ${unvotedPositions.length > 1 ? 'these positions' : 'this position'}?
                    </p>
                </div>
            </div>
        `;
        
        disclaimerSection.innerHTML = disclaimerHTML;
        previewContent.appendChild(disclaimerSection);
    }
    
    // Show preview modal
    const modal = document.getElementById('previewModal');
    modal.style.display = 'flex';
    
    // Add modal animation
    const modalContent = modal.querySelector('.modal');
    modalContent.style.transform = 'scale(0.9)';
    modalContent.style.opacity = '0';
    
    setTimeout(() => {
        modalContent.style.transition = 'all 0.3s ease-out';
        modalContent.style.transform = 'scale(1)';
        modalContent.style.opacity = '1';
    }, 50);
}

// Close preview modal
function closePreviewModal() {
    const modal = document.getElementById('previewModal');
    const modalContent = modal.querySelector('.modal');
    
    modalContent.style.transform = 'scale(0.9)';
    modalContent.style.opacity = '0';
    
    setTimeout(() => {
        modal.style.display = 'none';
        modalContent.style.transform = '';
        modalContent.style.opacity = '';
        modalContent.style.transition = '';
    }, 300);
}

// Confirm and submit vote
function confirmVote() {
    closePreviewModal();
    
    // Show loading state
    const submitBtn = document.getElementById('submitVoteBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    submitBtn.disabled = true;
    
    // Simulate blockchain submission (replace with actual API call)
    setTimeout(() => {
        submitVoteToBlockchain();
    }, 2000);
}

// Submit vote to blockchain and database
async function submitVoteToBlockchain() {
    const selectedVotes = getSelectedVotes();
    
    try {
        // Prepare vote data for API
        const voteData = {};
        Object.entries(selectedVotes).forEach(([position, candidate]) => {
            voteData[position] = candidate.id;
        });
        
        // Submit to API
        const response = await fetch('../api/submit_vote.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(voteData)
        });
        
        const result = await response.json();
        
        if (!response.ok || !result.success) {
            throw new Error(result.error || 'Failed to submit vote');
        }
        
        // Show success modal with actual transaction data
        const successData = {
            votes: selectedVotes,
            timestamp: result.data.timestamp,
            transactionHash: result.data.transaction_hash
        };
        
        showSuccessModal(successData);
        
        // Reset form
        document.getElementById('votingForm').reset();
        updateSubmitButtonState();
        
    } catch (error) {
        console.error('Vote submission error:', error);
        showNotification('Failed to submit vote: ' + error.message, 'error');
        
        // Re-enable submit button
        const submitBtn = document.getElementById('submitVoteBtn');
        submitBtn.innerHTML = '<i class="fas fa-vote-yea"></i> Cast Vote';
        submitBtn.disabled = false;
    }
}

// Show success modal
function showSuccessModal(voteData) {
    // Update transaction hash
    document.getElementById('transactionHash').textContent = voteData.transactionHash;
    
    // Populate vote summary
    const summaryContent = document.getElementById('voteSummaryContent');
    summaryContent.innerHTML = '';
    
    Object.entries(voteData.votes).forEach(([position, candidate]) => {
        const summaryItem = document.createElement('div');
        summaryItem.className = 'vote-preview-item';
        summaryItem.innerHTML = `
            <div>
                <h4>${position.replace('_', ' ').toUpperCase()}</h4>
                <p>${candidate.name}</p>
            </div>
            <div style="color: #10b981;">
                <i class="fas fa-check-circle"></i>
            </div>
        `;
        summaryContent.appendChild(summaryItem);
    });
    
    // Show success modal
    const modal = document.getElementById('successModal');
    modal.style.display = 'flex';
    
    // Add celebration animation
    setTimeout(() => {
        createCelebrationEffect();
    }, 500);
}

// Get selected votes
function getSelectedVotes() {
    const selectedVotes = {};
    const allRadios = document.querySelectorAll('input[type="radio"]:checked');
    
    allRadios.forEach(radio => {
        const position = radio.name;
        const candidateCard = radio.closest('.candidate-card');
        const candidateName = candidateCard.querySelector('h4').textContent;
        const candidatePhoto = candidateCard.querySelector('img').src;
        const candidateId = radio.value;
        
        selectedVotes[position] = {
            id: candidateId,
            name: candidateName,
            photo: candidatePhoto
        };
    });
    
    return selectedVotes;
}

// Get all available positions
function getAllPositions() {
    const positions = new Set();
    const allRadios = document.querySelectorAll('input[type="radio"]');
    
    allRadios.forEach(radio => {
        positions.add(radio.name);
    });
    
    return Array.from(positions);
}

// Generate mock transaction hash
function generateTransactionHash() {
    const chars = '0123456789abcdef';
    let hash = '0x';
    for (let i = 0; i < 64; i++) {
        hash += chars[Math.floor(Math.random() * chars.length)];
    }
    return hash;
}

// Show permanent selection feedback
function showPermanentSelectionFeedback(candidateCard) {
    // Remove any existing feedback first
    const existingFeedback = candidateCard.querySelector('.selection-feedback');
    if (existingFeedback) {
        existingFeedback.remove();
    }
    
    const feedback = document.createElement('div');
    feedback.className = 'selection-feedback';
    feedback.innerHTML = '<i class="fas fa-check"></i>';
    feedback.style.cssText = `
        position: absolute;
        top: 10px;
        right: 10px;
        background: #10b981;
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        z-index: 10;
        animation: selectionPop 0.4s ease-out;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    `;
    
    candidateCard.style.position = 'relative';
    candidateCard.appendChild(feedback);
    
    // Don't remove it automatically - it stays until another candidate is selected
}

// Platform modal functions
function viewPlatform(candidateId, name, partylist, position, platform) {
    // Populate platform modal content
    document.getElementById('platformCandidateName').textContent = name;
    document.getElementById('platformCandidatePosition').textContent = position;
    document.getElementById('platformCandidatePartylist').textContent = partylist;
    document.getElementById('platformCandidateText').textContent = platform;
    
    // Show platform modal
    const modal = document.getElementById('platformModal');
    modal.style.display = 'flex';
    
    // Add modal animation
    const modalContent = modal.querySelector('.modal');
    modalContent.style.transform = 'scale(0.9)';
    modalContent.style.opacity = '0';
    
    setTimeout(() => {
        modalContent.style.transition = 'all 0.3s ease-out';
        modalContent.style.transform = 'scale(1)';
        modalContent.style.opacity = '1';
    }, 50);
}

function closePlatformModal() {
    const modal = document.getElementById('platformModal');
    const modalContent = modal.querySelector('.modal');
    
    modalContent.style.transform = 'scale(0.9)';
    modalContent.style.opacity = '0';
    
    setTimeout(() => {
        modal.style.display = 'none';
        modalContent.style.transform = '';
        modalContent.style.opacity = '';
        modalContent.style.transition = '';
    }, 300);
}

// Navigation functions
function goToDashboard() {
    window.location.href = '../pages/student-dashboard.php';
}

function viewVotingHistory() {
    window.location.href = '../pages/student-voting-history.php';
}

// Create celebration effect
function createCelebrationEffect() {
    const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#feca57'];
    const confettiCount = 50;
    
    for (let i = 0; i < confettiCount; i++) {
        setTimeout(() => {
            createConfetti(colors[Math.floor(Math.random() * colors.length)]);
        }, i * 50);
    }
}

function createConfetti(color) {
    const confetti = document.createElement('div');
    confetti.style.cssText = `
        position: fixed;
        width: 10px;
        height: 10px;
        background: ${color};
        left: ${Math.random() * 100}vw;
        top: -10px;
        z-index: 10000;
        pointer-events: none;
        border-radius: 50%;
    `;
    
    document.body.appendChild(confetti);
    
    const fallAnimation = confetti.animate([
        { transform: 'translateY(0) rotate(0deg)', opacity: 1 },
        { transform: `translateY(${window.innerHeight + 10}px) rotate(360deg)`, opacity: 0 }
    ], {
        duration: Math.random() * 3000 + 2000,
        easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)'
    });
    
    fallAnimation.onfinish = () => confetti.remove();
}

// Keyboard navigation
function addKeyboardNavigation() {
    document.addEventListener('keydown', function(e) {
        // Close modals with Escape
        if (e.key === 'Escape') {
            const previewModal = document.getElementById('previewModal');
            const successModal = document.getElementById('successModal');
            const platformModal = document.getElementById('platformModal');
            
            if (previewModal.style.display === 'flex') {
                closePreviewModal();
            } else if (successModal.style.display === 'flex') {
                successModal.style.display = 'none';
            } else if (platformModal.style.display === 'flex') {
                closePlatformModal();
            }
        }
        
        // Submit with Ctrl+Enter
        if (e.ctrlKey && e.key === 'Enter') {
            const submitBtn = document.getElementById('submitVoteBtn');
            if (!submitBtn.disabled) {
                previewVote();
            }
        }
        
        // Navigate candidates with arrow keys
        if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
            navigateCandidates(e.key === 'ArrowDown' ? 1 : -1);
            e.preventDefault();
        }
    });
}

function navigateCandidates(direction) {
    const allCards = document.querySelectorAll('.candidate-card');
    const focusedCard = document.querySelector('.candidate-card:focus-within') || 
                       document.querySelector('.candidate-card.selected');
    
    if (!focusedCard) {
        allCards[0]?.querySelector('input[type="radio"]').focus();
        return;
    }
    
    const currentIndex = Array.from(allCards).indexOf(focusedCard);
    const nextIndex = currentIndex + direction;
    
    if (nextIndex >= 0 && nextIndex < allCards.length) {
        allCards[nextIndex].querySelector('input[type="radio"]').focus();
    }
}

// Handle form submission
document.getElementById('votingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    previewVote();
});

// Notification system (reuse from dashboard)
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

// Preload modal content for better performance
function preloadModalContent() {
    const previewModal = document.getElementById('previewModal');
    const successModal = document.getElementById('successModal');
    
    // Preload modal styles
    if (previewModal) {
        previewModal.style.display = 'none';
    }
    if (successModal) {
        successModal.style.display = 'none';
    }
}

// Add loading states
function showLoadingState(element, loadingText = 'Loading...') {
    const originalContent = element.innerHTML;
    element.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${loadingText}`;
    element.disabled = true;
    
    return () => {
        element.innerHTML = originalContent;
        element.disabled = false;
    };
}

// Intersection Observer for animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.animationPlayState = 'running';
        }
    });
}, observerOptions);

// Observe position sections for animations
document.querySelectorAll('.position-section').forEach(section => {
    section.style.animationPlayState = 'paused';
    observer.observe(section);
});

// Additional CSS animations
const additionalStyles = document.createElement('style');
additionalStyles.textContent = `
    @keyframes selectionPop {
        0% { transform: scale(0); opacity: 0; }
        50% { transform: scale(1.2); opacity: 1; }
        100% { transform: scale(1); opacity: 1; }
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
    
    .btn-submit.ready {
        animation: readyPulse 2s infinite;
    }
    
    @keyframes readyPulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
        50% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
    }
`;
document.head.appendChild(additionalStyles);
