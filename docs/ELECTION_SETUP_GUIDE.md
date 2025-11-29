# Election Management System Setup Guide

## Overview
This guide will help you set up the Positions and Candidates management system with database integration.

## Features Implemented

### 1. Positions Management
- Create, Read, Update, Delete positions
- Set maximum votes per position
- Positions dynamically populate candidate form

### 2. Candidates Management
- Add candidates with photo upload
- Link candidates to positions (foreign key relationship)
- Filter candidates by position
- Search functionality
- Full CRUD operations

### 3. Database Integration
- Foreign key constraints
- Cascade updates
- Delete restrictions (cannot delete position with candidates)
- Automatic timestamp tracking

## Setup Instructions

### Step 1: Database Setup

1. Open phpMyAdmin: `http://localhost/phpmyadmin`

2. Select your database: `soccs_financial_management`

3. Go to the SQL tab and execute the contents of `sql/election_positions_candidates.sql`

   This will create:
   - `positions` table
   - `candidates` table
   - Default positions (President, Vice President, Secretary, Treasurer, Auditor, P.I.O.)

### Step 2: Upload Directory Setup

1. Run the setup script by visiting:
   ```
   http://localhost/soccs-financial-management/setup_uploads.php
   ```

2. This will create the necessary upload directories with proper permissions

3. You should see confirmation messages for:
   - `uploads/candidates/` directory
   - `.htaccess` file for security

### Step 3: Verify Database Connection

Make sure your `includes/database.php` has the correct credentials:
```php
private $host = "localhost";
private $username = "root";
private $password = "";
private $database = "soccs_financial_management";
```

### Step 4: Test the System

1. **Test Positions**
   - Navigate to: `pages/positions.php`
   - Add a new position (e.g., "Public Relations Officer")
   - Edit an existing position
   - Try deleting a position (should work if no candidates)

2. **Test Candidates**
   - Navigate to: `pages/add-candidate.php`
   - The position dropdown should now show all positions from the database
   - Add a new candidate with photo
   - Filter candidates by position
   - Search for candidates

## How It Works

### Position-Candidate Relationship

When you add a position in `pages/positions.php`:
1. Position is saved to the database
2. Position automatically appears in the candidate form dropdown
3. You can now assign candidates to that position

### Data Flow

```
User adds position → API (positions/create.php) → Database (positions table)
                                                           ↓
User adds candidate → API (candidates/create.php) → Database (candidates table)
                         ↑                                  ↓
                    position_id (foreign key)      Links to position
```

### File Structure

```
api/
├── positions/
│   ├── create.php    - Add new position
│   ├── read.php      - Get all positions
│   ├── update.php    - Update position
│   └── delete.php    - Delete position
└── candidates/
    ├── create.php    - Add new candidate
    ├── read.php      - Get all candidates
    ├── update.php    - Update candidate
    └── delete.php    - Delete candidate

sql/
└── election_positions_candidates.sql - Database schema

uploads/
└── candidates/       - Candidate photos
```

## API Endpoints

### Positions API

**GET** `api/positions/read.php`
- Returns all positions with id, description, maxVotes

**POST** `api/positions/create.php`
```json
{
  "description": "Position Name",
  "maxVotes": 1
}
```

**POST** `api/positions/update.php`
```json
{
  "id": 1,
  "description": "Updated Position Name",
  "maxVotes": 2
}
```

**POST** `api/positions/delete.php`
```json
{
  "id": 1
}
```

### Candidates API

**GET** `api/candidates/read.php`
- Returns all candidates with position details joined

**POST** `api/candidates/create.php`
- FormData with fields: firstname, lastname, partylist, position_id, platform, photo (file)

**POST** `api/candidates/update.php`
- FormData with fields: id, firstname, lastname, partylist, position_id, platform, photo (file, optional)

**POST** `api/candidates/delete.php`
```json
{
  "id": 1
}
```

## Troubleshooting

### Issue: Positions not appearing in dropdown
**Solution:** 
- Check browser console for errors
- Verify `api/positions/read.php` is returning data
- Check database has positions records

### Issue: Photo upload fails
**Solution:**
- Run `setup_uploads.php` to create directories
- Check folder permissions (should be 0755)
- Verify file size is under 5MB
- Check allowed file types: jpg, jpeg, png, gif

### Issue: Cannot delete position
**Solution:**
- This is expected if candidates are assigned to that position
- Delete or reassign candidates first
- This prevents orphaned candidate records

### Issue: Database connection error
**Solution:**
- Verify XAMPP MySQL is running
- Check `includes/database.php` credentials
- Ensure database `soccs_financial_management` exists

## Security Features

1. **SQL Injection Protection**: All queries use prepared statements with bound parameters
2. **File Upload Validation**: 
   - Type checking (only images allowed)
   - Size limits (5MB max)
   - Unique filenames (prevents overwrites)
3. **Input Sanitization**: All inputs are trimmed and validated
4. **Foreign Key Constraints**: Maintains referential integrity
5. **Directory Protection**: .htaccess prevents directory listing

## What Changed from Mock Data

### Before (Mock Data)
- Data stored in JavaScript arrays
- Lost on page refresh
- Position dropdown was hardcoded
- No relationship between positions and candidates

### After (Database)
- Data persisted in MySQL
- Positions and candidates linked via foreign key
- Positions dropdown populated dynamically from database
- Adding position immediately reflects in candidate form
- Photo uploads stored on server
- Full CRUD operations with proper error handling

## Next Steps

You can now:
1. Add positions as needed for your election
2. Register candidates for those positions
3. The data is persisted and will survive page refreshes
4. Positions automatically appear in the candidate form dropdown
5. Filter and search candidates efficiently

## Notes

- The search bar was removed from positions.php as requested
- Positions are sorted by ID (creation order)
- Candidates are sorted by newest first
- Default positions are automatically inserted on first run
- You can customize position list at any time

