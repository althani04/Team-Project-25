<?php
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// receive and process the request data
$data = json_decode(file_get_contents('php://input'), true);

// validate the required parameters
$required = ['order_id','user_id', 'plan_id', 'quantity', 'frequency', 'start_date', 'payment_plan', 'total_price'];
foreach ($required as $field) {
    if (!isset($data[$field])) {
        http_response_code(400);
        echo json_encode(['error' => "Missing necessary parameters: $field"]);
        exit;
    }
}

// process the received parameters
$subscription = [
    'order_id' => $data['order_id'],
    'user_id' => (int)$data['user_id'],
    'plan_id' => (int)$data['plan_id'],
    'quantity' => (int)$data['quantity'],
    'frequency' => in_array($data['frequency'], ['2 weeks', 'Month']) ? $data['frequency'] : 'Month', // Ensure that the enumeration values are valid
    'start_date' => date('Y-m-d', strtotime($data['start_date'])),
    'payment_plan' => $data['payment_plan'] === 'pay-per-month' ? 'monthly' : 'annually',
    'total_price' => (float)$data['total_price']
];

// calculate the end date (based on frequency)
$interval = $subscription['frequency'] === '2 weeks' ? 'P2W' : 'P1M';
$end_date = new DateTime($subscription['start_date']);
$end_date->add(new DateInterval($interval));
$subscription['end_date'] = $end_date->format('Y-m-d');

try {
    // add debug log
    error_log("Preparing the subscription data for insertion：" . print_r($subscription, true));
    
    $sql = "INSERT INTO Subscriptions 
            (user_id, plan_id, quantity, frequency, start_date, end_date, payment_plan, total_price, order_id)
            VALUES 
            (:user_id, :plan_id, :quantity, :frequency, :start_date, :end_date, :payment_plan, :total_price, :order_id)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':user_id' => $subscription['user_id'],
        ':plan_id' => $subscription['plan_id'],
        ':quantity' => $subscription['quantity'],
        ':frequency' => $subscription['frequency'],
        ':start_date' => $subscription['start_date'],
        ':end_date' => $subscription['end_date'],
        ':payment_plan' => $subscription['payment_plan'],
        ':total_price' => $subscription['total_price'],
        ':order_id' => $subscription['order_id']
    ]);
    
    $subscription_id = $pdo->lastInsertId();

} catch (PDOException $e) {
    error_log("database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'subscribe failed']);
    exit;
}

// record the received data (for debugging)
error_log("Received subscription: " . print_r($subscription, true));

// return the success response
http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'subscribe success',
    'data' => array_merge($subscription, ['subscription_id' => $subscription_id])
]);