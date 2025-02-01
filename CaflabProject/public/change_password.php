<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

// check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please log in to change your password'
    ]);
    exit;
}

// get POST data
$currentPassword = $_POST['current_password'];
$newPassword = $_POST['new_password'];
$confirmPassword = $_POST['confirm_password'];

// validate the input (for password - must not be left empty)
if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    echo json_encode([
        'success' => false,
        'message' => 'All fields are required'
    ]);
    exit;
}

// a check to see if new passwords match
if ($newPassword !== $confirmPassword) {
    echo json_encode([
        'success' => false,
        'message' => 'New passwords do not match'
    ]);
    exit;
}

// password strength validation
if (strlen($newPassword) < 8) {
    echo json_encode([
        'success' => false,
        'message' => 'Password must be at least 8 characters long'
    ]);
    exit;
}

if (!preg_match('/[A-Z]/', $newPassword) || 
    !preg_match('/[a-z]/', $newPassword) || 
    !preg_match('/[0-9]/', $newPassword)) {
    echo json_encode([
        'success' => false,
        'message' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number'
    ]);
    exit;
}

try {
    // get the userss current password
    $stmt = $pdo->prepare("SELECT password FROM Users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    // verify the current password
    if (!password_verify($currentPassword, $user['password'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Current password is incorrect'
        ]);
        exit;
    }

    // hash the new password (for securiyt)
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // update password
    $stmt = $pdo->prepare("
        UPDATE Users 
        SET password = ?, 
            date_updated = CURRENT_TIMESTAMP
        WHERE user_id = ?
    ");
    $stmt->execute([$hashedPassword, $_SESSION['user_id']]);

    echo json_encode([
        'success' => true,
        'message' => 'Password changed successfully'
    ]);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while changing your password'
    ]);
}
?>
