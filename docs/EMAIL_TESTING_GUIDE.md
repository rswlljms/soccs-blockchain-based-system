# 📧 Email Testing Guide - Send to Real Gmail Account

## Quick Steps to Test Email

### Step 1: Configure Email Settings (One-time setup)

1. **Open sendmail.ini** (`C:\xampp\sendmail\sendmail.ini`)
   ```ini
   smtp_server=smtp.gmail.com
   smtp_port=587
   auth_username=lspuscc.soccs@gmail.com
   auth_password=djyp uewk poeh fkxy
   force_sender=lspuscc.soccs@gmail.com
   ```

2. **Get Gmail App Password:**
   - Visit: https://myaccount.google.com/security
   - Click "2-Step Verification" (enable if not yet)
   - Click "App passwords"
   - Select "Mail" and "Windows Computer" 
   - Copy the 16-character password
   - Paste it in `sendmail.ini` as `auth_password`

3. **Edit php.ini** (`C:\xampp\php\php.ini`)
   ```ini
   SMTP = smtp.gmail.com
   smtp_port = 587
   sendmail_from = lspuscc.soccs@gmail.com
   sendmail_path = "\"C:\xampp\sendmail\sendmail.exe\" -t"
   ```

4. **Restart Apache** in XAMPP Control Panel

---

### Step 2: Test Email Sending

#### Option A: Use Test Email Tool (Recommended)

1. Open browser and go to:
   ```
   http://localhost/soccs-financial-management/test_email.php
   ```

2. Enter your real Gmail address (e.g., `yourname@gmail.com`)

3. Click **"📨 Send Test Email"**

4. Check your Gmail inbox (and spam folder)

5. You should receive a professional LSPU-style email!

#### Option B: Test via Student Registration

1. Go to registration page:
   ```
   http://localhost/soccs-financial-management/pages/student-registration.php
   ```

2. Fill in the form with your real Gmail address

3. Submit the registration

4. Check your Gmail for the confirmation email

---

### Step 3: What the Email Looks Like

Your email will have:

✅ **Professional Header**
- SOCCS logo (📚 icon)
- Organization name
- Tagline: "EXCELLENCE • INNOVATION • LEADERSHIP"

✅ **Colored Banner**
- Purple gradient (✓ checkmark icon)

✅ **Personalized Content**
- Greeting with your name
- Registration details
- Student ID and email
- Status information

✅ **Professional Footer**
- Contact information
- System-generated message notice
- Blue footer with copyright

✅ **Mobile Responsive**
- Looks great on phones and tablets

---

## Email Preview

The email template matches the LSPU admission email style:

```
┌─────────────────────────────────┐
│           📚 Logo               │
│  Student Organization of the    │
│  College of Computer Studies    │
│  EXCELLENCE • INNOVATION • ...  │
├─────────────────────────────────┤
│                                 │
│     [Purple Banner with ✓]      │
│                                 │
├─────────────────────────────────┤
│                                 │
│  Good day, [Name] [ID]!         │
│                                 │
│  Thank you for registering...   │
│                                 │
│  ┌─────────────────────────┐   │
│  │ Registration Details:    │   │
│  │ Student ID: ST2024001    │   │
│  │ Email: email@gmail.com   │   │
│  │ Status: Pending          │   │
│  └─────────────────────────┘   │
│                                 │
│  Keep safe and stay connected.  │
│                                 │
├─────────────────────────────────┤
│  System generated message       │
│  Contact: lspuscc.soccs@...     │
├─────────────────────────────────┤
│     [Blue Footer]               │
│     SOCCS © 2024                │
└─────────────────────────────────┘
```

---

## Troubleshooting

### ❌ Email not received?

**Check these:**

1. ✅ Spam/Junk folder
2. ✅ Gmail App Password is correct (16 characters, no spaces)
3. ✅ sendmail.ini saved properly
4. ✅ Apache restarted after config changes
5. ✅ Internet connection active

