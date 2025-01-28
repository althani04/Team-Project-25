<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database configuration file using an absolute path
require_once __DIR__ . '/../../config/database.php';

try {
    // Get filters from the query string
    $category = isset($_GET['category']) ? $_GET['category'] : '';
    $priceRange = isset($_GET['priceRange']) ? $_GET['priceRange'] : '';
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    // Test database connection and tables
    try {
        // Test Categories table
        $test_query = "SELECT COUNT(*) as count FROM Category";
        $test_stmt = $pdo->query($test_query);
        $result = $test_stmt->fetch(PDO::FETCH_ASSOC);
        error_log("Total categories in database: " . $result['count']);

        // Test Products table
        $test_query = "SELECT COUNT(*) as count FROM Products";
        $test_stmt = $pdo->query($test_query);
        $result = $test_stmt->fetch(PDO::FETCH_ASSOC);
        error_log("Total products in database: " . $result['count']);
    } catch (PDOException $e) {
        error_log("Test query failed: " . $e->getMessage());
    }

    // Prepare the base query with JOIN to get category names
    $query = "
        SELECT 
            p.product_id,
            p.name,
            p.description,
            p.price,
            p.category_id,
            p.image_url,
            p.stock_level,
            p.size,
            c.name as category_name 
        FROM 
            Products p 
            LEFT JOIN Category c ON p.category_id = c.category_id 
        WHERE 
            1=1
    ";

    // Add category filter if provided
    if (!empty($category)) {
        $query .= " AND c.name = :category";
    }

    // Add price range filter if provided
    if (!empty($priceRange)) {
        list($min, $max) = explode('-', $priceRange);
        $max = ($max === '+') ? PHP_INT_MAX : $max;
        $query .= " AND p.price BETWEEN :min AND :max";
    }

    // Add search filter if provided
    if (!empty($search)) {
        $query .= " AND (p.name LIKE :search OR p.description LIKE :search)";
    }

    // Order by category and then by name
    $query .= " ORDER BY category_name, p.name";

    // Debug: Print the query and parameters
    error_log("SQL Query: " . $query);

    // Prepare and execute the query
    $stmt = $pdo->prepare($query);

    if (!empty($category)) {
        $stmt->bindValue(':category', $category);
        error_log("Category filter: " . $category);
    }

    if (!empty($priceRange)) {
        $stmt->bindValue(':min', (float)$min);
        $stmt->bindValue(':max', (float)$max);
        error_log("Price range filter: " . $priceRange);
    }

    if (!empty($search)) {
        $stmt->bindValue(':search', "%$search%");
        error_log("Search filter: " . $search);
    }

    $stmt->execute();

    // Fetch all products
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fix image paths and ensure numeric values are properly typed
    foreach ($products as &$product) {
        if ($product['image_url'] && $product['image_url'] !== 'N/A') {
            // Replace backslashes with forward slashes
            $product['image_url'] = str_replace('\\', '/', $product['image_url']);
            // Extract just the filename without the path
            if (preg_match('/[^\/]+\.png$/', $product['image_url'], $matches)) {
                $filename = $matches[0];
                $product['image_url'] = '../../assets/images/' . $filename;
            }
        }
        // Convert numeric values to proper types
        $product['product_id'] = (int)$product['product_id'];
        $product['category_id'] = (int)$product['category_id'];
        $product['price'] = (float)$product['price'];
    }

    // Debug: Print the number of products found
    error_log("Number of products found: " . count($products));

    // Return products as JSON with proper encoding options
    header('Content-Type: application/json');
    echo json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    // Handle database connection errors
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
