<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

try {
    // the user must be admin
    // session_start();
    // if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    //     throw new Exception('Insufficient permissions');
    // }

    $planId = $_GET['plan_id'] ?? null;
    $data = json_decode(file_get_contents('php://input'), true);

    $stmt = $pdo->prepare("
        UPDATE subscription_plans 
        SET name = ?, 
            type = ?, 
            description = ?, 
            price = ?
        WHERE plan_id = ?
    ");
    


    $success = $stmt->execute([
        $data['name'],
        $data['type'],
        $data['description'],
        $data['price'],
        $planId
    ]);

    echo json_encode([
        'success' => $success,
        'message' => $success ? 'Plan updated successfully' : 'Update failed'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
