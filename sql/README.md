# Database Setup Instructions

## Election Management System - Positions and Candidates

### Steps to Set Up the Database

1. **Open phpMyAdmin** in your browser:
   - URL: `http://localhost/phpmyadmin`
   - Login with your MySQL credentials

2. **Select Your Database**:
   - Click on your database name (e.g., `soccs_financial_management`)

3. **Import the SQL Files**:
   - Click on the "SQL" tab at the top
   - Copy and paste the contents of `election_positions_candidates.sql`
   - Click "Go" to execute
   - For Filing of Candidacy feature, also run `create_filing_candidacy_table.sql`

### Database Structure

#### Positions Table
- `id`: Auto-incrementing primary key
- `description`: Position name (unique)
- `max_votes`: Maximum number of votes allowed for this position
- `created_at`: Timestamp when created
- `updated_at`: Timestamp when last updated

#### Candidates Table
- `id`: Auto-incrementing primary key
- `firstname`: Candidate's first name
- `lastname`: Candidate's last name
- `partylist`: Party affiliation
- `position_id`: Foreign key reference to positions table
- `platform`: Candidate's platform statement
- `photo`: Path to candidate photo (optional)
- `created_at`: Timestamp when created
- `updated_at`: Timestamp when last updated

### Key Features

1. **Foreign Key Constraint**: Candidates are linked to positions via `position_id`
2. **Cascade Updates**: If a position ID changes, candidate records update automatically
3. **Delete Restriction**: Cannot delete a position if candidates are registered for it
4. **Default Positions**: Automatically inserts 6 default positions (President, Vice President, Secretary, Treasurer, Auditor, P.I.O.)

### API Endpoints

#### Positions
- `GET api/positions/read.php` - Get all positions
- `POST api/positions/create.php` - Create new position
- `POST api/positions/update.php` - Update existing position
- `POST api/positions/delete.php` - Delete position (if no candidates)

#### Candidates
- `GET api/candidates/read.php` - Get all candidates with position details
- `POST api/candidates/create.php` - Create new candidate (with photo upload)
- `POST api/candidates/update.php` - Update existing candidate
- `POST api/candidates/delete.php` - Delete candidate

#### Filing of Candidacy
- `GET api/filing-candidacy/read.php` - Get all filing periods
- `GET api/filing-candidacy/get_active.php` - Get currently active filing period
- `POST api/filing-candidacy/create.php` - Create new filing period
- `POST api/filing-candidacy/update.php` - Update existing filing period
- `POST api/filing-candidacy/delete.php` - Delete filing period
- `POST api/filing-candidacy/toggle_status.php` - Activate/deactivate filing period
- `POST api/candidates/create.php` - Create new candidate (with photo upload)
- `POST api/candidates/update.php` - Update existing candidate
- `POST api/candidates/delete.php` - Delete candidate

### File Upload Directory

The system will automatically create the directory:
- `uploads/candidates/` - For candidate photos

Make sure your web server has write permissions for the `uploads` directory.

