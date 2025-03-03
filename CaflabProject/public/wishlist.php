<?php
session_start();

// Initialize wishlist if not set
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

// Add item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item'])) {
    $item = trim($_POST['item']);
    if (!empty($item)) {
        $_SESSION['wishlist'][] = $item;
    }
    header("Location: wishlist.php");
    exit();
}

// Remove item
if (isset($_GET['remove'])) {
    $index = $_GET['remove'];
    if (isset($_SESSION['wishlist'][$index])) {
        array_splice($_SESSION['wishlist'], $index, 1);
    }
    header("Location: wishlist.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #d2b48c;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            max-width: 280px;
            background: #6f4e37;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
            text-align: center;
            color: #fff;
        }
        h2 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        input {
            width: 75%;
            padding: 6px;
            font-size: 12px;
            margin: 5px 0;
            border: none;
            border-radius: 4px;
            outline: none;
        }
        button {
            padding: 6px 12px;
            font-size: 12px;
            border: none;
            background: #c4a484;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background: #b08a69;
        }
        ul {
            list-style: none;
            padding: 0;
            margin: 10px 0 0 0;
        }
        li {
            background: #8b5a2b;
            margin: 3px 0;
            padding: 8px;
            font-size: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 4px;
        }
        .remove {
            background: #e06666;
            padding: 3px 7px;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            font-size: 10px;
            text-decoration: none;
        }
        .remove:hover {
            background: #cc0000;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Wishlist</h2>
        <form method="POST">
            <input type="text" name="item" placeholder="Add item">
            <button type="submit">+</button>
        </form>
        <ul>
            <?php foreach ($_SESSION['wishlist'] as $index => $item): ?>
                <li>
                    <?= htmlspecialchars($item) ?> 
                    <a href="?remove=<?= $index ?>" class="remove">x</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
