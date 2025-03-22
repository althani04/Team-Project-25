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

        // Check if user exists
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'This email is not registered in our system.']);
            exit;
        }

         // Check for lockout
         if (isset($_SESSION['lockout_expiry']) && time() < $_SESSION['lockout_expiry']) {
            $remaining_time = $_SESSION['lockout_expiry'] - time();
            echo json_encode(['success' => false, 'message' => 'Too many failed login attempts. Please wait ' . ceil($remaining_time / 60) . ' minutes and try again.']);
            exit;
        }

        // Verify password using bcrypt
        if (password_verify($password, $user['password'])) {
            // Reset failed login attempts on successful login
            $_SESSION['login_attempts'] = 0; 
            unset($_SESSION['lockout_expiry']); // clear lockout

            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            
            // Store return URL before setting redirect
            $returnUrl = isset($_SESSION['return_to']) ? $_SESSION['return_to'] : 'home.php';
            
            // Clear the stored return URL
            unset($_SESSION['return_to']);
            
            // Redirect based on role and return URL
            $redirect = ($user['role'] === 'admin' && strpos($returnUrl, '/admin/') !== false)
                ? $returnUrl
                : ($user['role'] === 'admin' ? '/Team-Project-255/admin/dashboard.php' : 'home.php');

            echo json_encode([
                'success' => true,
                'message' => 'Login successful.',
                'redirect' => $redirect,
                'isAdmin' => ($user['role'] === 'admin')
            ]);
        } else {
            // Increment failed login attempts
            $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;

            // Check if lockout is needed
            if ($_SESSION['login_attempts'] > 5) {
                $_SESSION['lockout_expiry'] = time() + 300; // 5 minutes lockout
                echo json_encode(['success' => false, 'message' => 'Too many failed login attempts. Please wait 5 minutes and try again.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Incorrect password. Please try again.']);
            }
        }
    } catch (PDOException $e) {
        // logging if theres any errors and return a message to users screen
        error_log("Database error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
