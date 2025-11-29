<?php

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/app_config.php';
require_once __DIR__ . '/image_preprocessor.php';

class DocumentVerificationService {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function enqueueJob($studentId, $minDelayMinutes = 5) {
        $minDelayMinutes = max(5, (int)$minDelayMinutes);
        $minProcessAfter = (new DateTime("+{$minDelayMinutes} minutes"))->format('Y-m-d H:i:s');

        $stmt = $this->conn->prepare("INSERT INTO document_verification_jobs (student_id, min_process_after) VALUES (?, ?)");
        $stmt->execute([$studentId, $minProcessAfter]);

        $this->markUnderReview($studentId);
    }

    private function markUnderReview($studentId) {
        $stmt = $this->conn->prepare("UPDATE student_registrations SET approval_status = 'pending' WHERE id = ?");
        $stmt->execute([$studentId]);
    }

    public function claimNextJob() {
        $this->conn->beginTransaction();
        try {
            $stmt = $this->conn->prepare("SELECT * FROM document_verification_jobs WHERE status='queued' AND min_process_after <= NOW() ORDER BY id ASC LIMIT 1 FOR UPDATE");
            $stmt->execute();
            $job = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$job) {
                $this->conn->commit();
                return null;
            }
            $upd = $this->conn->prepare("UPDATE document_verification_jobs SET status='processing', started_at = NOW() WHERE id = ?");
            $upd->execute([$job['id']]);
            $this->conn->commit();
            return $job;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function runVerification($studentId) {
        $stmt = $this->conn->prepare("SELECT * FROM student_registrations WHERE id = ?");
        $stmt->execute([$studentId]);
        $reg = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$reg) {
            return [
                'overall_result' => 'invalid',
                'reason' => 'Registration not found'
            ];
        }

        $studentIdImagePath = $reg['student_id_image'] ?? null;
        $corFilePath = $reg['cor_file'] ?? null;

        $qualityScore = $this->estimateImageQuality($studentIdImagePath);
        $tamperScore = $this->estimateTamperProbability($studentIdImagePath, $corFilePath);

        // Real OCR via OCR.Space
        error_log("Starting verification for student: {$studentId}");
        $ocrId = $this->runOcrSpace($studentIdImagePath);
        $ocrCor = $this->runOcrSpace($corFilePath);
        error_log("OCR ID text length: " . strlen($ocrId['text']) . ", confidence: " . $ocrId['confidence']);
        error_log("OCR COR text length: " . strlen($ocrCor['text']) . ", confidence: " . $ocrCor['confidence']);

        // Build expected fields (only: full name, student number, course)
        $fullNameParts = array_filter([
            trim((string)($reg['first_name'] ?? '')),
            trim((string)($reg['middle_name'] ?? '')),
            trim((string)($reg['last_name'] ?? ''))
        ]);
        $expectedFullName = trim(implode(' ', $fullNameParts));
        $expectedId = (string)$reg['id'];
        $expectedCourse = trim((string)($reg['course'] ?? ''));

        // Simplified verification: 
        // 1. Name (without middle name) must appear in Student ID OR COR
        // 2. Student ID number must appear in COR
        
        // Create name without middle name for matching
        $firstName = trim($reg['first_name']);
        $lastName = trim($reg['last_name']);
        $nameWithoutMiddle = $firstName . ' ' . $lastName;
        
        // Check name match in Student ID or COR (disregard middle name)
        $nameMatchId = $this->fuzzyContains($ocrId['text'], $nameWithoutMiddle);
        $nameMatchCor = $this->fuzzyContains($ocrCor['text'], $nameWithoutMiddle);
        
        // Check student ID number in COR (required)
        $studentNumberMatchCor = $this->fuzzyContains($ocrCor['text'], $expectedId);
        
        // Core matches: name found in any doc AND student ID found in COR
        $nameMatch = ($nameMatchId || $nameMatchCor);
        $studentNumberMatch = $studentNumberMatchCor; // Must be in COR
        
        // For DB compatibility
        $courseMatch = 1;
        $yearLevelMatch = 1;

        $reasons = [];
        $failedDocument = null;

        // Debug: Log what we're looking for vs what we found
        error_log("Looking for name (no middle): '{$nameWithoutMiddle}' in ID text: '" . substr($ocrId['text'], 0, 100) . "'");
        error_log("Looking for name (no middle): '{$nameWithoutMiddle}' in COR text: '" . substr($ocrCor['text'], 0, 100) . "'");
        error_log("Looking for Student ID: '{$expectedId}' in COR text: '" . substr($ocrCor['text'], 0, 100) . "'");
        error_log("Name match ID: " . ($nameMatchId ? 'YES' : 'NO') . ", COR: " . ($nameMatchCor ? 'YES' : 'NO'));
        error_log("Student ID match in COR: " . ($studentNumberMatchCor ? 'YES' : 'NO'));

        // Check if OCR completely failed (no text extracted)
        $idTextLength = strlen($ocrId['text']);
        $corTextLength = strlen($ocrCor['text']);
        if ($idTextLength < 10 && $corTextLength < 10) {
            $overall = 'mismatch';
            $reason = 'Documents could not be read. Please upload clearer images.';
            $reasons = ['OCR failed to extract text from documents'];
            $failedDocument = 'both';
        }
        
        // Additional check: if one document has very little text, it's likely wrong
        if (($idTextLength < 20 && $corTextLength < 20) || 
            ($idTextLength < 15 || $corTextLength < 15)) {
            $overall = 'mismatch';
            $reason = 'Documents appear to be unclear or incorrect. Please upload proper Student ID and Certificate of Registration.';
            $reasons = ['Documents contain insufficient readable text'];
            $failedDocument = 'both';
        }
        // Simple decision: if BOTH name and ID match anywhere, approve
        else if ($nameMatch && $studentNumberMatch) {
            $overall = 'valid';
            $reason = 'Name and student ID verified';
        } else {
            $overall = 'mismatch';
            if (!$nameMatch) { $reasons[] = 'Name not found in Student ID or COR'; }
            if (!$studentNumberMatch) { $reasons[] = 'Student ID number not found in COR'; }
            $failedDocument = 'both';
            $reason = implode('; ', $reasons);
        }

        // Final safety check: if no text was extracted at all, always reject
        if ($idTextLength < 5 && $corTextLength < 5) {
            $overall = 'mismatch';
            $reason = 'Documents could not be read. Please upload clearer images.';
            $reasons = ['OCR failed to extract text from documents'];
            $failedDocument = 'both';
        }

        // Debug: Log final decision
        error_log("FINAL DECISION: {$overall} - {$reason}");
        error_log("Text lengths - ID: {$idTextLength}, COR: {$corTextLength}");
        error_log("Name match: " . ($nameMatch ? 'YES' : 'NO') . ", Student ID match: " . ($studentNumberMatch ? 'YES' : 'NO'));

        return [
            'student_id_image_path' => $studentIdImagePath,
            'cor_file_path' => $corFilePath,
            'is_valid_id' => 1,
            'is_valid_cor' => 1,
            'name_match' => $nameMatch ? 1 : 0,
            'student_number_match' => $studentNumberMatch ? 1 : 0,
            'course_match' => $courseMatch ? 1 : 0,
            'year_level_match' => $yearLevelMatch ? 1 : 0,
            'tamper_score' => 0,
            'quality_score' => 1,
            'overall_result' => $overall,
            'reason' => $reason,
            'failed_document' => $failedDocument,
            'reasons' => $reasons
        ];
    }

