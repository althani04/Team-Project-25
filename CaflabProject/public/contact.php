<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Caf Lab</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/contact.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

<?php 
include 'session_check.php';
include 'navbar.php'; 
?>
<?php include 'basket_include.php'; ?>

    <div class="contact-container">
        <div class="contact-details">
            <h2>Get in Touch</h2>
            <p>
                We love hearing from our coffee enthusiasts! Your feedback and questions help us improve and ensure you 
                have the best experience with Caf Lab. Whether it's a compliment, a suggestion, or even a concern, 
                every message is a chance for us to serve you better. Don't hesitate to reach out – we're 
                always listening.
            </p>
            <br>
            <div class="additional-contact-info">
                <div class="info-item">
                    <img src="../../assets/images/phone.png" alt="Phone Icon" class="info-icon">
                    <div>
                        <h3>Call Us</h3>
                        <p>(+44) 1708 567 890</p>
                    </div>
                    <br><br><br><br><br>
                </div>
                <div class="info-item">
                    <img src="../../assets/images/email.png" alt="Email Icon" class="info-icon">
                    <div>
                        <h3>Email</h3>
                        <p><a href="mailto:contact@caflab.com">contact@caflab.com</a></p> 
                    </div>
                </div>
            </div>            
        </div>

        <div class="contact-form">
            <form id="contactForm" action="submit_contact.php" method="POST">
                <div class="form-group">
                    <label for="name">Full Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" placeholder="Your name" required minlength="3">
                    <small class="error-message" id="nameError"></small>
                </div>
                <div class="form-group">
                    <label for="email">Email <span class="required">*</span></label>
                    <input type="email" id="email" name="email" placeholder="Your email address" required>
                    <small class="error-message" id="emailError"></small>
                </div>
                <div class="form-group">
                    <label for="company">Company (Optional)</label>
                    <input type="text" id="company" name="company" placeholder="Company name">
                </div>
                <div class="form-group">
                    <label for="subject">Subject <span class="required">*</span></label>
                    <select id="subject" name="subject" required>
                        <option value="" disabled selected>Select a subject</option>
                        <option value="order-inquiry">Order Inquiry</option>
                        <option value="subscription">Subscription Question</option>
                        <option value="product-feedback">Product Feedback</option>
                        <option value="website-issue">Report a Website Issue</option>
                        <option value="general-inquiry">General Inquiry</option>
                    </select>
                    <small class="error-message" id="subjectError"></small>
                </div>
                <div class="form-group">
                    <label for="message">Leave us a message <span class="required">*</span></label>
                    <textarea id="message" name="message" placeholder="Your message" required minlength="5"></textarea>
                    <small class="error-message" id="messageError"></small>
                </div>
                <div class="form-submit">
                    <button type="submit" id="submitButton">Send Message</button>
                </div>
            </form>
        </div>        
    </div>

    <script>
        document.getElementById("contactForm").addEventListener("submit", function(e) {
            e.preventDefault();

            const submitButton = document.getElementById('submitButton');
            const originalText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Sending...';

            const formData = new FormData(this);

            fetch('submit_contact.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Message Sent',
                        text: data.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        this.reset();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message,
                        confirmButtonText: 'Try Again'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong while sending your message. Please try again later.',
                    confirmButtonText: 'OK'
                });
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            });
        });
    </script>

    <?php include 'Chatbot.php'; ?>
</body>
</html>
