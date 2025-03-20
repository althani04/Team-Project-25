<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="css/forgotpassword.css">
</head>
<body>
    <div class="container">
        <h2>Forgot Your Password?</h2>
        <p>Enter your email address to receive a password reset link.</p>
        <form action="send_reset_email.php" method="post">
            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <button type="submit" class="btn-primary">Reset Password</button>
        </form>
        <p class="login-link">Remember your password? <a href="login.php">Login</a></p>
    </div>
</body>
</html>
