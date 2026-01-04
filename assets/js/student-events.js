// Student Events Calendar JavaScript

class EventsCalendar {
  constructor() {
    this.currentDate = new Date();
    this.selectedDate = null;
    this.today = new Date();
    this.events = [];
    this.init();
  }

  async init() {
    this.setupEventListeners();
    await this.loadEvents();
    this.renderCalendar();
  }

  async loadEvents() {
    try {
      const response = await fetch('../api/events/read.php?status=all');
      const result = await response.json();
      
      if (result.success) {
        this.events = result.data.map(event => ({
          id: event.id,
          title: event.name,
          date: event.date,
          time: this.formatTime(event.time),
          location: event.location,
          category: event.category,
          description: event.description,
          contests: event.contests || [],
          is_multi_day: event.is_multi_day,
          end_date: event.end_date
        }));
        this.renderCalendar();
      } else {
        console.error('Failed to load events:', result.error);
      }
    } catch (error) {
      console.error('Error loading events:', error);
    }
  }

  formatTime(time) {
    if (!time) return '9:00 AM';
    const [hours, minutes] = time.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour % 12 || 12;
    return `${displayHour}:${minutes} ${ampm}`;
  }

    setupEventListeners() {
        // Navigation buttons
        document.getElementById('prevMonth').addEventListener('click', () => {
            this.currentDate.setMonth(this.currentDate.getMonth() - 1);
            this.renderCalendar();
        });

        document.getElementById('nextMonth').addEventListener('click', () => {
            this.currentDate.setMonth(this.currentDate.getMonth() + 1);
            this.renderCalendar();
        });
    }

  renderCalendar() {
    const year = this.currentDate.getFullYear();
    const month = this.currentDate.getMonth();
    
    // Update month display
    document.getElementById('currentMonth').textContent = 
      this.currentDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

    // Get first day of month and number of days
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const daysInMonth = lastDay.getDate();
    const startingDayOfWeek = firstDay.getDay();

    // Get previous month's days for padding
    const prevMonth = new Date(year, month - 1, 0);
    const daysInPrevMonth = prevMonth.getDate();

    const calendarDays = document.getElementById('calendarDays');
    calendarDays.innerHTML = '';

    // Add previous month's trailing days
    for (let i = startingDayOfWeek - 1; i >= 0; i--) {
      const day = daysInPrevMonth - i;
      const dayElement = this.createDayElement(day, true);
      calendarDays.appendChild(dayElement);
    }

    // Add current month's days
    for (let day = 1; day <= daysInMonth; day++) {
      const dayElement = this.createDayElement(day, false);
      calendarDays.appendChild(dayElement);
    }

    // Add next month's leading days
    const totalCells = calendarDays.children.length;
    const remainingCells = 42 - totalCells; // 6 weeks * 7 days
    for (let day = 1; day <= remainingCells; day++) {
      const dayElement = this.createDayElement(day, true);
      calendarDays.appendChild(dayElement);
    }
  }

