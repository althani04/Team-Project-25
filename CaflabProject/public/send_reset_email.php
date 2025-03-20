<?php
session_start();

// include database connection
require '../config/database.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
// Include email configuration file with credentials
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
                        //Server settings
                        $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
                        $mail->isSMTP();                                            //Send using SMTP
                        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
                        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                        $mail->Username   = SMTP_USERNAME;                     //SMTP username from config file
                        $mail->Password   = SMTP_PASSWORD;                         //SMTP password from config file
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
                        $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                        //Recipients
                        $mail->setFrom('caflab01@gmail.com', 'Caf Lab Team');
                        $mail->addAddress($email, $email);     //Add a recipient

                        //Content
                        $mail->isHTML(true);                                  //Set email format to HTML
                        $mail->Subject = 'Password Reset Request';
                        $mail->Body    = "Dear User,<br><br>You have requested to reset your password. Please click on the following link to reset your password:<br><br><a href='" . $reset_link . "'>Reset Password Link</a><br><br>This link will expire in 1 hour.<br><br>If you did not request a password reset, please ignore this email.<br><br>Sincerely,<br>Caf Lab Team";
                        $mail->AltBody = 'Dear User,\n\nYou have requested to reset your password. Please click on the following link to reset your password:\n\n' . $reset_link . '\n\nThis link will expire in 1 hour.\n\nIf you did not request a password reset, please ignore this email.\n\nSincerely,\nYour Website Team';

                        $mail->send();
                        $_SESSION['success_message'] = "A password reset link has been sent to your email address.";
                        header("Location: forgot_password.php");
                        exit();
                    } catch (Exception $e) {
                        $_SESSION['error_message'] = "PHPMailer Error: " . $mail->ErrorInfo;
                        error_log("PHPMailer Error: " . $mail->ErrorInfo);
                        header("Location: forgot_password.php");
                        exit();
                    }
                } else {
                    $_SESSION['error_message'] = "Failed to generate reset token.";
                    header("Location: forgot_password.php");
                    exit();
                }
            } catch (Exception $e) {
                $_SESSION['error_message'] = "Database error generating/storing token: " . $e->getMessage();
                header("Location: forgot_password.php");
                exit();
            }

        } finally {
           
        }
    }   

} catch (Exception $e) { // catch block for the outer try
    // log detailed error message to error log
    error_log("Password reset error in send_reset_email.php: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
    $_SESSION['error_message'] = "An error occurred during password reset. Please try again later.";
    header("Location: forgot_password.php");
    exit();

} finally {
    if ($conn) {
         $conn = null; 
    }
}
?>
