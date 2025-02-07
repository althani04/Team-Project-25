<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

// check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please log in to submit a return'
    ]);
    exit;
}

// Get POST data
$orderId = filter_var($_POST['order_id'], FILTER_VALIDATE_INT);
$reason = filter_var($_POST['reason'], FILTER_SANITIZE_STRING);
$comments = filter_var($_POST['comments'], FILTER_SANITIZE_STRING);

// validate input (inputs must not be empty)
if (!$orderId || empty($reason)) {
    echo json_encode([
        'success' => false,
        'message' => 'Order ID and reason are required'
    ]);
    exit;
}

try {
    // start the transaction
    $pdo->beginTransaction();

    // check if order exists and belongs to user
    $stmt = $pdo->prepare("
        SELECT o.*, DATEDIFF(CURRENT_DATE, o.order_date) as days_since_order
        FROM Orders o
        WHERE o.order_id = ? AND o.user_id = ?
    ");
    $stmt->execute([$orderId, $_SESSION['user_id']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    // validate the order
    if (!$order) {
        throw new Exception('Order not found');
    }

    // check if order is within the return window (30 days?? *change if needed)
    if ($order['days_since_order'] > 30) {
        throw new Exception('Order is outside the 30-day return window');
    }

    // check if return already exists for this order
    $stmt = $pdo->prepare("
        SELECT return_id FROM Returns 
        WHERE order_id = ?
    ");
    $stmt->execute([$orderId]);
    if ($stmt->fetch()) {
        throw new Exception('A return request already exists for this order');
    }

    // create a return request
    $stmt = $pdo->prepare("
        INSERT INTO Returns (
            order_id, 
            user_id,
            reason,
            comments,
            status,
            created_at
        ) VALUES (?, ?, ?, ?, 'pending', CURRENT_TIMESTAMP)
    ");
    $stmt->execute([
        $orderId,
        $_SESSION['user_id'],
        $reason,
        $comments
    ]);

    // update the order status
    $stmt = $pdo->prepare("
        UPDATE Orders 
        SET status = 'return_pending'
        WHERE order_id = ?
    ");
    $stmt->execute([$orderId]);

    // commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Return request submitted successfully'
    ]);

} catch (Exception $e) {
    // rollback transaction on error
    $pdo->rollBack();
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
