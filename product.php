<?php
require_once 'includes/config.php';

if(!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = (int)$_GET['id'];
$query = "SELECT *, 
         CASE WHEN stock <= 40 THEN price * 0.9 ELSE price END AS discounted_price,
         CASE WHEN stock <= 40 THEN 1 ELSE 0 END AS has_discount
          FROM products WHERE id = $product_id";
$result = $conn->query($query);

if($result->num_rows == 0) {
    header("Location: products.php");
    exit();
}

$product = $result->fetch_assoc();
$page_title = $product['name'];
require_once 'includes/header.php';
?>

<div class="row">
    <div class="col-md-6">
        <div class="product-image-container">
            <img src="assets/gambar/<?php echo $product['image']; ?>" class="img-fluid" alt="<?php echo $product['name']; ?>">
            <?php if ($product['has_discount']): ?>
                <span class="discount-badge">10% OFF</span>
                <span class="stock-badge">Only <?php echo $product['stock']; ?> left</span>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-6">
        <h2><?php echo $product['name']; ?></h2>
        <div class="price-container mb-3">
            <?php if ($product['has_discount']): ?>
                <span class="original-price"><?php echo $currency . number_format($product['price'], 2); ?></span>
            <?php endif; ?>
            <span class="product-price <?php echo $product['has_discount'] ? 'discounted' : ''; ?>">
                <?php echo $currency . number_format($product['discounted_price'], 2); ?>
            </span>
            <?php if ($product['has_discount']): ?>
                <span class="discount-text">(You save <?php echo $currency . number_format($product['price'] - $product['discounted_price'], 2); ?>)</span>
            <?php endif; ?>
        </div>
        <p><?php echo $product['description']; ?></p>
        
        <?php if($product['stock'] > 0): ?>
            <p class="text-success">In Stock (<?php echo $product['stock']; ?> available)</p>
            <form action="cart.php" method="post" class="mt-4">
                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity:</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" style="width: 80px;">
                </div>
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <input type="hidden" name="action" value="add">
                <button type="submit" class="btn btn-warning btn-lg">Add to Cart</button>
            </form>
        <?php else: ?>
            <p class="text-danger">Out of Stock</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>