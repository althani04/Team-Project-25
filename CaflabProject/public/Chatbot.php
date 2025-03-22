<?php
include 'session_check.php';
include 'navbar.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = strtolower(trim($_POST['message']));

    $intent_responses = [
        '/\b(hello|hi|hey|howdy|greetings)\b/i' => "Hey there! Welcome to CafLab, how can I help you with today?",
        '/(?:\bbest\b|\brecommend\b).*\bcoffee\b/i' => "It depends on your taste! Do you prefer espresso, cappuccino, or a rich dark roast?",
        '/\bespresso\b.*\b(make|how to|guide|method)\b/i' => "You'll need finely ground coffee and an espresso machine. Use 18-20g of coffee and extract for about 25-30 seconds!",
        '/\b(types of|kind of|varieties of)?\s*(coffee(s)?|beans|drinks)\b/i' => "We offer a wide variety of coffees, from single-origin beans to delicious blends and espresso drinks!",
        '/\b(how much|caffeine\s*content|caffeine level)\b.*\bcoffee\b/i' => "An espresso shot has about 63mg of caffeine, while a regular coffee cup has around 95mg! ☕",
        '/\b(what is|about|who are|tell me about)\s*caflab\b/i' => "Caflab is all about delivering fresh, high-quality coffee straight to your door with a focus on ethical sourcing and sustainability! ☕🌱",
        '/\b(coffee\s*)?subscriptions?\b.*\b(how (do|does) (it|they) work|information|details|manage|cancel|change|frequency|plans)\b/i' => "Yes! We provide a seamless subscription service so you never run out of your favorite coffee! ☕📦 You can manage your subscription in your account settings.",
        '/\b(ethically sourced|sustainable|fair trade)\s*coffee\b/i' => "Absolutely! We prioritize ethical sourcing and sustainability in every cup we offer. 🌍☕",
        '/\b(bye|goodbye|see you|later|farewell)\b/i' => "Goodbye! Enjoy your coffee! ☕😊",
        '/\b(is\s*(.*)\s*in stock|do you have\s*(.*)\s*available)\b/i' => "Please check our Products page for real-time stock availability! ☕",
        '/\b(what products do you sell|what kind of coffee do you offer)\b/i' => "We offer a variety of coffee beans, coffee pods, and cold brew! Check out our Products page to explore! ☕",
        '/\b(tell me more about your coffee|what are your coffee products like)\b/i' => "Our coffees are ethically sourced and roasted to perfection, ensuring the highest quality and flavor! ☕  You can see details on each product page.",
        '/\b(how do i create an account|sign up|register)\b/i' => "You can create an account by clicking 'Sign Up' on our homepage! It's quick and easy.",
        '/\b(forgot password|reset my password)\b/i' => "To reset your password, go to the login page and click 'Forgot Password'. Follow the instructions to reset it.",
        '/\b(update my profile|change account details|edit my information)\b/i' => "You can update your profile details in the 'Manage Account' section after logging in.",
        '/\b(view order history|see my past orders|check my orders)\b/i' => "You can view your order history in the 'Order History' section of your account.",
        '/\b(manage addresses|add address|edit address|delete address)\b/i' => "You can manage your saved addresses in the 'Manage Account' section",
        '/\b(what is my order status|check order status|where is my order)\b/i' => "To check your order status, please log into your account and go to the 'Order History' section. Click on the specific order to view its details and status.",
        '/\b(track my order|order tracking)\b/i' => "You can find tracking information for your order in the 'Order History' section once it has shipped. Click on the order to view tracking details.",
        '/\b(when will my order arrive|delivery time|how long does shipping take)\b/i' => "Delivery times vary depending on your location, but we usually ship orders within 1-2 business days. You'll see estimated delivery times during checkout and in your order confirmation."
    ];

    $response = "I'm not sure about that, but I'd love to talk about coffee! ☕";

    foreach ($intent_responses as $pattern => $res) {
        if (preg_match($pattern, $input)) {
            $response = $res;
            break;
        }
    }

    echo json_encode(["response" => $response]);
    exit;
}
?>
<?php include 'basket_include.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Chatbot</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/chatbot.css">
    
</head>
<body>
    <!-- Display the navbar -->
<?php include_once 'navbar.php'; ?>

    <div class="chat-container">
        <div class="chat-box" id="chatBox"></div>
        <div class="input-box">
            <input type="text" id="userInput" placeholder="Ask me about CAFLAB..." onkeydown="handleKeyDown(event)">
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>

    <script>
        function handleKeyDown(event) {
            if (event.key === 'Enter') {
                sendMessage();
                event.preventDefault();
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            let chatBox = document.getElementById("chatBox");
            chatBox.innerHTML += `<div><strong>Bot:</strong> Hello! This is CafLab's virtual assistant. How can I help?</div>`;
        });
        const API_KEY = "AIzaSyAHDA2-RYwF-8Z9uCIo-vUPCAjbKlatODU";
        function sendMessage() {
            let inputField = document.getElementById("userInput");
            let message = inputField.value;
            if (message.trim() === "") return;

            let chatBox = document.getElementById("chatBox");
            chatBox.innerHTML += `<div><strong>You:</strong> ${message}</div>`;
            inputField.value = "";
            chatBox.scrollTop = chatBox.scrollHeight;

            fetch("chatbot.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "message=" + encodeURIComponent(message)
            })
            .then(response => response.json())
            .then(data => {
                chatBox.innerHTML += `<div><strong>Bot:</strong> ${data.response}</div>`;
                chatBox.scrollTop = chatBox.scrollHeight;
            });
        }
    </script>
</body>
</html>
