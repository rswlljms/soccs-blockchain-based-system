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

class ElectionPDF extends Fpdi {
    public $currentTimestamp;
    public $totalPages = 0;
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', '', 7);
        $this->SetTextColor(102, 102, 102);
        $this->Cell(80, 4, 'Page ' . $this->PageNo() . ' of {nb}', 0, 0, 'L');
        $this->Cell(0, 4, 'Generated: ' . $this->currentTimestamp, 0, 0, 'R');
    }
}

$database = new Database();
$pdo = $database->getConnection();

$electionId = $_GET['election_id'] ?? null;

if (!$electionId) {
    die('Election ID is required');
}

$electionQuery = "SELECT * FROM elections WHERE id = ?";
$electionStmt = $pdo->prepare($electionQuery);
$electionStmt->execute([$electionId]);
$election = $electionStmt->fetch(PDO::FETCH_ASSOC);

if (!$election) {
    die('Election not found');
}

$posQuery = "SELECT * FROM positions ORDER BY id ASC";
$posStmt = $pdo->prepare($posQuery);
$posStmt->execute();
$positions = $posStmt->fetchAll(PDO::FETCH_ASSOC);

$electionResults = [];
$positionMaxVotes = [];
$totalVotes = 0;
$totalVoters = 0;

foreach ($positions as $position) {
    $candQuery = "SELECT 
        c.id,
        CONCAT(c.firstname, ' ', c.lastname) as name,
        c.partylist,
        COALESCE(COUNT(v.id), 0) as votes
    FROM candidates c
    LEFT JOIN votes v ON c.id = v.candidate_id AND v.election_id = ?
    WHERE c.position_id = ?
    GROUP BY c.id
    ORDER BY votes DESC, c.lastname ASC";
    
    $candStmt = $pdo->prepare($candQuery);
    $candStmt->execute([$electionId, $position['id']]);
    $candidates = $candStmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($candidates)) {
        $electionResults[$position['description']] = $candidates;
        $positionMaxVotes[$position['description']] = (int)$position['max_votes'];
    }
}

$statsQuery = "SELECT 
    COUNT(DISTINCT voter_id) as total_voters,
    COUNT(*) as total_votes
FROM votes 
WHERE election_id = ?";
$statsStmt = $pdo->prepare($statsQuery);
$statsStmt->execute([$electionId]);
$stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

$totalVotes = $stats['total_votes'];
$totalVoters = $stats['total_voters'];

$pdf = new ElectionPDF();
$pdf->currentTimestamp = date('Y-m-d H:i:s');
$pdf->AliasNbPages();
$pdf->SetMargins(20, 10, 20);
$pdf->SetAutoPageBreak(false, 20);

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
$pdf->Cell(210, 10, 'SOCCS Election Results', 0, 0, 'C');

$pdf->SetFont('Arial', '', 12);
$pdf->SetTextColor(102, 102, 102);
$pdf->SetXY(0, 52);
$pdf->Cell(210, 8, $election['title'], 0, 0, 'C');

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(102, 102, 102);
$pdf->SetXY(40, 65);
$pdf->Cell(50, 6, 'ELECTION DATE', 0, 0, 'L');
$pdf->SetXY(100, 65);
$pdf->Cell(50, 6, 'TOTAL VOTERS', 0, 0, 'L');
$pdf->SetXY(160, 65);
$pdf->Cell(50, 6, 'TOTAL VOTES', 0, 0, 'L');

$pdf->SetFont('Arial', '', 11);
$pdf->SetTextColor(51, 51, 51);
$electionDate = date('F d, Y', strtotime($election['start_date']));
$pdf->SetXY(40, 72);
$pdf->Cell(50, 6, $electionDate, 0, 0, 'L');
$pdf->SetXY(100, 72);
$pdf->Cell(50, 6, $totalVoters . ' voters', 0, 0, 'L');
$pdf->SetXY(160, 72);
$pdf->Cell(50, 6, $totalVotes . ' votes', 0, 0, 'L');

$currentY = 90;

