<link rel="stylesheet" href="css/basket.css">
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

        // price in £
        function formatPrice(price) {
            return `£${parseFloat(price).toFixed(2)}`;
        }

        //  update the basket ui
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
                                <img src="${item.image_url}" alt="${item.name}" onerror="this.src='/Team-Project-25/assets/images/coffeebeans.jpeg'">
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

            // animate the total price changing
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

            // update the basket count in navbar
            const basketCount = document.querySelector('.basket-count');
            if (basketCount) {
                basketCount.textContent = data.itemCount;
                basketCount.style.display = data.itemCount > 0 ? 'block' : 'none';
            }
        }

        //  fetch the contents for the basket
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

        // show basket
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

        // remove an item from the basket
        async function removeItem(productId) {
            try {
                // updating  UI 
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
                    // animation for removing an item 
                    if (itemToRemove) {
                        itemToRemove.style.transition = 'all 0.3s ease-out';
                        itemToRemove.style.height = '0';
                        itemToRemove.style.opacity = '0';
                        itemToRemove.style.margin = '0';
                        itemToRemove.style.padding = '0';
                        
                        // update UI after the animation
                        setTimeout(() => {
                            // Update UI immediately
                            updateBasketUI(data);
                            
                            // force an update for the basket count to 0 if nothing inside the basket
                            const basketCount = document.querySelector('.basket-count');
                            if (basketCount && (!data.basket || data.basket.length === 0)) {
                                basketCount.style.display = 'none';
                                basketCount.textContent = '0';
                                basketTotal.textContent = formatPrice(0);
                            }
                        }, 300);
                    } else {
                        updateBasketUI(data);
                    }
                } else {
                    // revert optimistic UI update if failed
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

        // Event Listeners
        basketIcon.addEventListener('click', (e) => {
            e.preventDefault();
            toggleBasket();
        });

        closeBasket.addEventListener('click', toggleBasket);

        // close basket if clicked anywhere outside the basket
        document.addEventListener('click', (e) => {
            if (isBasketOpen && 
                !basketDropdown.contains(e.target) && 
                !basketIcon.contains(e.target)) {
                toggleBasket();
            }
        });

        // handle chnages in quantity and item removal
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

        // handle checkout
        checkoutButton.addEventListener('click', () => {
            window.location.href = 'checkout.php';
        });

        // basket fetch
        fetchBasket();
    });
</script>
