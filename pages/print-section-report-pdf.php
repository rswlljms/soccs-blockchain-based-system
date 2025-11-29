<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../templates/login.php");
    exit;
}

date_default_timezone_set('Asia/Manila');

require_once '../vendor/autoload.php';
require_once '../includes/database.php';

use setasign\Fpdi\Fpdi;

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
        membership_fee_receipt,
        membership_fee_paid_at,
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
$courseLabel = (!empty($course) && $course !== 'All') ? $course : 'BSIT';
$yearInput = (!empty($year) && $year !== 'All') ? $year : '3';

// Add suffix to year (1st, 2nd, 3rd, 4th)
$yearSuffix = ['1' => 'st', '2' => 'nd', '3' => 'rd', '4' => 'th'];
$yearLabel = $yearInput . ($yearSuffix[$yearInput] ?? 'th');

$pdf = new Fpdi();
$pdf->SetMargins(0, 0, 0);
$pdf->SetAutoPageBreak(false, 0);

$templatePath = '../assets/img/soccs_reporting_format.pdf';

if (!file_exists($templatePath)) {
    die('Template PDF not found: ' . $templatePath);
}

$pageCount = $pdf->setSourceFile($templatePath);
$template = $pdf->importPage(1);

$pdf->AddPage('P', 'A4');
$pdf->useTemplate($template, 0, 0, 210);

$pdf->SetFont('Arial', 'B', 16);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetXY(0, 42);
$pdf->Cell(210, 10, 'SOCCS Membership Fee Report', 0, 0, 'C');

$pdf->SetFont('Arial', '', 12);
$pdf->SetTextColor(102, 102, 102);
$pdf->SetXY(0, 52);
$pdf->Cell(210, 8, 'Section ' . $sectionLabel . ' - ' . $courseLabel . ' ' . $yearLabel . ' Year', 0, 0, 'C');

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(102, 102, 102);
$pdf->SetXY(50, 65);
$pdf->Cell(50, 6, 'REPORT DATE', 0, 0, 'L');
$pdf->SetXY(130, 65);
$pdf->Cell(50, 6, 'REPORT TYPE', 0, 0, 'L');

$pdf->SetFont('Arial', '', 11);
$pdf->SetTextColor(51, 51, 51);
$pdf->SetXY(50, 72);
$pdf->Cell(50, 6, date('F d, Y'), 0, 0, 'L');
$pdf->SetXY(130, 72);
$pdf->Cell(50, 6, 'Membership Fee Status', 0, 0, 'L');

$currentY = 90;

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(40, 167, 69);
$pdf->SetXY(25, $currentY);
$pdf->Cell(10, 6, chr(149), 0, 0, 'L');
$pdf->SetXY(30, $currentY);
$pdf->Cell(100, 6, 'Paid Students (' . count($paidStudents) . ')', 0, 0, 'L');

$currentY += 10;

if (count($paidStudents) > 0) {
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetTextColor(73, 80, 87);
    $pdf->SetFillColor(248, 249, 250);
    $pdf->Rect(25, $currentY, 160, 6, 'F');
    
    $pdf->SetXY(27, $currentY + 1);
    $pdf->Cell(20, 4, 'STUDENT ID', 0, 0, 'L');
    $pdf->SetXY(50, $currentY + 1);
    $pdf->Cell(65, 4, 'FULL NAME', 0, 0, 'L');
    $pdf->SetXY(120, $currentY + 1);
    $pdf->Cell(20, 4, 'COURSE', 0, 0, 'L');
    $pdf->SetXY(145, $currentY + 1);
    $pdf->Cell(15, 4, 'YEAR', 0, 0, 'L');
    $pdf->SetXY(165, $currentY + 1);
    $pdf->Cell(15, 4, 'SECTION', 0, 0, 'L');
    
    $currentY += 8;
    
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetTextColor(51, 51, 51);
    
    foreach ($paidStudents as $student) {
        if ($currentY > 260) {
            $pdf->AddPage('P', 'A4');
            $pdf->useTemplate($template, 0, 0, 210);
            $currentY = 30;
        }
        
        $pdf->SetXY(27, $currentY);
        $pdf->Cell(20, 5, $student['id'], 0, 0, 'L');
        $pdf->SetXY(50, $currentY);
        $pdf->Cell(65, 5, substr($student['full_name'], 0, 35), 0, 0, 'L');
        $pdf->SetXY(120, $currentY);
        $pdf->Cell(20, 5, $student['course'], 0, 0, 'L');
        $pdf->SetXY(145, $currentY);
        $studentYearSuffix = $yearSuffix[$student['year_level']] ?? 'th';
        $pdf->Cell(15, 5, $student['year_level'] . $studentYearSuffix, 0, 0, 'L');
        $pdf->SetXY(165, $currentY);
        $pdf->Cell(15, 5, $student['section'], 0, 0, 'L');
        
        $currentY += 6;
    }
} else {
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->SetTextColor(153, 153, 153);
    $pdf->SetXY(25, $currentY + 10);
    $pdf->Cell(160, 6, 'No paid students found', 0, 0, 'C');
    $currentY += 20;
}

