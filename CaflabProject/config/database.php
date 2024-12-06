<?php
// Database connection configuration
$host = 'localhost';        // Host (usually localhost)
$db = 'caf_lab';       // Your database name
$user = 'root';    // Your MySQL username
$pass = '';    // Your MySQL password
$charset = 'utf8mb4';       // Charset

// DSN (Data Source Name) - specifies database details
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    // Create a PDO instance to connect to the database
    $pdo = new PDO($dsn, $user, $pass);
    // Set PDO to throw exceptions for any database errors
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection successful!";
} catch (PDOException $e) {
    // Handle connection failure
    die("Database connection failed: " . $e->getMessage());
}
?>
