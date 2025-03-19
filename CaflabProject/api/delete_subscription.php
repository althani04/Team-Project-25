<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../../config/database.php';

// get the plan_id to delete
$sub_id = $_GET['sub_id'];

if (!$sub_id) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'invalid sub id']));
}

try {
    $pdo->beginTransaction();

    // check if the plan exists
    $checkStmt = $pdo->prepare("SELECT * FROM subscriptions WHERE subscription_id = ?");
    $checkStmt->execute([$sub_id]);
    if ($checkStmt->rowCount() === 0) {
        http_response_code(404);
        die(json_encode(['success' => false, 'message' => 'sub not found']));
    }

    // delete the associated orders (adjust the database structure as needed)
    $deleteOrdersStmt = $pdo->prepare("DELETE FROM subscriptions WHERE subscription_id = ?");
    $deleteOrdersStmt->execute([$sub_id]);

    // delete the plan
    $deleteStmt = $pdo->prepare("DELETE FROM subscriptions WHERE subscription_id = ?");
    $deleteStmt->execute([$sub_id]);

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'subscription deleted successfully']);

} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'database error: ' . $e->getMessage()]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'server error: ' . $e->getMessage()]);
}