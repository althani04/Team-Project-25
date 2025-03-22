<?php
ini_set('session.cookie_secure', true);
ini_set('session.cookie_httponly', true);
session_start();

// store if user was admin for redirect
$wasAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

// clear all the session variables
$_SESSION = array();

// destroy the session
session_destroy();

// If user was admin then redirect to admin login page (dashboard.php page)
if ($wasAdmin) {
    header('Location: ' . '/Team-Project-255/admin/dashboard.php');
} else {
    header('Location: login.php');
}
exit();
?>
