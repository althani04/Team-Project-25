<link rel="stylesheet" href="css/navbar.css">

    <!-- button for nav (open for pages) -->
    <button class="nav-toggle">☰</button>
    
    <!-- nav menu -->
    <nav class="nav-menu">
        <ul>
            <li><a href="products.html">Products</a></li>
            <li><a href="subscriptions.html">Subscriptions</a></li>
            <li><a href="story.html">About Us</a></li>
            <li><a href="blog.html">Blog</a></li>
            <li><a href="manageaccount.html">Manage Account</a></li>
            <li><a href="contact.php">Contact Us</a></li>
            <li><a href="terms.html">Terms & Conditions</a></li>
        </ul>
    </nav>

    <!-- header section (logo and right links) -->
    <header class="header">    
        <a href="index.php" class="logo">Caf Lab</a>

        <div class="header-right">
            <a href="login.php">Log In</a>
            <a href="signup.php">Sign Up</a>
            <a href="basket.html" class="basket">
                <img src="../../assets/images/basket.png" alt="Basket" style="width: 24px; height: 24px;" />
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
</script>
