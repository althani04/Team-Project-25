<?php require_once __DIR__ . '/../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Caf Lab</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="<?= SITE_URL ?>/admin/vendor/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link href="<?= SITE_URL ?>/admin/vendor/css/all.min.css" rel="stylesheet">
    <!-- Navbar CSS -->
    <link href="<?= SITE_URL ?>/CaflabProject/public/css/navbar.css" rel="stylesheet">
    <!-- Admin Custom CSS -->
    <link href="<?= SITE_URL ?>/admin/css/admin.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="<?= SITE_URL ?>/admin/vendor/js/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="<?= SITE_URL ?>/admin/vendor/js/bootstrap.bundle.min.js"></script>
    <!-- Additional Head Content -->
    <?= isset($additionalHead) ? $additionalHead : '' ?>
</head>
<body>
    <!-- Include the public navbar -->
    <?php include __DIR__ . '/../../CaflabProject/public/navbar.php'; ?>
