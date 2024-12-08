<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In - Caf Lab</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <header class="header">
        <a href="index.php" class="logo">Caf Lab</a>
    </header>

    <main class="login-container">
        <form id="login-form" class="login-form" data-aos="fade-up">
            <h1 class="login-title">Welcome</h1>

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-input" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" class="form-input" required>
            </div>

            <div class="checkbox-container">
                <input type="checkbox" id="keep-signed-in">
                <label for="keep-signed-in">Keep me signed in</label>
            </div>

            <button type="submit" class="submit-btn">Login</button>
            <div class="forgot-password">
                <a href="forgotpassword.html">Forgot your password?</a>
            </div>

            </div>
            <div class="divider">Or</div>

            <button class="guest-btn">
                <img src= "https://cdn-icons-png.flaticon.com/512/847/847969.png"alt="Guest Logo">
                Continue as Guest
            </button>

            <div class="signup-link">
                Don't have an account? <a href="signup.php">Sign up here</a>
            </div>
        </form>
    </main>

    <footer>
        <p>© 2024 CAF LAB Coffee Company. All Rights Reserved. Ecommerce software by Team Expert 25</p>
    </footer>

    <script>
        $(document).ready(function () {
            $('#login-form').on('submit', function (e) {
                e.preventDefault(); 

                const email = $('#email').val().trim();
                const password = $('#password').val().trim();

                // validate the input from user
                if (!email || !password) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Input',
                        text: 'Please enter both email and password.',
                    });
                    return;
                }

                // an ajx call to the process_login.php file
                $.ajax({
                    type: 'POST',
                    url: 'process_login.php',
                    data: { email: email, password: password },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Login Successful',
                                text: 'You have logged in successfully.',
                            }).then(() => {
                                window.location.href = 'index.php'; // path to homepage
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Login Failed',
                                text: response.message,
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong. Please try again later.',
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>
