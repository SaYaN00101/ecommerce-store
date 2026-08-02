<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . '/../includes/connect.php');
include(__DIR__ . '/../function/common_function.php');
include(__DIR__ . '/../function/csrf.php');

$message = '';

$user_ip = getUserIP();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // CSRF check
    if (!verify_csrf_token()) {
        $message = "<div class='alert alert-danger text-center'>Invalid request. Please try again.</div>";
    } else {
        $login_input   = trim($_POST['login_input'] ?? '');
        $user_password = $_POST['password'] ?? '';

        if (empty($login_input) || empty($user_password)) {
            $message = "<div class='alert alert-danger text-center'>All fields are required.</div>";
        } else {
            // Prepared statement — no SQL injection
            $stmt = mysqli_prepare($con, "SELECT * FROM `user_table` WHERE user_name = ? OR user_email = ?");
            mysqli_stmt_bind_param($stmt, 'ss', $login_input, $login_input);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            // Generic error message prevents user enumeration (#12)
            if ($row = mysqli_fetch_assoc($result)) {
                if (password_verify($user_password, $row['user_password'])) {
                    $_SESSION['username'] = $row['user_name'];
                    mysqli_stmt_close($stmt);

                    // Check if cart has items
                    $cart_stmt = mysqli_prepare($con, "SELECT product_id FROM `card_details` WHERE ip_address = ?");
                    mysqli_stmt_bind_param($cart_stmt, 's', $user_ip);
                    mysqli_stmt_execute($cart_stmt);
                    mysqli_stmt_store_result($cart_stmt);
                    $cart_count = mysqli_stmt_num_rows($cart_stmt);
                    mysqli_stmt_close($cart_stmt);

                    $message = "<div class='alert alert-success text-center'>Login successful. Redirecting...</div>";

                    if (isset($_SESSION['return_url'])) {
                        $redirect = $_SESSION['return_url'];
                        unset($_SESSION['return_url']);
                        $safe_redirect = htmlspecialchars($redirect, ENT_QUOTES, 'UTF-8');
                        echo "<script>setTimeout(() => { window.location.href = " . json_encode($safe_redirect) . "; }, 1500);</script>";
                    } elseif ($cart_count > 0) {
                        echo "<script>setTimeout(() => { window.location.href = 'checkout.php'; }, 1500);</script>";
                    } else {
                        echo "<script>setTimeout(() => { window.location.href = 'profile.php'; }, 1500);</script>";
                    }
                } else {
                    // Same message as "not found" — prevents user enumeration
                    $message = "<div class='alert alert-danger text-center'>Invalid credentials.</div>";
                }
            } else {
                $message = "<div class='alert alert-danger text-center'>Invalid credentials.</div>";
            }
            if (isset($stmt) && $stmt) mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>User Login</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../sty.css">
</head>
<body>
<div class="container-fluid p-0">
    <nav class="navbar navbar-expand-lg bg-info">
        <div class="container-fluid">
            <img src="../images/icon.png" alt="Logo" class="logo">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navLogin">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navLogin">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="../display_all.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="user_registration.php">Register</a></li>
                </ul>
                <form class="d-flex" action="../search_product.php" method="get">
                    <input class="form-control me-2" type="search" placeholder="Search" name="search_data">
                    <input type="submit" value="Search" class="btn btn-outline-light" name="search_data_product">
                </form>
            </div>
        </div>
    </nav>

    <nav class="navbar navbar-expand-lg bg-secondary">
        <ul class="navbar nav me-auto">
            <?php
            if (!isset($_SESSION['username'])) {
                echo "<li class='nav-item'><a class='nav-link text-white' href='profile.php'><i class='fa-solid fa-user me-1'></i> Welcome</a></li>";
                echo "<li class='nav-item'><a class='nav-link text-white' href='user_login.php'>Login</a></li>";
            } else {
                $safe_user = htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');
                echo "<li class='nav-item'><a class='nav-link text-white' href='profile.php'><i class='fa-solid fa-user me-1'></i> Welcome {$safe_user}</a></li>";
                echo "<li class='nav-item'><a class='nav-link text-white' href='logout.php'>Logout</a></li>";
            }
            ?>
        </ul>
    </nav>

    <div class="container mt-5">
        <h1 class="text-center mb-4">User Login</h1>

        <?php if (!empty($message)) echo $message; ?>

        <?php include(__DIR__ . '/_login_fragment.php'); ?>
    </div>

    <?php include(__DIR__ . '/../includes/footer.php'); ?>
</div>

<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
