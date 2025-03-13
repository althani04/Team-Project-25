<?php
require_once 'config.php';
checkAdminAuth();

$conn = getConnection();
$error = null;
$success = null;

// handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        $reviewId = $_GET['id'];
        
        $stmt = $conn->prepare("DELETE FROM Reviews WHERE review_id = ?");
        $stmt->execute([$reviewId]);
        
        header('Location: reviews.php?success=Review deleted successfully');
        exit;
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// get filter parameters
$rating = isset($_GET['rating']) ? (int)$_GET['rating'] : 0;
$productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$websiteUsabilityRating = isset($_GET['website_usability_rating']) ? (int)$_GET['website_usability_rating'] : 0;
$deliveryServiceRating = isset($_GET['delivery_service_rating']) ? (int)$_GET['delivery_service_rating'] : 0;
$customerSupportRating = isset($_GET['customer_support_rating']) ? (int)$_GET['customer_support_rating'] : 0;


// get all products for filter
$stmt = $conn->query("SELECT product_id, name FROM Products ORDER BY name");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// determine current tab and set review type filter
$currentTab = isset($_GET['tab']) ? $_GET['tab'] : 'service-reviews';
$reviewTypeFilter = '';
if ($currentTab === 'product-reviews') {
    $reviewTypeFilter = " AND r.review_type = 'product'";
} elseif ($currentTab === 'service-reviews') {
    $reviewTypeFilter = " AND r.review_type = 'service'";
}

// build query
$query = "
    SELECT r.*, u.name as username,
           CASE
               WHEN r.product_id IS NOT NULL THEN p.name
               ELSE 'Service Review'
           END as product_name,
           r.review_type,
           r.website_usability_rating,
           r.delivery_service_rating,
           r.customer_support_rating
    FROM Reviews r
    JOIN Users u ON r.user_id = u.user_id
    LEFT JOIN Products p ON r.product_id = p.product_id
    WHERE 1=1" . $reviewTypeFilter;

$params = [];

if ($rating > 0) {
    $query .= " AND r.rating = ?";
    $params[] = $rating;
}

if ($productId > 0) {
    $query .= " AND r.product_id = ?";
    $params[] = $productId;
}
if ($websiteUsabilityRating > 0) {
    $query .= " AND r.website_usability_rating = ?";
    $params[] = $websiteUsabilityRating;
}

if ($deliveryServiceRating > 0) {
    $query .= " AND r.delivery_service_rating = ?";
    $params[] = $deliveryServiceRating;
}

if ($customerSupportRating > 0) {
    $query .= " AND r.customer_support_rating = ?";
    $params[] = $customerSupportRating;
}


