<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/login.css">
    <script defer src="js/forgotpassword.js"></script>
</head>
<body>
<?php
session_start();
include 'navbar.php';
include 'basket_include.php';
include 'Chatbot.php';
?>

    <!-- Main content (form container) -->
    <main class="login-container">
        <form id="forgot-password-form" class="login-form" data-aos="fade-up">
            <h1 class="login-title">Forgot Your Password?</h1>
            <p>Enter your email address below to receive a password reset link.</p>
        <br>
            <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" required>
                </div>
                <button type="submit" class="submit-btn">Reset Password</button>
            <div class="forgot-password">
                <a href="login.php" tabindex="0">Back to login page</a>
            </div>
            <div class="signup-link">
                Don't have an account? <a href="signup.php" tabindex="0">Sign up here</a>
            </div>
        </form>
    </main>

    <script>
       document.getElementById('forgot-password-form').addEventListener('submit', function(event) {
    event.preventDefault(); // prevent default form submission

    const email = document.getElementById('email').value;

    fetch('send_reset_email.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'email=' + encodeURIComponent(email)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                title: 'Success!',
                text: data.message,
                icon: 'success',
                confirmButtonText: 'OK'
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.message || 'Something went wrong. Please try again later.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            title: 'Error!',
            text: 'Something went wrong. Please try again later.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
});

    </script>
</body>
</html>
