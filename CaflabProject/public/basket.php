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
            <div class="loading">Loading...</div>
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

            // price in £
            function formatPrice(price) {
                return `£${parseFloat(price).toFixed(2)}`;
            }

            //  update basket UI for 0 items in the basket
            function updateBasketUI(data) {
                if (!data.basket || data.basket.length === 0) {
                    basketItems.innerHTML = '<div class="empty-basket">Your basket is empty</div>';
                    basketTotal.textContent = formatPrice(0);
                    checkoutButton.disabled = true;
                    return;
                }

                let html = '';
                let total = 0;
                data.basket.forEach(item => {
                    const statusClass = item.status === 'out_of_stock' ? 'out-of-stock' : 
                                     item.status === 'limited_stock' ? 'limited-stock' : '';
                    const statusMessage = item.message ? `<div class="status-message">${item.message}</div>` : '';
                    const subtotal = item.price * item.quantity;
                    total += subtotal;

                    html += `
                        <div class="basket-item ${statusClass}" data-id="${item.product_id}">
                            <div class="item-image">
                                <img src="${item.image_url}" alt="${item.name}">
                            </div>
                            <div class="item-details">
                                <div class="item-name">${item.name}</div>
                                <div class="item-price">${formatPrice(item.price)}</div>
                                ${statusMessage}
                                <div class="item-controls">
                                    <button class="quantity-btn minus" ${item.quantity <= 1 ? 'disabled' : ''}>-</button>
                                    <span class="quantity">${item.quantity}</span>
                                    <button class="quantity-btn plus" ${item.status === 'limited_stock' && item.quantity >= 5 ? 'disabled' : ''}>+</button>
                                    <button class="remove-item">Remove</button>
                                </div>
                                <div class="item-subtotal">Subtotal: ${formatPrice(subtotal)}</div>
                            </div>
                        </div>
                    `;
                });

                basketItems.innerHTML = html;

                // animate for total price chnage
                const oldTotal = parseFloat(basketTotal.textContent.replace('£', ''));
                const newTotal = total;
                
                const steps = 20;
                const increment = (newTotal - oldTotal) / steps;
                let currentStep = 0;
                
                const animateTotal = () => {
                    currentStep++;
                    const currentValue = oldTotal + (increment * currentStep);
                    basketTotal.textContent = formatPrice(currentValue);
                    
                    if (currentStep < steps) {
                        requestAnimationFrame(animateTotal);
                    }
                };
                
                requestAnimationFrame(animateTotal);
                checkoutButton.disabled = false;

                // update for basket count in navbar
                const basketCount = document.querySelector('.basket-count');
                if (basketCount) {
                    basketCount.textContent = data.itemCount;
                    basketCount.style.display = data.itemCount > 0 ? 'block' : 'none';
                }
            }

            // fetch the products in the basket
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

            // basket visibility
            function toggleBasket() {
                isBasketOpen = !isBasketOpen;
                basketDropdown.classList.toggle('active', isBasketOpen);
                if (isBasketOpen) {
                    fetchBasket();
                }
            }

            // update for item quantity
            async function updateQuantity(productId, newQuantity) {
                if (newQuantity < 1) {
                    return; 
                }

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

            // removing a product from the basket
            async function removeItem(productId) {
                try {
                    // update ui
                    const itemToRemove = document.querySelector(`.basket-item[data-id="${productId}"]`);
                    if (itemToRemove) {
                        itemToRemove.style.opacity = '0.5';
                    }

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
                        // animate when removing an item
                        if (itemToRemove) {
                            itemToRemove.style.transition = 'all 0.3s ease-out';
                            itemToRemove.style.height = '0';
                            itemToRemove.style.opacity = '0';
                            itemToRemove.style.margin = '0';
                            itemToRemove.style.padding = '0';
                            
                            // update the UI after animation
                            setTimeout(() => {
                                // calculate the new total after removing
                                const remainingItems = document.querySelectorAll('.basket-item:not([style*="height: 0"])');
                                let newTotal = 0;
                                remainingItems.forEach(item => {
                                    const price = parseFloat(item.querySelector('.item-price').textContent.replace('£', ''));
                                    const quantity = parseInt(item.querySelector('.quantity').textContent);
                                    newTotal += price * quantity;
                                });
                                
                                // update the total immediately
                                basketTotal.textContent = formatPrice(newTotal);
                                
                                // then update the full UI
                                updateBasketUI(data);
                            }, 300);
                        } else {
                            updateBasketUI(data);
                        }
                    } else {
                        if (itemToRemove) {
                            itemToRemove.style.opacity = '1';
                        }
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

            // event listeners
            basketIcon.addEventListener('click', (e) => {
                e.preventDefault();
                toggleBasket();
            });

            closeBasket.addEventListener('click', toggleBasket);

            // close the basket when clicking anywhere outside the basket
            document.addEventListener('click', (e) => {
                if (isBasketOpen && 
                    !basketDropdown.contains(e.target) && 
                    !basketIcon.contains(e.target)) {
                    toggleBasket();
                }
            });

            // handle changes for quantity and removing item
            basketItems.addEventListener('click', (e) => {
                const basketItem = e.target.closest('.basket-item');
                if (!basketItem) return;

                const productId = parseInt(basketItem.dataset.id);
                const currentQuantity = parseInt(basketItem.querySelector('.quantity').textContent);

                if (e.target.classList.contains('plus')) {
                    updateQuantity(productId, currentQuantity + 1);
                } else if (e.target.classList.contains('minus') && currentQuantity > 1) {
                    updateQuantity(productId, currentQuantity - 1);
                } else if (e.target.classList.contains('remove-item')) {
                    removeItem(productId);
                }
            });

            // checkout
            checkoutButton.addEventListener('click', () => {
                window.location.href = 'checkout.php';
            });
                        fetchBasket();
        });
    </script>
</body>
</html>
