<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['return_to'] = 'checkout.php';
}

include 'navbar.php';
include 'basket_include.php';

// initiliase user details
$user = [];
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// get basket items
$basketItems = isset($_SESSION['basket']) ? $_SESSION['basket'] : [];
$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Caf Lab</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/checkout.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<main>
    <div class="checkout-page">
        <!-- Left Section: Order Summary -->
        <div class="product-details">
            <h2>Order Summary</h2>
            <?php if (empty($basketItems)): ?>
                <p>Your basket is empty</p>
            <?php else: ?>
                <?php foreach ($basketItems as $item): 
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                ?>
                    <div class="product-item">
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>" 
                             class="product-image"
                             onerror="this.src='/Team-Project-25/assets/images/coffeebeans.jpeg'">
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p><strong>Quantity:</strong> <?php echo $item['quantity']; ?></p>
                            <p><strong>Price:</strong> £<?php echo number_format($item['price'], 2); ?></p>
                            <p><strong>Subtotal:</strong> £<?php echo number_format($subtotal, 2); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="total-cost">
                    <p>Total Cost: £<?php echo number_format($total, 2); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Section: Shipping Details -->
        <div class="checkout-details">
            <h2>Shipping Details</h2>
            <form id="checkout-form" action="process_order.php" method="POST">
                <input type="hidden" name="total_amount" value="<?php echo $total; ?>">
                
                <div class="name-fields">
                    <div>
                        <label for="first-name">First Name</label>
                        <input type="text" id="first-name" name="first_name" 
                               value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
                    </div>
                </div>

                <div>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                </div>

                <div>
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" 
                           value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>" required>
                </div>

                <div>
                    <label for="address">Delivery Address</label>
                    <input type="text" id="address" name="address" 
                           value="<?php echo htmlspecialchars($user['address_line'] ?? ''); ?>" required>
                </div>

                <div>
                    <label for="postcode">Postcode</label>
                    <input type="text" id="postcode" name="postcode" 
                           value="<?php echo htmlspecialchars($user['postcode'] ?? ''); ?>" required>
                </div>

                <div class="button-group">
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <button type="button" class="checkout-button" onclick="window.location.href='login.php'">Login to Place Order</button>
                    <?php else: ?>
                        <button type="submit" class="checkout-button" <?php echo empty($basketItems) ? 'disabled' : ''; ?>>
                            Place Order
                        </button>
                    <?php endif; ?>
                    
                    <button type="button" class="cancel-button" onclick="window.location.href='products.php'">
                        Continue Shopping
                    </button>
                </div>
                
            </form>
        </div>
    </div>
</main>

<footer>
    <p>© 2024 CAF LAB Coffee Company. All Rights Reserved. Ecommerce software by Team Expert 25</p>
</footer>

<script>
document.getElementById('checkout-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    try {
        // show a confirmation alert after clicking place order
        const result = await Swal.fire({
            title: 'Confirm Order',
            text: 'Are you sure you want to place this order?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Place Order',
            cancelButtonText: 'No, Cancel'
        });

        if (result.isConfirmed) {
            // show the loading state
            Swal.fire({
                title: 'Processing Order',
                text: 'Please wait...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // submit form data
            const formData = new FormData(this);
            const response = await fetch('process_order.php', {
                method: 'POST',
                body: formData
            });

            let data;
            try {
                data = await response.json();
            } catch (parseError) {
                console.error('JSON Parse Error:', parseError);
                throw new Error('Server response was not in the expected format');
            }

            if (data.success) {
                // show a message to say success 
                await Swal.fire({
                    icon: 'success',
                    title: 'Order Placed Successfully!',
                    text: 'Your order has been confirmed.',
                    confirmButtonText: 'View Order History'
                });
                
                // direct to order history page
                window.location.href = 'orderhistory.php';
            } else {
                throw new Error(data.message || 'Failed to place order');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Failed to place order. Please try again.'
        });
    }
});
</script>

</body>
</html>
