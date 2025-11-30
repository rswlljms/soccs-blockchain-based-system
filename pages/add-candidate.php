<?php
session_start();
require_once '../includes/page_access.php';
checkPageAccess(['register_candidates']);
include('../components/sidebar.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Candidate</title>
  <link rel="stylesheet" href="../assets/css/sidebar.css">
  <link rel="stylesheet" href="../assets/css/admin-table-styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    :root {
      --primary-gradient: linear-gradient(135deg, #4B0082, #9933ff);
      --primary-hover: linear-gradient(135deg, #3a0066, #7a29cc);
      --text-primary: #1f2937;
      --text-secondary: #4b5563;
      --border-color: #e5e7eb;
      --secondary-color: #f9fafb;
      --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
      --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
      --radius-sm: 8px;
      --radius-md: 12px;
    }
    
    body {
      font-family: 'Work Sans', sans-serif;
      background-color: #f6f7fb;
      margin: 0;
      padding: 0;
      color: var(--text-primary);
    }
    
    .main-content {
      margin-left: 280px !important;
      width: calc(100% - 280px) !important;
      padding: 40px 32px 32px 32px;
      min-height: 100vh;
      background-color: #f6f7fb;
      position: relative;
      box-sizing: border-box;
    }
    
    .page-title {
      font-size: 32px;
      font-weight: 700;
      margin-bottom: 32px;
      color: var(--text-primary);
      position: relative;
      padding-bottom: 14px;
    }
    
    .page-title::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 60px;
      height: 4px;
      background: var(--primary-gradient);
      border-radius: 2px;
    }
    
    .form-container {
      background: #fff;
      padding: 24px 20px;
      border-radius: 8px;
      box-shadow: 0 1px 2px rgba(0,0,0,0.05);
      margin-bottom: 28px;
    }
    
    form {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      align-items: center;
      margin-bottom: 0;
    }
    
    .input-group {
      position: relative;
      background: #fff;
      border: 1px solid #e2e8f0;
      border-radius: 6px;
      flex: 1;
      min-width: 200px;
      box-shadow: 0 1px 2px rgba(0,0,0,0.05);
      display: flex;
      align-items: center;
    }
    
    .input-group i {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: #9933ff;
      font-size: 18px;
      pointer-events: none;
    }
    
    input, select {
      width: 100%;
      padding: 12px 12px 12px 40px;
      border: none;
      border-radius: 6px;
      font-size: 15px;
      color: #111827;
      background: transparent;
      outline: none;
    }
    
    input::placeholder {
      color: #6B7280;
    }
    
    select {
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%236B7280' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 12px center;
      padding-right: 40px;
      color: #6B7280;
      cursor: pointer;
    }

    select option:not([disabled]) {
      color: #111827;
    }
    
    input[type="file"] {
      padding: 12px 12px 12px 40px;
      background: #fff;
      border-radius: 6px;
      color: #9933ff;
      font-size: 15px;
      border: none;
      cursor: pointer;
    }

    .btn-add {
      height: 46px;
      display: flex;
      align-items: center;
      gap: 8px;
      background: #9933ff;
      color: white;
      border: none;
      border-radius: 6px;
      padding: 0 24px;
      font-weight: 500;
      font-size: 14px;
      cursor: pointer;
      white-space: nowrap;
      margin-left: 12px;
      margin-top: 0;
      box-shadow: none;
      transition: background 0.15s;
    }
    
    .btn-add:hover {
      background: #7928CC;
    }

    
    .table-container {
      background: white;
      padding: 0;
      border-radius: var(--radius-md);
      box-shadow: var(--shadow-sm);
      margin-bottom: 32px;
      overflow: hidden;
    }
    
    table,
    .styled-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      margin-bottom: 20px;
      background: white;
      font-size: 14px;
    }
    
    table thead,
    .styled-table thead {
      background: var(--secondary-color);
    }
    
    th {
      text-align: left;
      padding: 18px 20px;
      font-weight: 600;
      font-size: 14px;
      color: var(--text-primary);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border-bottom: 1px solid var(--border-color);
      background-color: var(--secondary-color);
      white-space: nowrap;
    }

    th:last-child {
      text-align: right;
    }
    
    td {
      padding: 18px 20px;
      color: var(--text-primary);
      border-bottom: 1px solid var(--border-color);
      background: white;
      font-size: 14px;
      font-weight: 400;
    }
    
    table tbody tr:hover td,
    tr:hover td {
      background-color: rgba(153, 51, 255, 0.04);
      transition: background-color 0.2s ease;
    }

    table tbody tr:last-child td,
    tr:last-child td {
      border-bottom: none;
    }
    
    .empty-message {
      color: #6B7280;
      font-style: italic;
      text-align: center;
      padding: 60px 16px;
      font-size: 15px;
      background: #fafafa;
    }

    .custom-file-input {
      display: none;
    }
    .input-group.file-input-group {
      max-width: 260px;
      min-width: 180px;
      flex: none;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .inline-profile-preview {
      display: none;
      width: 36px;
      height: 36px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #9933ff;
      background: #f3f0fa;
    }
    .inline-profile-preview.active {
      display: block;
    }
    .custom-file-label {
      padding: 12px 12px 12px 38px;
      font-size: 14px;
      border-radius: 6px;
      width: 100%;
      height: 100%;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .file-name {
      max-width: 90px;
    }
    .profile-preview {
      display: none;
      flex-direction: column;
      align-items: center;
      margin-bottom: 8px;
      width: 80px;
    }
    .profile-preview.active {
      display: flex;
    }
    .profile-img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #9933ff;
      background: #f3f0fa;
      margin-bottom: 4px;
    }
    .profile-label {
      font-size: 13px;
      color: #6B7280;
      margin-bottom: 4px;
    }
    .candidate-list-header {
      background: white;
      padding: 20px 24px;
      border-radius: 12px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      margin-top: 32px;
    }

    .registered-title {
      font-size: 20px;
      font-weight: 700;
      color: var(--text-primary);
      margin: 0;
      letter-spacing: -0.02em;
    }

    .filter-group {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .filter-group label {
      font-size: 14px;
      color: var(--text-secondary);
      font-weight: 500;
    }

    .filter-group select {
      padding: 10px 14px;
      border: 2px solid #e5e7eb;
      border-radius: 8px;
      background: white;
      color: var(--text-primary);
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s;
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23666' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 12px center;
      padding-right: 36px;
      min-width: 200px;
    }

    .filter-group select:hover {
      border-color: #9933ff;
    }

    .filter-group select:focus {
      outline: none;
      border-color: #9933ff;
      box-shadow: 0 0 0 4px rgba(153, 51, 255, 0.1);
    }
    
    /* Action Buttons */
    .action-buttons {
      display: flex;
      gap: 8px;
      align-items: center;
      justify-content: flex-end;
    }
    
    .btn-approve {
      padding: 8px 16px;
      background: linear-gradient(135deg, #10b981, #059669);
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 6px;
      transition: all 0.2s;
      box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
    }
    
    .btn-reject {
      padding: 8px 16px;
      background: linear-gradient(135deg, #ef4444, #dc2626);
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 6px;
      transition: all 0.2s;
      box-shadow: 0 2px 4px rgba(239, 68, 68, 0.2);
    }
    
    .btn-approve:hover {
      background: linear-gradient(135deg, #059669, #047857);
      transform: translateY(-1px);
      box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
    }
    
    .btn-reject:hover {
      background: linear-gradient(135deg, #dc2626, #b91c1c);
      transform: translateY(-1px);
      box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
    }

    .btn-approve:active,
    .btn-reject:active {
      transform: translateY(0);
    }
    
    .btn-view {
      padding: 8px 16px;
      background: linear-gradient(135deg, #3b82f6, #2563eb);
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 6px;
      transition: all 0.2s;
      box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
    }
    
    .btn-view:hover {
      background: linear-gradient(135deg, #2563eb, #1d4ed8);
      transform: translateY(-1px);
      box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
    }

    .btn-view:active {
      transform: translateY(0);
    }
    
    .platform-cell {
      max-width: 200px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    
    .candidate-photo {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #ede9fe;
      box-shadow: 0 2px 4px rgba(147, 51, 234, 0.1);
    }

    .header-controls {
      background: white;
      padding: 20px 24px;
      border-radius: 12px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
    }

    .btn-new {
      background: var(--primary-gradient);
      color: white;
      border: none;
      border-radius: 8px;
      padding: 12px 24px;
      font-weight: 600;
      font-size: 14px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: all 0.2s;
      box-shadow: 0 2px 4px rgba(153, 51, 255, 0.2);
    }

    .btn-new:hover {
      background: var(--primary-hover);
      transform: translateY(-1px);
      box-shadow: 0 4px 8px rgba(153, 51, 255, 0.3);
    }

    .btn-new:active {
      transform: translateY(0);
    }

    .controls-section {
      display: flex;
      gap: 16px;
      align-items: center;
    }

    .search-box {
      position: relative;
      display: flex;
      align-items: center;
    }

    .search-box::before {
      content: '\f002';
      font-family: 'Font Awesome 6 Free';
      font-weight: 900;
      position: absolute;
      left: 14px;
      color: #9ca3af;
      font-size: 14px;
      pointer-events: none;
      z-index: 1;
    }

    .search-box input {
      padding: 12px 16px 12px 40px;
      border: 2px solid #e5e7eb;
      border-radius: 8px;
      font-size: 14px;
      width: 280px;
      transition: all 0.2s;
      background: white;
      color: var(--text-primary);
    }

    .search-box input:focus {
      outline: none;
      border-color: #9933ff;
      box-shadow: 0 0 0 4px rgba(153, 51, 255, 0.1);
    }

    .search-box input::placeholder {
      color: #9ca3af;
    }

    /* Modal Styles */
    .modal-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.5);
      z-index: 1000;
      backdrop-filter: blur(3px);
    }

    .modal {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: white;
      border-radius: 16px;
      z-index: 1001;
      width: 90%;
      max-width: 720px;
      max-height: 90vh;
      overflow: hidden;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }

    .modal.show,
    .modal-overlay.show {
      display: block;
    }

    .modal-header {
      background: var(--primary-gradient);
      padding: 24px 32px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-radius: 16px 16px 0 0;
    }

    .modal-title {
      font-size: 22px;
      font-weight: 600;
      color: white;
      margin: 0;
      letter-spacing: -0.02em;
    }

    .modal-close {
      background: rgba(255, 255, 255, 0.2);
      border: none;
      font-size: 18px;
      color: white;
      cursor: pointer;
      padding: 8px;
      border-radius: 50%;
      width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s;
      flex-shrink: 0;
    }

    .modal-close:hover {
      background: rgba(255, 255, 255, 0.3);
      transform: rotate(90deg);
    }

    .candidate-form-modal {
      padding: 32px;
      max-height: calc(90vh - 120px);
      overflow-y: auto;
    }

    .candidate-form-modal::-webkit-scrollbar {
      width: 8px;
    }

    .candidate-form-modal::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 4px;
    }

    .candidate-form-modal::-webkit-scrollbar-thumb {
      background: #cbd5e1;
      border-radius: 4px;
    }

    .candidate-form-modal::-webkit-scrollbar-thumb:hover {
      background: #9933ff;
    }

    .form-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 24px;
      margin-bottom: 0;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .form-group-full {
      grid-column: 1 / -1;
    }

    .form-group label {
      font-size: 14px;
      font-weight: 600;
      color: var(--text-primary);
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 4px;
    }

    .form-group label i {
      color: #9933ff;
      font-size: 15px;
      width: 18px;
      text-align: center;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
      padding: 14px 18px;
      border: 2px solid var(--border-color);
      border-radius: var(--radius-sm);
      font-size: 15px;
      color: var(--text-primary);
      background: white;
      transition: all 0.2s;
      font-family: 'Work Sans', sans-serif;
      width: 100%;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: #9933ff;
      box-shadow: 0 0 0 4px rgba(153, 51, 255, 0.1);
    }

    .form-group select {
      cursor: pointer;
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23666' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 12px center;
      padding-right: 36px;
    }

    .form-group textarea {
      resize: vertical;
      min-height: 100px;
      line-height: 1.5;
    }

    .form-group input::placeholder,
    .form-group textarea::placeholder {
      color: #9ca3af;
    }

    .form-group input[type="file"] {
      padding: 12px 18px;
      background: white;
      cursor: pointer;
      font-size: 14px;
      border: 2px dashed var(--border-color);
      border-radius: var(--radius-sm);
      transition: all 0.2s;
    }

    .form-group input[type="file"]:hover {
      border-color: #9933ff;
      background: rgba(153, 51, 255, 0.05);
    }

    .modal-footer {
      padding: 24px 32px;
      border-top: 1px solid var(--border-color);
      display: flex;
      justify-content: flex-end;
      gap: 14px;
      background: #fafbfc;
      border-radius: 0 0 16px 16px;
    }

    .btn-cancel {
      padding: 14px 32px;
      background: white;
      border: 2px solid var(--border-color);
      color: var(--text-primary);
      font-weight: 600;
      font-size: 15px;
      border-radius: var(--radius-sm);
      cursor: pointer;
      transition: all 0.2s;
      min-width: 120px;
    }

    .btn-cancel:hover {
      border-color: #cbd5e1;
      background: var(--secondary-color);
      transform: translateY(-1px);
    }

    .btn-cancel:active {
      transform: translateY(0);
    }

    .btn-save {
      padding: 14px 36px;
      background: var(--primary-gradient);
      border: none;
      color: white;
      font-weight: 600;
      font-size: 15px;
      border-radius: var(--radius-sm);
      cursor: pointer;
      transition: all 0.2s;
      min-width: 120px;
    }

    .btn-save:hover {
      background: var(--primary-hover);
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(153, 51, 255, 0.3);
    }

    .btn-save:active {
      transform: translateY(0);
    }

    @media (max-width: 768px) {
      .modal {
        width: 95%;
      }

      .form-grid {
        grid-template-columns: 1fr;
        gap: 20px;
      }

      .modal-header {
        padding: 20px 24px;
      }

      .candidate-form-modal {
        padding: 24px;
      }

      .modal-footer {
        padding: 20px 24px;
        flex-direction: column;
      }

      .btn-cancel,
      .btn-save {
        width: 100%;
      }
    }

    .notification-modal {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) scale(0.7);
      background: white;
      border-radius: 16px;
      z-index: 2000;
      width: 90%;
      max-width: 420px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      opacity: 0;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .notification-modal.show {
      display: block;
      opacity: 1;
      transform: translate(-50%, -50%) scale(1);
    }

    .notification-overlay-notif {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.4);
      z-index: 1999;
      backdrop-filter: blur(2px);
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .notification-overlay-notif.show {
      display: block;
      opacity: 1;
    }

    .notification-header {
      padding: 32px 32px 24px 32px;
      text-align: center;
      border-radius: 16px 16px 0 0;
    }

    .notification-icon {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      font-size: 40px;
    }

    .notification-icon.success {
      background: linear-gradient(135deg, #10b981, #059669);
      color: white;
      animation: successPulse 0.6s ease;
    }

    .notification-icon.error {
      background: linear-gradient(135deg, #ef4444, #dc2626);
      color: white;
      animation: errorShake 0.6s ease;
    }

    @keyframes successPulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.1); }
    }

    @keyframes errorShake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-10px); }
      75% { transform: translateX(10px); }
    }

    .notification-title {
      font-size: 24px;
      font-weight: 700;
      margin: 0 0 12px 0;
      color: var(--text-primary);
    }

    .notification-message {
      font-size: 15px;
      color: var(--text-secondary);
      line-height: 1.6;
      margin: 0;
      padding: 0 16px;
    }

    .notification-footer {
      padding: 24px 32px 32px 32px;
      display: flex;
      justify-content: center;
    }

    .btn-notification-close {
      padding: 14px 40px;
      background: var(--primary-gradient);
      border: none;
      color: white;
      font-weight: 600;
      font-size: 15px;
      border-radius: var(--radius-sm);
      cursor: pointer;
      transition: all 0.2s;
      min-width: 140px;
    }

    .btn-notification-close:hover {
      background: var(--primary-hover);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(153, 51, 255, 0.3);
    }

    .btn-notification-close:active {
      transform: translateY(0);
    }

    /* Platform Modal Styles */
    .platform-content {
      margin-bottom: 16px;
    }

    .candidate-info h4 {
      font-size: 18px;
      font-weight: 600;
      color: #1f2937;
      margin-bottom: 8px;
    }

    .platform-details {
      font-size: 14px;
      color: #6b7280;
      margin-bottom: 20px;
      line-height: 1.5;
    }

    .platform-text h5 {
      font-size: 16px;
      font-weight: 600;
      color: #374151;
      margin-bottom: 12px;
      border-bottom: 2px solid #e5e7eb;
      padding-bottom: 8px;
    }

    .platform-statement {
      background: #f9fafb;
      padding: 16px;
      border-radius: 8px;
      border-left: 4px solid #3B82F6;
      font-size: 14px;
      line-height: 1.6;
      color: #374151;
    }

    .confirm-modal {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) scale(0.7);
      background: white;
      border-radius: 16px;
      z-index: 2000;
      width: 90%;
      max-width: 420px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      opacity: 0;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .confirm-modal.show {
      display: block;
      opacity: 1;
      transform: translate(-50%, -50%) scale(1);
    }

    .confirm-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.4);
      z-index: 1999;
      backdrop-filter: blur(2px);
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .confirm-overlay.show {
      display: block;
      opacity: 1;
    }

    .confirm-header {
      padding: 32px 32px 24px 32px;
      text-align: center;
    }

    .confirm-icon {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      font-size: 40px;
      background: linear-gradient(135deg, #fef3c7, #fde68a);
      color: #d97706;
    }

    .confirm-title {
      font-size: 22px;
      font-weight: 700;
      margin: 0 0 12px 0;
      color: var(--text-primary);
    }

    .confirm-message {
      font-size: 15px;
      color: var(--text-secondary);
      line-height: 1.6;
      margin: 0;
    }

    .confirm-message strong {
      color: var(--text-primary);
    }

    .confirm-footer {
      padding: 24px 32px 32px 32px;
      display: flex;
      justify-content: center;
      gap: 12px;
    }

    .btn-confirm-cancel {
      padding: 14px 32px;
      background: white;
      border: 2px solid var(--border-color);
      color: var(--text-primary);
      font-weight: 600;
      font-size: 15px;
      border-radius: var(--radius-sm);
      cursor: pointer;
      transition: all 0.2s;
    }

    .btn-confirm-cancel:hover {
      border-color: #cbd5e1;
      background: var(--secondary-color);
    }

    .btn-confirm-delete {
      padding: 14px 32px;
      background: linear-gradient(135deg, #ef4444, #dc2626);
      border: none;
      color: white;
      font-weight: 600;
      font-size: 15px;
      border-radius: var(--radius-sm);
      cursor: pointer;
      transition: all 0.2s;
    }

    .btn-confirm-delete:hover {
      background: linear-gradient(135deg, #dc2626, #b91c1c);
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .pagination.centered {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 12px;
      padding: 20px;
      border-top: 1px solid var(--border-color);
    }

    .page-btn {
      background: white;
      border: 1px solid var(--border-color);
      padding: 8px 16px;
      border-radius: var(--radius-sm);
      cursor: pointer;
      font-size: 14px;
      color: var(--text-primary);
      text-decoration: none;
      transition: all 0.2s;
      pointer-events: auto;
      font-family: 'Work Sans', sans-serif;
      font-weight: 500;
    }

    .page-btn:hover:not(.disabled) {
      border-color: #9933ff;
      color: #9933ff;
      background-color: rgba(153, 51, 255, 0.05);
    }

    .page-btn.disabled {
      opacity: 0.5;
      cursor: not-allowed;
      pointer-events: none;
    }

    .page-btn:focus {
      outline: none;
    }

    .page-indicator {
      font-size: 14px;
      color: var(--text-secondary);
      padding: 0 16px;
    }
  </style>
</head>

<body>
  <div class="main-content">
    <h1 class="page-title">Candidates List</h1>
    
    <div class="header-controls">
      <div class="controls-section">
        <div class="search-box">
          <input type="text" id="searchInput" placeholder="Search candidates by name, position, or platform...">
        </div>
      </div>
      <button class="btn-new" onclick="openCandidateModal()">
        <i class="fas fa-plus"></i> New Candidate
      </button>
    </div>
    
    <div class="candidate-list-header">
      <h2 class="registered-title">Registered Candidates</h2>
      <div class="filter-group">
        <label for="filter-position">Filter by Position:</label>
        <select id="filter-position" onchange="filterCandidates()">
          <option value="">All Positions</option>
        </select>
      </div>
    </div>
    
    <div class="table-container">
      <table class="styled-table">
        <thead>
          <tr>
            <th>Photo</th>
            <th>Name</th>
            <th>Partylist</th>
            <th>Position</th>
            <th style="text-align: center;">Platform</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="candidates-table-body">
          <tr>
            <td colspan="6" class="empty-message">No candidates registered yet.</td>
          </tr>
        </tbody>
      </table>
      
      <div class="pagination centered">
        <button type="button" class="page-btn prev-btn" onclick="goToPage('prev')">&laquo; Prev</button>
        <span class="page-indicator">Page 1 of 1</span>
        <button type="button" class="page-btn next-btn" onclick="goToPage('next')">Next &raquo;</button>
      </div>
    </div>
  </div>

  <!-- Candidate Modal -->
  <div class="modal-overlay" id="candidateModalOverlay"></div>
  <div class="modal" id="candidateModal">
    <div class="modal-header">
      <h2 class="modal-title" id="modalTitle">Add New Candidate</h2>
      <button type="button" class="modal-close" onclick="closeCandidateModal()">
        <i class="fas fa-times"></i>
      </button>
    </div>
    
    <form id="candidateForm" class="candidate-form-modal" enctype="multipart/form-data">
      <div class="form-grid">
        <div class="form-group">
          <label for="candidateFirstname">
            <i class="fas fa-user"></i> First Name *
          </label>
          <input type="text" id="candidateFirstname" name="firstname" placeholder="Enter first name" required>
        </div>
        
        <div class="form-group">
          <label for="candidateLastname">
            <i class="fas fa-user"></i> Last Name *
          </label>
          <input type="text" id="candidateLastname" name="lastname" placeholder="Enter last name" required>
        </div>
        
        <div class="form-group">
          <label for="candidatePartylist">
            <i class="fas fa-users"></i> Partylist *
          </label>
          <input type="text" id="candidatePartylist" name="partylist" placeholder="Enter partylist name" required>
        </div>
        
        <div class="form-group">
          <label for="candidatePosition">
            <i class="fas fa-briefcase"></i> Position *
          </label>
          <select id="candidatePosition" name="position" required>
            <option value="" disabled selected>Select Position</option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="candidatePhoto">
            <i class="fas fa-image"></i> Photo
          </label>
          <input type="file" id="candidatePhoto" name="photo" accept="image/*">
        </div>
        
        <div class="form-group form-group-full">
          <label for="candidatePlatform">
            <i class="fas fa-file-alt"></i> Platform *
          </label>
          <textarea id="candidatePlatform" name="platform" placeholder="Enter candidate platform" required></textarea>
        </div>
      </div>
    </form>
    
    <div class="modal-footer">
      <button type="button" class="btn-cancel" onclick="closeCandidateModal()">
        Cancel
      </button>
      <button type="button" class="btn-save" onclick="document.getElementById('candidateForm').dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }))">
        <i class="fas fa-save"></i> Save Candidate
      </button>
    </div>
  </div>

  <!-- Platform Viewing Modal -->
  <div class="modal-overlay" id="platformModalOverlay"></div>
  <div class="modal" id="platformModal">
    <div class="modal-header">
      <h2 class="modal-title" id="platformModalTitle">Candidate Platform</h2>
      <button type="button" class="modal-close" onclick="closePlatformModal()">
        <i class="fas fa-times"></i>
      </button>
    </div>
    
    <div class="candidate-form-modal">
      <div class="platform-content">
        <div class="candidate-info">
          <h4 id="platformCandidateName"></h4>
          <p class="platform-details">
            <strong>Position:</strong> <span id="platformCandidatePosition"></span><br>
            <strong>Partylist:</strong> <span id="platformCandidatePartylist"></span>
          </p>
        </div>
        
        <div class="platform-text">
          <h5>Platform Statement:</h5>
          <div id="platformCandidateText" class="platform-statement"></div>
        </div>
      </div>
    </div>
    
    <div class="modal-footer">
      <button type="button" class="btn-cancel" onclick="closePlatformModal()">
        <i class="fas fa-times"></i> Close
      </button>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div class="confirm-overlay" id="confirmOverlay"></div>
  <div class="confirm-modal" id="confirmModal">
    <div class="confirm-header">
      <div class="confirm-icon">
        <i class="fas fa-exclamation-triangle"></i>
      </div>
      <h3 class="confirm-title">Delete Candidate</h3>
      <p class="confirm-message">Are you sure you want to delete <strong id="confirmCandidateName"></strong>? This action cannot be undone.</p>
    </div>
    <div class="confirm-footer">
      <button type="button" class="btn-confirm-cancel" onclick="closeConfirmModal()">Cancel</button>
      <button type="button" class="btn-confirm-delete" id="confirmDeleteBtn">
        <i class="fas fa-trash"></i> Delete
      </button>
    </div>
  </div>

  <!-- Notification Modal -->
  <div class="notification-overlay-notif" id="notificationOverlayNotif"></div>
  <div class="notification-modal" id="notificationModal">
    <div class="notification-header">
      <div class="notification-icon" id="notificationIcon">
        <i class="fas fa-check"></i>
      </div>
      <h3 class="notification-title" id="notificationTitle">Success!</h3>
      <p class="notification-message" id="notificationMessage">Operation completed successfully.</p>
    </div>
    <div class="notification-footer">
      <button type="button" class="btn-notification-close" onclick="closeNotification()">
        Got it
      </button>
    </div>
  </div>

  <script>
    let candidates = [];
    let positions = [];
    let editingCandidateId = null;
    let pendingDeleteId = null;
    let currentPage = 1;
    const itemsPerPage = 5;

    function showNotification(type, title, message) {
      const modal = document.getElementById('notificationModal');
      const overlay = document.getElementById('notificationOverlayNotif');
      const icon = document.getElementById('notificationIcon');
      const titleEl = document.getElementById('notificationTitle');
      const messageEl = document.getElementById('notificationMessage');

      icon.className = `notification-icon ${type}`;
      
      if (type === 'success') {
        icon.innerHTML = '<i class="fas fa-check"></i>';
      } else if (type === 'error') {
        icon.innerHTML = '<i class="fas fa-times"></i>';
      }

      titleEl.textContent = title;
      messageEl.textContent = message;

      overlay.classList.add('show');
      setTimeout(() => modal.classList.add('show'), 10);
    }

    function closeNotification() {
      const modal = document.getElementById('notificationModal');
      const overlay = document.getElementById('notificationOverlayNotif');
      
      modal.classList.remove('show');
      setTimeout(() => overlay.classList.remove('show'), 300);
    }

    async function loadPositions() {
      try {
        const response = await fetch('../api/positions/read.php');
        const result = await response.json();
        
        if (result.success) {
          positions = result.data;
          populatePositionSelects();
        } else {
          console.error('Failed to load positions:', result.error);
        }
      } catch (error) {
        console.error('Error loading positions:', error);
      }
    }

    function populatePositionSelects() {
      const positionSelect = document.getElementById('candidatePosition');
      const filterSelect = document.getElementById('filter-position');
      
      positionSelect.innerHTML = '<option value="" disabled selected>Select Position</option>';
      filterSelect.innerHTML = '<option value="">All Positions</option>';
      
      positions.forEach(pos => {
        const option1 = document.createElement('option');
        option1.value = pos.id;
        option1.textContent = pos.description;
        positionSelect.appendChild(option1);
        
        const option2 = document.createElement('option');
        option2.value = pos.description;
        option2.textContent = pos.description;
        filterSelect.appendChild(option2);
      });
    }

    async function loadCandidates() {
      try {
        const response = await fetch('../api/candidates/read.php');
        const result = await response.json();
        
        if (result.success) {
          candidates = result.data;
          sortCandidatesByPosition();
          currentPage = 1;
          renderCandidatesTable();
        } else {
          console.error('Failed to load candidates:', result.error);
        }
      } catch (error) {
        console.error('Error loading candidates:', error);
      }
    }

    function sortCandidatesByPosition() {
      const positionOrder = {};
      positions.forEach((pos, index) => {
        positionOrder[pos.id] = index;
      });
      
      candidates.sort((a, b) => {
        const orderA = positionOrder[a.positionId] ?? 999;
        const orderB = positionOrder[b.positionId] ?? 999;
        return orderA - orderB;
      });
    }

    function openCandidateModal(candidate = null) {
      const modal = document.getElementById('candidateModal');
      const overlay = document.getElementById('candidateModalOverlay');
      const title = document.getElementById('modalTitle');
      const form = document.getElementById('candidateForm');

      if (candidate) {
        title.textContent = 'Edit Candidate';
        document.getElementById('candidateFirstname').value = candidate.firstname;
        document.getElementById('candidateLastname').value = candidate.lastname;
        document.getElementById('candidatePartylist').value = candidate.partylist;
        document.getElementById('candidatePosition').value = candidate.positionId;
        document.getElementById('candidatePlatform').value = candidate.platform;
        editingCandidateId = candidate.id;
      } else {
        title.textContent = 'Add New Candidate';
        form.reset();
        editingCandidateId = null;
      }

      modal.classList.add('show');
      overlay.classList.add('show');
      document.body.style.overflow = 'hidden';
    }

    function viewPlatform(id) {
      const candidate = candidates.find(c => c.id === id);
      if (candidate) {
        const fullName = `${candidate.firstname} ${candidate.lastname}`;
        
        document.getElementById('platformCandidateName').textContent = fullName;
        document.getElementById('platformCandidatePosition').textContent = candidate.position;
        document.getElementById('platformCandidatePartylist').textContent = candidate.partylist;
        document.getElementById('platformCandidateText').textContent = candidate.platform;
        
        const modal = document.getElementById('platformModal');
        const overlay = document.getElementById('platformModalOverlay');
        modal.classList.add('show');
        overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
      }
    }

    function closePlatformModal() {
      const modal = document.getElementById('platformModal');
      const overlay = document.getElementById('platformModalOverlay');
      
      modal.classList.remove('show');
      overlay.classList.remove('show');
      document.body.style.overflow = '';
    }

    function closeCandidateModal() {
      const modal = document.getElementById('candidateModal');
      const overlay = document.getElementById('candidateModalOverlay');
      const form = document.getElementById('candidateForm');
      
      modal.classList.remove('show');
      overlay.classList.remove('show');
      document.body.style.overflow = '';
      form.reset();
      editingCandidateId = null;
    }

    function renderCandidatesTable(candidatesList = candidates) {
      const tbody = document.getElementById('candidates-table-body');
      tbody.innerHTML = '';

      if (candidatesList.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="6" class="empty-message">No candidates registered yet.</td>
          </tr>
        `;
        updatePagination(1, 1);
        return;
      }

      const totalPages = Math.ceil(candidatesList.length / itemsPerPage);
      const startIndex = (currentPage - 1) * itemsPerPage;
      const endIndex = startIndex + itemsPerPage;
      const paginatedCandidates = candidatesList.slice(startIndex, endIndex);

      paginatedCandidates.forEach(candidate => {
        const fullName = `${candidate.firstname} ${candidate.lastname}`;
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>
            <img src="${candidate.photo}" alt="${fullName}" class="candidate-photo" onerror="this.src='../assets/img/logo.png'">
          </td>
          <td>${fullName}</td>
          <td>${candidate.partylist}</td>
          <td>${candidate.position}</td>
          <td style="text-align: center;">
            <div class="action-buttons" style="justify-content: center;">
              <button class="action-btn view" onclick="viewPlatform(${candidate.id})" title="View Platform">
                <i class="fas fa-eye"></i>
              </button>
            </div>
          </td>
          <td>
            <div class="action-buttons">
              <button class="action-btn edit" onclick="editCandidate(${candidate.id})" title="Edit Candidate">
                <i class="fas fa-edit"></i>
              </button>
              <button class="action-btn delete" onclick="deleteCandidate(${candidate.id})" title="Delete Candidate">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </td>
        `;
        tbody.appendChild(row);
      });

      updatePagination(currentPage, totalPages);
    }

    function updatePagination(page, totalPages) {
      const pageIndicator = document.querySelector('.page-indicator');
      const prevBtn = document.querySelector('.prev-btn');
      const nextBtn = document.querySelector('.next-btn');
      
      if (pageIndicator) {
        pageIndicator.textContent = `Page ${page} of ${totalPages || 1}`;
      }
      
      if (prevBtn) {
        prevBtn.classList.toggle('disabled', page <= 1);
      }
      
      if (nextBtn) {
        nextBtn.classList.toggle('disabled', page >= totalPages);
      }
    }

    function goToPage(direction) {
      const selectedPosition = document.getElementById('filter-position').value;
      const searchTerm = document.getElementById('searchInput').value.toLowerCase();
      
      let filteredList = [...candidates];
      
      if (selectedPosition) {
        filteredList = filteredList.filter(c => c.position === selectedPosition);
      }
      
      if (searchTerm) {
        filteredList = filteredList.filter(c => {
          const fullName = `${c.firstname} ${c.lastname}`.toLowerCase();
          return fullName.includes(searchTerm) || 
                 c.position.toLowerCase().includes(searchTerm) ||
                 c.platform.toLowerCase().includes(searchTerm);
        });
      }

      const totalPages = Math.ceil(filteredList.length / itemsPerPage);
      
      if (direction === 'prev' && currentPage > 1) {
        currentPage--;
      } else if (direction === 'next' && currentPage < totalPages) {
        currentPage++;
      }
      
      renderCandidatesTable(filteredList);
    }

    function filterCandidates() {
      currentPage = 1;
      const selectedPosition = document.getElementById('filter-position').value;
      const searchTerm = document.getElementById('searchInput').value.toLowerCase();
      
      let filteredCandidates = [...candidates];
      
      if (selectedPosition) {
        filteredCandidates = filteredCandidates.filter(c => c.position === selectedPosition);
      }
      
      if (searchTerm) {
        filteredCandidates = filteredCandidates.filter(c => {
          const fullName = `${c.firstname} ${c.lastname}`.toLowerCase();
          return fullName.includes(searchTerm) || 
                 c.position.toLowerCase().includes(searchTerm) ||
                 c.platform.toLowerCase().includes(searchTerm);
        });
      }
      
      renderCandidatesTable(filteredCandidates);
    }


    function editCandidate(id) {
      const candidate = candidates.find(c => c.id === id);
      if (candidate) {
        openCandidateModal(candidate);
      }
    }

    function openConfirmModal(id) {
      const candidate = candidates.find(c => c.id === id);
      if (!candidate) return;

      pendingDeleteId = id;
      const fullName = `${candidate.firstname} ${candidate.lastname}`;
      document.getElementById('confirmCandidateName').textContent = fullName;

      document.getElementById('confirmOverlay').classList.add('show');
      setTimeout(() => document.getElementById('confirmModal').classList.add('show'), 10);
    }

    function closeConfirmModal() {
      document.getElementById('confirmModal').classList.remove('show');
      setTimeout(() => {
        document.getElementById('confirmOverlay').classList.remove('show');
        pendingDeleteId = null;
      }, 300);
    }

    async function confirmDelete() {
      if (!pendingDeleteId) return;
      
      const candidate = candidates.find(c => c.id === pendingDeleteId);
      const fullName = candidate ? `${candidate.firstname} ${candidate.lastname}` : '';
      
      try {
        const response = await fetch('../api/candidates/delete.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: pendingDeleteId })
        });
        
        const result = await response.json();
        closeConfirmModal();
        
        if (result.success) {
          showNotification('success', 'Deleted!', `Candidate "${fullName}" has been deleted successfully.`);
          loadCandidates();
        } else {
          showNotification('error', 'Delete Failed', result.error || 'Failed to delete candidate.');
        }
      } catch (error) {
        console.error('Error deleting candidate:', error);
        closeConfirmModal();
        showNotification('error', 'Error', 'An unexpected error occurred while deleting the candidate.');
      }
    }

    function deleteCandidate(id) {
      openConfirmModal(id);
    }

    function checkDuplicateName(firstname, lastname) {
      const normalizedFirst = firstname.trim().toLowerCase();
      const normalizedLast = lastname.trim().toLowerCase();
      
      return candidates.find(c => {
        if (editingCandidateId && c.id === editingCandidateId) return false;
        return c.firstname.toLowerCase() === normalizedFirst && 
               c.lastname.toLowerCase() === normalizedLast;
      });
    }

    document.getElementById('candidateForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      const firstname = formData.get('firstname');
      const lastname = formData.get('lastname');
      const candidateName = `${firstname} ${lastname}`;
      
      const duplicate = checkDuplicateName(firstname, lastname);
      if (duplicate) {
        showNotification('error', 'Duplicate Name', `A candidate named "${duplicate.firstname} ${duplicate.lastname}" already exists.`);
        return;
      }
      
      formData.set('position_id', formData.get('position'));
      formData.delete('position');
      
      if (editingCandidateId) {
        formData.append('id', editingCandidateId);
      }
      
      try {
        const url = editingCandidateId 
          ? '../api/candidates/update.php' 
          : '../api/candidates/create.php';

        const response = await fetch(url, {
          method: 'POST',
          body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
          closeCandidateModal();
          loadCandidates();
          
          const actionType = editingCandidateId ? 'updated' : 'added';
          const title = editingCandidateId ? 'Updated!' : 'Success!';
          showNotification('success', title, `Candidate "${candidateName}" has been ${actionType} successfully.`);
        } else {
          showNotification('error', 'Save Failed', result.error || 'Failed to save candidate.');
        }
      } catch (error) {
        console.error('Error saving candidate:', error);
        showNotification('error', 'Error', 'An unexpected error occurred while saving the candidate.');
      }
    });

    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function(e) {
      currentPage = 1;
      filterCandidates();
    });

    // Close modal when clicking overlay
    document.getElementById('candidateModalOverlay').addEventListener('click', closeCandidateModal);
    document.getElementById('platformModalOverlay').addEventListener('click', closePlatformModal);
    document.getElementById('confirmOverlay').addEventListener('click', closeConfirmModal);
    document.getElementById('confirmDeleteBtn').addEventListener('click', confirmDelete);

    // Auto-capitalize partylist input (first letter of each word)
    document.getElementById('candidatePartylist').addEventListener('input', function(e) {
      const input = e.target;
      const cursorPosition = input.selectionStart;
      const originalLength = input.value.length;
      
      input.value = input.value
        .toLowerCase()
        .split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
      
      const newLength = input.value.length;
      const newCursorPosition = cursorPosition + (newLength - originalLength);
      input.setSelectionRange(newCursorPosition, newCursorPosition);
    });

    // Initialize table on page load
    document.addEventListener('DOMContentLoaded', async function() {
      await loadPositions();
      await loadCandidates();
    });
  </script>
</body>
</html> 