<?php
// Enkripsi Vigenere
function vigenereEncrypt($text, $key) {
    $result = "";
    $key = strtolower($key); // Pastikan kunci dalam huruf kecil
    $keyLength = strlen($key);
    $keyIndex = 0;

    for ($i = 0; $i < strlen($text); $i++) {
        $char = $text[$i];
        if (ctype_alpha($char)) { // Memastikan hanya huruf yang diproses
            $ascii = ord($char);
            $offset = ctype_upper($char) ? 65 : 97; // Offset untuk huruf besar atau kecil
            $shift = ord($key[$keyIndex % $keyLength]) - 97; // Pergeseran berdasarkan kunci
            $result .= chr(($ascii + $shift - $offset) % 26 + $offset);
            $keyIndex++;
        } else {
            $result .= $char; // Karakter non-huruf tidak berubah
        }
    }
    return $result;
}

// Dekripsi Vigenere
function vigenereDecrypt($text, $key) {
    $result = "";
    $key = strtolower($key); // Pastikan kunci dalam huruf kecil
    $keyLength = strlen($key);
    $keyIndex = 0;

    for ($i = 0; $i < strlen($text); $i++) {
        $char = $text[$i];
        if (ctype_alpha($char)) { // Memastikan hanya huruf yang diproses
            $ascii = ord($char);
            $offset = ctype_upper($char) ? 65 : 97; // Offset untuk huruf besar atau kecil
            $shift = ord($key[$keyIndex % $keyLength]) - 97; // Pergeseran berdasarkan kunci
            $result .= chr(($ascii - $shift - $offset + 26) % 26 + $offset);
            $keyIndex++;
        } else {
            $result .= $char; // Karakter non-huruf tidak berubah
        }
    }
    return $result;
}

// Enkripsi AES menggunakan OpenSSL
function aesEncrypt($text, $key) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($text, 'aes-128-cbc', $key, 0, $iv);
    return base64_encode($encrypted . '::' . $iv);
}

// Dekripsi AES menggunakan OpenSSL
function aesDecrypt($encryptedText, $key) {
    $data = base64_decode($encryptedText);
    
    // Periksa apakah formatnya benar
    if (strpos($data, '::') === false) {
        return "Error: Invalid encrypted format.";
    }

    list($encrypted_data, $iv) = explode('::', $data, 2);
    return openssl_decrypt($encrypted_data, 'aes-128-cbc', $key, 0, $iv);
}

function superEncrypt($text, $Key) {
    $vigenereText = vigenereEncrypt($text, $Key);
    return aesEncrypt($vigenereText, $Key);
}
function superDecrypt($encryptedText, $Key) {
    $vigenereText = aesDecrypt($encryptedText, $Key);
    return vigenereDecrypt($vigenereText, $Key);
}
?>
