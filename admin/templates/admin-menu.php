<?php
// get the current page name for active state
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$currentDir = basename(dirname($_SERVER['PHP_SELF']));

// helper function to check if a page is active
function isActive($page, $currentPage, $currentDir = '') {
    if ($currentDir === 'reports' && $page === $currentDir) {
        return true;
    }
    return $page === $currentPage;
}
?>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Admin Menu</h5>
    </div>
    <div class="list-group list-group-flush">
        <a href="<?= SITE_URL ?>/admin/dashboard.php"
           class="list-group-item list-group-item-action <?= isActive('dashboard', $currentPage) ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
        </a>

        <a href="<?= SITE_URL ?>/admin/products.php"
           class="list-group-item list-group-item-action <?= isActive('products', $currentPage) ? 'active' : '' ?>">
            <i class="fas fa-box me-2"></i> Products
        </a>

        <a href="<?= SITE_URL ?>/admin/inventory.php"
           class="list-group-item list-group-item-action <?= isActive('inventory', $currentPage) ? 'active' : '' ?>">
            <i class="fas fa-boxes me-2"></i> Inventory
        </a>

        <a href="<?= SITE_URL ?>/admin/orders.php"
           class="list-group-item list-group-item-action <?= isActive('orders', $currentPage) ? 'active' : '' ?>">
            <i class="fas fa-shopping-cart me-2"></i> Orders
        </a>

        <a href="<?= SITE_URL ?>/admin/customers.php"
           class="list-group-item list-group-item-action <?= isActive('customers', $currentPage) ? 'active' : '' ?>">
            <i class="fas fa-users me-2"></i> Users
        </a>

        <a href="<?= SITE_URL ?>/admin/categories.php"
           class="list-group-item list-group-item-action <?= isActive('categories', $currentPage) ? 'active' : '' ?>">
            <i class="fas fa-tags me-2"></i> Categories
        </a>

        <!-- reports section -->
        <div class="list-group-item list-group-item-action p-0">
            <div class="d-flex align-items-center p-3"
                 data-bs-toggle="collapse"
                 data-bs-target="#reportsCollapse"
                 style="cursor: pointer;">
                <i class="fas fa-chart-bar me-2"></i>
                <span class="flex-grow-1">Reports</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="collapse <?= $currentDir === 'reports' ? 'show' : '' ?>" id="reportsCollapse">
                <a href="<?= SITE_URL ?>/admin/reports/sales.php"
                   class="list-group-item list-group-item-action border-0 ps-5 <?= $currentPage === 'sales' ? 'active' : '' ?>">
                    <i class="fas fa-chart-line me-2"></i> Sales Analysis
                </a>
            </div>
        </div>

        <a href="<?= SITE_URL ?>/admin/messages.php"
           class="list-group-item list-group-item-action <?= isActive('messages', $currentPage) ? 'active' : '' ?>">
            <i class="fas fa-envelope me-2"></i> Messages
        </a>

        <a href="<?= SITE_URL ?>/admin/returns.php"
           class="list-group-item list-group-item-action <?= isActive('returns', $currentPage) ? 'active' : '' ?>">
            <i class="fas fa-undo me-2"></i> Returns
        </a>

        <!-- reviews section -->
        <a href="<?= SITE_URL ?>/admin/reviews.php?tab=product-reviews"
           class="list-group-item list-group-item-action <?= isActive('reviews', $currentPage) ? 'active' : '' ?>">
            <i class="fas fa-star me-2"></i> Reviews
        </a>
        
    </div>
</div>

<style>
.list-group-item.active {
    background-color: #8B7355 !important;
    border-color: #8B7355 !important;
}

.list-group-item:hover:not(.active) {
    background-color: #E8DCCA !important;
    color: #2C1810 !important;
}

.list-group-item i {
    width: 20px;
    text-align: center;
}

#reportsCollapse .list-group-item, #reviewsCollapse .list-group-item {
    padding-left: 3rem !important;
}

#reportsCollapse .list-group-item.active, #reviewsCollapse .list-group-item.active {
    background-color: #D4A373 !important;
    border-color: #D4A373 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // keep reports and reviews sections expanded if on a reports or reviews page
    if (window.location.pathname.includes('/reports/') || window.location.pathname.includes('/admin/reviews.php')) {
        if (window.location.pathname.includes('/reports/')) {
            const reportsCollapse = document.getElementById('reportsCollapse');
            if (reportsCollapse) {
                new bootstrap.Collapse(reportsCollapse, { toggle: false }).show();
            }
        }
        if (window.location.pathname.includes('/admin/reviews.php')) {
            const reviewsCollapse = document.getElementById('reviewsCollapse');
            if (reviewsCollapse) {
                new bootstrap.Collapse(reviewsCollapse, { toggle: false }).show();
            }
        }
    }
});
</script>
