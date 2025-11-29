# 📧 Gmail Email Setup - Quick Instructions

## ✅ Configuration Complete!

I've updated your email settings to use **roswelljamesvitaliz@gmail.com** for sending, but emails will appear to come from **lspuscc.soccs@gmail.com**.

---

## 🚀 Final Setup Steps (2 minutes)

### Step 1: Get Gmail App Password

1. **Go to your Google Account:**
   - Visit: https://myaccount.google.com/security
   - Or click your profile picture → "Manage your Google Account" → "Security"

2. **Enable 2-Step Verification** (if not already enabled):
   - Scroll to "How you sign in to Google"
   - Click "2-Step Verification"
   - Follow the setup steps

3. **Generate App Password:**
   - Go back to Security page
   - Scroll to "How you sign in to Google"
   - Click "App passwords" (you'll need to sign in again)
   - Select:
     - App: **Mail**
     - Device: **Windows Computer**
   - Click **Generate**
   - Copy the **16-character password** (e.g., `abcd efgh ijkl mnop`)

### Step 2: Update sendmail.ini

1. **Open this file:**
   ```
   C:\xampp\sendmail\sendmail.ini
   ```

2. **Find line 47 and replace with your App Password:**
   ```ini
   auth_password=YOUR_GMAIL_APP_PASSWORD_HERE
   ```
   
   Change to (use your actual 16-char password):
   ```ini
   auth_password=abcd efgh ijkl mnop
   ```

3. **Save the file**

### Step 3: Update php.ini

1. **Open this file:**
   ```
   C:\xampp\php\php.ini
   ```

2. **Find the [mail function] section and update:**
   ```ini
   [mail function]
   SMTP = smtp.gmail.com
   smtp_port = 587
   sendmail_from = lspuscc.soccs@gmail.com
   sendmail_path = "\"C:\xampp\sendmail\sendmail.exe\" -t"
   ```

3. **Save the file**

### Step 4: Restart Apache

1. Open **XAMPP Control Panel**
2. Click **Stop** on Apache
3. Wait 2 seconds
4. Click **Start** on Apache

---

## 🧪 Test Your Email Setup

### Option 1: Visual Preview (No sending)
```
http://localhost/soccs-financial-management/email_preview.html
```
See how your emails will look!

### Option 2: Send Test Email
```
http://localhost/soccs-financial-management/test_email.php
```

1. Enter your test email address (can be any Gmail)
2. Click "Send Test Email"
3. Check your inbox (and spam folder)

---

## 📋 Current Configuration Summary

| Setting | Value |
|---------|-------|
| **SMTP Server** | smtp.gmail.com |
| **SMTP Port** | 587 (TLS) |
| **Authentication Email** | roswelljamesvitaliz@gmail.com |
| **Display From Email** | lspuscc.soccs@gmail.com |
| **Display From Name** | SOCCS Admin |
| **Reply-To Email** | lspuscc.soccs@gmail.com |

### What This Means:
- ✅ Emails are **sent using** roswelljamesvitaliz@gmail.com (your account)
- ✅ Emails **appear to come from** lspuscc.soccs@gmail.com
- ✅ Recipients see "SOCCS Admin <lspuscc.soccs@gmail.com>"
- ✅ Replies go to lspuscc.soccs@gmail.com

---

## 📂 Files I've Updated

1. ✅ `c:\xampp\sendmail\sendmail.ini`
   - SMTP server: smtp.gmail.com
   - Port: 587
   - Username: roswelljamesvitaliz@gmail.com
   - Force sender: lspuscc.soccs@gmail.com
   - Debug logging enabled

2. ✅ `includes/email_config.php`
   - From: SOCCS Admin <lspuscc.soccs@gmail.com>
   - Reply-To: lspuscc.soccs@gmail.com

---

## ⚙️ What You Still Need to Do

**Only ONE thing left:**

### Update the App Password in sendmail.ini

1. Get your Gmail App Password (16 characters)
2. Open `C:\xampp\sendmail\sendmail.ini`
3. Replace `YOUR_GMAIL_APP_PASSWORD_HERE` on line 47
4. Save file
5. Restart Apache

**That's it!** 🎉

---

## 🔍 Troubleshooting

### Email not sending?

**Check these files:**

1. **Error Log:**
   ```
   C:\xampp\sendmail\error.log
   ```

2. **Debug Log:**
   ```
   C:\xampp\sendmail\debug.log
   ```

### Common Issues:

**"Authentication failed"**
- ✅ Make sure you're using the 16-character App Password (not your regular Gmail password)
- ✅ Remove any spaces from the App Password
- ✅ Verify 2-Step Verification is enabled

**"Connection timeout"**
- ✅ Check internet connection
- ✅ Try port 465 with SSL instead
- ✅ Check firewall/antivirus settings

**Email goes to spam**
- ✅ Recipients should mark as "Not Spam"
- ✅ This is normal when using personal Gmail for business emails
- ✅ For production, consider a professional email service

---

## 📧 Email Templates

You now have **2 professional email templates**:

### 1. Registration Confirmation
- **Color:** Purple (#B366FF)
- **Icon:** ✓ Checkmark
- **Purpose:** Sent immediately after registration
- **Content:** Welcome message, pending approval notice

### 2. Approval Notification
- **Color:** Green (#4CAF50)
- **Icon:** 🎉 Party
- **Purpose:** Sent when admin approves registration
- **Content:** Congratulations, login access, member benefits

Both match the **LSPU admission email style**!

---

## 🎯 Quick Test Commands

### Test 1: Preview Emails
```
http://localhost/soccs-financial-management/email_preview.html
```

### Test 2: Send Test Email
```
http://localhost/soccs-financial-management/test_email.php
```

### Test 3: Full Registration Flow
```
http://localhost/soccs-financial-management/pages/student-registration.php
```

---

## ✅ Checklist

**Setup:**
- [ ] Get Gmail App Password from Google Account
- [ ] Update `sendmail.ini` with your App Password
- [ ] Update `php.ini` with email settings
- [ ] Restart Apache in XAMPP

**Testing:**
- [ ] Preview emails at `email_preview.html`
- [ ] Send test email at `test_email.php`
- [ ] Check inbox (and spam folder)
- [ ] Verify email looks professional
- [ ] Verify sender shows as "SOCCS Admin"

**Production:**
- [ ] Test with real student registration
- [ ] Monitor error logs
- [ ] Ensure emails not going to spam

---

## 🎨 Email Features

Your emails include:
- ✅ Professional LSPU-style design
- ✅ Organization branding
- ✅ Colored gradient banners
- ✅ Personalized content
- ✅ Clean, mobile-responsive layout
- ✅ Professional footer
- ✅ Contact information

---

## 📞 Need Help?

1. **Preview emails first:** `email_preview.html`
2. **Check error logs:** `C:\xampp\sendmail\error.log`
3. **Test sending:** `test_email.php`
4. **Documentation:** `docs/EMAIL_TESTING_GUIDE.md`

---

## 🚀 You're Almost Done!

**Next steps:**
1. Get Gmail App Password (2 minutes)
2. Update sendmail.ini line 47
3. Update php.ini [mail function]
4. Restart Apache
5. Test at `test_email.php`

**Your professional LSPU-style emails are ready!** 📬✨

