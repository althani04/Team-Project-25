<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In - Caf Lab</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
<?php
session_start();
// store the requested URL if its an admin page
if (isset($_SERVER['HTTP_REFERER'])) {
    if (strpos($_SERVER['HTTP_REFERER'], '/admin/') !== false) {
        $_SESSION['return_to'] = $_SERVER['HTTP_REFERER'];
    }
} else if (isset($_GET['redirect']) && strpos($_GET['redirect'], '/admin/') !== false) {
    $_SESSION['return_to'] = $_GET['redirect'];
}
include 'navbar.php';
include 'basket_include.php';
include 'Chatbot.php';

?>

<main class="login-container">
        <form id="resetPasswordForm" class="login-form" data-aos="fade-up">
            <h1 class="login-title">Reset Your Password</h1>
            
            <?php if ($error_message): ?>
                <div class="error-message" id="phpErrorMessage"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <?php if (!$error_message && $token): ?>
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                
                <div class="form-group">
                    <label class="form-label" for="password">New Password</label>
                    <input type="password" id="password" name="password" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password_confirm">Confirm New Password</label>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-input" required>
                </div>
                
                <button type="submit" class="submit-btn">Reset Password</button>
            <?php endif; ?>
        </form>
    </main>

    <footer>
        <p>© 2024 CAF LAB Coffee Company. All Rights Reserved. Ecommerce software by Team Expert 25</p>
    </footer>

    <script>
        document.getElementById('login-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            
            fetch('process_login.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Login Successful',
                        text: 'You have logged in successfully.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        if (data.isAdmin) {
                            window.location.href = data.redirect || '/Team-Project-255/admin/dashboard.php';
                        } else {
                            window.location.href = data.redirect || 'home.php';
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Failed',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong. Please try again later.'
                });
            });
        });
    </script>
</body>

</html>
