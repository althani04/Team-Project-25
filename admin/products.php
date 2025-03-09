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

// get products with category names
$stmt = $conn->query("
    SELECT p.*, c.name as category_name, 
           CASE p.stock_level 
               WHEN 'in stock' THEN 999 
               WHEN 'low stock' THEN 5 
               ELSE 0 
           END as stock
    FROM Products p 
    LEFT JOIN Category c ON p.category_id = c.category_id 
    ORDER BY p.product_id DESC
");
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
                        <a href="product-form.php" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Add Product
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

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
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
                                        <td>
                                            <span class="badge bg-<?= $product['stock'] <= 5 ? 'danger' : 'success' ?>">
                                                <?= $product['stock'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="product-form.php?id=<?= $product['product_id'] ?>" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger delete-product" 
                                                        data-id="<?= $product['product_id'] ?>"
                                                        data-name="<?= htmlspecialchars($product['name']) ?>">
                                                    <i class="fas fa-trash"></i>
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
