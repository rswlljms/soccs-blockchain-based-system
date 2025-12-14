<?php

function cleanupTempUploads() {
    $tempDir = __DIR__ . '/../uploads/temp/';
    
    if (!is_dir($tempDir)) {
        return;
    }
    
    $maxAge = 2 * 3600;
    $now = time();
    $deletedCount = 0;
    
    $files = glob($tempDir . '*');
    
    foreach ($files as $file) {
        if (is_file($file)) {
            $fileAge = $now - filemtime($file);
            
            if ($fileAge > $maxAge) {
                if (unlink($file)) {
                    $deletedCount++;
                    error_log("Deleted temp file: " . basename($file));
                }
            }
        }
    }
    
    if ($deletedCount > 0) {
        error_log("Cleanup: Deleted {$deletedCount} temporary files");
    }
    
    return $deletedCount;
}

if (php_sapi_name() === 'cli') {
    cleanupTempUploads();
    echo "Temporary files cleanup completed.\n";
}
?>
