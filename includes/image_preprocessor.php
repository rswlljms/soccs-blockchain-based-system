<?php

class ImagePreprocessor {
    
    public static function preprocess($imagePath) {
        // Check if GD library is available
        if (!extension_loaded('gd')) {
            error_log("GD library not enabled - skipping image preprocessing");
            return $imagePath;
        }

        if (!file_exists($imagePath)) {
            error_log("Image not found for preprocessing: {$imagePath}");
            return $imagePath;
        }

        $ext = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
        
        // Skip PDFs - they don't need image preprocessing
        if ($ext === 'pdf') {
            return $imagePath;
        }

        try {
            $image = self::loadImage($imagePath, $ext);
            if (!$image) {
                error_log("Failed to load image for preprocessing: {$imagePath}");
                return $imagePath;
            }

            // Apply preprocessing steps
            $image = self::autoRotate($image);
            $image = self::enhanceContrast($image);
            $image = self::sharpen($image);
            $image = self::denoise($image);

            // Save preprocessed image
            $preprocessedPath = self::savePreprocessed($image, $imagePath);
            imagedestroy($image);

            error_log("Image preprocessed successfully: {$imagePath} -> {$preprocessedPath}");
            return $preprocessedPath;

        } catch (Exception $e) {
            error_log("Preprocessing error for {$imagePath}: " . $e->getMessage());
            return $imagePath;
        }
    }

    private static function loadImage($path, $ext) {
        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                return @imagecreatefromjpeg($path);
            case 'png':
                return @imagecreatefrompng($path);
            case 'gif':
                return @imagecreatefromgif($path);
            case 'bmp':
                return @imagecreatefrombmp($path);
            case 'webp':
                return @imagecreatefromwebp($path);
            default:
                return false;
        }
    }

    private static function autoRotate($image) {
        // Try EXIF auto-rotation first
        if (function_exists('exif_read_data')) {
            $exif = @exif_read_data('data://image/jpeg;base64,' . base64_encode(self::imageToString($image)));
            if ($exif && isset($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:
                        return imagerotate($image, 180, 0);
                    case 6:
                        return imagerotate($image, -90, 0);
                    case 8:
                        return imagerotate($image, 90, 0);
                }
            }
        }
        return $image;
    }

    private static function enhanceContrast($image) {
        // Increase contrast for better OCR
        imagefilter($image, IMG_FILTER_CONTRAST, -20); // Negative = increase contrast
        return $image;
    }

    private static function sharpen($image) {
        // Sharpen edges for clearer text
        $sharpenMatrix = [
            [-1, -1, -1],
            [-1, 16, -1],
            [-1, -1, -1]
        ];
        $divisor = 8;
        $offset = 0;
        imageconvolution($image, $sharpenMatrix, $divisor, $offset);
        return $image;
    }

    private static function denoise($image) {
        // Reduce noise with slight blur
        imagefilter($image, IMG_FILTER_SMOOTH, 2);
        return $image;
    }

    private static function savePreprocessed($image, $originalPath) {
        $dir = dirname($originalPath);
        $filename = pathinfo($originalPath, PATHINFO_FILENAME);
        $preprocessedPath = $dir . '/preprocessed_' . $filename . '.jpg';
        
        imagejpeg($image, $preprocessedPath, 95); // High quality JPEG
        return $preprocessedPath;
    }

    private static function imageToString($image) {
        ob_start();
        imagejpeg($image);
        $data = ob_get_contents();
        ob_end_clean();
        return $data;
    }

    public static function cleanup($preprocessedPath) {
        if (strpos(basename($preprocessedPath), 'preprocessed_') === 0) {
            @unlink($preprocessedPath);
        }
    }
}

?>

