<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="css/forgotpassword.css">
</head>
<body>
    <div class="container">
        <h2>Forgot Your Password?</h2>
        <p>Enter your email address to receive a password reset link.</p>
        <form id="forgotPasswordForm">
            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <button type="submit" class="btn-primary">Reset Password</button>
        </form>
        <p class="login-link">Remember your password? <a href="login.php">Login</a></p>
    </div>

    <script>
        document.getElementById('forgotPasswordForm').addEventListener('submit', function(event) {
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
                    return response.text(); // or response.json() if send_reset_email.php returns JSON
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
                        text: data || 'Something went wrong. Please try again later.', // Display error message from send_reset_email.php if available
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
