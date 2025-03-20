<?php
// database config
require_once __DIR__ . '/../config/database.php';

// site config
define('ADMIN_PATH', __DIR__);
define('SITE_URL', '/Team-Project-255');
define('ASSETS_PATH', SITE_URL . '/assets');

// email config
define('ADMIN_EMAIL', 'admin@caflab.com');
define('ADMIN_NAME', 'CAF LAB Customer Service');
define('SITE_NAME', 'CAF LAB Coffee Company');

// auth check function
function checkAdminAuth() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        // Store the current URL before redirecting
        $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
        header('Location: ' . SITE_URL . '/CaflabProject/public/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

// database connection
// database connection
function getConnection() {
    error_log("getConnection() function called"); // Add debug log

    // database config
    require_once ADMIN_PATH . '/../config/database.php';

    global $host, $db, $user, $pass, $charset, $pdo;

    try {
        // create a new PDO instance
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false, 
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ];
        $pdo = new PDO($dsn, $user, $pass, $options);

        // error logging
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        ini_set('error_log', ADMIN_PATH . '/../error.log'); // Use ADMIN_PATH for error log path

        return $pdo; // Return the PDO connection object

    } catch (PDOException $e) {
        error_log("Database connection error in getConnection function: " . $e->getMessage());
        die('Database connection failed in getConnection function. Please try again later.');
    }
}

// status badges
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'pending':
            return 'warning';
        case 'processing':
            return 'info';
        case 'shipped':
            return 'primary';
        case 'delivered':
            return 'success';
        case 'cancelled':
            return 'danger';
        default:
            return 'secondary';
    }
}

// message status badges
function getMessageStatusBadgeClass($status, $type = 'contact') {
    if ($type === 'contact') {
        switch ($status) {
            case 'new':
                return 'warning';
            case 'responded':
                return 'info';
            case 'resolved':
                return 'success';
            default:
                return 'secondary';
        }
    } else {
        switch ($status) {
            case 'pending':
                return 'warning';
            case 'approved':
                return 'success';
            case 'rejected':
                return 'danger';
            default:
                return 'secondary';
        }
    }
}

// to send email
function sendEmail($to, $subject, $message, $fromName = ADMIN_NAME, $fromEmail = ADMIN_EMAIL) {
    $headers = "From: $fromName <$fromEmail>\r\n";
    $headers .= "Reply-To: $fromEmail\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    // add email templates
    $emailBody = "Dear Customer,\n\n";
    $emailBody .= $message . "\n\n";
    $emailBody .= "Best regards,\n";
    $emailBody .= SITE_NAME . " Team";
    
    return mail($to, $subject, $emailBody, $headers);
}

// to send admin notification
function sendAdminNotification($subject, $message) {
    $headers = "From: " . SITE_NAME . " System <noreply@caflab.com>\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    return mail(ADMIN_EMAIL, "[Admin Notification] " . $subject, $message, $headers);
}

// to get email template
function getEmailTemplate($type, $data = []) {
    switch ($type) {
        case 'contact_confirmation':
            return "Thank you for contacting " . SITE_NAME . ".\n\n" .
                   "We have received your message and will respond to you shortly.\n\n" .
                   "Your message details:\n" .
                   "Subject: " . ($data['subject'] ?? 'N/A') . "\n" .
                   "Message: " . ($data['message'] ?? 'N/A') . "\n\n" .
                   "We aim to respond to all inquiries within 24-48 hours.";
            
        case 'admin_new_message':
            return "A new message has been received from " . ($data['name'] ?? 'a customer') . ".\n\n" .
                   "Subject: " . ($data['subject'] ?? 'N/A') . "\n" .
                   "Email: " . ($data['email'] ?? 'N/A') . "\n" .
                   "Email: " . ($data['email'] ?? 'N/A') . "\n" .
                   "Message: " . ($data['message'] ?? 'N/A');
            
        default:
            return '';
    }
}
?>
