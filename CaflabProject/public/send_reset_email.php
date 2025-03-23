<?php
session_start();

// include database connection
require '../config/database.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
// include email configuration file with credentials
require '../config/email_config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$conn = getConnection(); 
if (!$conn) {
    die("Database connection failed in send_reset_email.php: " . error_get_last()['message']);
}

try { // try block for the outer try
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];

        // validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error_message'] = "Invalid email format.";
            header("Location: forgot_password.php");
            exit();
        }

        // check if email exists in the database
        $stmt = null;
        try {
            $stmt = $conn->prepare("SELECT user_id FROM Users WHERE email = ?");
            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . print_r($conn->errorInfo(), true));
            }
            $stmt->bindValue(1, $email);
            if (!$stmt->execute()) {
                throw new Exception("Statement execution failed: " . print_r($stmt->errorInfo(), true));
            }
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $user = $result;

            if (!$user) {
                $_SESSION['error_message'] = "Email address not found.";
                header("Location: forgot_password.php");
                exit();
            }

            $user_id = $user['user_id'];

            // generate unique token
            $token = bin2hex(random_bytes(50));
            $expiry_date = date("Y-m-d H:i:s", strtotime("+1 hour"));

            // store token in password_resets table
            $stmt = null;

            try {
                $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expiry_date) VALUES (?, ?, ?)");

                if (!$stmt) {
                    throw new Exception("Prepare statement failed: " . print_r($conn->errorInfo(), true));
                }

                $stmt->bindValue(1, $user_id);
                $stmt->bindValue(2, $token);
                $stmt->bindValue(3, $expiry_date);

                if ($stmt->execute()) {
            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/Team-Project-255/CaflabProject/public/reset_password.php?token=" . $token; // Correct absolute URL

                    $mail = new PHPMailer(true);

                    try {
                        // server settings
                        $mail->SMTPDebug = SMTP::DEBUG_OFF;                 
                        $mail->isSMTP();                                          
                        $mail->Host       = 'smtp.gmail.com';                
                        $mail->SMTPAuth   = true;                                  
                        $mail->Username   = SMTP_USERNAME;                    
                        $mail->Password   = SMTP_PASSWORD;                        
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;           
                        $mail->Port       = 587;                               

                        //recipients
                        $mail->setFrom('caflab01@gmail.com', 'Caf Lab Team');
                        $mail->addAddress($email, $email);     

                        //content
                        $mail->isHTML(true);                              
                        $mail->Subject = 'Password Reset Request';
                        $mail->Body    = "Dear User,<br><br>You have requested to reset your password. Please click on the following link to reset your password:<br><br><a href='" . $reset_link . "'>Reset Password Link</a><br><br>This link will expire in 1 hour.<br><br>If you did not request a password reset, please ignore this email.<br><br>Sincerely,<br>Caf Lab Team";
                        $mail->AltBody = 'Dear User,\n\nYou have requested to reset your password. Please click on the following link to reset your password:\n\n' . $reset_link . '\n\nThis link will expire in 1 hour.\n\nIf you did not request a password reset, please ignore this email.\n\nSincerely,\nYour Website Team';

                        $mail->send();
                        echo "success"; // send success response back to forgot_password.php
                        exit();
                    } catch (Exception $e) {
                        http_response_code(500); 
                        echo "PHPMailer Error: " . $mail->ErrorInfo; 
                        error_log("PHPMailer Error: " . $mail->ErrorInfo);
                        exit();
                    }
                } else {
                    http_response_code(500);
                    echo "Failed to generate reset token.";
                    exit();
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo "Database error generating/storing token: " . $e->getMessage();
                exit();
            }

        } finally {
           
        }
    } else {
        http_response_code(400); 
        echo "Invalid request method.";
        exit();
    }   

} catch (Exception $e) { // catch block for the outer try
    // log detailed error message to error log
    error_log("Password reset error in send_reset_email.php: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
    http_response_code(500);
    echo "An error occurred during password reset. Please try again later.";
    exit();

} finally {
    if ($conn) {
         $conn = null; 
    }
}
?>
