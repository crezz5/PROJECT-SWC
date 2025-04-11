<?php
require_once 'includes/config.php';
$page_title = "Checkout";

// Redirect if not logged in
if(!isset($_SESSION['user_id'])) {
    $_SESSION['redirect'] = 'checkout.php';
    header("Location: login.php");
    exit();
}

// Get user's cart items from database
$user_id = $_SESSION['user_id'];
$cart_query = "SELECT uc.*, p.name, p.price as original_price, p.stock 
               FROM user_carts uc
               JOIN products p ON uc.product_id = p.id
               WHERE uc.user_id = $user_id";
$cart_result = $conn->query($cart_query);
$cart_items = $cart_result->fetch_all(MYSQLI_ASSOC);

// Redirect if cart is empty
if(empty($cart_items)) {
    header("Location: cart.php");
    exit();
}

require_once 'includes/header.php';

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate payment method
    $valid_methods = ['credit_card', 'paypal', 'cod', 'online_banking', 'tng_wallet'];
    $payment_method = $_POST['payment_method'] ?? '';
    
    if(!in_array($payment_method, $valid_methods)) {
        $_SESSION['error'] = "Please select a valid payment method";
        header("Location: checkout.php");
        exit();
    }

    // Process payment and create order
    $total = 0;
    
    // Calculate total with discounts
    foreach($cart_items as $item) {
        $price = $item['discounted_price'] ?? $item['original_price'];
        $total += $price * $item['quantity'];
    }
    
    // Insert order with payment method
    $payment_method = $conn->real_escape_string($payment_method);
    $query = "INSERT INTO orders (user_id, total, payment_method) 
              VALUES ($user_id, $total, '$payment_method')";
    $conn->query($query);
    $order_id = $conn->insert_id;
    
    // Insert order items with correct prices
    foreach($cart_items as $item) {
        $price = $item['discounted_price'] ?? $item['original_price'];
        $insert_query = "INSERT INTO order_item (order_id, product_id, quantity, price) 
                         VALUES ($order_id, {$item['product_id']}, {$item['quantity']}, $price)";
        $conn->query($insert_query);
        
        // Update product stock
        $update_query = "UPDATE products SET stock = stock - {$item['quantity']} WHERE id = {$item['product_id']}";
        $conn->query($update_query);
    }
    
    // Clear cart from database
    $delete_query = "DELETE FROM user_carts WHERE user_id = $user_id";
    $conn->query($delete_query);
    
    // Update cart count in session
    $_SESSION['cart_count'] = 0;
    
    // Redirect to thank you page
    header("Location: order_confirmation.php?order_id=$order_id");
    exit();
}

// Get user info
$query = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($query);
$user = $result->fetch_assoc();
?>

<h2 class="mb-4">Checkout</h2>

<?php if(isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h4>Shipping Information</h4>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" 
                               value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Shipping Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3" required><?php 
                            echo htmlspecialchars($user['address'] ?? ''); 
                        ?></textarea>
                    </div>
                    
                    <h5 class="mt-4">Payment Method</h5>
                    <div class="payment-methods">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="credit_card" value="credit_card">
                            <label class="form-check-label" for="credit_card">
                                <i class="fas fa-credit-card"></i> Credit Card
                            </label>
                        </div>
                        <div id="credit_card_info" class="payment-details mb-3 ps-4">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="card_number" class="form-label">Card Number</label>
                                    <input type="text" class="form-control" id="card_number" placeholder="1234 5678 9012 3456">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="expiry" class="form-label">Expiry</label>
                                    <input type="text" class="form-control" id="expiry" placeholder="MM/YY">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="cvv" class="form-label">CVV</label>
                                    <input type="text" class="form-control" id="cvv" placeholder="123">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal">
                            <label class="form-check-label" for="paypal">
                                <i class="fab fa-paypal"></i> PayPal
                            </label>
                        </div>
                        
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="online_banking" value="online_banking">
                            <label class="form-check-label" for="online_banking">
                                <i class="fas fa-university"></i> Online Banking
                            </label>
                        </div>
                        <div id="online_banking_info" class="payment-details mb-3 ps-4">
                            <div class="mb-3">
                                <label for="bank_name" class="form-label">Bank Name</label>
                                <select class="form-select" id="bank_name">
                                    <option value="">Select Bank</option>
                                    <option value="maybank">Maybank</option>
                                    <option value="cimb">CIMB</option>
                                    <option value="public">Public Bank</option>
                                    <option value="rhb">RHB Bank</option>
                                    <option value="hongleong">Hong Leong Bank</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="tng_wallet" value="tng_wallet">
                            <label class="form-check-label" for="tng_wallet">
                                <i class="fas fa-wallet"></i> Touch 'n Go eWallet
                            </label>
                        </div>
                        
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                            <label class="form-check-label" for="cod">
                                <i class="fas fa-money-bill-wave"></i> Cash On Delivery (COD)
                            </label>
                        </div>
                        <div id="cod_info" class="payment-details mb-3 ps-4">
                            <div class="alert alert-info">
                                Pay cash when your order arrives. An additional RM5 processing fee applies.
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-warning btn-lg mt-3">Place Order</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4>Order Summary</h4>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php
                    $total = 0;
                    foreach($cart_items as $item): 
                        $price = $item['discounted_price'] ?? $item['original_price'];
                        $subtotal = $price * $item['quantity'];
                        $total += $subtotal;
                        $is_discounted = isset($item['discounted_price']) && $item['discounted_price'] < $item['original_price'];
                    ?>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><?php echo $item['name']; ?></span>
                            <span class="badge bg-primary rounded-pill"><?php echo $item['quantity']; ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <?php if ($is_discounted): ?>
                                <small class="text-muted"><?php echo $currency . number_format($item['original_price'], 2); ?></small>
                                <span class="text-danger"><?php echo $currency . number_format($price, 2); ?></span>
                            <?php else: ?>
                                <span><?php echo $currency . number_format($price, 2); ?></span>
                            <?php endif; ?>
                        </div>
                    </li>
                    <?php endforeach; ?>
                    
                    <?php if(isset($_POST['payment_method']) && $_POST['payment_method'] == 'cod'): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>COD Processing Fee</span>
                            <span><?php echo $currency . number_format(5, 2); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Total</strong>
                            <strong><?php echo $currency . number_format($total + 5, 2); ?></strong>
                        </li>
                    <?php else: ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Total</strong>
                            <strong><?php echo $currency . number_format($total, 2); ?></strong>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
// Show/hide payment details based on selection
document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', function() {
        // Hide all payment details first
        document.querySelectorAll('.payment-details').forEach(detail => {
            detail.style.display = 'none';
        });
        
        // Show selected payment details
        const selectedDetail = document.getElementById(this.id + '_info');
        if(selectedDetail) {
            selectedDetail.style.display = 'block';
        }
        
        // Update COD fee in summary
        if(this.id === 'cod') {
            // You would need to refresh the order summary here
            // This would typically be done with AJAX in a real application
        }
    });
});

// Initialize - hide all payment details except COD
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.payment-details').forEach(detail => {
        detail.style.display = 'none';
    });
    document.getElementById('cod_info').style.display = 'block';
});
</script>

<?php require_once 'includes/footer.php'; ?>