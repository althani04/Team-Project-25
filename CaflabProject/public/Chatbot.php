<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = strtolower(trim($_POST['message']));
    $responses = [
        "hello" => "Hey there! Welcome to CafLab ☕ How can I help you at CafLab today?",
        "best coffee" => "It depends on your taste! For more information visit our products page! ☕",
        "how to make espresso" => "You'll need finely ground coffee and an espresso machine. Use 18-20g of coffee and extract for about 25-30 seconds!",
        "best coffee beans" => "Arabica beans are a great choice for smooth flavor, while Robusta offers a stronger kick!",
        "types of coffee" => "There are so many! Espresso, latte, cappuccino, macchiato, and more! What’s your favorite?",
        "how much caffeine" => "An espresso shot has about 63mg of caffeine, while a regular coffee cup has around 95mg! ☕",
        "bye" => "Goodbye! Enjoy your coffee! ☕😊",
        "what is caflab?" => "Caflab is all about delivering fresh, high-quality coffee straight to your door with a focus on ethical sourcing and sustainability! ☕🌱",
        "do you offer coffee subscriptions?" => "Yes! We provide a seamless subscription service so you never run out of your favorite coffee! ☕📦",
        "is your coffee ethically sourced?" => "Absolutely! We prioritize ethical sourcing and sustainability in every cup we offer. 🌍☕",
    ];
    
    $response = $responses[$input] ?? "I'm not sure about that, but I’d love to talk about coffee! ☕";
    echo json_encode(["response" => $response]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Chatbot</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #ffffff;
            color: #3e2723;
        }
        .chat-container {
            width: 280px;
            background: #4e342e;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        .chat-box {
            height: 300px;
            overflow-y: auto;
            padding: 10px;
            border-bottom: 2px solid #795548;
            background: #d7ccc8;
            color: #3e2723;
        }
        .input-box {
            display: flex;
            padding: 10px;
            background: #6d4c41;
        }
        input {
            flex: 1;
            padding: 5px;
            border: none;
            border-radius: 5px;
            outline: none;
        }
        button {
            margin-left: 5px;
            padding: 5px 10px;
            border: none;
            background: #a1887f;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-box" id="chatBox"></div>
        <div class="input-box">
            <input type="text" id="userInput" placeholder="Ask me about CafLab...">
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>

    <script>
        function sendMessage() {
            let inputField = document.getElementById("userInput");
            let message = inputField.value;
            if (message.trim() === "") return;

            let chatBox = document.getElementById("chatBox");
            chatBox.innerHTML += `<div><strong>You:</strong> ${message}</div>`;
            inputField.value = "";
            chatBox.scrollTop = chatBox.scrollHeight;

            fetch("", {
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
