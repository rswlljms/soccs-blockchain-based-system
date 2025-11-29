# Election Management Guide

## Overview

The Election Management System allows administrators to create, start, and close elections. Students can only vote when an election is active, and the system automatically reflects the election status across all interfaces.

## Features

### Admin Features
1. **Create Elections** - Set up new elections with title, description, start date, and end date
2. **Start Elections** - Manually activate elections to allow voting
3. **Close Elections** - End voting and finalize results
4. **Edit Elections** - Modify election details (only for upcoming elections)
5. **Delete Elections** - Remove elections (cannot delete active elections)

### Student Features
1. **View Active Election** - See current election information on dashboard
2. **Vote** - Cast votes only when election is active
3. **View Results** - See election results after voting closes
4. **Real-time Countdown** - See time remaining until election ends

## How to Use

### Starting an Election

1. **Login as Admin**
   - Navigate to `http://localhost/soccs-financial-management/pages/elections.php`

2. **Create a New Election**
   - Click "New Election" button
   - Fill in the election details:
     - **Title**: e.g., "SOCCS General Elections 2025"
     - **Description**: Optional description
     - **Start Date & Time**: When voting should begin
     - **End Date & Time**: When voting should end
   - Click "Save Election"

3. **Start the Election**
   - Find the election in the list (status: "upcoming")
   - Click the "Start" button
   - Confirm the action
   - Election status changes to "active"

### What Happens When You Start an Election

- **Student Dashboard**: 
  - Shows the active election with live countdown
  - Displays election statistics (positions, candidates, eligible voters)
  - "Cast Your Vote" button becomes enabled

- **Voting Page**:
  - Students can access the voting page
  - Election title and end date are displayed
  - Students can submit their votes

### Closing an Election

1. **Navigate to Election Management**
   - Go to `http://localhost/soccs-financial-management/pages/elections.php`

2. **Close Active Election**
   - Find the active election in the list
   - Click the "Close" button
   - Confirm the action
   - Election status changes to "completed"

### What Happens When You Close an Election

- **Student Dashboard**:
  - Election card shows "No Active Election"
  - Students cannot access voting page

- **Voting Page**:
  - Redirects students away if they try to access it
  - Shows error message about no active election

- **Results Page**:
  - Election results become available
  - Students can view final vote counts

## Election Status Flow

```
upcoming → active → completed
    ↓
cancelled
```

- **upcoming**: Election created but not started yet
- **active**: Voting is open, students can cast votes
- **completed**: Voting has ended, results are available
- **cancelled**: Election was cancelled (manual action)

## Important Rules

1. **Only ONE active election** can exist at a time
2. **Cannot delete active elections** - must close them first
3. **Cannot edit active elections** - only upcoming elections can be modified
4. **End date must be after start date**
5. **Students must be logged in** to vote
6. **Each student can vote once** per position

## Database Structure

### Elections Table
```sql
CREATE TABLE elections (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  start_date DATETIME NOT NULL,
  end_date DATETIME NOT NULL,
  status ENUM('upcoming','active','completed','cancelled') DEFAULT 'upcoming',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## API Endpoints

### Read All Elections
- **URL**: `GET /api/elections/read.php`
- **Response**: List of all elections

### Get Active Election
- **URL**: `GET /api/elections/get_active.php`
- **Response**: Currently active election with statistics

### Create Election
- **URL**: `POST /api/elections/create.php`
- **Body**: `{ title, description, start_date, end_date }`

### Update Election
- **URL**: `POST /api/elections/update.php`
- **Body**: `{ id, title, description, start_date, end_date }`

### Update Election Status
- **URL**: `POST /api/elections/update_status.php`
- **Body**: `{ id, status }`
- **Status Options**: `upcoming`, `active`, `completed`, `cancelled`

### Delete Election
- **URL**: `POST /api/elections/delete.php`
- **Body**: `{ id }`

## Troubleshooting

### Students can't see the election
- Verify election status is "active" in admin panel
- Check that election end date hasn't passed
- Ensure students are logged in

### Can't start election
- Check that start/end dates are valid
- Verify no other election is currently active
- Ensure database connection is working

### Voting page shows error
- Confirm election is in "active" status
- Check that election hasn't ended
- Verify student authentication

## Security Notes

1. Only admins can manage elections
2. Students can only view and vote in active elections
3. All database queries use prepared statements
4. Input validation is performed on all API endpoints
5. CSRF protection should be implemented for production

## Next Steps

After setting up elections:
1. Add candidates to positions
2. Configure positions and maximum votes
3. Test voting flow with test student accounts
4. Monitor election in real-time
5. Close election and review results

