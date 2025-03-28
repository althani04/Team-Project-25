<?php
require_once __DIR__ . '/../../config/database.php';

try {
    $stmt = $pdo->query("
        SELECT *, 
               IFNULL(image_url, 'assets/images/default-plan.jpg') as image_url 
        FROM subscription_plans
    ");
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
