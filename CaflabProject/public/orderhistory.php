<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - Caf Lab</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/orderhistory.css">
</head>

<body>
    <header class="header">
        <a href="/" class="logo">Caf Lab</a>
    </header>

    <main class="order-history-container">
        <div class="order-history-content">
            <h1 class="order-history-title">Your Order History</h1>

            <ul class="order-history-list">
                <!-- Order 1 -->
                <li class="order-history-item">
                    <div>
                        <h4>Order #1 - January 2024</h4>
                        <p><strong>Items:</strong> Coffee Subscription Package</p>
                        <p><strong>Total:</strong> $25.99</p>
                        <p><strong>Shipping:</strong> Free</p>
                        <p><span class="status">Status: Shipped</span></p>
                    </div>

                    <button class="review-button" onclick="openModal(1)">Leave a Review</button>
                   
                </li>

                <!-- Order 2 -->
                <li class="order-history-item">
                    <div>
                        <h4>Order #2 - February 2024</h4>
                        <p><strong>Items:</strong> Coffee Subscription Package</p>
                        <p><strong>Total:</strong> $25.99</p>
                        <p><strong>Shipping:</strong> $5.99</p>
                        <p><span class="status">Status: Pending</span></p>
                    </div>
                    <button class="review-button" onclick="openModal(2)">Leave a Review</button>
                </li>

                <!-- Order 3 -->
                <li class="order-history-item">
                    <div>
                        <h4>Order #3 - March 2024</h4>
                        <p><strong>Items:</strong> Coffee Subscription Package</p>
                        <p><strong>Total:</strong> $25.99</p>
                        <p><strong>Shipping:</strong> Free</p>
                        <p><span class="status">Status: Delivered</span></p>
                    </div>
                    <button class="review-button" onclick="openModal(3)">Leave a Review</button>
                  
                </li>
            </ul>
        </div>
    </main>

    <!--Modal for Review-->
    <div id="reviewModal" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal()">x</button>
            <h2>Leave Your Review</h2>
            <!--Star Rating-->
            <div class="star-rating" id="starRating">
                <span class="star" data-index="1">★</span>
                <span class="star" data-index="2">★</span>
                <span class="star" data-index="3">★</span>
                <span class="star" data-index="4">★</span>
                <span class="star" data-index="5">★</span>
                </div>
            <textarea id="reviewText" placeholder="Write your review here..."></textarea>
            <button onclick="submitReview()">Submit Review</button>
        </div>
    </div>
   

    <footer>
        <p>© 2024 CAF LAB Coffee Company. All Rights Reserved. Ecommerce software by Team Expert 25</p>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

    <script>
                    let currentRating=0;
            const stars=document.querySelectorAll('.star');
            const reviewText=document.getElementById('reviewText');
            //Open Review Modal
            function openModal(orderId){
                document.getElementById('reviewModal').style.display='flex';
                currentRating=0;
                updateStarRating();
            }

            //Close Review Modal
            function closeModal(){
                document.getElementById('reviewModal').style.display='none';
            }

            //Handle Star Rating
            stars.forEach(star => {
                star.addEventListener('click',()=>{
                    currentRating=star.getAttribute('data-index');
                    updateStarRating();

                });
            });

            function updateStarRating(){
                stars.forEach(star => {
                    if(star.getAttribute('data-index')<=currentRating){
                        star.classList.add('filled');
                    } else{
                        star.classList.remove('filled');
                    }
                });
            }

            //Submit Review 
            function submitReview(){
                const review= reviewText.value;
                if(currentRating===0|| review.trim()===''){
                    alert('Please provide a rating and review.');
                } else {
                    alert(`Thank you for your review! Rating: ${currentRating} stars`);
                    closeModal();

                }
                
            }

        </script>

    
</body>

</html>