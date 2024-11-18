<?php

// Fungsi Caesar Cipher untuk mengenkripsi data
function caesarEncrypt($data, $key) {
    $encrypted = '';
    $shift = $key % 26; // Pergeseran hanya dalam rentang alfabet (A-Z, a-z)
    foreach (str_split($data) as $char) {
        if (ctype_alpha($char)) { // Hanya proses alfabet
            $offset = ctype_upper($char) ? 65 : 97; // 65 untuk A-Z, 97 untuk a-z
            $encrypted .= chr(($offset + (ord($char) - $offset + $shift) % 26));
        } else {
            $encrypted .= $char; // Karakter non-alfabet tetap sama
        }
    }
    return $encrypted;
}

// Fungsi Caesar Cipher untuk mendekripsi data
function caesarDecrypt($data, $key) {
    $decrypted = '';
    $shift = $key % 26; // Pergeseran hanya dalam rentang alfabet (A-Z, a-z)
    foreach (str_split($data) as $char) {
        if (ctype_alpha($char)) { // Hanya proses alfabet
            $offset = ctype_upper($char) ? 65 : 97; // 65 untuk A-Z, 97 untuk a-z
            $decrypted .= chr(($offset + (ord($char) - $offset - $shift + 26) % 26));
        } else {
            $decrypted .= $char; // Karakter non-alfabet tetap sama
        }
    }
    return $decrypted;
}

// Fungsi untuk mengenkripsi file
function encryptFile($filePath, $key, $outputFileName) {
    $data = file_get_contents($filePath);
    $encryptedData = caesarEncrypt($data, $key); // Enkripsi menggunakan Caesar Cipher
    file_put_contents($outputFileName, $encryptedData);
}

// Fungsi untuk mendekripsi file
function decryptFile($inputFilePath, $key, $outputFilePath) {
    $fileContents = file_get_contents($inputFilePath);
    $decryptedData = caesarDecrypt($fileContents, $key); // Dekripsi menggunakan Caesar Cipher
    file_put_contents($outputFilePath, $decryptedData);
}
?>
