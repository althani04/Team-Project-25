<?php
require_once 'config.php';
checkAdminAuth();

$conn = getConnection();
$error = null;
$success = null;

// get filter parameters
$categoryId = $_GET['category_id'] ?? '';
$stockLevel = $_GET['stock_level'] ?? '';
$search = $_GET['search'] ?? '';

// get categories for filter
$stmt = $conn->query("SELECT * FROM Category ORDER BY name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// build query
$query = "
    SELECT i.*, p.name as product_name, p.image_url, c.name as category_name
    FROM Inventory i
    JOIN Products p ON i.product_id = p.product_id
    JOIN Category c ON p.category_id = c.category_id
    WHERE 1=1
";
$params = [];

if ($categoryId) {
    $query .= " AND p.category_id = ?";
    $params[] = $categoryId;
}

if ($stockLevel) {
    if ($stockLevel === 'low') {
        $query .= " AND i.quantity <= i.low_stock_threshold AND i.quantity > 0";
    } elseif ($stockLevel === 'out') {
        $query .= " AND i.quantity = 0";
    } elseif ($stockLevel === 'in') {
        $query .= " AND i.quantity > i.low_stock_threshold";
    }
}

if ($search) {
    $query .= " AND (p.name LIKE ? OR p.description LIKE ? OR i.size LIKE ?)";
    $searchTerm = "%$search%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
}

$query .= " ORDER BY p.name ASC, i.size ASC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);

// get recent transactions
$stmt = $conn->query("
    SELECT t.*, i.size, p.name as product_name, u.name as user_name
    FROM Inventory_Transactions t
    JOIN Inventory i ON t.inventory_id = i.inventory_id
    JOIN Products p ON i.product_id = p.product_id
    JOIN Users u ON t.created_by = u.user_id
    ORDER BY t.created_at DESC
    LIMIT 10
");
$recentTransactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'templates/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <?php include 'templates/admin-menu.php'; ?>
        </div>
        
        <div class="col-md-10">
            <!-- filters -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Inventory Management</h4>
                </div>
                <div class="card-body">
                    <form method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-select">
                                    <option value="">All Categories</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['category_id'] ?>"
                                                <?= $categoryId == $category['category_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Stock Level</label>
                                <select name="stock_level" class="form-select">
                                    <option value="">All Levels</option>
                                    <option value="in" <?= $stockLevel === 'in' ? 'selected' : '' ?>>In Stock</option>
                                    <option value="low" <?= $stockLevel === 'low' ? 'selected' : '' ?>>Low Stock</option>
                                    <option value="out" <?= $stockLevel === 'out' ? 'selected' : '' ?>>Out of Stock</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" class="form-control"
                                       value="<?= htmlspecialchars($search) ?>"
                                       placeholder="Search products...">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="inventory.php" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Size</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Last Restock</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($inventory as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if ($item['image_url']): ?>
                                                    <img src="<?= ASSETS_PATH ?>/images/<?= htmlspecialchars($item['image_url']) ?>"
                                                         alt="<?= htmlspecialchars($item['product_name']) ?>"
                                                         class="img-thumbnail me-2" style="width: 50px;">
                                                <?php endif; ?>
                                                <?= htmlspecialchars($item['product_name']) ?>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($item['category_name']) ?></td>
                                        <td><?= htmlspecialchars($item['size'] ?? 'N/A') ?></td>
                                        <td><?= $item['quantity'] ?></td>
                                        <td>
                                            <?php
                                                $stockStatus = 'in stock';
                                                $badgeClass = 'success';
                                                if ($item['quantity'] <= 0) {
                                                    $stockStatus = 'out of stock';
                                                    $badgeClass = 'danger';
                                                } elseif ($item['quantity'] <= 5) {
                                                    $stockStatus = 'low stock';
                                                    $badgeClass = 'warning';
                                                }
                                            ?>
                                            <span class="badge bg-<?= $badgeClass ?>">
                                                <?= ucfirst($stockStatus) ?> (<?= $item['quantity'] ?> units)
                                            </span>
                                        </td>
                                        <td>
                                            <?= $item['last_restock_date'] ?
                                                date('M j, Y', strtotime($item['last_restock_date'])) :
                                                'Never' ?>
                                        </td>
                                        <td>
                                            <button type="button"
                                                    class="btn btn-sm btn-primary"
                                                    onclick="showStockModal(<?= htmlspecialchars(json_encode($item)) ?>)">
                                                Update Stock
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                                <?php if (empty($inventory)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No inventory items found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- recent transactions -->
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Recent Transactions</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>Updated By</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentTransactions as $transaction): ?>
                                    <tr>
                                        <td><?= date('M j, Y g:i A', strtotime($transaction['created_at'])) ?></td>
                                        <td>
                                            <?= htmlspecialchars($transaction['product_name']) ?>
                                            <?= $transaction['size'] ? '(' . htmlspecialchars($transaction['size']) . ')' : '' ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?=
                                                $transaction['type'] === 'restock' ? 'success' :
                                                ($transaction['type'] === 'sale' ? 'primary' :
                                                ($transaction['type'] === 'return' ? 'info' : 'warning'))
                                            ?>">
                                                <?= ucfirst($transaction['type']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($transaction['type'] === 'restock' || $transaction['type'] === 'return'): ?>
                                                <span class="text-success">+<?= $transaction['quantity'] ?></span>
                                            <?php else: ?>
                                                <span class="text-danger">-<?= abs($transaction['quantity']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($transaction['user_name']) ?></td>
                                        <td><?= htmlspecialchars($transaction['notes'] ?? '') ?></td>
                                    </tr>
                                <?php endforeach; ?>

                                <?php if (empty($recentTransactions)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No recent transactions.</td>
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

<!-- stock update -->
<div class="modal fade" id="stockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="stockForm">
                    <input type="hidden" id="inventoryId" name="inventory_id">

                    <div class="mb-3">
                        <label class="form-label">Product</label>
                        <p id="modalProductName" class="form-control-static"></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Current Stock</label>
                        <p id="modalCurrentStock" class="form-control-static"></p>
                    </div>

                    <div class="mb-3">
                        <label for="transactionType" class="form-label">Transaction Type</label>
                        <select class="form-select" id="transactionType" name="type" required>
                            <option value="restock">Restock</option>
                            <option value="adjustment">Adjustment</option>
                            <option value="return">Return</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" required min="1">
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateStock()">Update Stock</button>
            </div>
        </div>
    </div>
</div>

<script>
let stockModal;
let currentItem;

document.addEventListener('DOMContentLoaded', function() {
    stockModal = new bootstrap.Modal(document.getElementById('stockModal'));
});

function showStockModal(item) {
    currentItem = item;
    document.getElementById('inventoryId').value = item.inventory_id;
    document.getElementById('modalProductName').textContent = item.product_name + (item.size ? ` (${item.size})` : '');
    document.getElementById('modalCurrentStock').textContent = item.quantity;
    document.getElementById('quantity').value = '';
    document.getElementById('notes').value = '';
    stockModal.show();
}

function updateStock() {
    const form = document.getElementById('stockForm');
    const formData = new FormData(form);

    fetch('update_inventory.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred while updating stock.'
        });
    });
}
</script>

<?php include 'templates/footer.php'; ?>
