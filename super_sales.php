<?php
require_once 'includes/config.php';
$page_title = "Super Sales";
require_once 'includes/header.php';

// Get sort filter only (category filter removed)
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Pagination
$per_page = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $per_page) - $per_page : 0;

// Build query for super sales - only products with stock < 40 with 10% discount
$query = "SELECT *, 
          (price * 0.9) AS discounted_price,
          price AS original_price,
          1 AS has_discount
          FROM products 
          WHERE stock < 40";
          
$count_query = "SELECT COUNT(id) as total FROM products WHERE stock < 40";

// Add sorting
switch ($sort) {
    case 'price_asc':
        $query .= " ORDER BY discounted_price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY discounted_price DESC";
        break;
    case 'popular':
        $query .= " ORDER BY stock ASC"; // Lower stock = more popular
        break;
    default: // 'newest'
        $query .= " ORDER BY created_at DESC";
        break;
}

$query .= " LIMIT $start, $per_page";

// Get total products
$total = $conn->query($count_query)->fetch_assoc()['total'];
$pages = ceil($total / $per_page);

// Get products
$result = $conn->query($query);

?>

<div class="shop-container">
    <div class="shop-header">
        <h1>SUPER SALES</h1>
        <p class="sales-subtitle">Limited Stock Items - 10% OFF!</p>
        

    <div class="product-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <a href="product.php?id=<?php echo $row['id']; ?>">
                        <div class="product-image">
                            <img src="assets/gambar/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
                            <span class="discount-badge">10% OFF</span>
                            <span class="stock-badge">Only <?php echo $row['stock']; ?> left</span>
                        </div>
                        <div class="product-info">
                            <h3><?php echo $row['name']; ?></h3>
                            <div class="product-meta">
                                <div class="price-container">
                                    <span class="original-price"><?php echo $currency . number_format($row['original_price'], 2); ?></span>
                                    <span class="product-price discounted"><?php echo $currency . number_format($row['discounted_price'], 2); ?></span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-products">
                <p>No super sale items found.</p>
                <a href="products.php" class="btn btn-primary">View All Products</a>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <a href="super_sales.php?page=<?php echo $i; ?><?php echo $sort ? '&sort='.$sort : ''; ?>" 
                   class="<?php echo $page == $i ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>