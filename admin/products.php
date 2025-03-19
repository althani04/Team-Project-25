<?php
require_once 'config.php';
checkAdminAuth();

$conn = getConnection();
$error = null;
$success = null;

// get categories for the form
$stmt = $conn->query("SELECT * FROM Category ORDER BY name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        $conn->beginTransaction();
        
        $productId = $_GET['id'];
        
        // deletee related reviews
        $stmt = $conn->prepare("DELETE FROM Reviews WHERE product_id = ?");
        $stmt->execute([$productId]);
        
        // de;lete related basket items
        $stmt = $conn->prepare("DELETE FROM Basket WHERE product_id = ?");
        $stmt->execute([$productId]);
        
        // delete related order items
        $stmt = $conn->prepare("DELETE FROM Order_Items WHERE product_id = ?");
        $stmt->execute([$productId]);
        
        // finally delete the product
        $stmt = $conn->prepare("DELETE FROM Products WHERE product_id = ?");
        $stmt->execute([$productId]);
        
        $conn->commit();
        header('Location: products.php?success=Product deleted successfully');
        exit;
        
    } catch (Exception $e) {
        $conn->rollBack();
        $error = 'Failed to delete product. Please ensure there are no active orders for this product.';
    }
}

// Determine sorting parameters
$sort_by = $_GET['sort_by'] ?? 'name'; // Default sort by name
$sort_order = $_GET['sort_order'] ?? 'asc'; // Default ascending order

// Validate sort parameters to prevent SQL injection
$allowed_sort_columns = ['name', 'price'];
$allowed_sort_orders = ['asc', 'desc'];

$sort_column = in_array($sort_by, $allowed_sort_columns) ? $sort_by : 'name';
$order = in_array($sort_order, $allowed_sort_orders) ? $sort_order : 'asc';

// Construct ORDER BY clause
$order_by_clause = "ORDER BY p." . $sort_column . " " . $order;

// get products with category names
$sql = "SELECT p.*, c.name as category_name, COALESCE(i.quantity, 0) as quantity
    FROM Products p
    LEFT JOIN Category c ON p.category_id = c.category_id
    LEFT JOIN Inventory i ON p.product_id = i.product_id
    " . $order_by_clause;

$stmt = $conn->query($sql);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                        <h4 class="mb-0">Products</h4>
                        <a href="product-form.php" class="btn btn-primary">Add Product</a>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
                    <?php endif; ?>

                    <div class="mb-3 d-flex justify-content-end">
                        <form method="get" class="row g-2 align-items-center">
                            <div class="col-auto">
                                <label for="sort_by" class="col-form-label">Sort by:</label>
                            </div>
                            <div class="col-auto">
                                <select class="form-select" id="sort_by" name="sort_by">
                                    <option value="name">Name</option>
                                    <option value="price">Price</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <label for="sort_order" class="col-form-label">Order:</label>
                            </div>
                            <div class="col-auto">
                                <select class="form-select" id="sort_order" name="sort_order">
                                    <option value="asc">Ascending</option>
                                    <option value="desc">Descending</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">Sort</button>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                     <th>Category</th>
                                     <th>Price</th>
                                    <th>Size</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td>
                                            <?php if ($product['image_url']): ?>
                                                <img src="<?= ASSETS_PATH ?>/images/<?= htmlspecialchars($product['image_url']) ?>"
                                                     alt="<?= htmlspecialchars($product['name']) ?>"
                                                     class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light text-center" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($product['name']) ?></td>
                                        <td><?= htmlspecialchars($product['category_name'] ?? 'Uncategorized') ?></td>
                                        <td>£<?= number_format($product['price'], 2) ?></td>
                                        <td><?= htmlspecialchars($product['size'] ?? 'N/A') ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="product-form.php?id=<?= $product['product_id'] ?>" 
                                                   class="btn btn-sm btn-primary">
                                                    Edit
                                                </a>
                                                <button type="button"
                                                        class="btn btn-sm btn-danger delete-product" 
                                                        data-id="<?= $product['product_id'] ?>"
                                                        data-name="<?= htmlspecialchars($product['name']) ?>">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                
                                <?php if (empty($products)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No products found.</td>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-product').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            
            Swal.fire({
                title: 'Delete Product?',
                html: `Are you sure you want to delete <strong>${name}</strong>?<br>
                       This will also delete all related reviews and order items.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `products.php?action=delete&id=${id}`;
                }
            });
        });
    });
});
</script>

<?php include 'templates/footer.php'; ?>
