<?php
require_once 'config.php';
checkAdminAuth();

// for status badge classes
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'pending':
            return 'warning';
        case 'approved':
            return 'success';
        case 'rejected':
            return 'danger';
        default:
            return 'secondary';
    }
}

$conn = getConnection();

// get reviews with user and product details
$stmt = $conn->prepare("
    SELECT 
        r.*,
        u.name as customer_name,
        u.email as customer_email,
        p.name as product_name,
        o.order_id
    FROM Reviews r
    JOIN Users u ON r.user_id = u.user_id
    JOIN Orders o ON r.order_id = o.order_id
    LEFT JOIN Products p ON r.product_id = p.product_id
    ORDER BY r.created_at DESC
");
$stmt->execute();
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// add custom styles and scripts to header
$additionalHead = '
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/reviews.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
';

include 'templates/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <?php include 'templates/admin-menu.php'; ?>
        </div>
        
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Manage Reviews</h2>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary active" data-filter="all">All</button>
                        <button type="button" class="btn btn-outline-primary" data-filter="product">Product Reviews</button>
                        <button type="button" class="btn btn-outline-primary" data-filter="service">Service Reviews</button>
                        <button type="button" class="btn btn-outline-warning" data-filter="pending">Pending</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Type</th>
                                    <th>Product/Service</th>
                                    <th>Rating</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reviews as $review): ?>
                                    <tr class="review-row" data-type="<?= $review['review_type'] ?>" data-status="<?= $review['status'] ?>">
                                        <td><?= date('M j, Y', strtotime($review['created_at'])) ?></td>
                                        <td>
                                            <?= htmlspecialchars($review['customer_name']) ?>
                                            <br>
                                            <small class="text-muted"><?= htmlspecialchars($review['customer_email']) ?></small>
                                        </td>
                                        <td><?= ucfirst($review['review_type']) ?></td>
                                        <td>
                                            <?php if ($review['review_type'] === 'product'): ?>
                                                <?= htmlspecialchars($review['product_name']) ?>
                                            <?php else: ?>
                                                Website Service
                                            <?php endif; ?>
                                            <br>
                                            <small class="text-muted">Order #<?= $review['order_id'] ?></small>
                                        </td>
                                        <td>
                                            <div class="star-rating">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star <?= $i <= $review['rating'] ? 'text-warning' : 'text-muted' ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= getStatusBadgeClass($review['status']) ?>">
                                                <?= ucfirst($review['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info view-review" 
                                                    data-review-id="<?= $review['review_id'] ?>"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#reviewModal">
                                                View Details
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- review -->
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Review Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="reviewDetails"></div>
                <form id="reviewResponseForm" class="mt-4">
                    <input type="hidden" name="review_id" id="reviewIdInput">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <div class="btn-group w-100">
                            <input type="radio" class="btn-check" name="status" value="approved" id="approve">
                            <label class="btn btn-outline-success" for="approve">Approve</label>
                            
                            <input type="radio" class="btn-check" name="status" value="rejected" id="reject">
                            <label class="btn btn-outline-danger" for="reject">Reject</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="adminResponse" class="form-label">Admin Response (Optional)</label>
                        <textarea class="form-control" id="adminResponse" name="admin_response" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveResponse">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // filter buttons
    document.querySelectorAll('[data-filter]').forEach(button => {
        button.addEventListener('click', function() {
            // Update the active state
            document.querySelectorAll('[data-filter]').forEach(btn => {
                btn.classList.remove('active');
            });
            this.classList.add('active');

            // apply filter
            const filter = this.dataset.filter;
            document.querySelectorAll('.review-row').forEach(row => {
                if (filter === 'all') {
                    row.style.display = '';
                } else if (filter === 'pending') {
                    row.style.display = row.dataset.status === 'pending' ? '' : 'none';
                } else {
                    row.style.display = row.dataset.type === filter ? '' : 'none';
                }
            });
        });
    });

    // view review details
    document.querySelectorAll('.view-review').forEach(button => {
        button.addEventListener('click', async function() {
            const reviewId = this.dataset.reviewId;
            try {
                const response = await fetch(`process_review.php?action=get&review_id=${reviewId}`);
                const data = await response.json();
                
                if (data.success) {
                    const review = data.review;
                    document.getElementById('reviewIdInput').value = reviewId;
                    
                    document.getElementById('reviewDetails').innerHTML = `
                        <div class="review-details">
                            <h6>Customer</h6>
                            <p>${review.customer_name} (${review.customer_email})</p>
                            
                            <h6>Review Type</h6>
                            <p>${review.review_type === 'product' ? review.product_name : 'Website Service'}</p>
                            
                            <h6>Rating</h6>
                            <div class="star-rating mb-2">
                                ${Array(5).fill().map((_, i) => `
                                    <i class="fas fa-star ${i < review.rating ? 'text-warning' : 'text-muted'}"></i>
                                `).join('')}
                            </div>
                            
                            <h6>Review Text</h6>
                            <p>${review.review_text}</p>
                            
                            <h6>Date Submitted</h6>
                            <p>${new Date(review.created_at).toLocaleString()}</p>
                        </div>
                    `;

                    // set current status
                    const statusInput = document.querySelector(`input[name="status"][value="${review.status}"]`);
                    if (statusInput) statusInput.checked = true;

                    // set admin response if exists
                    document.getElementById('adminResponse').value = review.admin_response || '';
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    });

    // save response
    document.getElementById('saveResponse').addEventListener('click', async function() {
        const form = document.getElementById('reviewResponseForm');
        const formData = new FormData(form);
        const reviewId = formData.get('review_id');
        const status = formData.get('status');
        
        if (!status) {
            alert('Please select a status');
            return;
        }

        try {
            const response = await fetch('process_review.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    action: 'update',
                    review_id: reviewId,
                    status: status,
                    admin_response: formData.get('admin_response')
                })
            });

            const data = await response.json();
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Failed to update review');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while updating the review');
        }
    });
});

function getStatusBadgeClass(status) {
    switch (status) {
        case 'pending':
            return 'warning';
        case 'approved':
            return 'success';
        case 'rejected':
            return 'danger';
        default:
            return 'secondary';
    }
}
</script>

<?php include 'templates/footer.php'; ?>
