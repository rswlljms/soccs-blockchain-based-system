// Function to convert text to title case (capitalize each word)
function toTitleCase(text) {
    if (!text) return text;
    return text.toLowerCase().split(' ').map(word => {
        // Capitalize first letter of each word
        return word.charAt(0).toUpperCase() + word.slice(1);
    }).join(' ');
}

// Add event listeners to text inputs
document.addEventListener('DOMContentLoaded', function () {
    // Get all text input fields
    const textInputs = document.querySelectorAll('input[type="text"]');

    // Add input event listener to each text field
    textInputs.forEach(input => {
        input.addEventListener('input', function (e) {
            // Get cursor position before changing the value
            const cursorPosition = e.target.selectionStart;

            // Convert to title case
            const newValue = toTitleCase(e.target.value);

            // Only update if the value would actually change
            if (newValue !== e.target.value) {
                e.target.value = newValue;

                // Restore cursor position
                e.target.setSelectionRange(cursorPosition, cursorPosition);
            }
        });
    });
});

let currentPage = 1;
const limit = 6;

async function loadFunds(page = 1) {
    try {
        const requestedPage = page;
        const filterDate = document.getElementById('filter-date').value;

        const url = new URL('../api/get_funds.php', window.location.href);
        url.searchParams.set('page', requestedPage);
        url.searchParams.set('limit', limit);
        if (filterDate !== 'All') {
            url.searchParams.set('date_filter', filterDate);
        }

        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        if (!result.success) {
            throw new Error(result.message || 'Failed to load funds');
        }

        const funds = result.data;
        const total = result.total;
        const totalPages = Math.ceil(total / limit);

        // Update current page
        currentPage = Math.min(requestedPage, totalPages);
        if (currentPage < 1) currentPage = 1;

        const tableBody = document.getElementById('funds-table-body');
        tableBody.innerHTML = '';

        if (!funds || funds.length === 0) {
            const emptyRow = document.createElement('tr');
            emptyRow.innerHTML = `
                <td colspan="3" style="text-align: center; padding: 20px;">
                    No funds recorded yet
                </td>
            `;
            tableBody.appendChild(emptyRow);

            // Reset pagination
            document.querySelector('.page-indicator').textContent = 'Page 1 of 1';
            document.querySelector('.prev-btn').classList.add('disabled');
            document.querySelector('.next-btn').classList.add('disabled');
            return;
        }

        funds.forEach(fund => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="amount-cell">₱${parseFloat(fund.amount).toFixed(2)}</td>
                <td>${fund.description}</td>
                <td>${fund.date}</td>
            `;
            tableBody.appendChild(row);
        });

        // Update pagination
        document.querySelector('.page-indicator').textContent = `Page ${currentPage} of ${totalPages}`;

        // Update pagination buttons
        const prevBtn = document.querySelector('.prev-btn');
        const nextBtn = document.querySelector('.next-btn');

        prevBtn.classList.toggle('disabled', currentPage <= 1);
        nextBtn.classList.toggle('disabled', currentPage >= totalPages);

        // Update button functionality
        prevBtn.onclick = (e) => {
            e.preventDefault();
            if (currentPage > 1) loadFunds(currentPage - 1);
        };

        nextBtn.onclick = (e) => {
            e.preventDefault();
            if (currentPage < totalPages) loadFunds(currentPage + 1);
        };

    } catch (err) {
        console.error('Error loading funds:', err);
        alert('Error loading funds: ' + err.message);
    }
}

// Form submission handler
document.getElementById('fund-form').addEventListener('submit', async function (event) {
    event.preventDefault();

    try {
        const formData = new FormData(this);
        const fundData = {
            amount: formData.get('amount'),
            description: formData.get('description'),
            date: formData.get('date') || new Date().toISOString().split('T')[0],
            method: 'FUND'
        };

        // Validate required fields
        if (!fundData.amount || !fundData.description) {
            alert('Please fill in all required fields');
            return;
        }

        hideModal('fundModal');
        showModal('confirmModal');

        const confirmBtn = document.getElementById('confirmFund');
        const handleConfirm = async () => {
            try {
                confirmBtn.removeEventListener('click', handleConfirm);

                hideModal('confirmModal');
                showModal('loadingModal');

                // Send to blockchain
                const blockchainResponse = await fetch('http://localhost:3001/add-funds', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(fundData)
                });

                const blockchainResult = await blockchainResponse.json();

                if (blockchainResult.status === 'success') {
                    // Prepare form data for database
                    const dbData = new FormData();

                    // Add all fund data
                    Object.keys(fundData).forEach(key => {
                        dbData.append(key, fundData[key]);
                    });

                    // Add transaction hash
                    dbData.append('transaction_hash', blockchainResult.txHash);

                    const dbResponse = await fetch('../api/save_fund.php', {
                        method: 'POST',
                        body: dbData
                    });

                    if (!dbResponse.ok) {
                        throw new Error(`HTTP error! status: ${dbResponse.status}`);
                    }

                    const dbResult = await dbResponse.json();

                    if (dbResult.success) {
                        hideModal('loadingModal');
                        hideModal('fundModal');
                        document.getElementById('txHash').textContent = blockchainResult.txHash;
                        showModal('successModal');
                        this.reset();
                        await loadFunds();
                    } else {
                        throw new Error(dbResult.message || 'Failed to save to database');
                    }
                } else {
                    throw new Error(blockchainResult.message || 'Blockchain transaction failed');
                }
            } catch (err) {
                console.error('Error in confirmation handler:', err);
                hideModal('loadingModal');
                hideModal('confirmModal');
                alert('Error: ' + err.message);
            }
        };

        // Add event listener for confirmation
        confirmBtn.addEventListener('click', handleConfirm, { once: true });

        document.getElementById('cancelFund').onclick = () => {
            hideModal('confirmModal');
            showModal('fundModal');
            confirmBtn.removeEventListener('click', handleConfirm);
        };
    } catch (err) {
        console.error('Error in form submission:', err);
        alert('Error: ' + err.message);
    }
});

function hideAllModals() {
    const allModals = ['fundModal', 'confirmModal', 'loadingModal', 'successModal'];
    allModals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        const overlay = document.getElementById(modalId.replace('Modal', 'Overlay'));
        if (modal) modal.classList.remove('show');
        if (overlay) overlay.classList.remove('show');
    });
}

function showModal(modalId) {
    hideAllModals();
    const modal = document.getElementById(modalId);
    const overlay = document.getElementById(modalId.replace('Modal', 'Overlay'));
    if (modal && overlay) {
        setTimeout(() => {
            modal.classList.add('show');
            overlay.classList.add('show');
        }, 10);
    }
}

function hideModal(modalId) {
    const modal = document.getElementById(modalId);
    const overlay = document.getElementById(modalId.replace('Modal', 'Overlay'));
    if (modal && overlay) {
        modal.classList.remove('show');
        overlay.classList.remove('show');
    }
}

document.getElementById('successOk').onclick = () => {
    hideModal('successModal');
    hideModal('fundModal');
};

// Close (X) buttons for consistency
document.addEventListener('DOMContentLoaded', () => {
    const closeConfirm = document.getElementById('closeConfirm');
    if (closeConfirm) closeConfirm.addEventListener('click', () => hideModal('confirmModal'));
    const closeSuccess = document.getElementById('closeSuccess');
    if (closeSuccess) closeSuccess.addEventListener('click', () => hideModal('successModal'));
    const confirmOverlay = document.getElementById('confirmOverlay');
    if (confirmOverlay) confirmOverlay.addEventListener('click', () => hideModal('confirmModal'));
    const successOverlay = document.getElementById('successOverlay');
    if (successOverlay) successOverlay.addEventListener('click', () => hideModal('successModal'));
});

// Filter handling
document.getElementById('filter-date').addEventListener('change', function () {
    currentPage = 1; // Reset to first page when filtering
    loadFunds(currentPage);
});

// Initial load
document.addEventListener('DOMContentLoaded', () => loadFunds(currentPage)); 