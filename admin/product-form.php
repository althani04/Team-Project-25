<?php
require_once 'config.php';
checkAdminAuth();

$conn = getConnection();
$product = null;
$error = null;
$success = null;

// get categories for the form
$stmt = $conn->query("SELECT * FROM Category ORDER BY name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $category_id = $_POST['category_id'];
        $size = $_POST['size'];
        $units = $_POST['units']; // get units from form

        // handle image upload
        $image_url = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../assets/images/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($fileExtension, $allowedExtensions)) {
                throw new Exception('Invalid file type. Only JPG, PNG and GIF files are allowed.');
            }

            // generate a unique filename
            $image_url = uniqid('product_') . '.' . $fileExtension;
            $uploadPath = $uploadDir . $image_url;

            // check if file already exists and remove it
            if (file_exists($uploadPath)) {
                unlink($uploadPath);
            }

            // move the uploaded file
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                throw new Exception('Failed to upload image. Please check directory permissions.');
            }

            // if updating then remove old image
            if (isset($_POST['id']) && isset($product['image_url'])) {
                $oldImagePath = $uploadDir . $product['image_url'];
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
        }

        if (isset($_POST['id'])) {
            // update existing product
            $stmt = $conn->prepare("
                UPDATE Products 
                SET name = ?, description = ?, price = ?, category_id = ?, size = ?
                " . ($image_url ? ", image_url = ?" : "") . "
                WHERE product_id = ?
            ");
            
            $params = [$name, $description, $price, $category_id, $size];
            if ($image_url) {
                $params[] = $image_url;
            }
            $params[] = $_POST['id'];

            $stmt->execute($params);
            $success = 'Product updated successfully';

        } else {
            // create new product
            $stmt = $conn->prepare("
                INSERT INTO Products (name, description, price, stock_level, category_id, size, image_url)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([$name, $description, $price, 'in stock', $category_id, $size, $image_url]); // default stock_level to 'in stock'
            $productId = $conn->lastInsertId();

            // create inventory entry
            $stmt = $conn->prepare("
                INSERT INTO Inventory (
                    product_id, size, quantity, low_stock_threshold,
                    created_at, updated_at
                ) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
            ");

            $initialQuantity = isset($_POST['units']) ? max(0, intval($_POST['units'])) : 10; // Default to 10 if not provided or invalid

            $stmt->execute([
                $productId,
                $size,
                $initialQuantity,
                5 // default low stock threshold
            ]);

            // record initial stock transaction if quantity > 0
            if ($initialQuantity > 0) {
                $stmt = $conn->prepare("
                    INSERT INTO Inventory_Transactions (
                        inventory_id, type, quantity, previous_quantity,
                        new_quantity, notes, created_by
                    ) VALUES (?, 'restock', ?, 0, ?, 'Initial stock', ?)
                ");
                $inventoryId = $conn->lastInsertId(); // get inventory_id after insert
                $stmt->execute([
                    $inventoryId,
                    $initialQuantity,
                    $initialQuantity,
                    $_SESSION['user_id']
                ]);
            }

            $success = 'Product created successfully with initial stock';
        }

        header('Location: products.php?success=' . urlencode($success));
        exit;

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// get product data if editing
if (isset($_GET['id'])) {
    $stmt = $conn->prepare("SELECT * FROM Products WHERE product_id = ?");
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        header('Location: products.php');
        exit;
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
                    <a href="orders.php" class="list-group-item list-group-item-action">Orders</a>
                    <a href="customers.php" class="list-group-item list-group-item-action">Customers</a>
                    <a href="categories.php" class="list-group-item list-group-item-action">Categories</a>
                    <a href="messages.php" class="list-group-item list-group-item-action">Messages</a>
                    <a href="reviews.php" class="list-group-item list-group-item-action">Reviews</a>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><?= $product ? 'Edit' : 'Add' ?> Product</h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form action="product-form.php" method="POST" enctype="multipart/form-data">
                        <?php if ($product): ?>
                            <input type="hidden" name="id" value="<?= $product['product_id'] ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                   value="<?= htmlspecialchars($product['name'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-control" id="category" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['category_id'] ?>"
                                            <?= ($product['category_id'] ?? '') == $category['category_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">£</span>
                                        <input type="number" class="form-control" id="price" name="price"
                                               step="0.01" min="0"
                                               value="<?= htmlspecialchars($product['price'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if (!$product): ?>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="units" class="form-label">Units</label>
                                    <input type="number" class="form-control" id="units" name="units" 
                                           value="<?= htmlspecialchars('10') ?>" required min="0">
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="col-md-<?= $product ? '8' : '4' ?>">
                                <div class="mb-3">
                                    <label for="size" class="form-label">Size</label>
                                    <input type="text" class="form-control" id="size" name="size"
                                           value="<?= htmlspecialchars($product['size'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Product Image</label>
                            <?php if (isset($product['image_url']) && $product['image_url']): ?>
                                <div class="mb-2">
                                    <img src="<?= ASSETS_PATH ?>/images/<?= htmlspecialchars($product['image_url']) ?>"
                                         alt="Current product image" class="img-thumbnail"
                                         style="width: 200px; height: 200px; object-fit: cover;">
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*"
                                   <?= !$product ? 'required' : '' ?>>
                            <?php if ($product): ?>
                                <small class="text-muted">Leave empty to keep current image</small>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="products.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <?= $product ? 'Update' : 'Create' ?> Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
