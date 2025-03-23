<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/forgotpassword.css">
    <script defer src="js/forgotpassword.js"></script>
</head>
<body>

<?php include "navbar.php" ?>

    <!-- Main content (form container) -->
    <main class="main-content">
        <div class="forgot-password-container">
            <h2>Forgot Your Password?</h2>
            <p>Enter your email address below to receive a password reset link.</p>
            <form id="forgot-password-form">
        <br>
            <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" required>
                </div>
                <button type="submit" class="submit-btn">Reset Password</button>
            </form>
            <p class="back-to-login"><a href="login.php">Back to login page</a></p>
        </div>
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
    .then(response => {
        if (response.ok) {
            return response.text();
        } else {
            throw new Error('Request failed');
        }
    })
    .then(data => {
        if (data === 'success') {
            Swal.fire({
                title: 'Success!',
                text: 'Check your inbox for a link (including spam/junk) to reset your password.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: data || 'Something went wrong. Please try again later.',
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
