<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database configuration file
include_once __DIR__ . '/../config/database.php';

// Test the connection
if (isset($pdo)) {
    echo "Test successful: Database connection is working!";
} else {
    echo "Test failed: Could not connect to the database.";
}
?>
