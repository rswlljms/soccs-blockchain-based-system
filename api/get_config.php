<?php
require_once __DIR__ . '/../includes/app_config.php';
header('Content-Type: application/json');

$config = [
    'blockchainUrl' => AppConfig::get('BLOCKCHAIN_URL', 'http://localhost:3001')
];

echo json_encode($config);
?>

