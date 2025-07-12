<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include(__DIR__ . '/../includes/connect.php');
include(__DIR__ . '/../function/common_function.php');
include(__DIR__ . '/../function/profile_functions.php');

// Redirect if user not logged in
if (!isset($_SESSION['username'])) {
    header('Location: ../user_area/user_login.php');
    exit();
}

// Fetch user info from DB
$username = $_SESSION['username'];
$user_query = "SELECT * FROM `user_table` WHERE user_name = '$username'";
$user_result = mysqli_query($con, $user_query);
$user_data = mysqli_fetch_assoc($user_result);
$user_image = $user_data['user_image'] ?? 'default.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body { background: #f5f5f5; }
    .sidebar {
      height: 100vh;
      background-color: #2f3e46;
      color: white;
    }
    .sidebar a {
      color: white;
      text-decoration: none;
      padding: 10px;
      display: block;
    }
    .sidebar a:hover { background-color: #1f2a32; }
    .profile-img {
      width: 100%;
      max-height: 250px;
      object-fit: cover;
      border-radius: 8px;
    }
    .header {
      background-color: #6c757d;
      color: white;
      padding: 10px 20px;
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
<div class="container-fluid p-0">
  <!-- First Navbar -->
  <nav class="navbar navbar-expand-lg bg-info">
    <div class="container-fluid">
      <img src="../images/icon.png" alt="ICON" class="logo" style="width:40px;height:40px;">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link active" href="../index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="../display_all.php">Products</a></li>
          <li class="nav-item"><a class="nav-link" href="../user_area/user_registation.php">Register</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
          <li class="nav-item">
            <a class="nav-link" href="../cart.php">
              <i class="fa-solid fa-cart-shopping"></i>
              <sup><?php cart_item(); ?></sup>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../user_area/checkout.php">Total Price: <?php total_cart_price(); ?>/-</a>
          </li>
        </ul>
        <form class="d-flex" action="../search_product.php" method="get">
          <input class="form-control me-2" type="search" placeholder="Search" name="search_data">
          <input type="submit" value="Search" class="btn btn-outline-light" name="search_data_product">
        </form>
      </div>
    </div>
  </nav>

  <!-- Second Navbar -->
  <nav class="navbar navbar-expand-lg bg-secondary">
    <ul class="navbar nav me-auto">
              <?php
        if (!isset($_SESSION['username'])) {
          echo "<li class='nav-item'>
        <a class='nav-link' href='./profile.php'>
          <i class='fa-solid fa-user me-1'></i> Welcome </a></li>";
          echo "<li class='nav-item'><a class='nav-link' href='./user_area/user_login.php'>Login</a></li>";
        } else {
          echo "<li class='nav-item'>
        <a class='nav-link' href='./profile.php'>
          <i class='fa-solid fa-user me-1'></i> Welcome " . $_SESSION['username'] . "
        </a>
      </li>";

          echo "<li class='nav-item'><a class='nav-link' href='./user_area/logout.php'>Logout</a></li>";
        }
        ?>
    </ul>
  </nav>
</div>

<!-- MAIN CONTENT -->
<div class="container-fluid mt-4">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 sidebar p-3">
      <h5 class="text-center bg-info py-2">Your Profile</h5>
      <div class="text-center mb-3">
        <img src="user_images/<?= $user_image ?>" alt="Profile Picture" class="profile-img">
      </div>
      <a href="profile.php?pending_orders">Pending Orders</a>
      <a href="profile.php?edit_account">Edit Account</a>
      <a href="profile.php?my_orders">My Orders</a>
      <a href="profile.php?delete_account">Delete Account</a>
      <a href="../user_area/logout.php">Logout</a>
    </div>

    <!-- Main Section -->
    <div class="col-md-9 p-4">
      <h2 class="text-center">Store</h2>
      <p class="text-center">Communication is at the heart of e-commerce and community</p>

      <?php
      if (isset($_GET['edit_account'])) {
        show_edit_account_form(); 
      } elseif (isset($_GET['my_orders'])) {
        get_order_details(); 
      } elseif (isset($_GET['delete_account'])) {
        show_delete_account_form(); 
      } elseif (isset($_GET['pending_orders'])) {
        show_pending_orders();
      } else {
        echo "<h4 class='text-center mt-5'>Welcome to your profile dashboard</h4>";
      }
      ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
