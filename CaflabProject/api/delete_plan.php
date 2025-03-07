<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../config/database.php';

//session_start();
// the delete plan is only for admin
// if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
//     http_response_code(403);
//     die(json_encode(['success' => false, 'message' => 'permission denied']));
// }

// get the plan_id to delete
$plan_id = isset($_GET['plan_id']) ? intval($_GET['plan_id']) : null;

if (!$plan_id) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'invalid plan id']));
}

try {
    $pdo->beginTransaction();

    // check if the plan exists
    $checkStmt = $pdo->prepare("SELECT * FROM subscription_plans WHERE plan_id = ?");
    $checkStmt->execute([$plan_id]);
    if ($checkStmt->rowCount() === 0) {
        http_response_code(404);
        die(json_encode(['success' => false, 'message' => 'plan not found']));
    }

    // delete the associated orders (adjust the database structure as needed)
    $deleteOrdersStmt = $pdo->prepare("DELETE FROM subscriptions WHERE plan_id = ?");
    $deleteOrdersStmt->execute([$plan_id]);

    // delete the plan
    $deleteStmt = $pdo->prepare("DELETE FROM subscription_plans WHERE plan_id = ?");
    $deleteStmt->execute([$plan_id]);

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'plan deleted successfully']);

} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'database error: ' . $e->getMessage()]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'server error: ' . $e->getMessage()]);
}