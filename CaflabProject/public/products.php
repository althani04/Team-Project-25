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
    <?php 
    include 'session_check.php';
    include 'navbar.php'; 
    ?>
    <?php include 'basket_include.php'; ?>

    <main class="products-container">
<section class="filters" data-aos="fade-up">
            <div class="filter-group">
                <label for="category">Category</label>
                <select id="category" tabindex="0">
                    <option value="">All Categories</option>
                    <?php
                    require_once __DIR__ . '/../config/database.php';
                    try {
                        $conn = getConnection();
                        $stmt = $conn->query("SELECT name FROM Category ORDER BY name");
                        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        foreach ($categories as $category) {
                            $categoryName = htmlspecialchars($category['name']);
                            $selected = isset($_GET['category']) && $_GET['category'] === $categoryName ? 'selected' : '';
                            echo "<option value=\"{$categoryName}\" {$selected}>{$categoryName}</option>";
                        }
                    } catch (PDOException $e) {
                        error_log("Error loading categories: " . $e->getMessage());
                    }
                    ?>
                </select>
            </div>
            <div class="filter-group">
                <label for="price-range">Price Range</label>
                <select id="price-range" tabindex="0">
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
                    <input type="text" id="search" placeholder="Search products..." tabindex="0">
                    <button id="search-button" class="search-btn" tabindex="0">Search</button>
                </div>
            </div>
            <div class="filter-group">
                <label for="sort">Sort By</label>
                <select id="sort" tabindex="0">
                    <option value="">Default Sorting</option>
                    <option value="name_asc">Name (A-Z)</option>
                    <option value="name_desc">Name (Z-A)</option>
                    <option value="price_asc">Price (Low to High)</option>
                    <option value="price_desc">Price (High to Low)</option>
                </select>
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

        // toggle wishlist icon
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
        }

        // wait for DOM to be fully loaded
       document.addEventListener('DOMContentLoaded', function() {
           // to get URL parameters
           function getParameterByName(name, url = window.location.href) {
               name = name.replace(/[\[\]]/g, '\\$&');
               var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
                   results = regex.exec(url);
               if (!results) return null;
               if (!results[2]) return '';
               return decodeURIComponent(results[2].replace(/\+/g, ' '));
           }

           // updating URL with the current filters
            function updateURL(params) {
                const url = new URL(window.location.href);
                Object.keys(params).forEach(key => {
                    if (params[key]) {
                        url.searchParams.set(key, params[key]);
                    } else {
                        url.searchParams.delete(key);
                    }
                });
                window.history.pushState({}, '', url);
            }

            // getting sort option
            const sortSelect = document.getElementById('sort');

           // filtering  products
           function filterProducts(updateHistory = true) {
                const categorySelect = document.getElementById('category');
                const category = categorySelect.value;
                const priceRange = document.getElementById('price-range').value;
                const searchTerm = document.getElementById('search').value;
                const sortOption = sortSelect.value;

               // update URL if requested
                if (updateHistory) {
                    updateURL({
                        category: category,
                        priceRange: priceRange,
                        search: searchTerm,
                        sort: sortOption // Include sort in URL
                    });
                }

               const productList = document.getElementById('product-list');
               productList.innerHTML = '<div class="loading">Loading products...</div>';

               let fetchUrl = 'fetch_products.php?';
               if (category) {
                   fetchUrl += 'category=' + encodeURIComponent(category) + '&';
               }
               fetchUrl += 'priceRange=' + encodeURIComponent(priceRange) + '&';
               fetchUrl += 'search=' + encodeURIComponent(searchTerm) + '&';
               fetchUrl += 'sort=' + encodeURIComponent(sortOption); // Add sort parameter

               fetch(fetchUrl)
                   .then(response => {
                       if (!response.ok) {
                           throw new Error('Network response was not ok');
                       }
                       return response.json();
                   })
                   .then(data => {
                       const products = data.products;
                       productList.innerHTML = '';
                       if (products.length === 0) {
                           productList.innerHTML = '<div class="no-products">No products found matching your criteria.</div>';
                           return;
                       }

                       products.forEach(product => {
                           const card = document.createElement('div');
                           card.className = 'product-card';
                           card.setAttribute('data-aos', 'fade-up');

                           const imageUrl = product.image_url && product.image_url !== 'N/A' ?
                               product.image_url :
                               '/Team-Project-255/assets/images/coffeebeans.jpeg';

                           const stockClass = product.stock_level === 'out of stock' ? 'out-of-stock' :
                               product.stock_level === 'low stock' ? 'low-stock' : 'in-stock';

                           card.innerHTML = `
                            <div class="product-image-container">
                                <img src="${imageUrl}" alt="${product.name} product image" class="product-image" onerror="this.src='/Team-Project-255/assets/images/coffeebeans.jpeg'" loading="lazy">
                                <div class="learn-more-button-container">
                                    <a href="product_detail.php?product_id=${product.product_id}" class="learn-more-button" tabindex="0">Learn More</a>
                                </div>
                                <div class="wishlist-icon" onclick="toggleWishlist(this, ${product.product_id})" tabindex="0">&#x2661;</div>
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
                                        ${product.stock_level === 'out of stock' ? 'disabled' : ''}
                                        tabindex="0">
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
                       console.error('Error fetching products:', error.message); // Log specific error message
                       productList.innerHTML = '<div class="error">Failed to fetch products. Please try again later.</div>';
                   });
           }

           // initialise filters from URL parameters
           const urlCategory = getParameterByName('category');
           if (urlCategory) {
               document.getElementById('category').value = urlCategory;
           }
           const urlPriceRange = getParameterByName('priceRange');
           if (urlPriceRange) {
               document.getElementById('price-range').value = urlPriceRange;
           }
           const urlSearch = getParameterByName('search');
           if (urlSearch) {
               document.getElementById('search').value = urlSearch;
           }
            const urlSort = getParameterByName('sort');
           if (urlSort) {
               document.getElementById('sort').value = urlSort;
           }

           // adding event listeners for filters
           document.getElementById('category').addEventListener('change', () => filterProducts(true));
           document.getElementById('price-range').addEventListener('change', () => filterProducts(true));
           document.getElementById('search').addEventListener('input', () => filterProducts(true));
           document.getElementById('search-button').addEventListener('click', () => filterProducts(true));
           document.getElementById('sort').addEventListener('change', () => filterProducts(true));

           // handle browser back/forward buttons
           window.addEventListener('popstate', () => {
               const urlCategory = getParameterByName('category');
               if (urlCategory) {
                   document.getElementById('category').value = urlCategory;
               } else {
                   document.getElementById('category').value = '';
               }
               filterProducts(false);
           });

           // load the products, initially filtering by URL parameter if present
           filterProducts(false);

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
    <?php include 'Chatbot.php'; ?>
</body>
</html>
