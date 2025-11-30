let currentPage = 1;
let totalPages = 1;
let searchQuery = '';
let activityTypeFilter = '';
let moduleFilter = '';
let dateFilter = 'All';

const elements = {
    logsTableBody: document.getElementById('logsTableBody'),
    searchLogs: document.getElementById('searchLogs'),
    filterActivityType: document.getElementById('filterActivityType'),
    filterModule: document.getElementById('filterModule'),
    filterDate: document.getElementById('filterDate'),
    clearFilters: document.getElementById('clearFilters'),
    prevPage: document.getElementById('prevPage'),
    nextPage: document.getElementById('nextPage'),
    pageIndicator: document.getElementById('pageIndicator')
};

function init() {
    bindEvents();
    loadActivityLogs();
}

function bindEvents() {
    elements.searchLogs.addEventListener('input', debounce(() => {
        searchQuery = elements.searchLogs.value.trim();
        currentPage = 1;
        loadActivityLogs();
    }, 500));

    elements.filterActivityType.addEventListener('change', () => {
        activityTypeFilter = elements.filterActivityType.value;
        currentPage = 1;
        loadActivityLogs();
    });

    elements.filterModule.addEventListener('change', () => {
        moduleFilter = elements.filterModule.value;
        currentPage = 1;
        loadActivityLogs();
    });

    elements.filterDate.addEventListener('change', () => {
        dateFilter = elements.filterDate.value;
        currentPage = 1;
        loadActivityLogs();
    });

    elements.clearFilters.addEventListener('click', () => {
        elements.searchLogs.value = '';
        elements.filterActivityType.value = '';
        elements.filterModule.value = '';
        elements.filterDate.value = 'All';
        searchQuery = '';
        activityTypeFilter = '';
        moduleFilter = '';
        dateFilter = 'All';
        currentPage = 1;
        loadActivityLogs();
    });

    elements.prevPage.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            loadActivityLogs();
        }
    });

    elements.nextPage.addEventListener('click', () => {
        if (currentPage < totalPages) {
            currentPage++;
            loadActivityLogs();
        }
    });
}

async function loadActivityLogs() {
    elements.logsTableBody.innerHTML = `
        <tr>
            <td colspan="5">
                <div class="loading-spinner">
                    <div class="spinner"></div>
                </div>
            </td>
        </tr>
    `;

    const params = new URLSearchParams({
        page: currentPage,
        limit: 20
    });

    if (searchQuery) {
        params.append('search', searchQuery);
    }

    if (activityTypeFilter) {
        params.append('activity_type', activityTypeFilter);
    }

    if (moduleFilter) {
        params.append('module', moduleFilter);
    }

    if (dateFilter && dateFilter !== 'All') {
        params.append('date_filter', dateFilter);
    }

    try {
        const response = await fetch(`../api/activity-logs/read.php?${params}`);
        const data = await response.json();

        if (data.success) {
            renderLogs(data.data);
            updatePagination(data.pagination);
            updateFilters(data.filters);
        } else {
            showError(data.error || 'Failed to load activity logs');
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Failed to load activity logs');
    }
}

function renderLogs(logs) {
    if (logs.length === 0) {
        elements.logsTableBody.innerHTML = `
            <tr>
                <td colspan="5">
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>No activity logs found</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }

    elements.logsTableBody.innerHTML = logs.map(log => `
        <tr>
            <td>
                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <span style="font-weight: 500;">${formatDate(log.created_at)}</span>
                    <span style="font-size: 12px; color: var(--text-secondary);">${formatTime(log.created_at)}</span>
                </div>
            </td>
            <td>
                <div class="user-info">
                    <span class="user-name">${escapeHtml(log.first_name)} ${escapeHtml(log.last_name)}</span>
                    <span class="user-email">${escapeHtml(log.email)}</span>
                    <span class="user-role ${log.role}">${escapeHtml(log.role)}</span>
                </div>
            </td>
            <td>
                <span class="activity-type ${getActivityTypeClass(log.activity_type)}">${escapeHtml(log.activity_type)}</span>
            </td>
            <td>${escapeHtml(log.activity_description)}</td>
            <td>
                ${log.module ? `<span class="module-badge">${escapeHtml(log.module)}</span>` : '<span style="color: var(--text-secondary);">-</span>'}
            </td>
        </tr>
    `).join('');
}

function updatePagination(pagination) {
    currentPage = pagination.current_page;
    totalPages = pagination.total_pages;

    elements.pageIndicator.textContent = `Page ${currentPage} of ${totalPages}`;
    elements.prevPage.disabled = currentPage <= 1;
    elements.nextPage.disabled = currentPage >= totalPages;
}

function updateFilters(filters) {
    if (filters && filters.activity_types) {
        const activityTypeSelect = elements.filterActivityType;
        const currentValue = activityTypeSelect.value;
        const activityTypeContainer = activityTypeSelect.closest('.toolbar') || activityTypeSelect.parentElement;
        
        activityTypeSelect.innerHTML = '<option value="">All Activity Types</option>';
        
        if (filters.activity_types.length > 0) {
            filters.activity_types.forEach(type => {
                const option = document.createElement('option');
                option.value = type;
                option.textContent = type;
                if (type === currentValue) {
                    option.selected = true;
                }
                activityTypeSelect.appendChild(option);
            });
            activityTypeSelect.style.display = '';
            activityTypeSelect.disabled = false;
            activityTypeSelect.title = 'Filter by activity type';
        } else {
            activityTypeSelect.style.display = 'none';
            activityTypeSelect.disabled = true;
            activityTypeSelect.title = 'No activity types available';
        }
    }

    if (filters && filters.modules) {
        const moduleSelect = elements.filterModule;
        const currentValue = moduleSelect.value;
        const moduleContainer = moduleSelect.closest('.toolbar') || moduleSelect.parentElement;
        
        moduleSelect.innerHTML = '<option value="">All Modules</option>';
        
        if (filters.modules.length > 0) {
            filters.modules.forEach(module => {
                const option = document.createElement('option');
                option.value = module;
                option.textContent = module;
                if (module === currentValue) {
                    option.selected = true;
                }
                moduleSelect.appendChild(option);
            });
            moduleSelect.style.display = '';
            moduleSelect.disabled = false;
            moduleSelect.title = 'Filter by module';
        } else {
            moduleSelect.style.display = 'none';
            moduleSelect.disabled = true;
            moduleSelect.title = 'No modules available';
        }
    }
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function formatTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
}

function getActivityTypeClass(activityType) {
    const type = activityType.toLowerCase();
    
    if (type.includes('created') || type.includes('register') || type.includes('add')) return 'create';
    if (type.includes('updated') || type.includes('edit') || type.includes('modify') || type.includes('change')) return 'update';
    if (type.includes('deleted') || type.includes('remove') || type.includes('deactivate')) return 'delete';
    if (type.includes('viewed') || type.includes('read') || type.includes('access')) return 'view';
    if (type.includes('login')) return 'login';
    if (type.includes('logout')) return 'logout';
    if (type.includes('approved') || type.includes('reactivate') || type.includes('start')) return 'approve';
    if (type.includes('rejected') || type.includes('archive') || type.includes('end')) return 'reject';
    if (type.includes('generated') || type.includes('export')) return 'generate';
    
    return '';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showError(message) {
    elements.logsTableBody.innerHTML = `
        <tr>
            <td colspan="5">
                <div class="empty-state">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>${escapeHtml(message)}</p>
                </div>
            </td>
        </tr>
    `;
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

init();

