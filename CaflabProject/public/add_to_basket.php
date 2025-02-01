<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json');

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check product stock
function checkProductStock($pdo, $productId) {
    $stmt = $pdo->prepare("SELECT stock_level FROM Products WHERE product_id = ?");
    $stmt->execute([$productId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['stock_level'] : null;
}

// Function to get product details
function getProductDetails($pdo, $productId) {
    $stmt = $pdo->prepare("
        SELECT product_id, name, price, stock_level, image_url 
        FROM Products 
        WHERE product_id = ?
    ");
    $stmt->execute([$productId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

try {
    // Get the raw POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate input
    if (!isset($data['product_id']) || !isset($data['quantity'])) {
        throw new Exception('Missing required fields');
    }

    $productId = filter_var($data['product_id'], FILTER_VALIDATE_INT);
    $quantity = filter_var($data['quantity'], FILTER_VALIDATE_INT);
    $action = isset($data['action']) ? $data['action'] : 'add';

    if ($productId === false) {
        throw new Exception('Invalid product ID');
    }
    
    // For remove action, allow quantity to be 0
    if ($action !== 'remove' && ($quantity === false || $quantity < 1)) {
        throw new Exception('Invalid quantity');
    }

    // Initialize basket if not exists
    if (!isset($_SESSION['basket'])) {
        $_SESSION['basket'] = [];
    }

    // Check product stock
    $stockLevel = checkProductStock($pdo, $productId);
    $currentQuantity = 0;
    
    // Find current quantity in basket
    if (isset($_SESSION['basket'])) {
        foreach ($_SESSION['basket'] as $item) {
            if ($item['product_id'] === $productId) {
                $currentQuantity = $item['quantity'];
                break;
            }
        }
    }

    if (!$stockLevel || $stockLevel === 'out of stock') {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Product is out of stock'
        ]);
        exit;
    }

    $requestedTotal = ($action === 'add') ? $currentQuantity + $quantity : $quantity;
    
    if ($stockLevel === 'low stock' && $requestedTotal > 5) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Limited stock available. Maximum 5 items allowed.'
        ]);
        exit;
    }

    // Get product details
    $product = getProductDetails($pdo, $productId);
    if (!$product) {
        throw new Exception('Product not found');
    }

    // Initialize basket if not exists
    if (!isset($_SESSION['basket'])) {
        $_SESSION['basket'] = [];
    }

    // Handle different actions
    switch ($action) {
        case 'add':
            // Check if product already exists in basket and merge quantities
            $found = false;
            foreach ($_SESSION['basket'] as &$item) {
                if ($item['product_id'] === $productId) {
                    $newQuantity = $item['quantity'] + $quantity;
                    
                    // Check stock limits
                    if ($stockLevel === 'low stock' && $newQuantity > 5) {
                        throw new Exception('Limited stock available. Maximum 5 items allowed.');
                    }
                    
                    $item['quantity'] = $newQuantity;
                    $item['subtotal'] = $item['price'] * $newQuantity;
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $subtotal = $product['price'] * $quantity;
                $_SESSION['basket'][] = [
                    'product_id' => $productId,
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $quantity,
                    'image_url' => $product['image_url'],
                    'subtotal' => $subtotal
                ];
            }
            break;

        case 'update':
            foreach ($_SESSION['basket'] as &$item) {
                if ($item['product_id'] === $productId) {
                    if ($quantity < 1) {
                        throw new Exception('Quantity must be at least 1');
                    }
                    if ($stockLevel === 'low stock' && $quantity > 5) {
                        throw new Exception('Limited stock available. Maximum 5 items allowed.');
                    }
                    $item['quantity'] = $quantity;
                    $item['subtotal'] = $item['price'] * $quantity;
                    break;
                }
            }
            break;

        case 'remove':
            foreach ($_SESSION['basket'] as $key => $item) {
                if ($item['product_id'] === $productId) {
                    unset($_SESSION['basket'][$key]);
                    break;
                }
            }
            // Reindex array after removal
            $_SESSION['basket'] = array_values($_SESSION['basket']);
            break;

        default:
            throw new Exception('Invalid action');
    }

    // Calculate total and update subtotals
    $total = 0;
    foreach ($_SESSION['basket'] as &$item) {
        $item['subtotal'] = $item['price'] * $item['quantity'];
        $total += $item['subtotal'];
    }

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Basket updated successfully',
        'basket' => array_values($_SESSION['basket']),
        'total' => $total,
        'itemCount' => empty($_SESSION['basket']) ? 0 : count($_SESSION['basket'])
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
