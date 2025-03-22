<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - Caf Lab</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/orderhistory.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

<?php 
session_start();

// redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// check for order success message
$orderSuccess = isset($_SESSION['order_success']) && $_SESSION['order_success'];
if ($orderSuccess) {
    unset($_SESSION['order_success']); // clear message
}

include 'navbar.php'; 
include 'basket_include.php';
?>

    <div class="container mt-5 mb-5">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h1 class="mb-0">Your Order History</h1>
                    </div>
                    <div class="card-body">
                        <div id="loading" class="text-center py-4" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading orders...</span>
                            </div>
                        </div>
                        
                        <div id="error" class="alert alert-danger" style="display: none;">
                        </div>

                        <ul id="ordersList" class="order-history-list list-unstyled">
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>© 2024 CAF LAB Coffee Company. All Rights Reserved. Ecommerce software by Team Expert 25</p>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

    <script>
        const ordersList = document.getElementById('ordersList');
        const loading = document.getElementById('loading');
        const error = document.getElementById('error');

        // fetch orders when page loads
        document.addEventListener('DOMContentLoaded', fetchOrders);

        async function fetchOrders() {
            loading.style.display = 'block';
            error.style.display = 'none';
            ordersList.innerHTML = '';

            try {
                const response = await fetch('get_user_orders.php');
                let data;
                try {
                    data = await response.json();
                } catch (parseError) {
                    console.error('JSON Parse Error:', parseError);
                    throw new Error('Server response was not in the expected format');
                }

                if (!data.success) {
                    throw new Error(data.message || 'Failed to fetch orders');
                }

                if (data.orders.length === 0) {
                    ordersList.innerHTML = '<p>No orders found.</p>';
                    return;
                }

                data.orders.forEach(order => {
                    const orderDate = new Date(order.date).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });

                    const orderElement = document.createElement('li');
                    orderElement.className = 'order-history-item';
                    
                    let itemsHtml = order.items.map(item => `
                        <div class="order-item">
                            <div class="item-image">
                                <img src="${item.image_url}" alt="${item.name} product image">
                            </div>
                            <div class="item-details">
                                <h5>${item.name}</h5>
                                <p class="item-description">${item.description || ''}</p>
                                <div class="item-price-details">
                                    <span>Quantity: ${item.quantity}</span>
                                    <span>Price: £${item.price}</span>
                                    <span>Subtotal: £${item.subtotal}</span>
                                </div>
                            </div>
                        </div>
                    `).join('');

                    orderElement.innerHTML = `
                        <div class="order-header">
                            <div class="order-info">
                                <h4>Order #${order.id}</h4>
                                <p class="order-date">Ordered on ${orderDate}</p>
                                <p class="status ${order.status.toLowerCase()}">Status: ${order.status}</p>
                            </div>
                            <div class="order-total">
                                <p>Order Total: £${order.total}</p>
                                ${order.status === 'completed' ? 
                                    `<a href="manageaccount.php?section=reviews&order_id=${order.id}" class="review-button">Leave a Review</a>` 
                                    : ''}
                            </div>
                        </div>
                        <div class="order-items">
                            ${itemsHtml}
                        </div>
                    `;
                    ordersList.appendChild(orderElement);
                });

            } catch (err) {
                error.textContent = err.message;
                error.style.display = 'block';
            } finally {
                loading.style.display = 'none';
            }
        }
    </script>
</body>
</html>
