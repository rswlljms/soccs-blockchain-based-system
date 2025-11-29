<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration System Test | SOCCS</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/png" href="assets/img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Work Sans', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem;
            margin: 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 3rem;
        }
        .header h1 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        .header p {
            color: #6c757d;
            font-size: 1.1rem;
        }
        .test-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .test-section h2 {
            color: #2c3e50;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .test-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
        }
        .test-link {
            display: block;
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            color: #2c3e50;
            border: 2px solid #e9ecef;
            transition: all 0.3s;
            text-align: center;
        }
        .test-link:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .test-link i {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #667eea;
        }
        .test-link h3 {
            margin: 0 0 0.5rem 0;
            font-size: 1.2rem;
        }
        .test-link p {
            margin: 0;
            color: #6c757d;
            font-size: 0.9rem;
        }
        .status-info {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 2rem;
        }
        .status-info h3 {
            color: #155724;
            margin: 0 0 0.5rem 0;
        }
        .status-info p {
            color: #155724;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-graduation-cap"></i> Student Registration System</h1>
            <p>Complete registration and approval workflow for SOCCS</p>
        </div>

        <div class="status-info">
            <h3><i class="fas fa-info-circle"></i> System Status</h3>
            <p>✅ Registration form is functional | ✅ Admin approval interface is ready | ✅ Email notifications configured | ✅ Database schema updated</p>
        </div>

        <div class="test-section">
            <h2><i class="fas fa-user-plus"></i> Student Registration</h2>
            <div class="test-links">
                <a href="pages/student-registration.php" class="test-link">
                    <i class="fas fa-user-plus"></i>
                    <h3>Student Registration Form</h3>
                    <p>Complete registration form with file uploads and validation</p>
                </a>
            </div>
        </div>

        <div class="test-section">
            <h2><i class="fas fa-user-check"></i> Admin Management</h2>
            <div class="test-links">
                <a href="pages/student-approvals.php" class="test-link">
                    <i class="fas fa-user-check"></i>
                    <h3>Student Approvals</h3>
                    <p>Review, approve, or reject student registrations</p>
                </a>
                <a href="pages/students.php" class="test-link">
                    <i class="fas fa-users"></i>
                    <h3>Active Students</h3>
                    <p>View all approved and active students</p>
                </a>
            </div>
        </div>

        <div class="test-section">
            <h2><i class="fas fa-sign-in-alt"></i> Authentication</h2>
            <div class="test-links">
                <a href="templates/login.php" class="test-link">
                    <i class="fas fa-sign-in-alt"></i>
                    <h3>Login System</h3>
                    <p>Student and admin login with approval status checking</p>
                </a>
            </div>
        </div>

        <div class="test-section">
            <h2><i class="fas fa-cogs"></i> System Features</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                <div style="background: white; padding: 1rem; border-radius: 8px; border-left: 4px solid #28a745;">
                    <h4 style="margin: 0 0 0.5rem 0; color: #28a745;"><i class="fas fa-check"></i> Registration Features</h4>
                    <ul style="margin: 0; padding-left: 1.2rem; color: #6c757d; font-size: 0.9rem;">
                        <li>Complete student information form</li>
                        <li>File upload for Student ID</li>
                        <li>Certificate of Registration upload</li>
                        <li>Email validation and confirmation</li>
                    </ul>
                </div>
                
                <div style="background: white; padding: 1rem; border-radius: 8px; border-left: 4px solid #17a2b8;">
                    <h4 style="margin: 0 0 0.5rem 0; color: #17a2b8;"><i class="fas fa-user-check"></i> Admin Features</h4>
                    <ul style="margin: 0; padding-left: 1.2rem; color: #6c757d; font-size: 0.9rem;">
                        <li>View all registration requests</li>
                        <li>Approve or reject applications</li>
                        <li>Add rejection reasons</li>
                        <li>Email notifications</li>
                    </ul>
                </div>
                
                <div style="background: white; padding: 1rem; border-radius: 8px; border-left: 4px solid #ffc107;">
                    <h4 style="margin: 0 0 0.5rem 0; color: #ffc107;"><i class="fas fa-shield-alt"></i> Security Features</h4>
                    <ul style="margin: 0; padding-left: 1.2rem; color: #6c757d; font-size: 0.9rem;">
                        <li>Password hashing</li>
                        <li>File type validation</li>
                        <li>Approval status checking</li>
                        <li>Session management</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
