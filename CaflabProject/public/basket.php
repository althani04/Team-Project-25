<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Basket - Caf Lab</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/basket.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="basket-dropdown" id="basketDropdown">
        <div class="basket-header">
            <h3>Your Basket</h3>
            <button class="close-basket" id="closeBasket">&times;</button>
        </div>
        <div class="basket-items" id="basketItems">
            <div class="loading">Loading your basket...</div>
        </div>
        <div class="basket-footer">
            <div class="basket-total">Total: <span id="basketTotal">£0.00</span></div>
            <button class="checkout-button" id="checkoutButton" disabled>Proceed to Checkout</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const basketDropdown = document.getElementById('basketDropdown');
            const basketIcon = document.querySelector('.checkout img');
            const closeBasket = document.getElementById('closeBasket');
            const basketItems = document.getElementById('basketItems');
            const basketTotal = document.getElementById('basketTotal');
            const checkoutButton = document.getElementById('checkoutButton');
            let isBasketOpen = false;

            // Function to update basket UI
            function updateBasketUI(data) {
                if (!data.basket || data.basket.length === 0) {
                    basketItems.innerHTML = '<div class="empty-basket">Your basket is empty</div>';
                    checkoutButton.disabled = true;
                    return;
                }

                let html = '';
                data.basket.forEach(item => {
                    const statusClass = item.status === 'out_of_stock' ? 'out-of-stock' : 
                                     item.status === 'limited_stock' ? 'limited-stock' : '';
                    const statusMessage = item.message ? `<div class="status-message">${item.message}</div>` : '';

                    html += `
                        <div class="basket-item ${statusClass}" data-id="${item.product_id}">
                            <div class="item-image">
                                <img src="${item.image_url}" alt="${item.name}">
                            </div>
                            <div class="item-details">
                                <div class="item-name">${item.name}</div>
                                <div class="item-price">£${parseFloat(item.price).toFixed(2)}</div>
                                ${statusMessage}
                                <div class="item-controls">
                                    <button class="quantity-btn minus" ${item.quantity <= 1 ? 'disabled' : ''}>-</button>
                                    <span class="quantity">${item.quantity}</span>
                                    <button class="quantity-btn plus" ${item.status === 'limited_stock' && item.quantity >= 5 ? 'disabled' : ''}>+</button>
                                    <button class="remove-item">Remove</button>
                                </div>
                                <div class="item-subtotal">Subtotal: £${item.subtotal.toFixed(2)}</div>
                            </div>
                        </div>
                    `;
                });

                basketItems.innerHTML = html;
                basketTotal.textContent = data.formattedTotal;
                checkoutButton.disabled = false;

                // Update basket count in navbar
                const basketCount = document.querySelector('.basket-count');
                if (basketCount) {
                    basketCount.textContent = data.itemCount;
                    basketCount.style.display = data.itemCount > 0 ? 'block' : 'none';
                }
            }

            // Function to fetch basket contents
            function fetchBasket() {
                fetch('get_basket.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updateBasketUI(data);
                        } else {
                            throw new Error(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching basket:', error);
                        basketItems.innerHTML = '<div class="error">Error loading basket</div>';
                    });
            }

            // Toggle basket visibility
            function toggleBasket() {
                isBasketOpen = !isBasketOpen;
                basketDropdown.classList.toggle('active', isBasketOpen);
                if (isBasketOpen) {
                    fetchBasket();
                }
            }

            // Update item quantity
            async function updateQuantity(productId, newQuantity) {
                try {
                    const response = await fetch('add_to_basket.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            quantity: newQuantity,
                            action: 'update'
                        })
                    });

                    const data = await response.json();
                    if (data.success) {
                        updateBasketUI(data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message
                        });
                    }
                } catch (error) {
                    console.error('Error updating quantity:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to update quantity'
                    });
                }
            }

            // Remove item from basket
            async function removeItem(productId) {
                try {
                    const response = await fetch('add_to_basket.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            quantity: 0,
                            action: 'remove'
                        })
                    });

                    const data = await response.json();
                    if (data.success) {
                        updateBasketUI(data);
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    console.error('Error removing item:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to remove item'
                    });
                }
            }

            // Event Listeners
            basketIcon.addEventListener('click', (e) => {
                e.preventDefault();
                toggleBasket();
            });

            closeBasket.addEventListener('click', toggleBasket);

            // Close basket when clicking outside
            document.addEventListener('click', (e) => {
                if (isBasketOpen && 
                    !basketDropdown.contains(e.target) && 
                    !basketIcon.contains(e.target)) {
                    toggleBasket();
                }
            });

            // Handle quantity changes and item removal
            basketItems.addEventListener('click', (e) => {
                const basketItem = e.target.closest('.basket-item');
                if (!basketItem) return;

                const productId = parseInt(basketItem.dataset.id);
                const currentQuantity = parseInt(basketItem.querySelector('.quantity').textContent);

                if (e.target.classList.contains('plus')) {
                    updateQuantity(productId, currentQuantity + 1);
                } else if (e.target.classList.contains('minus')) {
                    updateQuantity(productId, currentQuantity - 1);
                } else if (e.target.classList.contains('remove-item')) {
                    removeItem(productId);
                }
            });

            // Handle checkout
            checkoutButton.addEventListener('click', () => {
                window.location.href = 'checkout.php';
            });

            // Initial basket fetch
            fetchBasket();
        });
    </script>
</body>
</html>
