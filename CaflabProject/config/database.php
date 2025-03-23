<?php
// Database connection
$host = '127.0.0.1';  // Database host
$db = 'caf_lab';  // Database name
$user = 'root';  // Database username
$pass = '';  // Database password (if any)

// Set DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db;charset=utf8";

// Set PDO options
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

// Create PDO instance
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // If connection fails, display an error
    echo "Database connection failed: " . $e->getMessage();
    exit;
}
?>

