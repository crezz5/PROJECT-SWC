<?php
require_once '../includes/config.php';
$page_title = "Manage Products";
require_once '../admin/admin_header.php';

// Initialize variables
$formData = ['id' => '', 'name' => '', 'price' => '', 'stock' => '', 'description' => '', 'image' => ''];
$isEditing = false;

// Handle all product actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_product'])) {
        $product_id = $_POST['product_id'];
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $_SESSION['message'] = "Product deleted successfully!";
    }
    elseif (isset($_POST['save_product'])) {
        $name = $_POST['name'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $description = $_POST['description'];
        $product_id = $_POST['product_id'];

        // Image handling
        $imageName = null;
        if (!empty($_FILES['image']['name'])) {
            $imageName = basename($_FILES['image']['name']);
            $uploadDir = '../assets/gambar/';
            $targetFile = $uploadDir . $imageName;

            // Allowed image types
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($_FILES['image']['type'], $allowedTypes)) {
                move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
            } else {
                $_SESSION['message'] = "Invalid file type. Only JPG, PNG, and GIF are allowed.";
                $imageName = null;
            }
        }

        if (!empty($product_id)) {
            // Update product
            if ($imageName) {
                $stmt = $conn->prepare("UPDATE products SET name=?, price=?, stock=?, description=?, image=? WHERE id=?");
                $stmt->bind_param("sdissi", $name, $price, $stock, $description, $imageName, $product_id);
            } else {
                $stmt = $conn->prepare("UPDATE products SET name=?, price=?, stock=?, description=? WHERE id=?");
                $stmt->bind_param("sdiss", $name, $price, $stock, $description, $product_id);
            }
            $_SESSION['message'] = "Product updated successfully!";
        } else {
            // Add new product
            $stmt = $conn->prepare("INSERT INTO products (name, price, stock, description, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sdiss", $name, $price, $stock, $description, $imageName);
            $_SESSION['message'] = "Product added successfully!";
        }
        $stmt->execute();
    }
    elseif (isset($_POST['edit_product'])) {
        $product_id = $_POST['product_id'];
        $result = $conn->query("SELECT * FROM products WHERE id = $product_id");
        if ($result->num_rows > 0) {
            $formData = $result->fetch_assoc();
            $isEditing = true;
        }
    }
}

// Fetch all products
$products = $conn->query("SELECT * FROM products");
?>

<div class="card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>All Products</h3>
        <button onclick="openModal()" class="btn btn-success"><i class="fas fa-plus"></i> Add New Product</button>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th>ID & Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($product = $products->fetch_assoc()): ?>
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <strong>#<?= htmlspecialchars($product['id']) ?></strong>
                        <?php if (!empty($product['image'])): ?>
                            <img src="../assets/gambar/<?= htmlspecialchars($product['image']) ?>" width="50" height="50" class="img-thumbnail">
                        <?php else: ?>
                            <span class="text-muted">No image</span>
                        <?php endif; ?>
                    </div>
                </td>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td>RM<?= number_format($product['price'], 2) ?></td>
                <td><?= htmlspecialchars($product['stock']) ?></td>
                <td>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <button type="submit" name="edit_product" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</button>
                    </form>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <button type="submit" name="delete_product" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div id="productModal" class="modal" style="<?= $isEditing ? 'display: block;' : '' ?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= $isEditing ? 'Edit Product' : 'Add New Product' ?></h5>
                <button type="button" class="btn-close" onclick="closeModal()"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="product_id" value="<?= $formData['id'] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($formData['name']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Price</label>
                        <input type="number" step="0.01" class="form-control" name="price" value="<?= htmlspecialchars($formData['price']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Stock</label>
                        <input type="number" class="form-control" name="stock" value="<?= htmlspecialchars($formData['stock']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="4"><?= htmlspecialchars($formData['description']) ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Product Image</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" name="save_product" class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('productModal').style.display = 'block';
        document.querySelector('input[name="product_id"]').value = '';
        document.querySelector('input[name="name"]').value = '';
        document.querySelector('input[name="price"]').value = '';
        document.querySelector('input[name="stock"]').value = '';
        document.querySelector('textarea[name="description"]').value = '';
        document.querySelector('input[name="image"]').value = '';
        document.querySelector('.modal-title').textContent = 'Add New Product';
    }

    function closeModal() {
        document.getElementById('productModal').style.display = 'none';
    }

    window.onclick = function(e) {
        if (e.target == document.getElementById('productModal')) {
            closeModal();
        }
    }
</script>

<?php 
$conn->close();
?>