**Check error logs:**
- `C:\xampp\sendmail\error.log`
- `C:\xampp\sendmail\debug.log`

### ❌ Connection timeout?

**Solutions:**
1. Try port 465 with SSL:
   ```ini
   smtp_port=465
   smtp_ssl=ssl
   ```

2. Check firewall settings
3. Verify antivirus not blocking

### ❌ Authentication failed?

**Solutions:**
1. Regenerate Gmail App Password
2. Ensure 2-Step Verification enabled
3. Use lspuscc.soccs@gmail.com account
4. Check for typos in password

---

## Testing Checklist

Before testing:
- [ ] sendmail.ini configured
- [ ] Gmail App Password obtained
- [ ] php.ini configured
- [ ] Apache restarted

During testing:
- [ ] Visit test_email.php
- [ ] Enter real Gmail address
- [ ] Click send
- [ ] Check email received

Email validation:
- [ ] Professional header visible
- [ ] Purple banner displays
- [ ] Content properly formatted
- [ ] Footer information correct
- [ ] Email not in spam
- [ ] Mobile responsive

---

## Advanced: Using Different SMTP Services

### Gmail (Current setup)
```ini
smtp_server=smtp.gmail.com
smtp_port=587
```

### Outlook/Office365
```ini
smtp_server=smtp.office365.com
smtp_port=587
```

### Yahoo Mail
```ini
smtp_server=smtp.mail.yahoo.com
smtp_port=587
```

### Custom SMTP
```ini
smtp_server=your-smtp-server.com
smtp_port=587
auth_username=your-email@domain.com
auth_password=your-password
```

---

## Email Service Features

### 1. Registration Confirmation Email
- **Trigger:** Student submits registration
- **Subject:** "SOCCS Registration - Confirmation"
- **Purpose:** Confirm registration received, pending approval
- **Color Theme:** Purple (#B366FF)

### 2. Approval Notification Email
- **Trigger:** Admin approves registration
- **Subject:** "SOCCS Registration - Application Approved!"
- **Purpose:** Welcome student, provide login access
- **Color Theme:** Green (#4CAF50)

---

## Production Deployment Notes

For production/live server:

1. **Use real domain in email links:**
   - Update login URL to your actual domain
   - Replace `http://localhost/...` with `https://yourdomain.com/...`

2. **Professional email sender:**
   - Use official organization email
   - Set up SPF and DKIM records
   - Configure DMARC policy

3. **Email service providers (Recommended):**
   - SendGrid (Free tier: 100 emails/day)
   - Mailgun (Free tier: 5,000 emails/month)
   - Amazon SES (Very cheap, highly reliable)

4. **Monitoring:**
   - Track email delivery rates
   - Monitor bounce rates
   - Set up email notifications for failures

---

## Quick Command Reference

### Restart Apache (XAMPP)
1. Open XAMPP Control Panel
2. Click "Stop" on Apache
3. Wait 2 seconds
4. Click "Start" on Apache

### Check if email sent
```bash
# Check sendmail log
type C:\xampp\sendmail\error.log

# Check debug log
type C:\xampp\sendmail\debug.log
```

### Clear logs
```bash
# Delete error log
del C:\xampp\sendmail\error.log

# Delete debug log
del C:\xampp\sendmail\debug.log
```

---

## Support

Need help?
- 📖 See: `EMAIL_SETUP_GUIDE.md` for detailed setup
- 🧪 Use: `test_email.php` for testing
- 📧 Contact: lspuscc.soccs@gmail.com

---

## Summary

✅ **You now have professional LSPU-style emails!**

The emails include:
- Professional header with branding
- Colored gradient banners
- Personalized content
- Clean, mobile-responsive design
- Professional footer

**Test it now:**
1. Go to `test_email.php`
2. Enter your Gmail
3. Click send
4. Check your inbox! 📬

