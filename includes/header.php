<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title . " | " . $site_name; ?></title>
    <link rel="stylesheet" href="<?php echo $site_url; ?>/assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
               <a class="navbar-brand" href="http://localhost/hzq/index.php">
                    <span style="font-weight: 700; font-size: 1.5rem; color: #fff;">FK</span>
               </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item"><a class="nav-link" href="http://localhost/hzq/index.php">Home</a></li>
                        
                        <!-- Categories Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Items
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="categoriesDropdown">
                                 <li><a class="dropdown-item" href="products.php?category=all">All Products</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="products.php?category=Trading Card Games">Trading Card</a></li>
                                <li><a class="dropdown-item" href="products.php?category=Action Figures">Action Figures</a></li>
                                <li><a class="dropdown-item" href="products.php?category=Comics">Comics</a></li>
                                <li><a class="dropdown-item" href="products.php?category=Stickers">Stickers</a></li>
                                <li><a class="dropdown-item" href="products.php?category=Keychains">Keychains</a></li>
                            </ul>
                        </li>
                        
                         <li class="nav-item"><a class="nav-link" href="super_sales.php">Super sales</a></li>
                    </ul>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="cart.php">
                                Cart <span class="badge bg-primary">
                                    <?php 
                                    // Initialize cart count
                                    $cart_count = 0;
                                    
                                    // Check if user is logged in
                                    if(isset($_SESSION['user_id']) && isset($conn)) {
                                        // For logged-in users, get count from database only
                                        $user_id = $_SESSION['user_id'];
                                        $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM user_carts WHERE user_id = ?");
                                        $stmt->bind_param("i", $user_id);
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        
                                        if($row = $result->fetch_assoc()) {
                                            $cart_count = (int)$row['total'];
                                        }
                                    } else {
                                        // For guests, get count from session only
                                        if(isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                                            $cart_count = array_sum($_SESSION['cart']);
                                        }
                                    }
                                    
                                    echo $cart_count > 0 ? $cart_count : '0';
                                    ?>
                                </span>
                            </a>
                        </li>
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <li class="nav-item"><a class="nav-link" href="account.php">Account</a></li>
                            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                        <?php else: ?>
                            <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                            <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main class="container my-4">