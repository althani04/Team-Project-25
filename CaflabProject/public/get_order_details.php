<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

// prevent any error output
error_reporting(0);
ini_set('display_errors', 0);

// set JSON content type header
ob_clean(); // Clear any previous output
header('Content-Type: application/json');

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
        SELECT order_id, total_price, status, order_date 
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
        SELECT oi.order_item_id, oi.quantity, oi.price, p.name, p.image_url, p.product_id
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
            'total' => (float)$order['total_price'], // cast to float
            'order_date' => $order['order_date']
        ],
        'items' => array_map(function($item) use ($order) {
            return [
                'order_item_id' => $item['order_item_id'],
                'product_id' => $item['product_id'],
                'name' => $item['name'],
                'quantity' => $item['quantity'],
                'price' => (float)$item['price'], // explicitly cast price to float
                'image_url' => $item['image_url']  && $item['image_url'] !== 'N/A' ? '/Team-Project-255/assets/images/' . $item['image_url'] : '/Team-Project-255/assets/images/coffeebeans.jpeg',
                'subtotal' => (float)($item['quantity'] * $item['price']),
                'returnable' => strtotime($order['order_date']) > strtotime('-30 days')
            ];
        }, $items),
        'total' => (float)$order['total_price']
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
