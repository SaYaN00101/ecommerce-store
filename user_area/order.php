<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . '/../includes/connect.php');
include(__DIR__ . '/../function/common_function.php');
include(__DIR__ . '/../function/csrf.php');

// Must be logged in
if (!isset($_SESSION['username'])) {
    popup_redirect('Please log in to place an order.', '../user_area/user_login.php', 2000, 'error', 'top-center');
    exit();
}

// CSRF check
if (!isset($_POST['payment_method']) || !verify_csrf_token()) {
    popup_redirect('Invalid request!', '../index.php', 3000, 'error', 'top-center');
    exit();
}

$payment_method = $_POST['payment_method'];
if (!in_array($payment_method, ['cod', 'online'], true)) {
    popup_redirect('Invalid payment method.', '../index.php', 2000, 'error', 'top-center');
    exit();
}

// ── Resolve user_id from session (never from GET) ────────────────────────
$username = $_SESSION['username'];
$user_stmt = mysqli_prepare($con, "SELECT user_id FROM `user_table` WHERE user_name = ?");
mysqli_stmt_bind_param($user_stmt, 's', $username);
mysqli_stmt_execute($user_stmt);
$user_result = mysqli_stmt_get_result($user_stmt);
$user_row    = mysqli_fetch_assoc($user_result);
mysqli_stmt_close($user_stmt);

if (!$user_row) {
    popup_redirect('User not found. Please log in again.', '../user_area/user_login.php', 2000, 'error', 'top-center');
    exit();
}
$user_id = (int)$user_row['user_id'];

// ── Load cart by IP ───────────────────────────────────────────────────────
$user_ip   = getUserIP();
$cart_stmt = mysqli_prepare($con, "SELECT product_id, quantity FROM `card_details` WHERE ip_address = ?");
mysqli_stmt_bind_param($cart_stmt, 's', $user_ip);
mysqli_stmt_execute($cart_stmt);
$cart_result   = mysqli_stmt_get_result($cart_stmt);
$total_products = mysqli_num_rows($cart_result);

if ($total_products === 0) {
    mysqli_stmt_close($cart_stmt);
    popup_redirect('Your cart is empty.', '../index.php', 3000, 'info', 'top-center');
    exit();
}

// ── Calculate total from the database (never trust client-side amounts) ──
$total_price = 0.0;
$cart_items  = [];

while ($row = mysqli_fetch_assoc($cart_result)) {
    $pid = (int)$row['product_id'];
    $qty = (int)$row['quantity'];

    $p_stmt = mysqli_prepare($con, "SELECT product_price FROM `products` WHERE product_id = ?");
    mysqli_stmt_bind_param($p_stmt, 'i', $pid);
    mysqli_stmt_execute($p_stmt);
    $p_result = mysqli_stmt_get_result($p_stmt);
    $p_row    = mysqli_fetch_assoc($p_result);
    mysqli_stmt_close($p_stmt);

    if ($p_row) {
        $total_price += (float)$p_row['product_price'] * $qty;
        $cart_items[] = ['product_id' => $pid, 'quantity' => $qty];
    }
}
mysqli_stmt_close($cart_stmt);

$invoice_number = mt_rand(100000, 999999);
$order_status   = 'pending';

// ── Insert into user_orders ───────────────────────────────────────────────
$insert_order = mysqli_prepare($con,
    "INSERT INTO `user_orders`
     (user_id, amount_due, invoice_number, total_products, order_status, payment_method)
     VALUES (?, ?, ?, ?, ?, ?)"
);
mysqli_stmt_bind_param($insert_order, 'idiiis',
    $user_id, $total_price, $invoice_number, $total_products, $order_status, $payment_method
);

if (!mysqli_stmt_execute($insert_order)) {
    mysqli_stmt_close($insert_order);
    popup_redirect('Failed to place order. Please try again.', '../index.php', 2000, 'error', 'top-center');
    exit();
}
mysqli_stmt_close($insert_order);

// ── Insert each item into orders_pending ─────────────────────────────────
foreach ($cart_items as $item) {
    $ins = mysqli_prepare($con,
        "INSERT INTO `orders_pending`
         (user_id, invoice_number, product_id, quantity, order_status)
         VALUES (?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($ins, 'iiis',   // Note: order_status is string
        $user_id, $invoice_number, $item['product_id'], $item['quantity']
    );
    // Re-bind including order_status
    mysqli_stmt_close($ins);

    $ins2 = mysqli_prepare($con,
        "INSERT INTO `orders_pending`
         (user_id, invoice_number, product_id, quantity, order_status)
         VALUES (?, ?, ?, ?, ?)"
    );
    $pid = $item['product_id'];
    $qty = $item['quantity'];
    mysqli_stmt_bind_param($ins2, 'iiiis', $user_id, $invoice_number, $pid, $qty, $order_status);
    mysqli_stmt_execute($ins2);
    mysqli_stmt_close($ins2);
}

// ── Clear cart ────────────────────────────────────────────────────────────
$del = mysqli_prepare($con, "DELETE FROM `card_details` WHERE ip_address = ?");
mysqli_stmt_bind_param($del, 's', $user_ip);
mysqli_stmt_execute($del);
mysqli_stmt_close($del);

// ── Redirect based on payment method ─────────────────────────────────────
if ($payment_method === 'online') {
    header("Location: confirm_payment.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Placed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow p-4 text-center mx-auto" style="max-width:500px;">
            <h4 class="text-success mb-3">
                <i class="fa-solid fa-circle-check"></i> Order Placed Successfully!
            </h4>
            <p>Your order has been placed using <strong>Cash on Delivery</strong>.</p>
            <div class="d-grid gap-2 col-8 mx-auto mt-4">
                <a href="../index.php"         class="btn btn-primary">🏠 Go to Home</a>
                <a href="checkout.php"         class="btn btn-info">🛒 Go to Checkout</a>
                <a href="profile.php?my_orders" class="btn btn-success">📦 View My Orders</a>
            </div>
        </div>
    </div>
</body>
</html>
