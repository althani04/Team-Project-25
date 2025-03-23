<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Coffee Subscription - Confirm Order</title>
    <link rel="stylesheet" href="css/subscription.css" />
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
      <div class="step">4. <span>Choose Your Plan</span></div>
    </div>

    <!-- Main Content -->
    <main>
      <div class="back-section">
        <button class="back-button" onclick="goBackToStep5()">
          Previous Page
        </button>
      </div>

      <section class="order-summary">
        <h2>Order Summary</h2>
        <div class="summary-details">
          <p>
            <strong>Coffee Plan:</strong> <span id="selectedProduct"></span>
          </p>
          <p>
            <strong>Payment Method:</strong>
            <span id="paymentMethod">Not added</span>
            <button class="edit-button" id="edit-payment">Edit/Add</button>
          </p>
          <p>
            <strong>Subscription Frequency:</strong>
            <span id="selectedFrequency"></span>
          </p>
          <p>
            <strong>Subscription Quantity:</strong>
            <span id="selectedQuantity"></span>
          </p>
          <p>
            <strong>Address:</strong>
            <span id="deliveryAddress">Not added</span>
            <button class="edit-button" id="edit-address">Change/Add</button>
          </p>
          <p>
            <strong>Payment Plan:</strong>
            <span id="selectedPaymentPlan">Not selected</span>
          </p>
          <p>
            <strong>Total Amount:</strong> £<span id="totalAmount">0.00</span>
          </p>
          <button class="subscribe-button">Subscribe Now!</button>
        </div>
      </section>
    </main>

    <!-- Add Payment Method Modal -->
    <div class="modal" id="payment-modal">
      <div class="modal-content">
        <span class="close" id="close-payment-modal">&times;</span>
        <h3>Add a New Payment Method</h3>
        <form>
          <label for="cardholder-name">Cardholder Name:</label>
          <input type="text" id="cardholder-name" required />
          <label for="card-number">Card Number:</label>
          <input type="text" id="card-number" required />
          <label for="expiry">Expiry Date:</label>
          <input type="text" id="expiry" placeholder="MM/YY" required />
          <label for="cvv">CVV:</label>
          <input type="text" id="cvv" required />
          <button type="submit" class="modal-button">Add Payment</button>
        </form>
      </div>
    </div>

    <!-- Edit Address Modal -->
    <div class="modal" id="address-modal">
      <div class="modal-content">
        <span class="close" id="close-address-modal">&times;</span>
        <h3>Edit Address</h3>
        <form>
          <label for="full-name">Full Name:</label>
          <input type="text" id="full-name" required />
          <label for="phone">Phone Number:</label>
          <input type="text" id="phone" required />
          <label for="address-line1">Address Line 1:</label>
          <input type="text" id="address-line1" required />
          <label for="address-line2">Address Line 2 (optional):</label>
          <input type="text" id="address-line2" />
          <label for="city">City:</label>
          <input type="text" id="city" required />
          <label for="postcode">Postcode:</label>
          <input type="text" id="postcode" required />
          <button type="submit" class="modal-button">Save Address</button>
        </form>
      </div>
    </div>

    <!-- Footer -->
    <footer>
      <p>© 2024 CAF LAB Coffee Company. All Rights Reserved. Ecommerce software by Team Expert 25</p>
    </footer>

    <script>
      console.log(localStorage)

      function goBackToStep5() {
        window.location.href = "./step5.php";
      }

      // Payment Modal
      const paymentModal = document.getElementById("payment-modal");
      const openPaymentModal = document.getElementById("edit-payment");
      const closePaymentModal = document.getElementById("close-payment-modal");

      openPaymentModal.addEventListener("click", () => {
        paymentModal.style.display = "block";
      });
      closePaymentModal.addEventListener("click", () => {
        paymentModal.style.display = "none";
      });

      // Address Modal
      const addressModal = document.getElementById("address-modal");
      const openAddressModal = document.getElementById("edit-address");
      const closeAddressModal = document.getElementById("close-address-modal");

      openAddressModal.addEventListener("click", () => {
        addressModal.style.display = "block";

        const userId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;
        fetch(`../api/get_user_inform.php?user_id=${userId}`)
          .then(response => response.json())
          .then(data => {
            const addressData = data.user;
            document.getElementById("full-name").value = addressData.name;
            document.getElementById("phone").value = addressData.phone_number;
            document.getElementById("address-line1").value = addressData.addressLine || null;
            document.getElementById("address-line2").value = null;
            document.getElementById("city").value = null;
            document.getElementById("postcode").value = addressData.postcode;
          })
          .catch(error => {
            console.error('get user information error:', error);
          });

      });
      closeAddressModal.addEventListener("click", () => {
        addressModal.style.display = "none";
      });

      // Close modal when clicking outside of it
      window.addEventListener("click", (event) => {
        if (event.target === paymentModal) {
          paymentModal.style.display = "none";
        }
        if (event.target === addressModal) {
          addressModal.style.display = "none";
        }
      });

      // The selected information is displayed when the page loads
      document.addEventListener("DOMContentLoaded", function () {
        // api call to get user information
        const userId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;
        fetch(`../api/get_user_inform.php?user_id=${userId}`)
          .then(response => response.json())
          .then(data => {
            console.log('user information:', data.user);
            // add user information to the page
            if(data.user.address_line) {
              const addressFromAPI = `${data.user.address_line}, ${data.user.postcode}`;
              // if the address is not saved in local storage, save it
              document.getElementById("deliveryAddress").textContent = addressFromAPI;
            }
          })
          .catch(error => {
            console.error('get user information error:', error);
          });

        // Displays the selected plan
        document.getElementById("selectedProduct").textContent =
          localStorage.getItem("productName") || "";
        document.getElementById("selectedFrequency").textContent =
          localStorage.getItem("frequency") || "";
        document.getElementById("selectedQuantity").textContent =
          localStorage.getItem("quantity") || "";
        document.getElementById("selectedPaymentPlan").textContent = 
          localStorage.getItem("paymentPlan") || "";
        document.getElementById("totalAmount").textContent =
          localStorage.getItem("paymentAmount") || "0.00";
      });

      // Processing payment form submissions
      document
        .querySelector("#payment-modal form")
        .addEventListener("submit", function (e) {
          e.preventDefault();
          const cardNumber = document.getElementById("card-number").value;
          const lastFour = cardNumber.slice(-4);
          const paymentDisplay = `****${lastFour}`;
          document.getElementById("paymentMethod").textContent = paymentDisplay;
          localStorage.setItem("paymentMethod", paymentDisplay);
          paymentModal.style.display = "none";
        });

      // Process address form submissions
      document
        .querySelector("#address-modal form")
        .addEventListener("submit", function (e) {
          e.preventDefault();
          const fullName = document.getElementById("full-name").value;
          const phone = document.getElementById("phone").value;
          const addressLine1 = document.getElementById("address-line1").value;
          const addressLine2 = document.getElementById("address-line2").value;
          const city = document.getElementById("city").value;
          const postcode = document.getElementById("postcode").value;
          const addressDisplay = `${addressLine1}, ${city}`;
          
          // get user id from session or local storage
          const userId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;

          let formData = new FormData();
          formData.append("user_id", userId);
          formData.append("full_name", fullName);
          formData.append("phone_number", phone);
          formData.append("address_line1", addressLine1);
          formData.append("address_line2", addressLine2);
          formData.append("city", city);
          formData.append("postcode", postcode);

          // send the address information to the server for updating
          fetch(`../api/update_address.php?user_id=${userId}`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              user_id: userId,
              full_name: fullName,
              phone_number: phone,
              address_line1: addressLine1,
              address_line2: addressLine2,
              city: city,
              postcode: postcode
            }),
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              document.getElementById("deliveryAddress").textContent = addressDisplay;
              localStorage.setItem("deliveryAddress", addressDisplay);
              addressModal.style.display = "none";
            } else {
              Swal.fire({
                icon: 'error',
                title: 'update Failed',
                text: data.message,
              });
            }
          })
          .catch(error => {
            Swal.fire({
              icon: 'error',
              title: 'update Failed',
              text: error.message,
            });
          });
        });

      // Subscribe button click event
      document
        .querySelector(".subscribe-button")
        .addEventListener("click", function () {
          const paymentMethod = document.getElementById("paymentMethod").textContent;
          const deliveryAddress = document.getElementById("deliveryAddress").textContent;

          if (!paymentMethod) {
            Swal.fire({
              icon: 'error',
              title: 'Subscribe Failed',
              text: 'Please select a payment method.'
            });
            return;
          }
          if (!deliveryAddress) {
            Swal.fire({
              icon: 'error',
              title: 'Subscribe Failed',
              text: 'Please add a delivery address.'
            });
            return;
          }

          // Save the order information for the final page display
          const totalAmount = localStorage.getItem("paymentAmount") || "0.00";

          localStorage.setItem("finalTotalAmount", totalAmount);
          localStorage.setItem(
            "orderNumber",
            Math.floor(Math.random() * 1000000)
          );

          // window.location.href = "./final.php";

          // get the data from the local storage
          let quantity = parseInt(localStorage.getItem("quantity"));
          let quantityFactor = quantity == 500 ? 1 : 2;
          const subscriptionData = {
            order_id: localStorage.getItem("orderNumber"),
            user_id: localStorage.getItem("userId"),
            plan_id: localStorage.getItem("planId"),
            quantity: quantityFactor * quantity,
            frequency: localStorage.getItem("frequency"),
            start_date: new Date().toISOString().split('T')[0],
            payment_plan: localStorage.getItem("paymentPlan").includes('pay-per-month') ? 'monthly' : 'annually',
            total_price: totalAmount,
          };
          // send the data to the server
          fetch('../api/subscribe.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(subscriptionData)
          })
          .then(response => {
            console.log(response)
            // if (!response.ok) throw new Error('subscribe failed');
            return response.json();
          })
          .then(data => {
            // save the order information
            const totalAmount = localStorage.getItem("paymentAmount") || "0.00";
            localStorage.setItem("finalTotalAmount", totalAmount);
            
            window.location.href = "./final.php";
          })
          .catch(error => {
            // console.error('Error:', error);
            Swal.fire({
              icon: 'error',
              title: 'Subscribe Failed',
              text: error.message,
            });
          });
        });
    </script>
    <?php include 'Chatbot.php'; ?>
  </body>
</html>
