<link rel="stylesheet" href="/Team-Project-255/CaflabProject/public/css/navbar.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- button for nav (open for pages) -->
<button class="nav-toggle">☰</button>

<!-- nav menu -->
<nav class="nav-menu">
    <ul>
        <li><a href="/Team-Project-255/CaflabProject/public/home.php" class="<?php if (strpos($_SERVER['REQUEST_URI'], 'home.php')) echo 'active-link'; ?>">Home</a></li>
        <li><a href="/Team-Project-255/CaflabProject/public/products.php" class="<?php if (strpos($_SERVER['REQUEST_URI'], 'products.php')) echo 'active-link'; ?>">Products</a></li>
        <li><a href="/Team-Project-255/CaflabProject/public/step1.php" class="<?php if (strpos($_SERVER['REQUEST_URI'], 'step1.php')) echo 'active-link'; ?>">Subscriptions</a></li>
        <li><a href="/Team-Project-255/CaflabProject/public/aboutus.php" class="<?php if (strpos($_SERVER['REQUEST_URI'], 'aboutus.php')) echo 'active-link'; ?>">About Us</a></li>
        <li><a href="/Team-Project-255/Blog Page/BlogHomepage.html" class="<?php if (strpos($_SERVER['REQUEST_URI'], 'BlogHomepage.html')) echo 'active-link'; ?>">Blog</a></li>
        <li><a href="/Team-Project-255/CaflabProject/public/manageaccount.php" class="<?php if (strpos($_SERVER['REQUEST_URI'], 'manageaccount.php')) echo 'active-link'; ?>">Manage Account</a></li>
        <li><a href="/Team-Project-255/CaflabProject/public/orderhistory.php" class="<?php if (strpos($_SERVER['REQUEST_URI'], 'orderhistory.php')) echo 'active-link'; ?>">Order History</a></li>
        <li><a href="/Team-Project-255/CaflabProject/public/contact.php" class="<?php if (strpos($_SERVER['REQUEST_URI'], 'contact.php')) echo 'active-link'; ?>">Contact Us</a></li>
        <li><a href="/Team-Project-255/CaflabProject/public/terms.php" class="<?php if (strpos($_SERVER['REQUEST_URI'], 'terms.php')) echo 'active-link'; ?>">Terms & Conditions</a></li>
    </ul>
</nav>
<?php
    $current_page = $_SERVER['REQUEST_URI'];
?>

<!-- header section (logo and right links) -->
<header class="header">    
    <div class="header-right">
        <?php if (isset($_SESSION['user_name'])): ?>
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <a href="/Team-Project-255/admin/dashboard.php" style="color: #007bff;" class="<?php if (strpos($_SERVER['REQUEST_URI'], '/admin/dashboard.php')) echo 'active-link'; ?>">Admin</a>
            <?php endif; ?>
            <a href="/Team-Project-255/CaflabProject/public/manageaccount.php" class="<?php if (strpos($_SERVER['REQUEST_URI'], '/manageaccount.php')) echo 'active-link'; ?>">My Account</a>
            <a href="#" id="logout-btn">Logout</a>
        <?php else: ?>
            <a href="login.php" class="<?php if (strpos($_SERVER['REQUEST_URI'], 'login.php')) echo 'active-link'; ?>">Log In</a>
            <a href="signup.php" class="<?php if (strpos($_SERVER['REQUEST_URI'], 'signup.php')) echo 'active-link'; ?>">Sign Up</a>
        <?php endif; ?>
        <a href="#" class="checkout active-link" id="basketIcon">
            <div style="display: inline-block; position: relative;">
                <span class="basket-count" style="display: none; background-color: #8B4513; color: white; border-radius: 50%; padding: 2px 6px; font-size: 9px;">0</span>
                <img src="/Team-Project-255/assets/images/basket.png" alt="Basket" style="width: 30px; height: 30px;" />
            </div>
        </a>
        <a href="/Team-Project-255/CaflabProject/public/wishlist.php" class="wishlist-link">&#x2661;</a>
    </div>

    <div class="logo">
        <a href="/Team-Project-255/CaflabProject/public/home.php">
            <img src="/Team-Project-255/assets/images/caf_lab_logo.png" alt="Company Logo" class="company-logo">
        </a>
    </div>
</header>

<script>
    // nav toggle
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');

    navToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        navMenu.classList.toggle('active');

        // animate the menu items
        const menuItems = navMenu.querySelectorAll('li');
        menuItems.forEach((item, index) => {
            item.style.transitionDelay = `${index * 0.1}s`;
        });
    });

    // close menu if clicked anywhere outside
    document.addEventListener('click', (e) => {
        if (!navMenu.contains(e.target) && !navToggle.contains(e.target)) {
            navMenu.classList.remove('active');
        }
    });

    // logout functionality
    document.querySelector('#logout-btn')?.addEventListener('click', function (e) {
        e.preventDefault(); 
        console.log("Logout button clicked");

        Swal.fire({
            icon: 'success',
            title: 'You have successfully logged out!',
            showConfirmButton: false,
            timer: 3000
        }).then(() => {
            let logoutUrl = '/Team-Project-255/CaflabProject/public/logout.php';
if (window.location.pathname.startsWith('/admin/')) {
    logoutUrl = '/Team-Project-255/CaflabProject/public/logout.php';
}
window.location.href = logoutUrl;
        });
    });

    // initial basket count update
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
