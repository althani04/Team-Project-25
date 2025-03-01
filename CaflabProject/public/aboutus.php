<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Caflab</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/aboutus.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php 
include 'session_check.php';
include 'navbar.php'; 
?>
<?php include 'basket_include.php'; ?>

    <section class="about-us">
        <div class="container">
            <div class="about-header">
                <h2>About Us</h2>
                <h1>Our Vision</h1>
                <p>
                    At Caflab, our vision is to redefine the coffee experience by bringing the highest quality, 
                    fresh ground coffee directly to your door. We believe that every cup tells a story, and we're 
                    dedicated to ensuring yours is filled with the finest flavors, ethical sourcing, and sustainability. 
                    <br><br>
                    Our mission extends beyond delivering exceptional coffee; we aim to create a community of coffee 
                    enthusiasts who celebrate the craft of brewing at home. By offering a seamless subscription service, 
                    along with a  selection of one off purchases, we empower our customers to transform their daily 
                    coffee routine into a moment of indulgence and joy.
                </p>
                <button class="btn" onclick="location.href='#about-values'">Learn More</button>
            </div>
            <div class="image">
                <img src="../../assets/images/logo.png" alt="About Us Image">
            </div>
        </div>
    </section>

    <section class="about-coffee">
        <h3 class="subheader">About Our Coffee</h3>
        <h2 class="section-title">What Makes Our Coffee Different?</h2>
        <div class="container">
            <div class="content">
                <!-- left side text -->
                <div class="text-block left">
                    <div class="feature">
                        <h3>ORGANIC COFFEE</h3>
                        <p>Our coffee is grown responsibly and without harmful chemicals, ensuring quality in every sip.</p>
                    </div>
                    <div class="divider"></div> <!-- Divider -->
                    <div class="feature">
                        <h3>PESTICIDE FREE</h3>
                        <p>Our beans are cultivated naturally, free of pesticides and artificial enhancements.</p>
                    </div>
                </div>
    
                <!-- image psoitioned in the center -->
                <div class="image-center">
                    <img src="../../assets/images/coffeebeans.jpeg" alt="Coffee Image">
                </div>
    
                <!-- right side text -->
                <div class="text-block right">
                    <div class="feature">
                        <h3>HAND PICKED</h3>
                        <p>Each bean is carefully selected to ensure a perfect brew every time.</p>
                    </div>

                    <!-- divider to seperate texts with a line -->
                    <div class="divider"></div> 
                    <div class="feature">
                        <h3>ALWAYS FRESH</h3>
                        <p>Enjoy coffee that's freshly roasted and packed to preserve its rich flavors.</p>
                    </div>
                </div>

                <div class = "button-products">
                    <!-- button to navigate to product page -->
                    <a href="products.php" class="btn-products">Browse Our Products</a>
                </div>

            </div>
        </div>
    </section>

    
    <section id="about-values" class="about-values">
        <h3 class="subheader">Why We Stand Out</h3>
        <h2 class="section-title">Our Core Values</h2>
        <div class="container">

            <div class="value-block">
                <img src="../../assets/images/sustainability.png" alt="Sustainability Icon" class="value-icon">
                <h3>Sustainability</h3>
                <p>We are committed to reducing waste and environmental impact, from eco-friendly packaging 
                    to responsible operations.</p>
            </div>

            <div class="value-block">
                <img src="../../assets/images/ourstory.png" alt="Our Story Icon" class="value-icon">
                <h3>Our Story</h3>
                <p>Born out of a love for coffee and community, Caflab is dedicated to delivering exceptional experiences, 
                    one cup at a time.</p>
            </div>

            <div class="value-block">
                <img src="../../assets/images/ethic.png" alt="Ethical Sourcing Icon" class="value-icon">
                <h3>Ethical Sourcing</h3>
                <p>Our beans are ethically sourced from trusted farmers, ensuring fair practices and the finest 
                    quality coffee.</p>
            </div>
        </div>
    </section>
</body>
</html>
