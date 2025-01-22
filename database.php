<?php
session_start();

if (!isset($_SESSION['hostname']) || !isset($_SESSION['dbname']) || !isset($_SESSION['username']) || !isset($_SESSION['password'])) {
    $dbErrorMessage = "No database connection details available.";
	header("Location: /index.php?message=" . urlencode($dbErrorMessage));
	exit;
}

$serverName = $_SESSION['hostname'];
$database = $_SESSION['dbname'];
$username = $_SESSION['username'];
$password = $_SESSION['password'];
$ignoreTrust = $_SESSION['ignore_trust'] === 'on' ?? false;


// Ensure `$ignoreTrust` is a proper boolean
$isIgnoreTrust = filter_var($ignoreTrust, FILTER_VALIDATE_BOOLEAN);

$isDbConnected = false;
$dbErrorMessage = null; // Initialize error message

if ($serverName && $database && $username && $password) {
    try {
	$encodedPassword = addcslashes($password, '{}');

	$options = [
    	  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    	  PDO::SQLSRV_ATTR_ENCRYPT => true,
    	  PDO::SQLSRV_ATTR_TRUST_SERVER_CERTIFICATE => $isIgnoreTrust
	];
        $conn = new PDO("sqlsrv:server=$serverName;Database=$database;", $username, $encodedPassword, $options);
		
	$stmt = $conn->query("Select Message FROM System_Table where Description = 'display_version'");
        $dbVersion = $stmt->fetchColumn();
        $isDbConnected = true;
    } catch(PDOException $e) {
        // Connection failed, show form
        $dbErrorMessage = $e->getMessage();
        header("Location: /index.php?message=" . urlencode($dbErrorMessage));
	exit;
}
