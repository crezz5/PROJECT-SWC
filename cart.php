<?php
require_once 'includes/config.php';

$page_title = "Your Shopping Cart";
require_once 'includes/header.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == 0) {
    $_SESSION['login_redirect'] = $_SERVER['REQUEST_URI'];
    
    // If trying to add a product, save that info for after login
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
        $_SESSION['cart_pending_product'] = [
            'product_id' => $_POST['product_id'],
            'quantity' => $_POST['quantity'] ?? 1,
            'action' => $_POST['action']
        ];
    }
    
    $_SESSION['message'] = "Please login to access your cart";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = (int)$_POST['product_id'];
    $action = $_POST['action'];
    
    switch ($action) {
        case 'add':
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            
            // Get product price and stock
            $product_query = "SELECT price, stock FROM products WHERE id = $product_id";
            $product_result = $conn->query($product_query);
            $product = $product_result->fetch_assoc();
            
            // Calculate discounted price
            $discounted_price = ($product['stock'] < 40) ? $product['price'] * 0.9 : $product['price'];
            
            // Check if item exists in cart
            $check_query = "SELECT * FROM user_carts WHERE user_id = $user_id AND product_id = $product_id";
            $check_result = $conn->query($check_query);
            
            if ($check_result->num_rows > 0) {
                // Update existing item
                $update_query = "UPDATE user_carts 
                                SET quantity = quantity + $quantity, 
                                    discounted_price = $discounted_price
                                WHERE user_id = $user_id AND product_id = $product_id";
                $conn->query($update_query);
            } else {
                // Add new item
                $insert_query = "INSERT INTO user_carts (user_id, product_id, discounted_price, quantity) 
                                VALUES ($user_id, $product_id, $discounted_price, $quantity)";
                $conn->query($insert_query);
            }
            $_SESSION['message'] = "Product added to cart!";
            break;
            
        case 'update':
            $quantity = (int)$_POST['quantity'];
            $update_query = "UPDATE user_carts SET quantity = $quantity 
                            WHERE user_id = $user_id AND product_id = $product_id";
            $conn->query($update_query);
            $_SESSION['message'] = "Cart updated!";
            break;
            
        case 'remove':
            $delete_query = "DELETE FROM user_carts WHERE user_id = $user_id AND product_id = $product_id";
            $conn->query($delete_query);
            $_SESSION['message'] = "Product removed from cart!";
            break;
    }
    
    // Update cart count in session
    $count_query = "SELECT SUM(quantity) as count FROM user_carts WHERE user_id = $user_id";
    $count_result = $conn->query($count_query);
    $_SESSION['cart_count'] = $count_result->fetch_assoc()['count'] ?? 0;
    
    header("Location: cart.php");
    exit();
}

// Get cart items with product details
$cart_query = "SELECT uc.*, p.name, p.image, p.price as original_price 
               FROM user_carts uc
               JOIN products p ON uc.product_id = p.id
               WHERE uc.user_id = $user_id";
$cart_result = $conn->query($cart_query);
$cart_items = $cart_result->fetch_all(MYSQLI_ASSOC);

if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-info">' . $_SESSION['message'] . '</div>';
    unset($_SESSION['message']);
}
?>

<div class="cart-container">
    <h2 class="mb-4">Your Shopping Cart</h2>
    
    <?php if (empty($cart_items)): ?>
        <div class="empty-cart">
            <p>Your cart is empty</p>
            <a href="products.php" class="btn btn-primary">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total = 0;
                    foreach ($cart_items as $item): 
                        $price = $item['discounted_price'] ?? $item['original_price'];
                        $subtotal = $price * $item['quantity'];
                        $total += $subtotal;
                        $is_discounted = isset($item['discounted_price']) && $item['discounted_price'] < $item['original_price'];
                    ?>
                    <tr>
                        <td class="product-info">
                            <img src="assets/gambar/<?php echo $item['image']; ?>" width="60" alt="<?php echo $item['name']; ?>">
                            <span><?php echo $item['name']; ?></span>
                        </td>
                        <td>
                            <?php if ($is_discounted): ?>
                                <span class="original-price"><?php echo $currency . number_format($item['original_price'], 2); ?></span><br>
                                <span class="discounted-price"><?php echo $currency . number_format($item['discounted_price'], 2); ?></span>
                                <span class="discount-badge">10% OFF</span>
                            <?php else: ?>
                                <?php echo $currency . number_format($item['original_price'], 2); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="post" class="quantity-form">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="form-control">
                                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                <input type="hidden" name="action" value="update">
                                <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                            </form>
                        </td>
                        <td><?php echo $currency . number_format($subtotal, 2); ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                <input type="hidden" name="action" value="remove">
                                <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                        <td colspan="2"><strong><?php echo $currency . number_format($total, 2); ?></strong></td>
                    </tr>
                </tbody>
            </table>
            
            <div class="cart-actions">
                <a href="products.php" class="btn btn-outline-secondary">Continue Shopping</a>
                <a href="checkout.php" class="btn btn-warning">Proceed to Checkout</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>