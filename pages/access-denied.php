<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Access Denied</title>
    <link rel="icon" href="../assets/img/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Work Sans', sans-serif; }
        body { 
            background: #f7f8fc; 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
        }
        .container {
            text-align: center;
            padding: 60px 40px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            max-width: 500px;
        }
        .icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }
        .icon i { font-size: 48px; color: #ef4444; }
        h1 { font-size: 28px; color: #1f2937; margin-bottom: 12px; }
        p { color: #6b7280; font-size: 16px; margin-bottom: 32px; line-height: 1.6; }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            background: linear-gradient(135deg, #4B0082, #9933ff);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(153,51,255,0.3); }
        .role-info {
            margin-top: 24px;
            padding: 16px;
            background: #f9fafb;
            border-radius: 8px;
            font-size: 14px;
            color: #6b7280;
        }
        .role-info strong { color: #1f2937; }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">
            <i class="fas fa-lock"></i>
        </div>
        <h1>Access Denied</h1>
        <p>You don't have permission to access this page. Please contact your administrator if you believe this is an error.</p>
        <a href="dashboard.php" class="btn">
            <i class="fas fa-home"></i> Go to Dashboard
        </a>
        <?php if (isset($_SESSION['user_role'])): ?>
        <div class="role-info">
            Your current role: <strong><?= ucwords(str_replace('_', ' ', $_SESSION['user_role'])) ?></strong>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>

