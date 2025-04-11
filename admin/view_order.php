<?php
require_once '../includes/config.php';
$page_title = "Order Details";
require_once '../admin/admin_header.php';

if (!isset($_GET['id'])) {
    die("Order ID is required.");
}

$order_id = intval($_GET['id']);

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_order_status'])) {
        $new_status = $_POST['order_status'];
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $order_id);
        $stmt->execute();
        $_SESSION['message'] = "Order status updated!";
    } elseif (isset($_POST['delete_order'])) {
        $stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();

        $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();

        $_SESSION['message'] = "Order deleted!";
        header("Location: manage_orders.php");
        exit;
    }
}

// Get order info
$order = $conn->query("
    SELECT o.*, u.username 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    WHERE o.id = $order_id
")->fetch_assoc();

// Get order items
$items = $conn->query("
    SELECT oi.*, p.name 
    FROM order_item oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = $order_id
");
?>

<div class="card">
    <h3>Order #<?= $order['id'] ?> Details</h3>
    <p><strong>Customer:</strong> <?= htmlspecialchars($order['username']) ?></p>
    <p><strong>Date:</strong> <?= date('M j, Y H:i', strtotime($order['created_at'])) ?></p>
    <p><strong>Total:</strong> RM<?= number_format($order['total'], 2) ?></p>
    <p><strong>Payment:</strong> <?= strtoupper($order['payment_method']) ?></p>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success"><?= $_SESSION['message'] ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <form method="POST" class="mb-4">
        <label><strong>Status:</strong></label>
        <select name="order_status" class="form-select w-auto d-inline-block">
            <option value="Preparing" <?= $order['status'] == 'Preparing' ? 'selected' : '' ?>>Preparing</option>
            <option value="Shipped" <?= $order['status'] == 'Shipped' ? 'selected' : '' ?>>Shipped</option>
            <option value="Completed" <?= $order['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
            <option value="Cancelled" <?= $order['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select>

        <button type="submit" name="update_order_status" class="btn btn-secondary btn-sm">Update Status</button>
    </form>

    <h5>Ordered Products</h5>
    <table class="table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php while($item = $items->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td>RM<?= number_format($item['price'], 2) ?></td>
                <td>RM<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this order?');">
        <button type="submit" name="delete_order" class="btn btn-danger"><i class="fas fa-trash"></i> Delete Order</button>
    </form>
</div>

<?php $conn->close(); ?>
