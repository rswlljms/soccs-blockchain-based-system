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

$dateFilter = $_GET['date_filter'] ?? 'All';

$whereConditions = ['1=1'];
$params = [];

if (!empty($dateFilter) && $dateFilter !== 'All') {
    switch ($dateFilter) {
        case 'Today':
            $whereConditions[] = 'DATE(date_received) = CURDATE()';
            break;
        case 'Week':
            $whereConditions[] = 'date_received >= DATE_SUB(NOW(), INTERVAL 7 DAY)';
            break;
        case 'Month':
            $whereConditions[] = 'date_received >= DATE_SUB(NOW(), INTERVAL 30 DAY)';
            break;
        case 'Year':
            $whereConditions[] = 'YEAR(date_received) = YEAR(NOW())';
            break;
    }
}

$whereClause = 'WHERE ' . implode(' AND ', $whereConditions);

$sql = "
    SELECT 
        id,
        source,
        amount,
        description,
        date_received,
        transaction_hash,
        created_at
    FROM funds 
    $whereClause
    ORDER BY date_received DESC, created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$funds = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalAmount = array_sum(array_column($funds, 'amount'));

$dateRangeLabel = '';
switch ($dateFilter) {
    case 'Today':
        $dateRangeLabel = 'Today - ' . date('M d, Y');
        break;
    case 'Week':
        $dateRangeLabel = 'This Week';
        break;
    case 'Month':
        $dateRangeLabel = 'This Month - ' . date('F Y');
        break;
    case 'Year':
        $dateRangeLabel = 'This Year - ' . date('Y');
        break;
    default:
        $dateRangeLabel = 'All Time';
        break;
}

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
$pdf->Cell(210, 10, 'SOCCS Budget Report', 0, 0, 'C');

$pdf->SetFont('Arial', '', 12);
$pdf->SetTextColor(102, 102, 102);
$pdf->SetXY(0, 52);
$pdf->Cell(210, 8, $dateRangeLabel, 0, 0, 'C');

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(102, 102, 102);
$pdf->SetXY(40, 65);
$pdf->Cell(50, 6, 'REPORT DATE', 0, 0, 'L');
$pdf->SetXY(100, 65);
$pdf->Cell(50, 6, 'TOTAL RECORDS', 0, 0, 'L');
$pdf->SetXY(160, 65);
$pdf->Cell(50, 6, 'TOTAL AMOUNT', 0, 0, 'L');

$pdf->SetFont('Arial', '', 11);
$pdf->SetTextColor(51, 51, 51);
$currentDate = date('F d, Y');
$pdf->SetXY(40, 72);
$pdf->Cell(50, 6, $currentDate, 0, 0, 'L');
$pdf->SetXY(100, 72);
$pdf->Cell(50, 6, count($funds) . ' items', 0, 0, 'L');
$pdf->SetXY(160, 72);
$pdf->Cell(50, 6, 'P' . number_format($totalAmount, 2), 0, 0, 'L');

$currentY = 90;

if (count($funds) > 0) {
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetTextColor(73, 80, 87);
    $pdf->SetFillColor(248, 249, 250);
    $pdf->Rect(20, $currentY, 170, 6, 'F');
    
    $pdf->SetXY(22, $currentY + 1);
    $pdf->Cell(25, 4, 'DATE', 0, 0, 'L');
    $pdf->SetXY(50, $currentY + 1);
    $pdf->Cell(100, 4, 'DESCRIPTION', 0, 0, 'L');
    $pdf->SetXY(155, $currentY + 1);
    $pdf->Cell(35, 4, 'AMOUNT', 0, 0, 'R');
    
    $currentY += 8;
    
    $pdf->SetFont('Arial', '', 7);
    $pdf->SetTextColor(51, 51, 51);
    
    foreach ($funds as $fund) {
        if ($currentY > 260) {
            $pdf->AddPage('P', 'A4');
            $pdf->useTemplate($template, 0, 0, 210);
            $currentY = 30;
            
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetTextColor(73, 80, 87);
            $pdf->SetFillColor(248, 249, 250);
            $pdf->Rect(20, $currentY, 170, 6, 'F');
            
            $pdf->SetXY(22, $currentY + 1);
            $pdf->Cell(25, 4, 'DATE', 0, 0, 'L');
            $pdf->SetXY(50, $currentY + 1);
            $pdf->Cell(100, 4, 'DESCRIPTION', 0, 0, 'L');
            $pdf->SetXY(155, $currentY + 1);
            $pdf->Cell(35, 4, 'AMOUNT', 0, 0, 'R');
            
            $currentY += 8;
            $pdf->SetFont('Arial', '', 7);
            $pdf->SetTextColor(51, 51, 51);
        }
        
        $pdf->SetXY(22, $currentY);
        $pdf->Cell(25, 5, date('Y-m-d', strtotime($fund['date_received'])), 0, 0, 'L');
        $pdf->SetXY(50, $currentY);
        $pdf->Cell(100, 5, substr($fund['description'], 0, 50), 0, 0, 'L');
        $pdf->SetXY(155, $currentY);
        $pdf->Cell(35, 5, 'P' . number_format($fund['amount'], 2), 0, 0, 'R');
        
        $currentY += 6;
    }
} else {
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->SetTextColor(153, 153, 153);
    $pdf->SetXY(20, $currentY + 10);
    $pdf->Cell(170, 6, 'No budget found', 0, 0, 'C');
}

$pdf->SetFont('Arial', '', 7);
$pdf->SetTextColor(102, 102, 102);
$currentTimestamp = date('Y-m-d H:i:s');
$pdf->SetXY(20, 280);
$pdf->Cell(80, 4, 'Page 1 of 1', 0, 0, 'L');
$pdf->SetXY(110, 280);
$pdf->Cell(80, 4, 'Generated: ' . $currentTimestamp, 0, 0, 'R');

$filename = 'SOCCS_Budget_Report_' . date('Y-m-d_His') . '.pdf';
$pdf->Output('I', $filename);
?>

