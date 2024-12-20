<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/forgotpassword.css">
</head>

<body>
    <div class="forgot-password-container">
        <h1 class="forgot-password-title">Forgot Password</h1>
        <p class="forgot-password-description">Enter your email address below, and we'll send you instructions to reset your password.</p>
        <form id="forgot-password-form">
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="Enter your email address" required>
                <span id="error-message" class="error-message">Please enter a valid email address.</span>
            </div>
            <button type="submit" class="submit-btn">Send Reset Link</button>
            <span id="success-message" class="success-message" style="display: none;">Reset link sent successfully!</span>
        </form>
        <div class="back-to-login">
            <a href="login.php">Back to Login</a>
        </div>
    </div>

    <script>
        const form = document.getElementById('forgot-password-form');
        const emailInput = document.getElementById('email');
        const errorMessage = document.getElementById('error-message');
        const successMessage = document.getElementById('success-message');

        form.addEventListener('submit', (e) => {
            e.preventDefault(); // Prevent default form submission

            // Clear previous messages
            errorMessage.style.display = 'none';
            successMessage.style.display = 'none';

            const emailValue = emailInput.value.trim();

            // Validate email format using regex
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailValue)) {
                errorMessage.style.display = 'block';
                return;
            }

            // Simulate sending the reset link (mocked response)
            setTimeout(() => {
                successMessage.style.display = 'block';
                emailInput.value = ''; // Clear input field
            }, 1000);
        });
    </script>
</body>

</html>
