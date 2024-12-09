<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Shop - Caf Lab</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/products.css">
</head>

<body>
    
<?php include 'navbar.php'; ?>

    <main class="products-container">
        <section class="filters" data-aos="fade-up">
            <div class="filter-group">
                <label for="category">Category</label>
                <select id="category">
                    <option value="">All Categories</option>
                    <option value="coffee-beans">Coffee Beans</option>
                    <option value="ground-coffee">Ground Coffee</option>
                    <option value="brewing-equipment">Brewing Equipment</option>
                    <option value="accessories">Accessories</option>
                    <option value="gift-sets">Gift Sets</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="price-range">Price Range</label>
                <select id="price-range">
                    <option value="">All Prices</option>
                    <option value="0-15">Under £15</option>
                    <option value="15-30">£15 - £30</option>
                    <option value="30-50">£30 - £50</option>
                    <option value="50+">Over £50</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="search">Search</label>
                <input type="text" id="search" placeholder="Search products...">
            </div>
        </section>

        <div class="products-grid">
            <!-- Product cards will be dynamically populated here -->
        </div>
    </main>

    <footer>
        <p>© 2024 CAF LAB Coffee Company. All Rights Reserved. Ecommerce software by Team Expert 25</p>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });

        // Navigation toggle
        const navToggle = document.querySelector('.nav-toggle');
        const navMenu = document.querySelector('.nav-menu');

        navToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            navMenu.classList.toggle('active');
        });

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!navMenu.contains(e.target) && !navToggle.contains(e.target)) {
                navMenu.classList.remove('active');
            }
        });

        // Sample product data
        const products = [
            {
                id: 1,
                name: 'Ethiopian Yirgacheffe',
                description: 'Light roast with floral notes and citrus undertones',
                price: 14.99,
                category: 'coffee-beans',
                image: '/api/placeholder/400/400',
                stock: 50
            },
            {
                id: 2,
                name: 'Colombian Supremo',
                description: 'Medium roast with caramel sweetness and nutty finish',
                price: 12.99,
                category: 'coffee-beans',
                image: '/api/placeholder/400/400',
                stock: 45
            },
            // Add more products here
        ];

        // Render products
        function renderProducts(productsToRender) {
            const grid = document.querySelector('.products-grid');
            grid.innerHTML = '';

            productsToRender.forEach(product => {
                const card = document.createElement('div');
                card.className = 'product-card';
                card.setAttribute('data-aos', 'fade-up');

                card.innerHTML = `
                    <img src="${product.image}" alt="${product.name}" class="product-image">
                    <div class="product-info">
                        <h3 class="product-title">${product.name}</h3>
                        <p class="product-description">${product.description}</p>
                        <p class="product-price">£${product.price.toFixed(2)}</p>
                        <p class="stock-level ${product.stock < 10 ? 'low' : ''}">${product.stock < 10 ? 'Low Stock' : 'In Stock'
                    }</p>
                        <button class="add-to-cart">Add to Cart</button>
                    </div>
                `;

                grid.appendChild(card);
            });
        }

        // Filter products
        function filterProducts() {
            const category = document.getElementById('category').value;
            const priceRange = document.getElementById('price-range').value;
            const search = document.getElementById('search').value.toLowerCase();

            let filtered = products;

            if (category) {
                filtered = filtered.filter(p => p.category === category);
            }

            if (priceRange) {
                const [min, max] = priceRange.split('-').map(v => v === '+' ? Infinity : Number(v));
                filtered = filtered.filter(p => p.price >= min && p.price < (max || Infinity));
            }

            if (search) {
                filtered = filtered.filter(p =>
                    p.name.toLowerCase().includes(search) ||
                    p.description.toLowerCase().includes(search)
                );
            }

            renderProducts(filtered);
        }

        // Add event listeners for filters
        document.getElementById('category').addEventListener('change', filterProducts);
        document.getElementById('price-range').addEventListener('change', filterProducts);
        document.getElementById('search').addEventListener('input', filterProducts);

        // Initial render
        renderProducts(products);
    </script>
</body>

</html>