    public function completeJob($jobId, $studentId, $result) {
        $this->conn->beginTransaction();
        try {
            $now = (new DateTime())->format('Y-m-d H:i:s');

            $ins = $this->conn->prepare("INSERT INTO document_verification_results (student_id, student_id_image_path, cor_file_path, is_valid_id, is_valid_cor, name_match, student_number_match, course_match, year_level_match, tamper_score, quality_score, overall_result, reason, started_at, completed_at, processing_time_seconds) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, TIMESTAMPDIFF(SECOND, ?, ?))");
            $ins->execute([
                $studentId,
                $result['student_id_image_path'] ?? null,
                $result['cor_file_path'] ?? null,
                $result['is_valid_id'] ?? null,
                $result['is_valid_cor'] ?? null,
                $result['name_match'] ?? null,
                $result['student_number_match'] ?? null,
                $result['course_match'] ?? null,
                $result['year_level_match'] ?? null,
                $result['tamper_score'] ?? null,
                $result['quality_score'] ?? null,
                $result['overall_result'] ?? null,
                $result['reason'] ?? null,
                $now,
                $now,
                $now,
                $now
            ]);

            $combined = $result['reason'] ?? null;
            if (!empty($result['reasons'])) {
                $combined .= "\nDetails: " . json_encode(['failed_document' => ($result['failed_document'] ?? null), 'reasons' => $result['reasons']], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
            $updJob = $this->conn->prepare("UPDATE document_verification_jobs SET status='completed', completed_at = NOW(), result = ?, reason = ? WHERE id = ?");
            $updJob->execute([$result['overall_result'] ?? null, $combined, $jobId]);

            $updReg = $this->conn->prepare("UPDATE student_registrations SET approval_status = ? WHERE id = ?");
            $updReg->execute([
                ($result['overall_result'] === 'valid' ? 'approved' : 'rejected'),
                $studentId
            ]);

            $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    private function estimateImageQuality($path) {
        if (!$path || !file_exists(__DIR__ . '/../' . $path)) return 0.0;
        $size = filesize(__DIR__ . '/../' . $path);
        $score = min(1.0, max(0.0, $size / (300 * 1024))); // ~300KB -> 1.0 cap
        return round($score, 2);
    }

    private function estimateTamperProbability($idPath, $corPath) {
        // Placeholder heuristic: very small files or uncommon extensions -> higher tamper suspicion
        $paths = array_filter([$idPath, $corPath]);
        if (empty($paths)) return 1.0;
        $base = 0.2;
        foreach ($paths as $p) {
            $full = __DIR__ . '/../' . $p;
            if (!file_exists($full)) { $base += 0.4; continue; }
            $ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','pdf'])) $base += 0.2;
            $size = max(1, filesize($full));
            if ($size < 80 * 1024) $base += 0.3; // very small
        }
        return min(1.0, round($base, 2));
    }

    private function runOcrSpace($relativePath) {
        $apiKey = AppConfig::get('OCR_SPACE_API_KEY', '');
        if (!$relativePath) return ['text' => '', 'confidence' => 0.0];
        $fullPath = realpath(__DIR__ . '/../' . $relativePath);
        if (!$fullPath || !file_exists($fullPath)) return ['text' => '', 'confidence' => 0.0];

        // Skip preprocessing for faster processing (optional optimization)
        // $preprocessedPath = ImagePreprocessor::preprocess($fullPath);
        // $fullPath = $preprocessedPath;

        // Use local file upload when possible; otherwise send base64 payload
        $endpoint = 'https://api.ocr.space/parse/image';
        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $isPdf = ($ext === 'pdf');
        // Map uncommon image formats to supported MIME where needed
        $isImage = in_array($ext, ['jpg','jpeg','png','webp','heic','heif','tif','tiff','bmp','gif']);
        $useFileUpload = (function_exists('curl_file_create') || class_exists('CURLFile'));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15); // 15 second timeout (faster)
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // 5 second connection timeout

        if ($useFileUpload) {
            $fileField = function_exists('curl_file_create')
                ? curl_file_create($fullPath, ($isPdf ? 'application/pdf' : ($isImage ? (mime_content_type($fullPath) ?: 'image/jpeg') : (mime_content_type($fullPath) ?: 'application/octet-stream'))), basename($fullPath))
                : new CURLFile($fullPath);
            $postFields = [
                'file' => $fileField,
                'language' => 'eng',
                'OCREngine' => '2',
                'scale' => 'true',
                'isOverlayRequired' => 'false',
                'isTable' => $isPdf ? 'true' : 'false'
            ];
            if ($isPdf) {
                $postFields['filetype'] = 'PDF';
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'apikey: ' . $apiKey ]);
        } else {
            $bytes = @file_get_contents($fullPath);
            if ($bytes === false) return ['text' => '', 'confidence' => 0.0];
            $mime = $isPdf ? 'application/pdf' : (mime_content_type($fullPath) ?: 'application/octet-stream');
            $b64 = 'data:' . $mime . ';base64,' . base64_encode($bytes);
            $params = [
                'base64Image' => $b64,
                'language' => 'eng',
                'OCREngine' => '2',
                'scale' => 'true',
                'isOverlayRequired' => 'false',
                'isTable' => $isPdf ? 'true' : 'false'
            ];
            if ($isPdf) {
                $params['filetype'] = 'PDF';
            }
            $body = http_build_query($params);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'apikey: ' . $apiKey,
                'Content-Type: application/x-www-form-urlencoded'
            ]);
        }
        $res = curl_exec($ch);
        if ($res === false) {
            curl_close($ch);
            return ['text' => '', 'confidence' => 0.0];
        }
        curl_close($ch);

        // Cleanup preprocessed file
        ImagePreprocessor::cleanup($fullPath);

        $data = json_decode($res, true);
        // Debug logging
        error_log("OCR Response for {$relativePath}: " . substr($res, 0, 500));
        
        if (!is_array($data) || empty($data['ParsedResults'][0])) {
            error_log("OCR failed or empty for {$relativePath}. Response: " . print_r($data, true));
            return ['text' => '', 'confidence' => 0.0];
        }
        $parsed = $data['ParsedResults'][0];
        $text = strtolower($parsed['ParsedText'] ?? '');
        error_log("OCR extracted text from {$relativePath}: " . substr($text, 0, 200));
        $conf = 0.0;
        if (isset($parsed['TextOverlay']['Lines']) && is_array($parsed['TextOverlay']['Lines'])) {
            $sum = 0; $count = 0;
            foreach ($parsed['TextOverlay']['Lines'] as $line) {
                foreach ($line['Words'] as $w) { $sum += ($w['WordConfidence'] ?? 0); $count++; }
            }
            if ($count > 0) $conf = $sum / (100 * $count); // normalize ~0..1
        }
        return ['text' => $text, 'confidence' => round($conf, 2)];
    }

