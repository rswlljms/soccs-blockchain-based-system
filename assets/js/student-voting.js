// Student Voting JavaScript Functionality
document.addEventListener('DOMContentLoaded', function() {
    initializeVotingPage();
});

function initializeVotingPage() {
    updateSubmitButtonState();
    addKeyboardNavigation();
    preloadModalContent();
    setupInputBehavior();
}

function handleCandidateClick(inputElement) {
    const maxVotes = parseInt(inputElement.dataset.maxVotes) || 1;
    
    if (inputElement.type === 'checkbox') {
        const position = inputElement.dataset.position;
        const checkedCount = document.querySelectorAll(`input[data-position="${position}"]:checked`).length;
        
        if (inputElement.checked && checkedCount > maxVotes) {
            inputElement.checked = false;
            showNotification(`You can only select up to ${maxVotes} candidate${maxVotes > 1 ? 's' : ''} for this position`, 'warning');
            return;
        }
    } else {
        inputElement.checked = true;
    }
    
    selectCandidate(inputElement);
}

function setupInputBehavior() {
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('click', function(e) {
            const name = this.name;
            document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
                if (r !== this) r.checked = false;
            });
            this.checked = true;
        });
    });

    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function(e) {
            const maxVotes = parseInt(this.dataset.maxVotes) || 1;
            const position = this.dataset.position;
            const checkedCount = document.querySelectorAll(`input[data-position="${position}"]:checked`).length;
            
            if (this.checked && checkedCount > maxVotes) {
                this.checked = false;
                e.preventDefault();
                showNotification(`You can only select up to ${maxVotes} candidate${maxVotes > 1 ? 's' : ''} for this position`, 'warning');
            }
        });
    });
}

function selectCandidate(inputElement) {
    const candidateCard = inputElement.closest('.candidate-card');
    const positionSection = inputElement.closest('.position-section');
    const isCheckbox = inputElement.type === 'checkbox';
    const maxVotes = parseInt(positionSection.dataset.maxVotes) || 1;
    
    if (isCheckbox) {
        positionSection.querySelectorAll('.candidate-card').forEach(card => {
            const input = card.querySelector('input[type="checkbox"]');
            if (input && input.checked) {
                card.classList.add('selected');
                showPermanentSelectionFeedback(card);
            } else {
                card.classList.remove('selected');
                const existingFeedback = card.querySelector('.selection-feedback');
                if (existingFeedback) existingFeedback.remove();
            }
        });
        
        // Check if max votes reached and add/remove class
        const checkedCount = positionSection.querySelectorAll('input[type="checkbox"]:checked').length;
        if (checkedCount >= maxVotes) {
            positionSection.classList.add('max-reached');
        } else {
            positionSection.classList.remove('max-reached');
        }
    } else {
        positionSection.querySelectorAll('.candidate-card').forEach(card => {
            card.classList.remove('selected');
            const existingFeedback = card.querySelector('.selection-feedback');
            if (existingFeedback) existingFeedback.remove();
        });
        
        if (inputElement.checked) {
            candidateCard.classList.add('selected');
            candidateCard.style.transform = 'scale(1.02)';
            setTimeout(() => { candidateCard.style.transform = ''; }, 200);
            showPermanentSelectionFeedback(candidateCard);
            // For radio buttons, max is always 1 and always reached when selected
            positionSection.classList.add('max-reached');
        } else {
            positionSection.classList.remove('max-reached');
        }
    }
    
    updateSubmitButtonState();
}

function resetPosition(positionName) {
    const inputs = document.querySelectorAll(`input[name="${positionName}"], input[name="${positionName}[]"]`);
    if (inputs.length === 0) return;
    
    const positionSection = inputs[0].closest('.position-section');
    
    positionSection.querySelectorAll('input[type="radio"], input[type="checkbox"]').forEach(input => {
        input.checked = false;
    });
    
    positionSection.querySelectorAll('.candidate-card').forEach(card => {
        card.classList.remove('selected');
        const existingFeedback = card.querySelector('.selection-feedback');
        if (existingFeedback) existingFeedback.remove();
    });
    
    positionSection.classList.remove('max-reached');
    
    updateSubmitButtonState();
    showNotification('Position selection reset', 'info');
}

