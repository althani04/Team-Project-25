<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist - Caf Lab</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/products.css"> 
    <link rel="stylesheet" href="css/basket.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php
    include 'session_check.php';
    include 'navbar.php';
    include 'basket_include.php';
    ?>

    <main class="products-container">
        <h1>Your Wishlist</h1>
        <section class="products-grid" id="wishlist-list">
            <div class="loading">Loading wishlist...</div>
        </section>
    </main>

    <footer>
        <p>© 2024 CAF LAB Coffee Company. All Rights Reserved. Ecommerce software by Team Expert 25</p>
    </footer>

    <script>
        // update the basket count
        function updateBasketCount(count) {
            const basketCount = document.querySelector('.basket-count');
            if (basketCount) {
                basketCount.textContent = count;
                basketCount.style.display = count > 0 ? 'block' : 'none';
            }
        }

        // toggle wishlist icon and update local storage
        function toggleWishlist(icon, productId) {
            let wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
            const index = wishlist.indexOf(productId);
            if (index > -1) {
                wishlist.splice(index, 1);
                icon.innerHTML = '&#x2661;'; // unfilled heart
                icon.classList.remove('filled');
            } else {
                wishlist.push(productId);
                icon.innerHTML = '&#x2665;'; // filled heart
                icon.classList.add('filled');
            }
            localStorage.setItem('wishlist', JSON.stringify(wishlist));
            // refresh the wishlist display
            displayWishlist();
        }

        async function displayWishlist() {
            const wishlistList = document.getElementById('wishlist-list');
            wishlistList.innerHTML = '<div class="loading">Loading wishlist...</div>';

            let wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
            if (wishlist.length === 0) {
                wishlistList.innerHTML = '<div class="no-products">Your wishlist is empty.</div>';
                return;
            }

            try {
                // fetch product details for each product ID in the wishlist
                const response = await fetch('fetch_products.php?productIds=' + wishlist.join(','));
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const data = await response.json();
                const products = data.products;

                wishlistList.innerHTML = ''; // clear loading message
                products.forEach(product => {
                    const card = document.createElement('div');
                    card.className = 'product-card';

                    const imageUrl = product.image_url && product.image_url !== 'N/A' ?
                        product.image_url :
                        '/Team-Project-255/assets/images/coffeebeans.jpeg';

                    const stockClass = product.stock_level === 'out of stock' ? 'out-of-stock' :
                        product.stock_level === 'low stock' ? 'low-stock' : 'in-stock';

                    card.innerHTML = `
                        <div class="product-image-container">
                            <img src="${imageUrl}" alt="${product.name}" class="product-image" onerror="this.src='/Team-Project-255/assets/images/coffeebeans.jpeg'" loading="lazy">
                            <div class="wishlist-icon filled" onclick="toggleWishlist(this, ${product.product_id})">&#x2665;</div>
                            <div class="stock-badge ${stockClass}">${product.stock_level}</div>
                        </div>
                        <div class="product-info">
                            <div class="product-category">${product.category_name}</div>
                            <h3 class="product-title">${product.name}</h3>
                            <p class="product-description">${product.description || 'No description available'}</p>
                            <div class="product-details">
                                <p class="product-price">£${parseFloat(product.price).toFixed(2)}</p>
                                ${product.size ? `<p class="product-size">Size: ${product.size}</p>` : ''}
                            </div>
                            <button class="add-to-cart-btn" 
                                    onclick="addToBasket(${product.product_id})"
                                    ${product.stock_level === 'out of stock' ? 'disabled' : ''}>
                                ${product.stock_level === 'out of stock' ? 'Out of Stock' : 'Add to Cart'}
                            </button>
                            <button class="remove-wishlist-btn" onclick="toggleWishlist(this, ${product.product_id})">Remove from Wishlist</button>
                        </div>
                    `;
                    wishlistList.appendChild(card);
                });
            } catch (error) {
                console.error('Error fetching wishlist products:', error);
                wishlistList.innerHTML = '<div class="error">Failed to load wishlist. Please try again later.</div>';
            }
        }

        // add item to basket
        async function addToBasket(productId) {
            try {
                const response = await fetch('add_to_basket.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: 1,
                        action: 'add'
                    })
                });

                const data = await response.json();
                if (data.success) {
                    // update basket count immediately
                    updateBasketCount(data.itemCount);
                    
                    // trigger the basket update event
                    const basketUpdateEvent = new CustomEvent('basketUpdate', { detail: data });
                    document.dispatchEvent(basketUpdateEvent);
                    
                    // show a message if it was successfull
                    Swal.fire({
                        icon: 'success',
                        title: 'Added to Basket',
                        text: 'Item has been added to your basket',
                        showConfirmButton: false,
                        timer: 1500
                    });

                    // fetch the updated basket data
                    fetch('get_basket.php')
                        .then(response => response.json())
                        .then(basketData => {
                            if (basketData.success) {
                                updateBasketCount(basketData.itemCount);
                            }
                        })
                        .catch(error => console.error('Error fetching basket:', error));
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Error adding to basket:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Failed to add item to basket'
                });
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            displayWishlist();

            // Initial basket count update
            fetch('get_basket.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateBasketCount(data.itemCount);
                    }
                })
                .catch(error => console.error('Error fetching basket count:', error));
        });
    </script>
</body>
</html>
