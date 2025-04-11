<?php
require_once '../includes/config.php';
$page_title = "Admin Registration";
require_once '../includes/header.php';

// Only allow existing admins to register new admins
if(!isset($_SESSION['admin_id'])) {
    $_SESSION['message'] = "You must be logged in as admin to access this page.";
    header("Location: admin_login.php");
    exit();
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validate
    $errors = [];
    
    if(empty($username)) {
        $errors[] = "Username is required.";
    } elseif(strlen($username) < 4) {
        $errors[] = "Username must be at least 4 characters.";
    }
    
    if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
    
    if(empty($password)) {
        $errors[] = "Password is required.";
    } elseif(strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }
    
    if($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    
    // Check if username/email exists in admin table
    $query = "SELECT id FROM admin WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $errors[] = "Username or email already exists in admin system.";
    }
    
    // If no errors, register admin
    if(empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $query = "INSERT INTO admin (username, email, password, created_at) 
                  VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $username, $email, $hashed_password);
        
        if($stmt->execute()) {
            $_SESSION['message'] = "Admin registration successful!";
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $errors[] = "Admin registration failed. Please try again.";
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4>Register New Admin</h4>
            </div>
            <div class="card-body">
                <?php if(!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach($errors as $error): ?>
                            <p><?php echo $error; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <form method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username *</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password *</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <small class="text-muted">Minimum 8 characters</small>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password *</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Register Admin</button>
                    <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>