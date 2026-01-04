// Enhanced Events Management System
class EventsManager {
    constructor() {
        this.events = [];
        this.currentPage = 1;
        this.itemsPerPage = 6;
        this.editingEventId = null;
        this.init();
    }

    async init() {
        this.setupEventListeners();
        await this.loadEvents();
        this.updateSummaryCards();
        this.displayEvents();
    }

    async loadEvents() {
        try {
            const statusFilter = document.getElementById('filter-status')?.value || 'all';
            const dateFilter = document.getElementById('filter-date')?.value || '';
            const searchFilter = document.getElementById('search-event')?.value || '';
            
            const params = new URLSearchParams();
            if (statusFilter) params.append('status', statusFilter);
            if (dateFilter) params.append('date', dateFilter);
            if (searchFilter) params.append('search', searchFilter);
            
            const response = await fetch(`../api/events/read.php?${params.toString()}`);
            const result = await response.json();
            
            if (result.success) {
                this.events = result.data;
            } else {
                console.error('Failed to load events:', result.error);
            }
        } catch (error) {
            console.error('Error loading events:', error);
        }
    }

    setupEventListeners() {
        // Add event button click
        document.getElementById('addEventBtn').addEventListener('click', () => {
            this.openModal();
        });

        // Cancel button click
        document.getElementById('cancelEventBtn').addEventListener('click', () => {
            this.closeModal();
        });

        // Form submission
        document.getElementById('eventForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveEvent();
        });

        // Success modal OK button
        document.getElementById('successOk').addEventListener('click', () => {
            this.closeSuccessModal();
        });

        // Close (X) buttons
        const closeEventBtn = document.getElementById('closeEventModal');
        if (closeEventBtn) closeEventBtn.addEventListener('click', () => this.closeModal());
        const closeSuccessBtn = document.getElementById('closeSuccessModal');
        if (closeSuccessBtn) closeSuccessBtn.addEventListener('click', () => this.closeSuccessModal());

        // Close when clicking overlays (but not the modal itself)
        const eventOverlay = document.getElementById('eventModalOverlay');
        if (eventOverlay) {
            eventOverlay.addEventListener('click', (e) => {
                if (e.target === eventOverlay) this.closeModal();
            });
        }
        const successOverlay = document.getElementById('successOverlay');
        if (successOverlay) {
            successOverlay.addEventListener('click', (e) => {
                if (e.target === successOverlay) this.closeSuccessModal();
            });
        }

        // Filters and search
        document.getElementById('filter-status').addEventListener('change', async () => {
            this.currentPage = 1;
            await this.loadEvents();
            this.displayEvents();
        });
        
        document.getElementById('filter-date').addEventListener('change', async () => {
            this.currentPage = 1;
            await this.loadEvents();
            this.displayEvents();
        });
        
        document.getElementById('search-event').addEventListener('input', async () => {
            this.currentPage = 1;
            await this.loadEvents();
            this.displayEvents();
        });

