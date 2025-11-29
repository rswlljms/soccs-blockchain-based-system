<?php
/**
 * Test Banner Display
 * Quick test to see if banner image loads correctly
 */

echo "<!DOCTYPE html>
<html>
<head>
    <title>Banner Test - SOCCS</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f0f0f0; }
        .test-container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .banner-image { width: 100%; height: 180px; object-fit: cover; display: block; border: 2px solid #ddd; }
        .status { padding: 15px; margin: 15px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
    </style>
</head>
<body>
    <div class='test-container'>
        <h1>🎨 Banner Image Test</h1>
        
        <div class='info'>
            <strong>Testing:</strong> https://i.imgur.com/cRtxCnL.jpeg
        </div>
        
        <h3>Banner Image Preview:</h3>
        <img src='https://i.imgur.com/cRtxCnL.jpeg' alt='SOCCS Banner' class='banner-image' onload=\"document.getElementById('status').innerHTML='✅ Banner image loaded successfully!'; document.getElementById('status').className='status success';\" onerror=\"document.getElementById('status').innerHTML='❌ Banner image failed to load. Check URL or internet connection.'; document.getElementById('status').className='status error';\">
        
        <div id='status' class='status info'>
            🔄 Loading banner image...
        </div>
        
        <div class='info' style='margin-top: 20px;'>
            <strong>If banner shows above:</strong> The image URL is working correctly.<br>
            <strong>If banner doesn't show:</strong> There might be a network issue or the URL is incorrect.
        </div>
        
        <div style='margin-top: 20px; padding: 15px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px;'>
            <strong>Next Steps:</strong><br>
            1. If banner shows here → Test email at <a href='test_email.php'>test_email.php</a><br>
            2. If banner doesn't show → Check internet connection or try different URL<br>
            3. Clear browser cache and try again
        </div>
    </div>
</body>
</html>";
?>
