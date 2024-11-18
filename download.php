<?php
include '../tugasakhirkripto/functions/db.php';
include 'functions/file_encryption.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fileId = $_POST['file_id'];
    $key = $_POST['key'];

    // Ambil file dari database berdasarkan ID
    $stmt = $conn->prepare("SELECT file, key_file, tipe_file, nama_file FROM catatan WHERE id_catatan = ?");
    $stmt->bind_param("i", $fileId);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($fileData, $keyFile, $mimeType, $fileName);
    $stmt->fetch();

    // Periksa apakah kunci cocok
    if ($key === $keyFile) {
        // Simpan file terenkripsi ke sementara
        $encryptedFilePath = 'temp_encrypted_' . $fileName;
        file_put_contents($encryptedFilePath, stripslashes($fileData));

        // Dekripsi file
        $decryptedFilePath = 'decrypted_' . $fileName;
        decryptFile($encryptedFilePath, $key, $decryptedFilePath);

        // Kirim file yang didekripsi ke pengguna
        header("Content-Type: $mimeType");
        header("Content-Disposition: attachment; filename=$fileName");
        readfile($decryptedFilePath);

        // Hapus file sementara
        unlink($encryptedFilePath);
        unlink($decryptedFilePath);

        exit;
    } else {
        header('Location:encrypt_file.php?pesan=salah');
    }
}
?>
