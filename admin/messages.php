<?php
require_once 'config.php';
checkAdminAuth();

$conn = getConnection();
$error = null;
$success = null;

// handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id']) && isset($_GET['type'])) {
    try {
        $id = $_GET['id'];
        $type = $_GET['type'];
        
        if ($type === 'contact') {
            $stmt = $conn->prepare("DELETE FROM Contact_Messages WHERE message_id = ?");
            $stmt->execute([$id]);
        } 
        
        header('Location: messages.php?success=Message deleted successfully');
        exit;
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// get filter parameters
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';
$status = $_GET['status'] ?? 'all';

// build query for contact messages
$query = "
    SELECT 
        message_id as id,
        created_at,
        name,
        email,
        subject,
        status
    FROM Contact_Messages 
    WHERE 1=1 
";

$params = [];

if ($dateFrom) {
    $query .= " AND DATE(created_at) >= ?";
    $params[] = $dateFrom;
}

if ($dateTo) {
    $query .= " AND DATE(created_at) <= ?";
    $params[] = $dateTo;
}

if ($status !== 'all') {
    if ($status === 'new') {
        $query .= " AND (status = 'new' OR status IS NULL)";
    } else if ($status === 'responded') {
        $query .= " AND status = 'responded'";
    }
}

$query .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// get counts for status badges
$stmt = $conn->query("
    SELECT 
        CASE 
            WHEN status IS NULL OR status = 'new' THEN 'new'
            ELSE status 
        END as status,
        COUNT(*) as count
    FROM Contact_Messages 
    GROUP BY 
        CASE 
            WHEN status IS NULL OR status = 'new' THEN 'new'
            ELSE status 
        END
");
$statusCounts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

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
                            <h4 class="mb-0">Messages</h4>
                            <div class="mt-2">
                                <span class="badge bg-warning me-2">
                                    New: <?= $statusCounts['new'] ?? 0 ?>
                                </span>
                                <span class="badge bg-info me-2">
                                    Responded: <?= $statusCounts['responded'] ?? 0 ?>
                                </span>
                                <span class="badge bg-success">
                                    Resolved: <?= $statusCounts['resolved'] ?? 0 ?>
                                </span>
                            </div>
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
                    
                    <!-- filters -->
                    <form method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label">Date From</label>
                                <input type="date" name="date_from" class="form-control" value="<?= $dateFrom ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Date To</label>
                                <input type="date" name="date_to" class="form-control" value="<?= $dateTo ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>All Status</option>
                                    <option value="new" <?= $status === 'new' ? 'selected' : '' ?>>New</option>
                                    <option value="responded" <?= $status === 'responded' ? 'selected' : '' ?>>Responded</option>
                                    <option value="resolved" <?= $status === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter me-1"></i> Filter
                                    </button>
                                    <a href="messages.php" class="btn btn-secondary">
                                        <i class="fas fa-undo me-1"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
<?php foreach ($messages as $message): ?>
                                    <tr>
                                        <td><?= date('M j, Y', strtotime($message['created_at'])) ?></td>
                                        <td><?= htmlspecialchars($message['name']) ?></td>
                                        <td><?= htmlspecialchars($message['email']) ?></td>
                                        <td><?= htmlspecialchars($message['subject']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= getMessageStatusBadgeClass($message['status'], 'contact') ?>">
                                                <?= ucfirst($message['status'] ?? 'new') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="message-details.php?type=contact&id=<?= $message['id'] ?>" 
                                                   class="btn btn-sm btn-info">
                                                    View
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger delete-message"
                                                        data-type="contact"
                                                        data-id="<?= $message['id'] ?>"
                                                        data-subject="<?= htmlspecialchars($message['subject']) ?>">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                
                                <?php if (empty($messages)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No messages found.</td>
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
                    window.location.href = `messages.php?action=delete&id=${id}&type=${type}`;
                }
            });
        });
    });
});
</script>

<?php include 'templates/footer.php'; ?>
