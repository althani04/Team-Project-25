<?php
// database connection
include_once __DIR__ . '/../../config/database.php';

try {
    $pdo->query('SELECT 1');
    // a debug for database connection
    file_put_contents('debug.log', 'Database connected successfully' . PHP_EOL, FILE_APPEND);
} catch (PDOException $e) {
    file_put_contents('error.log', 'Database connection failed: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

// a check to see if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // colelcting data from the user
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);
    $role = trim($_POST['role']);

    // logging the data
    file_put_contents('debug.log', json_encode($_POST) . PHP_EOL, FILE_APPEND);

    // validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($confirmPassword) || empty($role)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit;
    }

    if (strlen($password) < 8) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters.']);
        exit;
    }

    if ($password !== $confirmPassword) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
        exit;
    }

    if (!in_array($role, ['customer', 'admin'])) { // ensure only valid roles are allowed
        echo json_encode(['success' => false, 'message' => 'Invalid role selected.']);
        exit;
    }

    try {
        // a check to see if the email already exists
        $query = "SELECT * FROM Users WHERE email = :email";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['email' => $email]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => false, 'message' => 'Email already exists.']);
            exit;
        }

        // hashing the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // inserting the details enetered by user into the database
        $query = "INSERT INTO Users (name, email, password, role) VALUES (:name, :email, :password, :role)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'name' => $firstName . ' ' . $lastName,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => $role,
        ]);

        echo json_encode(['success' => true, 'message' => 'User registered successfully.']);
    } catch (PDOException $e) {
        // logging any sql errors for debugging
        file_put_contents('error.log', 'Insert failed: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
