<?php
// Database configuration
$host = 'localhost'; // Database host
$db = 'caf_lab'; // Database name
$user = 'root'; // Database username
$pass = ''; // Database password
$charset = 'utf8mb4'; // Database charset

try {
    // Create a new PDO instance
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false, // Disable emulated prepares
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ];
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Display a generic error message if connection fails
    die('Database connection failed. Please try again later.');
}
?>
