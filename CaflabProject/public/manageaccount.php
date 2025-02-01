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
    <link rel="stylesheet" href="css/manageaccount.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <?php include 'basket_include.php'; ?>

    <div class="account-container">
        <div class="account-sidebar">
            <div class="user-info">
                <img src="/Team-Project-25/assets/images/profile.png" alt="Profile" class="profile-image">
                <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?></h2>
            </div>
            <nav class="account-nav">
                <button class="nav-button active" data-section="personal-info">Personal Information</button>
                <button class="nav-button" data-section="password">Change Password</button>
                <button class="nav-button" data-section="orders">Order History</button>
                <button class="nav-button" data-section="returns">Returns</button>
            </nav>
        </div>

        <div class="account-content">
            <!-- Personal Information Section -->
            <section id="personal-info" class="account-section active">
                <h2>Personal Information</h2>
                <form id="personal-info-form" class="account-form">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address_line'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="postcode">Postcode</label>
                        <input type="text" id="postcode" name="postcode" value="<?php echo htmlspecialchars($user['postcode'] ?? ''); ?>">
                    </div>
                    <button type="submit" class="submit-btn">Save Changes</button>
                </form>
            </section>

            <!-- Password Change Section -->
            <section id="password" class="account-section">
                <h2>Change Password</h2>
                <form id="password-form" class="account-form">
                    <div class="form-group">
                        <label for="current-password">Current Password</label>
                        <input type="password" id="current-password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new-password">New Password</label>
                        <input type="password" id="new-password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm-password">Confirm New Password</label>
                        <input type="password" id="confirm-password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="submit-btn">Change Password</button>
                </form>
            </section>

            <!-- Order History Section -->
            <section id="orders" class="account-section">
                <h2>Order History</h2>
                <div class="orders-list">
                    <?php if (empty($orders)): ?>
                        <p class="no-orders">You haven't placed any orders yet.</p>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <div class="order-card">
                                <div class="order-header">
                                    <div class="order-info">
                                        <h3>Order #<?php echo $order['order_id']; ?></h3>
                                        <p>Placed on: <?php echo date('d M Y', strtotime($order['order_date'])); ?></p>
                                    </div>
                                    <div class="order-status">
                                        <span class="status-badge <?php echo strtolower($order['status']); ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="order-details">
                                    <p>Items: <?php echo $order['item_count']; ?></p>
                                    <p>Total: £<?php echo number_format($order['total_price'], 2); ?></p>
                                </div>
                                <button class="view-order-btn" data-order-id="<?php echo $order['order_id']; ?>">
                                    View Details
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Returns Section -->
            <section id="returns" class="account-section">
                <h2>Returns</h2>
                <div class="returns-info">
                    <h3>Return Policy</h3>
                    <p>Items can be returned within 30 days of delivery. Please ensure items are unused and in their original packaging.</p>
                </div>
                <form id="returns-form" class="account-form">
                    <div class="form-group">
                        <label for="order-select">Select Order</label>
                        <select id="order-select" name="order_id" required>
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
                    <div class="form-group">
                        <label for="return-reason">Reason for Return</label>
                        <select id="return-reason" name="reason" required>
                            <option value="">Select a reason</option>
                            <option value="wrong_item">Wrong item received</option>
                            <option value="defective">Item is defective</option>
                            <option value="not_as_described">Item not as described</option>
                            <option value="no_longer_needed">No longer needed</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="return-comments">Additional Comments</label>
                        <textarea id="return-comments" name="comments" rows="4"></textarea>
                    </div>
                    <button type="submit" class="submit-btn">Submit Return Request</button>
                </form>
            </section>
        </div>
    </div>

    <footer>
        <p>© 2024 CAF LAB Coffee Company. All Rights Reserved. Ecommerce software by Team Expert 25</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // tab navigation
            const navButtons = document.querySelectorAll('.nav-button');
            const sections = document.querySelectorAll('.account-section');

            navButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const targetSection = button.dataset.section;
                    
                    // update the active states
                    navButtons.forEach(btn => btn.classList.remove('active'));
                    sections.forEach(section => section.classList.remove('active'));
                    
                    button.classList.add('active');
                    document.getElementById(targetSection).classList.add('active');
                });
            });

            // personal info Form
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

            // password change Form
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
                
                const formData = new FormData(this);
                try {
                    const response = await fetch('change_password.php', {
                        method: 'POST',
                        body: formData
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
                        const data = await response.json();
                        
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

            // returns form
            document.getElementById('returns-form').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
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
        });
    </script>
</body>
</html>
