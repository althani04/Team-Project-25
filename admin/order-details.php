<?php
require_once 'config.php';
checkAdminAuth();

$conn = getConnection();
$error = null;

if (!isset($_GET['id'])) {
    header('Location: orders.php');
    exit;
}

$orderId = $_GET['id'];

// get order details with customer info
$stmt = $conn->prepare("
    SELECT o.*, u.name as username, u.email, u.phone_number, u.address_line, u.postcode
    FROM Orders o
    JOIN Users u ON o.user_id = u.user_id
    WHERE o.order_id = ?
");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: orders.php');
    exit;
}

// get order items with product details
$stmt = $conn->prepare("
    SELECT oi.*, p.name as product_name, p.image_url
    FROM Order_Items oi
    JOIN Products p ON oi.product_id = p.product_id
    WHERE oi.order_id = ?
");
$stmt->execute([$orderId]);
$orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// handlde status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    try {
        $status = $_POST['status'];
        
        $stmt = $conn->prepare("UPDATE Orders SET status = ? WHERE order_id = ?");
        $stmt->execute([$status, $orderId]);
        
        $order['status'] = $status;
        $success = 'Order status updated successfully';
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

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
                    <a href="orders.php" class="list-group-item list-group-item-action active">Orders</a>
                    <a href="customers.php" class="list-group-item list-group-item-action">Customers</a>
                    <a href="categories.php" class="list-group-item list-group-item-action">Categories</a>
                    <a href="messages.php" class="list-group-item list-group-item-action">Messages</a>
                    <a href="reviews.php" class="list-group-item list-group-item-action">Reviews</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Order #<?= $order['order_id'] ?></h4>
                    <a href="orders.php" class="btn btn-secondary">Back to Orders</a>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Order Information</h5>
                            <table class="table">
                                <tr>
                                    <th>Order Date:</th>
                                    <td><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="update_status">
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
                                </tr>
                                <tr>
                                    <th>Total Amount:</th>
                                    <td>£<?= number_format($order['total_price'], 2) ?></td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Customer Information</h5>
                            <table class="table">
                                <tr>
                                    <th>Name:</th>
                                    <td>
                                        <a href="customer-details.php?id=<?= $order['user_id'] ?>">
                                            <?= htmlspecialchars($order['username']) ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td><?= htmlspecialchars($order['email']) ?></td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td><?= htmlspecialchars($order['phone_number'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <th>Address:</th>
                                    <td>
                                        <?php if ($order['address_line']): ?>
                                            <?= htmlspecialchars($order['address_line']) ?><br>
                                            <?= htmlspecialchars($order['postcode']) ?>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <h5>Order Items</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Image</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orderItems as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                                        <td>
                                            <?php if ($item['image_url']): ?>
                                                <img src="<?= ASSETS_PATH ?>/images/<?= htmlspecialchars($item['image_url']) ?>" 
                                                     alt="<?= htmlspecialchars($item['product_name']) ?>" 
                                                     class="img-thumbnail" style="width: 50px;">
                                            <?php else: ?>
                                                <div class="bg-light text-center" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>£<?= number_format($item['price'], 2) ?></td>
                                        <td><?= $item['quantity'] ?></td>
                                        <td>£<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                    <td><strong>£<?= number_format($order['total_price'], 2) ?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
