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
        } else {
          $safe_user = htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item"><a class="nav-link active" href="../index.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="../display_all.php">Products</a></li>
            <li class="nav-item"><a class="nav-link" href="user_registration.php">Register</a></li>
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
          echo "<li class='nav-item'><a class='nav-link' href='profile.php'><i class='fa-solid fa-user me-1'></i> Welcome</a></li>";
          echo "<li class='nav-item'><a class='nav-link' href='user_login.php'>Login</a></li>";
        } else {
          $safe_user = htmlspecialchars(
              
              
              
              
              
              
              
            
          
          
        
          
          
          
          
          
          
          
          $_SESSION['username'], ENT_QUOTES, 'UTF-8');
          echo "<li class='nav-item'><a class='nav-link' href='profile.php'><i class='fa-solid fa-user me-1'></i> Welcome " . $safe_user . "</a></li>";
          echo "<li class='nav-item'><a class='nav-link' href='logout.php'>Logout</a></li>";
        }
        ?>
      </ul>
    </nav>

    <!-- Page Header -->
    <div class="bg-light text-center p-3">
      <h3>Checkout</h3>
      <p>Please login or proceed with payment to complete your order</p>
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
              include(__DIR__ . '/_login_fragment.php');
            } else {
              include(__DIR__ . '/payment.php');
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
