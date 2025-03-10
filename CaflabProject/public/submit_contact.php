<?php
// database config
$host = 'localhost';
$db = 'caf_lab'; 
$user = 'root'; 
$pass = ''; 
$charset = 'utf8mb4';

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
    ini_set('error_log', __DIR__ . '/../error.log');

} catch (PDOException $e) {
    error_log("Database connection error: " . $e->getMessage());
    die('Database connection failed. Please try again later.');
}
?>
<?php
$debug_log = [];
$debug_log[] = 'fetch_products.php accessed';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$debug_log[] = 'fetch_products.php started';

//  database config file
// require_once __DIR__ . '/../../config/database.php'; // Removed include, database config is now inlined above

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
        error_log("Preparing to insert message into database...");
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
        error_log("Insert message query prepared.");
        
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':company' => $company,
            ':subject' => $subject,
            ':message' => $message
        ]);
        error_log("Insert message query executed successfully.");

        echo json_encode([
            'status' => 'success', 
            'message' => 'Your message has been sent successfully! We will respond to you shortly.'
        ]);

    } catch (PDOException $e) {
        error_log("PDOException in submit_contact.php: " . $e->getMessage());
        echo json_encode([
            'status' => 'error', 
            'message' => 'An error occurred while saving your message. Please try again later. PDOException: ' . $e->getMessage()
        ]);
    } catch (Exception $e) {
        error_log("General error in submit_contact.php: " . $e->getMessage());
        echo json_encode([
            'status' => 'error', 
            'message' => 'An unexpected error occurred. Please try again later. General Exception: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Invalid request method.'
    ]);
}
?>
