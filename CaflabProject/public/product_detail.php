<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details - Caf Lab</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="css/navbar.css">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/product_detail.css">
    <link rel="stylesheet" href="css/basket.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .review-rating {
            display: inline-block;
            font-size: 1.2rem;
            color: var(--accent);
        }

        .review-rating .star {
            color: #ccc;
        }

        .review-rating .star.filled {
            color: var(--accent);
        }
    </style>
</head>
<body>
    <?php
    include 'session_check.php';
    include 'navbar.php';
    include 'basket_include.php';

    function renderStarRating($rating) {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                $stars .= '<span class="star filled">&#9733;</span>';
            } else {
                $stars .= '<span class="star">&#9733;</span>';
            }
        }
        return $stars;
    }
    ?>

    <main class="product-detail-container">
        <section class="product-info">
            <?php
            require_once __DIR__ . '/../config/database.php';
            $product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

            if ($product_id <= 0) {
                echo "<div class='error'>Invalid product ID.</div>";
            } else {
                try {
                    $conn = getConnection();
                    $stmt = $conn->prepare("SELECT p.name, p.description, p.price, p.image_url, c.name AS category_name FROM Products p JOIN Category c ON p.category_id = c.category_id WHERE p.product_id = ?");
                    $stmt->execute([$product_id]);
                    $product = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($product) {
                        $imageUrl = $product['image_url'] && $product['image_url'] !== 'N/A' ? '/Team-Project-255/assets/images/' . $product['image_url'] : '/Team-Project-255/assets/images/coffeebeans.jpeg';
                        ?>
                        
                        <div class="product-image">
                            <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="<?php echo htmlspecialchars($product['name']); ?> product image" onerror="this.src='/Team-Project-255/assets/images/coffeebeans.jpeg'">
                        </div>
                        <div class="product-details">
                            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                            <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                            <p class="product-price">£<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
                            <button class="add-to-cart-btn" onclick="addToBasket(<?php echo htmlspecialchars($product_id); ?>)">Add to Cart</button>
                        </div>

                        <section class="product-reviews">
                            <h2>Customer Reviews</h2>
                            <?php
                                // average rating
                                $avgRatingStmt = $conn->prepare("SELECT AVG(rating) as averageRating FROM Reviews WHERE product_id = ?");
                                $avgRatingStmt->execute([$product_id]);
                                $averageRatingResult = $avgRatingStmt->fetch(PDO::FETCH_ASSOC);
                                $averageRating = round(floatval($averageRatingResult['averageRating']), 1); // Round to 1 decimal place
                            ?>
                            <?php if ($averageRating > 0): ?>
                                <div class="average-rating">
                                    Average Rating: 
                                    <div class="review-rating">
                                        <?php echo renderStarRating($averageRating); ?>
                                    </div>
                                    (<?php echo htmlspecialchars($averageRating); ?> out of 5)
                                </div>
                            <?php endif; ?>
                            <div class="reviews-container">
                                <?php
                                // reviews for the product
                                $reviewsStmt = $conn->prepare("SELECT r.review_text, r.rating, r.created_at, u.name FROM Reviews r JOIN Users u ON r.user_id = u.user_id WHERE r.product_id = ? ORDER BY r.created_at DESC");
                                $reviewsStmt->execute([$product_id]);
                                $reviews = $reviewsStmt->fetchAll(PDO::FETCH_ASSOC);

                                if ($reviews) {
                                    foreach ($reviews as $review) {
                                        ?>
                                        <div class="review-item">
                                            <h4 class="review-author"><?php echo htmlspecialchars($review['name']); ?></h4>
                                            <div class="review-rating">
                                                <?php echo renderStarRating(intval($review['rating'])); ?>
                                            </div>
                                            <p class="review-date"><?php echo htmlspecialchars(date('F j, Y', strtotime($review['created_at']))); ?></p>
                                            <p class="review-content"><?php echo htmlspecialchars($review['review_text']); ?></p>
                                        </div>
                                        <?php
                                    }
                                } else {
                                    echo "<div class='no-reviews'>No reviews yet for this product.</div>";
                                }
                                ?>
                            </div>
                        </section>
                        <?php
                    } else {
                        echo "<div class='error'>Product not found.</div>";
                    }
                } catch (PDOException $e) {
                    error_log("Database error: " . $e->getMessage());
                    echo "<div class='error'>Failed to load product details. Please try again later.</div>";
                }
            }
            ?>
        </section>
    </main>
<script>
    // add item to basket
    async function addToBasket(productId) {
        try {
            const response = await fetch('add_to_basket.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1,
                    action: 'add'
                })
            });

            const data = await response.json();
            if (data.success) {                    
                // show a message if it was successfull
                Swal.fire({
                    icon: 'success',
                    title: 'Added to Basket',
                    text: 'Item has been added to your basket',
                    showConfirmButton: false,
                    timer: 1500
                });
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Error adding to basket:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Failed to add item to basket'
            });
        }
    }
</script>
<footer>
        <p>© 2024 CAF LAB Coffee Company. All Rights Reserved. Ecommerce software by Team Expert 25</p>
    </footer>
    <?php include 'chatbot.php'; ?>
</body>
</html>
