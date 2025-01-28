<link rel="stylesheet" href="css/navbar.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- button for nav (open for pages) -->
<button class="nav-toggle">☰</button>

<!-- nav menu -->
<nav class="nav-menu">
    <ul>
        <li><a href="home.php">Home</a></li>
        <li><a href="products.php">Products</a></li>
        <li><a href="step1.php">Subscriptions</a></li>
        <li><a href="aboutus.php">About Us</a></li>
        <li><a href="blog.html">Blog</a></li>
        <li><a href="manageaccount.php">Manage Account</a></li>
        <li><a href="orderhistory.php">Order History</a></li>
        <li><a href="contact.php">Contact Us</a></li>
        <li><a href="terms.php">Terms & Conditions</a></li>
    </ul>
</nav>

<!-- header section (logo and right links) -->
<header class="header">    
    <?php session_start(); ?>

    <div class="header-right">
        <?php if (isset($_SESSION['user_name'])): ?>
            <a href="manageaccount.html">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></a>
            <a href="#" id="logout-btn">Logout</a>
        <?php else: ?>
            <a href="login.php">Log In</a>
            <a href="signup.php">Sign Up</a>
        <?php endif; ?>
        <a href="#" class="checkout" id="basketIcon">
            <img src="/Team-Project-25/assets/images/basket.png" alt="Basket" style="width: 24px; height: 24px;" />
            <span class="basket-count" style="display: none;">0</span>
        </a>
    </div>

    <div class="logo">
        <a href="home.php">
            <img src="/Team-Project-25/assets/images/caf_lab_logo.png" alt="Company Logo" class="company-logo">
        </a>
    </div>
</header>

<script>
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

    // Logout functionality
    document.querySelector('#logout-btn')?.addEventListener('click', function (e) {
        e.preventDefault(); 
        console.log("Logout button clicked");

        Swal.fire({
            icon: 'success',
            title: 'You have successfully logged out!',
            showConfirmButton: false,
            timer: 3000
        }).then(() => {
            window.location.href = 'logout.php';
        });
    });

    // Initial basket count update
    fetch('get_basket.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const basketCount = document.querySelector('.basket-count');
                if (basketCount) {
                    basketCount.textContent = data.itemCount;
                    basketCount.style.display = data.itemCount > 0 ? 'block' : 'none';
                }
            }
        })
        .catch(error => console.error('Error fetching basket count:', error));
</script>
