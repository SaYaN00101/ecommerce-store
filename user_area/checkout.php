<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Correct file paths
include(__DIR__ . '/../includes/connect.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout page</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <link rel="stylesheet" href="../sty.css">

  <style>
    body {
      overflow-x: hidden;
      margin: 0;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    .main-content {
      flex: 1;
    }
  </style>
</head>
<body>
  <div class="container-fluid p-0 main-content">
    <!-- First Navbar -->
    <nav class="navbar navbar-expand-lg bg-info">
      <div class="container-fluid">
        <img src="../images/icon.png" alt="ICON" class="logo">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item"><a class="nav-link active" href="../index.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="../display_all.php">Products</a></li>
            <li class="nav-item"><a class="nav-link" href="user_registation.php">Register</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
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

    <!-- Page Header -->
    <div class="bg-light text-center p-3">
      <h3></h3>
      <p></p>
    </div>

    <!-- Content -->
    <div class="row px-1">
      <div class="col-nd-12">
        <div class="row">
          <?php
            if (!isset($_SESSION['username'])) {
              if (!isset($_SESSION['return_url'])) {
                $_SESSION['return_url'] = 'checkout.php';
              }
              include('user_login.php');
            } else {
              include('payment.php');
            }
          ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <?php include('../includes/footer.php'); ?>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
