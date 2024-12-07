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

    <button class="nav-toggle">☰</button>
    
<nav class="nav-menu">
    <ul>
        <li><a href="products.html">Products</a></li>
        <li><a href="subscriptions.html">Subscriptions</a></li>
        <li><a href="story.html">About Us</a></li>
        <li><a href="manageaccount.html">Manage Account</a></li>
        <li><a href="contacts.html">Contact Us</a></li>
        <li><a href="terms.html">Terms & Conditions</a></li>
    </ul>
</nav>


<header class="header">    
    <a href="index.php" class="logo">Caf Lab</a>

    <div class="header-right">
        <a href="login.php">Log In</a>
        <a href="signup.php">Sign Up</a>
        <a href="search.html" class="search">Search</a>
        <a href="basket.html" class="basket">Basket</a>
    </div>
</header>

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
            $('#signup-form').on('submit', function (e) {
                e.preventDefault();

                const formData = {
                    firstName: $('#firstName').val(),
                    lastName: $('#lastName').val(),
                    email: $('#email').val(),
                    password: $('#password').val(),
                    confirmPassword: $('#confirmPassword').val(),
                };

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
        
        
      // Navigation toggle
      const navToggle = document.querySelector('.nav-toggle');
      const navMenu = document.querySelector('.nav-menu');

      navToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        navMenu.classList.toggle('active');

        // Animate menu items
        const menuItems = navMenu.querySelectorAll('li');
        menuItems.forEach((item, index) => {
        item.style.transitionDelay = `${index * 0.1}s`;
        });
      });

      // Close menu when clicking outside
      document.addEventListener('click', (e) => {
          if (!navMenu.contains(e.target) && !navToggle.contains(e.target)) {
              navMenu.classList.remove('active');
          }
      });

    </script>

</body>
</html>
