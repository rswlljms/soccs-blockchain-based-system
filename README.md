# SOCCSChain - Blockchain-Based Student Governance System

A comprehensive blockchain-powered platform for managing student organization operations, elections, events, financial transparency, and administrative tasks for the Student Organization of the College of Computer Studies (SOCCS) at LSPU – Santa Cruz Campus.

## 🎯 Overview

SOCCSChain is a modern web application that leverages blockchain technology to provide transparent, secure, and efficient management of student organization activities. The system integrates financial management, election systems, event management, and student registration with blockchain verification for enhanced security and transparency.

## ✨ Key Features

### 🔐 Authentication & Security
- Dual login system (Admin and Student)
- Secure student registration with document verification
- Session-based authentication
- Blockchain-secured transactions and votes
- Role-based access control

### 💰 Financial Management
- Fund tracking and management
- Expense recording and reporting
- Financial transparency dashboard
- Blockchain-verified transactions
- Receipt upload and management
- Budget summaries by section

### 🗳️ Election System
- Position and candidate management
- Secure blockchain-based voting
- Real-time election status tracking
- Voting history and statistics
- Election result transparency

### 📅 Event Management
- Create and manage organization events
- Calendar view for students
- Event categories (Academic, Competition, Social, Workshop)
- Multi-day event support
- Event registration and tracking

### 📢 Announcements
- Real-time announcement system
- Priority alerts for critical updates
- Notification modal implementation
- Student-specific announcements

### 👥 Student Management
- Student registration with document verification
- Profile management
- Membership fee tracking
- Activity logs and audit trails
- Masterlist validation

## 🛠️ Technology Stack

### Backend
- **PHP 7.4+** - Server-side scripting
- **MySQL/MariaDB** - Database management
- **PDO** - Database abstraction layer
- **PHPMailer** - Email notifications
- **FPDF/FPDI** - PDF generation

### Frontend
- **HTML5/CSS3** - Structure and styling
- **JavaScript (ES6+)** - Client-side interactivity
- **Font Awesome** - Icons
- **Responsive Design** - Mobile-first approach

### Blockchain
- **Solidity** - Smart contract development
- **Web3.js** - Blockchain interaction
- **Ethereum** - Blockchain network
- **MetaMask** - Wallet integration

### Development Tools
- **XAMPP** - Local development environment
- **Composer** - PHP dependency management
- **Node.js** - Blockchain signer dependencies

## 📋 Prerequisites

- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10+
- Apache 2.4+ (or Nginx)
- Composer
- Node.js 18+ (for blockchain features)
- Web3 wallet (MetaMask) for blockchain interactions

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/rswlljms/soccs-blockchain-based-system.git
cd soccs-financial-management
```

### 2. Database Setup

1. Create a MySQL database:
```sql
CREATE DATABASE soccs_financial_management;
```

2. Import the database schema:
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Select the `soccs_financial_management` database
   - Import `soccs_financial_management.sql`
   - Or run individual SQL files from the `sql/` directory

### 3. Configure Database Connection

Edit `includes/database.php` with your database credentials:

```php
private $host = "localhost";
private $username = "your_username";
private $password = "your_password";
private $database = "soccs_financial_management";
```

### 4. Install PHP Dependencies

```bash
composer install
```

### 5. Install Node.js Dependencies (for Blockchain)

```bash
cd blockchain/blockchain-signer
npm install
```

### 6. Configure Email Settings

1. Copy `.env.example` to `.env` (if available)
2. Configure email settings in `includes/email_config.php`
3. See `docs/EMAIL_SETUP_GUIDE.md` for detailed instructions

### 7. Set Up Upload Directories

Ensure the following directories exist with proper permissions:
- `uploads/receipts/`
- `uploads/documents/`
- `uploads/student-ids/`
- `uploads/candidates/`
- `uploads/temp/`

Create `.gitkeep` files in each directory to maintain structure in version control.

### 8. Configure Web Server

#### Apache (.htaccess)
Ensure mod_rewrite is enabled and `.htaccess` files are processed.

#### Nginx
Configure appropriate rewrite rules for PHP routing.

## ⚙️ Configuration

### Environment Variables

Create a `.env` file in the root directory with your credentials:

```env
# OCR Space API Key (for document verification)
OCR_SPACE_API_KEY=your_ocr_space_api_key_here

# SMTP Configuration (Gmail)
smtp_username=your_email@gmail.com
smtp_password=your_gmail_app_password_here

