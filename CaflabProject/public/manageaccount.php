<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

// check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// get details of the user
$stmt = $pdo->prepare("SELECT * FROM Users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// get the users orders
$stmt = $pdo->prepare("
    SELECT o.*, COUNT(oi.order_item_id) as item_count 
    FROM Orders o 
    LEFT JOIN Order_Items oi ON o.order_id = oi.order_id 
    WHERE o.user_id = ? 
    GROUP BY o.order_id 
    ORDER BY o.order_date DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Account - Caf Lab</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/manageaccount.css">
    <link rel="stylesheet" href="css/reviews.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <?php include 'basket_include.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <img src="/Team-Project-25/assets/images/profile.png" alt="Profile" class="profile-image me-4">
                            <h2 class="mb-0">Welcome, <?php echo htmlspecialchars($user['name']); ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <nav class="account-nav">
                    <button class="nav-button active" data-section="personal-info">
                        <i class="fas fa-user"></i> Personal Information
                    </button>
                    <button class="nav-button" data-section="password">
                        <i class="fas fa-lock"></i> Change Password
                    </button>
                    <button class="nav-button" data-section="orders">
                        <i class="fas fa-shopping-bag"></i> Order History
                    </button>
                    <button class="nav-button" data-section="returns">
                        <i class="fas fa-undo"></i> Returns
                    </button>
                    <button class="nav-button" data-section="reviews">
                        <i class="fas fa-star"></i> Reviews
                    </button>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
            <!-- Personal information section -->
            <section id="personal-info" class="account-section active">
                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="mb-0"><i class="fas fa-user"></i> Personal Information</h2>
                    </div>
                    <div class="card-body">
                        <form id="personal-info-form" class="account-form">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="address" class="form-label">Address</label>
                                        <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['address_line'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="postcode" class="form-label">Postcode</label>
                                        <input type="text" class="form-control" id="postcode" name="postcode" value="<?php echo htmlspecialchars($user['postcode'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <!-- Password change section -->
            <section id="password" class="account-section">
                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="mb-0"><i class="fas fa-lock"></i> Change Password</h2>
                    </div>
                    <div class="card-body">
                        <form id="password-form" class="account-form">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="current-password" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="current-password" name="current_password" autocomplete="current-password" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="new-password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new-password" name="new_password" autocomplete="new-password" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="confirm-password" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm-password" name="confirm_password" autocomplete="new-password" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Change Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <!-- Order history section -->
            <section id="orders" class="account-section">
                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="mb-0"><i class="fas fa-shopping-bag"></i> Order History</h2>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (empty($orders)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">You haven't placed any orders yet.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>#<?php echo $order['order_id']; ?></td>
                                            <td><?php echo date('d M Y', strtotime($order['order_date'])); ?></td>
                                            <td><?php echo $order['item_count']; ?></td>
                                            <td>£<?php echo number_format($order['total_price'], 2); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo strtolower($order['status']); ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-info btn-sm view-order-btn" data-order-id="<?php echo $order['order_id']; ?>">
                                                    View Details
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Returns section -->
            <section id="returns" class="account-section">
                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="mb-0"><i class="fas fa-undo"></i> Returns</h2>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-4">
                            <h3>Return Policy</h3>
                            <p>Items can be returned within 30 days of delivery. Please ensure items are unused and in their original packaging.</p>
                        </div>
                        <form id="returns-form" class="account-form">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="order-select" class="form-label">Select Order</label>
                                        <select class="form-select" id="order-select" name="order_id" required>
                                            <option value="">Choose an order</option>
                                            <?php foreach ($orders as $order): ?>
                                                <?php if (strtotime($order['order_date']) > strtotime('-30 days')): ?>
                                                    <option value="<?php echo $order['order_id']; ?>">
                                                        Order #<?php echo $order['order_id']; ?> (<?php echo date('d M Y', strtotime($order['order_date'])); ?>)
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Order items section (populated thorugh JavaScript) -->
                            <div id="order-items" class="row g-3 mt-3" style="display: none;">
                                <div class="col-12">
                                    <h4>Select Items to Return</h4>
                                    <div id="return-items-list" class="list-group mb-3">
                                        <!-- items will be populated here -->
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="return-reason" class="form-label">Reason for Return</label>
                                        <select class="form-select" id="return-reason" name="reason" required>
                                            <option value="">Select a reason</option>
                                            <option value="wrong_item">Wrong item received</option>
                                            <option value="defective">Item is defective</option>
                                            <option value="not_as_described">Item not as described</option>
                                            <option value="no_longer_needed">No longer needed</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="return-comments" class="form-label">Additional Comments</label>
                                        <textarea class="form-control" id="return-comments" name="comments" rows="4"></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary" id="submit-return" style="display: none;">
                                    Submit Return Request
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <!-- Reviews section -->
            <section id="reviews" class="account-section">
                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="mb-0"><i class="fas fa-star"></i> Reviews</h2>
                    </div>
                    <div class="card-body">
                        <div class="reviews-tabs">
                            <button class="review-tab active" data-review-type="product">
                                Product Reviews
                            </button>
                            <button class="review-tab" data-review-type="service">
                                Service Review
                            </button>
                        </div>

                        <!-- Product reviews -->
                        <div id="product-reviews" class="review-content active">
                            <div class="alert alert-info mb-4">
                                <h3>Product Reviews</h3>
                                <p>Share your experience with products you've purchased. Your reviews help other customers make informed decisions.</p>
                            </div>
                            <div id="product-review-list">
                                <!-- Products will be loaded here -->
                            </div>
                        </div>

                        <!-- Service review -->
                        <div id="service-review" class="review-content">
                            <div class="alert alert-info mb-4">
                                <h3>Website Service Review</h3>
                                <p>Help us improve by sharing your experience with our website and services.</p>
                            </div>
                            <form id="service-review-form" class="review-form">
                                <div class="service-review-aspects">
                                    <div class="aspect-rating">
                                        <h4>Website Usability</h4>
                                        <div class="star-rating" data-aspect="usability">
                                            <input type="radio" id="usability-5" name="usability" value="5">
                                            <label for="usability-5"><i class="fas fa-star"></i></label>
                                            <input type="radio" id="usability-4" name="usability" value="4">
                                            <label for="usability-4"><i class="fas fa-star"></i></label>
                                            <input type="radio" id="usability-3" name="usability" value="3">
                                            <label for="usability-3"><i class="fas fa-star"></i></label>
                                            <input type="radio" id="usability-2" name="usability" value="2">
                                            <label for="usability-2"><i class="fas fa-star"></i></label>
                                            <input type="radio" id="usability-1" name="usability" value="1">
                                            <label for="usability-1"><i class="fas fa-star"></i></label>
                                        </div>
                                    </div>

                                    <div class="aspect-rating">
                                        <h4>Delivery Service</h4>
                                        <div class="star-rating" data-aspect="delivery">
                                            <input type="radio" id="delivery-5" name="delivery" value="5">
                                            <label for="delivery-5"><i class="fas fa-star"></i></label>
                                            <input type="radio" id="delivery-4" name="delivery" value="4">
                                            <label for="delivery-4"><i class="fas fa-star"></i></label>
                                            <input type="radio" id="delivery-3" name="delivery" value="3">
                                            <label for="delivery-3"><i class="fas fa-star"></i></label>
                                            <input type="radio" id="delivery-2" name="delivery" value="2">
                                            <label for="delivery-2"><i class="fas fa-star"></i></label>
                                            <input type="radio" id="delivery-1" name="delivery" value="1">
                                            <label for="delivery-1"><i class="fas fa-star"></i></label>
                                        </div>
                                    </div>

                                    <div class="aspect-rating">
                                        <h4>Customer Support</h4>
                                        <div class="star-rating" data-aspect="support">
                                            <input type="radio" id="support-5" name="support" value="5">
                                            <label for="support-5"><i class="fas fa-star"></i></label>
                                            <input type="radio" id="support-4" name="support" value="4">
                                            <label for="support-4"><i class="fas fa-star"></i></label>
                                            <input type="radio" id="support-3" name="support" value="3">
                                            <label for="support-3"><i class="fas fa-star"></i></label>
                                            <input type="radio" id="support-2" name="support" value="2">
                                            <label for="support-2"><i class="fas fa-star"></i></label>
                                            <input type="radio" id="support-1" name="support" value="1">
                                            <label for="support-1"><i class="fas fa-star"></i></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="review-form-group">
                                    <label for="service-review-text">Additional Comments</label>
                                    <textarea id="service-review-text" name="review_text" class="review-textarea" 
                                            placeholder="Share your overall experience with our website and services..." required></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">Submit Service Review</button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<footer>
    <p>© 2024 CAF LAB Coffee Company. All Rights Reserved. Ecommerce software by Team Expert 25</p>
</footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // check URL parameters for section and order_id
            const urlParams = new URLSearchParams(window.location.search);
            const section = urlParams.get('section');
            const orderId = urlParams.get('order_id');

            // tab navigation
            const navButtons = document.querySelectorAll('.nav-button');
            const sections = document.querySelectorAll('.account-section');

            function activateSection(sectionId) {
                navButtons.forEach(btn => btn.classList.remove('active'));
                sections.forEach(section => section.classList.remove('active'));
                
                const targetButton = document.querySelector(`.nav-button[data-section="${sectionId}"]`);
                const targetSection = document.getElementById(sectionId);
                
                if (targetButton && targetSection) {
                    targetButton.classList.add('active');
                    targetSection.classList.add('active');

                    // if its the reviews section then load the reviews
                    if (sectionId === 'reviews') {
                        loadProductReviews();
                    }
                }
            }

            // handle click events
            navButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const targetSection = button.dataset.section;
                    activateSection(targetSection);
                });
            });

            // if section parameter exists in URL then activate that section
            if (section) {
                activateSection(section);
            }

            // if order_id exists then scroll to the reviews section
            if (orderId) {
                const reviewsSection = document.getElementById('reviews');
                if (reviewsSection) {
                    reviewsSection.scrollIntoView({ behavior: 'smooth' });
                }
            }

            // personal info form
            document.getElementById('personal-info-form').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                try {
                    const response = await fetch('update_profile.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();
                    
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Your information has been updated.'
                        });
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Failed to update information.'
                    });
                }
            });

            // password change form
            document.getElementById('password-form').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const newPassword = this.new_password.value;
                const confirmPassword = this.confirm_password.value;
                
                if (newPassword !== confirmPassword) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'New passwords do not match.'
                    });
                    return;
                }
                
                const formData = new FormData();
                formData.append('current_password', this.current_password.value);
                formData.append('new_password', this.new_password.value);
                formData.append('confirm_password', this.confirm_password.value);

                try {
                    const response = await fetch('change_password.php', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await response.json();
                    
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Your password has been changed.'
                        });
                        this.reset();
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Failed to change password.'
                    });
                }
            });

            // view order details
            document.querySelectorAll('.view-order-btn').forEach(button => {
                button.addEventListener('click', async function() {
                    const orderId = this.dataset.orderId;
                    try {
                        const response = await fetch(`get_order_details.php?order_id=${orderId}`);
                        let data;
                        try {
                            data = await response.json();
                        } catch (parseError) {
                            console.error('JSON Parse Error:', parseError);
                            throw new Error('Server response was not in the expected format');
                        }

                        if (data.success) {
                            let itemsHtml = data.items.map(item => `
                                <div class="order-item">
                                    <p>${item.name} x ${item.quantity}</p>
                                    <p>£${(item.price * item.quantity).toFixed(2)}</p>
                                </div>
                            `).join('');

                            Swal.fire({
                                title: `Order #${orderId} Details`,
                                html: `
                                    <div class="order-details-popup">
                                        <div class="order-items">
                                            ${itemsHtml}
                                        </div>
                                        <div class="order-total">
                                            <p>Total: £${data.total.toFixed(2)}</p>
                                        </div>
                                    </div>
                                `,
                                width: 600
                            });
                        } else {
                            throw new Error(data.message);
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'Failed to load order details.'
                        });
                    }
                });
            });

            // order select for returns
            document.getElementById('order-select').addEventListener('change', async function() {
                const orderId = this.value;
                const orderItems = document.getElementById('order-items');
                const submitButton = document.getElementById('submit-return');
                const itemsList = document.getElementById('return-items-list');
                
                if (!orderId) {
                    orderItems.style.display = 'none';
                    submitButton.style.display = 'none';
                    return;
                }
                
                try {
                    const response = await fetch(`get_order_details.php?order_id=${orderId}`);
                    const data = await response.json();
                    
                    if (data.success) {
                        itemsList.innerHTML = data.items.map(item => `
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">${item.name}</h6>
                                        <p class="mb-1">Price: £${item.price}</p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <label class="me-2">Qty to Return:</label>
                                        <select class="form-select form-select-sm" style="width: 80px;" 
                                                name="return_items[${item.order_item_id}]">
                                            <option value="0">0</option>
                                            ${Array.from({length: item.quantity}, (_, i) => 
                                                `<option value="${i + 1}">${i + 1}</option>`
                                            ).join('')}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        `).join('');
                        
                        orderItems.style.display = 'block';
                        submitButton.style.display = 'block';
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load order items'
                    });
                }
            });

            // returns form
            document.getElementById('returns-form').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // Get selected items
                const returnItems = {};
                this.querySelectorAll('[name^="return_items"]').forEach(select => {
                    const quantity = parseInt(select.value);
                    if (quantity > 0) {
                        const itemId = select.name.match(/\[(\d+)\]/)[1];
                        returnItems[itemId] = quantity;
                    }
                });
                
                if (Object.keys(returnItems).length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please select at least one item to return'
                    });
                    return;
                }
                
                const formData = new FormData(this);
                formData.append('return_items', JSON.stringify(returnItems));
                
                try {
                    const response = await fetch('submit_return.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();
                    
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Your return request has been submitted.'
                        });
                        this.reset();
                        document.getElementById('order-items').style.display = 'none';
                        document.getElementById('submit-return').style.display = 'none';
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Failed to submit return request.'
                    });
                }
            });

            // reviews Section
            // load reviews when reviews tab is clicked
            navButtons.forEach(button => {
                const originalClick = button.onclick;
                button.onclick = async (e) => {
                    if (button.dataset.section === 'reviews') {
                        await loadProductReviews();
                    }
                    if (originalClick) originalClick.call(button, e);
                };
            });

            // tab switching for reviews
            const reviewTabs = document.querySelectorAll('.review-tab');
            const reviewContents = document.querySelectorAll('.review-content');

            reviewTabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const type = tab.dataset.reviewType;
                    
                    // update active states
                    reviewTabs.forEach(t => t.classList.remove('active'));
                    reviewContents.forEach(c => c.classList.remove('active'));
                    
                    tab.classList.add('active');
                    document.querySelector(`#${type}-reviews`).classList.add('active');

                    if (type === 'product') {
                        loadProductReviews();
                    }
                });
            });

            // load product reviews
            async function loadProductReviews() {
                const productList = document.getElementById('product-review-list');
                productList.innerHTML = '<div class="loading">Loading products...</div>';

                try {
                    const response = await fetch('get_reviews.php?type=product');
                    const data = await response.json();

                    if (data.success) {
                        if (data.products.length === 0) {
                            productList.innerHTML = '<div class="alert alert-info">No products available for review.</div>';
                            return;
                        }

                        productList.innerHTML = data.products.map(product => `
                            <div class="product-review-card">
                                <div class="product-review-header">
                                    <div>
                                        <h3 class="product-review-title">${product.name}</h3>
                                        <span class="verified-purchase">
                                            <i class="fas fa-check-circle"></i> Verified Purchase
                                        </span>
                                    </div>
                                    <div class="product-review-date">
                                        Ordered on ${new Date(product.order_date).toLocaleDateString()}
                                    </div>
                                </div>

                                ${product.review ? `
                                    <div class="existing-review">
                                        <div class="star-rating" data-rating="${product.review.rating}">
                                            ${Array(5).fill().map((_, i) => `
                                                <i class="fas fa-star ${i < product.review.rating ? 'text-warning' : 'text-muted'}"></i>
                                            `).join('')}
                                        </div>
                                        <p class="mt-2">${product.review.review_text}</p>
                                    </div>
                                ` : `
                                    <form class="review-form" data-product-id="${product.product_id}" data-order-id="${product.order_id}">
                                        <div class="star-rating" data-aspect="rating">
                                            ${Array(5).fill().map((_, i) => `
                                                <input type="radio" id="rating-${product.product_id}-${5-i}" name="rating" value="${5-i}">
                                                <label for="rating-${product.product_id}-${5-i}"><i class="fas fa-star"></i></label>
                                            `).join('')}
                                        </div>
                                        <div class="review-form-group">
                                            <textarea class="review-textarea" name="review_text" 
                                                placeholder="Share your thoughts about this product..." required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Submit Review</button>
                                    </form>
                                `}
                            </div>
                        `).join('');

                        // add event listeners to new review forms
                        document.querySelectorAll('.review-form').forEach(form => {
                            form.addEventListener('submit', submitProductReview);
                        });
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    productList.innerHTML = `
                        <div class="alert alert-danger">
                            Failed to load products: ${error.message}
                        </div>
                    `;
                }
            }

            // submit product review
            async function submitProductReview(e) {
                e.preventDefault();
                const form = e.target;
                const productId = form.dataset.productId;
                const orderId = form.dataset.orderId;
                const rating = form.querySelector('input[name="rating"]:checked')?.value;
                const reviewText = form.querySelector('textarea[name="review_text"]').value;

                if (!rating) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please select a rating'
                    });
                    return;
                }

                try {
                    const response = await fetch('submit_review.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            order_id: orderId,
                            rating: rating,
                            review_text: reviewText,
                            review_type: 'product'
                        })
                    });

                    const data = await response.json();
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Your review has been submitted'
                        });
                        loadProductReviews(); // Reload the reviews
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Failed to submit review'
                    });
                }
            }

            // service review form submission
            document.getElementById('service-review-form').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // get most recent completed order
                try {
                    const orderResponse = await fetch('get_user_orders.php?status=completed&limit=1');
                    const orderData = await orderResponse.json();
                    
                    if (!orderData.success || !orderData.orders.length) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'You need to have completed orders to submit a service review'
                        });
                        return;
                    }

                    const orderId = orderData.orders[0].order_id;
                    
                    // validate all ratings
                    const ratings = {};
                    let missingRating = false;
                    
                    ['usability', 'delivery', 'support'].forEach(aspect => {
                        const rating = this.querySelector(`input[name="${aspect}"]:checked`)?.value;
                        if (!rating) {
                            missingRating = true;
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: `Please rate the ${aspect.charAt(0).toUpperCase() + aspect.slice(1)} aspect`
                            });
                        }
                        ratings[aspect] = rating;
                    });

                    if (missingRating) return;

                    const reviewText = this.querySelector('#service-review-text').value;
                    if (!reviewText.trim()) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Please provide your feedback in the comments section'
                        });
                        return;
                    }

                    const response = await fetch('submit_review.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            order_id: orderId,
                            ratings: ratings,
                            review_text: reviewText,
                            review_type: 'service'
                        })
                    });

                    const data = await response.json();
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Your service review has been submitted'
                        });
                        this.reset();
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Failed to submit service review'
                    });
                }
            });

            // load product reviews on page load if reviews section is active
            if (document.querySelector('.nav-button[data-section="reviews"]').classList.contains('active')) {
                loadProductReviews();
            }
        });
    </script>
</body>
</html>
