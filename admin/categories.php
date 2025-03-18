<?php
require_once 'config.php';
checkAdminAuth();

$conn = getConnection();
$error = null;
$success = null;

// handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        $categoryId = $_GET['id'];
        
        // check if category has products
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Products WHERE category_id = ?");
        $stmt->execute([$categoryId]);
        $productCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($productCount > 0) {
            throw new Exception('Cannot delete category. Please remove or reassign all products first.');
        }
        
        // delete the category
        $stmt = $conn->prepare("DELETE FROM Category WHERE category_id = ?");
        $stmt->execute([$categoryId]);
        
        header('Location: categories.php?success=Category deleted successfully');
        exit;
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = trim($_POST['name']);
        
        if (empty($name)) {
            throw new Exception('Category name is required.');
        }
        
        if (isset($_POST['id'])) {
            // update existing category
            $stmt = $conn->prepare("UPDATE Category SET name = ? WHERE category_id = ?");
            $stmt->execute([$name, $_POST['id']]);
            $success = 'Category updated successfully';
        } else {
            // create new category
            $stmt = $conn->prepare("INSERT INTO Category (name) VALUES (?)");
            $stmt->execute([$name]);
            $success = 'Category created successfully';
        }
        
        header('Location: categories.php?success=' . urlencode($success));
        exit;
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// get all categories with product counts
$stmt = $conn->query("
    SELECT c.*, COUNT(p.product_id) as product_count 
    FROM Category c 
    LEFT JOIN Products p ON c.category_id = p.category_id 
    GROUP BY c.category_id 
    ORDER BY c.name
");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'templates/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <?php include 'templates/admin-menu.php'; ?>
        </div>
        
        <div class="col-md-10">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Add/Edit Category</h4>
                    <a href="category-form.php" class="btn btn-primary">Add Category</a>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
                    <?php endif; ?>
                    
                    <form action="categories.php" method="POST" id="categoryForm">
                        <input type="hidden" name="id" id="categoryId">
                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Category</button>
                        <button type="button" class="btn btn-secondary" onclick="resetForm()">Clear</button>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Categories</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Products</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($category['name']) ?></td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?= $category['product_count'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-primary"
                                                        onclick="editCategory(<?= htmlspecialchars(json_encode($category)) ?>)">
                                                    Edit
                                                </button>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger"
                                                            onclick="deleteCategory(<?= $category['category_id'] ?>, '<?= htmlspecialchars($category['name']) ?>')">
                                                        Delete
                                                    </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                
                                <?php if (empty($categories)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No categories found.</td>
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
function editCategory(category) {
    document.getElementById('categoryId').value = category.category_id;
    document.getElementById('name').value = category.name;
}

function resetForm() {
    document.getElementById('categoryForm').reset();
    document.getElementById('categoryId').value = '';
}

function deleteCategory(id, name) {
    Swal.fire({
        title: 'Delete Category?',
        html: `Are you sure you want to delete <strong>${name}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `categories.php?action=delete&id=${id}`;
        }
    });
}
</script>

<?php include 'templates/footer.php'; ?>
