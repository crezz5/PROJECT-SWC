<?php
// Don't require config.php here since it will be included before this file

class Cart {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getCartCount($user_id) {
        $count = 0;
        
        // Count items in database for logged-in users
        if ($user_id) {
            $stmt = $this->conn->prepare("SELECT SUM(quantity) as total FROM user_carts WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $count += $row['total'] ?? 0;
        }
        
        // Count items in session for guests or additional items not in DB
        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            $count += array_sum($_SESSION['cart']);
        }
        
        return $count;
    }

    public function addItem($user_id, $product_id, $quantity = 1) {
        if ($user_id) {
            $stmt = $this->conn->prepare("SELECT id FROM user_carts WHERE user_id = ? AND product_id = ?");
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $stmt = $this->conn->prepare("UPDATE user_carts SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
                $stmt->bind_param("iii", $quantity, $user_id, $product_id);
            } else {
                $stmt = $this->conn->prepare("INSERT INTO user_carts (user_id, product_id, quantity) VALUES (?, ?, ?)");
                $stmt->bind_param("iii", $user_id, $product_id, $quantity);
            }
            $stmt->execute();
        }
        
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        $_SESSION['cart'][$product_id] = isset($_SESSION['cart'][$product_id]) ? 
            $_SESSION['cart'][$product_id] + $quantity : $quantity;
        
        return true;
    }

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

    public function getCart($user_id) {
        $cart = [];
        
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
            
            if (isset($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $product_id => $quantity) {
                    if (!isset($cart[$product_id])) {
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

    public function transferGuestCart($user_id) {
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $product_id => $quantity) {
                $this->addItem($user_id, $product_id, $quantity);
            }
            unset($_SESSION['cart']);
        }
    }

    private function getProduct($product_id) {
        $stmt = $this->conn->prepare("SELECT id, name, price, image FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}

// Initialize cart handler
global $conn; // Ensure $conn is available from config.php
$cart = new Cart($conn);
?>