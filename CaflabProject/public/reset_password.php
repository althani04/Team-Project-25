<?php
session_start();

// Include database connection
require '../config/database.php';

$conn = null; // Initialize $conn

try {
    // Get database connection
    $conn = getConnection();
    if (!$conn) {
        throw new Exception("Failed to connect to the database.");
    }

    $error_message = "";
    $token = isset($_GET['token']) ? $_GET['token'] : ''; 
    $reset_data = null; 

    if (empty($token)) {
        $error_message = "Invalid reset link.";
    } else {
        // checkto see if token exists and is not expired
        $stmt = null;
        try {
            $stmt = $conn->prepare("SELECT user_id, expiry_date FROM password_resets WHERE token = ?");
            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . print_r($conn->errorInfo(), true));
            }
            $stmt->bindValue(1, $token);
            if (!$stmt->execute()) {
                throw new Exception("Statement execution failed: " . print_r($stmt->errorInfo(), true));
            }
            $reset_data = $stmt->fetch(PDO::FETCH_ASSOC); 


            if (!$reset_data) {
                $error_message = "Invalid or used reset link.";
            } else {
                $expiry_date = new DateTime($reset_data['expiry_date']);
                $current_date = new DateTime();
                if ($expiry_date < $current_date) {
                    $error_message = "Reset link has expired.";
                }
            }
        } finally {
            
        }
    }


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!$conn) {
            throw new Exception("Database connection lost in POST request.");
        }
        $password = $_POST['password'];
        $password_confirm = $_POST['password_confirm'];

        if ($password != $password_confirm) {
            $error_message = "Passwords do not match.";
        } elseif (strlen($password) < 8) {
            $error_message = "Password must be at least 8 characters long.";
        } elseif ($reset_data) { 
            // hashe new password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // update password in users table
            $user_id = $reset_data['user_id']; 
            $stmt = null;
            try {
                $stmt = $conn->prepare("UPDATE Users SET password = ? WHERE user_id = ?");
                if (!$stmt) {
                    throw new Exception("Prepare statement failed: " . print_r($conn->errorInfo(), true));
                }
                $stmt->bindValue(1, $hashed_password);
                $stmt->bindValue(2, $user_id);

                if ($stmt->execute()) {
                    // delete token from passwords reset table after use
                    $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
                    if (!$stmt) {
                        throw new Exception("Prepare statement failed: " . print_r($conn->errorInfo(), true));
                    }
                    $stmt->bindValue(1, $token);
                    $stmt->execute();


                    $_SESSION['success_message'] = "Password updated successfully. You can now <a href='login.php'>login</a>.";
                    header("Location: login.php"); 
                    exit();
                } else {
                    $error_message = "Failed to update password.";
                }
            } finally {
                
            }
        }
    }


} catch (Exception $e) {
    // log error and display a user-friendly message
    error_log("Password reset error in reset_password.php: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
    $error_message = "An error occurred during password reset. Please try again later.";

} finally {
    if ($conn) {
         $conn = null; 
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="css/forgotpassword.css">
</head>
<body>
    <div class="container">
        <h2>Reset Your Password</h2>

        <?php if ($error_message): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (!$error_message && $token): ?>
            <form action="" method="post">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <div class="form-group">
                    <label for="password">New Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="password_confirm">Confirm New Password:</label>
                    <input type="password" id="password_confirm" name="password_confirm" required>
                </div>
                <button type="submit" class="btn-primary">Reset Password</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
