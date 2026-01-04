<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Registration - Step 1 | SOCCS</title>
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
                <h1>Student Registration</h1>
                <p>Upload your documents to get started</p>
            </div>

            <div class="progress-steps">
                <div class="step active">
                    <div class="step-circle">1</div>
                    <span class="step-label">Upload Documents</span>
                </div>
                <div class="step-divider"></div>
                <div class="step">
                    <div class="step-circle">2</div>
                    <span class="step-label">Complete Registration</span>
                </div>
            </div>

            <form id="document-upload-form">
                <div class="section-title">
                    <i class="fas fa-cloud-upload-alt"></i>
                    Required Documents
                </div>

                <div class="info-badge" style="margin-bottom: 1.5rem;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Upload either Student ID image or COR.</span>
                </div>

                <div class="form-group full-width">
                    <label>Student ID Image or Certificate of Registration (COR) <span class="required">*</span></label>
                    <div class="file-upload-container">
                        <input type="file" id="documentFile" name="documentFile" class="file-upload-input" accept=".pdf,.jpg,.jpeg,.png,.heic,.heif,.webp" required>
                        <label for="documentFile" class="file-upload-label" id="documentDropZone">
                            <i class="fas fa-file-upload"></i>
                            <div class="upload-title">Upload Student ID or COR</div>
                            <div class="upload-subtitle">Click or drag to upload</div>
                        </label>
                        <div class="file-info">JPG, PNG, HEIC, WEBP, or PDF - Max 1MB</div>
                    </div>
                </div>

                <button type="submit" class="submit-btn" id="submitBtn">
                    <i class="fas fa-arrow-right" id="submitIcon"></i>
                    <span id="submitText">Continue to Registration</span>
                </button>
            </form>
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

        function setupFileUpload(inputId, labelId) {
            const input = document.getElementById(inputId);
            const label = document.getElementById(labelId);

            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validate file size (max 1MB)
                    if (file.size > 1 * 1024 * 1024) {
                        showErrorModal('File Too Large', 'File size must be less than 1MB. Please upload a smaller file.');
                        input.value = ''; // Clear the file input
                        label.classList.remove('has-file');
                        const title = label.querySelector('.upload-title');
                        title.textContent = 'Upload Student ID or COR';
                        return;
                    }
                    
                    const title = label.querySelector('.upload-title');
                    title.textContent = file.name;
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
                    // Validate file size (max 1MB)
                    if (file.size > 1 * 1024 * 1024) {
                        showErrorModal('File Too Large', 'File size must be less than 1MB. Please upload a smaller file.');
                        input.value = ''; // Clear the file input
                        label.classList.remove('has-file');
                        const title = label.querySelector('.upload-title');
                        title.textContent = 'Upload Student ID or COR';
                        return;
                    }
                    
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    input.files = dataTransfer.files;
                    const title = label.querySelector('.upload-title');
                    title.textContent = file.name;
                    label.classList.add('has-file');
                }
            });
        }

        setupFileUpload('documentFile', 'documentDropZone');

        const form = document.getElementById('document-upload-form');
        const submitBtn = document.getElementById('submitBtn');
        const submitIcon = document.getElementById('submitIcon');
        const submitText = document.getElementById('submitText');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(form);
            
            const documentFile = document.getElementById('documentFile').files[0];
            
            if (!documentFile) {
                showErrorModal('Upload Required', 'Please upload either Student ID image or Certificate of Registration (COR).');
                return;
            }
            
            // Validate file size (max 1MB)
            if (documentFile.size > 1 * 1024 * 1024) {
                showErrorModal('File Too Large', 'File size must be less than 1MB. Please upload a smaller file.');
                return;
            }
            
            // Determine file type and set appropriate field name
            const fileExtension = documentFile.name.split('.').pop().toLowerCase();
            const isPdf = fileExtension === 'pdf';
            
            // Add file to formData with appropriate field name
            if (isPdf) {
                formData.append('corFile', documentFile);
            } else {
                // For images, treat as Student ID if small, otherwise COR
                if (documentFile.size <= 1 * 1024 * 1024) {
                    formData.append('studentIdImage', documentFile);
                } else {
                    formData.append('corFile', documentFile);
                }
            }
            
            // Remove the original documentFile from formData
            formData.delete('documentFile');
            
            // Disable all form inputs
            const inputs = form.querySelectorAll('input, button, select, textarea');
            inputs.forEach(input => input.disabled = true);
            
            submitBtn.disabled = true;
            submitIcon.className = 'fas fa-spinner fa-spin';
            submitText.textContent = 'Processing...';

            try {
                const response = await fetch('../api/extract-student-info.php', {
                    method: 'POST',
                    body: formData
                });
                
                console.log('Response status:', response.status);
                
                const text = await response.text();
                console.log('Raw response:', text);
                
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error('JSON parse error:', e);
                    
                    // Re-enable all form inputs on error
                    const inputs = form.querySelectorAll('input, button, select, textarea');
                    inputs.forEach(input => input.disabled = false);
                    
                    submitBtn.disabled = false;
                    submitIcon.className = 'fas fa-arrow-right';
                    submitText.textContent = 'Continue to Registration';
                    showErrorModal('Upload Failed', 'Server returned invalid response: ' + text.substring(0, 100));
                    return;
                }

                if (data.status === 'success') {
                    submitIcon.className = 'fas fa-check';
                    submitText.textContent = 'Success!';
                    sessionStorage.setItem('registrationData', JSON.stringify(data.data));
                    setTimeout(() => {
                        window.location.href = 'student-registration-step2.php';
                    }, 500);
                } else {
                    // Re-enable all form inputs on error
                    const inputs = form.querySelectorAll('input, button, select, textarea');
                    inputs.forEach(input => input.disabled = false);
                    
                    submitBtn.disabled = false;
                    submitIcon.className = 'fas fa-arrow-right';
                    submitText.textContent = 'Continue to Registration';
                    showErrorModal('Upload Failed', data.message || 'Unknown error occurred');
                }
            } catch (error) {
                console.error('Fetch error:', error);
                
                // Re-enable all form inputs on error
                const inputs = form.querySelectorAll('input, button, select, textarea');
                inputs.forEach(input => input.disabled = false);
                
                submitBtn.disabled = false;
                submitIcon.className = 'fas fa-arrow-right';
                submitText.textContent = 'Continue to Registration';
                showErrorModal('Upload Failed', 'Network error: ' + error.message);
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

        document.getElementById('errorOk').addEventListener('click', function() {
            hideModal('errorModal');
        });

        function goBack() {
            window.location.href = '../templates/login.php';
        }
    </script>
</body>
</html>
