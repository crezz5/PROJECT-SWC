<?php
require_once 'includes/config.php';
$page_title = "Shop";
require_once 'includes/header.php';

// Get filters
$category_filter = isset($_GET['category']) ? $_GET['category'] : null;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Pagination
$per_page = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $per_page) - $per_page : 0;

// Build query based on filters - Modified to calculate discount
$query = "SELECT *, 
          CASE WHEN stock <= 40 THEN price * 0.9 ELSE price END AS discounted_price,
          CASE WHEN stock <= 40 THEN 1 ELSE 0 END AS has_discount
          FROM products";
          
$count_query = "SELECT COUNT(id) as total FROM products";

$where = [];
if ($category_filter) {
    $where[] = "category = '$category_filter'";
}

if (!empty($where)) {
    $query .= " WHERE " . implode(' AND ', $where);
    $count_query .= " WHERE " . implode(' AND ', $where);
}

// Add sorting
switch ($sort) {
    case 'price_asc':
        $query .= " ORDER BY discounted_price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY discounted_price DESC";
        break;
    case 'popular':
        $query .= " ORDER BY stock ASC";
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

// Category names for display
$category_names = [
    'Trading Card Games' => 'Trading Card Games',
    'Action Figures' => 'Action Figures',
    'Comics' => 'Comics',
    'Stickers' => 'Stickers',
    'Keychains' => 'Keychains'
];
?>

<div class="shop-container">
    <div class="shop-header">
        <h1>SHOP</h1>
        <div class="shop-controls">
            <div class="category-filters">
                <a href="products.php" class="filter-btn <?php echo !$category_filter ? 'active' : ''; ?>">ALL</a>
                <?php foreach ($category_names as $key => $name): ?>
                    <a href="products.php?category=<?php echo $key; ?>" 
                       class="filter-btn <?php echo $category_filter == $key ? 'active' : ''; ?>">
                        <?php echo $name; ?>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <div class="sort-options">
                <select id="sortSelect" onchange="window.location.href=this.value">
                    <option value="products.php?sort=newest<?php echo $category_filter ? '&category='.$category_filter : ''; ?>" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Newest</option>
                    <option value="products.php?sort=price_asc<?php echo $category_filter ? '&category='.$category_filter : ''; ?>" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="products.php?sort=price_desc<?php echo $category_filter ? '&category='.$category_filter : ''; ?>" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                    <option value="products.php?sort=popular<?php echo $category_filter ? '&category='.$category_filter : ''; ?>" <?php echo $sort == 'popular' ? 'selected' : ''; ?>>Popular</option>
                </select>
            </div>
        </div>
    </div>

    <div class="product-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <a href="product.php?id=<?php echo $row['id']; ?>">
                        <div class="product-image">
                            <img src="assets/gambar/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
                            <?php if ($row['has_discount']): ?>
                                <span class="discount-badge">10% OFF</span>
                                <span class="stock-badge">Only <?php echo $row['stock']; ?> left</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <h3><?php echo $row['name']; ?></h3>
                            <div class="product-meta">
                                <div class="price-container">
                                    <?php if ($row['has_discount']): ?>
                                        <span class="original-price"><?php echo $currency . number_format($row['price'], 2); ?></span>
                                    <?php endif; ?>
                                    <span class="product-price <?php echo $row['has_discount'] ? 'discounted' : ''; ?>">
                                        <?php echo $currency . number_format($row['discounted_price'], 2); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-products">
                <p>No products found matching your criteria.</p>
                <a href="products.php" class="btn btn-warning">View All Products</a>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <a href="products.php?page=<?php echo $i; ?><?php echo $category_filter ? '&category='.$category_filter : ''; ?><?php echo $sort ? '&sort='.$sort : ''; ?>" 
                   class="<?php echo $page == $i ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>