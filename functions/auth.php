<?php
// auth.php
session_start();
include 'db.php';

// Fungsi hash kata sandi
function hashPassword($password) {
return password_hash($password, PASSWORD_BCRYPT);
}

// Fungsi pendaftaran
function register($username, $password) {
    global $conn;
    $hashedPassword = hashPassword($password);
    $sql = "INSERT INTO users (username, password) VALUES ('$username', '$hashedPassword')";
    return $conn->query($sql);
}

function login($username, $password) {
    global $conn;
    // Escape input untuk mencegah SQL Injection
    $username = mysqli_real_escape_string($conn, $username);
    // Query untuk mengambil data pengguna
    $sql = "SELECT id, password FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Ambil data pengguna
        $row = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($password, $row['password'])) {
            // Simpan ID pengguna ke sesi
            $_SESSION['id'] = $row['id'];
            return true; // Login berhasil
        }
    }

    return false; // Login gagal
}

?>
