<?php
require_once 'config.php';
checkAdminAuth();

$error = null;

// check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // retrieve the category name from the form data
    $categoryName = $_POST['name'];

    // validate input (check if it's empty)
    if (empty($categoryName)) {
        $error = "Category name cannot be empty.";
    } else {

        try {
            $conn = getConnection(); // from database.php
            // insert the category into the database
            $stmt = $conn->prepare("INSERT INTO Category (name) VALUES (?)");
            $stmt->execute([$categoryName]);

            // success - redirect back to categories.php
            header("Location: categories.php");
            exit(); // important to prevent further execution
        } catch (PDOException $e) {
            // error handling
            $error = "Error adding category: " . $e->getMessage();
        }
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
                    <a href="categories.php" class="list-group-item list-group-item-action active">Categories</a>
                    <a href="messages.php" class="list-group-item list-group-item-action">Messages</a>
                    <a href="reviews.php" class="list-group-item list-group-item-action">Reviews</a>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Add Category</h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name:</label>
                            <input type="text" class="form-control" name="name" id="name" required>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="categories.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Add Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
