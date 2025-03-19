<?php
require_once 'config.php';
checkAdminAuth();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

// get POST data
$inventoryId = $_POST['inventory_id'] ?? null;
$type = $_POST['type'] ?? null;
$quantity = (int)($_POST['quantity'] ?? 0);
$notes = $_POST['notes'] ?? '';

// validate input
if (!$inventoryId || !$type || ($quantity < 0) || ($type !== 'adjustment' && $quantity <= 0)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid input parameters'
    ]);
    exit;
}

// validate transaction type
$validTypes = ['restock', 'adjustment', 'return'];
if (!in_array($type, $validTypes)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid transaction type'
    ]);
    exit;
}

try {
    $conn = getConnection();
    $conn->beginTransaction();

    // get current inventory details
    $stmt = $conn->prepare("
        SELECT i.*, p.name as product_name 
        FROM Inventory i 
        JOIN Products p ON i.product_id = p.product_id 
        WHERE i.inventory_id = ?
    ");
    $stmt->execute([$inventoryId]);
    $inventory = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$inventory) {
        throw new Exception('Inventory item not found');
    }

    $previousQuantity = $inventory['quantity'];
    $newQuantity = $previousQuantity;

    // ca,lculate new quantity based on transaction type
    switch ($type) {
        case 'restock':
        case 'return':
            $newQuantity += $quantity;
            break;
        case 'adjustment':
            $newQuantity = $quantity; // direct set to new quantity
            break;
    }

    // prevent negative stock
    if ($newQuantity < 0) {
        throw new Exception('Operation would result in negative stock');
    }

    // update inventory quantity and restock date if applicable
    $updateQuery = "
        UPDATE Inventory 
        SET quantity = ?, 
            updated_at = CURRENT_TIMESTAMP
    ";
    $updateParams = [$newQuantity];

    if ($type === 'restock') {
        $updateQuery .= ", 
            last_restock_date = CURRENT_TIMESTAMP,
            last_restock_quantity = ?
        ";
        $updateParams[] = $quantity;
    }

    $updateQuery .= " WHERE inventory_id = ?";
    $updateParams[] = $inventoryId;

    $stmt = $conn->prepare($updateQuery);
    $stmt->execute($updateParams);

    // record the transaction
    $stmt = $conn->prepare("
        INSERT INTO Inventory_Transactions (
            inventory_id, type, quantity, previous_quantity, 
            new_quantity, notes, created_by
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $inventoryId,
        $type,
        $type === 'adjustment' ? ($newQuantity - $previousQuantity) : $quantity,
        $previousQuantity,
        $newQuantity,
        $notes,
        $_SESSION['user_id']
    ]);

    // update product stock level
    $stockLevel = $newQuantity <= 0 ? 'out of stock' :
                 ($newQuantity <= 5 && $newQuantity > 0 ? 'low stock' : 'in stock');

    $stmt = $conn->prepare("
        UPDATE Products 
        SET stock_level = ?
        WHERE product_id = ?
    ");
    $stmt->execute([$stockLevel, $inventory['product_id']]);

    $conn->commit();

    // prepare success message
    $action = $type === 'restock' ? 'restocked' : 
             ($type === 'return' ? 'returned' : 'adjusted');
    
    $message = sprintf(
        'Successfully %s inventory for %s. New quantity: %d',
        $action,
        $inventory['product_name'],
        $newQuantity
    );

    echo json_encode([
        'success' => true,
        'message' => $message,
        'newQuantity' => $newQuantity,
        'stockLevel' => $stockLevel
    ]);

} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollBack();
    }
    
    error_log("Inventory update error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
