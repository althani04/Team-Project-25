<?php

// database config
include_once __DIR__ . '/config.php';

try {
    // generating a random key
    $bytes = openssl_random_pseudo_bytes(32);
    $adminKey = bin2hex($bytes);
    $expiryTime = date('Y-m-d H:i:s', strtotime('+5 minutes'));

    // put the new key into the database with a expiry 
    $query = "INSERT INTO admin_keys (key_value, expiry_timestamp) VALUES (:key_value, :expiry_timestamp)";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['key_value' => $adminKey, 'expiry_timestamp' => $expiryTime]);

    echo "<h1>New Admin Key Generated</h1>";
    echo "<p>This admin key is valid for 5 minutes only.</p>";
    echo "<p>Please provide this key to new administrators for signup immediately:</p>";
    echo "<p><strong>" . $adminKey . "</strong></p>";
    echo "<p>Key generated on: " . date('Y-m-d H:i:s') . "</p>";
    echo "<p>Expiry time: " . $expiryTime . "</p>";

} catch (PDOException $e) {
    http_response_code(500);
    echo "<h1>Error generating admin key</h1>";
    echo "<p>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
    // log the error for debugging
    error_log("Admin Key Generation Error: " . $e->getMessage() . " on " . date('Y-m-d H:i:s'));
}

?>
