<?php
require_once 'includes/config.php';

if(!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = (int)$_GET['order_id'];
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Verify order belongs to user
$query = "SELECT * FROM orders WHERE id = $order_id AND user_id = $user_id";
$result = $conn->query($query);

if($result->num_rows == 0) {
    header("Location: index.php");
    exit();
}

$order = $result->fetch_assoc();
$page_title = "Order Confirmation";
require_once 'includes/header.php';
?>

<div class="text-center py-5">
    <h1 class="display-4 text-success">Thank You!</h1>
    <p class="lead">Your order has been placed successfully.</p>
    <div class="my-4">
        <p>Order Number: <strong>#<?php echo $order['id']; ?></strong></p>
        <p>Order Total: <strong><?php echo $currency . number_format($order['total'], 2); ?></strong></p>
        <p>Order Date: <strong><?php echo date('F j, Y', strtotime($order['created_at'])); ?></strong></p>
    </div>
    <p>We've sent a confirmation email to your registered email address.</p>
    <a href="products.php" class="btn btn-primary">Continue Shopping</a>
</div>

<?php
// Get order items
$query = "SELECT oi.*, p.name, p.image 
          FROM order_item oi 
          JOIN products p ON oi.product_id = p.id 
          WHERE oi.order_id = $order_id";
$result = $conn->query($query);
?>

<h3 class="mb-4">Order Details</h3>
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php while($item = $result->fetch_assoc()): ?>
            <tr>
                <td>
                    <img src="assets/gambar/<?php echo $item['image']; ?>" width="50" class="me-2">
                    <?php echo $item['name']; ?>
                </td>
                <td><?php echo $currency . number_format($item['price'], 2); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td><?php echo $currency . number_format($item['price'] * $item['quantity'], 2); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>