<?php
// Include the database configuration file using an absolute path
require_once __DIR__ . '/../config/database.php';

try {
    // Get the database connection
    $pdo = getConnection();

    // Test the database connection
    $stmt = $pdo->query('SELECT "Database connection successful!" AS message');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Display the result
    echo $result['message'];
} catch (PDOException $e) {
    // Display a generic error message if connection fails
    die('Database connection test failed. Please try again later.');
}
?>