function updateSubmitButtonState() {
    const submitBtn = document.getElementById('submitVoteBtn');
    if (!submitBtn) return;
    
    const allPositions = document.querySelectorAll('.position-section');
    let hasSelections = false;
    
    allPositions.forEach(section => {
        const inputs = section.querySelectorAll('input[type="radio"], input[type="checkbox"]');
        const hasSelection = Array.from(inputs).some(input => input.checked);
        if (hasSelection) hasSelections = true;
    });
    
    submitBtn.disabled = !hasSelections;
    submitBtn.classList.toggle('ready', hasSelections);
}

function previewVote() {
    const selectedVotes = getSelectedVotes();
    const allPositions = getAllPositions();
    
    if (Object.keys(selectedVotes).length === 0) {
        showNotification('Please select at least one candidate before previewing', 'warning');
        return;
    }
    
    const previewContent = document.getElementById('votePreviewContent');
    previewContent.innerHTML = '';
    
    Object.entries(selectedVotes).forEach(([position, candidates]) => {
        const previewItem = document.createElement('div');
        previewItem.className = 'vote-preview-item';
        
        if (Array.isArray(candidates)) {
            const candidateNames = candidates.map(c => c.name).join(', ');
            previewItem.innerHTML = `
                <div>
                    <h4>${position.replace(/_/g, ' ').toUpperCase()}</h4>
                    <p>${candidateNames}</p>
                </div>
                <div class="candidate-preview-photos">
                    ${candidates.map(c => `<img src="${c.photo}" alt="${c.name}" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover; margin-left: -8px; border: 2px solid white;" onerror="this.src='../assets/img/logo.png'">`).join('')}
                </div>
            `;
        } else {
            previewItem.innerHTML = `
                <div>
                    <h4>${position.replace(/_/g, ' ').toUpperCase()}</h4>
                    <p>${candidates.name}</p>
                </div>
                <div class="candidate-preview-photo">
                    <img src="${candidates.photo}" alt="${candidates.name}" 
                         style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;"
                         onerror="this.src='../assets/img/logo.png'">
                </div>
            `;
        }
        previewContent.appendChild(previewItem);
    });
    
    const unvotedPositions = allPositions.filter(pos => !selectedVotes.hasOwnProperty(pos));
    
    if (unvotedPositions.length > 0) {
        const disclaimerSection = document.createElement('div');
        disclaimerSection.className = 'unvoted-positions-disclaimer';
        disclaimerSection.style.cssText = `margin-top: 20px; padding: 15px; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 6px;`;
        
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
            disclaimerHTML += `<li style="margin: 4px 0;"><strong>${position.replace(/_/g, ' ').toUpperCase()}</strong></li>`;
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
    
    const modal = document.getElementById('previewModal');
    modal.style.display = 'flex';
    
    const modalContent = modal.querySelector('.modal');
    modalContent.style.transform = 'scale(0.9)';
    modalContent.style.opacity = '0';
    
    setTimeout(() => {
        modalContent.style.transition = 'all 0.3s ease-out';
        modalContent.style.transform = 'scale(1)';
        modalContent.style.opacity = '1';
    }, 50);
}

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

let isSubmitting = false;

function confirmVote() {
    if (isSubmitting) return;
    
    closePreviewModal();
    submitVoteToBlockchain();
}

async function submitVoteToBlockchain() {
    if (isSubmitting) return;
    
    const submitBtn = document.getElementById('submitVoteBtn');
    if (!submitBtn) return;
    
    isSubmitting = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    submitBtn.disabled = true;
    
    const selectedVotes = getSelectedVotes();
    
    try {
        const voteData = {};
        Object.entries(selectedVotes).forEach(([position, candidates]) => {
            if (Array.isArray(candidates)) {
                candidates.forEach((candidate, index) => {
                    voteData[`${position}_${index}`] = candidate.id;
                });
            } else {
                voteData[position] = candidates.id;
            }
        });
        
        const response = await fetch('../api/submit_vote.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(voteData)
        });
        
        const result = await response.json();
        
        if (!response.ok || !result.success) {
            throw new Error(result.error || 'Failed to submit vote');
        }
        
        const successData = {
            votes: selectedVotes,
            timestamp: result.data.timestamp,
            transactionHash: result.data.transaction_hash || 'Processing...'
        };
        
        showSuccessModal(successData);
        document.getElementById('votingForm').reset();
        updateSubmitButtonState();
        
    } catch (error) {
        console.error('Vote submission error:', error);
        showNotification('Failed to submit vote: ' + error.message, 'error');
        
        submitBtn.innerHTML = '<i class="fas fa-vote-yea"></i> Cast Vote';
        submitBtn.disabled = false;
        isSubmitting = false;
    } finally {
        setTimeout(() => {
            isSubmitting = false;
        }, 1000);
    }
}

function showSuccessModal(voteData) {
    document.getElementById('transactionHash').textContent = voteData.transactionHash;
    
    const summaryContent = document.getElementById('voteSummaryContent');
    summaryContent.innerHTML = '';
    
    Object.entries(voteData.votes).forEach(([position, candidates]) => {
        const summaryItem = document.createElement('div');
        summaryItem.className = 'vote-preview-item';
        
        if (Array.isArray(candidates)) {
            const candidateNames = candidates.map(c => c.name).join(', ');
            summaryItem.innerHTML = `
                <div>
                    <h4>${position.replace(/_/g, ' ').toUpperCase()}</h4>
                    <p>${candidateNames}</p>
                </div>
                <div style="color: #10b981;"><i class="fas fa-check-circle"></i></div>
            `;
        } else {
            summaryItem.innerHTML = `
                <div>
                    <h4>${position.replace(/_/g, ' ').toUpperCase()}</h4>
                    <p>${candidates.name}</p>
                </div>
                <div style="color: #10b981;"><i class="fas fa-check-circle"></i></div>
            `;
        }
        summaryContent.appendChild(summaryItem);
    });
    
    const modal = document.getElementById('successModal');
    modal.style.display = 'flex';
    
    setTimeout(() => { createCelebrationEffect(); }, 500);
}

function getSelectedVotes() {
    const selectedVotes = {};
    const positions = new Set();
    
    document.querySelectorAll('input[type="radio"]:checked, input[type="checkbox"]:checked').forEach(input => {
        const positionKey = input.dataset.position || input.name.replace('[]', '');
        positions.add(positionKey);
    });
    
    positions.forEach(position => {
        const checkedInputs = document.querySelectorAll(`input[data-position="${position}"]:checked`);
        
        if (checkedInputs.length === 0) return;
        
        if (checkedInputs.length === 1) {
            const input = checkedInputs[0];
            const candidateCard = input.closest('.candidate-card');
            selectedVotes[position] = {
                id: input.value,
                name: candidateCard.querySelector('h4').textContent,
                photo: candidateCard.querySelector('img').src
            };
        } else {
            selectedVotes[position] = [];
            checkedInputs.forEach(input => {
                const candidateCard = input.closest('.candidate-card');
                selectedVotes[position].push({
                    id: input.value,
                    name: candidateCard.querySelector('h4').textContent,
                    photo: candidateCard.querySelector('img').src
                });
            });
        }
    });
    
    return selectedVotes;
}

function getAllPositions() {
    const positions = new Set();
    document.querySelectorAll('.position-section').forEach(section => {
        const position = section.dataset.position;
        if (position) positions.add(position);
    });
    return Array.from(positions);
}

function showPermanentSelectionFeedback(candidateCard) {
    const existingFeedback = candidateCard.querySelector('.selection-feedback');
    if (existingFeedback) existingFeedback.remove();
    
    const feedback = document.createElement('div');
    feedback.className = 'selection-feedback';
    feedback.innerHTML = '<i class="fas fa-check"></i>';
    feedback.style.cssText = `
        position: absolute; top: 10px; right: 10px;
        background: #10b981; color: white;
        width: 30px; height: 30px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; z-index: 10;
        animation: selectionPop 0.4s ease-out;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    `;
    
    candidateCard.style.position = 'relative';
    candidateCard.appendChild(feedback);
}

function viewPlatform(candidateId, name, partylist, position, platform) {
    document.getElementById('platformCandidateName').textContent = name;
    document.getElementById('platformCandidatePosition').textContent = position;
    document.getElementById('platformCandidatePartylist').textContent = partylist;
    document.getElementById('platformCandidateText').textContent = platform;
    
    const modal = document.getElementById('platformModal');
    modal.style.display = 'flex';
    
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

function goToDashboard() {
    window.location.href = '../pages/student-dashboard.php';
}

function viewVotingHistory() {
    window.location.href = '../pages/student-voting-history.php';
}

function createCelebrationEffect() {
    const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#feca57'];
    for (let i = 0; i < 50; i++) {
        setTimeout(() => createConfetti(colors[Math.floor(Math.random() * colors.length)]), i * 50);
    }
}

function createConfetti(color) {
    const confetti = document.createElement('div');
    confetti.style.cssText = `
        position: fixed; width: 10px; height: 10px;
        background: ${color}; left: ${Math.random() * 100}vw;
        top: -10px; z-index: 10000; pointer-events: none; border-radius: 50%;
    `;
    
    document.body.appendChild(confetti);
    
    const fallAnimation = confetti.animate([
        { transform: 'translateY(0) rotate(0deg)', opacity: 1 },
        { transform: `translateY(${window.innerHeight + 10}px) rotate(360deg)`, opacity: 0 }
    ], { duration: Math.random() * 3000 + 2000, easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)' });
    
    fallAnimation.onfinish = () => confetti.remove();
}

function addKeyboardNavigation() {
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const previewModal = document.getElementById('previewModal');
            const successModal = document.getElementById('successModal');
            const platformModal = document.getElementById('platformModal');
            
            if (previewModal && previewModal.style.display === 'flex') closePreviewModal();
            else if (successModal && successModal.style.display === 'flex') successModal.style.display = 'none';
            else if (platformModal && platformModal.style.display === 'flex') closePlatformModal();
        }
        
        if (e.ctrlKey && e.key === 'Enter') {
            const submitBtn = document.getElementById('submitVoteBtn');
            if (submitBtn && !submitBtn.disabled) previewVote();
        }
    });
}

const votingForm = document.getElementById('votingForm');
if (votingForm) {
    votingForm.addEventListener('submit', function(e) {
        e.preventDefault();
        previewVote();
    });
}

function showNotification(message, type = 'info') {
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());

    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    
    const icons = { 'info': 'fa-info-circle', 'success': 'fa-check-circle', 'warning': 'fa-exclamation-triangle', 'error': 'fa-times-circle' };
    
    notification.innerHTML = `
        <i class="fas ${icons[type] || icons.info}"></i>
        <span>${message}</span>
        <button class="notification-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
        notification.style.opacity = '1';
    }, 100);

    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

function preloadModalContent() {
    const previewModal = document.getElementById('previewModal');
    const successModal = document.getElementById('successModal');
    if (previewModal) previewModal.style.display = 'none';
    if (successModal) successModal.style.display = 'none';
}

const additionalStyles = document.createElement('style');
additionalStyles.textContent = `
    @keyframes selectionPop {
        0% { transform: scale(0); opacity: 0; }
        50% { transform: scale(1.2); opacity: 1; }
        100% { transform: scale(1); opacity: 1; }
    }
    
    .notification {
        position: fixed; top: 20px; right: 20px;
        background: white; border-radius: 8px;
        padding: 16px 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: flex; align-items: center; gap: 12px;
        z-index: 1001; transform: translateX(100%);
        opacity: 0; transition: all 0.3s ease-out; max-width: 300px;
    }
    
    .notification.notification-info { border-left: 4px solid #3b82f6; }
    .notification.notification-success { border-left: 4px solid #10b981; }
    .notification.notification-warning { border-left: 4px solid #f59e0b; }
    .notification.notification-error { border-left: 4px solid #ef4444; }
    
    .notification-close {
        background: none; border: none; cursor: pointer;
        padding: 4px; color: #6b7280; margin-left: auto;
    }
    .notification-close:hover { color: #374151; }
    
    .btn-submit.ready { animation: readyPulse 2s infinite; }
    
    @keyframes readyPulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
        50% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
    }
`;
document.head.appendChild(additionalStyles);