# Blockchain Service URL
BLOCKCHAIN_URL=http://localhost:3001
```

**Important**: Never commit `.env` files to version control. See `docs/ENVIRONMENT_SETUP.md` for detailed configuration instructions.

### Blockchain Configuration

1. Deploy smart contracts following `blockchain/DEPLOYMENT_GUIDE.md`
2. Create `.env` file in `blockchain/blockchain-signer/` with:
   ```env
   CONTRACT_ADDRESS=your_deployed_contract_address
   INFURA_URL=https://sepolia.infura.io/v3/your_project_id
   PRIVATE_KEY=your_wallet_private_key
   ```
3. Configure RPC endpoints in blockchain signer configuration

## 📖 Documentation

Comprehensive documentation is available in the `docs/` directory:

- **[System Architecture](docs/SYSTEM_ARCHITECTURE.md)** - Overall system design and architecture
- **[Student Dashboard](docs/STUDENT_DASHBOARD_README.md)** - Student portal documentation
- **[Election Management](docs/ELECTION_MANAGEMENT_GUIDE.md)** - Election system guide
- **[Election Setup](docs/ELECTION_SETUP_GUIDE.md)** - Setting up elections
- **[Registration Setup](docs/REGISTRATION_SETUP.md)** - Student registration guide
- **[Email Setup](docs/EMAIL_SETUP_GUIDE.md)** - Email configuration
- **[Event Management](docs/EVENT_MANAGEMENT_GUIDE.md)** - Event system documentation
- **[Filing Candidacy](docs/FILING_CANDIDACY_GUIDE.md)** - Candidate registration guide
- **[Activity Log System](docs/ACTIVITY_LOG_SYSTEM.md)** - Audit trail documentation
- **[Environment Setup](docs/ENVIRONMENT_SETUP.md)** - Environment configuration guide
- **[Blockchain Deployment](blockchain/DEPLOYMENT_GUIDE.md)** - Smart contract deployment

## 🎮 Usage

### For Administrators

1. **Login**: Access admin panel at `templates/login.php`
2. **Manage Students**: Approve/reject student registrations
3. **Financial Management**: Add funds, record expenses, view reports
4. **Election Management**: Create elections, manage positions and candidates
5. **Event Management**: Create and manage organization events
6. **Announcements**: Post important updates for students

### For Students

1. **Register**: Complete registration form with required documents
2. **Login**: Use Student ID or email with password
3. **Dashboard**: View elections, events, financial transparency, and announcements
4. **Vote**: Participate in active elections securely
5. **Profile**: Update personal information and view activity history

## 📁 Project Structure

```
soccs-financial-management/
├── api/                    # REST API endpoints
│   ├── elections/         # Election APIs
│   ├── events/            # Event APIs
│   ├── candidates/        # Candidate APIs
│   └── users/             # User management APIs
├── assets/                # Static assets
│   ├── css/               # Stylesheets
│   ├── js/                # JavaScript files
│   └── img/               # Images and logos
├── auth/                  # Authentication handlers
├── blockchain/            # Blockchain integration
│   ├── contracts/         # Solidity smart contracts
│   └── blockchain-signer/ # Web3 integration
├── components/            # Reusable PHP components
├── docs/                  # Documentation
├── includes/              # Core PHP classes and utilities
├── pages/                 # Main application pages
├── scripts/               # Utility scripts
├── sql/                   # Database migration files
├── templates/             # Template files
├── uploads/               # User-uploaded files
├── composer.json          # PHP dependencies
├── package.json           # Node.js dependencies
└── README.md             # This file
```

## 🔒 Security Features

- **SQL Injection Prevention**: PDO prepared statements
- **XSS Protection**: Output escaping and sanitization
- **CSRF Protection**: Token-based form validation
- **Password Hashing**: Bcrypt encryption
- **Session Security**: Secure session management
- **File Upload Validation**: MIME type and size checks
- **Blockchain Verification**: Immutable transaction records
- **Access Control**: Role-based permissions

## 🤝 Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Code Standards

- Follow PSR-12 coding standards for PHP
- Use meaningful variable and function names
- Add comments for complex logic
- Write tests for new features
- Update documentation for changes

## 📝 License

This is a capstone project developed for the Student Organization of the College of Computer Studies (SOCCS) at LSPU – Santa Cruz Campus.

## 👥 Authors

**Developer:**
- Roswell James Vitaliz

**Documentation:**
- Christine Nicole Valdellon
- Fhammiell Noguera

## 🙏 Acknowledgments

- LSPU – Santa Cruz Campus
- College of Computer Studies
- SOCCS Organization Members

---

**Version**: 1.0.0  
**Last Updated**: January 2026  
**Project Type**: Capstone Project

