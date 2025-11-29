# 🎨 How to Add Banner Image to Emails

## Current Setup
✅ Banner sections are ready to use images!  
⚠️ You need to upload your banner to a public URL (like Imgur)

---

## 📋 Option 1: Replace Banner with Full Image (Recommended)

### Step 1: Upload Banner Image
1. Go to: **https://imgur.com/upload**
2. Upload your banner image (recommended size: 600px × 180px)
3. Right-click → "Copy image address"
4. You'll get: `https://i.imgur.com/xyz789.png`

### Step 2: Update Registration Email Banner
1. Open: `includes/email_config.php`
2. Find **around line 292** (Registration email banner):
   ```html
   <!-- Banner -->
   <div class='banner'>
       <div class='banner-icon'>✓</div>
   </div>
   ```

3. **Replace with:**
   ```html
   <!-- Banner -->
   <img src='https://i.imgur.com/xyz789.png' alt='SOCCS Banner' class='banner-image'>
   ```

### Step 3: Update Approval Email Banner  
1. In same file, find **around line 78** (Approval email banner):
   ```html
   <div class='banner'>
       <div class='banner-icon'>🎉</div>
   </div>
   ```

2. **Replace with:**
   ```html
   <img src='https://i.imgur.com/abc123.png' alt='SOCCS Banner' class='banner-image'>
   ```
   (You can use same or different banner for approval)

### Step 4: Test
- Visit: `test_email.php`
- Send test email
- Banner image should appear! 🎉

---

## 📋 Option 2: Use Image as Background (Keep Icon)

If you want to show an icon/text over your banner image:

### Step 1: Upload Banner (Same as above)

### Step 2: Update Registration Email
Find around line 292:
```html
<div class='banner'>
    <div class='banner-icon'>✓</div>
</div>
```

**Replace with:**
```html
<div class='banner' style='background-image: url("https://i.imgur.com/xyz789.png");'>
    <div class='banner-icon'>✓</div>
</div>
```

### Step 3: Update Approval Email
Find around line 78:
```html
<div class='banner'>
    <div class='banner-icon'>🎉</div>
</div>
```

**Replace with:**
```html
<div class='banner' style='background-image: url("https://i.imgur.com/abc123.png");'>
    <div class='banner-icon'>🎉</div>
</div>
```

---

## 🎨 Banner Image Specifications

### Recommended Sizes:
- **Width:** 600px (email width)
- **Height:** 180px (banner height)
- **Format:** JPG or PNG
- **File size:** Under 500KB

### Design Tips:
- ✅ Use high contrast for text readability
- ✅ Keep important content centered
- ✅ Test on mobile devices
- ✅ Avoid tiny text (won't be readable)
- ✅ Use web-safe colors

### Color Themes:
- **Registration:** Purple/Violet theme (#B366FF)
- **Approval:** Green theme (#4CAF50)

---

## 📍 Where to Update in `includes/email_config.php`

### 1. Approval Email Banner (Line ~78):
```html
<!-- Search for this in approval email: -->
<div class='banner'>
    <div class='banner-icon'>🎉</div>
</div>
```

### 2. Registration Email Banner (Line ~292):
```html
<!-- Search for this in registration email: -->
<div class='banner'>
    <div class='banner-icon'>✓</div>
</div>
```

---

## 🎯 Quick Copy-Paste Examples

### Full Banner Image (No icon):
```html
<img src='https://i.imgur.com/YOUR_BANNER.png' alt='SOCCS Banner' class='banner-image'>
```

### Banner with Background Image + Icon:
```html
<div class='banner' style='background-image: url("https://i.imgur.com/YOUR_BANNER.png");'>
    <div class='banner-icon'>✓</div>
</div>
```

### Banner with Background Image + Custom Text:
```html
<div class='banner' style='background-image: url("https://i.imgur.com/YOUR_BANNER.png");'>
    <div style='color: white; font-size: 24px; font-weight: bold;'>Welcome to SOCCS!</div>
</div>
```

---

## 🔍 Troubleshooting

### Banner image not showing?
- ✅ Make sure URL is publicly accessible
- ✅ Check if URL ends with `.png` or `.jpg`
- ✅ Try opening URL in browser
- ✅ Ensure image is uploaded to Imgur (not album link)

### Wrong Imgur URL format?
❌ Wrong: `https://imgur.com/a/0fzpMaR` (album link)  
✅ Correct: `https://i.imgur.com/abc123.png` (direct image)

**How to get direct link:**
1. Upload to Imgur
2. **Right-click the image** (not the page)
3. "Copy image address"

### Image too large/small?
- Resize image to 600×180 pixels
- Use tools like Photoshop, Canva, or online resizers
- Recommended: https://www.resizepixel.com/

---

## 🎨 Design Options

### Option A: Simple Gradient (Current)
```html
<div class='banner'>
    <div class='banner-icon'>✓</div>
</div>
```
Result: Purple/green gradient with icon

### Option B: Full Banner Image
```html
<img src='URL' alt='Banner' class='banner-image'>
```
Result: Your custom banner image

### Option C: Background Image + Icon
```html
<div class='banner' style='background-image: url("URL")'>
    <div class='banner-icon'>✓</div>
</div>
```
Result: Your image with icon overlay

---

## 🚀 Quick Steps Summary

1. **Create/Design** banner image (600×180px)
2. **Upload** to Imgur
3. **Copy** direct image URL (`https://i.imgur.com/...`)
4. **Update** `includes/email_config.php` (2 locations)
5. **Test** at `test_email.php`
6. **Done!** 🎉

---

## 📧 Current Banner Locations

| Email Type | Line Number | Current |
|------------|-------------|---------|
| Approval Email | ~78 | Green gradient + 🎉 |
| Registration Email | ~292 | Purple gradient + ✓ |

---

## 💡 Pro Tips

1. **Use different banners** for different email types:
   - Registration: Welcome/Purple theme
   - Approval: Celebration/Green theme

2. **Test email compatibility:**
   - Send to Gmail, Outlook, Yahoo
   - Check on mobile devices

3. **Keep file sizes small:**
   - Compress images before upload
   - Use JPEG for photos, PNG for graphics

4. **Brand consistency:**
   - Match your website/portal design
   - Use official SOCCS colors
   - Include organization elements

---

## ✅ Checklist

**Setup:**
- [ ] Design banner image (600×180px)
- [ ] Upload to Imgur
- [ ] Get direct image URL (right-click → copy)
- [ ] Update approval email banner (~line 78)
- [ ] Update registration email banner (~line 292)
- [ ] Save `includes/email_config.php`

**Testing:**
- [ ] Send test email
- [ ] Check banner displays correctly
- [ ] Test on mobile
- [ ] Verify image loads quickly

---

## 📝 Example Configuration

```html
<!-- APPROVAL EMAIL (Green Theme) -->
<img src='https://i.imgur.com/approval-banner.png' alt='SOCCS Approval Banner' class='banner-image'>

<!-- REGISTRATION EMAIL (Purple Theme) -->
<img src='https://i.imgur.com/registration-banner.png' alt='SOCCS Registration Banner' class='banner-image'>
```

---

## 🎉 You're Ready!

Your banner sections are now ready to display custom images. Just upload to Imgur and update the URLs!

For any issues, check that:
1. Image URL is direct (ends with .png or .jpg)
2. URL is publicly accessible
3. Image dimensions are appropriate (600×180px recommended)

Happy designing! 🎨✨

