<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . '/../includes/connect.php');
include(__DIR__ . '/../function/common_function.php');
include(__DIR__ . '/../function/csrf.php');

if (!isset($_SESSION['username'])) {
    header("Location: user_login.php");
    exit();
}

$username  = $_SESSION['username'];

// Resolve user_id from session
$user_stmt = mysqli_prepare($con, "SELECT user_id FROM user_table WHERE user_name = ?");
mysqli_stmt_bind_param($user_stmt, 's', $username);
mysqli_stmt_execute($user_stmt);
$user_result = mysqli_stmt_get_result($user_stmt);
$user_data   = mysqli_fetch_assoc($user_result);
mysqli_stmt_close($user_stmt);

if (!$user_data) {
    header("Location: user_login.php");
    exit();
}
$user_id = (int)$user_data['user_id'];

// Fetch user's latest pending order
$order_stmt = mysqli_prepare($con,
    "SELECT * FROM user_orders
     WHERE user_id = ? AND order_status = 'pending'
     ORDER BY order_id DESC LIMIT 1"
);
mysqli_stmt_bind_param($order_stmt, 'i', $user_id);
mysqli_stmt_execute($order_stmt);
$order_result = mysqli_stmt_get_result($order_stmt);
$order_data   = mysqli_fetch_assoc($order_result);
mysqli_stmt_close($order_stmt);

$order_id = $order_data ? (int)$order_data['order_id'] : null;

// ── Recalculate the real amount from the database ─────────────────────────
// Never trust amount from the form — always recalculate server-side.
$real_amount = 0.0;
if ($order_id) {
    $items_stmt = mysqli_prepare($con,
        "SELECT op.product_id, op.quantity, p.product_price
         FROM orders_pending op
         JOIN products p ON op.product_id = p.product_id
         WHERE op.invoice_number = ? AND op.user_id = ?"
    );
    $inv = (int)$order_data['invoice_number'];
    mysqli_stmt_bind_param($items_stmt, 'ii', $inv, $user_id);
    mysqli_stmt_execute($items_stmt);
    $items_result = mysqli_stmt_get_result($items_stmt);
    while ($item = mysqli_fetch_assoc($items_result)) {
        $real_amount += (float)$item['product_price'] * (int)$item['quantity'];
    }
    mysqli_stmt_close($items_stmt);

    // Fall back to stored amount_due if no pending items found (already cleared)
    if ($real_amount <= 0) {
        $real_amount = (float)($order_data['amount_due'] ?? 0);
    }
}

if (isset($_POST['confirm_payment'])) {
    if (!verify_csrf_token()) {
        popup_redirect('Invalid request. Please try again.', 'confirm_payment.php', 2000, 'error', 'top-center');
        exit();
    }

    if (!$order_id) {
        popup_redirect('No pending order found.', 'profile.php?my_orders', 2000, 'error', 'top-center');
        exit();
    }

    $payment_mode = $_POST['payment_mode'] ?? '';
    $allowed_modes = ['UPI', 'Bank Transfer', 'Cash on Delivery', 'Credit/Debit Card'];
    if (!in_array($payment_mode, $allowed_modes, true)) {
        popup_redirect('Invalid payment mode selected.', 'confirm_payment.php', 2000, 'error', 'top-center');
        exit();
    }

    $invoice_num = (int)$order_data['invoice_number'];

    // Insert payment record using the server-calculated amount
    $ins = mysqli_prepare($con,
        "INSERT INTO payments (order_id, invoice_number, amount, payment_mode, payment_date)
         VALUES (?, ?, ?, ?, NOW())"
    );
    mysqli_stmt_bind_param($ins, 'iids', $order_id, $invoice_num, $real_amount, $payment_mode);
    mysqli_stmt_execute($ins);
    mysqli_stmt_close($ins);

    // Mark order as completed
    $upd = mysqli_prepare($con,
        "UPDATE user_orders SET order_status = 'completed' WHERE order_id = ?"
    );
    mysqli_stmt_bind_param($upd, 'i', $order_id);
    mysqli_stmt_execute($upd);
    mysqli_stmt_close($upd);

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
        <div class="card mx-auto shadow p-4" style="max-width:600px;">
            <h4 class="text-center mb-4 text-primary">Confirm Your Payment</h4>

            <?php if ($order_id): ?>
            <form method="post">
                <?php csrf_input(); ?>

                <div class="mb-3">
                    <label class="form-label">Invoice Number</label>
                    <input type="text" class="form-control"
                           value="<?= htmlspecialchars((string)$order_data['invoice_number'], ENT_QUOTES, 'UTF-8') ?>"
                           readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Amount</label>
                    <!-- Display only — amount is recalculated server-side on submit -->
                    <input type="text" class="form-control"
                           value="₹<?= htmlspecialchars(number_format($real_amount, 2), ENT_QUOTES, 'UTF-8') ?>"
                           readonly>
                </div>
                <div class="mb-3">
                    <label for="payment_mode" class="form-label">Select Payment Mode</label>
                    <select class="form-select" name="payment_mode" id="payment_mode" required>
                        <option value="">-- Select --</option>
                        <option value="UPI">UPI</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Cash on Delivery">Cash on Delivery</option>
                        <option value="Credit/Debit Card">Credit/Debit Card</option>
                    </select>
                </div>
                <div class="text-center">
                    <button type="submit" name="confirm_payment" class="btn btn-success">
                        Confirm Payment
                    </button>
                </div>
            </form>
            <?php else: ?>
                <div class="alert alert-warning text-center">
                    No pending orders found to confirm payment.
                </div>
                <div class="text-center mt-3">
                    <a href="../index.php" class="btn btn-primary">Go to Home</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
