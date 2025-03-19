<?php
header("Content-Type: multipart/form-data");
include_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $type = trim($_POST['type']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $file = $_FILES['image'];



    try {
        if(empty($name) || empty($type) || empty($description) || empty($price)){
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'missing required field']);
            exit;
        }
        $stmt = $pdo->prepare("
            INSERT INTO subscription_plans 
            (name, type, description, price, image_url) 
            VALUES (:name, :type, :description, :price, :image_url)
        ");

        $success = $stmt->execute([
            'name' => $_POST['name'],
            'type' => $_POST['type'],
            'description' => $_POST['description'],
            'price' => $_POST['price'],
            'image_url' => null,
        ]);
        
        $newPlanId = $pdo->lastInsertId();
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
            $filename = 'plan_' . $newPlanId . '.' . $extension;
            $targetPath = $uploadDir . $filename;
    
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $imagePath = 'images/' . $filename;
                
                // update the image_url in database
                $pdo->prepare("UPDATE subscription_plans SET image_url = ? WHERE plan_id = ?")
                    ->execute([$imagePath, $newPlanId]);
            }
        }

        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Plan created successfully' : 'Failed to create plan',
            'plan_id' => $success ? $pdo->lastInsertId() : null,
            'name' => $_POST['name'],
            'type' => $_POST['type'],
            'description' => $_POST['description'],
            'price' => $_POST['price'],
            'image_url' => $imagePath
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

?>