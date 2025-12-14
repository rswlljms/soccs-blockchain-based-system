<?php
session_start();
require_once '../includes/database.php';

if (!isset($_SESSION['student'])) {
    header('Location: ../templates/login.php');
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $studentId = $_SESSION['student']['id'];
    
    $query = "SELECT * FROM students WHERE id = ? AND is_active = 1";
    $stmt = $conn->prepare($query);
    $stmt->execute([$studentId]);
    $studentData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$studentData) {
        session_destroy();
        header('Location: ../templates/login.php');
        exit;
    }
    
    $student = [
        'id' => $studentData['id'],
        'firstName' => $studentData['first_name'],
        'middleName' => $studentData['middle_name'] ?? '',
        'lastName' => $studentData['last_name'],
        'email' => $studentData['email'],
        'yearLevel' => $studentData['year_level'],
        'section' => $studentData['section'],
        'course' => $studentData['course'] ?? 'BSIT',
        'dateOfBirth' => $studentData['date_of_birth'] ?? '',
        'phoneNumber' => $studentData['phone_number'] ?? '',
        'address' => $studentData['address'] ?? '',
        'profileImage' => $studentData['profile_image'] ?? null
    ];
    
    $_SESSION['student'] = array_merge($_SESSION['student'], $student);
    
} catch (Exception $e) {
    error_log('Profile load error: ' . $e->getMessage());
    $student = $_SESSION['student'];
}
?>

