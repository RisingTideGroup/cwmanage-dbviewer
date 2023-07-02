<?php
session_start();

if (!isset($_SESSION['hostname']) || !isset($_SESSION['dbname']) || !isset($_SESSION['username']) || !isset($_SESSION['password'])) {
    echo "No database connection details available.";
	header("Location: /index.php");
}

$serverName = $_SESSION['hostname'];
$database = $_SESSION['dbname'];
$username = $_SESSION['username'];
$password = $_SESSION['password'];

try {
    $conn = new PDO("sqlsrv:server=$serverName;Database=$database", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    header("Location: index.php");
}