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

$category = $_GET['category'] ?? 'All';
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';

$whereConditions = ['1=1'];
$params = [];

if (!empty($category) && $category !== 'All') {
    $whereConditions[] = 'category = ?';
    $params[] = $category;
}

if (!empty($startDate)) {
    $whereConditions[] = 'date >= ?';
    $params[] = $startDate;
}

if (!empty($endDate)) {
    $whereConditions[] = 'date <= ?';
    $params[] = $endDate;
}

$whereClause = 'WHERE ' . implode(' AND ', $whereConditions);

$sql = "
    SELECT 
        id,
        name,
        amount,
        category,
        description,
        supplier,
        date,
        document,
        transaction_hash
    FROM expenses 
    $whereClause
    ORDER BY date DESC, id DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalAmount = array_sum(array_column($expenses, 'amount'));

$categoryLabel = (!empty($category) && $category !== 'All') ? $category : 'All Categories';
$dateRangeLabel = '';
if (!empty($startDate) && !empty($endDate)) {
    $dateRangeLabel = date('M d, Y', strtotime($startDate)) . ' - ' . date('M d, Y', strtotime($endDate));
} elseif (!empty($startDate)) {
    $dateRangeLabel = 'From ' . date('M d, Y', strtotime($startDate));
} elseif (!empty($endDate)) {
    $dateRangeLabel = 'Until ' . date('M d, Y', strtotime($endDate));
} else {
    $dateRangeLabel = 'All Time';
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
$pdf->Cell(210, 10, 'SOCCS Expenses Report', 0, 0, 'C');

$pdf->SetFont('Arial', '', 12);
$pdf->SetTextColor(102, 102, 102);
$pdf->SetXY(0, 52);
$pdf->Cell(210, 8, $categoryLabel . ' - ' . $dateRangeLabel, 0, 0, 'C');

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(102, 102, 102);
$pdf->SetXY(40, 65);
$pdf->Cell(50, 6, 'REPORT DATE', 0, 0, 'L');
$pdf->SetXY(100, 65);
$pdf->Cell(50, 6, 'TOTAL EXPENSES', 0, 0, 'L');
$pdf->SetXY(160, 65);
$pdf->Cell(50, 6, 'TOTAL AMOUNT', 0, 0, 'L');

$pdf->SetFont('Arial', '', 11);
$pdf->SetTextColor(51, 51, 51);
$currentDate = date('F d, Y');
$pdf->SetXY(40, 72);
$pdf->Cell(50, 6, $currentDate, 0, 0, 'L');
$pdf->SetXY(100, 72);
$pdf->Cell(50, 6, count($expenses) . ' items', 0, 0, 'L');
$pdf->SetXY(160, 72);
$pdf->Cell(50, 6, 'P' . number_format($totalAmount, 2), 0, 0, 'L');

$currentY = 90;

if (count($expenses) > 0) {
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetTextColor(73, 80, 87);
    $pdf->SetFillColor(248, 249, 250);
    $pdf->Rect(20, $currentY, 170, 6, 'F');
    
    $pdf->SetXY(22, $currentY + 1);
    $pdf->Cell(20, 4, 'DATE', 0, 0, 'L');
    $pdf->SetXY(42, $currentY + 1);
    $pdf->Cell(45, 4, 'EXPENSE NAME', 0, 0, 'L');
    $pdf->SetXY(90, $currentY + 1);
    $pdf->Cell(25, 4, 'CATEGORY', 0, 0, 'L');
    $pdf->SetXY(118, $currentY + 1);
    $pdf->Cell(25, 4, 'SUPPLIER', 0, 0, 'L');
    $pdf->SetXY(145, $currentY + 1);
    $pdf->Cell(40, 4, 'DESCRIPTION', 0, 0, 'L');
    $pdf->SetXY(175, $currentY + 1);
    $pdf->Cell(15, 4, 'AMOUNT', 0, 0, 'R');
    
    $currentY += 8;
    
    $pdf->SetFont('Arial', '', 7);
    $pdf->SetTextColor(51, 51, 51);
    
    foreach ($expenses as $expense) {
        if ($currentY > 260) {
            $pdf->AddPage('P', 'A4');
            $pdf->useTemplate($template, 0, 0, 210);
            $currentY = 30;
            
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetTextColor(73, 80, 87);
            $pdf->SetFillColor(248, 249, 250);
            $pdf->Rect(20, $currentY, 170, 6, 'F');
            
            $pdf->SetXY(22, $currentY + 1);
            $pdf->Cell(20, 4, 'DATE', 0, 0, 'L');
            $pdf->SetXY(42, $currentY + 1);
            $pdf->Cell(45, 4, 'EXPENSE NAME', 0, 0, 'L');
            $pdf->SetXY(90, $currentY + 1);
            $pdf->Cell(25, 4, 'CATEGORY', 0, 0, 'L');
            $pdf->SetXY(118, $currentY + 1);
            $pdf->Cell(25, 4, 'SUPPLIER', 0, 0, 'L');
            $pdf->SetXY(145, $currentY + 1);
            $pdf->Cell(40, 4, 'DESCRIPTION', 0, 0, 'L');
            $pdf->SetXY(175, $currentY + 1);
            $pdf->Cell(15, 4, 'AMOUNT', 0, 0, 'R');
            
            $currentY += 8;
            $pdf->SetFont('Arial', '', 7);
            $pdf->SetTextColor(51, 51, 51);
        }
        
        $pdf->SetXY(22, $currentY);
        $pdf->Cell(20, 5, date('Y-m-d', strtotime($expense['date'])), 0, 0, 'L');
        $pdf->SetXY(42, $currentY);
        $pdf->Cell(45, 5, substr($expense['name'], 0, 25), 0, 0, 'L');
        $pdf->SetXY(90, $currentY);
        $pdf->Cell(25, 5, substr($expense['category'], 0, 15), 0, 0, 'L');
        $pdf->SetXY(118, $currentY);
        $pdf->Cell(25, 5, substr($expense['supplier'], 0, 15), 0, 0, 'L');
        $pdf->SetXY(145, $currentY);
        $pdf->Cell(40, 5, substr($expense['description'], 0, 22), 0, 0, 'L');
        $pdf->SetXY(175, $currentY);
        $pdf->Cell(15, 5, 'P' . number_format($expense['amount'], 2), 0, 0, 'R');
        
        $currentY += 6;
    }
} else {
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->SetTextColor(153, 153, 153);
    $pdf->SetXY(20, $currentY + 10);
    $pdf->Cell(170, 6, 'No expenses found', 0, 0, 'C');
}

$pdf->SetFont('Arial', '', 7);
$pdf->SetTextColor(102, 102, 102);
$currentTimestamp = date('Y-m-d H:i:s');
$pdf->SetXY(20, 280);
$pdf->Cell(80, 4, 'Page 1 of 1', 0, 0, 'L');
$pdf->SetXY(110, 280);
$pdf->Cell(80, 4, 'Generated: ' . $currentTimestamp, 0, 0, 'R');

$filename = 'SOCCS_Expenses_Report_' . date('Y-m-d_His') . '.pdf';
$pdf->Output('I', $filename);
?>

