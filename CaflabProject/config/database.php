<?php
$host = 'localhost';       
$db = '';    
$user = '';      
$pass = ''; 
$charset = 'utf8mb4';    

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
    // print message if database connection successful/failed
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection successful!";
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