$currentY += 10;

if ($currentY > 240) {
    $pdf->AddPage('P', 'A4');
    $pdf->useTemplate($template, 0, 0, 210);
    $currentY = 30;
}

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(255, 193, 7);
$pdf->SetXY(25, $currentY);
$pdf->Cell(10, 6, chr(149), 0, 0, 'L');
$pdf->SetXY(30, $currentY);
$pdf->Cell(100, 6, 'Unpaid Students (' . count($unpaidStudents) . ')', 0, 0, 'L');

$currentY += 10;

if (count($unpaidStudents) > 0) {
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetTextColor(73, 80, 87);
    $pdf->SetFillColor(248, 249, 250);
    $pdf->Rect(25, $currentY, 160, 6, 'F');
    
    $pdf->SetXY(27, $currentY + 1);
    $pdf->Cell(20, 4, 'STUDENT ID', 0, 0, 'L');
    $pdf->SetXY(50, $currentY + 1);
    $pdf->Cell(65, 4, 'FULL NAME', 0, 0, 'L');
    $pdf->SetXY(120, $currentY + 1);
    $pdf->Cell(20, 4, 'COURSE', 0, 0, 'L');
    $pdf->SetXY(145, $currentY + 1);
    $pdf->Cell(15, 4, 'YEAR', 0, 0, 'L');
    $pdf->SetXY(165, $currentY + 1);
    $pdf->Cell(15, 4, 'SECTION', 0, 0, 'L');
    
    $currentY += 8;
    
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetTextColor(51, 51, 51);
    
    foreach ($unpaidStudents as $student) {
        if ($currentY > 260) {
            $pdf->AddPage('P', 'A4');
            $pdf->useTemplate($template, 0, 0, 210);
            $currentY = 30;
        }
        
        $pdf->SetXY(27, $currentY);
        $pdf->Cell(20, 5, $student['id'], 0, 0, 'L');
        $pdf->SetXY(50, $currentY);
        $pdf->Cell(65, 5, substr($student['full_name'], 0, 35), 0, 0, 'L');
        $pdf->SetXY(120, $currentY);
        $pdf->Cell(20, 5, $student['course'], 0, 0, 'L');
        $pdf->SetXY(145, $currentY);
        $studentYearSuffix = $yearSuffix[$student['year_level']] ?? 'th';
        $pdf->Cell(15, 5, $student['year_level'] . $studentYearSuffix, 0, 0, 'L');
        $pdf->SetXY(165, $currentY);
        $pdf->Cell(15, 5, $student['section'], 0, 0, 'L');
        
        $currentY += 6;
    }
} else {
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->SetTextColor(153, 153, 153);
    $pdf->SetXY(25, $currentY + 10);
    $pdf->Cell(160, 6, 'No unpaid students found', 0, 0, 'C');
}

$pdf->SetFont('Arial', '', 7);
$pdf->SetTextColor(102, 102, 102);
$pdf->SetXY(25, 280);
$pdf->Cell(80, 4, 'Page 1 of 1', 0, 0, 'L');
$pdf->SetXY(105, 280);
$pdf->Cell(80, 4, date('Y-m-d H:i:s'), 0, 0, 'R');

$pdf->Output('I', 'SOCCS_Section_Report_' . $sectionLabel . '.pdf');
?>

