<?php
//includes sql srver connection
include('includes/connect.php');
//includes all funcations 
include('function/common_function.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-Commerce</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="sty.css">
</head>

<body>
  <!-- Navbar -->
  <div class="container-fluid p-0">

    <!-- First Navbar -->
    <nav class="navbar navbar-expand-lg bg-info">
      <div class="container-fluid">
        <img src="./images/icon.png" alt="ICON" class="logo">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="display_all.php">Products</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Register</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
            <li class="nav-item">
              <a class="nav-link" href="#"><i class="fa-solid fa-cart-shopping"></i><sup>1</sup></a>
            </li>
            <li class="nav-item"><a class="nav-link" href="#">Total Price: ₹100</a></li>
          </ul>
          <!-- search -->
          <form class="d-flex" action="" method="get">
            <input class="form-control me-2" type="search" placeholder="Search" aria-label="search" name="search_data">
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
        <a class='nav-link' href='./user_area/profile.php'>
          <i class='fa-solid fa-user me-1'></i> Welcome </a></li>";
          echo "<li class='nav-item'><a class='nav-link' href='./user_area/user_login.php'>Login</a></li>";
        } else {
          echo "<li class='nav-item'>
        <a class='nav-link' href='./user_area/profile.php'>
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
      <h3>Store</h3>
      <p>Our all-in-one solution</p>
    </div>

    <!-- Product Section -->
    <div class="row">

      <!-- Product Grid -->
      <div class="col-md-10">
        <div class="row">
          <!-- Product 1 -->
          <?php
          //Calling funcation getproducts() from function\common_function.php .....
          search_product();
          //getproducts();
          get_unique_category();
          get_unique_brand();
          ?>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-md-2 bg-secondary p-0">
        <!-- Brands -->
        <ul class="navbar-nav me-auto text-center">
          <li class="nav-item bg-info">
            <a href="#" class="nav-link text-light">
              <h5>Brands</h5>
            </a>
          </li>
          <?php
          //Calling funcation getbrands() from function\common_function.php .....
          getbrands();
          ?>
        </ul>

        <!-- Categories -->
        <ul class="navbar-nav me-auto text-center">
          <li class="nav-item bg-info">
            <a href="#" class="nav-link text-light">
              <h5>Categories</h5>
            </a>
          </li>
          <?php
          //Calling funcation getcategory() from function\common_function.php .....
          getcategory();
          ?>
        </ul>
      </div>
    </div>

    <!-- Footer -->
    <?php
    //includes footer 
    include('includes/footer.php');
    ?>

  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>