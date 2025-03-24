<?php
require_once 'config.php';
checkAdminAuth();

$conn = getConnection();
$error = null;
$success = null;

if (!isset($_GET['id']) || !isset($_GET['type'])) {
    header('Location: messages.php');
    exit;
}

$messageId = $_GET['id'];
$messageType = $_GET['type'];

// handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    try {
        if ($messageType === 'contact') {
            $status = $_POST['status'];
            $stmt = $conn->prepare("
                UPDATE Contact_Messages 
                SET status = ?
                WHERE message_id = ?
            ");
            $stmt->execute([$status, $messageId]);
        } else if ($messageType === 'return') {
            $status = $_POST['return_status'];
            $stmt = $conn->prepare("
                UPDATE Returns 
                SET status = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE return_id = ?
            ");
            $stmt->execute([$status, $messageId]);

            // if return is approved or rejected then update order status
            if ($status === 'approved' || $status === 'rejected') {
$stmt = $conn->prepare("
                        UPDATE Orders o
                        JOIN Returns r ON o.order_id = r.order_id
                        SET o.status = ?
                        WHERE r.return_id = ?
                    ");
$stmt = $conn->prepare("
                        UPDATE Orders o
                        JOIN Returns r ON o.order_id = r.order_id
                        SET o.status = ?
                        WHERE r.return_id = ?
                    ");
                    $stmt->execute([$status === 'approved' ? 'return_approved' : 'return_rejected', $messageId]);
                }
            }
        
        $success = "Status updated successfully";
        
        // refresh message details
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// handle email response
else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['response'])) {
    $response = trim($_POST['response']);
    $to = $_POST['email'];
    $subject = "Re: " . $_POST['subject'];
    
    try {
        // send email using helper function
        if (sendEmail($to, $subject, $response)) {
            if ($messageType === 'contact') {
                // update contact message status and save response
                $stmt = $conn->prepare("
                    UPDATE Contact_Messages 
                    SET status = 'responded',
                        admin_response = ?,
                        response_date = CURRENT_TIMESTAMP
                    WHERE message_id = ?
                ");
                $stmt->execute([$response, $messageId]);
            } else if ($messageType === 'return') {
                // update return request status and save response
                $status = $_POST['return_status'] ?? 'pending';
                $stmt = $conn->prepare("
                    UPDATE Returns 
                    SET status = ?,
                        admin_response = ?,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE return_id = ?
                ");
                $stmt->execute([$status, $response, $messageId]);

                // if return is approved or rejected then update order status
                if ($status === 'approved' || $status === 'rejected') {
                    $stmt = $conn->prepare("
                        UPDATE Orders o
                        JOIN Returns r ON o.order_id = r.order_id
                        SET o.status = ?
                        WHERE r.return_id = ?
                    ");
                    $stmt->execute([$status === 'approved' ? 'return_approved' : 'return_rejected', $messageId]);
                }
            }
            
            $success = "Response sent successfully";
        } else {
            throw new Exception("Failed to send email");
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// get message details
if ($messageType === 'contact') {
    $stmt = $conn->prepare("SELECT * FROM Contact_Messages WHERE message_id = ?");
    $stmt->execute([$messageId]);
    $message = $stmt->fetch(PDO::FETCH_ASSOC);
} else if ($messageType === 'return') {
    $stmt = $conn->prepare("
        SELECT 
            r.*,
            u.name,
            u.email,
            o.order_date,
            CONCAT('Return Request - Order #', r.order_id) as subject,
            GROUP_CONCAT(
                CONCAT(p.name, ' (Qty: ', ri.quantity, ')')
                SEPARATOR '\n'
            ) as return_items
        FROM Returns r
        JOIN Users u ON r.user_id = u.user_id
        JOIN Orders o ON r.order_id = o.order_id
        JOIN Return_Items ri ON r.return_id = ri.return_id
        JOIN Order_Items oi ON ri.order_item_id = oi.order_item_id
        JOIN Products p ON oi.product_id = p.product_id
        WHERE r.return_id = ?
        GROUP BY r.return_id
    ");
    $stmt->execute([$messageId]);
    $message = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($message) {
        $message['message'] = "Return Items:\n" . $message['return_items'] . "\n\nReason: " . $message['reason'];
        if ($message['comments']) {
            $message['message'] .= "\n\nAdditional Comments:\n" . $message['comments'];
        }
    }
}

if (!$message) {
    header('Location: messages.php');
    exit;
}

include 'templates/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <?php include 'templates/admin-menu.php'; ?>
        </div>
        
        <div class="col-md-10">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">Message Details</h4>
                        <span class="badge bg-<?= getMessageStatusBadgeClass($message['status'], $messageType) ?>">
                            <?= ucfirst($message['status']) ?>
                        </span>
                    </div>
                    <div>
                        <button type="button" 
                                class="btn btn-danger"
                                onclick="deleteMessage('<?= $messageType ?>', <?= $messageType === 'contact' ? $message['message_id'] : $message['return_id'] ?>)">
                            Delete <?= $messageType === 'contact' ? 'Message' : 'Return Request' ?>
                        </button>
<a href="<?= $messageType === 'return' ? 'returns.php' : 'messages.php' ?>" class="btn btn-secondary">Back to <?= $messageType === 'return' ? 'Returns' : 'Messages' ?></a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Contact Information</h5>
                            <table class="table">
                                <tr>
                                    <th>Name:</th>
                                    <td><?= htmlspecialchars($message['name']) ?></td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>
                                        <a href="mailto:<?= htmlspecialchars($message['email']) ?>">
                                            <?= htmlspecialchars($message['email']) ?>
                                        </a>
                                    </td>
                                </tr>
                                <?php if ($messageType === 'contact'): ?>
                                    <tr>
                                        <th>Company:</th>
                                        <td><?= htmlspecialchars($message['company'] ?? 'N/A') ?></td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <th>Order ID:</th>
                                        <td>#<?= htmlspecialchars($message['order_id']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Order Date:</th>
                                        <td><?= date('M j, Y', strtotime($message['order_date'])) ?></td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <th>Date:</th>
                                    <td><?= date('M j, Y g:i A', strtotime($message['created_at'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <form method="POST" class="d-flex align-items-center gap-2">
                                            <?php if ($messageType === 'contact'): ?>
                                                <select name="status" class="form-select form-select-sm" style="width: auto;">
                                                    <option value="new" <?= $message['status'] === 'new' ? 'selected' : '' ?>>New</option>
                                                    <option value="responded" <?= $message['status'] === 'responded' ? 'selected' : '' ?>>Responded</option>
                                                    <option value="resolved" <?= $message['status'] === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                                                </select>
                                            <?php else: ?>
                                                <select name="return_status" class="form-select form-select-sm" style="width: auto;">
                                                    <option value="pending" <?= $message['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                    <option value="approved" <?= $message['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                                                    <option value="rejected" <?= $message['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                                </select>
                                            <?php endif; ?>
                                            <button type="submit" name="update_status" class="btn btn-sm btn-primary">Update</button>
                                        </form>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Subject</h5>
                        <p class="lead"><?= htmlspecialchars($message['subject']) ?></p>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Message</h5>
                        <div class="card">
                            <div class="card-body bg-light">
                                <?= nl2br(htmlspecialchars($message['message'])) ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($message['admin_response']): ?>
                    <div class="mb-4">
                        <h5>Previous Response</h5>
                        <div class="card">
                            <div class="card-body bg-light">
                                <p class="text-muted mb-2">
                                    Sent on <?= date('M j, Y g:i A', strtotime($message['response_date'])) ?>
                                </p>
                                <?= nl2br(htmlspecialchars($message['admin_response'])) ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="mt-4">
                        <h5>Send Response</h5>
                        <form method="POST" class="needs-validation" novalidate>
                            <input type="hidden" name="email" value="<?= htmlspecialchars($message['email']) ?>">
                            <input type="hidden" name="subject" value="<?= htmlspecialchars($message['subject']) ?>">
                            
                            <div class="mb-3">
                                <select class="form-select mb-3" id="response-template">
                                    <option value="">Select a response template</option>
                                    <?php if ($messageType === 'contact'): ?>
                                        <option value="general">General Response</option>
                                        <option value="order">Order Inquiry Response</option>
                                        <option value="product">Product Information</option>
                                    <?php else: ?>
                                        <option value="return_approved">Return Request Approved</option>
                                        <option value="return_rejected">Return Request Rejected</option>
                                        <option value="return_info">Return Information Required</option>
                                    <?php endif; ?>
                                    <option value="custom">Custom Response</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <textarea class="form-control" name="response" rows="6" required></textarea>
                                <div class="invalid-feedback">
                                    Please enter your response
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Send Response</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('response-template').addEventListener('change', function() {
    const textarea = document.querySelector('textarea[name="response"]');
    const customerName = <?= json_encode($message['name']) ?>;
    
    const orderId = <?= $messageType === 'return' ? json_encode($message['order_id']) : 'null' ?>;
    
    switch(this.value) {
        case 'general':
            textarea.value = `Dear ${customerName},\n\n` +
                `Thank you for contacting CAF LAB Coffee Company. We appreciate your message.\n\n` +
                `[Your response here]\n\n` +
                `If you have any other questions, please don't hesitate to ask.\n\n` +
                `Best regards,\n` +
                `CAF LAB Customer Service`;
            break;
            
        case 'order':
            textarea.value = `Dear ${customerName},\n\n` +
                `Thank you for your inquiry about your order. I'd be happy to help you with that.\n\n` +
                `[Order details/response here]\n\n` +
                `If you need any further assistance, please don't hesitate to contact us.\n\n` +
                `Best regards,\n` +
                `CAF LAB Customer Service`;
            break;
            
        case 'product':
            textarea.value = `Dear ${customerName},\n\n` +
                `Thank you for your interest in our products. Here's the information you requested:\n\n` +
                `[Product information here]\n\n` +
                `If you have any other questions about our products, please feel free to ask.\n\n` +
                `Best regards,\n` +
                `CAF LAB Customer Service`;
            break;
            
        case 'return_approved':
            textarea.value = `Dear ${customerName},\n\n` +
                `Thank you for submitting your return request for Order #${orderId}. We have reviewed your request and are happy to approve it.\n\n` +
                `Please follow these steps to return your items:\n\n` +
                `1. Package the items securely in their original packaging if possible\n` +
                `2. Include a copy of your order number (#${orderId})\n` +
                `3. Send the package to:\n` +
                `   CAF LAB Returns\n` +
                `   [Return Address]\n\n` +
                `Once we receive your return, we will process your refund within 3-5 business days.\n\n` +
                `If you have any questions about the return process, please don't hesitate to contact us.\n\n` +
                `Best regards,\n` +
                `CAF LAB Customer Service`;
            break;
            
        case 'return_rejected':
            textarea.value = `Dear ${customerName},\n\n` +
                `Thank you for submitting your return request for Order #${orderId}. We have carefully reviewed your request, but unfortunately, we are unable to approve it at this time.\n\n` +
                `[Explain reason for rejection]\n\n` +
                `If you would like to discuss this further or have any questions, please don't hesitate to contact us.\n\n` +
                `Best regards,\n` +
                `CAF LAB Customer Service`;
            break;
            
        case 'return_info':
            textarea.value = `Dear ${customerName},\n\n` +
                `Thank you for submitting your return request for Order #${orderId}. To help us process your request, we need some additional information:\n\n` +
                `[List required information]\n\n` +
                `Please reply to this email with the requested information, and we'll be happy to continue processing your return request.\n\n` +
                `Best regards,\n` +
                `CAF LAB Customer Service`;
            break;
            
        case 'custom':
            textarea.value = `Dear ${customerName},\n\n` +
                `Thank you for your message.\n\n` +
                `\n\n` +
                `Best regards,\n` +
                `CAF LAB Customer Service`;
            break;
    }
});

function deleteMessage(type, id) {
    const messageType = type === 'contact' ? 'message' : 'return request';
    if (confirm(`Are you sure you want to delete this ${messageType}?`)) {
        window.location.href = `messages.php?action=delete&type=${type}&id=${id}`;
    }
}

// Form validation
(function() {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();
</script>

<?php include 'templates/footer.php'; ?>
