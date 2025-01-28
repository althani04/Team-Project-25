<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json');

try {
    // Initialize response array
    $response = [
        'success' => true,
        'basket' => [],
        'total' => 0,
        'itemCount' => 0
    ];

    // If basket exists in session
    if (isset($_SESSION['basket']) && !empty($_SESSION['basket'])) {
        // Get latest product information for each basket item
        $basketItems = [];
        $total = 0;

        foreach ($_SESSION['basket'] as $item) {
            // Get current product details
            $stmt = $pdo->prepare("
                SELECT product_id, name, price, stock_level, image_url 
                FROM Products 
                WHERE product_id = ?
            ");
            $stmt->execute([$item['product_id']]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                // Check if product is still available
                if ($product['stock_level'] === 'out of stock') {
                    $item['status'] = 'out_of_stock';
                    $item['message'] = 'This item is no longer available';
                } elseif ($product['stock_level'] === 'low stock' && $item['quantity'] > 5) {
                    $item['status'] = 'limited_stock';
                    $item['message'] = 'Limited stock available. Maximum 5 items allowed.';
                    $item['quantity'] = 5; // Adjust quantity to maximum allowed
                } else {
                    $item['status'] = 'available';
                }

                // Update price in case it changed
                $item['price'] = $product['price'];
                $item['image_url'] = $product['image_url'];
                $item['subtotal'] = $product['price'] * $item['quantity'];
                
                $total += $item['subtotal'];
                $basketItems[] = $item;
            }
        }

        // Update session with latest information
        $_SESSION['basket'] = $basketItems;

        $response['basket'] = $basketItems;
        $response['total'] = $total;
        $response['itemCount'] = count($basketItems);
    }

    // Add formatted total for display
    $response['formattedTotal'] = '£' . number_format($response['total'], 2);

    // Return response
    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error retrieving basket: ' . $e->getMessage()
    ]);
}
?>
