# Student Dashboard Documentation

## Overview
The Student Dashboard is a comprehensive interface designed for SOCCS (Student Organization of the College of Computer Studies) students to access elections, events, financial transparency, and announcements in a secure, blockchain-verified environment.

## Features

### 🔐 Authentication & Security
- **Dual Login System**: Separate authentication for students and administrators
- **Student Registration**: Self-registration with auto-generated credentials
- **Session Management**: Secure session tokens with expiration
- **Blockchain Security**: All transactions and votes secured on blockchain

### 🗳️ Elections Section
- **Live Election Status**: Real-time countdown and voting status
- **Candidate Viewing**: Browse all candidates and their platforms
- **Secure Voting**: Blockchain-secured voting system
- **Voting History**: Transparent record of participation
- **Election Statistics**: Live voter counts and participation rates

### 📅 Events & Announcements
- **Upcoming Events**: Calendar view of SOCCS events
- **Event Categories**: Academic, social, competition events
- **Real-time Announcements**: Important updates and notifications
- **Priority Alerts**: Critical announcements highlighted

### 💰 Financial Transparency
- **Fund Overview**: Total funds, expenses, and available balance
- **Transaction History**: Recent financial transactions
- **Blockchain Verification**: All transactions verified on blockchain
- **Read-only Access**: Students can view but not modify financial data

### 🏠 Dashboard Features
- **Personal Profile**: Student information and academic details
- **Responsive Design**: Mobile-friendly interface
- **Real-time Updates**: Live data updates every minute
- **Interactive Elements**: Hover effects and smooth animations

## File Structure

```
student-dashboard/
├── pages/
│   └── student-dashboard.php          # Main dashboard page
├── components/
│   └── student-sidebar.php            # Student-specific navigation
├── assets/
│   ├── css/
│   │   └── student-dashboard.css      # Dashboard styling
│   └── js/
│       └── student-dashboard.js       # Dashboard functionality
├── api/
│   ├── get_student_financial_summary.php
│   ├── get_recent_transactions.php
│   ├── get_student_events.php
│   ├── get_student_announcements.php
│   └── check_new_announcements.php
├── auth/
│   ├── student-auth.php               # Student authentication
│   └── student-register.php           # Student registration
└── sql/
    └── student_dashboard_schema.sql   # Database schema
```

## Database Schema

### Core Tables
- **students**: Student profiles and credentials
- **elections**: Election management
- **candidates**: Election candidates
- **votes**: Blockchain-secured voting records
- **events**: SOCCS events and activities
- **announcements**: Student announcements
- **funds**: Financial transparency data
- **expenses**: Expense tracking

### Security Features
- **student_sessions**: Secure session management
- **transaction_hash**: Blockchain verification for all transactions
- **vote_hash**: Blockchain verification for all votes

## Installation

1. **Database Setup**:
   ```sql
   -- Run the SQL schema
   mysql -u username -p database_name < sql/student_dashboard_schema.sql
   ```

2. **File Permissions**:
   - Ensure proper read/write permissions for uploads directory
   - Configure web server to serve PHP files

3. **Configuration**:
   - Update database connection in `includes/database.php`
   - Configure blockchain endpoints if using external blockchain

## Usage

### For Students
1. **Registration**: Use student registration form with personal details
2. **Login**: Use Student ID or email with default password (Student ID)
3. **Voting**: Access secure voting portal during election periods
4. **Transparency**: View financial reports and blockchain verification

### For Administrators
1. **Student Management**: View registered students in admin panel
2. **Election Setup**: Create elections, approve candidates
3. **Financial Management**: Add funds and expenses with blockchain verification
4. **Event Management**: Create and manage student events

## Security Features

### Blockchain Integration
- All financial transactions recorded on blockchain
- Voting records immutably stored
- Transaction hashes provide verification
- Transparent and tamper-proof records

### Access Control
- Role-based access (Student vs Admin)
- Session-based authentication
- API endpoint protection
- Input validation and sanitization

### Data Protection
- Password hashing with bcrypt
- SQL injection prevention
- XSS protection
- CSRF token implementation

## API Endpoints

### Financial Data
- `GET /api/get_student_financial_summary.php` - Fund overview
- `GET /api/get_recent_transactions.php` - Recent transactions

### Events & Announcements
- `GET /api/get_student_events.php` - Upcoming events
- `GET /api/get_student_announcements.php` - Student announcements
- `GET /api/check_new_announcements.php` - Check for new announcements

### Authentication
- `POST /auth/student-auth.php` - Student login
- `POST /auth/student-register.php` - Student registration

## Responsive Design

The dashboard is fully responsive and optimized for:
- **Desktop**: Full-featured layout with sidebar navigation
- **Tablet**: Adapted grid layout with touch-friendly interactions
- **Mobile**: Stacked layout with hamburger menu navigation

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## Contributing

When contributing to the student dashboard:

1. Follow the existing code structure and naming conventions
2. Ensure all new features include proper error handling
3. Add blockchain verification for any new transaction types
4. Update this documentation for any new features
5. Test thoroughly on all supported devices and browsers

## Support

For technical support or feature requests, contact the SOCCS development team.

---

**Last Updated**: December 2024
**Version**: 1.0.0
