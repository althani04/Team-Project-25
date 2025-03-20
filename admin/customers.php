<?php
require_once 'config.php';
checkAdminAuth();

$conn = getConnection();
$error = null;
$success = null;

// handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        $conn->beginTransaction();
        
        $userId = $_GET['id'];
        
        
        // delete the customer/admin 
        $stmt = $conn->prepare("DELETE FROM Users WHERE user_id = ?");
        $stmt->execute([$userId]);
        
        $conn->commit();
        $success = 'Customer deleted successfully';
        
    } catch (Exception $e) {
        $conn->rollBack();
        $error = $e->getMessage();
    }
}

// get customers with their order counts and total spend
$stmt = $conn->query("
    SELECT 
        u.*,
        COUNT(DISTINCT o.order_id) as order_count,
        COALESCE(SUM(o.total_price), 0) as total_spend,
        MAX(o.created_at) as last_order_date
    FROM Users u
    LEFT JOIN Orders o ON u.user_id = o.user_id
WHERE u.role IN ('customer', 'admin')
    GROUP BY u.user_id
    ORDER BY u.date_created DESC
");
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'templates/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <?php include 'templates/admin-menu.php'; ?>
        </div>
        
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
<h4 class="mb-0">Users</h4>
                        <a href="customer-form.php" class="btn btn-primary">Add User</a>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Orders</th>
                                    <th>Role</th>
                                    <th>Total Spend</th>
                                    <th>Last Order</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($customers as $customer): ?>
                                    <tr>
                                        <td>
                                            <a href="customer-details.php?id=<?= $customer['user_id'] ?>" class="text-decoration-none">
                                                 <?= htmlspecialchars($customer['name']) ?>
                                            </a>
                                        </td>
                                          <td><?= htmlspecialchars($customer['email']) ?></td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?= $customer['order_count'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($customer['role'] === 'admin'): ?>
                                                <span class="badge bg-danger">Admin</span>
                                            <?php elseif ($customer['role'] === 'customer'): ?>
                                                <span class="badge bg-info">Customer</span>
                                            <?php else: ?>
                                                <?= htmlspecialchars($customer['role']) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>£<?= number_format($customer['total_spend'], 2) ?></td>
                                        <td>
                                            <?= $customer['last_order_date'] 
                                                ? date('M j, Y', strtotime($customer['last_order_date']))
                                                : 'Never' ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="customer-details.php?id=<?= $customer['user_id'] ?>" 
                                                   class="btn btn-sm btn-info">
                                                    View
                                                </a>
                                                <a href="customer-form.php?id=<?= $customer['user_id'] ?>" 
                                                   class="btn btn-sm btn-primary">
                                                    Edit
                                                </a>
                                                <button type="button"
                                                        class="btn btn-sm btn-danger delete-customer"
                                                        data-id="<?= $customer['user_id'] ?>"
                                                        data-name="<?= htmlspecialchars($customer['name']) ?>">
                                                     Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                
                                <?php if (empty($customers)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No customers found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const successAlert = document.querySelector('.alert-success');
    if (successAlert) {
      setTimeout(function() {
        successAlert.style.display = 'none';
      }, 3000); // 3 seconds
    }
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-customer').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            
            Swal.fire({
                title: 'Delete User?',
                html: `Are you sure you want to delete <strong>${name}</strong>?<br>
                       Only the user account will be deleted.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `customers.php?action=delete&id=${id}`;
                }
            });
        });
    });
});
</script>

<?php include 'templates/footer.php'; ?>
