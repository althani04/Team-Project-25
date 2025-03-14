<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

// check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please log in to update your profile'
    ]);
    exit;
}

// get POST data
$name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$phone = htmlspecialchars(trim($_POST['phone']), ENT_QUOTES, 'UTF-8');
$address = htmlspecialchars(trim($_POST['address']), ENT_QUOTES, 'UTF-8');
$postcode = htmlspecialchars(trim($_POST['postcode']), ENT_QUOTES, 'UTF-8');


// validate required fields
if (empty($name) || empty($email)) {
    echo json_encode([
        'success' => false,
        'message' => 'Name and email are required'
    ]);
    exit;
}

// validate the format of the email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid email format'
    ]);
    exit;
}

// Validate delivery address
$address = $_POST['address'];
if (strlen($address) < 5 || strlen($address) > 255 || preg_match('/[@#%$^&]/', $address)) {
    echo json_encode([
        'success' => false,
        'message' => 'Please enter a valid delivery address.'
    ]);
    exit;
}

// Validate postcode (UK format)
$postcode = $_POST['postcode'];
if (!preg_match('/^[A-Z]{1,2}[0-9][0-9A-Z]?\s?[0-9][A-Z]{2}$/i', $postcode)) {
    echo json_encode([
        'success' => false,
        'message' => 'Please enter a valid UK postcode.'
    ]);
    exit;
}

// Validate phone number (UK format)
$phone = $_POST['phone'];
if (!preg_match('/^(?:0|\+?44)(?:\d\s?){9,10}$/', $phone)) {
    echo json_encode([
        'success' => false,
        'message' => 'Please enter a valid UK phone number.'
    ]);
    exit;
}

try {
    // check if email is already used by another user
    $stmt = $pdo->prepare("
        SELECT user_id FROM Users 
        WHERE email = ? AND user_id != ?
    ");
    $stmt->execute([$email, $_SESSION['user_id']]);
    if ($stmt->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'Email is already in use by another account'
        ]);
        exit;
    }

    // update the users details
    $stmt = $pdo->prepare("
        UPDATE Users 
        SET name = ?, 
            email = ?, 
            phone_number = ?, 
            address_line = ?, 
            postcode = ?,
            date_updated = CURRENT_TIMESTAMP
        WHERE user_id = ?
    ");

    $stmt->execute([
        $name,
        $email,
        $phone,
        $address,
        $postcode,
        $_SESSION['user_id']
    ]);

    // update session data
    $_SESSION['user_name'] = $name;

    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully'
    ]);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while updating your profile'
    ]);
}
?>
