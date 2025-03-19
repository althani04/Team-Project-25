<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = strtolower(trim($_POST['message']));
    
    // Define a base list of responses
    $responses = [
       "best coffee" => "It depends on your taste! Do you prefer espresso, cappuccino, or a rich dark roast? ☕",
"how to make espresso" => "You'll need finely ground coffee and an espresso machine. Use 18-20g of coffee and extract for about 25-30 seconds!",
"best coffee beans" => "Arabica beans are a great choice for smooth flavor, while Robusta offers a stronger kick!",
"types of coffee" => "There are so many! Espresso, latte, cappuccino, macchiato, and more! What’s your favorite?",
"how much caffeine" => "An espresso shot has about 63mg of caffeine, while a regular coffee cup has around 95mg! ☕",
"bye" => "Goodbye! Enjoy your coffee! ☕😊",
"what is caflab" => "Caflab is all about delivering fresh, high-quality coffee straight to your door with a focus on ethical sourcing and sustainability! ☕🌱",
"do you offer coffee subscriptions" => "Yes! We provide a seamless subscription service so you never run out of your favorite coffee! ☕📦",
"is your coffee ethically sourced" => "Absolutely! We prioritize ethical sourcing and sustainability in every cup we offer. 🌍☕",
"how do I subscribe to Caflab" => "You can easily subscribe on our website and choose the coffee you love, then receive it on a regular basis! ☕📅",
"what makes caflab different" => "We prioritize high-quality, sustainably sourced coffee and offer personalized subscription plans to fit your tastes! 🌱☕",
"do you roast your coffee beans" => "Yes! We roast our beans to order, ensuring the freshest flavor possible for every cup! ☕🔥",
"can I choose my coffee roast" => "Of course! You can choose from light, medium, and dark roasts depending on your flavor preference! ☕✨",
"what is the best coffee for beginners" => "A medium roast is a great start! It's smooth with balanced flavors that aren't too strong or bitter. ☕😊",
"do you offer decaf options" => "Yes! We have high-quality decaf coffee beans for those who prefer to enjoy the taste without the caffeine boost! ☕😌",
"what type of grinder should I use" => "For espresso, a burr grinder is best. For drip coffee, a medium grind works well. ☕🔧",
"is your packaging eco-friendly" => "Absolutely! We use sustainable and recyclable packaging to reduce our environmental impact. 🌍♻️",
"how fresh is your coffee" => "Our coffee is roasted and shipped fresh to order, ensuring it reaches you at its peak flavor! ☕✨",
"can I gift a coffee subscription" => "Yes! You can easily send a coffee subscription as a gift to anyone who loves great coffee! 🎁☕",
"how can I contact Caflab" => "You can reach us via our website's contact form or email us directly. We’re always happy to help! 💬",
"do you have flavored coffee" => "Yes, we offer some delicious flavored coffee options, like vanilla and caramel! ☕🍮",
"is caflab available internationally" => "At the moment, we're serving select countries. Check our website for delivery options to your location! 🌍🚚",
"do you sell coffee accessories" => "Yes! We offer a variety of coffee-related accessories, including grinders, mugs, and espresso machines! ☕🔧",
"what’s the best coffee for a latte" => "A medium roast or espresso works perfectly for a smooth and rich latte! ☕🥛",
"can I buy individual coffee bags" => "Yes! You can purchase individual bags of your favorite coffee from our selection. ☕💼",
"is your coffee organic" => "Many of our coffees are certified organic, providing a cleaner and healthier cup. 🌱☕",
"how long does coffee stay fresh" => "Coffee is best consumed within 2-3 weeks of roasting to ensure maximum flavor. ☕🕰️",
"how should I store my coffee beans" => "Store your beans in an airtight container in a cool, dry place to preserve their freshness! ☕🔒",
"what is cold brew coffee" => "Cold brew is coffee steeped in cold water for 12-24 hours, resulting in a smoother, less acidic drink! ☕❄️",
"can I customize my coffee order" => "Absolutely! You can select your roast, grind, and even flavor preferences when you order! ☕✏️",
"do you offer sample packs" => "Yes, we offer sample packs so you can try a variety of our coffees before committing to a full bag! ☕🛍️",
"how do I brew a French press" => "For French press, use a coarse grind, add hot water (about 200°F), and let it steep for 4 minutes before pressing! ☕",
"is your coffee fair trade certified" => "Yes! We work with fair trade certified farmers to ensure ethical sourcing practices. 🌍☕",
"how do I make iced coffee" => "Brew your coffee hot, let it cool, then pour over ice and add milk or sweetener if desired. Enjoy! ☕❄️",
"what’s the best coffee for a cappuccino" => "Espresso is the classic base for a cappuccino, topped with steamed milk and a dusting of cocoa! ☕🍫",
"do you offer flavored syrups" => "Yes, we offer a variety of flavored syrups like vanilla, caramel, and hazelnut! ☕🍯",
"can I return coffee beans" => "We stand behind our coffee quality, but if you're unsatisfied, please reach out and we'll find a solution! ☕👍",
"do you have any dairy-free options" => "Yes! We offer dairy-free alternatives like almond, oat, and soy milk for your coffee drinks. 🌱🥛",
"how do I make a latte" => "Brew a shot of espresso and add steamed milk. Top with a small layer of foam. Enjoy your creamy latte! ☕🥛",
"can I buy gift cards" => "Yes! We offer gift cards that can be redeemed for coffee, subscriptions, and more. 🎁☕",
"do you offer a dark roast" => "Yes! We have dark roast options that bring out a bold, intense flavor for coffee lovers who like it strong! ☕🔥",
"how do I make an Americano" => "Brew a shot of espresso and add hot water to it. It’s a simple, smooth drink! ☕💧",
"what is the best grind size for drip coffee" => "A medium grind works best for drip coffee to ensure balanced extraction and flavor. ☕⚙️",
"is there sugar in your coffee" => "Our coffees are sugar-free, but you can always add your own sweeteners if you prefer! ☕🍯",
"can I cancel my subscription" => "Yes! You can cancel your subscription anytime through our website or by contacting us. ☕❌",
"how much is shipping" => "Shipping costs depend on your location. Check our website for details on shipping rates! 🚚💸",
"do you offer espresso machines" => "Yes! We offer high-quality espresso machines and accessories to make your coffee experience complete. ☕⚙️",
"can I get a refund" => "We want you to be happy with your purchase! If you're not satisfied, please reach out for a solution. ☕🔄",
"how do I brew pour-over coffee" => "Use a medium grind, pour hot water in a circular motion over the coffee, and let it drip through slowly. ☕⏳",
"do you sell coffee mugs" => "Yes! We offer stylish, high-quality mugs for your coffee enjoyment. ☕🖤",
"can I buy in bulk" => "Yes, we offer bulk purchasing options for larger orders of our delicious coffee! ☕📦",
"what is a macchiato" => "A macchiato is a shot of espresso with a small amount of milk, giving it a strong, rich flavor! ☕🥛",
"how do I make coffee stronger" => "Increase the coffee-to-water ratio or use a finer grind for a bolder taste! ☕💪",
"how do I make a flat white" => "A flat white is similar to a latte but with a higher ratio of espresso to milk, giving it a richer flavor! ☕🥛",
"how do I contact Caflab" => "For any other enquiries please visit contact us page!"

    ];
     
      // General greeting pattern
      $greetings = [
        "/\b(hello|hi|hey|howdy|greetings)\b/" => "Hey there! Welcome to CafLab ☕ How can I help you with coffee today?",
        "/\b(bye|goodbye|see you)\b/" => "Goodbye! Enjoy your coffee! ☕😊"
    ];

    // Check if the message matches a greeting pattern
    foreach ($greetings as $pattern => $response) {
        if (preg_match($pattern, $input)) {
            $response = $response;
            echo json_encode(["response" => $response]);
            exit;
        }
    }


    // Check if there's a direct match in the responses array
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
    <link rel="stylesheet" href="chatbot.css"> <!-- Link to CSS file -->
    
    <style>
        
    </style>
</head>
<body>
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

            fetch("", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "message=" + encodeURIComponent(message) + "&key=" + API_KEY
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
