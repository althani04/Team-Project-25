<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// set JSON response header
header('Content-Type: application/json');

// check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in'
    ]);
    exit;
}

try {
    $conn = getConnection();

    // get parameters
    $status = $_GET['status'] ?? null;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;

    // get orders
    $query = "
        SELECT o.*, COUNT(oi.order_item_id) as item_count 
        FROM Orders o 
        LEFT JOIN Order_Items oi ON o.order_id = oi.order_id 
        WHERE o.user_id = ?
    ";

    $params = [$_SESSION['user_id']];

    if ($status) {
        $query .= " AND o.status = ?";
        $params[] = $status;
    }

    $query .= " GROUP BY o.order_id ORDER BY o.order_date DESC";

    if ($limit) {
        $query .= " LIMIT ?";
        $params[] = $limit;
    }

    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // get items for each order
    $formattedOrders = [];
    foreach ($orders as $order) {
        $itemsStmt = $conn->prepare("
            SELECT oi.*, p.name, p.image_url
            FROM Order_Items oi
            JOIN Products p ON oi.product_id = p.product_id
            WHERE oi.order_id = ?
        ");
        $itemsStmt->execute([$order['order_id']]);
        $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

        // format items
        $formattedItems = array_map(function($item) {
            return [
                'id' => $item['order_item_id'],
                'name' => $item['name'],
                'quantity' => $item['quantity'],
                'price' => (float)$item['price'],
                'subtotal' => (float)($item['quantity'] * $item['price']),
                'image_url' => $item['image_url'] && $item['image_url'] !== 'N/A' 
                    ? '/Team-Project-255/assets/images/' . $item['image_url'] 
                    : '/Team-Project-255/assets/images/coffeebeans.jpeg'
            ];
        }, $items);

        // format order
        $formattedOrders[] = [
            'id' => $order['order_id'],
            'date' => $order['order_date'],
            'status' => $order['status'],
            'total' => (float)$order['total_price'],
            'items' => $formattedItems
        ];
    }

    echo json_encode([
        'success' => true,
        'orders' => $formattedOrders
    ]);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching orders'
    ]);
}
