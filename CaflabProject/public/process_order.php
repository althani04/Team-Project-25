<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

// check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// check if basket is empty
if (empty($_SESSION['basket'])) {
    header('Location: checkout.php');
    exit;
}

try {
    // start transaction
    $pdo->beginTransaction();

    // get form data
    $userId = $_SESSION['user_id'];
    $totalAmount = filter_var($_POST['total_amount'], FILTER_VALIDATE_FLOAT);
    $firstName = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
    $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
    $postcode = filter_var($_POST['postcode'], FILTER_SANITIZE_STRING);
    $notes = filter_var($_POST['notes'] ?? '', FILTER_SANITIZE_STRING);

    // validatation on data (enusuree no field is left empty)
    if (!$totalAmount || !$firstName || !$email || !$phone || !$address || !$postcode) {
        throw new Exception('Please fill in all required fields');
    }

    // update user details if changed
    $stmt = $pdo->prepare("
        UPDATE Users 
        SET name = ?, email = ?, phone_number = ?, address_line = ?, postcode = ?
        WHERE user_id = ?
    ");
    $stmt->execute([$firstName, $email, $phone, $address, $postcode, $userId]);

    // create a new order
    $stmt = $pdo->prepare("
        INSERT INTO Orders (user_id, total_price, status)
        VALUES (?, ?, 'processing')
    ");
    $stmt->execute([$userId, $totalAmount]);
    $orderId = $pdo->lastInsertId();

    // add order items and update stock levels
    $stmt = $pdo->prepare("
        INSERT INTO Order_Items (order_id, product_id, quantity, price)
        VALUES (?, ?, ?, ?)
    ");

    $stockUpdateStmt = $pdo->prepare("
        INSERT INTO Inventory_Logs (product_id, action, quantity)
        VALUES (?, 'remove stock', ?)
    ");

    foreach ($_SESSION['basket'] as $item) {
        // add to order items
        $stmt->execute([
            $orderId,
            $item['product_id'],
            $item['quantity'],
            $item['price']
        ]);

        // log inventory change
        $stockUpdateStmt->execute([
            $item['product_id'],
            $item['quantity']
        ]);
    }

    // clear the users basket
    unset($_SESSION['basket']);

    // commit transaction
    $pdo->commit();

    // set a message for successfull order
    $_SESSION['order_success'] = true;
    $_SESSION['order_id'] = $orderId;

    // display the success message 
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully',
        'orderId' => $orderId
    ]);
    exit;

} catch (Exception $e) {
    // rollback transaction on error
    $pdo->rollBack();

    // set error message
    $_SESSION['order_error'] = $e->getMessage();
    
    // direct user back to checkout page
    header('Location: checkout.php');
    exit;
}
?>
