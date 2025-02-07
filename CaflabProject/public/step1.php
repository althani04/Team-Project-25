<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Coffee Subscription</title>
    <link rel="stylesheet" href="css/subscription.css" />
  </head>
  <body>

  <?php include 'navbar.php'; ?>


    <main>
      <!-- Introduction Section -->
      <section class="intro-section">
          <h1>Flexible Subscription Plans</h1>
          <p>
              Our subscription service is designed to cater to your unique coffee preferences. 
              Choose from flexible plans that deliver freshly roasted coffee to your door, 
              ensuring your mornings are always fueled with your favorite brew.
          </p>
      </section>

      <!-- Steps Section -->
      <section class="steps-section">
          <h2>How Does It Work?</h2>
          <div class="intro-steps">
            <div class="intro-step">
                <h3>1. SELECT YOUR COFFE</h3>
                <p>Choose your favorite coffee from our wide variety of options!</p>
            </div>
            <div class="intro-step">
                <h3>2. PLAN & CHECKOUT</h3>
                <p>Enjoy a wide range of personalized options for your coffee subscription!</p>
            </div>
            <div class="intro-step">
                <h3>3. YOUR ENJOY!</h3>
                <p>After placing your order, look forward to your personalized coffee delivered to your & enjoy the joy it brings!</p>
            </div>
        </div>        
      </section>

      <!-- Button Section -->
      <section class="button-section">
          <a href="step2.php" class="button_GS">Get Started</a>
      </section>
  </main>

  <footer>
    <p>© 2024 CAF LAB Coffee Company. All Rights Reserved. Ecommerce software by Team Expert 25</p>
  </footer>

  <div class="modal" id="confirmation-modal">
    <div class="modal-content">
      <span class="close" id="close-confirmation-modal">&times;</span>
      <h3>Confirm Your Selection</h3>
      <p id="confirmation-message"></p>
      <button id="confirm-button" class="modal-button">Confirm</button>
      <button id="cancel-button" class="modal-button">Cancel</button>
    </div>
  </div>

    <script>
      function saveAndNext(type) {
        localStorage.setItem("coffeeType", type);
        window.location.href = "step2.php";
      }
      // Navigation toggle
      const navToggle = document.querySelector('.nav-toggle');
      const navMenu = document.querySelector('.nav-menu');

      navToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        navMenu.classList.toggle('active');

        // Animate menu items
        const menuItems = navMenu.querySelectorAll('li');
        menuItems.forEach((item, index) => {
        item.style.transitionDelay = `${index * 0.1}s`;
        });
      });

      // Close menu when clicking outside
      document.addEventListener('click', (e) => {
          if (!navMenu.contains(e.target) && !navToggle.contains(e.target)) {
              navMenu.classList.remove('active');
          }
      });
    </script>
  </body>
</html>
