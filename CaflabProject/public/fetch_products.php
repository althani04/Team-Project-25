<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

//  database config file
require_once __DIR__ . '/../../config/database.php';

try {
    // get the filters from the query string
    $category = isset($_GET['category']) ? $_GET['category'] : '';
    $priceRange = isset($_GET['priceRange']) ? $_GET['priceRange'] : '';
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    // test database connection and tables
    try {
        // test categories table
        $test_query = "SELECT COUNT(*) as count FROM Category";
        $test_stmt = $pdo->query($test_query);
        $result = $test_stmt->fetch(PDO::FETCH_ASSOC);
        error_log("Total categories in database: " . $result['count']);

        // test products table
        $test_query = "SELECT COUNT(*) as count FROM Products";
        $test_stmt = $pdo->query($test_query);
        $result = $test_stmt->fetch(PDO::FETCH_ASSOC);
        error_log("Total products in database: " . $result['count']);
    } catch (PDOException $e) {
        error_log("Test query failed: " . $e->getMessage());
    }

    // prepare the base query with JOIN to get category names
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

    // add a category filter if its provided
    if (!empty($category)) {
        $query .= " AND c.name = :category";
    }

    // add price range filter if provided
    if (!empty($priceRange)) {
        list($min, $max) = explode('-', $priceRange);
        $max = ($max === '+') ? PHP_INT_MAX : $max;
        $query .= " AND p.price BETWEEN :min AND :max";
    }

    // add search filter if provided
    if (!empty($search)) {
        $query .= " AND (p.name LIKE :search OR p.description LIKE :search)";
    }

    // order by category and then by name
    $query .= " ORDER BY category_name, p.name";

    // debug: print the query and parameters
    error_log("SQL Query: " . $query);

    // prepare and execute the query
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

    // fetch all the products
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // image paths and ensure numeric values are properly typed
    foreach ($products as &$product) {
        if ($product['image_url'] && $product['image_url'] !== 'N/A') {
            // Replace backslashes with forward slashes
            $product['image_url'] = str_replace('\\', '/', $product['image_url']);
            // Extract just the filename without the path
            if (preg_match('/[^\/]+\.png$/', $product['image_url'], $matches)) {
                $filename = $matches[0];
                $product['image_url'] = '/Team-Project-255/assets/images/' . $filename;
            }
        }
        // convert numeric values to proper types
        $product['product_id'] = (int)$product['product_id'];
        $product['category_id'] = (int)$product['category_id'];
        $product['price'] = (float)$product['price'];
    }

    // debug log
    error_log("Number of products found: " . count($products));

    // return as JSON
    header('Content-Type: application/json');
    echo json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

} // make sure this closing bracket exists before catch
catch (PDOException $e) {
    // handle any errors in the database
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
