<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure $con is available; include only when missing so this file can be embedded
if (!isset($con)) {
    include(__DIR__ . '/../includes/connect.php');
}
include_once(__DIR__ . '/../function/common_function.php');
include_once(__DIR__ . '/../function/csrf.php');

// Must be logged in to reach payment
if (!isset($_SESSION['username'])) {
    header("Location: user_login.php");
    exit();
}

// Resolve user_id from session — never from IP or GET
$username  = $_SESSION['username'];
$user_stmt = mysqli_prepare($con, "SELECT user_id FROM `user_table` WHERE user_name = ?");
mysqli_stmt_bind_param($user_stmt, 's', $username);
mysqli_stmt_execute($user_stmt);
$user_result = mysqli_stmt_get_result($user_stmt);
$user_row    = mysqli_fetch_assoc($user_result);
mysqli_stmt_close($user_stmt);

if (!$user_row) {
    header("Location: user_login.php");
    exit();
}
$user_id = (int)$user_row['user_id'];

// Output fragment suitable for inclusion inside another page (no DOCTYPE/head/body)
?>

<div class="bg-light text-center p-3">
    <h3>Payment</h3>
    <p>Complete your purchase securely</p>
</div>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4 shadow-lg">
                <h4 class="text-center mb-4">Choose Payment Method</h4>

                <form action="order.php" method="post">
                    <?php csrf_input(); ?>
                    <input type="hidden" name="user_id" value="<?= $user_id ?>">

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                        <label class="form-check-label" for="cod">Cash on Delivery</label>
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="radio" name="payment_method" id="online" value="online">
                        <label class="form-check-label" for="online">Online Payment</label>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-success w-50">Proceed</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

