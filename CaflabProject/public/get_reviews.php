<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// set a JSON response header
header('Content-Type: application/json');

// check to see if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in'
    ]);
    exit;
}

$type = $_GET['type'] ?? 'product';

try {
    $conn = getConnection();

    if ($type === 'product') {
        // get completed orders with their products and any existing reviews
        $stmt = $conn->prepare("
            SELECT DISTINCT 
                p.product_id,
                p.name,
                o.order_id,
                o.order_date,
                r.review_id,
                r.rating,
                r.review_text,
                r.status
            FROM Orders o
            JOIN Order_Items oi ON o.order_id = oi.order_id
            JOIN Products p ON oi.product_id = p.product_id
            LEFT JOIN Reviews r ON r.product_id = p.product_id 
                AND r.user_id = o.user_id 
                AND r.order_id = o.order_id
            WHERE o.user_id = ? 
            AND o.status = 'completed'
            ORDER BY o.order_date DESC
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // format the response
        $formattedProducts = array_map(function($product) {
            return [
                'product_id' => $product['product_id'],
                'name' => $product['name'],
                'order_id' => $product['order_id'],
                'order_date' => $product['order_date'],
                'review' => $product['review_id'] ? [
                    'rating' => $product['rating'],
                    'review_text' => $product['review_text'],
                    'status' => $product['status']
                ] : null
            ];
        }, $products);

        echo json_encode([
            'success' => true,
            'products' => $formattedProducts
        ]);
    } else if ($type === 'service') {
        // get users service review if it exists
        $stmt = $conn->prepare("
            SELECT r.*, o.order_date
            FROM Reviews r
            JOIN Orders o ON r.order_id = o.order_id
            WHERE r.user_id = ? 
            AND r.review_type = 'service'
            ORDER BY r.created_at DESC
            LIMIT 1
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $review = $stmt->fetch(PDO::FETCH_ASSOC);

        // get users completed orders for service review eligibility
        $stmt = $conn->prepare("
            SELECT COUNT(*) as completed_orders
            FROM Orders
            WHERE user_id = ? AND status = 'completed'
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $orderCount = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'can_review' => $orderCount['completed_orders'] > 0,
            'review' => $review ?: null
        ]);
    }

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching reviews'
    ]);
}
