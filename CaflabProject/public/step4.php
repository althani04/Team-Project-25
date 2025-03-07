<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Coffee Subscription - Set Frequency</title>
    <link rel="stylesheet" href="css/subscription.css" />
  </head>
  <body>

  <?php include 'navbar.php'; ?>


    <!-- Step Navigation -->
    <div class="step-navigation">
      <div class="step">1. <span>Select Product</span></div>
      <div class="step">2. <span>Set Quantity</span></div>
      <div class="step active">3. <span>Set Frequency</span></div>
      <div class="step">4. <span>Choose Your Plan</span></div>
    </div>

    <!-- Main Content -->
    <main>
      <div class="back-section">
        <button class="back-button" onclick="goBackToStep3()">
          Previous Page
        </button>
      </div>

      <section class="frequency-section">
        <h2>How Often Would You Like It Delivered?</h2>
        <div class="frequency-options">
          <button
            class="frequency-option"
            onclick="saveAndNext('Every 2 Weeks')"
          >
            Every 2 Weeks
          </button>
          <button class="frequency-option" onclick="saveAndNext('Every Month')">
            Every Month
          </button>
        </div>
      </section>
    </main>

    <!-- Footer -->
    <footer>
      <p>© 2024 CAF LAB Coffee Company. All Rights Reserved. Ecommerce software by Team Expert 25</p>
    </footer>

    <script>
      function goBackToStep3() {
        window.location.href = "./step3.php";
      }

      function saveAndNext(frequency) {
        localStorage.setItem("frequency", frequency);
        window.location.href = "./step5.php";
      }
    </script>
  </body>
</html>