$query .= " ORDER BY r.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                        <h4 class="mb-0">Reviews</h4>
                    </div>
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link <?= !isset($_GET['tab']) || $_GET['tab'] === 'service-reviews' ? 'active' : '' ?>" href="?tab=service-reviews">Service Reviews</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= isset($_GET['tab']) && $_GET['tab'] === 'product-reviews' ? 'active' : '' ?>" href="?tab=product-reviews">Product Reviews</a>
                        </li>
                    </ul>
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
                        <input type="hidden" name="tab" value="<?= htmlspecialchars($_GET['tab'] ?? 'service-reviews') ?>">
                        <div class="row g-3">
                            <?php if (!isset($_GET['tab']) || $_GET['tab'] === 'service-reviews'): ?>
                                <div class="col-md-4">
                                    <label class="form-label">Website Usability Rating</label>
                                    <select name="website_usability_rating" class="form-select">
                                        <option value="">All</option>
                                        <?php for ($i = 5; $i >= 1; $i--) : ?>
                                            <option value="<?= $i ?>" <?= isset($_GET['website_usability_rating']) && $_GET['website_usability_rating'] == $i ? 'selected' : '' ?>><?= $i ?> Stars</option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Delivery Service Rating</label>
                                    <select name="delivery_service_rating" class="form-select">
                                        <option value="">All</option>
                                        <?php for ($i = 5; $i >= 1; $i--) : ?>
                                            <option value="<?= $i ?>" <?= isset($_GET['delivery_service_rating']) && $_GET['delivery_service_rating'] == $i ? 'selected' : '' ?>><?= $i ?> Stars</option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Customer Support Rating</label>
                                    <select name="customer_support_rating" class="form-select">
                                        <option value="">All</option>
                                        <?php for ($i = 5; $i >= 1; $i--) : ?>
                                            <option value="<?= $i ?>" <?= isset($_GET['customer_support_rating']) && $_GET['customer_support_rating'] == $i ? 'selected' : '' ?>><?= $i ?> Stars</option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($_GET['tab']) && $_GET['tab'] === 'product-reviews') : ?>
                                <div class="col-md-4">
                                    <label class="form-label">Rating</label>
                                    <select name="rating" class="form-select">
                                        <option value="">All Ratings</option>
                                        <?php for ($i = 5; $i >= 1; $i--) : ?>
                                            <option value="<?= $i ?>" <?= $rating === $i ? 'selected' : '' ?>>
                                                <?= $i ?> Star<?= $i > 1 ? 's' : '' ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Product</label>
                                    <select name="product_id" class="form-select">
                                        <option value="">All Products</option>
                                        <?php foreach ($products as $product) : ?>
                                            <option value="<?= $product['product_id'] ?>" <?= $productId === $product['product_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($product['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>
                            <div class="col-md-4" style="display:none;">
                                
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter me-1"></i> Filter
                                    </button>
                                    <a href="reviews.php<?php if (isset($_GET['tab'])) : ?>?tab=<?= htmlspecialchars($_GET['tab']) ?><?php endif; ?>" class="btn btn-secondary">
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
                                    <th>Review Type</th>
                                    <th>Product</th>
                                    <th>Customer</th>
                                    <th>Rating</th>
                                    <?php if (!isset($_GET['tab']) || $_GET['tab'] === 'service-reviews'): ?>
                                        <th>Website Usability</th>
                                        <th>Delivery Service</th>
                                        <th>Customer Support</th>
                                    <?php endif; ?>
                                    <th>Review</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reviews as $review): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($review['review_type']) ?></td>
                                        <td><?= htmlspecialchars($review['product_name']) ?></td>
                                        <td>
                                            <a href="customer-details.php?id=<?= $review['user_id'] ?>" class="text-decoration-none">
                                                <?= htmlspecialchars($review['username']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="text-warning">
                                                <?php for ($i = 0; $i < 5; $i++): ?>
                                                    <i class="fas fa-star<?= $i >= $review['rating'] ? '-o' : '' ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </td>
                                        <?php if (!isset($_GET['tab']) || $_GET['tab'] === 'service-reviews'): ?>
                                            <td>
                                                <div class="text-warning">
                                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                                        <i class="fas fa-star<?= $i >= $review['website_usability_rating'] ? '-o' : '' ?>"></i>
                                                    <?php endfor; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-warning">
                                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                                        <i class="fas fa-star<?= $i >= $review['delivery_service_rating'] ? '-o' : '' ?>"></i>
                                                    <?php endfor; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-warning">
                                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                                        <i class="fas fa-star<?= $i >= $review['customer_support_rating'] ? '-o' : '' ?>"></i>
                                                    <?php endfor; ?>
                                                </div>
                                            </td>
                                        <?php endif; ?>
                                        <td><?= htmlspecialchars($review['review_text']) ?></td>
                                        <td><?= date('M j, Y', strtotime($review['created_at'])) ?></td>
                                        <td>
                                            <button type="button"
                                                    class="btn btn-sm btn-danger delete-review"
                                                    data-id="<?= $review['review_id'] ?>"
                                                    data-product="<?= htmlspecialchars($review['product_name']) ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                                <?php if (empty($reviews)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No reviews found.</td>
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
    document.querySelectorAll('.delete-review').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const product = this.dataset.product;
            
            Swal.fire({
                title: 'Delete Review?',
                html: `Are you sure you want to delete the review for:<br><strong>${product}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `reviews.php?action=delete&id=${id}`;
                }
            });
        });
    });
});
</script>

<?php include 'templates/footer.php'; ?>
