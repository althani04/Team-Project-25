<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <div class="container">
        <a class="navbar-brand" href="<?php echo BASE_URL; ?>">Caf Lab</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php 
                    $current_page = basename($_SERVER['PHP_SELF']); 
                ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'products.php') ? 'active text-primary' : ''; ?>" 
                       href="<?php echo BASE_URL; ?>/pages/products.php">Shop</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'about.php') ? 'active text-primary' : ''; ?>" 
                       href="<?php echo BASE_URL; ?>/pages/about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'contact.php') ? 'active text-primary' : ''; ?>" 
                       href="<?php echo BASE_URL; ?>/pages/contact.php">Contact</a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'cart.php') ? 'active text-primary' : ''; ?>" 
                           href="<?php echo BASE_URL; ?>/pages/cart.php">
                            <i class="fas fa-shopping-cart"></i>
                            <?php 
                                // Get cart count
                                $stmt = $pdo->prepare("SELECT COUNT(*) FROM cart_items WHERE user_id = ?");
                                $stmt->execute([getCurrentUserId()]);
                                $cartCount = $stmt->fetchColumn();
                                if ($cartCount > 0): 
                            ?>
                                <span class="badge bg-primary"><?php echo $cartCount; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <?php echo $_SESSION['first_name']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if (isAdmin()): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/dashboard.php">Dashboard</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item <?php echo ($current_page == 'orders.php') ? 'active text-primary' : ''; ?>" 
                                   href="<?php echo BASE_URL; ?>/pages/orders.php">My Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/auth/logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'login.php') ? 'active text-primary' : ''; ?>" 
                           href="<?php echo BASE_URL; ?>/auth/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'signup.php') ? 'active text-primary' : ''; ?>" 
                           href="<?php echo BASE_URL; ?>/auth/signup.php">Sign Up</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Spacer for fixed navbar -->
<div style="margin-top: 76px;"></div>