<?php include('../components/student-sidebar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Profile Settings | SOCCS Student Portal</title>
  <link rel="stylesheet" href="../assets/css/sidebar.css">
  <link rel="stylesheet" href="../assets/css/student-mobile-first.css">
  <link rel="stylesheet" href="../assets/css/student-dashboard.css">
  <link rel="stylesheet" href="../assets/css/student-profile-new.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
  <!-- Mobile Menu Toggle -->
  <div class="mobile-menu-toggle" id="mobileMenuToggle">
    <i class="fas fa-bars"></i>
  </div>
  
  <!-- Mobile Overlay -->
  <div class="mobile-overlay" id="mobileOverlay"></div>
  
  <div class="main-content">
    <div class="dashboard-wrapper">
      <!-- Page Header -->
      <div class="dashboard-header">
        <div class="header-left">
          <h1 class="page-title">Profile Settings</h1>
          <p class="welcome-text">Manage your account information and preferences</p>
        </div>
        <div class="header-right">
        </div>
      </div>

      <!-- Profile Summary Card -->
      <div class="profile-summary-card">
        <div class="profile-summary-content">
          <div class="profile-image-section">
            <div class="profile-image-container">
              <img src="<?= !empty($student['profileImage']) ? '../' . htmlspecialchars($student['profileImage']) : '../assets/img/logo.png' ?>" alt="Profile Image" class="profile-image" id="profileImage">
              <button class="profile-image-upload" id="profileImageUpload">
                <i class="fas fa-camera"></i>
              </button>
              <input type="file" id="profileImageFile" accept="image/*" style="display: none;">
            </div>
          </div>
          <div class="profile-details">
            <h2 class="student-name"><?= htmlspecialchars($student['firstName'] . ' ' . $student['middleName'] . ' ' . $student['lastName']) ?></h2>
            <p class="student-role">College Student</p>
            
            <div class="student-info-grid">
              <div class="info-item">
                <i class="fas fa-calendar"></i>
                <span>Date of Birth: <?= !empty($student['dateOfBirth']) ? date('m/d/Y', strtotime($student['dateOfBirth'])) : 'N/A' ?></span>
              </div>
              <div class="info-item">
                <i class="fas fa-graduation-cap"></i>
                <span>Course: <?php 
                  $courseMap = ['BSIT' => 'Bachelor of Science in Information Technology', 'BSCS' => 'Bachelor of Science in Computer Science'];
                  echo htmlspecialchars($courseMap[$student['course']] ?? $student['course']);
                ?></span>
              </div>
              <div class="info-item">
                <i class="fas fa-phone"></i>
                <span>Phone: <?= !empty($student['phoneNumber']) ? htmlspecialchars($student['phoneNumber']) : 'N/A' ?></span>
              </div>
              <div class="info-item">
                <i class="fas fa-envelope"></i>
                <span>Email: <?= htmlspecialchars($student['email']) ?></span>
              </div>
              <div class="info-item">
                <i class="fas fa-home"></i>
                <span>Address: <?= !empty($student['address']) ? htmlspecialchars($student['address']) : 'N/A' ?></span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Profile Forms -->
      <div class="profile-forms">
        <!-- Basic Details -->
        <div class="form-section">
          <div class="form-section-header">
            <h3><i class="fas fa-user"></i> Basic Details</h3>
          </div>
          <div class="form-content">
            <form id="basicDetailsForm">
              <div class="form-row">
                <div class="form-group">
                  <label for="firstName">First Name <span class="required">*</span></label>
                  <input type="text" id="firstName" name="firstName" value="<?= htmlspecialchars($student['firstName']) ?>" required>
                </div>
                <div class="form-group">
                  <label for="middleName">Middle Name <span class="required">*</span></label>
                  <input type="text" id="middleName" name="middleName" value="<?= htmlspecialchars($student['middleName']) ?>" required>
                </div>
                <div class="form-group">
                  <label for="lastName">Last Name <span class="required">*</span></label>
                  <input type="text" id="lastName" name="lastName" value="<?= htmlspecialchars($student['lastName']) ?>" required>
                </div>
              </div>
              
              <div class="form-row">
                <div class="form-group">
                  <label for="dateOfBirth">Date of Birth <span class="required">*</span></label>
                  <input type="date" id="dateOfBirth" name="dateOfBirth" value="<?= htmlspecialchars($student['dateOfBirth']) ?>" required>
                </div>
                <div class="form-group">
                  <label for="phoneNumber">Phone Number <span class="required">*</span></label>
                  <input type="tel" id="phoneNumber" name="phoneNumber" value="<?= htmlspecialchars($student['phoneNumber']) ?>" required>
                </div>
                <div class="form-group">
                  <label for="email">Email Address <span class="required">*</span></label>
                  <input type="email" id="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>
                </div>
              </div>
              
              <div class="form-row">
                <div class="form-group full-width">
                  <label for="address">Address <span class="required">*</span></label>
                  <textarea id="address" name="address" rows="3" required><?= htmlspecialchars($student['address']) ?></textarea>
                </div>
              </div>
              
              <div class="form-actions">
                <button type="submit" class="btn-save-basic" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; box-shadow: 0 3px 10px rgba(139, 92, 246, 0.3); display: flex; align-items: center; gap: 8px; text-transform: uppercase; letter-spacing: 0.5px; min-width: 140px; justify-content: center;">
                  <i class="fas fa-save"></i>
                  Save Changes
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Academic Details -->
        <div class="form-section">
          <div class="form-section-header">
            <h3><i class="fas fa-university"></i> Academic Details</h3>
          </div>
          <div class="form-content">
            <form id="academicDetailsForm">
              <div class="form-row">
                <div class="form-group">
                  <label for="academicYear">Academic Year</label>
                  <input type="text" id="academicYear" name="academicYear" value="2025-2026" readonly>
                </div>
                <div class="form-group">
                  <label for="studentNumber">Student Number</label>
                  <input type="text" id="studentNumber" name="studentNumber" value="0122-1141" readonly>
                </div>
              </div>
              
              <div class="form-row">
                <div class="form-group">
                  <label for="college">College</label>
                  <input type="text" id="college" name="college" value="College of Computer Studies" readonly>
                </div>
                <div class="form-group">
                  <label for="course">Course</label>
                  <input type="text" id="course" name="course" value="Bachelor of Science in Information Technology" readonly>
                </div>
              </div>
              
              <div class="form-row">
                <div class="form-group">
                  <label for="yearLevel">Year Level</label>
                  <input type="text" id="yearLevel" name="yearLevel" value="Fourth Year" readonly>
                </div>
                <div class="form-group">
                  <label for="semester">Semester</label>
                  <input type="text" id="semester" name="semester" value="Second (2nd) Semester" readonly>
                </div>
                <div class="form-group">
                  <label for="section">Section</label>
                  <input type="text" id="section" name="section" value="BSIT WAM - 4A" readonly>
                </div>
              </div>
              
            </form>
          </div>
        </div>
      </div>

      </div>
    </div>
  </div>


  <script src="../assets/js/student-dashboard.js"></script>
  <script>
    // Profile Image Upload
    document.getElementById('profileImageUpload').addEventListener('click', function() {
      document.getElementById('profileImageFile').click();
    });

    document.getElementById('profileImageFile').addEventListener('change', async function(e) {
      const file = e.target.files[0];
      if (file) {
        if (file.type.startsWith('image/')) {
          const reader = new FileReader();
          reader.onload = function(e) {
            const profileImage = document.getElementById('profileImage');
            profileImage.src = e.target.result;
          };
          reader.readAsDataURL(file);
          
          const formData = new FormData();
          formData.append('profileImage', file);
          
          try {
            const response = await fetch('../api/upload_student_profile_image.php', {
              method: 'POST',
              body: formData
            });
            const result = await response.json();
            
            if (result.success) {
              console.log('Profile image uploaded successfully');
            } else {
              console.error('Upload failed:', result.message);
            }
          } catch (error) {
            console.error('Upload error:', error);
          }
        } else {
          alert('Please select a valid image file.');
        }
      }
    });

    // Basic Details Form
    document.getElementById('basicDetailsForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const submitBtn = document.querySelector('.btn-save-basic');
      const originalText = submitBtn.innerHTML;
      const form = this;
      
      submitBtn.classList.add('loading');
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
      submitBtn.disabled = true;
      
      try {
        const formData = new FormData(form);
        
        console.log('Sending update request...');
        
        const response = await fetch('../api/update_student_profile.php', {
          method: 'POST',
          body: formData
        });
        
        console.log('Response status:', response.status);
        
        if (!response.ok) {
          throw new Error('Network response was not ok: ' + response.status);
        }
        
        const text = await response.text();
        console.log('Response text:', text);
        
        let result;
        try {
          result = JSON.parse(text);
        } catch (parseError) {
          console.error('JSON parse error:', parseError);
          throw new Error('Invalid JSON response');
        }
        
        console.log('Parsed result:', result);
        
        if (result.success) {
          const data = result.data;
          
          document.querySelector('.student-name').textContent = 
            data.firstName + ' ' + (data.middleName ? data.middleName + ' ' : '') + data.lastName;
          
          const infoItems = document.querySelectorAll('.info-item span');
          if (infoItems[0]) infoItems[0].textContent = 'Date of Birth: ' + (data.dateOfBirth ? new Date(data.dateOfBirth).toLocaleDateString('en-US') : 'N/A');
          if (infoItems[2]) infoItems[2].textContent = 'Phone: ' + (data.phoneNumber || 'N/A');
          if (infoItems[3]) infoItems[3].textContent = 'Email: ' + data.email;
          if (infoItems[4]) infoItems[4].textContent = 'Address: ' + (data.address || 'N/A');
          
          submitBtn.innerHTML = '<i class="fas fa-check"></i> Saved!';
          submitBtn.style.background = 'linear-gradient(135deg, #28a745 0%, #20c997 100%)';
          
          setTimeout(() => {
            submitBtn.classList.remove('loading');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            submitBtn.style.background = '';
          }, 2000);
        } else {
          throw new Error(result.message || 'Failed to update profile');
        }
      } catch (error) {
        console.error('Update error:', error);
        alert('Error: ' + error.message);
        submitBtn.classList.remove('loading');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        submitBtn.style.background = '';
      }
    });

    // Academic Details Form
    document.getElementById('academicDetailsForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      const data = Object.fromEntries(formData);
      
      // Update course in profile summary
      const courseItem = document.querySelector('.info-item:nth-child(3) span');
      courseItem.textContent = 'Course: ' + data.course;
      
      alert('Academic details updated successfully!');
    });

  </script>
</body>
</html>
