<?php
include('../includes/connect.php');
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['username'])) {
  header("Location: user_login.php");
  exit();
}

$username = $_SESSION['username'];

// Fetch user's latest pending order
$user_query = "SELECT user_id FROM user_table WHERE user_name = '$username'";
$user_result = mysqli_query($con, $user_query);
$user_data = mysqli_fetch_assoc($user_result);
$user_id = $user_data['user_id'];

$order_query = "SELECT * FROM user_orders WHERE user_id = $user_id AND order_status = 'pending' ORDER BY order_id DESC LIMIT 1";
$order_result = mysqli_query($con, $order_query);
$order_data = mysqli_fetch_assoc($order_result);
$order_id = $order_data['order_id'] ?? null;

if (isset($_POST['confirm_payment'])) {
  $invoice = $_POST['invoice'];
  $amount = $_POST['amount'];
  $payment_mode = $_POST['payment_mode'];

  // Insert into payment table (optional if you track payment logs)
  $insert_payment = "INSERT INTO payments (order_id, invoice_number, amount, payment_mode, payment_date) 
                     VALUES ($order_id, '$invoice', $amount, '$payment_mode', NOW())";
  mysqli_query($con, $insert_payment);

  // Update order status
  $update_order = "UPDATE user_orders SET order_status = 'completed' WHERE order_id = $order_id";
  mysqli_query($con, $update_order);

  //echo "<script>alert('Thank you! Your payment has been confirmed.'); window.location.href='profile.php?my_orders';</script>";
  popup_redirect('Thank you! Your payment has been confirmed.', 'profile.php?my_orders', 1500, 'success', 'top-center');
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Confirm Payment</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="card mx-auto shadow p-4" style="max-width: 600px;">
      <h4 class="text-center mb-4 text-primary">Confirm Your Payment</h4>
      <?php if ($order_id): ?>
      <form method="post">
        <div class="mb-3">
          <label for="invoice" class="form-label">Invoice Number</label>
          <input type="text" class="form-control" id="invoice" name="invoice" value="<?= $order_data['invoice_number'] ?>" readonly>
        </div>
        <div class="mb-3">
          <label for="amount" class="form-label">Amount</label>
          <input type="text" class="form-control" id="amount" name="amount" value="<?= $order_data['amount_due'] ?>" readonly>
        </div>
        <div class="mb-3">
          <label for="payment_mode" class="form-label">Select Payment Mode</label>
          <select class="form-select" name="payment_mode" required>
            <option value="">-- Select --</option>
            <option value="UPI">UPI</option>
            <option value="Bank Transfer">Bank Transfer</option>
            <option value="Cash on Delivery">Cash on Delivery</option>
            <option value="Credit/Debit Card">Credit/Debit Card</option>
          </select>
        </div>
        <div class="text-center">
          <button type="submit" name="confirm_payment" class="btn btn-success">Confirm Payment</button>
        </div>
      </form>
      <?php else: ?>
        <div class="alert alert-warning text-center">
          No pending orders found to confirm payment.
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
