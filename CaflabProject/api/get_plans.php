<?php
require_once __DIR__ . '/../config/database.php';

try {
    $stmt = $pdo->query("SELECT * FROM caf_lab.subscription_plans");
    $plans = $stmt->fetchAll();
    
    echo json_encode([
        "success" => true,
        "data" => $plans
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "get plans failed: " . $e->getMessage()
    ]);
}
?>
