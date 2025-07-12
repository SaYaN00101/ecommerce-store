<?php
session_start();
if (!isset($_SESSION['admin_name'])) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../sty.css">

    <style>
        .admin-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .sidebar-button {
            width: 100%;
            text-align: left;
        }

        .sidebar-button i {
            margin-right: 10px;
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.2rem;
        }

        .admin-name {
            font-weight: 600;
            margin-top: 10px;
            color: white;
        }
    </style>
</head>

<body>

    <div class="container-fluid p-0">

        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-info">
            <div class="container-fluid">
                <div class="d-flex align-items-center">
                    <a href="../index.php">

                        <img src="../images/icon.png" alt="Icon" style="width: 40px;">

                    </a>
                    <span class="navbar-brand ms-2 mb-0 h1">Admin Panel</span>
                </div>

                <div class="d-flex align-items-center">
                    <a href="../index.php" class="text-white">
                        <i class="fa-solid fa-house"></i>
                    </a>
                    <span class="navbar-text text-white ms-3">
                        <i class="fa-solid fa-user me-1"></i>
                        <?php echo htmlspecialchars($_SESSION['admin_name']); ?>
                    </span>
                </div>


            </div>
        </nav>

        <!-- Page Title -->
        <div class="bg-light py-3">
            <h3 class="text-center text-dark">Manage Store Details</h3>
        </div>

        <!-- Sidebar and Content -->
        <div class="row g-0">
            <div class="col-md-3 bg-secondary p-4 text-white text-center">
                <img src="admin_images/pngtre.png" class="admin-image mb-2" alt="Admin Image">
                <div class="admin-name">Welcome <?php echo htmlspecialchars($_SESSION['admin_name']); ?></div>

                <div class="d-grid gap-2 mt-4">
                    <a href="index.php?insert_product" class="btn btn-info text-white sidebar-button"><i class="fas fa-plus"></i> Insert Products</a>
                    <a href="index.php?view_products" class="btn btn-info text-white sidebar-button"><i class="fas fa-eye"></i> View Products</a>
                    <a href="index.php?insert_categories" class="btn btn-info text-white sidebar-button"><i class="fas fa-plus-square"></i> Insert Categories</a>
                    <a href="index.php?view_categories" class="btn btn-info text-white sidebar-button"><i class="fas fa-folder-open"></i> View Categories</a>
                    <a href="index.php?insert_brand" class="btn btn-info text-white sidebar-button"><i class="fas fa-plus-circle"></i> Insert Brands</a>
                    <a href="index.php?view_brands" class="btn btn-info text-white sidebar-button"><i class="fas fa-eye"></i> View Brands</a>
                    <a href="index.php?all_orders" class="btn btn-info text-white sidebar-button"><i class="fas fa-shopping-bag"></i> All Orders</a>
                    <a href="index.php?all_payments" class="btn btn-info text-white sidebar-button"><i class="fas fa-credit-card"></i> All Payments</a>
                    <a href="index.php?list_users" class="btn btn-info text-white sidebar-button"><i class="fas fa-users"></i> List Users</a>
                    <a href="admin_logout.php" class="btn btn-danger text-white sidebar-button"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-md-9 p-4">
                <?php
                if (isset($_GET['insert_product'])) {
                    include('insert_product.php');
                } elseif (isset($_GET['view_products'])) {
                    include('view_products.php');
                } elseif (isset($_GET['edit_product'])) {
                    include('edit_product.php');
                } elseif (isset($_GET['delete_product'])) {
                    include('delete_product.php');
                } elseif (isset($_GET['insert_categories'])) {
                    include('insert_categories.php');
                } elseif (isset($_GET['view_categories'])) {
                    include('view_categories.php');
                } elseif (isset($_GET['edit_category'])) {
                    include('edit_category.php');
                } elseif (isset($_GET['delete_category'])) {
                    include('delete_category.php');
                } elseif (isset($_GET['insert_brand'])) {
                    include('insert_brands.php');
                } elseif (isset($_GET['view_brands'])) {
                    include('view_brands.php');
                } elseif (isset($_GET['edit_brand'])) {
                    include('edit_brand.php');
                } elseif (isset($_GET['delete_brand'])) {
                    include('delete_brand.php');
                } elseif (isset($_GET['all_orders'])) {
                    include('all_orders.php');
                } elseif (isset($_GET['all_payments'])) {
                    include('all_payments.php');
                } elseif (isset($_GET['list_users'])) {
                    include('list_users.php');
                } elseif (isset($_GET['admin_logout'])) {
                    include('admin_logout.php');
                } else {
                    // Show default welcome message
                    echo '
    <div class="text-center mt-5">
        <img src="admin_images/welcome_admin.png" alt="Welcome" class="img-fluid" style="max-width: 300px;">
        <h2 class="mt-3">Welcome to Admin Dashboard</h2>
        <p class="text-muted">Use the sidebar to manage products, categories, orders, and more.</p>
    </div>';
                }
                ?>




            </div>
        </div>

        <!-- Footer -->
        <?php include('../includes/footer.php'); ?>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>