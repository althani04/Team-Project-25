<?php
 function getConnection() {
     static $pdo = null;
     
     if ($pdo === null) {
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
            // Log detailed error for debugging
            error_log("Database connection error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            error_log("Connection string (DSN): " . $dsn);
            error_log("Username: " . $user);
            // Do NOT log password for security reasons
            throw new PDOException('Database connection failed. Check error logs for details.');
        }
    }
    return $pdo;

}
?>

