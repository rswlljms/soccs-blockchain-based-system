<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Organization of the College of Computer Studies | Registration</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: url('../assets/img/bg-login.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            z-index: 0;
        }

        .registration-wrapper {
            width: 100%;
            max-width: 1100px;
            display: flex;
            background: white;
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            position: relative;
            z-index: 1;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .map-section {
            flex: 0 0 400px;
            position: relative;
            background: linear-gradient(135deg, #e9d5ff 0%, #ddd6fe 100%);
            border-right: 1px solid #e5e7eb;
            overflow: hidden;
        }

        #dotCanvas {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }

        .map-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            z-index: 10;
        }

        .map-logo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 30px rgba(147, 51, 234, 0.2), 0 0 0 3px rgba(147, 51, 234, 0.1);
            animation: fadeInDown 0.7s ease;
        }

        .map-logo img {
            width: 75px;
            height: 75px;
            object-fit: contain;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .map-title {
            font-size: 1.125rem;
            font-weight: 600;
            background: linear-gradient(135deg, #7c3aed 0%, #9333ea 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-align: center;
            max-width: 320px;
            line-height: 1.6;
            animation: fadeInDown 0.8s ease;
        }

        .map-section .back-button {
            position: absolute;
            top: 1rem;
            left: 1rem;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #e5e7eb;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            z-index: 15;
        }

        .back-button {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #e5e7eb;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            z-index: 15;
        }

        .back-button:hover {
            background: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .back-button i {
            color: #9333ea;
            font-size: 1rem;
        }

        .form-section {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
            max-height: 700px;
        }

        .form-header {
            margin-bottom: 1.5rem;
        }

        .form-header h1 {
            font-size: 1.875rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }

        .form-header p {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .section-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1f2937;
            margin: 1.5rem 0 1rem 0;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #9333ea;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-title i {
            color: #9333ea;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.25rem;
        }

        label .required {
            color: #9333ea;
        }

        .input-wrapper {
            position: relative;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"] {
            width: 100%;
            padding: 0.625rem 0.75rem;
            font-size: 0.875rem;
            color: #1f2937;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        select {
            width: 100%;
            padding: 0.625rem 2.5rem 0.625rem 0.75rem;
            font-size: 0.875rem;
            color: #1f2937;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-color: #f9fafb;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 14px 14px;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        input:focus {
            outline: none;
            background-color: white;
            border-color: #9333ea;
            box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.1);
        }

        select:focus {
            outline: none;
            background-color: white;
            border-color: #9333ea;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%239333ea' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        }

        input::placeholder {
            color: #9ca3af;
        }

        select option {
            background-color: white;
            color: #1f2937;
            padding: 0.5rem;
        }

        .file-upload-container {
            margin-top: 0.5rem;
        }

        .file-upload-input {
            display: none;
        }

        .file-upload-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: #f9fafb;
            border: 2px dashed #d1d5db;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }

        .file-upload-label:hover,
        .file-upload-label.dragover {
            background: #f3f4f6;
            border-color: #9333ea;
        }

        .file-upload-label i {
            color: #9333ea;
            font-size: 1.5rem;
            margin-right: 0.5rem;
        }

        .file-upload-label span {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .file-upload-label.has-file {
            background: #f0fdf4;
            border-color: #22c55e;
        }

        .file-upload-label.has-file i {
            color: #22c55e;
        }

        .file-upload-label.has-file span {
            color: #166534;
            font-weight: 500;
        }

        .file-info {
            margin-top: 0.25rem;
            font-size: 0.75rem;
            color: #9ca3af;
        }

        .submit-btn {
            width: 100%;
            padding: 0.75rem;
            margin-top: 1.5rem;
            background: linear-gradient(135deg, #9333ea 0%, #a855f7 100%);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .submit-btn:hover {
            box-shadow: 0 10px 25px rgba(147, 51, 234, 0.3);
            transform: translateY(-1px);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 999;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.show {
            display: block;
            opacity: 1;
        }

        .modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.9);
            z-index: 1000;
            display: none;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .modal.show {
            display: block;
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }

        .modal-content {
            background: white;
            padding: 2.5rem 2rem;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            max-width: 450px;
            width: 90vw;
        }

        .modal-icon {
            font-size: 4rem;
            margin-bottom: 1.2rem;
            animation: scaleIn 0.5s ease;
        }

        .modal-icon.success {
            color: #22c55e;
        }

        .modal-icon.error {
            color: #ef4444;
        }

        @keyframes scaleIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .modal-title {
            color: #1f2937;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .modal-content p {
            color: #6b7280;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .modal-btn {
            padding: 0.75rem 2.5rem;
            background: linear-gradient(135deg, #9333ea 0%, #a855f7 100%);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .modal-btn:hover {
            box-shadow: 0 8px 20px rgba(147, 51, 234, 0.3);
            transform: translateY(-2px);
        }

        #preloader {
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.9);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .loader {
            border: 4px solid #e5e7eb;
            border-top: 4px solid #9333ea;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .mobile-header {
            display: none;
        }

        @media (max-width: 968px) {
            body {
                padding: 0;
            }

            .registration-wrapper {
                max-width: 100%;
                flex-direction: column;
                border-radius: 0;
                min-height: 100vh;
            }

            .map-section {
                display: none;
            }

            .mobile-header {
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 2rem 1.5rem 1.5rem;
                background: url('../assets/img/bg-login.jpg') no-repeat center center;
                background-size: cover;
                border-bottom: 1px solid rgba(255, 255, 255, 0.2);
                position: relative;
            }

            .mobile-header::before {
                content: '';
                position: absolute;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                backdrop-filter: blur(2px);
                z-index: 0;
            }

            .mobile-header > * {
                position: relative;
                z-index: 1;
            }

            .mobile-logo {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                background: white;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 1rem;
                box-shadow: 0 8px 20px rgba(147, 51, 234, 0.2), 0 0 0 3px rgba(147, 51, 234, 0.1);
                animation: fadeInDown 0.7s ease;
            }

            .mobile-logo img {
                width: 50px;
                height: 50px;
                object-fit: contain;
            }

            .mobile-title {
                font-size: 0.95rem;
                font-weight: 600;
                color: #ffffff;
                text-align: center;
                line-height: 1.5;
                max-width: 280px;
                text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            }

            .mobile-header .back-button {
                position: absolute;
                top: 1rem;
                left: 1rem;
            }

            .form-section {
                max-height: none;
                padding-bottom: 2rem;
            }
        }

        @media (max-width: 640px) {
            body {
                padding: 0;
                min-height: 100vh;
                display: flex;
            }

            .registration-wrapper {
                border-radius: 0;
                min-height: 100vh;
                width: 100%;
            }

            .mobile-header {
                padding: 1.5rem 1rem 1rem;
                border-radius: 0;
            }

            .mobile-logo {
                width: 70px;
                height: 70px;
                box-shadow: 0 6px 16px rgba(147, 51, 234, 0.2), 0 0 0 2px rgba(147, 51, 234, 0.1);
            }

            .mobile-logo img {
                width: 45px;
                height: 45px;
            }

            .mobile-title {
                font-size: 0.875rem;
                max-width: 260px;
                color: #ffffff;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-section {
                padding: 1.25rem;
                padding-bottom: 2rem;
            }

            .form-header h1 {
                font-size: 1.5rem;
            }

            .form-header p {
                font-size: 0.8rem;
            }

            .section-title {
                font-size: 0.9rem;
            }

            input[type="text"],
            input[type="email"],
            input[type="password"],
            input[type="number"],
            select {
                font-size: 16px;
            }

            .submit-btn {
                padding: 0.875rem;
                font-size: 0.9rem;
            }

            .file-upload-label {
                padding: 1.5rem 1rem;
            }

            .file-upload-label i {
                font-size: 1.25rem;
            }

            .file-upload-label span {
                font-size: 0.8rem;
            }

            .mobile-header .back-button {
                width: 36px;
                height: 36px;
            }

            .mobile-header .back-button i {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 400px) {
            .mobile-header {
                padding: 1.25rem 0.75rem 0.75rem;
            }

            .mobile-logo {
                width: 60px;
                height: 60px;
                box-shadow: 0 4px 12px rgba(147, 51, 234, 0.2), 0 0 0 2px rgba(147, 51, 234, 0.1);
            }

            .mobile-logo img {
                width: 40px;
                height: 40px;
            }

            .mobile-title {
                font-size: 0.8rem;
                max-width: 240px;
                color: #ffffff;
            }

            .form-section {
                padding: 1rem;
            }

            .form-header h1 {
                font-size: 1.35rem;
            }

            .section-title {
                font-size: 0.85rem;
                margin-top: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="registration-wrapper">
        <div class="map-section">
            <div class="back-button" onclick="goBack()">
                <i class="fas fa-arrow-left"></i>
            </div>
            <canvas id="dotCanvas"></canvas>
            <div class="map-overlay">
                <div class="map-logo">
                    <img src="../assets/img/logo.png" alt="SOCCS Logo">
                </div>
                <h2 class="map-title">Student Organization of the College of Computer Studies</h2>
            </div>
        </div>

        <div class="mobile-header">
        <div class="back-button" onclick="goBack()">
            <i class="fas fa-arrow-left"></i>
            </div>
            <div class="mobile-logo">
                <img src="../assets/img/logo.png" alt="SOCCS Logo">
            </div>
            <h2 class="mobile-title">Student Organization of the College of Computer Studies</h2>
        </div>
        
                <div class="form-section">

            <div class="form-header">
                <h1>Create Account</h1>
                <p>Join the SOCCS community</p>
                    </div>

            <form id="student-registration-form">
                <div class="section-title">
                        <i class="fas fa-user"></i>
                    Personal Information
                    </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>First Name <span class="required">*</span></label>
                        <input type="text" name="firstName" required placeholder="Enter first name">
                    </div>
                    <div class="form-group">
                        <label>Middle Name</label>
                        <input type="text" name="middleName" placeholder="Enter middle name (optional)">
                    </div>
                    <div class="form-group">
                        <label>Last Name <span class="required">*</span></label>
                        <input type="text" name="lastName" required placeholder="Enter last name">
                    </div>
                    <div class="form-group">
                        <label>Suffix</label>
                        <select name="suffix">
                            <option value="" selected>None</option>
                            <option value="Jr.">Jr.</option>
                            <option value="Sr.">Sr.</option>
                            <option value="II">II</option>
                            <option value="III">III</option>
                            <option value="IV">IV</option>
                            <option value="V">V</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Age <span class="required">*</span></label>
                        <input type="number" name="age" required placeholder="Enter age" min="16" max="100">
                    </div>
                    <div class="form-group">
                        <label>Gender <span class="required">*</span></label>
                        <select name="gender" required>
                            <option value="" disabled selected>Select gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Email Address <span class="required">*</span></label>
                        <input type="email" name="email" required placeholder="your.email@example.com">
                    </div>
                </div>

                <div class="section-title">
                    <i class="fas fa-graduation-cap"></i>
                    Academic Information
                    </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Student ID <span class="required">*</span></label>
                        <input type="text" name="studentId" required placeholder="e.g., 2021-00001">
                    </div>
                    <div class="form-group">
                        <label>Course <span class="required">*</span></label>
                        <select name="course" required>
                            <option value="" disabled selected>Select course</option>
                            <option value="BSIT">BSIT - Bachelor of Science in Information Technology</option>
                            <option value="BSCS">BSCS - Bachelor of Science in Computer Science</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Year Level <span class="required">*</span></label>
                        <select name="yearLevel" required>
                            <option value="" disabled selected>Select year</option>
                            <option value="1">1st Year</option>
                            <option value="2">2nd Year</option>
                            <option value="3">3rd Year</option>
                            <option value="4">4th Year</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Section <span class="required">*</span></label>
                        <input type="text" name="section" id="section" required placeholder="e.g., A" maxlength="1">
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Student ID Image <span class="required">*</span></label>
                        <div class="file-upload-container">
                            <input type="file" id="studentIdImage" name="studentIdImage" class="file-upload-input" accept=".jpg,.jpeg,.png" required>
                            <label for="studentIdImage" class="file-upload-label" id="studentIdDropZone">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span id="studentUploadText">Click or drag to upload Student ID image</span>
                            </label>
                            <div class="file-info">JPG or PNG, max 5MB</div>
                        </div>
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Certificate of Registration (COR) <span class="required">*</span></label>
                        <div class="file-upload-container">
                            <input type="file" id="corFile" name="corFile" class="file-upload-input" accept=".pdf,.jpg,.jpeg,.png" required>
                            <label for="corFile" class="file-upload-label" id="corDropZone">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span id="corUploadText">Click or drag to upload COR</span>
                            </label>
                            <div class="file-info">PDF, JPG or PNG, max 10MB</div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-user-plus"></i>
                    <span>Register Student</span>
                </button>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="successOverlay"></div>
    <div class="modal" id="successModal">
        <div class="modal-content">
            <i class="fas fa-check-circle modal-icon success"></i>
            <h3 class="modal-title">Registration Successful!</h3>
            <p>Thank you for registering. Please check your email to confirm and wait for admin approval.</p>
            <button class="modal-btn" id="successOk">Continue to Login</button>
        </div>
    </div>

    <div class="modal-overlay" id="errorOverlay"></div>
    <div class="modal" id="errorModal">
        <div class="modal-content">
            <i class="fas fa-exclamation-circle modal-icon error"></i>
            <h3 class="modal-title" id="errorTitle">Error</h3>
            <p id="errorMessage">An error occurred</p>
            <button class="modal-btn" id="errorOk">OK</button>
            </div>
        </div>

    <div id="preloader">
        <div class="loader"></div>
    </div>

    <script>
        const canvas = document.getElementById('dotCanvas');
        if (canvas) {
            const ctx = canvas.getContext('2d');

            function resizeCanvas() {
                canvas.width = canvas.parentElement.offsetWidth;
                canvas.height = canvas.parentElement.offsetHeight;
            }

            window.addEventListener('resize', resizeCanvas);
            resizeCanvas();

            function generateDots(width, height) {
                const dots = [];
                const gap = 12;
                
                for (let x = 0; x < width; x += gap) {
                    for (let y = 0; y < height; y += gap) {
                        const isInMapShape =
                            ((x < width * 0.25 && x > width * 0.05) && (y < height * 0.4 && y > height * 0.1)) ||
                            ((x < width * 0.25 && x > width * 0.15) && (y < height * 0.8 && y > height * 0.4)) ||
                            ((x < width * 0.45 && x > width * 0.3) && (y < height * 0.35 && y > height * 0.15)) ||
                            ((x < width * 0.5 && x > width * 0.35) && (y < height * 0.65 && y > height * 0.35)) ||
                            ((x < width * 0.7 && x > width * 0.45) && (y < height * 0.5 && y > height * 0.1)) ||
                            ((x < width * 0.8 && x > width * 0.65) && (y < height * 0.8 && y > height * 0.6));

                        if (isInMapShape && Math.random() > 0.3) {
                            dots.push({
                                x, y,
                                radius: 1,
                                opacity: Math.random() * 0.5 + 0.2
                            });
                        }
                    }
                }
                return dots;
            }

            const routes = [
                { start: { x: 100, y: 150, delay: 0 }, end: { x: 200, y: 80, delay: 2 }, color: '#9333ea' },
                { start: { x: 200, y: 80, delay: 2 }, end: { x: 260, y: 120, delay: 4 }, color: '#9333ea' },
                { start: { x: 50, y: 50, delay: 1 }, end: { x: 150, y: 180, delay: 3 }, color: '#9333ea' },
                { start: { x: 280, y: 60, delay: 0.5 }, end: { x: 180, y: 180, delay: 2.5 }, color: '#9333ea' }
            ];

            let startTime = Date.now();
            const dots = generateDots(canvas.width, canvas.height);

            function animate() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                dots.forEach(dot => {
                    ctx.beginPath();
                    ctx.arc(dot.x, dot.y, dot.radius, 0, Math.PI * 2);
                    ctx.fillStyle = `rgba(147, 51, 234, ${dot.opacity})`;
                    ctx.fill();
                });

                const currentTime = (Date.now() - startTime) / 1000;

                routes.forEach(route => {
                    const elapsed = currentTime - route.start.delay;
                    if (elapsed <= 0) return;
                    
                    const duration = 3;
                    const progress = Math.min(elapsed / duration, 1);
                    const x = route.start.x + (route.end.x - route.start.x) * progress;
                    const y = route.start.y + (route.end.y - route.start.y) * progress;
                    
                    ctx.beginPath();
                    ctx.moveTo(route.start.x, route.start.y);
                    ctx.lineTo(x, y);
                    ctx.strokeStyle = route.color;
                    ctx.lineWidth = 1.5;
                    ctx.stroke();
                    
                    ctx.beginPath();
                    ctx.arc(route.start.x, route.start.y, 3, 0, Math.PI * 2);
                    ctx.fillStyle = route.color;
                    ctx.fill();
                    
                    ctx.beginPath();
                    ctx.arc(x, y, 3, 0, Math.PI * 2);
                    ctx.fillStyle = '#a855f7';
                    ctx.fill();
                    
                    ctx.beginPath();
                    ctx.arc(x, y, 6, 0, Math.PI * 2);
                    ctx.fillStyle = 'rgba(147, 51, 234, 0.4)';
                    ctx.fill();
                    
                    if (progress === 1) {
                        ctx.beginPath();
                        ctx.arc(route.end.x, route.end.y, 3, 0, Math.PI * 2);
                        ctx.fillStyle = route.color;
                        ctx.fill();
                    }
                });
                
                if (currentTime > 15) startTime = Date.now();
                requestAnimationFrame(animate);
            }

            animate();
        }

        function setupFileUpload(inputId, labelId, textId) {
            const input = document.getElementById(inputId);
            const label = document.getElementById(labelId);
            const text = document.getElementById(textId);

            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    text.textContent = file.name;
                    label.classList.add('has-file');
                }
            });

            ['dragenter', 'dragover'].forEach(evt => {
                label.addEventListener(evt, (e) => {
                        e.preventDefault();
                    label.classList.add('dragover');
                    });
                });

            ['dragleave', 'drop'].forEach(evt => {
                label.addEventListener(evt, (e) => {
                        e.preventDefault();
                    label.classList.remove('dragover');
                    });
                });

            label.addEventListener('drop', (e) => {
                const file = e.dataTransfer.files[0];
                    if (file) {
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                    input.files = dataTransfer.files;
                    text.textContent = file.name;
                    label.classList.add('has-file');
                    }
                });
            }

        setupFileUpload('studentIdImage', 'studentIdDropZone', 'studentUploadText');
        setupFileUpload('corFile', 'corDropZone', 'corUploadText');

        document.getElementById('section').addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });

        const form = document.getElementById('student-registration-form');
        const preloader = document.getElementById('preloader');

        form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(form);
            preloader.style.display = 'flex';

            try {
                const response = await fetch('../auth/student-register.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                preloader.style.display = 'none';

                    if (data.status === 'success') {
                        showModal('successModal');
                        form.reset();
                    document.getElementById('studentIdDropZone').classList.remove('has-file');
                    document.getElementById('corDropZone').classList.remove('has-file');
                    document.getElementById('studentUploadText').textContent = 'Click or drag to upload Student ID image';
                    document.getElementById('corUploadText').textContent = 'Click or drag to upload COR';
                    } else {
                        showErrorModal('Registration Failed', data.message);
                    }
            } catch (error) {
                preloader.style.display = 'none';
                    showErrorModal('Registration Failed', 'An error occurred. Please try again.');
            }
        });

        function showModal(modalId) {
            document.getElementById(modalId).classList.add('show');
            document.getElementById(modalId.replace('Modal', 'Overlay')).classList.add('show');
        }

        function hideModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
            document.getElementById(modalId.replace('Modal', 'Overlay')).classList.remove('show');
        }

        function showErrorModal(title, message) {
            document.getElementById('errorTitle').textContent = title;
            document.getElementById('errorMessage').textContent = message;
            showModal('errorModal');
        }

        document.getElementById('successOk').addEventListener('click', function() {
            hideModal('successModal');
            window.location.href = '../templates/login.php';
        });

        document.getElementById('errorOk').addEventListener('click', function() {
            hideModal('errorModal');
        });

        function goBack() {
            window.location.href = '../templates/login.php';
        }
    </script>
</body>
</html> 
