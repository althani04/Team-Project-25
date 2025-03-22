<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Caf Lab</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/signup.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

<?php include 'navbar.php';
include 'basket_include.php';
include 'Chatbot.php'; 
?>


    <main class="signup-container">
        <form id="signup-form" class="signup-form" method="POST" data-aos="fade-up">
            <h1 class="signup-title">Create Your Account</h1>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="firstName">First Name</label>
                    <input type="text" id="firstName" name="firstName" class="form-input" required>
                    <span class="error-message">Please enter your first name</span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="lastName">Last Name</label>
                    <input type="text" id="lastName" name="lastName" class="form-input" required>
                    <span class="error-message">Please enter your last name</span>
                </div>

                <div class="form-group full-width">
                    <label class="form-label" for="email">Email address</label>
                    <input type="email" id="email" name="email" class="form-input" required>
                    <span class="error-message">Please enter a valid email address</span>
                </div>

                <div class="form-group full-width">
                    <label class="form-label" for="role">Role</label>
                    <select id="role" name="role" class="form-input" required>
                        <option value="" disabled selected>Select Role</option>
                        <option value="customer">Customer</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="form-group full-width admin-key-field" style="display: none;">
                    <label class="form-label" for="adminKey">Admin Key</label>
                    <input type="password" id="adminKey" name="adminKey" class="form-input">
                    <span class="error-message">Please enter the correct admin key</span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-input" required>
                    <span class="error-message">Password must be at least 8 characters</span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirmPassword">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" class="form-input" required>
                    <span class="error-message">Passwords do not match</span>
                </div>
            </div>

            <button type="submit" class="submit-btn">Sign Up</button>
        </form>
    </main>

    <footer>
        <p>© 2024 CAF LAB Coffee Company. All Rights Reserved. Ecommerce software by Team Expert 25</p>
    </footer>

    <script>
        $(document).ready(function () {
            $('.admin-key-field').hide(); // initially hide admin key field

            $('#role').change(function () {
                if ($(this).val() === 'admin') {
                    $('.admin-key-field').show();
                } else {
                    $('.admin-key-field').hide();
                }
            });

            $('#signup-form').on('submit', function (e) {
                e.preventDefault();

                const formData = {
                    firstName: $('#firstName').val(),
                    lastName: $('#lastName').val(),
                    email: $('#email').val(),
                    role: $('#role').val(),
                    password: $('#password').val(),
                    confirmPassword: $('#confirmPassword').val(),
                    adminKey: $('#adminKey').val() // include adminkey in formdata
                };

                // front end validation for role
                if (!formData.role) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Role Required',
                        text: 'Please select a role (Customer or Admin) before proceeding.',
                    });
                    return; // stop form submission
                }
                 if (formData.role === 'admin' && !formData.adminKey) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Admin Key Required',
                        text: 'Please enter the Admin Key for admin role.',
                    });
                    return; // stop form submission
                }

                $.ajax({
                    type: 'POST',
                    url: 'process.php',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Registration Successful',
                                text: response.message,
                            }).then(() => {
                                window.location.href = 'login.php';
                            });

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Registration Failed',
                                text: response.message,
                            });

                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong. Please try again later.',
                        });
                    },
                });
            });
        });

    </script>

</body>
</html>
