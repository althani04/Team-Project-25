<?php
$debug_log = [];
$debug_log[] = 'process.php accessed';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$debug_log[] = 'process.php started';

// database connection
include_once __DIR__ . '/../../config/database.php';

try {
    $pdo->query('SELECT 1');
    // a debug for database connection
    $debug_log[] = 'Database connected successfully';
} catch (PDOException $e) {
    file_put_contents('error.log', 'Database connection failed: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage(), 'debug_log' => $debug_log]);
    exit;
}

$debug_log[] = 'Database connection test passed';

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect data from the user
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);
    $role = trim($_POST['role']);

    // Validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($confirmPassword) || empty($role)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.', 'debug_log' => $debug_log]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.', 'debug_log' => $debug_log]);
        exit;
    }

    if (strlen($password) < 8) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters.', 'debug_log' => $debug_log]);
        exit;
    }

    if ($password !== $confirmPassword) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match.', 'debug_log' => $debug_log]);
        exit;
    }

    if (!in_array($role, ['customer', 'admin'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid role selected.', 'debug_log' => $debug_log]);
        exit;
    }

    try {
        // Check if the email already exists
        $debug_log[] = 'Checking if email exists: ' . $email;
        $query = "SELECT * FROM Users WHERE email = :email";
        $stmt = $pdo->prepare($query);
        $debug_log[] = 'Email check query prepared';
        $stmt->execute(['email' => $email]);
        $debug_log[] = 'Email check query executed';

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => false, 'message' => 'Email already exists.', 'debug_log' => $debug_log]);
            exit;
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert the user details into the database
        $debug_log[] = 'Inserting user: ' . $email;
        $query = "INSERT INTO Users (name, email, password, role) VALUES (:name, :email, :password, :role)";
        $stmt = $pdo->prepare($query);
        $debug_log[] = 'Insert user query prepared';
        $stmt->execute([
            'name' => $firstName . ' ' . $lastName,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => $role,
        ]);
        $debug_log[] = 'Insert user query executed';

        echo json_encode(['success' => true, 'message' => 'User registered successfully.', 'debug_log' => $debug_log]);

    } catch (PDOException $e) {
        $debug_log[] = 'PDOException in signup: ' . $e->getMessage();
        echo json_encode(['success' => false, 'message' => 'Something went wrong. Please try again later. Error: ' . $e->getMessage(), 'debug_log' => $debug_log]); // User-friendly message with error details
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.', 'debug_log' => $debug_log]);
}