    private function containsSchoolName($text) {
        $primary = AppConfig::get('SCHOOL_NAME_PRIMARY', '');
        $aliases = AppConfig::get('SCHOOL_NAME_ALIASES', '');
        $candidates = array_filter(array_map('trim', array_merge([$primary], explode(';', $aliases))));
        $t = strtolower($text ?? '');
        foreach ($candidates as $c) {
            if ($c && strpos($t, strtolower($c)) !== false) return true;
        }
        return false;
    }

    private function fuzzyContains($haystack, $needle) {
        $haystack = strtolower($haystack ?? '');
        $needle = strtolower($needle ?? '');
        if ($needle === '') return false;
        
        // First try exact match
        if (strpos($haystack, $needle) !== false) return true;
        
        // For names, require at least 80% of words to match (more strict)
        $parts = preg_split('/\s+/', $needle);
        $matches = 0; $total = 0;
        foreach ($parts as $p) {
            if ($p === '') continue;
            $total++;
            if (strpos($haystack, $p) !== false) $matches++;
        }
        
        // Require at least 80% match for names (was 60%)
        return $total > 0 ? ($matches / $total) >= 0.8 : false;
    }

    private function summarizeReasons($failedDocument, $reasons) {
        // Group issues concisely per document
        $docIssues = [ 'student_id' => [], 'cor' => [] ];
        foreach (array_unique($reasons) as $r) {
            $key = null; $tag = null;
            $lr = strtolower($r);
            if (strpos($lr, 'student id') !== false) $key = 'student_id';
            if (strpos($lr, 'cor:') !== false) $key = 'cor';

            if (strpos($lr, 'name') !== false) $tag = 'name';
            elseif (strpos($lr, 'student number') !== false) $tag = 'number';
            elseif (strpos($lr, 'course') !== false) $tag = 'course';
            elseif (strpos($lr, 'quality') !== false) $tag = 'quality';
            elseif (strpos($lr, 'tamper') !== false) $tag = 'tamper';
            elseif (strpos($lr, 'validation') !== false || strpos($lr, 'confidence') !== false) $tag = 'confidence';

            if ($key && $tag) {
                $docIssues[$key][$tag] = true;
            }
        }

        $parts = [];
        if ($failedDocument === 'student_id') {
            $parts[] = 'Student ID: ' . $this->joinTags(array_keys($docIssues['student_id']));
        } elseif ($failedDocument === 'cor') {
            $parts[] = 'COR: ' . $this->joinTags(array_keys($docIssues['cor']));
        } else {
            if (!empty($docIssues['student_id'])) $parts[] = 'Student ID: ' . $this->joinTags(array_keys($docIssues['student_id']));
            if (!empty($docIssues['cor'])) $parts[] = 'COR: ' . $this->joinTags(array_keys($docIssues['cor']));
        }

        $prefix = 'Verification failed.';
        if ($failedDocument === 'student_id') $prefix = 'Verification failed for Student ID.';
        elseif ($failedDocument === 'cor') $prefix = 'Verification failed for COR.';

        return trim($prefix . ' ' . implode(' ', $parts));
    }

    private function joinTags($tags) {
        $map = [
            'name' => 'name',
            'number' => 'student number',
            'course' => 'course',
            'quality' => 'quality',
            'tamper' => 'tamper',
            'confidence' => 'confidence'
        ];
        $labels = [];
        foreach ($tags as $t) { if (isset($map[$t])) $labels[] = $map[$t]; }
        return empty($labels) ? 'general validation issues' : implode(', ', $labels);
    }
}

?>


