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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include 'navbar.php'; ?>

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
            <!-- Product cards will be dynamically populated here -->
            <div class="loading">Loading products...</div>
        </section>
    </main>

    <footer>
        <p>© 2024 CAF LAB Coffee Company. All Rights Reserved. Ecommerce software by Team Expert 25</p>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true,
            offset: 50
        });

        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            function filterProducts() {
                const category = document.getElementById('category').value;
                const priceRange = document.getElementById('price-range').value;
                const searchTerm = document.getElementById('search').value;

                // Show loading state
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
                            
                            // Fix image path by replacing backslashes with forward slashes
                            const imageUrl = product.image_url && product.image_url !== 'N/A' 
                                ? product.image_url.replace(/\\/g, '/') 
                                : '/assets/images/coffeebeans.jpeg';
                            
                            card.innerHTML = `
                                <div class="product-image-container">
                                    <img src="${imageUrl}" alt="${product.name}" class="product-image">
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
                                    <button class="add-to-cart-btn" onclick="addToCart(${product.product_id})">
                                        Add to Cart
                                    </button>
                                </div>
                            `;

                            productList.appendChild(card);
                        });

                        // Refresh AOS for new elements
                        AOS.refresh();
                    })
                    .catch(error => {
                        console.error('Error fetching products:', error);
                        productList.innerHTML = '<div class="error">Failed to fetch products. Please try again later.</div>';
                    });
            }

            function addToCart(productId) {
                // TODO: Implement add to cart functionality
                Swal.fire({
                    icon: 'success',
                    title: 'Added to Cart!',
                    text: 'Product has been added to your cart.',
                    showConfirmButton: false,
                    timer: 1500
                });
                console.log('Adding product to cart:', productId);
            }

            // Add event listeners for filters
            document.getElementById('category').addEventListener('change', filterProducts);
            document.getElementById('price-range').addEventListener('change', filterProducts);
            document.getElementById('search').addEventListener('input', filterProducts);
            document.getElementById('search-button').addEventListener('click', filterProducts);

            // Initial render
            filterProducts();
        });
    </script>
</body>

</html>
