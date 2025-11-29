# Quick Start Guide: Election Management

## Setup Instructions

### 1. Database Setup

Make sure the `elections` table exists in your database. Run this SQL if needed:

```sql
CREATE TABLE IF NOT EXISTS `elections` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(255) NOT NULL,
  `description` text,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` enum('upcoming','active','completed','cancelled') DEFAULT 'upcoming',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

### 2. Start an Election (Admin)

1. **Login as Admin** and go to:
   ```
   http://localhost/soccs-financial-management/pages/elections.php
   ```

2. **Click "New Election"** button

3. **Fill in the form:**
   - Title: `SOCCS General Elections 2025`
   - Description: `Annual elections for student organization officers`
   - Start Date: Choose current or future date/time
   - End Date: Choose a date/time after start date

4. **Click "Save Election"**

5. **Click "Start"** button next to the election to activate it

### 3. How It Reflects on Student Side

Once you click **"Start"**, the following happens automatically:

#### Student Dashboard (`student-dashboard.php`)
- ✅ Shows the active election with its title
- ✅ Displays real-time countdown to election end
- ✅ Shows statistics: positions, candidates, eligible voters
- ✅ "Cast Your Vote" button becomes available
- ✅ Election status badge shows "Election Live"

#### Student Voting Page (`student-voting.php`)
- ✅ Accessible only when election is active
- ✅ Shows election title and end date
- ✅ Students can submit their votes
- ✅ Redirects if no active election

### 4. Close an Election (Admin)

1. Go back to: `http://localhost/soccs-financial-management/pages/elections.php`

2. Find the **active election** in the list

3. Click the **"Close"** button

4. Confirm the action

### 5. What Happens When You Close

Once you click **"Close"**, the following happens automatically:

#### Student Dashboard
- ❌ Shows "No Active Election" message
- ❌ "Cast Your Vote" button is hidden
- ℹ️ Shows message: "There are currently no ongoing elections"

#### Student Voting Page
- ❌ Redirects students away from voting page
- ❌ Shows error message about no active election

## Quick Test

### Test Starting an Election:

1. **Admin Side:** Create and start an election
2. **Student Side:** Open `student-dashboard.php` → Should see the election
3. **Student Side:** Click "Cast Your Vote" → Should open voting page

### Test Closing an Election:

1. **Admin Side:** Close the active election
2. **Student Side:** Refresh `student-dashboard.php` → Should see "No Active Election"
3. **Student Side:** Try accessing `student-voting.php` → Should redirect

## File Structure

```
soccs-financial-management/
├── pages/
│   ├── elections.php              (Admin: Manage Elections)
│   ├── student-dashboard.php       (Student: View Active Election)
│   └── student-voting.php          (Student: Cast Vote)
├── api/
│   └── elections/
│       ├── read.php               (Get all elections)
│       ├── get_active.php         (Get active election)
│       ├── create.php             (Create new election)
│       ├── update.php             (Update election details)
│       ├── update_status.php      (Start/Close election)
│       └── delete.php             (Delete election)
├── assets/
│   ├── css/
│   │   └── elections.css          (Election management styles)
│   └── js/
│       ├── elections.js           (Admin election management)
│       └── student-dashboard.js   (Student dashboard with election)
└── docs/
    └── ELECTION_MANAGEMENT_GUIDE.md
```

## Key Features

### Admin Features
- ✅ Create multiple elections
- ✅ Only ONE election can be active at a time
- ✅ Start elections manually
- ✅ Close elections manually
- ✅ Edit upcoming elections
- ✅ Delete upcoming/completed elections
- ❌ Cannot delete active elections

### Student Features  
- ✅ See active election automatically
- ✅ Real-time countdown timer
- ✅ Vote only when election is active
- ✅ View election statistics
- ✅ Blocked from voting if no active election

## Troubleshooting

**Q: Students don't see the election**
- A: Make sure you clicked "Start" in admin panel
- A: Check election status is "active" not "upcoming"
- A: Refresh the student dashboard page

**Q: Can't start the election**
- A: Check that another election isn't already active
- A: Verify start_date is before end_date
- A: Make sure database connection is working

**Q: Student voting page shows error**
- A: Verify an election is "active" in admin panel
- A: Check election hasn't passed its end_date

## Next Steps

After setting up elections:
1. ✅ Add positions via `positions.php`
2. ✅ Add candidates via `add-candidate.php`
3. ✅ Test voting flow
4. ✅ Close election when done
5. ✅ View results

---

**Need more details?** See `ELECTION_MANAGEMENT_GUIDE.md` for complete documentation.

