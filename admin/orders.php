<?php
require_once 'config.php';
checkAdminAuth();

$conn = getConnection();
$error = null;
$success = null;

// handle status update
if (isset($_POST['action']) && $_POST['action'] === 'update_status') {
    try {
        $orderId = $_POST['order_id'];
        $status = $_POST['status'];
        
        $stmt = $conn->prepare("UPDATE Orders SET status = ? WHERE order_id = ?");
        $stmt->execute([$status, $orderId]);
        
        header('Location: orders.php?success=Order status updated successfully');
        exit;
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// get filter parameters
$status = $_GET['status'] ?? '';
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';

// build query
$query = "
    SELECT o.*, u.name as username,
           COUNT(oi.order_item_id) as item_count
    FROM Orders o
    JOIN Users u ON o.user_id = u.user_id
    LEFT JOIN Order_Items oi ON o.order_id = oi.order_id
    WHERE 1=1
";
$params = [];

if ($status) {
    $query .= " AND o.status = ?";
    $params[] = $status;
}

if ($dateFrom) {
    $query .= " AND DATE(o.created_at) >= ?";
    $params[] = $dateFrom;
}

if ($dateTo) {
    $query .= " AND DATE(o.created_at) <= ?";
    $params[] = $dateTo;
}

$query .= " GROUP BY o.order_id ORDER BY o.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'templates/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <?php include 'templates/admin-menu.php'; ?>
        </div>
        
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Orders</h4>
                    </div>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
                    <?php endif; ?>
                    
                    <!-- filters -->
                    <form method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All</option>
                                    <option value="processing" <?= $status === 'processing' ? 'selected' : '' ?>>Processing</option>
                                    <option value="shipped" <?= $status === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                    <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Completed</option>
                                    <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date From</label>
                                <input type="date" name="date_from" class="form-control" value="<?= $dateFrom ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date To</label>
                                <input type="date" name="date_to" class="form-control" value="<?= $dateTo ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter me-1"></i> Filter
                                    </button>
                                    <a href="orders.php" class="btn btn-secondary">
                                        <i class="fas fa-undo me-1"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>#<?= $order['order_id'] ?></td>
                                        <td>
                                            <a href="customer-details.php?id=<?= $order['user_id'] ?>" class="text-decoration-none">
                                                <?= htmlspecialchars($order['username']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?= $order['item_count'] ?>
                                            </span>
                                        </td>
                                        <td>£<?= number_format($order['total_price'], 2) ?></td>
                                        <td><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                                        <td>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                                <select name="status" class="form-select form-select-sm d-inline-block w-auto" 
                                                        onchange="this.form.submit()">
                                                    <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>
                                                        Processing
                                                    </option>
                                                    <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>
                                                        Shipped
                                                    </option>
                                                    <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>
                                                        Completed
                                                    </option>
                                                    <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>
                                                        Cancelled
                                                    </option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            <a href="order-details.php?id=<?= $order['order_id'] ?>" 
                                               class="btn btn-sm btn-info">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                
                                <?php if (empty($orders)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No orders found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
