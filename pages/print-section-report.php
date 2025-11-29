<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../templates/login.php");
    exit;
}

require_once '../includes/database.php';

$database = new Database();
$pdo = $database->getConnection();

$course = $_GET['course'] ?? '';
$year = $_GET['year'] ?? '';
$section = $_GET['section'] ?? '';

if (empty($section)) {
    die('Section filter is required');
}

$whereConditions = ['is_archived = 0', 'section = ?'];
$params = [strtoupper($section)];

if (!empty($course) && $course !== 'All') {
    $whereConditions[] = 'course = ?';
    $params[] = $course;
}

if (!empty($year) && $year !== 'All') {
    $whereConditions[] = 'year_level = ?';
    $params[] = $year;
}

$whereClause = 'WHERE ' . implode(' AND ', $whereConditions);

$sql = "
    SELECT 
        id,
        CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) as full_name,
        course,
        year_level,
        section,
        membership_fee_status,
        email
    FROM students 
    $whereClause
    ORDER BY membership_fee_status DESC, last_name, first_name
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

$paidStudents = array_filter($students, function($s) {
    return $s['membership_fee_status'] === 'paid';
});

$unpaidStudents = array_filter($students, function($s) {
    return $s['membership_fee_status'] !== 'paid';
});

$sectionLabel = strtoupper($section);
$courseLabel = (!empty($course) && $course !== 'All') ? $course : 'All Courses';
$yearLabel = (!empty($year) && $year !== 'All') ? $year . ' Year' : 'All Years';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Section Report - <?php echo $sectionLabel; ?></title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none;
            }
            .page-break {
                page-break-before: always;
            }
        }
        
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }
        
        .report-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #9933ff;
            padding-bottom: 20px;
        }
        
        .report-header h1 {
            margin: 0 0 10px 0;
            color: #9933ff;
            font-size: 28px;
        }
        
        .report-header h2 {
            margin: 5px 0;
            color: #666;
            font-size: 18px;
            font-weight: normal;
        }
        
        .report-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .info-item {
            flex: 1;
        }
        
        .info-label {
            font-weight: bold;
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
        }
        
        .info-value {
            font-size: 16px;
            color: #333;
            margin-top: 5px;
        }
        
        .summary-section {
            margin: 20px 0 30px 0;
            padding: 20px;
            background: linear-gradient(135deg, #9933ff 0%, #6610f2 100%);
            color: white;
            border-radius: 8px;
        }
        
        .summary-section h3 {
            margin: 0 0 15px 0;
            font-size: 20px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        
        .summary-card {
            background: rgba(255, 255, 255, 0.2);
            padding: 15px;
            border-radius: 6px;
            text-align: center;
        }
        
        .summary-card .label {
            font-size: 12px;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        
        .summary-card .value {
            font-size: 32px;
            font-weight: bold;
        }
        
        .students-section {
            margin: 30px 0;
        }
        
        .students-section h3 {
            font-size: 20px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
            color: #333;
        }
        
        .students-section.paid h3 {
            color: #28a745;
            border-bottom-color: #28a745;
        }
        
        .students-section.unpaid h3 {
            color: #ffc107;
            border-bottom-color: #ffc107;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table thead {
            background: #f8f9fa;
        }
        
        table th {
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #e2e8f0;
            font-size: 12px;
            text-transform: uppercase;
        }
        
        table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }
        
        table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .no-students {
            text-align: center;
            padding: 40px;
            color: #999;
            font-style: italic;
        }
        
        .report-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #666;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background: #9933ff;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(153, 51, 255, 0.3);
            z-index: 1000;
        }
        
        .print-button:hover {
            background: #7c2dcc;
        }
        
        .print-button i {
            margin-right: 8px;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">
        <i class="fas fa-print"></i> Print Report
    </button>
    
    <div class="report-header">
        <h1>SOCCS Membership Fee Report</h1>
        <h2>Section <?php echo $sectionLabel; ?> - <?php echo $courseLabel; ?> <?php echo $yearLabel; ?></h2>
    </div>
    
    <div class="report-info">
        <div class="info-item">
            <div class="info-label">Report Date</div>
            <div class="info-value"><?php echo date('F d, Y'); ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Generated By</div>
            <div class="info-value">SOCCS Admin</div>
        </div>
        <div class="info-item">
            <div class="info-label">Report Type</div>
            <div class="info-value">Membership Fee Status</div>
        </div>
    </div>
    
    <div class="summary-section">
        <h3>Summary Statistics</h3>
        <div class="summary-grid">
            <div class="summary-card">
                <div class="label">Total Students</div>
                <div class="value"><?php echo count($students); ?></div>
            </div>
            <div class="summary-card">
                <div class="label">Paid</div>
                <div class="value"><?php echo count($paidStudents); ?></div>
            </div>
            <div class="summary-card">
                <div class="label">Unpaid</div>
                <div class="value"><?php echo count($unpaidStudents); ?></div>
            </div>
        </div>
    </div>
    
    <div class="students-section paid">
        <h3><i class="fas fa-check-circle"></i> Paid Students (<?php echo count($paidStudents); ?>)</h3>
        <?php if (count($paidStudents) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th style="width: 15%;">Student ID</th>
                        <th style="width: 40%;">Full Name</th>
                        <th style="width: 15%;">Course</th>
                        <th style="width: 15%;">Year</th>
                        <th style="width: 15%;">Section</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($paidStudents as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['id']); ?></td>
                            <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['course']); ?></td>
                            <td><?php echo $student['year_level']; ?></td>
                            <td><?php echo htmlspecialchars($student['section']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-students">No paid students found</div>
        <?php endif; ?>
    </div>
    
    <?php if (count($paidStudents) > 0 && count($unpaidStudents) > 0): ?>
        <div class="page-break"></div>
    <?php endif; ?>
    
    <div class="students-section unpaid">
        <h3><i class="fas fa-exclamation-circle"></i> Unpaid Students (<?php echo count($unpaidStudents); ?>)</h3>
        <?php if (count($unpaidStudents) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th style="width: 15%;">Student ID</th>
                        <th style="width: 40%;">Full Name</th>
                        <th style="width: 15%;">Course</th>
                        <th style="width: 15%;">Year</th>
                        <th style="width: 15%;">Section</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($unpaidStudents as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['id']); ?></td>
                            <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['course']); ?></td>
                            <td><?php echo $student['year_level']; ?></td>
                            <td><?php echo htmlspecialchars($student['section']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-students">No unpaid students found</div>
        <?php endif; ?>
    </div>
    
    <div class="report-footer">
        <div>
            <strong>SOCCS Financial Management System</strong><br>
            School of Computing and Communication Studies
        </div>
        <div style="text-align: right;">
            Page 1 of 1<br>
            <?php echo date('Y-m-d H:i:s'); ?>
        </div>
    </div>
</body>
</html>

