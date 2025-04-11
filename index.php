<?php
require_once 'includes/config.php';
$page_title = "Home";
require_once 'includes/header.php';
?>

<!-- Carousel Section -->
<div id="heroCarousel" class="carousel slide mb-5" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="assets/gambar/slide1.png" class="d-block w-100" alt="Slide 1">
            <div class="carousel-caption d-none d-md-block">
                <h1>Welcome to <?php echo $site_name; ?></h1>
                <p>Discover our exclusive collection</p>
            </div>
        </div>
        <div class="carousel-item">
            <img src="assets/gambar/slide2.png" class="d-block w-100" alt="Slide 2">
            <div class="carousel-caption d-none d-md-block">
                <h1>Premium Quality</h1>
                <p>Only the best items for you</p>
            </div>
        </div>
        <div class="carousel-item">
            <img src="assets/gambar/slide3.png" class="d-block w-100" alt="Slide 3">
            <div class="carousel-caption d-none d-md-block">
                <h1>Special Offers</h1>
                <p>Check out our discounts</p>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<h2 class="mb-4">Featured Products</h2>
<div class="row">
    <?php
    $query = "SELECT * FROM products ORDER BY RAND() LIMIT 6";
    $result = $conn->query($query);
    
    while($row = $result->fetch_assoc()):
    ?>
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <img src="assets/gambar/<?php echo $row['image']; ?>" class="card-img-top" alt="<?php echo $row['name']; ?>">
            <div class="card-body">
                <h5 class="card-title"><?php echo $row['name']; ?></h5>
                <p class="card-text"><?php echo substr($row['description'], 0, 100); ?>...</p>
                <p class="text-primary"><?php echo $currency . $row['price']; ?></p>
            </div>
            <div class="card-footer bg-white">
                <a href="product.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">View Details</a>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<div class="text-center mb-4">
    <a href="products.php" class="btn btn-warning btn-lg">Shop Now</a>
</div>


<?php require_once 'includes/footer.php'; ?>
