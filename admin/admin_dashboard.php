<?php
require_once '../includes/config.php';
$page_title = "Admin Dashboard";
require_once '../admin/admin_header.php';

// Secure page - only accessible to logged in admins
if(!isset($_SESSION['admin_id'])) {
    $_SESSION['message'] = "Please login to access admin area.";
    header("Location: admin_login.php");
    exit();
}
?>

<div class="container">
    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h4>Welcome, <?php echo $_SESSION['admin_username']; ?></h4>
                </div>
                <div class="card-body">
                    <p>You are logged in as Admin</p>
                    <!-- Add dashboard content here -->
                </div>
            </div>
        </div>
    </div>
</div>

<?php