<?php
require_once 'config.php';
checkAdminAuth();

$conn = getConnection();
$error = null;

if (!isset($_GET['id'])) {
    header('Location: customers.php');
    exit;
}

$userId = $_GET['id'];

// get customer details
$stmt = $conn->prepare("
    SELECT * FROM Users 
    WHERE user_id = ?
");
$stmt->execute([$userId]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    header('Location: customers.php');
    exit;
}

// get customers orders
$stmt = $conn->prepare("
    SELECT o.*, 
           COUNT(oi.order_item_id) as item_count
    FROM Orders o 
    LEFT JOIN Order_Items oi ON o.order_id = oi.order_id
    WHERE o.user_id = ?
    GROUP BY o.order_id
    ORDER BY o.created_at DESC
");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// get customers reviews
$stmt = $conn->prepare("
    SELECT r.*, p.name as product_name
    FROM Reviews r
    JOIN Products p ON r.product_id = p.product_id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
");
$stmt->execute([$userId]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// get customer statistics
$stmt = $conn->prepare("
    SELECT 
        COUNT(DISTINCT o.order_id) as total_orders,
        COALESCE(SUM(o.total_price), 0) as total_spent,
        COUNT(DISTINCT r.review_id) as total_reviews,
        AVG(r.rating) as avg_rating
    FROM Users u
    LEFT JOIN Orders o ON u.user_id = o.user_id
    LEFT JOIN Reviews r ON u.user_id = r.user_id
    WHERE u.user_id = ?
");
$stmt->execute([$userId]);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

include 'templates/header.php';
?>

<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Admin Menu</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="dashboard.php" class="list-group-item list-group-item-action">Dashboard</a>
                    <a href="products.php" class="list-group-item list-group-item-action">Products</a>
                    <a href="orders.php" class="list-group-item list-group-item-action">Orders</a>
                    <a href="customers.php" class="list-group-item list-group-item-action active">Customers</a>
                    <a href="categories.php" class="list-group-item list-group-item-action">Categories</a>
                    <a href="messages.php" class="list-group-item list-group-item-action">Messages</a>
                    <a href="reviews.php" class="list-group-item list-group-item-action">Reviews</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Customer Details</h4>
                    <div>
<a href="customer-form.php?id=<?= $customer['user_id'] ?>" class="btn btn-primary me-2">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="customers.php" class="btn btn-secondary">Back to Customers</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Account Information</h5>
                            <table class="table">
                                <tr>
                                    <th style="width: 35%">Name:</th>
                                    <td><?= htmlspecialchars($customer['name']) ?></td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td><?= htmlspecialchars($customer['email']) ?></td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td><?= htmlspecialchars($customer['phone_number'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <th>Address:</th>
                                    <td>
                                        <?php if ($customer['address_line']): ?>
                                            <?= htmlspecialchars($customer['address_line']) ?><br>
                                            <?= htmlspecialchars($customer['postcode']) ?>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Joined:</th>
                                    <td><?= date('M j, Y', strtotime($customer['date_created'])) ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Customer Statistics</h5>
                            <div class="row g-4">
                                <div class="col-6">
                                    <div class="p-3 border rounded text-center">
                                        <h3 class="mb-1"><?= $stats['total_orders'] ?></h3>
                                        <small class="text-muted">Total Orders</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 border rounded text-center">
                                        <h3 class="mb-1">£<?= number_format($stats['total_spent'], 2) ?></h3>
                                        <small class="text-muted">Total Spent</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 border rounded text-center">
                                        <h3 class="mb-1"><?= $stats['total_reviews'] ?></h3>
                                        <small class="text-muted">Reviews</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 border rounded text-center">
                                        <h3 class="mb-1">
                                            <?php if ($stats['avg_rating']): ?>
                                                <?= number_format($stats['avg_rating'], 1) ?> ★
                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </h3>
                                        <small class="text-muted">Avg Rating</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order History</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($orders)): ?>
                        <p class="text-muted">No orders found.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>#<?= $order['order_id'] ?></td>
                                            <td><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                                            <td><?= $order['item_count'] ?></td>
                                            <td>£<?= number_format($order['total_price'], 2) ?></td>
                                            <td>
                                                <span class="badge bg-<?= getStatusBadgeClass($order['status']) ?>">
                                                    <?= ucfirst($order['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="order-details.php?id=<?= $order['order_id'] ?>" 
                                                   class="btn btn-sm btn-info">View</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Reviews</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($reviews)): ?>
                        <p class="text-muted">No reviews found.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Rating</th>
                                        <th>Review</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reviews as $review): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($review['product_name']) ?></td>
                                            <td>
                                                <div class="text-warning">
                                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                                        <i class="fas fa-star<?= $i >= $review['rating'] ? '-o' : '' ?>"></i>
                                                    <?php endfor; ?>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($review['review_text']) ?></td>
                                            <td><?= date('M j, Y', strtotime($review['created_at'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
