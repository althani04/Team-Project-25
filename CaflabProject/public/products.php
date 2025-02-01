<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Caf Lab</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/products.css">
    <link rel="stylesheet" href="css/basket.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <?php include 'basket_include.php'; ?>

    <main class="products-container">
        <section class="filters" data-aos="fade-up">
            <div class="filter-group">
                <label for="category">Category</label>
                <select id="category">
                    <option value="">All Categories</option>
                    <option value="Single Origin">Single Origin</option>
                    <option value="Accessories">Accessories</option>
                    <option value="Coffee Capsules">Coffee Capsules</option>
                    <option value="Instant Coffee">Instant Coffee</option>
                    <option value="Decaf Coffee">Decaf Coffee</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="price-range">Price Range</label>
                <select id="price-range">
                    <option value="">All Prices</option>
                    <option value="0-10">Under £10</option>
                    <option value="10-15">£10 - £15</option>
                    <option value="15-20">£15 - £20</option>
                    <option value="20+">Over £20</option>
                </select>
            </div>
            <div class="filter-group search-group">
                <label for="search">Search Products</label>
                <div class="search-input-group">
                    <input type="text" id="search" placeholder="Search products...">
                    <button id="search-button" class="search-btn">Search</button>
                </div>
            </div>
        </section>

        <section class="products-grid" id="product-list">
            <div class="loading">Loading products...</div>
        </section>
    </main>

    <footer>
        <p>© 2024 CAF LAB Coffee Company. All Rights Reserved. Ecommerce software by Team Expert 25</p>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // initialise AOS
        AOS.init({
            duration: 1000,
            once: true,
            offset: 50
        });

        // update the basket count
        function updateBasketCount(count) {
            const basketCount = document.querySelector('.basket-count');
            if (basketCount) {
                basketCount.textContent = count;
                basketCount.style.display = count > 0 ? 'block' : 'none';
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

        // wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // filter products
            function filterProducts() {
                const category = document.getElementById('category').value;
                const priceRange = document.getElementById('price-range').value;
                const searchTerm = document.getElementById('search').value;

                const productList = document.getElementById('product-list');
                productList.innerHTML = '<div class="loading">Loading products...</div>';

                fetch('fetch_products.php?category=' + encodeURIComponent(category) + 
                     '&priceRange=' + encodeURIComponent(priceRange) + 
                     '&search=' + encodeURIComponent(searchTerm))
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(products => {
                        productList.innerHTML = '';
                        if (products.length === 0) {
                            productList.innerHTML = '<div class="no-products">No products found matching your criteria.</div>';
                            return;
                        }

                        products.forEach(product => {
                            const card = document.createElement('div');
                            card.className = 'product-card';
                            card.setAttribute('data-aos', 'fade-up');
                            
                            const imageUrl = product.image_url && product.image_url !== 'N/A' 
                                ? product.image_url
                                : '/Team-Project-255/assets/images/coffeebeans.jpeg';
                            
                            const stockClass = product.stock_level === 'out of stock' ? 'out-of-stock' : 
                                            product.stock_level === 'low stock' ? 'low-stock' : '';
                            
                            card.innerHTML = `
                                <div class="product-image-container">
                                    <img src="${imageUrl}" alt="${product.name}" class="product-image" onerror="this.src='/Team-Project-255/assets/images/coffeebeans.jpeg'" loading="lazy">
                                    <div class="stock-badge ${product.stock_level.replace(' ', '-')}">${product.stock_level}</div>
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
                                </div>
                            `;

                            productList.appendChild(card);
                        });

                        // refresh AOS for new elements
                        AOS.refresh();
                    })
                    .catch(error => {
                        console.error('Error fetching products:', error);
                        productList.innerHTML = '<div class="error">Failed to fetch products. Please try again later.</div>';
                    });
            }

            // adding event listeners for filters
            document.getElementById('category').addEventListener('change', filterProducts);
            document.getElementById('price-range').addEventListener('change', filterProducts);
            document.getElementById('search').addEventListener('input', filterProducts);
            document.getElementById('search-button').addEventListener('click', filterProducts);

            // load the prodcuts
            filterProducts();

            // basket count update
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
