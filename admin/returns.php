<?php
require_once 'config.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$conn = getConnection();
$error = null;
$success = null;

// handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        $id = $_GET['id'];
        
        // first delete related return items
        $stmt = $conn->prepare("DELETE FROM Return_Items WHERE return_id = ?");
        $stmt->execute([$id]);
        
        // then delete the return request
        $stmt = $conn->prepare("DELETE FROM Returns WHERE return_id = ?");
        $stmt->execute([$id]);
        
        header('Location: returns.php?success=Return deleted successfully');
        exit;
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

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
                        <div>
                            <h4 class="mb-0">Returns Management</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
                    <?php endif; ?>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Return ID</th>
                                    <th>Order ID</th>
                                    <th>Customer Name</th>
                                    <th>Return Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
$stmt = $conn->query("
    SELECT 
        r.*,
        u.name as customer_name
    FROM Returns r
    JOIN Users u ON r.user_id = u.user_id
    ORDER BY r.created_at DESC
");
$returns = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($returns)): ?>
    <tr>
        <td colspan="6" class="text-center">No returns found yet.</td>
    </tr>
<?php else: ?>
    <?php foreach ($returns as $return): ?>
    <tr>
        <td><?= htmlspecialchars($return['return_id']) ?></td>
        <td><?= htmlspecialchars($return['order_id']) ?></td>
        <td><?= htmlspecialchars($return['customer_name']) ?></td>
        <td><?= date('M j, Y', strtotime($return['created_at'])) ?></td>
        <td>
            <span class="badge bg-<?= getMessageStatusBadgeClass($return['status'], 'return') ?>">
                <?= htmlspecialchars(ucfirst($return['status'])) ?>
            </span>
        </td>
        <td>
            <a href="message-details.php?type=return&id=<?= $return['return_id'] ?>" class="btn btn-sm btn-info">
                <i class="fas fa-eye"></i> View
            </a>
            <button type="button" class="btn btn-sm btn-danger delete-message" data-id="<?= $return['return_id'] ?>" data-subject="Return Request #<?= htmlspecialchars($return['return_id']) ?>">
                <i class="fas fa-trash"></i> Delete
            </button>
        </td>
    </tr>
    <?php endforeach; ?>
<?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-message').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const subject = this.dataset.subject;
            
            Swal.fire({
                title: 'Delete Message?',
                html: `Are you sure you want to delete the message:<br><strong>${subject}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const type = this.dataset.type;
                    window.location.href = `returns.php?action=delete&id=${id}&type=return`;
                }
            });
        });
    });
});
</script>

<?php include 'templates/footer.php'; ?>
