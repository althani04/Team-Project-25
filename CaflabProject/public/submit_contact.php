<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Enable error logging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('error_log', __DIR__ . '/../../error.log');

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
        echo json_encode(['status' => 'error', 'message' => implode(' ', $errors)]);
        exit;
    }

    try {
        // First, insert the message into the database
        $stmt = $pdo->prepare("
            INSERT INTO Contact_Messages (
                name, 
                email, 
                company, 
                subject, 
                message
            ) VALUES (
                :name, 
                :email, 
                NULLIF(:company, ''),
                :subject, 
                :message
            )
        ");
        
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':company' => $company,
            ':subject' => $subject,
            ':message' => $message
        ]);

        echo json_encode([
            'status' => 'success', 
            'message' => 'Your message has been sent successfully! We will respond to you shortly.'
        ]);

    } catch (PDOException $e) {
        error_log("Database error in submit_contact.php: " . $e->getMessage());
        echo json_encode([
            'status' => 'error', 
            'message' => 'An error occurred while saving your message. Please try again later.'
        ]);
    } catch (Exception $e) {
        error_log("General error in submit_contact.php: " . $e->getMessage());
        echo json_encode([
            'status' => 'error', 
            'message' => 'An unexpected error occurred. Please try again later.'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Invalid request method.'
    ]);
}
