<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Organization of the College of Computer Studies | Login</title>
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
      padding: 1rem;
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

    .login-wrapper {
      width: 100%;
      max-width: 1000px;
      display: flex;
      background: white;
      border-radius: 1rem;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
      overflow: hidden;
      animation: slideIn 0.5s ease;
      position: relative;
      z-index: 1;
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
      flex: 1;
      position: relative;
      background: linear-gradient(135deg, #e9d5ff 0%, #ddd6fe 100%);
      border-right: 1px solid #e5e7eb;
      min-height: 600px;
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

    .form-section {
      flex: 1;
      padding: 2.5rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
      background: white;
      animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .form-header h1 {
      font-size: 1.875rem;
      font-weight: 700;
      color: #1f2937;
      margin-bottom: 0.25rem;
    }

    .form-header p {
      color: #6b7280;
      margin-bottom: 2rem;
    }

    .divider {
      position: relative;
      margin: 1.5rem 0;
      text-align: center;
    }

    .divider::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 0;
      right: 0;
      height: 1px;
      background: #e5e7eb;
    }

    .divider span {
      position: relative;
      background: white;
      padding: 0 0.5rem;
      color: #6b7280;
      font-size: 0.875rem;
    }

    .form-group {
      margin-bottom: 1.25rem;
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
    input[type="password"] {
      width: 100%;
      padding: 0.625rem 0.75rem;
      font-size: 0.875rem;
      color: #1f2937;
      background: #f9fafb;
      border: 1px solid #e5e7eb;
      border-radius: 0.5rem;
      transition: all 0.3s ease;
    }

    input:focus {
      outline: none;
      background: white;
      border-color: #9333ea;
      box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.1);
    }

    input::placeholder {
      color: #9ca3af;
    }

    .password-toggle {
      position: absolute;
      right: 0.75rem;
      top: 50%;
      transform: translateY(-50%);
      color: #6b7280;
      cursor: pointer;
      transition: color 0.3s;
    }

    .password-toggle:hover {
      color: #374151;
    }

    .submit-btn {
      width: 100%;
      padding: 0.625rem;
      margin-top: 0.5rem;
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
      position: relative;
      overflow: hidden;
    }

    .submit-btn:hover {
      box-shadow: 0 10px 25px rgba(147, 51, 234, 0.3);
      transform: translateY(-1px);
    }

    .submit-btn:active {
      transform: translateY(0);
    }

    .submit-btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
      transition: left 0.5s;
    }

    .submit-btn:hover::before {
      left: 100%;
    }

    .forgot-link {
      text-align: center;
      margin-top: 1.5rem;
    }

    .forgot-link a {
      color: #9333ea;
      text-decoration: none;
      font-size: 0.875rem;
      transition: color 0.3s;
    }

    .forgot-link a:hover {
      color: #7c3aed;
    }

    .register-section {
      margin-top: 1.5rem;
      padding-top: 1.5rem;
      border-top: 1px solid #e5e7eb;
      text-align: center;
    }

    .register-section p {
      color: #6b7280;
      font-size: 0.875rem;
      margin-bottom: 0.75rem;
    }
    
    .register-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      background: #f3f4f6;
      border: 1px solid #e5e7eb;
      color: #374151;
      padding: 0.625rem 1.25rem;
      border-radius: 0.5rem;
      text-decoration: none;
      font-size: 0.875rem;
      font-weight: 500;
      transition: all 0.3s;
    }
    
    .register-btn:hover {
      background: #e5e7eb;
      border-color: #d1d5db;
    }

    .error-message {
      background: #fef2f2;
      border: 1px solid #fecaca;
      border-left: 4px solid #ef4444;
      padding: 0.75rem;
      margin-top: 1rem;
      border-radius: 0.5rem;
      color: #991b1b;
      font-size: 0.875rem;
      display: none;
      align-items: center;
      gap: 0.5rem;
      animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .error-message i {
      color: #ef4444;
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
      width: 40px;
      height: 40px;
      animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .mobile-header {
      display: none;
    }

    @media (max-width: 768px) {
      body {
        padding: 0;
      }

      .login-wrapper {
        max-width: 100%;
        flex-direction: column;
        margin: 0;
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

      .form-section {
        padding: 1.5rem;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
      }

      .form-header h1 {
        font-size: 1.5rem;
      }

      .form-header p {
        font-size: 0.8rem;
      }

      input[type="text"],
      input[type="email"],
      input[type="password"] {
      font-size: 16px;
    }
    
      .submit-btn {
        padding: 0.75rem;
        font-size: 0.9rem;
      }

      .register-btn {
        padding: 0.75rem 1.5rem;
        font-size: 0.85rem;
      }
    }

    @media (max-width: 480px) {
      body {
        padding: 0;
        min-height: 100vh;
        display: flex;
      }

      .login-wrapper {
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

      .form-section {
        padding: 1.25rem;
        border-radius: 0;
      }

      .form-header h1 {
        font-size: 1.35rem;
      }

      .submit-btn {
        font-size: 0.875rem;
      }
    }
  </style>
</head>
<body>
  <div class="login-wrapper">
    <div class="map-section">
      <canvas id="dotCanvas"></canvas>
      <div class="map-overlay">
        <div class="map-logo">
          <img src="../assets/img/logo.png" alt="SOCCS Logo">
        </div>
        <h2 class="map-title">Student Organization of the College of Computer Studies</h2>
      </div>
    </div>

    <div class="mobile-header">
      <div class="mobile-logo">
        <img src="../assets/img/logo.png" alt="SOCCS Logo">
      </div>
      <h2 class="mobile-title">Student Organization of the College of Computer Studies</h2>
    </div>

    <div class="form-section">
      <div class="form-header">
        <h1>Welcome back</h1>
        <p>Sign in to your account</p>
      </div>

      <form id="login-form">
        <div class="form-group">
          <label for="email">Username <span class="required">*</span></label>
          <input type="text" name="email" id="email" placeholder="Enter your Student ID or Email" required>
        </div>

        <div class="form-group">
          <label for="password">Password <span class="required">*</span></label>
          <div class="input-wrapper">
            <input type="password" name="password" id="password" placeholder="Enter your password" required>
            <span class="password-toggle" onclick="togglePassword()">
            <i class="fas fa-eye-slash" id="eyeIcon"></i>
          </span>
          </div>
        </div>

        <button type="submit" class="submit-btn">
          <span>Sign in</span>
          <i class="fas fa-arrow-right"></i>
        </button>

        <p class="error-message">
          <i class="fas fa-exclamation-circle"></i>
          <span id="error-text"></span>
        </p>
      </form>

      <div class="forgot-link">
        <a href="./forgot-password.php">Forgot password?</a>
      </div>

      <div class="register-section">
        <p>Don't have an account?</p>
        <a href="../pages/student-registration.php" class="register-btn">
          <i class="fas fa-user-plus"></i>
          Register
        </a>
      </div>
    </div>
  </div>

  <div id="preloader">
    <div class="loader"></div>
  </div>

  <script>
    const canvas = document.getElementById('dotCanvas');
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

    function togglePassword() {
      const pw = document.getElementById("password");
      const icon = document.getElementById("eyeIcon");

      if (pw.type === "password") {
        pw.type = "text";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
      } else {
        pw.type = "password";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
      }
    }

    const form = document.getElementById('login-form');
    const loader = document.getElementById('preloader');
    const errorMsg = document.querySelector('.error-message');
    const errorText = document.getElementById('error-text');

    form.addEventListener('submit', async function(e) {
      e.preventDefault();
      loader.style.display = 'flex';
      errorMsg.style.display = 'none';

      const formData = new FormData(form);
      let studentData = null;
      
      try {
        const studentResponse = await fetch('../auth/student-auth.php', {
          method: 'POST',
          body: new FormData(form)
        });
        studentData = await studentResponse.json();
        
        if (studentData.status === 'success') {
          loader.style.display = 'none';
          window.location.href = studentData.redirect || '../pages/student-dashboard.php';
          return;
        }
        
        if (studentData.message && (studentData.message.includes('pending') || studentData.message.includes('rejected'))) {
          loader.style.display = 'none';
          errorText.textContent = studentData.message;
          errorMsg.style.display = 'flex';
          return;
        }
      } catch (err) {
        console.log('Student auth error:', err);
      }
      
      try {
        const adminResponse = await fetch('../auth.php', {
          method: 'POST',
          body: new FormData(form)
        });
        const adminData = await adminResponse.json();
        
        loader.style.display = 'none';
        if (adminData.status === 'success') {
          window.location.href = adminData.redirect || '../pages/dashboard.php';
        } else {
          if (studentData && studentData.message) {
            errorText.textContent = studentData.message;
          } else {
            errorText.textContent = adminData.message || 'Invalid Student ID/Email or password';
          }
          errorMsg.style.display = 'flex';
        }
      } catch (err) {
        loader.style.display = 'none';
        errorText.textContent = "Something went wrong.";
        errorMsg.style.display = 'flex';
      }
    });
  </script>
</body>
</html>
