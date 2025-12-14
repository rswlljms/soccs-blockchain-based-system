<?php
header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'message' => 'API is working',
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => phpversion(),
    'uploads_dir_exists' => is_dir('../uploads/temp/'),
    'uploads_dir_writable' => is_writable('../uploads/temp/') || is_writable('../uploads/')
]);
?>
