document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let totalPages = 1;
    let showInactive = false;
    let searchQuery = '';
    let roleFilter = 'all';
    let allPermissions = [];
    let isEditMode = false;

    const elements = {
        usersTableBody: document.getElementById('usersTableBody'),
        searchUser: document.getElementById('searchUser'),
        filterRole: document.getElementById('filterRole'),
        toggleInactive: document.getElementById('toggleInactive'),
        addUserBtn: document.getElementById('addUserBtn'),
        prevPage: document.getElementById('prevPage'),
        nextPage: document.getElementById('nextPage'),
        pageIndicator: document.getElementById('pageIndicator'),
        
        userModal: document.getElementById('userModal'),
        userModalOverlay: document.getElementById('userModalOverlay'),
        userModalTitle: document.getElementById('userModalTitle'),
        userForm: document.getElementById('userForm'),
        closeUserModal: document.getElementById('closeUserModal'),
        cancelUserModal: document.getElementById('cancelUserModal'),
        saveUserBtn: document.getElementById('saveUserBtn'),
        userId: document.getElementById('userId'),
        passwordRequired: document.getElementById('passwordRequired'),
        passwordHint: document.getElementById('passwordHint'),
        statusGroup: document.getElementById('statusGroup'),
        
        permissionsModal: document.getElementById('permissionsModal'),
        permissionsModalOverlay: document.getElementById('permissionsModalOverlay'),
        permissionsModalTitle: document.getElementById('permissionsModalTitle'),
        permissionsContainer: document.getElementById('permissionsContainer'),
        closePermissionsModal: document.getElementById('closePermissionsModal'),
        cancelPermissionsModal: document.getElementById('cancelPermissionsModal'),
        savePermissionsBtn: document.getElementById('savePermissionsBtn'),
        permissionsUserId: document.getElementById('permissionsUserId'),
        
        deleteModal: document.getElementById('deleteModal'),
        deleteModalOverlay: document.getElementById('deleteModalOverlay'),
        closeDeleteModal: document.getElementById('closeDeleteModal'),
        cancelDeleteModal: document.getElementById('cancelDeleteModal'),
        confirmDeleteBtn: document.getElementById('confirmDeleteBtn'),
        deleteMessage: document.getElementById('deleteMessage'),
        deleteUserId: document.getElementById('deleteUserId'),
        
        toastContainer: document.getElementById('toastContainer'),
        
        totalUsers: document.getElementById('totalUsers'),
        activeUsers: document.getElementById('activeUsers'),
        adminUsers: document.getElementById('adminUsers'),
        inactiveUsers: document.getElementById('inactiveUsers')
    };

    init();

    function init() {
        loadUsers();
        loadPermissions();
        bindEvents();
    }

    const roleDescriptions = {
        'adviser': 'Full system access - Can manage all features including users, finances, students, events, elections, and reports',
        'dean': 'View-only access to all records plus user management capabilities',
        'president': 'Financial management, event viewing, and comprehensive report generation',
        'treasurer': 'Full financial management - Funds, expenses, membership fees, and financial reports',
        'auditor': 'View financial records, modify membership fees, and generate financial reports',
        'secretary': 'Student management and general report generation',
        'comelec': 'Election management - Start/end elections, view results, generate election reports',
        'event_coordinator': 'Event management - Create events, view finances, generate event reports',
        'officer': 'Basic access - View dashboard, events, and election results'
    };

    function bindEvents() {
        elements.searchUser.addEventListener('input', debounce(function() {
            searchQuery = this.value;
            currentPage = 1;
            loadUsers();
        }, 300));

        elements.filterRole.addEventListener('change', function() {
            roleFilter = this.value;
            currentPage = 1;
            loadUsers();
        });

        elements.toggleInactive.addEventListener('click', function() {
            showInactive = !showInactive;
            this.classList.toggle('active', showInactive);
            this.querySelector('span').textContent = showInactive ? 'Hide Inactive' : 'Show Inactive';
            currentPage = 1;
            loadUsers();
        });

        document.getElementById('userRole').addEventListener('change', function() {
            const roleDesc = document.getElementById('roleDescription');
            roleDesc.textContent = roleDescriptions[this.value] || 'Default permissions will be assigned based on role';
        });

        elements.addUserBtn.addEventListener('click', openAddUserModal);
        elements.closeUserModal.addEventListener('click', closeUserModal);
        elements.cancelUserModal.addEventListener('click', closeUserModal);
        elements.userModalOverlay.addEventListener('click', closeUserModal);
        elements.saveUserBtn.addEventListener('click', saveUser);

        elements.closePermissionsModal.addEventListener('click', closePermissionsModal);
        elements.cancelPermissionsModal.addEventListener('click', closePermissionsModal);
        elements.permissionsModalOverlay.addEventListener('click', closePermissionsModal);
        elements.savePermissionsBtn.addEventListener('click', savePermissions);

        elements.closeDeleteModal.addEventListener('click', closeDeleteModal);
        elements.cancelDeleteModal.addEventListener('click', closeDeleteModal);
        elements.deleteModalOverlay.addEventListener('click', closeDeleteModal);
        elements.confirmDeleteBtn.addEventListener('click', confirmDelete);

        elements.prevPage.addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                loadUsers();
            }
        });

        elements.nextPage.addEventListener('click', function() {
            if (currentPage < totalPages) {
                currentPage++;
                loadUsers();
            }
        });

        const passwordToggle = document.getElementById('passwordToggle');
        const passwordInput = document.getElementById('password');
        const passwordToggleIcon = document.getElementById('passwordToggleIcon');
        
        if (passwordToggle && passwordInput && passwordToggleIcon) {
            passwordToggle.addEventListener('click', function() {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                const newType = isPassword ? 'text' : 'password';
                passwordInput.setAttribute('type', newType);
                
                if (newType === 'text') {
                    passwordToggleIcon.classList.remove('fa-eye-slash');
                    passwordToggleIcon.classList.add('fa-eye');
                    passwordToggle.setAttribute('aria-label', 'Hide password');
                } else {
                    passwordToggleIcon.classList.remove('fa-eye');
                    passwordToggleIcon.classList.add('fa-eye-slash');
                    passwordToggle.setAttribute('aria-label', 'Show password');
                }
            });
        }
    }

    function loadUsers() {
        elements.usersTableBody.innerHTML = `
            <tr>
                <td colspan="6">
                    <div class="loading-spinner"><div class="spinner"></div></div>
                </td>
            </tr>
        `;

        const params = new URLSearchParams({
            page: currentPage,
            limit: 10,
            show_inactive: showInactive,
            search: searchQuery,
            role: roleFilter
        });

        fetch(`../api/users/read.php?${params}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderUsers(data.data);
                    updatePagination(data.pagination);
                    updateStats(data.data);
                } else {
                    showToast('Failed to load users', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to load users', 'error');
            });
    }

    function renderUsers(users) {
        if (users.length === 0) {
            elements.usersTableBody.innerHTML = `
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="fas fa-users-slash"></i>
                            <h3>No users found</h3>
                            <p>Try adjusting your search or filter criteria</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        elements.usersTableBody.innerHTML = users.map(user => {
            const initials = `${user.first_name.charAt(0)}${user.last_name.charAt(0)}`.toUpperCase();
            const lastLogin = user.last_login ? formatDate(user.last_login) : 'Never';
            const permCount = user.permissions ? user.permissions.length : 0;
            
            return `
                <tr data-id="${user.id}">
                    <td>
                        <div class="user-info">
                            <div class="user-avatar">${initials}</div>
                            <div class="user-details">
                                <h4>${escapeHtml(user.first_name)} ${escapeHtml(user.last_name)}</h4>
                                <span>${escapeHtml(user.email)}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="role-badge ${user.role}">${formatRole(user.role)}</span>
                    </td>
                    <td>
                        <span class="status-badge ${user.status}">${capitalizeFirst(user.status)}</span>
                    </td>
                    <td>
                        <span style="color: #6b7280;">${permCount} permission${permCount !== 1 ? 's' : ''}</span>
                    </td>
                    <td style="color: #6b7280; font-size: 13px;">${lastLogin}</td>
                    <td>
                        ${typeof userPermissions !== 'undefined' && (userPermissions.canManageUsers || userPermissions.canDemoteAccounts) ? `
                        <div class="action-buttons">
                            ${typeof userPermissions !== 'undefined' && userPermissions.canManageUsers ? `
                            <button class="action-btn edit" onclick="editUser(${user.id})" title="Edit User">
                                <i class="fas fa-edit"></i>
                            </button>
                            ` : ''}
                            ${typeof userPermissions !== 'undefined' && userPermissions.canManageUsers ? `
                            <button class="action-btn permissions" onclick="managePermissions(${user.id}, '${escapeHtml(user.first_name)} ${escapeHtml(user.last_name)}')" title="Manage Permissions">
                                <i class="fas fa-key"></i>
                            </button>
                            ` : ''}
                            ${typeof userPermissions !== 'undefined' && userPermissions.canManageUsers ? (user.status === 'active' ? `
                                <button class="action-btn delete" onclick="deactivateUser(${user.id}, '${escapeHtml(user.first_name)} ${escapeHtml(user.last_name)}')" title="Deactivate User">
                                    <i class="fas fa-user-slash"></i>
                                </button>
                            ` : `
                                <button class="action-btn restore" onclick="reactivateUser(${user.id})" title="Reactivate User">
                                    <i class="fas fa-user-check"></i>
                                </button>
                            `) : ''}
                        </div>
                        ` : '<span style="color: #9ca3af;">View Only</span>'}
                    </td>
                </tr>
            `;
        }).join('');
    }

    function updatePagination(pagination) {
        currentPage = pagination.current_page;
        totalPages = pagination.total_pages;
        
        elements.pageIndicator.textContent = `Page ${currentPage} of ${totalPages || 1}`;
        elements.prevPage.classList.toggle('disabled', currentPage <= 1);
        elements.nextPage.classList.toggle('disabled', currentPage >= totalPages);
    }

    function updateStats(users) {
        fetch('../api/users/read.php?show_inactive=true&limit=1000')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const allUsers = data.data;
                    const active = allUsers.filter(u => u.status === 'active').length;
                    const inactive = allUsers.filter(u => u.status !== 'active').length;
                    const admins = allUsers.filter(u => u.role === 'adviser' || u.role === 'dean').length;
                    
                    elements.totalUsers.textContent = allUsers.length;
                    elements.activeUsers.textContent = active;
                    elements.adminUsers.textContent = admins;
                    elements.inactiveUsers.textContent = inactive;
                }
            });
    }

    function loadPermissions() {
        fetch('../api/users/get_permissions.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    allPermissions = data.data;
                }
            })
            .catch(error => console.error('Error loading permissions:', error));
    }

    function openAddUserModal() {
        isEditMode = false;
        elements.userModalTitle.textContent = 'Add New User';
        elements.userForm.reset();
        elements.userId.value = '';
        elements.passwordRequired.style.display = 'inline';
        elements.passwordHint.textContent = 'Minimum 8 characters';
        elements.statusGroup.style.display = 'none';
        document.getElementById('password').required = true;
        
        elements.userModal.classList.add('show');
        elements.userModalOverlay.classList.add('show');
    }

    function closeUserModal() {
        elements.userModal.classList.remove('show');
        elements.userModalOverlay.classList.remove('show');
        elements.userForm.reset();
    }

    window.editUser = function(userId) {
        isEditMode = true;
        elements.userModalTitle.textContent = 'Edit User';
        elements.passwordRequired.style.display = 'none';
        elements.passwordHint.textContent = 'Leave blank to keep current password';
        elements.statusGroup.style.display = 'block';
        document.getElementById('password').required = false;
        
        fetch(`../api/users/get_single.php?id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const user = data.data;
                    elements.userId.value = user.id;
                    document.getElementById('firstName').value = user.first_name;
                    document.getElementById('lastName').value = user.last_name;
                    document.getElementById('email').value = user.email;
                    document.getElementById('userRole').value = user.role;
                    document.getElementById('userStatus').value = user.status;
                    document.getElementById('password').value = '';
                    
                    elements.userModal.classList.add('show');
                    elements.userModalOverlay.classList.add('show');
                } else {
                    showToast('Failed to load user data', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to load user data', 'error');
            });
    };

    function saveUser() {
        const form = elements.userForm;
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = {
            first_name: document.getElementById('firstName').value,
            last_name: document.getElementById('lastName').value,
            email: document.getElementById('email').value,
            role: document.getElementById('userRole').value
        };

        const password = document.getElementById('password').value;
        if (password) {
            formData.password = password;
        }

        if (isEditMode) {
            formData.id = parseInt(elements.userId.value);
            formData.status = document.getElementById('userStatus').value;
        }

        const url = isEditMode ? '../api/users/update.php' : '../api/users/create.php';

        elements.saveUserBtn.disabled = true;
        elements.saveUserBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(isEditMode ? 'User updated successfully' : 'User created successfully', 'success');
                closeUserModal();
                loadUsers();
            } else {
                showToast(data.error || 'Failed to save user', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to save user', 'error');
        })
        .finally(() => {
            elements.saveUserBtn.disabled = false;
            elements.saveUserBtn.innerHTML = '<i class="fas fa-save"></i> Save User';
        });
    }

    window.managePermissions = function(userId, userName) {
        elements.permissionsUserId.value = userId;
        elements.permissionsModalTitle.textContent = `Manage Permissions - ${userName}`;
        
        elements.permissionsModal.classList.add('show');
        elements.permissionsModalOverlay.classList.add('show');
        
        fetch(`../api/users/get_single.php?id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const userPermIds = data.data.permissions.map(p => p.id);
                    const userRole = data.data.role;
                    renderPermissions(userPermIds, userRole);
                }
            });
    };

    function renderPermissions(userPermIds, userRole) {
        const moduleIcons = {
            'Dashboard': 'fas fa-home',
            'Funds': 'fas fa-money-bill',
            'Expenses': 'fas fa-receipt',
            'Membership': 'fas fa-id-card',
            'Students': 'fas fa-users',
            'Events': 'fas fa-calendar',
            'Reports': 'fas fa-file-alt',
            'Elections': 'fas fa-vote-yea',
            'Users': 'fas fa-user-cog'
        };

        const isReadOnly = userRole === 'adviser' || userRole === 'dean';
        
        if (isReadOnly) {
            elements.savePermissionsBtn.disabled = true;
            elements.savePermissionsBtn.style.opacity = '0.6';
            elements.savePermissionsBtn.style.cursor = 'not-allowed';
        } else {
            elements.savePermissionsBtn.disabled = false;
            elements.savePermissionsBtn.style.opacity = '1';
            elements.savePermissionsBtn.style.cursor = 'pointer';
        }

        const grouped = {};
        allPermissions.forEach(perm => {
            const module = capitalizeFirst(perm.module);
            if (!grouped[module]) grouped[module] = [];
            grouped[module].push(perm);
        });

        let html = '';
        for (const [module, perms] of Object.entries(grouped)) {
            const icon = moduleIcons[module] || 'fas fa-folder';
            html += `
                <div class="permission-module">
                    <h4><i class="${icon}"></i> ${module}</h4>
                    <div class="permissions-grid">
                        ${perms.map(perm => {
                            const isChecked = userPermIds.includes(perm.id);
                            const disabledAttr = isReadOnly ? 'disabled' : '';
                            const clickHandler = isReadOnly ? '' : 'onclick="togglePermission(this)"';
                            const disabledClass = isReadOnly ? 'disabled' : '';
                            return `
                                <div class="permission-item ${isChecked ? 'checked' : ''} ${disabledClass}" ${clickHandler}>
                                    <input type="checkbox" name="permissions[]" value="${perm.id}" ${isChecked ? 'checked' : ''} ${disabledAttr}>
                                    <label>${perm.name}</label>
                                </div>
                            `;
                        }).join('')}
                    </div>
                </div>
            `;
        }
        
        elements.permissionsContainer.innerHTML = html;
    }

    window.togglePermission = function(element) {
        if (element.classList.contains('disabled')) {
            return;
        }
        const checkbox = element.querySelector('input[type="checkbox"]');
        if (checkbox.disabled) {
            return;
        }
        checkbox.checked = !checkbox.checked;
        element.classList.toggle('checked', checkbox.checked);
    };

    function closePermissionsModal() {
        elements.permissionsModal.classList.remove('show');
        elements.permissionsModalOverlay.classList.remove('show');
        elements.savePermissionsBtn.disabled = false;
        elements.savePermissionsBtn.style.opacity = '1';
        elements.savePermissionsBtn.style.cursor = 'pointer';
    }

    function savePermissions() {
        const userId = elements.permissionsUserId.value;
        
        if (!userId || userId <= 0) {
            showToast('Invalid user ID', 'error');
            return;
        }

        if (elements.savePermissionsBtn.disabled) {
            showToast('Permissions for SOCCS Adviser cannot be modified', 'error');
            return;
        }

        const checkboxes = elements.permissionsContainer.querySelectorAll('input[type="checkbox"]:checked');
        const permissions = Array.from(checkboxes).map(cb => parseInt(cb.value));

        elements.savePermissionsBtn.disabled = true;
        elements.savePermissionsBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

        fetch('../api/users/update.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: parseInt(userId), permissions: permissions })
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        return { success: false, error: `HTTP ${response.status}: ${text.substring(0, 100)}` };
                    }
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                console.log('Permission update successful:', data);
                if (data.saved_permissions) {
                    console.log('Saved permissions:', data.saved_permissions);
                    console.log('Permission count:', data.permission_count);
                }
                const message = data.message || 'Permission Updated';
                showToast(message, 'success');
                closePermissionsModal();
                loadUsers();
            } else {
                console.error('Permission update error:', data);
                showToast(data.error || 'Failed to update permissions', 'error');
            }
        })
        .catch(error => {
            console.error('Network error:', error);
            showToast('Network error: Failed to update permissions', 'error');
        })
        .finally(() => {
            elements.savePermissionsBtn.disabled = false;
            elements.savePermissionsBtn.innerHTML = '<i class="fas fa-save"></i> Save Permissions';
        });
    }

    window.deactivateUser = function(userId, userName) {
        elements.deleteUserId.value = userId;
        elements.deleteMessage.textContent = `Are you sure you want to deactivate "${userName}"?`;
        elements.confirmDeleteBtn.innerHTML = '<i class="fas fa-user-slash"></i> Deactivate';
        elements.confirmDeleteBtn.style.background = '#ef4444';
        
        elements.deleteModal.classList.add('show');
        elements.deleteModalOverlay.classList.add('show');
    };

    window.reactivateUser = function(userId) {
        fetch('../api/users/update.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: userId, status: 'active' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('User reactivated successfully', 'success');
                loadUsers();
            } else {
                showToast(data.error || 'Failed to reactivate user', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to reactivate user', 'error');
        });
    };

    function closeDeleteModal() {
        elements.deleteModal.classList.remove('show');
        elements.deleteModalOverlay.classList.remove('show');
    }

    function confirmDelete() {
        const userId = elements.deleteUserId.value;
        
        elements.confirmDeleteBtn.disabled = true;
        elements.confirmDeleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        fetch('../api/users/delete.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: parseInt(userId) })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('User deactivated successfully', 'success');
                closeDeleteModal();
                loadUsers();
            } else {
                showToast(data.error || 'Failed to deactivate user', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to deactivate user', 'error');
        })
        .finally(() => {
            elements.confirmDeleteBtn.disabled = false;
            elements.confirmDeleteBtn.innerHTML = '<i class="fas fa-user-slash"></i> Deactivate';
        });
    }

    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            <span class="toast-message">${message}</span>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        elements.toastContainer.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func.apply(this, args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    }

    function formatDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-US', { 
            month: 'short', 
            day: 'numeric', 
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function formatRole(role) {
        const roles = {
            'adviser': 'SOCCS Adviser',
            'dean': 'CCS Dean',
            'president': 'SOCCS President',
            'treasurer': 'SOCCS Treasurer',
            'auditor': 'SOCCS Auditor',
            'secretary': 'Secretary',
            'comelec': 'COMELEC',
            'event_coordinator': 'Event Coordinator',
            'officer': 'Officer'
        };
        return roles[role] || role;
    }

    function capitalizeFirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
});

