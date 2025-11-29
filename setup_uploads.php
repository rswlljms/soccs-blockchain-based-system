<?php
$uploadDirs = [
    'uploads/candidates'
];

foreach ($uploadDirs as $dir) {
    if (!file_exists($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "✓ Created directory: $dir\n";
        } else {
            echo "✗ Failed to create directory: $dir\n";
        }
    } else {
        echo "✓ Directory already exists: $dir\n";
    }
}

if (!file_exists('uploads/.htaccess')) {
    $htaccess = "Options -Indexes\n";
    $htaccess .= "<FilesMatch \"\.(jpg|jpeg|png|gif)$\">\n";
    $htaccess .= "    Order Allow,Deny\n";
    $htaccess .= "    Allow from all\n";
    $htaccess .= "</FilesMatch>\n";
    
    if (file_put_contents('uploads/.htaccess', $htaccess)) {
        echo "✓ Created .htaccess in uploads directory\n";
    } else {
        echo "✗ Failed to create .htaccess\n";
    }
} else {
    echo "✓ .htaccess already exists in uploads directory\n";
}

echo "\n=== Setup Complete ===\n";
echo "Upload directories are ready for use.\n";

