<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['hostname'] = $_POST['hostname'];
    $_SESSION['dbname'] = $_POST['dbname'];
    $_SESSION['username'] = $_POST['username'];
    $_SESSION['password'] = $_POST['password'];
}

// Redirect back to the main page
header("Location: /index.php");
exit;
