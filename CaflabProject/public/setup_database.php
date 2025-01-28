<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../config/database.php';

try {
    // Read and execute schema.sql
    echo "Executing schema.sql...\n";
    $schema = file_get_contents(__DIR__ . '/../../schema.sql');
    $pdo->exec($schema);
    echo "Schema created successfully.\n";

    // Read and execute seed.sql
    echo "Executing seed.sql...\n";
    $seed = file_get_contents(__DIR__ . '/../../seed.sql');
    $pdo->exec($seed);
    echo "Data seeded successfully.\n";

    // Verify the data
    $categories = $pdo->query("SELECT COUNT(*) as count FROM Category")->fetch(PDO::FETCH_ASSOC);
    $products = $pdo->query("SELECT COUNT(*) as count FROM Products")->fetch(PDO::FETCH_ASSOC);

    echo "\nVerification:\n";
    echo "Categories count: " . $categories['count'] . "\n";
    echo "Products count: " . $products['count'] . "\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
