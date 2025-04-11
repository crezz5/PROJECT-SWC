<?php
require_once 'includes/config.php';

// Redirect if not logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if order ID is provided
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: account.php");
    exit();
}

$order_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Verify the order belongs to the logged-in user
$query = "SELECT o.*, u.full_name, u.email, u.address 
          FROM orders o 
          JOIN users u ON o.user_id = u.id 
          WHERE o.id = ? AND o.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if(!$order) {
    $_SESSION['error'] = "Order not found or you don't have permission to view it.";
    header("Location: account.php");
    exit();
}

// Get order items with product images
$query = "SELECT oi.*, p.name, p.image, p.description, p.game_category 
          FROM order_items oi 
          JOIN products p ON oi.product_id = p.id 
          WHERE oi.order_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_items = $stmt->get_result();

// Game names for display (same as in products.php)
$game_names = [
    'genshin' => 'Genshin Impact',
    'starrail' => 'Honkai: Star Rail',
    'zenless' => 'Zenless Zone Zero'
];

$page_title = "Order #" . $order['id'] . " Details";
require_once 'includes/header.php';
?>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Order Items</h5>
            </div>
            <div class="card-body">
                <?php if($order_items->num_rows > 0): ?>
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
                                <?php while($item = $order_items->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if(!empty($item['image'])): ?>
                                                <img src="assets/images/<?php echo htmlspecialchars($item['image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                                     class="img-thumbnail" 
                                                     style="width: 80px; height: 80px; object-fit: cover;"
                                                     onerror="this.onerror=null; this.src='assets/images/default.jpg'">
                                            <?php else: ?>
                                                <img src="assets/images/default.jpg" 
                                                     alt="No image available" 
                                                     class="img-thumbnail" 
                                                     style="width: 80px; height: 80px; object-fit: cover;">
                                            <?php endif; ?>
                                            <div class="ms-3">
                                                <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                                <div class="text-muted small"><?php echo htmlspecialchars($item['description']); ?></div>
                                                <span class="badge bg-secondary mt-1">
                                                    <?php echo $game_names[$item['game_category']] ?? 'HoyoVerse'; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>RM<?php echo number_format($item['price'], 2); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>RM<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>No items found for this order.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Order Summary</h5>
            </div>
            <div class="card-body">
                <p><strong>Order #:</strong> <?php echo $order['id']; ?></p>
                <p><strong>Date:</strong> <?php echo date('F j, Y \a\t g:i a', strtotime($order['created_at'])); ?></p>
                <p><strong>Status:</strong> <span class="badge bg-<?php 
                    switch($order['status']) {
                        case 'completed': echo 'success'; break;
                        case 'processing': echo 'primary'; break;
                        case 'shipped': echo 'info'; break;
                        case 'cancelled': echo 'danger'; break;
                        default: echo 'secondary';
                    }
                ?>"><?php echo ucfirst($order['status']); ?></span></p>
                <p><strong>Payment Method:</strong> <?php echo ucfirst($order['payment_method']); ?></p>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h5>Shipping Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($order['full_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                <p><strong>Address:</strong><br><?php echo nl2br(htmlspecialchars($order['address'])); ?></p>
                <?php if(!empty($order['tracking_number'])): ?>
                    <p><strong>Tracking Number:</strong> <?php echo htmlspecialchars($order['tracking_number']); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="text-center mt-3">
    <a href="account.php" class="btn btn-secondary">Back to Account</a>
    <?php if($order['status'] == 'processing'): ?>
        <button class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">Cancel Order</button>
    <?php endif; ?>
</div>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelOrderModalLabel">Cancel Order</h5>
                <button type="button" class="btn-close" data-bs-close="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this order?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <form method="post" action="cancel_order.php">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    <button type="submit" class="btn btn-danger">Confirm Cancellation</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
[file content end]