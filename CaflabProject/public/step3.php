<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Coffee Subscription - Set Quantity</title>
    <link rel="stylesheet" href="css/subscription.css" />
  </head>
  <body>

  <?php include 'navbar.php'; ?>


    <!-- Step Navigation -->
    <div class="step-navigation">
      <div class="step">1. <span>Select Product</span></div>
      <div class="step active">2. <span>Set Quantity</span></div>
      <div class="step">3. <span>Set Frequency</span></div>
      <div class="step">4. <span>Choose Your Plan</span></div>
    </div>

    <!-- Main Content -->
    <main>
      <div class="back-section">
        <button class="back-button" onclick="goBackToStep2()">
         Previous Page
        </button>
      </div>

      <section class="quantity-section">
        <h2>How Much Coffee Would You Like Per Delivery?</h2>
        <div class="quantity-options">
          <button class="quantity-option" onclick="saveAndNext('500g')">
            (SINGLE BAG) <br> 500g
          </button>
          <button class="quantity-option" onclick="saveAndNext('1kg')">
            (DOUBLE BAG) <br> 1kg
          </button>
        </div>
      </section>
    </main>

    <!-- Footer -->
    <footer>
      <p>© 2024 CAF LAB Coffee Company. All Rights Reserved. Ecommerce software by Team Expert 25</p>
    </footer>

    <script>
      function goBackToStep2() {
        window.location.href = "./step2.php";
      }

      function saveAndNext(quantity) {
        localStorage.setItem("quantity", quantity);
        window.location.href = "./step4.php";
      }  
    </script>
  </body>
</html>