if (count($electionResults) > 0) {
    foreach ($electionResults as $position => $candidates) {
        $maxWinners = $positionMaxVotes[$position] ?? 1;
        
        if ($currentY > 250) {
            $pdf->AddPage('P', 'A4');
            $pdf->useTemplate($template, 0, 0, 210);
            $currentY = 62;
        }
        
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor(75, 0, 130);
        $pdf->SetXY(20, $currentY);
        $pdf->Cell(170, 8, strtoupper($position), 0, 0, 'L');
        $currentY += 10;
        
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor(73, 80, 87);
        $pdf->SetFillColor(248, 249, 250);
        $pdf->Rect(20, $currentY, 170, 6, 'F');
        
        $pdf->SetXY(22, $currentY + 1);
        $pdf->Cell(10, 4, 'RANK', 0, 0, 'C');
        $pdf->SetXY(35, $currentY + 1);
        $pdf->Cell(70, 4, 'CANDIDATE NAME', 0, 0, 'L');
        $pdf->SetXY(110, $currentY + 1);
        $pdf->Cell(40, 4, 'PARTYLIST', 0, 0, 'L');
        $pdf->SetXY(155, $currentY + 1);
        $pdf->Cell(20, 4, 'VOTES', 0, 0, 'C');
        $pdf->SetXY(175, $currentY + 1);
        $pdf->Cell(15, 4, 'STATUS', 0, 0, 'C');
        
        $currentY += 8;
        
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(51, 51, 51);
        
        $rank = 1;
        foreach ($candidates as $candidate) {
            if ($currentY > 265) {
                $pdf->AddPage('P', 'A4');
                $pdf->useTemplate($template, 0, 0, 210);
                $currentY = 62;
                
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->SetTextColor(75, 0, 130);
                $pdf->SetXY(20, $currentY);
                $pdf->Cell(170, 8, strtoupper($position) . ' (continued)', 0, 0, 'L');
                $currentY += 10;
                
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->SetTextColor(73, 80, 87);
                $pdf->SetFillColor(248, 249, 250);
                $pdf->Rect(20, $currentY, 170, 6, 'F');
                
                $pdf->SetXY(22, $currentY + 1);
                $pdf->Cell(10, 4, 'RANK', 0, 0, 'C');
                $pdf->SetXY(35, $currentY + 1);
                $pdf->Cell(70, 4, 'CANDIDATE NAME', 0, 0, 'L');
                $pdf->SetXY(110, $currentY + 1);
                $pdf->Cell(40, 4, 'PARTYLIST', 0, 0, 'L');
                $pdf->SetXY(155, $currentY + 1);
                $pdf->Cell(20, 4, 'VOTES', 0, 0, 'C');
                $pdf->SetXY(175, $currentY + 1);
                $pdf->Cell(15, 4, 'STATUS', 0, 0, 'C');
                
                $currentY += 8;
                $pdf->SetFont('Arial', '', 9);
                $pdf->SetTextColor(51, 51, 51);
            }
            
            $isWinner = ($rank <= $maxWinners && $candidate['votes'] > 0);
            
            if ($isWinner) {
                $pdf->SetFillColor(220, 252, 231);
                $pdf->Rect(20, $currentY - 1, 170, 7, 'F');
            }
            
            $pdf->SetXY(22, $currentY);
            $pdf->Cell(10, 5, $rank, 0, 0, 'C');
            $pdf->SetXY(35, $currentY);
            $pdf->Cell(70, 5, substr($candidate['name'], 0, 35), 0, 0, 'L');
            $pdf->SetXY(110, $currentY);
            $pdf->Cell(40, 5, substr($candidate['partylist'], 0, 20), 0, 0, 'L');
            $pdf->SetXY(155, $currentY);
            $pdf->Cell(20, 5, $candidate['votes'], 0, 0, 'C');
            
            if ($isWinner) {
                $pdf->SetFont('Arial', 'B', 7);
                $pdf->SetTextColor(21, 128, 61);
                $pdf->SetXY(175, $currentY);
                $pdf->Cell(15, 5, 'WINNER', 0, 0, 'C');
                $pdf->SetFont('Arial', '', 9);
                $pdf->SetTextColor(51, 51, 51);
            } else {
                $pdf->SetXY(175, $currentY);
                $pdf->Cell(15, 5, '-', 0, 0, 'C');
            }
            
            $currentY += 7;
            $rank++;
        }
        
        $currentY += 5;
    }
} else {
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->SetTextColor(153, 153, 153);
    $pdf->SetXY(20, $currentY + 10);
    $pdf->Cell(170, 6, 'No results available', 0, 0, 'C');
}

$electionTitle = preg_replace('/[^A-Za-z0-9_\-]/', '_', $election['title']);
$filename = 'SOCCS_Election_Results_' . $electionTitle . '_' . date('Y-m-d_His') . '.pdf';
$pdf->Output('I', $filename);
?>
