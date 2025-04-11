<?php
require_once 'config.php';

class Cart {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Add item to cart (database and session)
    public function addItem($user_id, $product_id, $quantity = 1) {
        // Check if user is logged in
        if ($user_id) {
            // Check if product already in cart
            $stmt = $this->conn->prepare("SELECT id FROM user_carts WHERE user_id = ? AND product_id = ?");
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                // Update quantity if exists
                $stmt = $this->conn->prepare("UPDATE user_carts SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
                $stmt->bind_param("iii", $quantity, $user_id, $product_id);
            } else {
                // Add new item if not exists
                $stmt = $this->conn->prepare("INSERT INTO user_carts (user_id, product_id, quantity) VALUES (?, ?, ?)");
                $stmt->bind_param("iii", $user_id, $product_id, $quantity);
            }
            $stmt->execute();
        }
        
        // Also update session cart
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
        
        return true;
    }

    // Remove item from cart
    public function removeItem($user_id, $product_id) {
        if ($user_id) {
            $stmt = $this->conn->prepare("DELETE FROM user_carts WHERE user_id = ? AND product_id = ?");
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
        }
        
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
        
        return true;
    }

    // Update item quantity
    public function updateQuantity($user_id, $product_id, $quantity) {
        if ($quantity <= 0) {
            return $this->removeItem($user_id, $product_id);
        }
        
        if ($user_id) {
            $stmt = $this->conn->prepare("UPDATE user_carts SET quantity = ? WHERE user_id = ? AND product_id = ?");
            $stmt->bind_param("iii", $quantity, $user_id, $product_id);
            $stmt->execute();
        }
        
        $_SESSION['cart'][$product_id] = $quantity;
        return true;
    }

    // Get user's cart (combines database and session for guests)
    public function getCart($user_id) {
        $cart = [];
        
        // Get from database if logged in
        if ($user_id) {
            $stmt = $this->conn->prepare("
                SELECT p.id, p.name, p.price, p.image, uc.quantity 
                FROM user_carts uc
                JOIN products p ON uc.product_id = p.id
                WHERE uc.user_id = ?
            ");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $cart[$row['id']] = [
                    'name' => $row['name'],
                    'price' => $row['price'],
                    'image' => $row['image'],
                    'quantity' => $row['quantity']
                ];
            }
            
            // Merge with session cart if exists
            if (isset($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $product_id => $quantity) {
                    if (!isset($cart[$product_id])) {
                        // Add session items not in database
                        $product = $this->getProduct($product_id);
                        if ($product) {
                            $cart[$product_id] = [
                                'name' => $product['name'],
                                'price' => $product['price'],
                                'image' => $product['image'],
                                'quantity' => $quantity
                            ];
                        }
                    }
                }
            }
        } else {
            // For guests, use session only
            if (isset($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $product_id => $quantity) {
                    $product = $this->getProduct($product_id);
                    if ($product) {
                        $cart[$product_id] = [
                            'name' => $product['name'],
                            'price' => $product['price'],
                            'image' => $product['image'],
                            'quantity' => $quantity
                        ];
                    }
                }
            }
        }
        
        return $cart;
    }

    // Transfer guest cart to user after login
    public function transferGuestCart($user_id) {
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $product_id => $quantity) {
                $this->addItem($user_id, $product_id, $quantity);
            }
            unset($_SESSION['cart']);
        }
    }

    // Helper function to get product details
    private function getProduct($product_id) {
        $stmt = $this->conn->prepare("SELECT id, name, price, image FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}

// Initialize cart handler
$cart = new Cart($conn);
?>