<?php
// database config
$host = 'localhost';
$db = 'caf_lab'; 
$user = 'root'; 
$pass = ''; 
$charset = 'utf8mb4';

try {
    // create a new PDO instance
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false, 
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ];
    $pdo = new PDO($dsn, $user, $pass, $options);

    // error logging
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('error_log', __DIR__ . '/../error.log');

} catch (PDOException $e) {
    error_log("Database connection error: " . $e->getMessage());
    die('Database connection failed. Please try again later.');
}
?>
