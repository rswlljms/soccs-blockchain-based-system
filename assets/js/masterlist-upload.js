document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('masterlistFile');
    const fileUploadArea = document.getElementById('fileUploadArea');
    const fileSelected = document.getElementById('fileSelected');
    const uploadPlaceholder = document.querySelector('.upload-placeholder');
    const fileName = document.getElementById('fileName');
    const removeFileBtn = document.getElementById('removeFile');
    const uploadForm = document.getElementById('masterlistUploadForm');
    const uploadBtn = document.getElementById('uploadBtn');
    const resultsContainer = document.getElementById('resultsContainer');
    const extractedTableBody = document.getElementById('extractedTableBody');
    const resultsCount = document.getElementById('resultsCount');
    const clearResultsBtn = document.getElementById('clearResults');
    const loadingModal = document.getElementById('loadingModal');
    const errorModal = document.getElementById('errorModal');
    const errorMessage = document.getElementById('errorMessage');

    fileInput.addEventListener('change', handleFileSelect);
    
    fileUploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        fileUploadArea.classList.add('dragover');
    });
    
    fileUploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        fileUploadArea.classList.remove('dragover');
    });
    
    fileUploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        fileUploadArea.classList.remove('dragover');
        
        if (e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            handleFileSelect();
        }
    });
    
    removeFileBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        fileInput.value = '';
        fileSelected.style.display = 'none';
        uploadPlaceholder.style.display = 'block';
        fileUploadArea.classList.remove('has-file');
        return false;
    });
    
    fileSelected.addEventListener('click', function(e) {
        if (e.target !== removeFileBtn && !removeFileBtn.contains(e.target)) {
            e.stopPropagation();
        }
    });
    
    uploadForm.addEventListener('submit', handleUpload);
    clearResultsBtn.addEventListener('click', clearResults);
    
    function handleFileSelect() {
        const file = fileInput.files[0];
        if (file) {
            const maxSize = 10 * 1024 * 1024;
            if (file.size > maxSize) {
                showError('File size must be less than 10MB');
                fileInput.value = '';
                fileUploadArea.classList.remove('has-file');
                return;
            }
            
            fileName.textContent = file.name;
            fileSelected.style.display = 'flex';
            uploadPlaceholder.style.display = 'none';
            fileUploadArea.classList.add('has-file');
        }
    }
    
    async function handleUpload(e) {
        e.preventDefault();
        
        const file = fileInput.files[0];
        if (!file) {
            showError('Please select a file to upload');
            return;
        }
        
        const formData = new FormData();
        formData.append('masterlistFile', file);
        
        showLoading();
        uploadBtn.disabled = true;
        
        try {
            const response = await fetch('../api/upload-masterlist.php', {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const text = await response.text();
            
            if (!text || text.trim() === '') {
                throw new Error('Empty response from server');
            }
            
            let data;
            try {
                data = JSON.parse(text);
            } catch (parseError) {
                console.error('JSON Parse Error:', parseError);
                console.error('Response text:', text);
                throw new Error('Invalid JSON response from server. Check console for details.');
            }
            
            hideLoading();
            uploadBtn.disabled = false;
            
            if (data.status === 'success') {
                displayResults(data.data);
            } else {
                showError(data.message || 'Failed to process masterlist');
            }
        } catch (error) {
            hideLoading();
            uploadBtn.disabled = false;
            showError('Network error: ' + error.message);
        }
    }
    
    function displayResults(students) {
        extractedTableBody.innerHTML = '';
        
        if (!students || students.length === 0) {
            extractedTableBody.innerHTML = `
                <tr>
                    <td colspan="5" style="text-align: center; padding: 60px 20px; color: #6b7280;">
                        <i class="fas fa-info-circle" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px; display: block;"></i>
                        <p style="font-size: 16px; font-weight: 500; margin-bottom: 8px;">No students found</p>
                        <p style="font-size: 14px; color: #9ca3af;">The document may not contain recognizable student information, or the format is not supported.</p>
                    </td>
                </tr>
            `;
            resultsCount.textContent = '0 students found';
            return;
        }
        
        students.forEach((student, index) => {
            const row = document.createElement('tr');
            
            const studentId = student.studentId || '<span style="color: #9ca3af; font-style: italic;">Not found</span>';
            const name = student.name || '<span style="color: #9ca3af; font-style: italic;">Not found</span>';
            const course = student.course || '<span style="color: #9ca3af; font-style: italic;">-</span>';
            const section = student.section || '<span style="color: #9ca3af; font-style: italic;">-</span>';
            
            row.innerHTML = `
                <td style="font-weight: 500; color: #6b7280; width: 60px;">${index + 1}</td>
                <td style="font-weight: 500; font-family: monospace; color: #1f2937;">${studentId}</td>
                <td style="color: #1f2937;">${name}</td>
                <td style="color: #1f2937; font-weight: 500;">${course}</td>
                <td style="color: #1f2937; font-weight: 500;">${section}</td>
            `;
            
            extractedTableBody.appendChild(row);
        });
        
        resultsCount.textContent = `${students.length} student(s) found`;
        
        resultsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    
    function clearResults() {
        extractedTableBody.innerHTML = `
            <tr>
                <td colspan="5" style="text-align: center; padding: 60px 20px; color: #6b7280;">
                    <i class="fas fa-file-upload" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px; display: block;"></i>
                    <p style="font-size: 16px; font-weight: 500; margin-bottom: 8px;">No data extracted yet</p>
                    <p style="font-size: 14px; color: #9ca3af;">Upload a masterlist document to extract student information</p>
                </td>
            </tr>
        `;
        resultsCount.textContent = '';
    }
    
    function showLoading() {
        loadingModal.classList.add('show');
    }
    
    function hideLoading() {
        loadingModal.classList.remove('show');
    }
    
    function showError(message) {
        errorMessage.textContent = message;
        errorModal.classList.add('show');
    }
    
    window.closeModal = function(modalId) {
        document.getElementById(modalId).classList.remove('show');
    };
    
    errorModal.addEventListener('click', function(e) {
        if (e.target === errorModal) {
            closeModal('errorModal');
        }
    });
    
    loadingModal.addEventListener('click', function(e) {
        if (e.target === loadingModal) {
            e.stopPropagation();
        }
    });
});

