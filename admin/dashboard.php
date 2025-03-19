<?php
require_once 'config.php';
checkAdminAuth();

$conn = getConnection();

// get recent orders
$stmt = $conn->query("
    SELECT o.*, u.name as customer_name
    FROM Orders o
    JOIN Users u ON o.user_id = u.user_id
    ORDER BY o.created_at DESC
    LIMIT 5
");
$recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// get low stock products
$stmt = $conn->query("
    SELECT p.*, c.name as category_name, i.quantity, i.low_stock_threshold
    FROM Products p
    JOIN Category c ON p.category_id = c.category_id
    JOIN Inventory i ON p.product_id = i.product_id
    WHERE i.quantity <= i.low_stock_threshold
    LIMIT 5
");
$lowStockProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// get todays stats
$today = date('Y-m-d');
$stmt = $conn->prepare("
    SELECT 
        COUNT(DISTINCT o.order_id) as orders_today,
        SUM(o.total_price) as revenue_today,
        COUNT(DISTINCT o.user_id) as customers_today
    FROM Orders o
    WHERE DATE(o.created_at) = ?
    AND o.status != 'cancelled'
");
$stmt->execute([$today]);
$todayStats = $stmt->fetch(PDO::FETCH_ASSOC);

// get this months stats
$monthStart = date('Y-m-01');
$stmt = $conn->prepare("
    SELECT 
        COUNT(DISTINCT o.order_id) as orders_month,
        COALESCE(SUM(o.total_price), 0) as revenue_month,
        COUNT(DISTINCT o.user_id) as customers_month
    FROM Orders o
    WHERE DATE(o.created_at) >= ?
    AND o.status != 'cancelled'
");
$stmt->execute([$monthStart]);
$monthStats = $stmt->fetch(PDO::FETCH_ASSOC);

include 'templates/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <?php include 'templates/admin-menu.php'; ?>
        </div>
        
        <div class="col-md-10">
            <!-- todays overview -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-uppercase mb-1">Today's Orders</h6>
                                    <h3 class="mb-0"><?= number_format($todayStats['orders_today']) ?></h3>
                                </div>
                                <i class="fas fa-shopping-cart fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-uppercase mb-1">Today's Revenue</h6>
                                    <h3 class="mb-0">£<?= number_format($todayStats['revenue_today'] ?? 0, 2) ?></h3>
                                </div>
                                <i class="fas fa-pound-sign fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-uppercase mb-1">Monthly Revenue</h6>
                                    <h3 class="mb-0">£<?= number_format($monthStats['revenue_month'], 2) ?></h3>
                                </div>
                                <i class="fas fa-chart-line fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-uppercase mb-1">Monthly Orders</h6>
                                    <h3 class="mb-0"><?= number_format($monthStats['orders_month'] ?? 0) ?></h3>
                                </div>
                                <i class="fas fa-box fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- reports overview -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card h-100">
                    <div class="card-header">
                            <h5 class="mb-0">Low Stock Alerts</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Alert Type</th>
                                            <th>Count</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($lowStockProducts as $product): ?>
                                            <tr>
                                                <td>
                                                    <span class="badge bg-<?= $product['quantity'] <= 0 ? 'danger' : 'warning' ?>">
                                                        <?= $product['quantity'] <= 0 ? 'Out of Stock' : 'Low Stock' ?> (<?= htmlspecialchars($product['quantity']) ?> units)
                                                    </span>
                                                </td>
                                                <td><?= htmlspecialchars($product['name']) ?></td>
                                                <td>
                                                    <a href="inventory.php" class="btn btn-sm btn-primary">Update Stock</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Sales Analysis Overview</h5>
                            <a href="<?= SITE_URL ?>/admin/reports/sales.php" class="btn btn-sm btn-primary">
                                <i class="fas fa-chart-line me-1"></i> View Full Report
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="p-3 border rounded text-center">
                                        <h3 class="mb-1"><?= number_format($monthStats['orders_month']) ?></h3>
                                        <small class="text-muted">Monthly Orders</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 border rounded text-center">
                                        <h3 class="mb-1">£<?= number_format($monthStats['revenue_month'], 2) ?></h3>
                                        <small class="text-muted">Monthly Revenue</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 border rounded text-center">
                                        <h3 class="mb-1"><?= number_format($monthStats['customers_month']) ?></h3>
                                        <small class="text-muted">Active Customers</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 border rounded text-center">
                                        <h3 class="mb-1">£<?= number_format($monthStats['revenue_month'] / ($monthStats['orders_month'] ?: 1), 2) ?></h3>
                                        <small class="text-muted">Avg. Order Value</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- recent orders -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Orders</h5>
                    <a href="orders.php" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentOrders as $order): ?>
                                    <tr>
                                        <td>#<?= $order['order_id'] ?></td>
                                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                        <td>£<?= number_format($order['total_price'], 2) ?></td>
                                        <td>
                                            <span class="badge bg-<?= getStatusBadgeClass($order['status']) ?>">
                                                <?= ucfirst($order['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></td>
                                        <td>
                                            <a href="order-details.php?id=<?= $order['order_id'] ?>" 
                                               class="btn btn-sm btn-info">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
