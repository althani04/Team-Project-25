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
        'message' => 'Please log in to submit a return'
    ]);
    exit;
}

// get POST data
$orderId = filter_var($_POST['order_id'], FILTER_VALIDATE_INT);
$reason = filter_var($_POST['reason'], FILTER_SANITIZE_STRING);
$comments = filter_var($_POST['comments'], FILTER_SANITIZE_STRING);
$returnItems = json_decode($_POST['return_items'], true);

// validate input (inputs must not be empty)
if (!$orderId || empty($reason)) {
    echo json_encode([
        'success' => false,
        'message' => 'Order ID, reason and items are required'
    ]);
    exit;
}

try {
    // start the transaction
    $pdo->beginTransaction();

    // check if order exists and belongs to user
    $stmt = $pdo->prepare("
        SELECT o.*, u.email, u.name as customer_name, DATEDIFF(CURRENT_DATE, o.order_date) as days_since_order
        FROM Orders o
        JOIN Users u ON o.user_id = u.user_id
        WHERE o.order_id = ? AND o.user_id = ?
    ");
    $stmt->execute([$orderId, $_SESSION['user_id']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    // validate the order
    if (!$order) {
        throw new Exception('Order not found');
    }

    // check if order is within the return window
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
    $returnId = $pdo->lastInsertId();

    // insert return items
    $stmt = $pdo->prepare("
        INSERT INTO Return_Items (
            return_id,
            order_item_id,
            quantity
        ) VALUES (?, ?, ?)
    ");
    foreach ($returnItems as $orderItemId => $quantity) {
        $stmt->execute([$returnId, $orderItemId, $quantity]);
    }

    // create message in Contact_Messages for admin
    $messageSubject = "Return Request - Order #" . $orderId;
    $messageBody = "Return Request Details:\n\n";
    $messageBody .= "Customer: " . $order['customer_name'] . "\n";
    $messageBody .= "Email: " . $order['email'] . "\n";
    $messageBody .= "Order #: " . $orderId . "\n";
    $messageBody .= "Reason: " . $reason . "\n";
    if (!empty($comments)) {
        $messageBody .= "Comments: " . $comments . "\n";
    }
    $messageBody .= "\nItems to Return:\n";

    // get item details
    $stmt = $pdo->prepare("
        SELECT oi.order_item_id, p.name, oi.quantity as ordered_quantity
        FROM Order_Items oi
        JOIN Products p ON oi.product_id = p.product_id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$orderId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($items as $item) {
        if (isset($returnItems[$item['order_item_id']])) {
            $returnQty = $returnItems[$item['order_item_id']];
            $messageBody .= "- " . $item['name'] . " (Qty: " . $returnQty . " of " . $item['ordered_quantity'] . ")\n";
        }
    }

    // $stmt = $pdo->prepare("
    //     INSERT INTO Contact_Messages (
    //         name,
    //         email,
    //         subject,
    //         message,
    //         created_at
    //     ) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)
    // ");
    // $stmt->execute([
    //     $order['customer_name'],
    //     $order['email'],
    //     $messageSubject,
    //     $messageBody
    // ]);

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
