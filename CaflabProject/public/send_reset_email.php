<?php
// Include the database connection file
include('db_config.php');  // Ensure this path is correct

// Check if the email is provided in the POST request
if (isset($_POST['email'])) {
    $email = $_POST['email'];

    // Generate a random password reset token
    $token = bin2hex(random_bytes(50));  // Generate a random token

    // Set the expiry date for the token (1 hour from now)
    $expiry_date = date("Y-m-d H:i:s", strtotime('+1 hour'));

    try {
        // Prepare the SQL statement to insert the reset token into the password_resets table
        $sql = "INSERT INTO password_resets (user_id, token, expiry_date) 
                VALUES ((SELECT user_id FROM Users WHERE email = :email), :token, :expiry_date)";

        // Prepare and execute the query
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':email' => $email,
            ':token' => $token,
            ':expiry_date' => $expiry_date
        ]);

        // Send the password reset email
        // In a real application, replace this with your actual email sending logic
        $reset_link = "https://yourwebsite.com/reset_password.php?token=" . $token;

        // For demonstration purposes, we'll simply print the reset link.
        // You can use mail() or any email sending library here (e.g., PHPMailer).
        mail($email, "Password Reset Request", "Click the following link to reset your password: $reset_link");

        echo "A password reset link has been sent to your email address.";

    } catch (PDOException $e) {
        // If there's an error, show the error message
        echo "Error: " . $e->getMessage();
    }
} else {
    // If email is not provided in the POST request, display an error message
    echo "Please provide a valid email address.";
}
?>




