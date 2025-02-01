<?php
session_start();
include_once __DIR__ . '/../../config/database.php';

// if post request 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // collecting the data from the request
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // validate the input fields
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Please enter both email and password.']);
        exit;
    }

    // validate the email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit;
    }

    try {
        // a check to see if the email already exists in the database
        $query = "SELECT * FROM Users WHERE email = :email";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // check if the user exists in the database and password is correct
        if ($user && password_verify($password, $user['password'])) {
            // set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            // check if there was a previous page to return to
            $returnUrl = isset($_SESSION['return_to']) ? $_SESSION['return_to'] : 'home.php';
            unset($_SESSION['return_to']); // clear the stored URL

            echo json_encode([
                'success' => true, 
                'message' => 'Login successful.',
                'redirect' => $returnUrl
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
        }
    } catch (PDOException $e) {
        // logging if theres any errors and return a message to users screen
        error_log("Database error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
