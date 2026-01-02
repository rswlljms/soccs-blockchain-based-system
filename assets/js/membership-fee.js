(() => {
  const state = {
    students: [],
    page: 1,
    perPage: 10,
    currentUser: null
  };

  function formatCurrency(amount) {
    try { return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(amount); } catch { return `₱${Number(amount || 0).toFixed(2)}`; }
  }

  function getFilters() {
    return {
      search: document.getElementById('search').value.trim(),
      course: document.getElementById('course').value,
      year: document.getElementById('year').value,
      section: document.getElementById('section').value.trim(),
      status: document.getElementById('status').value
    };
  }

  async function loadCurrentUser() {
    try {
      const response = await fetch('../api/get_current_user.php');
      if (!response.ok) throw new Error('Failed to load user');
      const result = await response.json();
      if (result.success && result.user) {
        state.currentUser = result.user;
      }
    } catch (err) {
      console.error('Failed to load current user:', err);
    }
  }

  async function loadStudents() {
    const { search, course, year, section, status } = getFilters();
    const url = new URL('../api/get_students.php', window.location.href);
    if (search) url.searchParams.set('search', search);
    if (course && course !== 'All') url.searchParams.set('course', course);
    if (year && year !== 'All') url.searchParams.set('year', year);
    if (section) url.searchParams.set('section', section);
    if (status && status !== 'All') url.searchParams.set('status', status);
    url.searchParams.set('show_archived', 'false');

    const response = await fetch(url.toString());
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    const result = await response.json();
    if (!result.success) throw new Error(result.error || 'Failed to load students');

    state.students = Array.isArray(result.students) ? result.students : [];
    updateSummary(result.summary);
    state.page = 1;
    renderTable();

    // Load section summary if section filter is applied
    const sectionFilter = (section || '').trim().toUpperCase();
    if (sectionFilter) {
      await loadSectionSummary(course, year, sectionFilter);
    } else {
      const wrapper = document.getElementById('sectionSummaryWrapper');
      if (wrapper) wrapper.style.display = 'none';
      const container = document.getElementById('sectionSummary');
      if (container) container.innerHTML = '';
    }
  }

  function updateSummary(summary) {
    const totalStudents = document.getElementById('totalStudents');
    const paidStudents = document.getElementById('paidStudents');
    const totalCollected = document.getElementById('totalCollected');
    totalStudents.textContent = summary?.total_students ?? 0;
    paidStudents.textContent = summary?.paid_students ?? 0;
    totalCollected.textContent = formatCurrency(summary?.total_collected ?? 0);
  }

  function paginate(items, page, perPage) {
    const start = (page - 1) * perPage;
    return items.slice(start, start + perPage);
  }

  function renderTable() {
    const tbody = document.getElementById('membership-table-body');
    tbody.innerHTML = '';
    const totalPages = Math.max(1, Math.ceil(state.students.length / state.perPage));
    if (state.page > totalPages) state.page = totalPages;
    const rows = paginate(state.students, state.page, state.perPage);

    rows.forEach(student => {
      const tr = document.createElement('tr');
      const status = (student.payment_status || 'unpaid').toLowerCase();
      tr.innerHTML = `
        <td>${student.id}</td>
        <td>${student.full_name}</td>
        <td>${student.course || '-'}</td>
        <td>${student.year_level || '-'}</td>
        <td>${student.section || '-'}</td>
        <td>
          <span class="status-badge ${status === 'paid' ? 'status-paid' : 'status-unpaid'}">${status.toUpperCase()}</span>
        </td>
        <td>
          ${student.membership_control_number ? `<strong>${student.membership_control_number}</strong>` : '<span style="color:#6b7280">-</span>'}
        </td>
        <td>
          ${status === 'paid' && student.membership_control_number ? `<button class="btn-print-receipt" data-student-id="${student.id}"><i class="fas fa-print"></i> Print</button>` : '<span style="color:#6b7280">-</span>'}
        </td>
        <td>
          <div class="actions">
            ${status === 'unpaid' ? `<button class="btn-mark-paid" data-student-id="${student.id}" data-student-name="${student.full_name || ''}"><i class="fas fa-check-circle"></i> Mark as Paid</button>` : ''}
            ${status === 'paid' ? `<button class="mark-unpaid" data-student-id="${student.id}"><i class="fas fa-rotate-left"></i> Mark Unpaid</button>` : ''}
          </div>
        </td>
      `;
      tbody.appendChild(tr);
    });

    const pageIndicator = document.getElementById('pageIndicator');
    pageIndicator.textContent = `Page ${state.page} of ${Math.max(1, Math.ceil(state.students.length / state.perPage))}`;

    bindRowActions();
  }

  function bindRowActions() {
    // Upload handlers
    document.querySelectorAll('.upload-input').forEach(input => {
      input.addEventListener('change', async (e) => {
        const file = e.target.files?.[0];
        const studentId = e.target.getAttribute('data-student-id');
        if (!file || !studentId) return;
        try {
          const formData = new FormData();
          formData.append('student_id', studentId);
          formData.append('receipt', file);
          const res = await fetch('../api/upload_membership_receipt.php', { method: 'POST', body: formData });
          const result = await res.json();
          if (!res.ok || !result.success) throw new Error(result.error || 'Upload failed');
          await loadStudents();
        } catch (err) {
          alert(err.message || 'Failed to upload receipt');
        } finally {
          e.target.value = '';
        }
      });
    });

    // Mark as Paid button handlers
    document.querySelectorAll('.btn-mark-paid').forEach(btn => {
      btn.addEventListener('click', () => {
        const studentId = btn.getAttribute('data-student-id');
        const studentName = btn.getAttribute('data-student-name');
        openMarkPaidModal(studentId, studentName);
      });
    });

    // Mark unpaid handlers
    document.querySelectorAll('.mark-unpaid').forEach(btn => {
      btn.addEventListener('click', () => {
        const studentId = btn.getAttribute('data-student-id');
        if (!studentId) return;
        
        // Find student data
        const student = state.students.find(s => s.id == studentId);
        if (!student) return;
        
        openConfirmUnpaidModal(studentId, student);
      });
    });

    // Print receipt handlers
    document.querySelectorAll('.btn-print-receipt').forEach(btn => {
      btn.addEventListener('click', () => {
        const studentId = btn.getAttribute('data-student-id');
        if (!studentId) return;
        
        const student = state.students.find(s => s.id == studentId);
        if (!student) return;
        
        printStudentReceipt(student);
      });
    });

  }

  function bindFilters() {
    document.getElementById('applyFilters').addEventListener('click', (e) => { e.preventDefault(); loadStudents(); });
    document.getElementById('clearFilters').addEventListener('click', (e) => {
      e.preventDefault();
      document.getElementById('search').value = '';
      document.getElementById('course').value = 'All';
      document.getElementById('year').value = 'All';
      document.getElementById('section').value = '';
      document.getElementById('status').value = 'All';
      loadStudents();
    });

    document.getElementById('prevPage').addEventListener('click', (e) => { e.preventDefault(); if (state.page > 1) { state.page--; renderTable(); } });
    document.getElementById('nextPage').addEventListener('click', (e) => {
      e.preventDefault();
      const totalPages = Math.max(1, Math.ceil(state.students.length / state.perPage));
      if (state.page < totalPages) { state.page++; renderTable(); }
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    bindFilters();
    loadCurrentUser();
    loadStudents().catch(err => alert(err.message || 'Failed to load data'));
    
  });

  // Modal management functions
  let currentStudentId = null;

  async function openMarkPaidModal(studentId, studentName = '') {
    currentStudentId = studentId;
    const modal = document.getElementById('markPaidModal');
    const overlay = document.getElementById('markPaidModalOverlay');
    
    // Find student data
    const student = state.students.find(s => s.id == studentId);
    
    if (modal && overlay) {
      document.getElementById('modalStudentName').value = studentName || '';
      document.getElementById('modalPaymentDate').value = new Date().toISOString().split('T')[0];
      document.getElementById('modalAmountPaid').value = '250.00';
      
      // Fetch next control number
      try {
        const res = await fetch('../api/get_next_control_number.php');
        const result = await res.json();
        if (result.success && result.next_control_number) {
          if (student) {
            student.membership_control_number = result.next_control_number;
          }
        }
      } catch (err) {
        console.error('Failed to fetch control number:', err);
      }
      
      // Generate receipt preview when date changes
      const dateInput = document.getElementById('modalPaymentDate');
      
      // Remove existing listener by cloning and replacing
      const newDateInput = dateInput.cloneNode(true);
      dateInput.parentNode.replaceChild(newDateInput, dateInput);
      
      newDateInput.addEventListener('change', () => generateReceiptPreview(student));
      
      // Initial receipt preview
      generateReceiptPreview(student);
      
      modal.classList.add('show');
      overlay.classList.add('show');
      document.body.style.overflow = 'hidden';
    }
  }
  
  function generateReceiptPreview(student = null) {
    const receiptPreview = document.getElementById('receiptPreview');
    if (!receiptPreview) return;
    
    const studentName = document.getElementById('modalStudentName')?.value || '';
    const amount = parseFloat(document.getElementById('modalAmountPaid')?.value || 0);
    const paymentDate = document.getElementById('modalPaymentDate')?.value || new Date().toISOString().split('T')[0];
    
    if (!student && currentStudentId) {
      student = state.students.find(s => s.id == currentStudentId);
    }
    
    if (!student) return;
    
    // Format date
    const dateObj = new Date(paymentDate + 'T00:00:00');
    const formattedDate = dateObj.toLocaleDateString('en-US', { 
      month: 'short', 
      day: '2-digit', 
      year: 'numeric' 
    });
    const formattedTime = new Date().toLocaleTimeString('en-US', { 
      hour: '2-digit', 
      minute: '2-digit',
      hour12: true 
    }).toUpperCase();
    
    // Get control number (will be assigned when payment is confirmed)
    const controlNumber = student.membership_control_number || 'PENDING';
    
    // Convert amount to words
    const amountInWords = convertNumberToWords(amount);
    
    const receiptHTML = `
      <div class="receipt">
        <div class="receipt-header">
          <div class="receipt-logo">
            <img src="../assets/img/logo.png" alt="SOCCS Logo" onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\\'fas fa-university\\'></i>';">
          </div>
          <div class="receipt-institution">Student Organization of the College of Computer Studies</div>
          <div class="receipt-address">Santa Cruz, Laguna - Main Campus</div>
        </div>
        
        <div class="receipt-divider"></div>
        <div class="receipt-title">SYSTEM RECEIPT</div>
        <div class="receipt-divider"></div>
        
        <div class="receipt-details">
          <div class="receipt-detail-row">
            <span class="receipt-detail-label">CONTROL NO.:</span>
            <span class="receipt-detail-value">${controlNumber}</span>
          </div>
          <div class="receipt-detail-row">
            <span class="receipt-detail-label">DATE:</span>
            <span class="receipt-detail-value">${formattedDate} ${formattedTime}</span>
          </div>
          <div class="receipt-detail-row">
            <span class="receipt-detail-label">ID:</span>
            <span class="receipt-detail-value">${student.id || '-'}</span>
          </div>
          <div class="receipt-detail-row">
            <span class="receipt-detail-label">STUDENT:</span>
            <span class="receipt-detail-value">${studentName || student.full_name || '-'}</span>
          </div>
          <div class="receipt-detail-row">
            <span class="receipt-detail-label">COURSE / YEAR:</span>
            <span class="receipt-detail-value">${student.course || '-'} - ${student.year_level || '-'}</span>
          </div>
        </div>
        
        <div class="receipt-table">
          <div class="receipt-table-header">
            <span class="receipt-table-description">DESCRIPTION</span>
            <span class="receipt-table-amount">AMOUNT PAID</span>
          </div>
          ${amount > 0 ? `
          <div class="receipt-table-row">
            <span class="receipt-table-description">Membership Fee</span>
            <span class="receipt-table-amount">P${amount.toFixed(2)}</span>
          </div>
          ` : '<div class="receipt-table-row"><span class="receipt-table-description" style="color:#999;font-style:italic;">Enter amount to preview receipt</span></div>'}
        </div>
        
        ${amount > 0 ? `
        <div class="receipt-total">
          <span>TOTAL PAID</span>
          <span>P${amount.toFixed(2)}</span>
        </div>
        <div class="receipt-divider"></div>
        
        <div class="receipt-amount-words">
          *** ${amountInWords} ***
        </div>
        <div class="receipt-divider"></div>
        
        <div class="receipt-received-by">
          <div class="receipt-received-by-label">RECEIVED BY: ${state.currentUser?.full_name || state.currentUser?.email || 'ADMIN'}</div>
          <div class="receipt-system-message">This is a system-generated receipt. Thank you for your payment.</div>
        </div>
        <div class="receipt-divider"></div>
        <div class="receipt-footer">-- END OF RECEIPT --</div>
        ` : ''}
       </div>
     `;
     
     receiptPreview.innerHTML = receiptHTML;
     
     // Show/hide print button based on amount
     const receiptActions = document.getElementById('receiptActions');
     if (receiptActions) {
       receiptActions.style.display = amount > 0 ? 'flex' : 'none';
     }
  }
  
  function convertNumberToWords(num) {
    if (num === 0) return 'ZERO PESOS ONLY';
    
    const ones = ['', 'ONE', 'TWO', 'THREE', 'FOUR', 'FIVE', 'SIX', 'SEVEN', 'EIGHT', 'NINE'];
    const teens = ['TEN', 'ELEVEN', 'TWELVE', 'THIRTEEN', 'FOURTEEN', 'FIFTEEN', 'SIXTEEN', 'SEVENTEEN', 'EIGHTEEN', 'NINETEEN'];
    const tens = ['', '', 'TWENTY', 'THIRTY', 'FORTY', 'FIFTY', 'SIXTY', 'SEVENTY', 'EIGHTY', 'NINETY'];
    
    function convertHundreds(n) {
      let result = '';
      
      if (n >= 100) {
        result += ones[Math.floor(n / 100)] + ' HUNDRED';
        n %= 100;
        if (n > 0) result += ' ';
      }
      
      if (n >= 20) {
        result += tens[Math.floor(n / 10)];
        n %= 10;
        if (n > 0) result += ' ' + ones[n];
      } else if (n >= 10) {
        result += teens[n - 10];
      } else if (n > 0) {
        result += ones[n];
      }
      
      return result;
    }
    
    const wholePart = Math.floor(num);
    const decimalPart = Math.round((num - wholePart) * 100);
    
    let words = '';
    
    if (wholePart >= 1000) {
      words += convertHundreds(Math.floor(wholePart / 1000)) + ' THOUSAND';
      wholePart %= 1000;
      if (wholePart > 0) words += ' ';
    }
    
    words += convertHundreds(wholePart);
    
    if (words.trim()) {
      words += ' PESOS';
    }
    
    if (decimalPart > 0) {
      words += ' ' + convertHundreds(decimalPart) + ' CENTAVOS';
    }
    
    words += ' ONLY';
    
    return words.trim() || 'ZERO PESOS ONLY';
  }
  
  function printReceipt() {
    const receiptContent = document.querySelector('.receipt');
    if (!receiptContent) return;
    
    const clonedReceipt = receiptContent.cloneNode(true);
    
    // Update logo path for print window (use absolute path)
    const logoImg = clonedReceipt.querySelector('.receipt-logo img');
    if (logoImg) {
      const currentPath = window.location.pathname;
      const basePath = currentPath.substring(0, currentPath.lastIndexOf('/'));
      logoImg.src = basePath + '/../assets/img/logo.png';
    }
    
    const printWindow = window.open('', '_blank');
    
    // Get all receipt styles from the page
    const styles = Array.from(document.querySelectorAll('style, link[rel="stylesheet"]'))
      .map(s => s.outerHTML)
      .join('\n');
    
    printWindow.document.write(`
      <!DOCTYPE html>
      <html>
        <head>
          <title>Official Receipt</title>
          <meta charset="UTF-8">
          <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
          <link rel="preconnect" href="https://fonts.googleapis.com">
          <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
          <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
          <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { 
              font-family: 'Inter','Segoe UI',Arial,Helvetica,sans-serif; 
              padding: 24px;
              background: white;
              color: #111827;
              -webkit-font-smoothing: antialiased;
              -moz-osx-font-smoothing: grayscale;
              font-size: 13px;
              line-height: 1.6;
            }
            .receipt {
              width: 100%;
              max-width: 600px;
              margin: 0 auto;
              background: white;
              border: 2px dotted #333;
              padding: 30px;
            }
            .receipt-header { text-align: center; margin-bottom: 20px; }
            .receipt-logo {
              width: 80px;
              height: 80px;
              margin: 0 auto 15px;
              display: flex;
              align-items: center;
              justify-content: center;
            }
            .receipt-logo img {
              max-width: 100%;
              max-height: 100%;
              object-fit: contain;
            }
            .receipt-logo i {
              font-size: 32px;
              color: #666;
            }
            .receipt-institution {
              font-weight: 800;
              font-size: 18px;
              text-transform: uppercase;
              margin-bottom: 8px;
              letter-spacing: 1px;
              color: #111827;
            }
            .receipt-address {
              font-size: 12px;
              color: #6b7280;
              margin-bottom: 15px;
            }
            /* Hide extra dividers in print */
            .receipt-divider { display: none; }
            .receipt-title {
              text-align: center;
              font-weight: 800;
              font-size: 18px;
              text-transform: uppercase;
              margin: 16px 0;
              letter-spacing: 2px;
              padding: 10px 0;
              border-top: 1px solid #e5e7eb;
              border-bottom: 1px solid #e5e7eb;
            }
            .receipt-details {
              margin-bottom: 20px;
              font-size: 12px;
              line-height: 1.8;
            }
            .receipt-detail-row {
              display: flex;
              justify-content: space-between;
              margin-bottom: 5px;
            }
            .receipt-detail-label { font-weight: 800; color: #111827; font-size: 12px; }
            .receipt-detail-value {
              flex: 1;
              text-align: right;
              font-weight: 500;
            }
            .receipt-table {
              width: 100%;
              margin: 20px 0;
              font-size: 13px;
            }
            .receipt-table-header {
              display: flex;
              justify-content: space-between;
              /* single divider only */
              border-top: none;
              border-bottom: 1px solid #e5e7eb;
              padding: 8px 0;
              font-weight: 800;
              margin-bottom: 10px;
            }
            .receipt-table-row {
              display: flex;
              justify-content: space-between;
              margin-bottom: 8px;
              padding-bottom: 8px;
            }
            .receipt-table-description { flex: 2; text-align: left; }
            .receipt-table-amount { flex: 1; text-align: right; }
            .receipt-total {
              display: flex;
              justify-content: space-between;
              /* single divider only */
              border-top: 1px solid #e5e7eb;
              border-bottom: none;
              padding: 12px 0;
              font-weight: 800;
              font-size: 13px;
              margin: 15px 0;
            }
            .receipt-amount-words {
              text-align: center;
              font-weight: 800;
              font-size: 12px;
              text-transform: uppercase;
              margin: 22px 0;
              padding: 10px 0;
              border-top: 1px dashed #9ca3af;
              border-bottom: 1px dashed #9ca3af;
            }
            .receipt-fee-summary {
              border: 2px solid #333;
              padding: 15px;
              margin: 20px 0;
            }
            .receipt-fee-summary-title {
              text-align: center;
              font-weight: bold;
              font-size: 13px;
              text-transform: uppercase;
              margin-bottom: 15px;
              letter-spacing: 1px;
            }
            .receipt-fee-summary-row {
              display: flex;
              justify-content: space-between;
              margin-bottom: 8px;
              font-size: 12px;
            }
            .receipt-received-by {
              text-align: center;
              margin-top: 25px;
              padding-top: 12px;
              border-top: 1px solid #e5e7eb;
            }
            .receipt-received-by-label {
              font-weight: 800;
              font-size: 12px;
              text-transform: uppercase;
              margin-bottom: 5px;
            }
            .receipt-system-message {
              text-align: center;
              font-size: 12px;
              color: #6b7280;
              margin: 10px 0;
              font-style: italic;
            }
            .receipt-footer {
              text-align: center;
              font-size: 12px;
              color: #6b7280;
              margin-top: 15px;
              padding-top: 10px;
              border-top: 1px solid #e5e7eb;
            }
            @media print {
              body { padding: 10px; }
              .receipt { border: 2px dotted #333; }
              @page { margin: 0.5cm; }
            }
          </style>
        </head>
        <body>
          ${clonedReceipt.outerHTML}
          <script>
            window.onload = function() { 
              setTimeout(() => window.print(), 250);
            }
          </script>
        </body>
      </html>
    `);
    printWindow.document.close();
  }

  function closeMarkPaidModal() {
    const modal = document.getElementById('markPaidModal');
    const overlay = document.getElementById('markPaidModalOverlay');
    const form = document.getElementById('markPaidForm');
    
    if (modal && overlay) {
      modal.classList.remove('show');
      overlay.classList.remove('show');
      document.body.style.overflow = '';
      if (form) form.reset();
      currentStudentId = null;
    }
  }

  // Make functions globally accessible
  window.openMarkPaidModal = openMarkPaidModal;
  window.closeMarkPaidModal = closeMarkPaidModal;
  window.submitMarkAsPaid = submitMarkAsPaid;
  window.printReceipt = printReceipt;

  async function submitMarkAsPaid() {
    const form = document.getElementById('markPaidForm');
    if (!form || !currentStudentId) return;

    const amount = document.getElementById('modalAmountPaid').value;
    const paymentDate = document.getElementById('modalPaymentDate').value;

    if (!amount || !paymentDate || parseFloat(amount) <= 0) {
      showValidationErrorModal();
      return;
    }

    try {
      const formData = new FormData();
      formData.append('student_id', currentStudentId);
      formData.append('amount', amount);
      formData.append('payment_date', paymentDate);

      const res = await fetch('../api/mark_membership_paid.php', {
        method: 'POST',
        body: formData
      });
      
      const result = await res.json();
      if (!res.ok || !result.success) {
        throw new Error(result.error || 'Failed to mark as paid');
      }
      
      // Update the student data with the control number
      const student = state.students.find(s => s.id == currentStudentId);
      if (student && result.control_number) {
        student.membership_control_number = result.control_number;
        student.membership_fee_status = 'paid';
        student.membership_fee_paid_at = paymentDate;
        
        // Regenerate receipt with control number
        generateReceiptPreview(student);
      }
      
      closeMarkPaidModal();
      showSuccessModal(result.control_number);
      await loadStudents();
    } catch (err) {
      alert(err.message || 'Failed to record payment');
    }
  }

  // Close modal on overlay click
  const markPaidOverlay = document.getElementById('markPaidModalOverlay');
  if (markPaidOverlay) {
    markPaidOverlay.addEventListener('click', closeMarkPaidModal);
  }

  async function loadSectionSummary(course, year, section) {
    try {
      const url = new URL('../api/get_section_summary.php', window.location.href);
      if (course && course !== 'All') url.searchParams.set('course', course);
      if (year && year !== 'All') url.searchParams.set('year', year);
      url.searchParams.set('section', section);
      const res = await fetch(url.toString());
      const data = await res.json();
      if (!res.ok || !data.success || !Array.isArray(data.summary) || data.summary.length === 0) {
        const wrapper = document.getElementById('sectionSummaryWrapper');
        if (wrapper) wrapper.style.display = 'none';
        return;
      }

      const wrapper = document.getElementById('sectionSummaryWrapper');
      const container = document.getElementById('sectionSummary');
      if (!wrapper || !container) return;
      wrapper.style.display = 'block';

      let html = '';
      data.summary.forEach(s => {
        html += `
          <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;margin-bottom:16px;">
              <div style="flex:1;min-width:200px;">
                <h3 style="margin:0 0 6px;color:#2c3e50;font-size:18px;font-weight:600;">${s.course} ${s.year_level}-${s.section}</h3>
                <p style="margin:0;color:#6b7280;font-size:14px;">Total Students: <strong style="color:#111827;">${s.total_students}</strong></p>
              </div>
              <button onclick="window.open('print-section-report-pdf.php?course=${encodeURIComponent(s.course)}&year=${encodeURIComponent(s.year_level)}&section=${encodeURIComponent(s.section)}','_blank')" style="background:linear-gradient(135deg,#9933ff,#6610f2);color:#fff;border:none;border-radius:8px;padding:10px 16px;cursor:pointer;display:flex;align-items:center;gap:8px;font-size:14px;font-weight:500;">
                <i class="fas fa-print"></i> Print Report
              </button>
            </div>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
              <div style="background:linear-gradient(135deg,rgba(16,185,129,0.12),rgba(16,185,129,0.06));padding:14px;border-radius:10px;border-left:4px solid #10b981;">
                <div style="color:#065f46;font-size:11px;font-weight:700;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.5px;">Paid Students</div>
                <div style="font-size:28px;font-weight:700;color:#065f46;">${s.paid_count}</div>
              </div>
              <div style="background:linear-gradient(135deg,rgba(239,68,68,0.12),rgba(239,68,68,0.06));padding:14px;border-radius:10px;border-left:4px solid #ef4444;">
                <div style="color:#7f1d1d;font-size:11px;font-weight:700;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.5px;">Unpaid Students</div>
                <div style="font-size:28px;font-weight:700;color:#7f1d1d;">${s.unpaid_count}</div>
              </div>
            </div>
          </div>
        `;
      });
      container.innerHTML = html;
    } catch (e) {
      const wrapper = document.getElementById('sectionSummaryWrapper');
      if (wrapper) wrapper.style.display = 'none';
    }
  }

  window.showValidationErrorModal = function() {
    document.getElementById('validationErrorOverlay').classList.add('show');
    document.getElementById('validationErrorModal').classList.add('show');
  };

  window.closeValidationErrorModal = function() {
    document.getElementById('validationErrorOverlay').classList.remove('show');
    document.getElementById('validationErrorModal').classList.remove('show');
  };

  window.showSuccessModal = function(controlNumber) {
    document.getElementById('successControlNumber').textContent = controlNumber || '---';
    document.getElementById('successModalOverlay').classList.add('show');
    document.getElementById('successModal').classList.add('show');
  };

  window.closeSuccessModal = function() {
    document.getElementById('successModalOverlay').classList.remove('show');
    document.getElementById('successModal').classList.remove('show');
  };

  let pendingUnpaidStudentId = null;

  window.openConfirmUnpaidModal = function(studentId, student) {
    pendingUnpaidStudentId = studentId;
    
    const infoDisplay = document.getElementById('unpaidStudentInfo');
    if (infoDisplay && student) {
      infoDisplay.innerHTML = `
        <p><strong>Student:</strong> ${student.full_name || '-'}</p>
        <p><strong>ID:</strong> ${student.id || '-'}</p>
        <p><strong>Control No.:</strong> ${student.membership_control_number || '-'}</p>
      `;
    }
    
    document.getElementById('confirmUnpaidOverlay').classList.add('show');
    document.getElementById('confirmUnpaidModal').classList.add('show');
  };

  window.closeConfirmUnpaidModal = function() {
    pendingUnpaidStudentId = null;
    document.getElementById('confirmUnpaidOverlay').classList.remove('show');
    document.getElementById('confirmUnpaidModal').classList.remove('show');
  };

  window.confirmMarkUnpaid = async function() {
    if (!pendingUnpaidStudentId) return;
    
    try {
      const res = await fetch('../api/toggle_membership_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ student_id: pendingUnpaidStudentId, action: 'set_unpaid' })
      });
      const result = await res.json();
      if (!res.ok || !result.success) throw new Error(result.error || 'Update failed');
      
      closeConfirmUnpaidModal();
      await loadStudents();
      alert('Student marked as unpaid successfully');
    } catch (err) {
      alert(err.message || 'Failed to update status');
    }
  };

  window.printStudentReceipt = function(student) {
    if (!student) return;
    
    const amount = 250.00;
    const paymentDate = student.membership_fee_paid_at ? new Date(student.membership_fee_paid_at) : new Date();
    const formattedDate = paymentDate.toLocaleDateString('en-US', { 
      month: 'short', 
      day: '2-digit', 
      year: 'numeric' 
    });
    const formattedTime = paymentDate.toLocaleTimeString('en-US', { 
      hour: '2-digit', 
      minute: '2-digit',
      hour12: true 
    }).toUpperCase();
    
    const amountInWords = convertNumberToWords(amount);
    const controlNumber = student.membership_control_number || '---';
    const studentName = student.full_name || (student.first_name + ' ' + student.last_name);
    const processedBy = student.membership_processed_by || state.currentUser?.full_name || 'ADMIN';
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
      <!DOCTYPE html>
      <html>
      <head>
        <title>Receipt - ${controlNumber}</title>
        <style>
          body { font-family: 'Courier New', monospace; padding: 40px; }
          .receipt { max-width: 600px; margin: 0 auto; border: 2px dotted #333; padding: 30px; }
          .receipt-header { text-align: center; margin-bottom: 20px; }
          .receipt-institution { font-weight: 800; font-size: 16px; text-transform: uppercase; margin-bottom: 8px; }
          .receipt-address { font-size: 12px; color: #666; margin-bottom: 15px; }
          .receipt-title { text-align: center; font-weight: 800; font-size: 18px; text-transform: uppercase; margin: 16px 0; padding: 10px 0; border-top: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb; }
          .receipt-details { margin-bottom: 20px; font-size: 12px; line-height: 1.8; }
          .receipt-detail-row { display: flex; justify-content: space-between; margin-bottom: 5px; }
          .receipt-detail-label { font-weight: 700; }
          .receipt-table { width: 100%; margin: 20px 0; font-size: 12px; }
          .receipt-table-header { display: flex; justify-content: space-between; border-bottom: 1px solid #e5e7eb; padding: 8px 0; font-weight: bold; }
          .receipt-table-row { display: flex; justify-content: space-between; margin-bottom: 8px; padding-bottom: 8px; }
          .receipt-table-description { flex: 2; text-align: left; }
          .receipt-table-amount { flex: 1; text-align: right; }
          .receipt-total { display: flex; justify-content: space-between; border-top: 1px solid #e5e7eb; padding: 10px 0; font-weight: bold; font-size: 13px; margin: 15px 0; }
          .receipt-amount-words { text-align: center; font-weight: bold; font-size: 11px; text-transform: uppercase; margin: 20px 0; padding: 10px 0; border-top: 1px dashed #999; border-bottom: 1px dashed #999; }
          .receipt-received-by { text-align: center; margin-top: 25px; padding-top: 12px; border-top: 1px solid #e5e7eb; }
          .receipt-received-by-label { font-weight: bold; font-size: 11px; text-transform: uppercase; margin-bottom: 5px; }
          .receipt-system-message { text-align: center; font-size: 10px; color: #666; margin: 10px 0; font-style: italic; }
          .receipt-footer { text-align: center; font-size: 10px; color: #666; margin-top: 15px; padding-top: 10px; border-top: 1px solid #e5e7eb; }
          @media print {
            body { padding: 0; }
            .no-print { display: none; }
          }
        </style>
      </head>
      <body onload="window.print(); window.close();">
        <div class="receipt">
          <div class="receipt-header">
            <div class="receipt-institution">Student Organization of the College of Computer Studies</div>
            <div class="receipt-address">Santa Cruz, Laguna - Main Campus</div>
          </div>
          
          <div class="receipt-title">SYSTEM RECEIPT</div>
          
          <div class="receipt-details">
            <div class="receipt-detail-row">
              <span class="receipt-detail-label">CONTROL NO.:</span>
              <span class="receipt-detail-value">${controlNumber}</span>
            </div>
            <div class="receipt-detail-row">
              <span class="receipt-detail-label">DATE:</span>
              <span class="receipt-detail-value">${formattedDate} ${formattedTime}</span>
            </div>
            <div class="receipt-detail-row">
              <span class="receipt-detail-label">ID:</span>
              <span class="receipt-detail-value">${student.id || '-'}</span>
            </div>
            <div class="receipt-detail-row">
              <span class="receipt-detail-label">STUDENT:</span>
              <span class="receipt-detail-value">${studentName}</span>
            </div>
            <div class="receipt-detail-row">
              <span class="receipt-detail-label">COURSE / YEAR:</span>
              <span class="receipt-detail-value">${student.course || '-'} - ${student.year_level || '-'}</span>
            </div>
          </div>
          
          <div class="receipt-table">
            <div class="receipt-table-header">
              <span class="receipt-table-description">DESCRIPTION</span>
              <span class="receipt-table-amount">AMOUNT PAID</span>
            </div>
            <div class="receipt-table-row">
              <span class="receipt-table-description">SOCCS Membership Fee</span>
              <span class="receipt-table-amount">₱${amount.toFixed(2)}</span>
            </div>
          </div>
          
          <div class="receipt-total">
            <span>TOTAL:</span>
            <span>₱${amount.toFixed(2)}</span>
          </div>
          
          <div class="receipt-amount-words">
            *** ${amountInWords} ***
          </div>
          
          <div class="receipt-received-by">
            <div class="receipt-received-by-label">RECEIVED BY: ${processedBy}</div>
            <div class="receipt-system-message">This is a system-generated receipt. Thank you for your payment.</div>
          </div>
          
          <div class="receipt-footer">-- END OF RECEIPT --</div>
        </div>
      </body>
      </html>
    `);
    printWindow.document.close();
  };

  document.getElementById('validationErrorOverlay')?.addEventListener('click', window.closeValidationErrorModal);
  document.getElementById('successModalOverlay')?.addEventListener('click', window.closeSuccessModal);
  document.getElementById('confirmUnpaidOverlay')?.addEventListener('click', window.closeConfirmUnpaidModal);
})();


