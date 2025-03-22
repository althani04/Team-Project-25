<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Subscription - Choose Payment Plan</title>
    <link rel="stylesheet" href="css/subscription.css">
</head>
<body>

<?php 
  include 'session_check.php';
  include 'navbar.php'; 
  ?>


    <!-- Step Navigation -->
    <div class="step-navigation">
        <div class="step">1. <span>Select Product</span></div>
        <div class="step">2. <span>Set Quantity</span></div>
        <div class="step">3. <span>Set Frequency</span></div>
        <div class="step active">4. <span>Choose Your Plan</span></div>

    </div>

    <!-- Main Content -->
    <main>
        <div class="back-section">
            <button class="back-button" onclick="goBackToStep4()">
                Previous Page
            </button>
          </div>

        <section class="payment-plan-section">
            <h2>Choose Your Payment Plan</h2>
            <div class="payment-options">
                <div class="payment-option">
                    <h3>Pay-per-month</h3>
                    <p>Pay each time your coffee is delivered.</p>
                    <ul>
                        <li>Flexible cancellations</li>
                        <li>Adjust delivery dates</li>
                    </ul>
                    <p class="price">£0.00 per delivery</p>
                    <button class="select-plan">Select Plan</button>
                </div>
                <div class="payment-option">
                    <h3>Annual Plan</h3>
                    <p>Pay upfront for the whole year.</p>
                    <ul>
                        <li>Save 20%</li>
                        <li>Free shipping</li>
                        <li>Exclusive offers</li>
                    </ul>
                    <p class="price">£0.00 per year</p>
                    <button class="select-plan">Select Plan</button>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <p>© 2024 CAF LAB Coffee Company. All Rights Reserved. Ecommerce software by Team Expert 25</p>
    </footer>

    <script>
        function goBackToStep4() {
            window.location.href = "./step4.php";
        }
        
        document.addEventListener("DOMContentLoaded", function () {
            const quantity = localStorage.getItem("quantity");
            const frequency = localStorage.getItem("frequency");
            const basePrice = parseFloat(localStorage.getItem("productPrice")) || 0;

            calculatePlanAmounts(quantity, frequency, basePrice);
        });
         
        // Calculated amount
        function calculatePlanAmounts(quantity, frequency, basePrice) {
            let multiplier = quantity === "1kg" ? 2 : 1; // 1kg is double the price of 500g
            let frequencyFactor = frequency === "Every 2 Weeks" ? 2 : 1; // Monthly has 1 delivery per month

            const payPerMonthPrice = basePrice * multiplier * frequencyFactor;
            const annualPlanPrice =
            (payPerMonthPrice * 12) * 0.8; // 20% discount for annual

            // Update UI with calculated values
            document.querySelector(".payment-option:nth-child(1) .price").textContent =
                `£${payPerMonthPrice.toFixed(2)} per month`;
            document.querySelector(".payment-option:nth-child(2) .price").textContent =
                `£${annualPlanPrice.toFixed(2)} per year`;
        }

        document.addEventListener("DOMContentLoaded", function () {
            const plans = document.querySelectorAll(".select-plan");
            plans.forEach((button, index) => {
                button.addEventListener("click", function () {
                    const plan = index === 0 ? "Pay-per-month" : "Annual Plan";
                    const price =
                        index === 0
                            ? document.querySelector(
                                ".payment-option:nth-child(1) .price"
                            ).textContent.replace("£", "").split(" ")[0]
                            : document.querySelector(
                                ".payment-option:nth-child(2) .price"
                            ).textContent.replace("£", "").split(" ")[0];

                    localStorage.setItem("paymentPlan", plan);
                    localStorage.setItem("paymentAmount", price);
                    window.location.href = "./step6.php";
                });
            });
        });
    </script>
    <?php include 'Chatbot.php'; ?>
  </body>
</html>
