<?php
include 'functions/auth.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    if (register($username, $password)) {
        $_SESSION['username'] = $username;
        header("Location: index.php");
    } else {
        echo "Error during registration.";
    }
}
?>
<form method="POST">
    Username: <input type="text" name="username">
    Password: <input type="password" name="password">
    <button type="submit">Register</button>
</form>