  createDayElement(day, isOtherMonth) {
    const dayElement = document.createElement('div');
    dayElement.className = 'calendar-day';
    
    if (isOtherMonth) {
      dayElement.classList.add('other-month');
    }

    // Check if it's today
    const today = new Date();
    const currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), day);
    if (!isOtherMonth && this.isSameDay(currentDate, today)) {
      dayElement.classList.add('today');
    }

    // Check if it's selected
    if (this.selectedDate && !isOtherMonth && this.isSameDay(currentDate, this.selectedDate)) {
      dayElement.classList.add('selected');
    }

    // Add day number
    const dayNumber = document.createElement('div');
    dayNumber.className = 'day-number';
    dayNumber.textContent = day;
    dayElement.appendChild(dayNumber);

    // Add clean event indicators (dots only)
    if (!isOtherMonth) {
      const eventsForDay = this.getEventsForDate(currentDate);
      if (eventsForDay.length > 0) {
        const indicatorsContainer = document.createElement('div');
        indicatorsContainer.className = 'event-indicators';
        
        // Show up to 3 dots, with more indicator if needed
        const maxDots = 3;
        const dotsToShow = Math.min(eventsForDay.length, maxDots);
        const hasMore = eventsForDay.length > maxDots;
        
        for (let i = 0; i < dotsToShow; i++) {
          const dot = document.createElement('div');
          dot.className = `event-dot ${eventsForDay[i].category}`;
          indicatorsContainer.appendChild(dot);
        }
        
        if (hasMore) {
          const moreDot = document.createElement('div');
          moreDot.className = 'event-dot more-events';
          moreDot.textContent = '+';
          moreDot.title = `${eventsForDay.length - maxDots} more events`;
          indicatorsContainer.appendChild(moreDot);
        }
        
        dayElement.appendChild(indicatorsContainer);
      }
    }

    // Add click event
    if (!isOtherMonth) {
      dayElement.addEventListener('click', () => {
        this.selectDate(currentDate);
      });
    }

    return dayElement;
  }

  selectDate(date) {
    this.selectedDate = date;
    this.renderCalendar(); // Re-render to update selected state
    this.displayEventsForDate(date);
  }

  displayEventsForDate(date) {
    const eventsForDate = this.getEventsForDate(date);
    const eventsContainer = document.getElementById('selectedDateEvents');
    
    if (eventsForDate.length === 0) {
      eventsContainer.innerHTML = `
        <div class="no-events-message">
          <i class="fas fa-calendar-day"></i>
          <p>No events scheduled for ${date.toLocaleDateString('en-US', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
          })}</p>
        </div>
      `;
    } else {
      // Show header with date and event count
      const header = document.createElement('div');
      header.className = 'selected-date-header';
      header.innerHTML = `
        <h3>${date.toLocaleDateString('en-US', { 
          weekday: 'long', 
          month: 'long', 
          day: 'numeric' 
        })}</h3>
        <span class="event-count">${eventsForDate.length} event${eventsForDate.length > 1 ? 's' : ''}</span>
      `;
      
      const eventsList = document.createElement('div');
      eventsList.className = 'events-list';
      
      eventsForDate.forEach(event => {
        const eventCard = document.createElement('div');
        eventCard.className = 'event-card';
        
        let durationInfo = '';
        if (event.is_multi_day && event.end_date) {
          const startDate = new Date(event.date);
          const endDate = new Date(event.end_date);
          const days = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
          durationInfo = `<div class="event-duration"><i class="fas fa-calendar-week"></i> ${days} days event</div>`;
        }
        
        let contestSection = '';
        if (event.contests && event.contests.length > 0) {
          contestSection = '<div class="contests-list">';
          event.contests.forEach((contest, index) => {
            contestSection += `
              <div class="contest-item-display">
                <div class="contest-details">
                  <div class="contest-header">
                    <i class="fas fa-trophy"></i>
                    <strong>Contest Details</strong>
                  </div>
                  <p>${contest.contest_details}</p>
                </div>
                <a href="${contest.registration_link}" target="_blank" rel="noopener noreferrer" class="registration-link-btn">
                  <i class="fas fa-external-link-alt"></i>
                  Register Now
                </a>
              </div>
            `;
          });
          contestSection += '</div>';
        }
        
        eventCard.innerHTML = `
          <div class="event-header">
            <div class="event-time">${event.time}</div>
          </div>
          <div class="event-title">${event.title}</div>
          ${durationInfo}
          <div class="event-location">
            <i class="fas fa-map-marker-alt"></i>
            ${event.location}
          </div>
          ${event.description ? `<div class="event-description">${event.description}</div>` : ''}
          <div class="event-tag ${event.category}">${event.category}</div>
          ${contestSection}
        `;
        eventsList.appendChild(eventCard);
      });
      
      eventsContainer.innerHTML = '';
      eventsContainer.appendChild(header);
      eventsContainer.appendChild(eventsList);
    }
  }

  getEventsForDate(date) {
    const dateString = this.formatDateForComparison(date);
    return this.events.filter(event => {
      const eventStart = event.date;
      
      if (event.is_multi_day && event.end_date) {
        const eventEnd = event.end_date;
        return dateString >= eventStart && dateString <= eventEnd;
      }
      
      return event.date === dateString;
    });
  }

  formatDateForComparison(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  }

  isSameDay(date1, date2) {
    return date1.getDate() === date2.getDate() &&
           date1.getMonth() === date2.getMonth() &&
           date1.getFullYear() === date2.getFullYear();
  }

}

// Initialize calendar when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
  new EventsCalendar();
});

// Add smooth scrolling for mobile
document.addEventListener('DOMContentLoaded', () => {
  // Add touch support for mobile calendar navigation
  let startX = 0;
  let startY = 0;
  
  const calendarGrid = document.querySelector('.calendar-grid');
  
  if (calendarGrid) {
    calendarGrid.addEventListener('touchstart', (e) => {
      startX = e.touches[0].clientX;
      startY = e.touches[0].clientY;
    });
    
    calendarGrid.addEventListener('touchend', (e) => {
      const endX = e.changedTouches[0].clientX;
      const endY = e.changedTouches[0].clientY;
      const diffX = startX - endX;
      const diffY = startY - endY;
      
      // Only trigger if horizontal swipe is more significant than vertical
      if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 50) {
        if (diffX > 0) {
          // Swipe left - next month
          document.getElementById('nextMonth').click();
        } else {
          // Swipe right - previous month
          document.getElementById('prevMonth').click();
        }
      }
    });
  }
});
