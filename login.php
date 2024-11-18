<?php
include 'functions/auth.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    if (login($username, $password)) {
        $_SESSION['username'] = $username;
        
        header("Location: index.php");
    } else {
        echo "Invalid username or password.";
    }
}