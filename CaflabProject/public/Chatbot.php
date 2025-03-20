<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Process the chatbot responses first (for AJAX requests)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = strtolower(trim($_POST['message']));

    // Define a base list of responses
    $responses = [
        "best coffee" => "It depends on your taste! Do you prefer espresso, cappuccino, or a rich dark roast? ☕",
        "how to make espresso" => "You'll need finely ground coffee and an espresso machine. Use 18-20g of coffee and extract for about 25-30 seconds!",
        "best coffee beans" => "Arabica beans are a great choice for smooth flavor, while Robusta offers a stronger kick!",
        "types of coffee" => "There are so many! Espresso, latte, cappuccino, macchiato, and more! What's your favorite?",
        "how much caffeine" => "An espresso shot has about 63mg of caffeine, while a regular coffee cup has around 95mg! ☕",
        "bye" => "Goodbye! Enjoy your coffee! ☕😊",
        "what is caflab" => "Caflab is all about delivering fresh, high-quality coffee straight to your door with a focus on ethical sourcing and sustainability! ☕🌱",
        "do you offer coffee subscriptions" => "Yes! We provide a seamless subscription service so you never run out of your favorite coffee! ☕📦",
        "is your coffee ethically sourced" => "Absolutely! We prioritize ethical sourcing and sustainability in every cup we offer. 🌍☕",
    ];

    // General greeting pattern
    $greetings = [
        "/\b(hello|hi|hey|howdy|greetings)\b/" => "Hey there! Welcome to CafLab ☕ How can I help you with coffee today?",
        "/\b(bye|goodbye|see you)\b/" => "Goodbye! Enjoy your coffee! ☕😊"
    ];

    // Check if the message matches a greeting pattern
    foreach ($greetings as $pattern => $response) {
        if (preg_match($pattern, $input)) {
            echo json_encode(["response" => $response]);
            exit;
        }
    }

    // Check if there's a direct match in the responses array
    $response = $responses[$input] ?? "I'm not sure about that, but I'd love to talk about coffee! ☕";

    echo json_encode(["response" => $response]);
    exit;
}

// For normal page view, capture the navbar
ob_start();
include 'navbar.php';
$navbar = ob_get_clean();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Chatbot</title>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="css/chatbot.css">
</head>
<body>
    <!-- Display the navbar -->
    <?php echo $navbar; ?>
    
    <div class="chat-container">
        <div class="chat-box" id="chatBox"></div>
        <div class="input-box">
            <input type="text" id="userInput" placeholder="Ask me about CAFLAB...">
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let chatBox = document.getElementById("chatBox");
            chatBox.innerHTML += `<div><strong>Bot:</strong> Hello! This is CafLab's virtual assistant. How can I help? ☕</div>`;
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