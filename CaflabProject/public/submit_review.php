<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// set a JSON response header
header('Content-Type: application/json');

// check if user is logged in or not
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in'
    ]);
    exit;
}

// get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['review_type']) || !isset($data['review_text'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields'
    ]);
    exit;
}

try {
    $conn = getConnection();
    $conn->beginTransaction();

    if ($data['review_type'] === 'product') {
        // validate product review data
        if (!isset($data['product_id']) || !isset($data['order_id']) || !isset($data['rating'])) {
            throw new Exception('Missing required fields for product review');
        }

        // chcek if order belongs to user and if its completed
        $stmt = $conn->prepare("
            SELECT order_id 
            FROM Orders 
            WHERE order_id = ? AND user_id = ? AND status = 'completed'
        ");
        $stmt->execute([$data['order_id'], $_SESSION['user_id']]);
        if (!$stmt->fetch()) {
            throw new Exception('Invalid order or order not completed');
        }

        // check if the review already exists
        $stmt = $conn->prepare("
            SELECT review_id 
            FROM Reviews 
            WHERE user_id = ? AND product_id = ? AND order_id = ?
        ");
        $stmt->execute([$_SESSION['user_id'], $data['product_id'], $data['order_id']]);
        if ($stmt->fetch()) {
            throw new Exception('Review already exists for this product');
        }

        // insert product review
        $stmt = $conn->prepare("
            INSERT INTO Reviews (
                user_id,
                product_id,
                order_id,
                review_type,
                rating,
                review_text,
                status,
                created_at
            ) VALUES (?, ?, ?, 'product', ?, ?, 'pending', CURRENT_TIMESTAMP)
        ");
        $stmt->execute([
            $_SESSION['user_id'],
            $data['product_id'],
            $data['order_id'],
            $data['rating'],
            $data['review_text']
        ]);

    } else if ($data['review_type'] === 'service') {
        // validate service review data
        if (!isset($data['usability']) || !isset($data['delivery']) || !isset($data['support']) || !isset($data['overall_service'])) {
            throw new Exception('Missing required fields for service review');
        }

        $websiteUsabilityRating = intval($data['usability']);
        $deliveryServiceRating = intval($data['delivery']);
        $customerSupportRating = intval($data['support']);
        $overallWebsiteServiceRating = intval($data['overall_service']);


        if ($websiteUsabilityRating < 1 || $websiteUsabilityRating > 5 ||
            $deliveryServiceRating < 1 || $deliveryServiceRating > 5 ||
            $customerSupportRating < 1 || $customerSupportRating > 5 ||
            $overallWebsiteServiceRating < 1 || $overallWebsiteServiceRating > 5) {
            throw new Exception('Invalid rating value');
        }


        // get users most recent completed order
        $stmt = $conn->prepare("
            SELECT order_id 
            FROM Orders 
            WHERE user_id = ? AND status = 'completed'
            ORDER BY order_date DESC 
            LIMIT 1
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$order) {
            throw new Exception('No completed orders found');
        }

        // check if service review already exists
        $stmt = $conn->prepare("
            SELECT review_id 
            FROM Reviews 
            WHERE user_id = ? AND review_type = 'service'
            AND created_at > DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        $stmt->execute([$_SESSION['user_id']]);
        if ($stmt->fetch()) {
            throw new Exception('Service review already submitted within the last 30 days');
        }


        // insert service review
        $stmt = $conn->prepare("
            INSERT INTO Reviews (
                user_id,
                order_id,
                product_id,
                review_text,
                website_usability_rating,
                delivery_service_rating,
                customer_support_rating,
                overall_website_service_rating,
                review_type,
                status,
                created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'service', 'pending', CURRENT_TIMESTAMP)
        ");
        $stmt->execute([
            $_SESSION['user_id'],
            $order['order_id'],
            NULL,
            $data['review_text'],
            $websiteUsabilityRating,
            $deliveryServiceRating,
            $customerSupportRating,
            $overallWebsiteServiceRating
        ]);
    }

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Review submitted successfully'
    ]);

} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollBack();
    }
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
