<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

// check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please log in to view order details'
    ]);
    exit;
}

// get order ID from query string
$orderId = filter_var($_GET['order_id'], FILTER_VALIDATE_INT);
if (!$orderId) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid order ID'
    ]);
    exit;
}

try {
    // check if order belongs to user
    $stmt = $pdo->prepare("
        SELECT order_id, total_price, status 
        FROM Orders 
        WHERE order_id = ? AND user_id = ?
    ");
    $stmt->execute([$orderId, $_SESSION['user_id']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo json_encode([
            'success' => false,
            'message' => 'Order not found'
        ]);
        exit;
    }

    // get order items
    $stmt = $pdo->prepare("
        SELECT oi.*, p.name, p.image_url
        FROM Order_Items oi
        JOIN Products p ON oi.product_id = p.product_id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$orderId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // format response
    $response = [
        'success' => true,
        'order' => [
            'id' => $order['order_id'],
            'status' => $order['status'],
            'total' => $order['total_price']
        ],
        'items' => array_map(function($item) {
            return [
                'name' => $item['name'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'image_url' => $item['image_url'],
                'subtotal' => $item['quantity'] * $item['price']
            ];
        }, $items),
        'total' => $order['total_price']
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while retrieving order details'
    ]);
}
?>
