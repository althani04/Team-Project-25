<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caf Lab - Artisanal Coffee Experience</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="css/home.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

<?php 
include 'session_check.php';
include 'navbar.php'; 
?>
<?php include 'basket_include.php'; ?>

    <main>
        <section class="hero">
            <img src="/Team-Project-255/assets/images/coffeebeans.jpeg" alt="Artisanal coffee preparation" class="hero-image">
            <div class="hero-content">
                <h2>Discover Your Perfect Cup</h2>
                <p>Embark on a journey of exceptional flavors with our artisanal coffee, carefully curated and delivered
                    fresh to your door</p>
                <a href="products.php" class="cta-button">Begin Your Journey</a>
            </div>
            <div class="hero-scroll-indicator">
                <div class="scroll-arrow"></div>
            </div>
        </section>

        <section class="features">
            <div class="feature" data-aos="fade-up">
                <h3>Family-Owned & Built on Loyalty</h3>
                <p>As a family-owned business, we bring generations of coffee-making tradition to every cup. Our
                    commitment to quality and consistency isn't just about business; it's about building lasting
                    relationships with our customers and community.</p>
            </div>
            <div class="feature" data-aos="fade-up" data-aos-delay="100">
                <h3>Ethically Sourced, Responsibly Crafted</h3>
                <p>Our coffee journey begins with ethical sourcing practices, giving back to responsible farms across
                    Central and South America. We collaborate with local communities to ensure sustainable practices and
                    fair trade partnerships.</p>
            </div>
            <div class="feature" data-aos="fade-up" data-aos-delay="200">
                <h3>Award-Winning Quality You Can Taste</h3>
                <p>Recognized for excellence, we're honored to be named 'Innovative Sustainability Leader in Coffee
                    2022'. These accolades reflect our vision and dedication to delivering exceptional coffee
                    experiences.</p>
            </div>
        </section>

        <section class="categories">
            <h2 data-aos="fade-up">Product Categories</h2>
            <div class="category-grid">
                <?php
                require_once __DIR__ . '/../config/database.php';
                try {
                    $conn = getConnection();
                    $stmt = $conn->query("SELECT * FROM Category");
                    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($categories as $category) {
                        $categoryName = htmlspecialchars($category['name']);
                        // Map category names to appropriate images
                        $categoryImages = [
                            'Single Origin' => 'Ethiopian_coffee',
                            'Accessories' => 'Coffee_pods',
                            'Coffee Capsules' => 'chocolatepods',
                            'Instant Coffee' => 'coffeebeans',
                            'Decaf Coffee' => 'Brazagian_coffee',
                            'Coffee' => 'Colombian_Coffee'
                        ];
                        
                        $imageFileName = isset($categoryImages[$categoryName]) ? $categoryImages[$categoryName] : 'coffeebeans';
                        $imageUrl = '/Team-Project-255/assets/images/' . $imageFileName . ($imageFileName === 'coffeebeans' ? '.jpeg' : '.png');
                        
                        echo <<<HTML
                        <a href="products.php?category={$categoryName}" class="category-card" data-aos="fade-up">
                            <img src="{$imageUrl}" alt="{$categoryName}" onerror="this.src='/Team-Project-255/assets/images/coffeebeans.jpeg'">
                            <h3>{$categoryName}</h3>
                        </a>
                        HTML;
                    }
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
                ?>
            </div>
        </section>

        <section class="newsletter">
            <h2 data-aos="fade-up">Newsletter Subscribe</h2>
            <p data-aos="fade-up">Stay in the loop for the latest updates and announcements on our journey</p>
            <form data-aos="fade-up" data-aos-delay="100">
                <input type="email" placeholder="Enter email here" required>
                <button type="submit">Subscribe</button>
            </form>
        </section>
    </main>

    <footer>
        <p>© 2024 CAF LAB Coffee Company. All Rights Reserved. Ecommerce software by Team Expert 25</p>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });

        // Remove loading screen
        window.addEventListener('load', () => {
            const loader = document.querySelector('.loading');
            if (loader) {
                loader.style.opacity = '0';
                setTimeout(() => {
                    loader.style.display = 'none';
                }, 500);
            }
        });

        // Product card hover effect
        const productCards = document.querySelectorAll('.product-card');

        productCards.forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                card.style.transform = `
                    translateY(-10px)
                    perspective(1000px)
                    rotateX(${(y - rect.height / 2) / 20}deg)
                    rotateY(${(x - rect.width / 2) / 20}deg)
                `;
            });

            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0) rotateX(0) rotateY(0)';
            });
        });

        // Newsletter form submission
        const newsletterForm = document.querySelector('.newsletter form');

        newsletterForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const button = newsletterForm.querySelector('button');
            const input = newsletterForm.querySelector('input');

            button.textContent = 'Thanks!';
            button.style.backgroundColor = '#4CAF50';
            input.value = '';

            setTimeout(() => {
                button.textContent = 'Subscribe';
                button.style.backgroundColor = '';
            }, 2000);
        });
    </script>
</body>

</html>