        // Pagination buttons
        document.querySelector('.prev-btn').addEventListener('click', () => {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.displayEvents();
            }
        });

        document.querySelector('.next-btn').addEventListener('click', () => {
            const filteredEvents = this.getFilteredEvents();
            const totalPages = Math.ceil(filteredEvents.length / this.itemsPerPage);

            if (this.currentPage < totalPages) {
                this.currentPage++;
                this.displayEvents();
            }
        });
    }

    updateSummaryCards() {
        const totalEventsCount = this.events.length;
        const upcomingEventsCount = this.events.filter(event => event.status === 'upcoming').length;
        const pastEventsCount = this.events.filter(event => event.status === 'completed' || event.status === 'archived').length;

        // Update the DOM
        document.getElementById('totalEvents').textContent = totalEventsCount;
        document.getElementById('upcomingEvents').textContent = upcomingEventsCount;
        document.getElementById('pastEvents').textContent = pastEventsCount;
    }

    getFilteredEvents() {
        return this.events;
    }

    displayEvents() {
        const filteredEvents = this.getFilteredEvents();
        const totalPages = Math.ceil(filteredEvents.length / this.itemsPerPage);

        // Update pagination controls
        const prevBtn = document.querySelector('.prev-btn');
        const nextBtn = document.querySelector('.next-btn');
        const pageIndicator = document.querySelector('.page-indicator');
        
        prevBtn.classList.toggle('disabled', this.currentPage <= 1);
        nextBtn.classList.toggle('disabled', this.currentPage >= totalPages);
        pageIndicator.textContent = `Page ${this.currentPage} of ${totalPages || 1}`;

        // Calculate pagination slice
        const startIndex = (this.currentPage - 1) * this.itemsPerPage;
        const endIndex = startIndex + this.itemsPerPage;
        const paginatedEvents = filteredEvents.slice(startIndex, endIndex);

        // Clear table
        const eventTableBody = document.getElementById('event-table-body');
        eventTableBody.innerHTML = '';

        // Add events to table
        if (paginatedEvents.length === 0) {
            const emptyRow = document.createElement('tr');
            emptyRow.innerHTML = `
                <td colspan="6" style="text-align: center; padding: 40px;">
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <h3>No events found</h3>
                        <p>Try adjusting your filters or add a new event</p>
                    </div>
                </td>
            `;
            eventTableBody.appendChild(emptyRow);
        } else {
            paginatedEvents.forEach(event => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div class="event-category-icon ${event.category}">
                                <i class="fas ${this.getCategoryIcon(event.category)}"></i>
                            </div>
                            <div>
                                <div style="font-weight: 600; color: var(--text-primary);">${event.name}</div>
                                <div style="font-size: 12px; color: var(--text-secondary);">${this.formatTime12Hour(event.time)}</div>
                            </div>
                        </div>
                    </td>
                    <td>${this.formatEventDate(event)}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <i class="fas fa-map-marker-alt" style="color: var(--text-secondary); font-size: 12px;"></i>
                            <span>${event.location}</span>
                        </div>
                    </td>
                    <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">${event.description}</td>
                    <td><span class="event-status ${event.status}">${this.capitalizeFirstLetter(event.status)}</span></td>
                    <td>
                        <div class="action-buttons">
                            ${typeof userPermissions !== 'undefined' && userPermissions.canManageEvents ? `
                            <button class="action-btn edit" onclick="eventsManager.editEvent(${event.id})" title="Edit Event">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn archive" onclick="eventsManager.archiveEvent(${event.id})" title="Archive Event">
                                <i class="fas fa-archive"></i>
                            </button>
                            ` : '<span style="color: #9ca3af; font-size: 12px;">No actions available</span>'}
                        </div>
                    </td>
                `;
                eventTableBody.appendChild(row);
            });
        }
    }

    getCategoryIcon(category) {
        const icons = {
            'academic': 'fa-graduation-cap',
            'competition': 'fa-trophy',
            'social': 'fa-users',
            'workshop': 'fa-tools'
        };
        return icons[category] || 'fa-calendar';
    }

    openModal(eventId = null) {
        this.editingEventId = eventId;
        const modalTitle = document.getElementById('eventModalTitle');
        
        // Always reset contest counter and clear contests container when opening modal
        window.contestCounter = 0;
        const contestsContainer = document.getElementById('contestsContainer');
        if (contestsContainer) {
            contestsContainer.innerHTML = '';
        }

        if (eventId) {
            // Edit existing event
            const event = this.events.find(e => e.id === eventId);
            if (event) {
                modalTitle.textContent = 'Edit Event';
                document.getElementById('eventName').value = event.name;
                document.getElementById('eventDate').value = event.date;
                document.getElementById('eventLocation').value = event.location;
                document.getElementById('eventDescription').value = event.description;
                document.getElementById('eventStatus').value = event.status;
                document.getElementById('eventCategory').value = event.category || '';
                document.getElementById('eventTime').value = event.time || '';
                
                // Handle multi-day events
                if (event.is_multi_day && event.end_date) {
                    document.getElementById('eventDurationType').value = 'multiple';
                    document.getElementById('eventEndDate').value = event.end_date;
                    toggleDateInputs();
                } else {
                    document.getElementById('eventDurationType').value = 'single';
                    toggleDateInputs();
                }
                
                // Handle contests (counter already reset above)
                if (event.contests && event.contests.length > 0) {
                    event.contests.forEach((contest, index) => {
                        window.contestCounter++;
                        const contestItem = document.createElement('div');
                        contestItem.className = 'contest-item';
                        contestItem.dataset.contestId = window.contestCounter;
                        contestItem.innerHTML = `
                            <div class="contest-item-header">
                                <h4><i class="fas fa-trophy"></i> Contest</h4>
                                <button type="button" class="remove-contest-btn" onclick="removeContest(${window.contestCounter})">
                                    <i class="fas fa-times"></i> Remove
                                </button>
                            </div>
                            <div class="input-group">
                                <i class="fas fa-file-alt"></i>
                                <textarea name="contest_details[]" class="contest-details-input" placeholder="Contest Details" rows="4" required>${contest.contest_details || ''}</textarea>
                            </div>
                            <div class="input-group">
                                <i class="fas fa-link"></i>
                                <input type="url" name="registration_link[]" class="registration-link-input" placeholder="Registration Link (e.g., Google Forms, etc.)" value="${contest.registration_link || ''}" required>
                            </div>
                        `;
                        contestsContainer.appendChild(contestItem);
                    });
                }
            }
        } else {
            // Add new event
            modalTitle.textContent = 'Add Event';
            document.getElementById('eventForm').reset();
            document.getElementById('eventDate').valueAsDate = new Date();
            document.getElementById('eventTime').value = '09:00';
            document.getElementById('eventDurationType').value = 'single';
            toggleDateInputs();
            document.getElementById('contestsContainer').innerHTML = '';
            window.contestCounter = 0;
        }

        document.body.classList.add('modal-open');
        document.getElementById('eventModalOverlay').classList.add('show');
    }

    closeModal() {
        document.body.classList.remove('modal-open');
        document.getElementById('eventModalOverlay').classList.remove('show');
        this.editingEventId = null;
        document.getElementById('eventForm').reset();
        document.getElementById('eventDurationType').value = 'single';
        toggleDateInputs();
        document.getElementById('contestsContainer').innerHTML = '';
        window.contestCounter = 0;
    }

    async saveEvent() {
        const saveButton = document.querySelector('#eventModalOverlay .modal-footer button[type="submit"]');
        const originalButtonText = saveButton ? saveButton.innerHTML : '';
        
        if (saveButton) {
            saveButton.disabled = true;
            saveButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        }
        
        try {
            const durationType = document.getElementById('eventDurationType').value;
            
            const contests = [];
            const contestItems = document.querySelectorAll('.contest-item');
            contestItems.forEach(item => {
                const details = item.querySelector('.contest-details-input').value.trim();
                const link = item.querySelector('.registration-link-input').value.trim();
                if (details && link) {
                    contests.push({
                        contest_details: details,
                        registration_link: link
                    });
                }
            });
            
            const eventData = {
                name: document.getElementById('eventName').value,
                date: document.getElementById('eventDate').value,
                location: document.getElementById('eventLocation').value,
                description: document.getElementById('eventDescription').value,
                status: document.getElementById('eventStatus').value,
                time: document.getElementById('eventTime').value || "09:00",
                category: document.getElementById('eventCategory').value,
                is_multi_day: durationType === 'multiple',
                end_date: durationType === 'multiple' ? document.getElementById('eventEndDate').value : null,
                contests: contests
            };

            let url, method;
            
            if (this.editingEventId) {
                url = '../api/events/update.php';
                method = 'POST';
                eventData.id = this.editingEventId;
            } else {
                url = '../api/events/create.php';
                method = 'POST';
            }

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(eventData)
            });

            const result = await response.json();

            if (result.success) {
                this.closeModal();
                await this.loadEvents();
                this.updateSummaryCards();
                this.displayEvents();
                setTimeout(() => this.showSuccessModal(), 100);
            } else {
                alert('Error: ' + result.error);
            }
        } catch (error) {
            console.error('Error saving event:', error);
            alert('An error occurred while saving the event');
        } finally {
            if (saveButton) {
                saveButton.disabled = false;
                saveButton.innerHTML = originalButtonText;
            }
        }
    }

    editEvent(eventId) {
        this.openModal(eventId);
    }

    async archiveEvent(eventId) {
        try {
            const response = await fetch('../api/events/update.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: eventId,
                    status: 'archived'
                })
            });

            const result = await response.json();

            if (result.success) {
                await this.loadEvents();
                this.updateSummaryCards();
                this.displayEvents();
                this.showSuccessModal('Event archived successfully');
            } else {
                alert('Error: ' + result.error);
            }
        } catch (error) {
            console.error('Error archiving event:', error);
            alert('An error occurred while archiving the event');
        }
    }

    showSuccessModal(message = 'Event has been saved successfully') {
        document.body.classList.add('modal-open');
        const msgEl = document.getElementById('successMessage');
        if (msgEl) msgEl.textContent = message;
        document.getElementById('successOverlay').classList.add('show');
    }

    closeSuccessModal() {
        document.body.classList.remove('modal-open');
        document.getElementById('successOverlay').classList.remove('show');
    }

    formatDate(dateString) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString(undefined, options);
    }

    formatEventDate(event) {
        const startDate = new Date(event.date);
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        
        if (event.is_multi_day && event.end_date) {
            const endDate = new Date(event.end_date);
            const startStr = startDate.toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
            const endStr = endDate.toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
            return `${startStr} - ${endStr}`;
        }
        
        return startDate.toLocaleDateString(undefined, options);
    }

    capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    formatTime12Hour(time) {
        if (!time) return '9:00 AM';
        const [hours, minutes] = time.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const displayHour = hour % 12 || 12;
        return `${displayHour}:${minutes} ${ampm}`;
    }
}

// Initialize the events manager when DOM is loaded
let eventsManager;
document.addEventListener('DOMContentLoaded', function () {
    eventsManager = new EventsManager();
});

// Add CSS for enhanced styling
document.addEventListener('DOMContentLoaded', function () {
    const style = document.createElement('style');
    style.textContent = `
        .event-category-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
        }
        
        .event-category-icon.academic {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        }
        
        .event-category-icon.competition {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }
        
        .event-category-icon.social {
            background: linear-gradient(135deg, #10b981, #059669);
        }
        
        .event-category-icon.workshop {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-secondary);
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            color: var(--text-secondary);
        }
        
        .empty-state h3 {
            font-size: 18px;
            margin-bottom: 8px;
            color: var(--text-primary);
        }
        
        .empty-state p {
            font-size: 14px;
            margin: 0;
        }
    `;
    document.head.appendChild(style);
});