<?php
require_once __DIR__ . '/../../config/database.php';

try {

    $user_id = $_GET['user_id'];

    $stmt = $pdo->query("
        SELECT *
        FROM subscriptions
        WHERE user_id = $user_id
    ");
    $subs = $stmt->fetchAll();
    
    echo json_encode([
        "success" => true,
        "subs" => $subs
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "get subscriptions failed: " . $e->getMessage()
    ]);
}
?>
