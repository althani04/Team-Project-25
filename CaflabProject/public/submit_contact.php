<?php
include_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // sanitising and validating the input
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $company = htmlspecialchars(trim($_POST['company'] ?? ''));
    $subject = htmlspecialchars(trim($_POST['subject'] ?? ''));
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));

    $errors = [];

    if (empty($name) || strlen($name) < 3) {
        $errors[] = "Full Name must be at least 3 characters long.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }
    if (empty($subject)) {
        $errors[] = "Please select a subject.";
    }
    if (empty($message) || strlen($message) < 5) {
        $errors[] = "Message must be at least 5 characters long.";
    }

    if (!empty($errors)) {
        // returning the errora to the front end
        echo json_encode(['status' => 'error', 'message' => implode(' ', $errors)]);
        exit;
    }

    try {
        // putting the data into the contact_Messages table
        $stmt = $pdo->prepare("INSERT INTO Contact_Messages (name, email, company, subject, message) 
                               VALUES (:name, :email, :company, :subject, :message)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':company', $company);
        $stmt->bindParam(':subject', $subject);
        $stmt->bindParam(':message', $message);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Your message has been sent successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to send your message.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
