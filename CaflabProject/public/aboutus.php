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
include 'basket_include.php';
?>

<section class="hero-section section-padding" style="background-image: url('assets/images/aboutus2.jpg');">
    <div class="container hero-container">
        <div class="hero-content">
            <h2 class="subheader">About Us</h2>
            <h1 class="section-title">Our Vision</h1>
            <p>
                At Caflab, our vision is to redefine the coffee experience by bringing the highest quality,
                fresh ground coffee directly to your door. We believe that every cup tells a story, and we're
                dedicated to ensuring yours is filled with the finest flavors, ethical sourcing, and sustainability.
                <br><br>
                Our mission extends beyond delivering exceptional coffee; we aim to create a community of coffee
                enthusiasts who celebrate the craft of brewing at home. By offering a seamless subscription service,
                along with a selection of one-off purchases, we empower our customers to transform their daily
                coffee routine into a moment of indulgence and joy.
            </p>
            <a href="#values" class="btn" tabindex="0">Learn More</a>
        </div>
    </div>
</section>

<section class="our-story-section section-padding">
    <div class="container our-story-container">
        <div class="story-content">
            <h2 class="subheader">Our Story</h2>
            <h1 class="section-title">From Bean to Cup, Our Journey</h1>
            <p>
                Founded in 2024, Caflab began as a small roastery with a big dream: to share our passion for
                exceptional coffee with everyone. Starting with ethically sourced beans and a commitment to
                sustainable practices, we've grown into a community of coffee lovers dedicated to quality and
                experience.
                <br><br>
                Our journey is fueled by a desire to innovate and explore the rich world of coffee. From selecting
                the finest beans to perfecting our roasting techniques, every step is taken with care and intention.
                We invite you to join us as we continue to explore, learn, and share the best coffee experiences.
            </p>
            <br>
            <a href="products.php" class="btn" tabindex="0">Browse Our Products</a>
        </div>
        <div class="story-image">
            <img src="/Team-Project-255/assets/images/aboutus2.jpg" alt="Coffee Roasting Process">
        </div>
    </div>
</section>


<section class="values-section section-padding" id="values">
    <div class="container values-container">
        <h3 class="subheader">Our Values</h3>
        <h2 class="section-title">What Drives Us</h2>
        <div class="values-grid">
            <div class="value-card">
                <img src="/Team-Project-255/assets/images/sustainability.png" alt="Sustainability Icon" class="value-icon">
                <h3>Sustainability</h3>
                <p>We are committed to reducing waste and environmental impact, from eco-friendly packaging to responsible operations.</p>
            </div>
            <div class="value-card">
                <img src="/Team-Project-255/assets/images/ourstory.png" alt="Passion Icon" class="value-icon">
                <h3>Passion</h3>
                <p>Born out of a love for coffee and community, Caflab is dedicated to delivering exceptional experiences, one cup at a time.</p>
            </div>
            <div class="value-card">
                <img src="/Team-Project-255/assets/images/ethic.png" alt="Ethical Sourcing Icon" class="value-icon">
                <h3>Ethical Sourcing</h3>
                <p>Our beans are ethically sourced from trusted farmers, ensuring fair practices and the finest quality coffee.</p>
            </div>
        </div>
    </div>
</section>

<footer>
        <p>© 2024 CAF LAB Coffee Company. All Rights Reserved. Ecommerce software by Team Expert 25</p>
    </footer>

    <?php include 'Chatbot.php'; ?>
</body>
</html>
