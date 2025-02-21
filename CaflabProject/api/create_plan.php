<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../config/database.php';

// // check if the user is admin
// if (!isAdmin()) {
//     http_response_code(403);
//     echo json_encode(['success' => false, 'message' => 'Permission denied']);
//     exit;
// }

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // check the required fields
    $requiredFields = ['name', 'type', 'description', 'price'];
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
            exit;
        }
    }

    $stmt = $pdo->prepare("
        INSERT INTO subscription_plans 
        (name, type, description, price)
        VALUES (?, ?, ?, ?)
    ");
    
    $success = $stmt->execute([
        $data['name'],
        $data['type'],
        $data['description'],
        $data['price']
    ]);

    echo json_encode([
        'success' => $success,
        'message' => $success ? 'Plan created successfully' : 'Failed to create plan',
        'plan_id' => $success ? $pdo->lastInsertId() : null,
        'name' => $data['name'],
        'type' => $data['type'],
        'description' => $data['description'],
        'price' => $data['price']
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>