<?php
require_once 'config.php';
checkAdminAuth();

header('Content-Type: application/json');

try {
    $conn = getConnection();

    // get review details
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get') {
        if (!isset($_GET['review_id'])) {
            throw new Exception('Review ID is required');
        }

        $stmt = $conn->prepare("
            SELECT 
                r.*,
                u.name as customer_name,
                u.email as customer_email,
                p.name as product_name,
                o.order_id
            FROM Reviews r
            JOIN Users u ON r.user_id = u.user_id
            JOIN Orders o ON r.order_id = o.order_id
            LEFT JOIN Products p ON r.product_id = p.product_id
            WHERE r.review_id = ?
        ");
        $stmt->execute([$_GET['review_id']]);
        $review = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$review) {
            throw new Exception('Review not found');
        }

        echo json_encode([
            'success' => true,
            'review' => $review
        ]);
        exit;
    }

    // update review status and response
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['action']) || $data['action'] !== 'update') {
            throw new Exception('Invalid action');
        }

        if (!isset($data['review_id']) || !isset($data['status'])) {
            throw new Exception('Missing required fields');
        }

        $conn->beginTransaction();

        // get review details before update
        $stmt = $conn->prepare("
            SELECT r.*, u.email, p.name as product_name
            FROM Reviews r
            JOIN Users u ON r.user_id = u.user_id
            LEFT JOIN Products p ON r.product_id = p.product_id
            WHERE r.review_id = ?
        ");
        $stmt->execute([$data['review_id']]);
        $review = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$review) {
            throw new Exception('Review not found');
        }

        // update review status and response
        $stmt = $conn->prepare("
            UPDATE Reviews 
            SET status = ?,
                admin_response = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE review_id = ?
        ");
        $stmt->execute([
            $data['status'],
            $data['admin_response'] ?? null,
            $data['review_id']
        ]);

        // send email notifs to user
        $subject = "Your Review Has Been " . ucfirst($data['status']);
        
        $message = "Dear Customer,\n\n";
        $message .= "Your review ";
        if ($review['review_type'] === 'product') {
            $message .= "for " . $review['product_name'] . " ";
        } else {
            $message .= "of our website service ";
        }
        $message .= "has been " . $data['status'] . ".\n\n";
        
        if (!empty($data['admin_response'])) {
            $message .= "Admin Response:\n" . $data['admin_response'] . "\n\n";
        }
        
        $message .= "Thank you for your feedback!\n\n";
        $message .= "Best regards,\nCAF LAB Coffee Company";

        // use your email sending function here
        if (sendEmail($review['email'], $subject, $message)) {
            $conn->commit();
            echo json_encode([
                'success' => true,
                'message' => 'Review updated successfully'
            ]);
        } else {
            throw new Exception('Failed to send notification email');
        }
    }

} catch (Exception $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
