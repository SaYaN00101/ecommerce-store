<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if (isset($_SESSION['message'])) {
  echo $_SESSION['message'];
  unset($_SESSION['message']); // clear it after showing once
}
//includes sql server connection
include('includes/connect.php');
//includes all functions 
include('function/common_function.php');
// Message variable to store success or error message
$message = '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-Commerce cart details</title>

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
            <li class="nav-item"><a class="nav-link active" aria-current="page" href="index.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="display_all.php">Products</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Register</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
            <li class="nav-item">
              <a class="nav-link" href="cart.php"><i class="fa-solid fa-cart-shopping"></i><sup><?php cart_item(); ?></sup></a>
            </li>
          </ul>
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

    <!-- Cart Table -->
    <div class="container">
      <div class="row">

        <?php
        $user_ip = getUserIP();

        // Update cart quantities
        if (isset($_POST['update_cart'])) {
          foreach ($_POST['qty'] as $product_id => $quantity) {
            $update_cart = "UPDATE `card_details` SET quantity='$quantity' WHERE ip_address='$user_ip' AND product_id='$product_id'";
            mysqli_query($con, $update_cart);
          }
          echo "<script>window.location.href='cart.php';</script>";
        }

        // Remove selected items
        if (isset($_POST['remove_cart'])) {
          if (!empty($_POST['removeitem'])) {
            foreach ($_POST['removeitem'] as $remove_id) {
              $delete_query = "DELETE FROM `card_details` WHERE product_id='$remove_id' AND ip_address='$user_ip'";
              mysqli_query($con, $delete_query);
            }
            echo "<script>window.location.href='cart.php';</script>";
          }
        }

        $total = 0;
        $cart_query = "SELECT * FROM `card_details` WHERE ip_address='$user_ip'";
        $result = mysqli_query($con, $cart_query);

        if (mysqli_num_rows($result) > 0) {
        ?>
        <form action="" method="post">
          <table class="table text-center table-bordered">
            <thead>
              <tr>
                <th>Product Title</th>
                <th>Product Image</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Remove</th>
                <th colspan="2">Operations</th>
              </tr>
            </thead>
            <tbody>
              <?php
              while ($row = mysqli_fetch_array($result)) {
                $product_id = $row['product_id'];
                $quantity = $row['quantity'];
                $select_products = "SELECT * FROM `products` WHERE product_id='$product_id'";
                $result_products = mysqli_query($con, $select_products);
                while ($row_product_price = mysqli_fetch_array($result_products)) {
                  $price_table = $row_product_price['product_price'];
                  $product_title = $row_product_price['product_title'];
                  $product_image1 = $row_product_price['product_image1'];
                  $product_total = $price_table * $quantity;
                  $total += $product_total;
              ?>
              <tr>
                <td><?php echo $product_title ?></td>
                <td><img src="admin_area/product_images/<?php echo $product_image1; ?>" alt="product" class="cart_imag" style="width: 50px; height: auto;"></td>
                <td><input type="number" name="qty[<?php echo $product_id; ?>]" value="<?php echo $quantity; ?>" class="form-input w-50" min="1"></td>
                <td><?php echo $product_total ?>/-</td>
                <td><input type="checkbox" name="removeitem[]" value="<?php echo $product_id; ?>"></td>
                <td colspan="2">
                  <input type="submit" class="bg-info px-3 py-2 border-0 mx-1" value="Update Cart" name="update_cart">
                  <input type="submit" class="bg-danger px-3 py-2 border-0 mx-1 text-white" value="Remove" name="remove_cart">
                </td>
              </tr>
              <?php
                }
              }
              ?>
            </tbody>
          </table>

          <!-- Subtotal Section -->
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-3 bg-light rounded shadow-sm mb-4">
  <h4 class="mb-0">Subtotal: <span class="text-success"><?php echo $total; ?>/-</span></h4>
  
  <div class="d-flex gap-2">
    <a href="index.php" class="btn btn-info">Continue Shopping</a>
    <a href="./user_area/checkout.php" class="btn btn-dark">Checkout</a>
  </div>
</div>

        </form>
        <?php
        } else {
          echo "<h2 class='text-center text-danger mt-5'>Your cart is empty 😔</h2>";
          echo "<div class='text-center mb-5'><a href='index.php' class='btn btn-primary'>Start Shopping</a></div>";
        }
        ?>

      </div>
    </div>

    <!-- Footer -->
    <?php //include('includes/footer.php'); ?>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>