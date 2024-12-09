<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Coffee Subscription - Success</title>
    <link rel="stylesheet" href="css/subscription.css" />
  </head>
  <body>

  <?php include 'navbar.php'; ?>

    <!-- Main Content -->
    <main>
      <section class="success-section">
        <h1>Congratulations!</h1>
        <p>Your coffee subscription has been successfully completed.</p>
        <div class="order-details">
          <p><strong>Order Number:</strong> #<span id="orderNumber"></span></p>
          <p><strong>Plan:</strong> <span id="selectedProduct"></span></p>
          <p>
            <strong>Delivery Frequency:</strong>
            <span id="selectedFrequency"></span>
          </p>
          <p><strong>Total Amount:</strong> £<span id="totalAmount"></span></p>
        </div>
        <div class="success-actions">
          <button
            class="action-button"
            onclick="window.location.href='./step1.php'"
          >
            Go to Homepage
          </button>
          <button
            class="action-button"
            onclick="window.location.href='products.php'"
          >
            Browse More Products
          </button>
        </div>
      </section>
    </main>

    <!-- Footer -->
    <footer>
      <p>© 2024 CAF LAB Coffee Company. All Rights Reserved. Ecommerce software by Team Expert 25</p>
    </footer>

    <script>
      document.addEventListener("DOMContentLoaded", function () {
        // Show order information
        document.getElementById("orderNumber").textContent =
          localStorage.getItem("orderNumber") || "";
        document.getElementById("selectedProduct").textContent =
          localStorage.getItem("productName") || "";
        document.getElementById("selectedFrequency").textContent =
          localStorage.getItem("frequency") || "";
        document.getElementById("totalAmount").textContent =
          localStorage.getItem("finalTotalAmount") || "0.00";

        // Clear local storage (after order completion)
        localStorage.clear();
      });

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
