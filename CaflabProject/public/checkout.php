<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Caf Lab</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/checkout.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<main>
    <!-- <div style="text-align: center; margin-bottom: 1rem;">
        <h1 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; color: var(--primary);">Checkout</h1>
    </div> -->

    <!-- Checkout Container -->
    <div class="checkout-page">
        <!-- Left Section: Product Image and Details -->
        <div class="product-details">
            <!-- Product 1 -->
            <div class="product-item">
                <h2>Product 1</h2>
                <img src="path-to-your-product-image-1.jpg" alt="Product 1 Image" class="product-image">
                <div class="product-info">
                    <p><strong>Product Code:</strong> <span>12345</span></p>
                    <p><strong>Quantity:</strong> <span>1</span></p>
                    <p><strong>Price:</strong> £<span>12.00</span></p>
                    <p><strong>Description:</strong> <span>Coffee Beans</span></p>
                </div>
            </div>

            <!-- Product 2 -->
            <div class="product-item">
                <h2>Product 2</h2>
                <img src="path-to-your-product-image-2.jpg" alt="Product 2 Image" class="product-image">
                <div class="product-info">
                    <p><strong>Product Code:</strong> <span>67890</span></p>
                    <p><strong>Quantity:</strong> <span>1</span></p>
                    <p><strong>Price:</strong> £<span>15.00</span></p>
                    <p><strong>Description:</strong> <span>Classic Black Coffee Mug with Logo</span></p>
                </div>
            </div>

            <!-- Total Cost -->
            <div class="total-cost">
                <p>Total Cost: £<span>27.00</span></p>
            </div>
        </div>

        <!-- Right Section: Checkout and Payment Details -->
        <div class="checkout-details">
            <h2>Personal Details</h2>
            <form id="checkout-form">
                <div>
                    <label for="country">Country or Region</label>
                    <select id="country" name="country">
                        <option value="UK">United Kingdom</option>
                    </select>
                </div>
                <div class="name-fields">
                    <div>
                        <label for="first-name">First Name</label>
                        <input type="text" id="first-name" name="first-name" placeholder="Your Forename" required>
                    </div>
                    <div>
                        <label for="last-name">Surname</label>
                        <input type="text" id="last-name" name="last-name" placeholder="Your Surname" required>
                    </div>
                </div>
                <div>
                    <label for="address">Street Address</label>
                    <input type="text" id="address" name="address" placeholder="Your Home Address" required>
                </div>
                <div>
                    <label for="postcode">Postcode</label>
                    <input type="text" id="postcode" name="postcode" placeholder="Postcode" required>
                </div>
                <div>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Email" required>
                </div>
                <div>
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" placeholder="Phone Number" required>
                </div>

                <h2>Payment Details</h2>
                <div>
                    <label for="card-number">Card Number</label>
                    <input type="text" id="card-number" name="card-number" placeholder="1234 5678 9012 3456" required>
                </div>
                <div>
                    <label for="expiry-date">Expiration Date</label>
                    <input type="text" id="expiry-date" name="expiry-date" placeholder="MM/YY" required>
                </div>
                <div>
                    <label for="cvv">CVV</label>
                    <input type="text" id="cvv" name="cvv" placeholder="123" required>
                </div>

                <button type="submit" class="checkout-button">Confirm and Pay</button>
                <button type="button" class="cancel-button" onclick="window.location.href='/cancel'">Cancel Order</button>
            </form>
        </div>
    </div>
</main>


    <footer>
        <p>© 2024 CAF LAB Coffee Company. All Rights Reserved. Ecommerce software by Team Expert 25</p>
    </footer>

    
</body>
</html>