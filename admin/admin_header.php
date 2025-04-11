<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to admin login if not authenticated
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['message'] = "Please login to access admin area.";
    header("Location: admin_login.php");
    exit();
}

// Set default page title if not defined
$page_title = $page_title ?? "Admin Panel";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom admin CSS -->
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            background: #f1f1f1; 
        }
        .header { 
            background: #333; 
            color: white; 
            padding: 15px 20px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        .sidebar { 
            width: 250px; 
            background: #222; 
            color: white; 
            position: fixed; 
            height: 100%; 
            padding-top: 20px; 
        }
        .sidebar a { 
            color: white; 
            padding: 15px; 
            text-decoration: none; 
            display: block; 
            transition: 0.3s; 
        }
        .sidebar a:hover { 
            background: #444; 
        }
        .main-content { 
            margin-left: 250px; 
            padding: 20px; 
        }
        .card { 
            background: white; 
            border-radius: 5px; 
            padding: 20px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); 
            margin-bottom: 20px; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        th, td { 
            padding: 12px; 
            text-align: left; 
            border-bottom: 1px solid #ddd; 
        }
        th { 
            background-color: #f2f2f2; 
        }
        .btn { 
            padding: 6px 12px; 
            border-radius: 4px; 
            text-decoration: none; 
        }
        .btn-primary { 
            background-color: #007bff; 
            color: white; 
        }
        .btn-danger { 
            background-color: #dc3545; 
            color: white; 
        }
        .btn-success { 
            background-color: #28a745; 
            color: white; 
        }
        .status-pending { 
            color: #ffc107; 
        }
        .status-completed { 
            color: #28a745; 
        }
        .status-cancelled { 
            color: #dc3545; 
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2 style="padding: 0 15px;">Admin Panel</h2>
        <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="products_management.php"><i class="fas fa-box"></i> Manage Products</a>
        <a href="users_management.php"><i class="fas fa-users"></i> Manage Users</a>
        <a href="orders_management.php"><i class="fas fa-shopping-cart"></i> Manage Orders</a>
        <a href="../admin/logout.php" class="text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-info alert-dismissible fade show">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2"><?php echo htmlspecialchars($page_title); ?></h1>
            <div class="text-muted">
                Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
            </div>
        </div>