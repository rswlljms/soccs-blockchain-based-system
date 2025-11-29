# 🎨 How to Add Your Official SOCCS Logo to Emails

## Current Status
✅ Email templates are ready to use your logo!  
⚠️ You need to upload your logo to a public URL

---

## 📋 Option 1: Upload Logo to Imgur (Easiest - 2 minutes)

### Step 1: Upload to Imgur
1. Go to: **https://imgur.com/upload**
2. Click **"New post"** or drag your logo file (`assets/img/logo.png`)
3. After upload, **right-click** on the image
4. Click **"Copy image address"**
5. You'll get a URL like: `https://i.imgur.com/abc123.png`

### Step 2: Update Email Config
1. Open: `includes/email_config.php`
2. Find these lines (appears twice - around lines 73 and 286):
   ```php
   <img src='https://i.imgur.com/YourLogoHere.png'
   ```
3. Replace with your Imgur URL:
   ```php
   <img src='https://i.imgur.com/abc123.png'
   ```
4. Save the file

### Step 3: Test
- Send a test email at: `test_email.php`
- Your logo should now appear! 🎉

---

## 📋 Option 2: Use Base64 Encoded Image (Works Offline)

### Step 1: Convert Logo to Base64
1. Go to: **https://www.base64-image.de/**
2. Upload your logo (`assets/img/logo.png`)
3. Click **"copy image"**
4. Copy the entire base64 string

### Step 2: Update Email Config
1. Open: `includes/email_config.php`
2. Find:
   ```php
   <img src='https://i.imgur.com/YourLogoHere.png'
   ```
3. Replace with:
   ```php
   <img src='data:image/png;base64,YOUR_BASE64_STRING_HERE'
   ```
4. Save the file

**Note:** Base64 strings are very long - that's normal!

---

## 📋 Option 3: Host on Your Server (For Production)

### If you have a website/server:
1. Upload logo to your server: `https://yourwebsite.com/images/logo.png`
2. Update email config:
   ```php
   <img src='https://yourwebsite.com/images/logo.png'
   ```

---

## 🎯 Quick Fix - Copy & Paste Ready

### For Imgur (Replace URL):
```php
<img src='https://i.imgur.com/YOUR_IMAGE_ID.png' alt='SOCCS Logo' class='logo' style='max-width: 80px; height: auto; margin-bottom: 15px;' onerror="this.style.display='none'; this.parentElement.innerHTML+='<div style=font-size:60px;margin-bottom:15px>📚</div>';">
```

### For Base64:
```php
<img src='data:image/png;base64,iVBORw0KGgoAAAANS...(your full base64 string)' alt='SOCCS Logo' class='logo' style='max-width: 80px; height: auto; margin-bottom: 15px;'>
```

---

## 📍 Where to Update

Update **2 locations** in `includes/email_config.php`:

1. **Line ~286** - Registration Confirmation Email
2. **Line ~73** - Approval Notification Email

Search for: `<img src='https://i.imgur.com/YourLogoHere.png'`

---

## ✅ After Adding Logo

Test your email:
1. Visit: `http://localhost/soccs-financial-management/test_email.php`
2. Enter your email
3. Send test email
4. Check inbox - logo should appear!

---

## 🔍 Troubleshooting

### Logo not showing?
- ✅ Make sure URL is publicly accessible
- ✅ Check if URL ends with `.png`, `.jpg`, or `.jpeg`
- ✅ Try opening the URL in a new browser tab
- ✅ Clear browser cache

### Using local file path won't work!
❌ Don't use: `../assets/img/logo.png`  
❌ Don't use: `C:\xampp\htdocs\...\logo.png`  
✅ Use: Public URL or Base64 encoding

---

## 📧 Current Logo Setup

Your email templates currently have:
```
Placeholder URL → https://i.imgur.com/YourLogoHere.png
Fallback → 📚 emoji (shows if logo fails to load)
```

Just replace the URL and you're done! 🎨

---

## 🚀 Recommended: Use Imgur

**Why Imgur?**
- ✅ Free and fast
- ✅ Reliable hosting
- ✅ Works in all email clients
- ✅ No signup required
- ✅ Permanent links

**Quick Steps:**
1. Upload to Imgur
2. Copy image URL
3. Replace in email config
4. Test!

---

## 📝 Summary

1. **Choose method:** Imgur (easiest) or Base64
2. **Get logo URL** or base64 string
3. **Update** `includes/email_config.php` (2 locations)
4. **Test** at `test_email.php`
5. **Done!** ✨

Your SOCCS logo will now appear in all registration emails!

