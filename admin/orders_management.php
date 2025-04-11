<?php
require_once '../includes/config.php';
$page_title = "Manage Orders";
require_once '../admin/admin_header.php';

// Fetch all orders with user information
$orders = $conn->query("
    SELECT o.id, o.created_at, o.total, o.status, u.username 
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
");
?>

<div class="card">
    <h3>All Orders</h3>
    
    <table class="table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($order = $orders->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($order['id']) ?></td>
                <td><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                <td><?= htmlspecialchars($order['username']) ?></td>
                <td>RM<?= number_format($order['total'], 2) ?></td>
                <td class="status-<?= strtolower($order['status']) ?>">
                    <?= htmlspecialchars($order['status']) ?>
                </td>
                <td>
                    <a href="../admin/view_order.php?id=<?= $order['id'] ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye"></i> View
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php 
$conn->close();
?>
