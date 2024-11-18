<?php
// steganography.php

function createImageFromFile($imagePath) {
    $imageInfo = getimagesize($imagePath);
    $mimeType = $imageInfo['mime'];

    switch ($mimeType) {
        case 'image/jpeg':
            return imagecreatefromjpeg($imagePath);
        case 'image/png':
            return imagecreatefrompng($imagePath);
        case 'image/gif':
            return imagecreatefromgif($imagePath);
        default:
            throw new Exception("Unsupported image format");
    }
}

function saveImageToFile($image, $outputPath, $mimeType) {
    switch ($mimeType) {
        case 'image/jpeg':
            imagejpeg($image, $outputPath);
            break;
        case 'image/png':
            imagepng($image, $outputPath);
            break;
        case 'image/gif':
            imagegif($image, $outputPath);
            break;
        default:
            throw new Exception("Unsupported image format");
    }
}

function hideMessage($imagePath, $message, $outputFilename) {
    $img = createImageFromFile($imagePath);
    $imageInfo = getimagesize($imagePath);
    $mimeType = $imageInfo['mime'];
    $binaryMessage = '';

    // Convert message to binary
    for ($i = 0; $i < strlen($message); $i++) {
        $binaryMessage .= str_pad(decbin(ord($message[$i])), 8, '0', STR_PAD_LEFT);
    }
    $binaryMessage .= str_pad(decbin(0), 8, '0', STR_PAD_LEFT);

    $x = 0; $y = 0;
    for ($i = 0; $i < strlen($binaryMessage); $i++) {
        $rgb = imagecolorat($img, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;

        $newB = ($b & 0xFE) | $binaryMessage[$i];
        $color = imagecolorallocate($img, $r, $g, $newB);
        imagesetpixel($img, $x, $y, $color);

        $x++;
        if ($x >= imagesx($img)) {
            $x = 0;
            $y++;
        }
    }

    // Debug uploads folder path
    $uploadsDir = __DIR__ . '../uploads/';
    echo "Uploads directory: $uploadsDir\n";

    if (!is_dir($uploadsDir)) {
        if (!mkdir($uploadsDir, 0755, true)) {
            echo "Failed to create uploads directory.\n";
            return;
        }
    }

    $outputPath = $uploadsDir . $outputFilename;

    try {
        saveImageToFile($img, $outputPath, $mimeType);
        echo "File has been saved to: $outputPath\n";
    } catch (Exception $e) {
        echo "Error saving file: " . $e->getMessage() . "\n";
    }

    imagedestroy($img);
}

function retrieveMessage($imagePath) {
    $img = createImageFromFile($imagePath);
    $binaryMessage = '';
    $x = 0; $y = 0;
    while (true) {
        $rgb = imagecolorat($img, $x, $y);
        $b = $rgb & 0xFF;

        $binaryMessage .= ($b & 1) ? '1' : '0';

        // Periksa apakah kita menemukan akhir watermark (terminator "00000000")
        if (strlen($binaryMessage) % 8 == 0) {
            $char = chr(bindec(substr($binaryMessage, -8)));
            if ($char === "\0") {
                break;
            }
        }

        $x++;
        if ($x >= imagesx($img)) {
            $x = 0;
            $y++;
            if ($y >= imagesy($img)) break;  // Prevent reading beyond image bounds
        }
    }

    // Konversi biner ke teks
    $text = '';
    for ($i = 0; $i < strlen($binaryMessage) - 8; $i += 8) {
        $text .= chr(bindec(substr($binaryMessage, $i, 8)));
    }

    imagedestroy($img);

    // Validasi watermark
    $expectedWatermark = $_SESSION['username']; // Watermark yang diharapkan
    if (strpos($text, $expectedWatermark) === 0) {
        return $expectedWatermark;
    }

    return null; // Tidak ada watermark valid
}
?>
