<?php
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_GET['user_id']) ? trim($_GET['user_id']) : null;

    // Modified parameter obtaining method
    $data = json_decode(file_get_contents('php://input'), true);

    $full_name = trim($data['full_name'] ?? '');
    $phone_number = trim($data['phone_number'] ?? '');
    $address_line = trim($data['address_line1'] ?? '') . ' ' . trim($data['address_line2'] ?? '');
    $postcode = trim($data['postcode'] ?? '');

    try {
        $stmt = $pdo->prepare("
            UPDATE Users 
            SET phone_number = :phone_number, address_line = :address_line, postcode = :postcode
            WHERE user_id = :user_id
        ");

        $success = $stmt->execute([
            'user_id' => $user_id,
            'phone_number' => $phone_number,
            'address_line' => $address_line,
            'postcode' => $postcode
        ]);

        echo json_encode([
            'success' => $success,
            'message' => $success ? 'User message updated successfully' : 'Failed to update user message',
            'user_id' => $user_id,
            'full_name' => $full_name,
            'phone_number' => $phone_number,
            'address_line' => $address_line,
            'postcode' => $postcode
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
