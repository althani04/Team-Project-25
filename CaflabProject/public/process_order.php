SQLSTATE[42S22]: Column not found: 1054 Unknown column 'product_name' in 'field list'<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

// prevent any error output
error_reporting(0);
ini_set('display_errors', 0);

// set a JSON content type header at the start
ob_clean(); // Clear any previous output
header('Content-Type: application/json');

// check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please log in to place an order'
    ]);
    exit;
}

// check if basket is empty
if (empty($_SESSION['basket'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Your basket is empty'
    ]);
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

    // Validate delivery address
    if (strlen($address) < 5 || strlen($address) > 200 || preg_match('/[@#%$^&]/', $address)) {
        throw new Exception('Please enter a valid delivery address.');
    }

    // Validate postcode (UK format)
    if (!preg_match('/^[A-Z]{1,2}[0-9][0-9A-Z]?\s?[0-9][A-Z]{2}$/i', $postcode)) {
        throw new Exception('Please enter a valid UK postcode.');
    }

    // Validate phone number (UK format)
    if (!preg_match('/^(?:0|\+?44)(?:\d\s?){9,10}$/', $phone)) {
        throw new Exception('Please enter a valid UK phone number.');
    }

    // Validate required fields are not empty
    if (!$totalAmount || !$firstName || !$email || !$phone || !$address || !$postcode) {
        throw new Exception('Please fill in all required fields.');
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

    // Prepare statement for order items insert
    $stmt = $pdo->prepare("
        INSERT INTO Order_Items (order_id, product_id, quantity, price)
        VALUES (?, ?, ?, ?)
    ");

    // Validate stock and insert order items
    foreach ($_SESSION['basket'] as $item) {
        // fetch current inventory quantity
        $inventoryStmt = $pdo->prepare("
            SELECT i.quantity, p.name as product_name FROM Inventory i INNER JOIN Products p ON i.product_id = p.product_id WHERE i.product_id = ?
        ");
        $inventoryStmt->execute([$item['product_id']]);
        $currentInventory = $inventoryStmt->fetch(PDO::FETCH_ASSOC);

        if ($currentInventory && $currentInventory['quantity'] >= $item['quantity']) {
            // add to order items
            $stmt->execute([
                $orderId,
                $item['product_id'],
                $item['quantity'],
                $item['price']
            ]);
        } else {
            throw new Exception('Insufficient stock for product: ' . htmlspecialchars($currentInventory['product_name']) . '. Only ' . $currentInventory['quantity'] . ' units available.');
        }
    }

    // update stock levels and record transactions after successful order item insertion
    foreach ($_SESSION['basket'] as $item) {
        // update inventory quantity (moved after validation)
        $inventoryStmt = $pdo->prepare("
            SELECT inventory_id, quantity, low_stock_threshold FROM Inventory WHERE product_id = ?
        ");
        $inventoryStmt->execute([$item['product_id']]);
        $inventory = $inventoryStmt->fetch(PDO::FETCH_ASSOC);

        if ($inventory) { // re-check if inventory exists (should always be true at this point)
            $previousQuantity = $inventory['quantity'];
            $newQuantity = max(0, $previousQuantity - $item['quantity']);

            $updateInventoryStmt = $pdo->prepare("
                UPDATE Inventory SET quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE inventory_id = ?
            ");
            $updateInventoryStmt->execute([$newQuantity, $inventory['inventory_id']]);

            // record inventory transaction
            $transactionStmt = $pdo->prepare("
                INSERT INTO Inventory_Transactions (
                    inventory_id, type, quantity, previous_quantity, new_quantity, created_by
                ) VALUES (?, 'sale', ?, ?, ?, ?)
            ");
            $transactionStmt->execute([
                $inventory['inventory_id'],
                -$item['quantity'],
                $previousQuantity,
                $newQuantity,
                $_SESSION['user_id']
            ]);

            // update product stock level
            $stockLevel = $newQuantity <= 0 ? 'out of stock' :
                         ($newQuantity <= 5 && $newQuantity > 0 ? 'low stock' : 'in stock');

            $updateProductStmt = $pdo->prepare("
                UPDATE Products SET stock_level = ? WHERE product_id = ?
            ");
            $updateProductStmt->execute([$stockLevel, $item['product_id']]);
        }
    }

    // clear the users basket
    unset($_SESSION['basket']);

    // commit transaction
    $pdo->commit();

    // set a message for successfull order
    $_SESSION['order_success'] = true;
    $_SESSION['order_id'] = $orderId;

    // display the success message 
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully',
        'orderId' => $orderId
    ]);
    exit;

} catch (Exception $e) {
    // rollback transaction on error
    $pdo->rollBack();

    // return JSON error response
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}
?>
