<?php
require '../config/database.php';

$conn = getConnection();
if (!$conn) {
    die("Database connection failed in update_password.php");
}
session_start();

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if (!$token) {
        $error_message = "Invalid request.";
    } else {
        // check if token exists and is not expired
        $stmt = $conn->prepare("SELECT user_id, expiry_date FROM password_resets WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $reset_data = $result->fetch_assoc();

        if (!$reset_data) {
            $error_message = "Invalid or used reset link.";
        } else {
            $expiry_date = new DateTime($reset_data['expiry_date']);
            $current_date = new DateTime();
            if ($expiry_date < $current_date) {
                $error_message = "Reset link has expired.";
            } else {
                if ($password != $password_confirm) {
                    $error_message = "Passwords do not match.";
                } elseif (strlen($password) < 8) {
                    $error_message = "Password must be at least 8 characters long.";
                } else {
                    // hash the new password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // update password in users table
                    $user_id = $reset_data['user_id'];
                    $stmt = $conn->prepare("UPDATE Users SET password = ? WHERE user_id = ?");
                    $stmt->bind_param("si", $hashed_password, $user_id);

                    if ($stmt->execute()) {
                        // delete token from password_resets table
                        $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
                        $stmt->bind_param("s", $token);
                        $stmt->execute();

                        $success_message = "Password updated successfully. You can now <a href='login.php'>login</a>.";
                    } else {
                        $error_message = "Failed to update password.";
                    }
                }
            }
        }
    }
} else {
    $error_message = "Invalid request method.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Password</title>
    <link rel="stylesheet" href="css/forgotpassword.css">
</head>
<body>
    <div class="container">
        <h2>Update Your Password</h2>

        <?php if ($error_message): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (!$error_message && !$success_message): ?>
            <div class="error-message">Something went wrong. Please try again or contact support.</div>
        <?php endif; ?>
    </div>
</body>
</html>
