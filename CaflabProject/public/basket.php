<?php
session_start();

// Initialize basket session if not already set
if (!isset($_SESSION['basket'])) {
    $_SESSION['basket'] = [];
}

// Helper function to get the current basket and its total price
function getBasket() {
    $totalPrice = 0;
    $items = [];

    foreach ($_SESSION['basket'] as $item) {
        $totalPrice += $item['price'];
        $items[] = $item;
    }

    return ['items' => $items, 'total' => $totalPrice];
}

// Handle AJAX requests for adding/removing items
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data['action'] === 'add') {
        // Add item to basket
        $_SESSION['basket'][] = [
            'id' => $data['id'],
            'name' => $data['name'],
            'price' => $data['price']
        ];
    } elseif ($data['action'] === 'remove') {
        // Remove item from basket
        foreach ($_SESSION['basket'] as $index => $item) {
            if ($item['id'] == $data['id']) {
                unset($_SESSION['basket'][$index]);
                break;
            }
        }
    }

    // Return updated basket
    echo json_encode(getBasket());
    exit;
}

// Handle GET request to load the basket
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(getBasket());
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caf Lab</title>
    <style>
        /* Your CSS Styles */
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap');

        :root {
            --primary: #8B7355;
            --secondary: #E8DCCA;
            --accent: #D4A373;
            --text: #2C1810;
            --background: #FAF6F1;
            --white: #FFFFFF;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--background);
            color: var(--text);
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        h1, h2, h3, h4 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
        }

        /* Header & Navigation */
        .header {
            background-color: rgba(250, 246, 241, 0.9);
            backdrop-filter: blur(10px);
            padding: 1.5rem 3rem;
            position: fixed;
            width: 100%;
            z-index: 100;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        .logo {
            color: var(--text);
            font-size: 2rem;
            font-weight: bold;
            text-decoration: none;
            font-family: 'Playfair Display', serif;
        }

        /* Products Section */
        .products {
            padding: 8rem 4rem;
            background: var(--secondary);
            text-align: center;
        }

        .products h2 {
            font-size: 2.5rem;
            margin-bottom: 4rem;
        }

        .product {
            display: inline-block;
            background: var(--white);
            padding: 1.5rem;
            border-radius: 10px;
            margin: 1rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .product p {
            margin-bottom: 1rem;
            font-size: 1.2rem;
            color: var(--text);
        }

        .add-to-basket {
            background: var(--accent);
            color: var(--white);
            border: none;
            padding: 0.7rem 1.5rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .add-to-basket:hover {
            background: var(--primary);
        }

        /* Basket */
        .basket-container {
            position: fixed;
            top: 10px;
            right: 10px;
            width: 300px;
            background: var(--white);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            z-index: 2000;
        }

        .basket-header {
            background: var(--accent);
            color: var(--white);
            padding: 1rem;
            font-size: 1.2rem;
            text-align: center;
        }

        .basket-items {
            max-height: 300px;
            overflow-y: auto;
            padding: 1rem;
        }

        .basket-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #ddd;
        }

        .basket-total {
            padding: 1rem;
            text-align: right;
            font-size: 1rem;
            font-weight: bold;
            border-top: 1px solid #ddd;
        }

        .checkout-button {
            background-color: #28a745;
            color: white;
            padding: 1rem;
            border: none;
            border-radius: 5px;
            width: 100%;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .checkout-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        footer {
            background: var(--text);
            color: var(--white);
            text-align: center;
            padding: 2rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <a href="/" class="logo">Caf Lab</a>
    </header>

    <main>
        <section class="products">
            <h2>Shop Our Products</h2>
            <div class="product" data-id="1" data-name="Coffee Beans" data-price="10.00">
                <p>Coffee Beans - $10.00</p>
                <button class="add-to-basket">Add to Basket</button>
            </div>
            <div class="product" data-id="2" data-name="Espresso Blend" data-price="15.00">
                <p>Espresso Blend - $15.00</p>
                <button class="add-to-basket">Add to Basket</button>
            </div>
            <div class="product" data-id="3" data-name="Cold Brew" data-price="8.00">
                <p>Cold Brew - $8.00</p>
                <button class="add-to-basket">Add to Basket</button>
            </div>
        </section>
    </main>

    <div class="basket-container">
        <div class="basket-header">Your Basket</div>
        <div class="basket-items">
            <p>No items in the basket.</p>
        </div>
        <div class="basket-total">Total: $<span id="total-price">0.00</span></div>
        <div class="checkout-container">
            <button class="checkout-button" id="checkout-button">Checkout</button>
        </div>
    </div>

    <footer>
        <p>© 2024 CAF LAB Coffee Company. All Rights Reserved. Ecommerce software by Team Expert 25</p>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const addToBasketButtons = document.querySelectorAll(".add-to-basket");
            const basketItemsContainer = document.querySelector(".basket-items");
            const totalPriceElement = document.getElementById("total-price");
            const checkoutButton = document.getElementById("checkout-button");

            let totalPrice = 0;

            // Function to update basket
            const updateBasket = () => {
                fetch("<?php echo $_SERVER['PHP_SELF']; ?>", {
                    method: "GET"
                })
                .then(response => response.json())
                .then(data => {
                    basketItemsContainer.innerHTML = '';
                    if (data.items.length > 0) {
                        data.items.forEach(item => {
                            const basketItem = document.createElement("div");
                            basketItem.className = "basket-item";
                            basketItem.innerHTML = `
                                <span>${item.name}</span>
                                <span>$${item.price.toFixed(2)}</span>
                                <button class="remove-item" data-id="${item.id}">Remove</button>
                            `;
                            basketItemsContainer.appendChild(basketItem);
                        });
                        totalPriceElement.textContent = data.total.toFixed(2);
                    } else {
                        basketItemsContainer.innerHTML = '<p>No items in the basket.</p>';
                    }
                });
            };

            // Adding item to basket using AJAX
            addToBasketButtons.forEach(button => {
                button.addEventListener("click", () => {
                    const product = button.parentElement;
                    const id = product.getAttribute("data-id");
                    const name = product.getAttribute("data-name");
                    const price = parseFloat(product.getAttribute("data-price"));

                    fetch("<?php echo $_SERVER['PHP_SELF']; ?>", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({ action: "add", id, name, price })
                    })
                    .then(response => response.json())
                    .then(() => updateBasket());
                });
            });

            // Remove item from basket using AJAX
            basketItemsContainer.addEventListener("click", (e) => {
                if (e.target.classList.contains("remove-item")) {
                    const itemId = e.target.getAttribute("data-id");

                    fetch("<?php echo $_SERVER['PHP_SELF']; ?>", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({ action: "remove", id: itemId })
                    })
                    .then(response => response.json())
                    .then(() => updateBasket());
                }
            });

            // Checkout button functionality
            checkoutButton.addEventListener("click", () => {
                if (totalPrice > 0) {
                    alert(`Proceeding to checkout with a total of $${totalPrice.toFixed(2)}.`);
                    window.location.href = "/checkout.html";
                } else {
                    alert("Your basket is empty. Add some items before checking out.");
                }
            });

            // Initialize the basket on page load
            updateBasket();
        });
    </script>
</body>
</html>