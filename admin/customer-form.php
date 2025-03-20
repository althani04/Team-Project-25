<?php
require_once 'config.php';
checkAdminAuth();

$conn = getConnection();
$customer = null;
$error = null;
$success = null;
$generatedPassword = null; 

// handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'] ?: null;
        $address = $_POST['address'] ?: null;
        $postcode = $_POST['postcode'] ?: null;
        
        if (isset($_POST['id'])) {
            // update existing customer
            $stmt = $conn->prepare("
                UPDATE Users 
                SET name = ?, 
                    email = ?, 
                    phone_number = ?,
                    address_line = ?,
                    postcode = ?,
                    date_updated = CURRENT_TIMESTAMP
                WHERE user_id = ? AND role = 'customer'
            ");
            
            $stmt->execute([$name, $email, $phone, $address, $postcode, $_POST['id']]);
            $success = 'Customer updated successfully';
            
        } else {
            // check if email already exists
            $stmt = $conn->prepare("SELECT user_id FROM Users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                throw new Exception('Email address already exists');
            }
            
            // generate a random password
            $password = bin2hex(random_bytes(8));
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $generatedPassword = $password; // Assign to generatedPassword

            
            // create new customer
            $stmt = $conn->prepare("
                INSERT INTO Users (
                    name, email, password, role, phone_number, 
                    address_line, postcode, first_login,
                    date_created, date_updated
                ) VALUES (
                    ?, ?, ?, 'customer', ?, 
                    ?, ?, TRUE,
                    CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
                )
            ");
            
            $stmt->execute([$name, $email, $hashedPassword, $phone, $address, $postcode]);
            $success = 'Customer created successfully. Initial password: ' . $password;
        }
        
        // Store success message and password in session
        $_SESSION['success'] = $success;
        $_SESSION['password'] = $password; // Store the password in session
        // Redirect to customer-form.php to display the message
        header('Location: customer-form.php');
        exit;

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// check for success message in session
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    $generatedPassword = $_SESSION['password']; 
    unset($_SESSION['success']); // clear message after displaying it once
    unset($_SESSION['password']); // clear password after displaying it once
}

include 'templates/header.php';
?>

<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Admin Menu</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="dashboard.php" class="list-group-item list-group-item-action">Dashboard</a>
                    <a href="products.php" class="list-group-item list-group-item-action">Products</a>
                    <a href="orders.php" class="list-group-item list-group-item-action">Orders</a>
                    <a href="customers.php" class="list-group-item list-group-item-action active">Customers</a>
                    <a href="categories.php" class="list-group-item list-group-item-action">Categories</a>
                    <a href="messages.php" class="list-group-item list-group-item-action">Messages</a>
                    <a href="reviews.php" class="list-group-item list-group-item-action">Reviews</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                                <h4 class="mb-0"><?= $customer ? 'Edit' : 'Add' ?> User</h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <?= htmlspecialchars($success) ?>
                            <?php if (isset($generatedPassword)): ?>
                                <br><b>Initial Password:</b> <code><?= htmlspecialchars($generatedPassword) ?></code>
                                <br>Please ensure to communicate this password to the new customer securely.
                            <?php endif; ?>
                        </div>

                        <script>
                          document.addEventListener('DOMContentLoaded', function() {
                            const successAlert = document.querySelector('.alert-success');
                            if (successAlert) {
                              setTimeout(function() {
                                successAlert.style.display = 'none';
                              }, 5000); // 5 seconds
                            }
                          });
                        </script>
                    <?php endif; ?>

                    <form action="customer-form.php" method="POST">
                        <?php if ($customer): ?>
                            <input type="hidden" name="id" value="<?= $customer['user_id'] ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= htmlspecialchars($customer['name'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($customer['email'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?= htmlspecialchars($customer['phone_number'] ?? '') ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="2"><?= htmlspecialchars($customer['address_line'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="postcode" class="form-label">Postcode</label>
                            <input type="text" class="form-control" id="postcode" name="postcode" 
                                   value="<?= htmlspecialchars($customer['postcode'] ?? '') ?>">
                        </div>
                        
                        <?php if (!$customer): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                A random password will be generated for the new customer. They will be required to change it on first login.
                            </div>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between">
                            <a href="customers.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <?= $customer ? 'Update' : 'Create' ?> Customer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
