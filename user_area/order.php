<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include(__DIR__ . '/../includes/connect.php');
include(__DIR__ . '/../function/common_function.php');

if (isset($_GET['user_id']) && isset($_POST['payment_method'])) {
  $user_id = $_GET['user_id'];
  $payment_method = $_POST['payment_method'];

  // Getting user IP
  $user_ip = getUserIP();

  // Initialize values
  $total_price = 0;
  $invoice_number = mt_rand(100000, 999999);
  $order_status = 'pending';

  $cart_query = "SELECT * FROM `card_details` WHERE ip_address='$user_ip'";
  $cart_result = mysqli_query($con, $cart_query);
  $total_products = mysqli_num_rows($cart_result);

  if ($total_products === 0) {
    popup_redirect('Your cart is empty.', '../index.php', 3000, 'info', 'top-center');
    exit();
  } else {
    // Calculate total price
    while ($cart_row = mysqli_fetch_array($cart_result)) {
      $product_id = $cart_row['product_id'];
      $quantity = $cart_row['quantity'];

      $product_query = "SELECT product_price FROM `products` WHERE product_id = $product_id";
      $product_result = mysqli_query($con, $product_query);
      $product_data = mysqli_fetch_array($product_result);
      $price = $product_data['product_price'];

      $total_price += $price * $quantity;
    }

    // Insert into user_orders
    $insert_order = "INSERT INTO `user_orders` 
      (user_id, amount_due, invoice_number, total_products, order_status, payment_method) 
      VALUES 
      ('$user_id', '$total_price', '$invoice_number', '$total_products', '$order_status', '$payment_method')";
    $run_order = mysqli_query($con, $insert_order);

    if ($run_order) {
      // Insert into orders_pending
      $cart_result = mysqli_query($con, $cart_query);
      while ($cart_row = mysqli_fetch_array($cart_result)) {
        $product_id = $cart_row['product_id'];
        $quantity = $cart_row['quantity'];

        $insert_pending = "INSERT INTO `orders_pending` 
          (user_id, invoice_number, product_id, quantity, order_status) 
          VALUES 
          ('$user_id', '$invoice_number', '$product_id', '$quantity', '$order_status')";
        mysqli_query($con, $insert_pending);
      }

      // Clear cart
      $clear_cart = "DELETE FROM `card_details` WHERE ip_address='$user_ip'";
      mysqli_query($con, $clear_cart);

      //  Redirect based on payment method
      if ($payment_method === 'online') {
        header("Location: confirm_payment.php");
        exit();
      } else {
        // Cash on Delivery: show success page with options
?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
          <meta charset="UTF-8">
          <title>Order Placed</title>
          <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>

        <body class="bg-light">
          <div class="container mt-5">
            <div class="card shadow p-4 text-center mx-auto" style="max-width: 500px;">
              <h4 class="text-success mb-3"><i class="fa-solid fa-circle-check"></i> Order Placed Successfully!</h4>
              <p>Your order has been placed using <strong>Cash on Delivery</strong>.</p>
              <div class="d-grid gap-2 col-8 mx-auto mt-4">
                <a href="../index.php" class="btn btn-primary">🏠 Go to Home</a>
                <a href="checkout.php" class="btn btn-info">🛒 Go to Checkout</a>
                <a href="profile.php?my_orders" class="btn btn-success">📦 View My Orders</a>
              </div>
            </div>
          </div>
        </body>

        </html>
<?php
        exit();
      }
    } else {
      popup_redirect('Failed to place order. Please try again.', '../index.php', 2000, 'error', 'top-center');
      exit();
    }
  }
} else {
  popup_redirect('Invalid Access!', '../index.php', 3000, 'error', 'top-center');
  exit();
}
?>