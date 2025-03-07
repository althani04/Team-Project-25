<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

try {
    $userId = $_GET['user_id'] ?? null;
    if (!$userId) {
        throw new Exception('missing user id');
    }

    $stmt = $pdo->prepare("SELECT role FROM Users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('user not found');
    }

    echo json_encode([
        'success' => true,
        'role' => $user['role']
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
