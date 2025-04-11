<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'finders_keepers');

// Start session
session_start();

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set timezone (Malaysia time)
date_default_timezone_set('Asia/Kuala_Lumpur');

// Website settings
$site_name = "Finders Keepers";
$site_url = "http://localhost/project";
$currency = "RM";
$currency_code = "MYR";

// We'll require cart_functions.php at the end
?>

<?php
// Now include cart_functions.php after all other configurations
require_once 'cart_functions.php';

// Initialize cart handler
$cart = new Cart($conn);

// Initialize cart count in session if not set
if (!isset($_SESSION['cart_count'])) {
    $user_id = $_SESSION['user_id'] ?? 0;
    $_SESSION['cart_count'] = $cart->getCartCount($user_id);
}
?>