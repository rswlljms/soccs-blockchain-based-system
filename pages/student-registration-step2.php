<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Registration - Step 2 | SOCCS</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/student-registration-steps.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                <h1>Complete Registration</h1>
                <p>Review and edit your information</p>
            </div>

            <div class="progress-steps">
                <div class="step completed">
                    <div class="step-circle"><i class="fas fa-check"></i></div>
                    <span class="step-label">Upload Documents</span>
                </div>
                <div class="step-divider"></div>
                <div class="step active">
                    <div class="step-circle">2</div>
                    <span class="step-label">Complete Registration</span>
                </div>
            </div>

            <form id="registration-form">
                <input type="hidden" id="tempId" name="tempId">
                <input type="hidden" id="studentIdPath" name="studentIdPath">
                <input type="hidden" id="corPath" name="corPath">

                <div class="section-title">
                    <i class="fas fa-user"></i>
                    Personal Information
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>First Name <span class="required">*</span></label>
                        <input type="text" name="firstName" id="firstName" required placeholder="Enter first name">
                    </div>
                    <div class="form-group">
                        <label>Middle Name</label>
                        <input type="text" name="middleName" id="middleName" placeholder="Enter middle name (optional)">
                    </div>
                    <div class="form-group">
                        <label>Last Name <span class="required">*</span></label>
                        <input type="text" name="lastName" id="lastName" required placeholder="Enter last name">
                    </div>
                    <div class="form-group">
                        <label>Suffix</label>
                        <select name="suffix" id="suffix">
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
                        <label>Gender <span class="required">*</span></label>
                        <select name="gender" id="gender" required>
                            <option value="" disabled selected>Select gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Email Address <span class="required">*</span></label>
                        <input type="email" name="email" id="email" required placeholder="your.email@example.com">
                    </div>
                </div>

                <div class="section-title">
                    <i class="fas fa-graduation-cap"></i>
                    Academic Information
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Student ID <span class="required">*</span></label>
                        <input type="text" name="studentId" id="studentId" required placeholder="e.g., 2021-00001">
                    </div>
                    <div class="form-group">
                        <label>Course <span class="required">*</span></label>
                        <select name="course" id="course" required>
                            <option value="" disabled>Select course</option>
                            <option value="BSIT">BSIT - Bachelor of Science in Information Technology</option>
                            <option value="BSCS">BSCS - Bachelor of Science in Computer Science</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Year Level <span class="required">*</span></label>
                        <select name="yearLevel" id="yearLevel" required>
                            <option value="" disabled>Select year</option>
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
                </div>

                <button type="submit" class="submit-btn" id="submitBtn">
                    <i class="fas fa-user-plus" id="submitIcon"></i>
                    <span id="submitText">Register Student</span>
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

        const registrationData = sessionStorage.getItem('registrationData');
        
        if (!registrationData) {
            window.location.href = 'student-registration-step1.php';
        } else {
            const data = JSON.parse(registrationData);
            
            console.log('=== REGISTRATION DATA ===');
            console.log('Full Data:', data);
            console.log('Extracted Info:', data.extractedInfo);
            
            document.getElementById('tempId').value = data.tempId || '';
            document.getElementById('studentIdPath').value = data.studentIdPath || '';
            document.getElementById('corPath').value = data.corPath || '';
            
            const extracted = data.extractedInfo;
            
            if (extracted.studentId) {
                console.log('Auto-filling Student ID:', extracted.studentId);
                document.getElementById('studentId').value = extracted.studentId;
            } else {
                console.warn('Student ID not extracted from COR');
            }
            
            if (extracted.course) {
                console.log('Auto-filling Course:', extracted.course);
                document.getElementById('course').value = extracted.course;
            } else {
                console.warn('Course not extracted from COR');
            }
            
            if (extracted.yearLevel) {
                console.log('Auto-filling Year Level:', extracted.yearLevel);
                document.getElementById('yearLevel').value = extracted.yearLevel;
            } else {
                console.warn('Year Level not extracted from COR');
            }
            
            if (extracted.gender) {
                console.log('Auto-filling Gender:', extracted.gender);
                document.getElementById('gender').value = extracted.gender;
            } else {
                console.warn('Gender not extracted from COR');
            }
            
            console.log('=== AUTO-FILL COMPLETE ===');
        }

        document.getElementById('section').addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });

        const form = document.getElementById('registration-form');
        const submitBtn = document.getElementById('submitBtn');
        const submitIcon = document.getElementById('submitIcon');
        const submitText = document.getElementById('submitText');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(form);

            // Disable all form inputs
            const inputs = form.querySelectorAll('input, button, select, textarea');
            inputs.forEach(input => input.disabled = true);

            submitBtn.disabled = true;
            submitIcon.className = 'fas fa-spinner fa-spin';
            submitText.textContent = 'Submitting...';

            try {
                const response = await fetch('../auth/student-register-step2.php', {
                    method: 'POST',
                    body: formData
                });
                
                console.log('Step 2 Response status:', response.status);
                
                const text = await response.text();
                console.log('Step 2 Raw response:', text);
                
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error('Step 2 JSON parse error:', e);
                    
                    // Re-enable all form inputs on error
                    const inputs = form.querySelectorAll('input, button, select, textarea');
                    inputs.forEach(input => input.disabled = false);
                    
                    submitBtn.disabled = false;
                    submitIcon.className = 'fas fa-user-plus';
                    submitText.textContent = 'Register Student';
                    showErrorModal('Registration Failed', 'Server returned invalid response: ' + text.substring(0, 100));
                    return;
                }

                if (data.status === 'success') {
                    submitIcon.className = 'fas fa-check';
                    submitText.textContent = 'Registration Successful!';
                    sessionStorage.removeItem('registrationData');
                    setTimeout(() => {
                        showModal('successModal');
                        form.reset();
                    }, 500);
                } else {
                    // Re-enable all form inputs on error
                    const inputs = form.querySelectorAll('input, button, select, textarea');
                    inputs.forEach(input => input.disabled = false);
                    
                    submitBtn.disabled = false;
                    submitIcon.className = 'fas fa-user-plus';
                    submitText.textContent = 'Register Student';
                    showErrorModal('Registration Failed', data.message || 'Unknown error occurred');
                }
            } catch (error) {
                console.error('Step 2 Fetch error:', error);
                
                // Re-enable all form inputs on error
                const inputs = form.querySelectorAll('input, button, select, textarea');
                inputs.forEach(input => input.disabled = false);
                
                submitBtn.disabled = false;
                submitIcon.className = 'fas fa-user-plus';
                submitText.textContent = 'Register Student';
                showErrorModal('Registration Failed', 'Network error: ' + error.message);
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
            if (confirm('Are you sure you want to go back? Your uploaded documents will be lost.')) {
                sessionStorage.removeItem('registrationData');
                window.location.href = 'student-registration-step1.php';
            }
        }
    </script>
</body>
</html>
