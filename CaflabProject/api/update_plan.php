<?php
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: multipart/form-data');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plan_id = isset($_GET['id']) ? trim($_GET['id']) : null;
    $name = trim($_POST['name']);
    $type = trim($_POST['type']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $file = $_FILES['image'];

    try {
        $stmt = $pdo->prepare("
            UPDATE subscription_plans 
            SET name = :name, type = :type, description = :description, price = :price, image_url = :image_url
            WHERE plan_id = :plan_id
        ");

        $success = $stmt->execute([
            'plan_id' => $plan_id,
            'name' => $_POST['name'],
            'type' => $_POST['type'],
            'description' => $_POST['description'],
            'price' => $_POST['price'],
            'image_url' => null,
        ]);

        $imagePath = null;
        if (!empty($_FILES['image'])) {
            $uploadDir = __DIR__ . '../../public/images/'; // fix url
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
    
            $file = $_FILES['image'];
            $allowedTypes = ['image/jpeg', 'image/png'];
            $maxSize = 2 * 1024 * 1024; // 2MB
    
            if (!in_array($file['type'], $allowedTypes)) {
                throw new Exception('only jpeg and png images are allowed');
            }
    
            if ($file['size'] > $maxSize) {
                throw new Exception('file size should be less than 2MB');
            }
    
            // use the plan_id as part of the filename to avoid overwriting existing files
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'plan_' . $plan_id . '.' . $extension;
            $targetPath = $uploadDir . $filename;
    
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $imagePath = 'images/' . $filename;
                
                // update the image_url in database
                $pdo->prepare("UPDATE subscription_plans SET image_url = ? WHERE plan_id = ?")
                    ->execute([$imagePath, $plan_id]);
            }
        }

        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Plan updated successfully' : 'Failed to update plan',
            'plan_id' => $plan_id,
            'name' => $_POST['name'],
            'type' => $_POST['type'],
            'description' => $_POST['description'],
            'price' => $_POST['price'],
            'image_url' => $imagePath
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
