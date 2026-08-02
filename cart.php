<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('includes/connect.php');
include('function/common_function.php');
include('function/csrf.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart — Shop Smart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="sty.css">
</head>
<body>
<div class="container-fluid p-0">

    <!-- First Navbar -->
    <nav class="navbar navbar-expand-lg bg-info">
        <div class="container-fluid">
            <img src="./images/icon.png" alt="Logo" class="logo">
            <button class="navbar-toggler" type="button"
                    data-bs-toggle="collapse" data-bs-target="#cartNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="cartNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="display_all.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="./user_area/user_registration.php">Register</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <sup><?php cart_item(); ?></sup>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./user_area/checkout.php">
                            Total: <?php total_cart_price(); ?>/-
                        </a>
                    </li>
                </ul>
                <form class="d-flex" action="search_product.php" method="get">
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
                        <a class='nav-link text-white' href='./user_area/profile.php'>
                            <i class='fa-solid fa-user me-1'></i> Welcome
                        </a>
                      </li>";
                echo "<li class='nav-item'>
                        <a class='nav-link text-white' href='./user_area/user_login.php'>Login</a>
                      </li>";
            } else {
                $safe_user = htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');
                echo "<li class='nav-item'>
                        <a class='nav-link text-white' href='./user_area/profile.php'>
                            <i class='fa-solid fa-user me-1'></i> Welcome {$safe_user}
                        </a>
                      </li>";
                echo "<li class='nav-item'>
                        <a class='nav-link text-white' href='./user_area/logout.php'>Logout</a>
                      </li>";
            }
            ?>
        </ul>
    </nav>

    <!-- Page Header -->
    <div class="bg-light text-center p-3">
        <h3>Your Cart</h3>
        <p>Review your items before checkout</p>
    </div>

    <!-- Cart Content -->
    <div class="container my-4">
        <?php
        $user_ip = getUserIP();

        // Update cart quantities
        if (isset($_POST['update_cart'])) {
            if (verify_csrf_token()) {
                foreach ($_POST['qty'] as $pid => $qty) {
                    $pid = (int)$pid;
                    $qty = max(1, (int)$qty);
                    $stmt = mysqli_prepare($con,
                        "UPDATE `card_details` SET quantity = ? WHERE ip_address = ? AND product_id = ?"
                    );
                    mysqli_stmt_bind_param($stmt, 'isi', $qty, $user_ip, $pid);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            }
            echo "<script>window.location.href='cart.php';</script>";
        }

        // Remove selected items
        if (isset($_POST['remove_cart'])) {
            if (verify_csrf_token() && !empty($_POST['removeitem'])) {
                foreach ($_POST['removeitem'] as $remove_id) {
                    $remove_id = (int)$remove_id;
                    $stmt = mysqli_prepare($con,
                        "DELETE FROM `card_details` WHERE product_id = ? AND ip_address = ?"
                    );
                    mysqli_stmt_bind_param($stmt, 'is', $remove_id, $user_ip);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            }
            echo "<script>window.location.href='cart.php';</script>";
        }

        // Fetch cart items
        $cart_stmt = mysqli_prepare($con,
            "SELECT cd.product_id, cd.quantity,
                    p.product_title, p.product_image1, p.product_price
             FROM `card_details` cd
             JOIN `products` p ON cd.product_id = p.product_id
             WHERE cd.ip_address = ?"
        );
        mysqli_stmt_bind_param($cart_stmt, 's', $user_ip);
        mysqli_stmt_execute($cart_stmt);
        $cart_result = mysqli_stmt_get_result($cart_stmt);
        $cart_rows   = mysqli_fetch_all($cart_result, MYSQLI_ASSOC);
        mysqli_stmt_close($cart_stmt);

        if (count($cart_rows) > 0):
            $total = 0;
        ?>
        <form action="" method="post">
            <?php csrf_input(); ?>
            <div class="table-responsive">
                <table class="table text-center table-bordered align-middle">
                    <thead class="table-info">
                        <tr>
                            <th>Product</th>
                            <th>Image</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Remove</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_rows as $row):
                            $pid           = (int)$row['product_id'];
                            $qty           = (int)$row['quantity'];
                            $price         = (float)$row['product_price'];
                            $product_total = $price * $qty;
                            $total        += $product_total;
                            $title         = htmlspecialchars($row['product_title'],   ENT_QUOTES, 'UTF-8');
                            $img           = htmlspecialchars($row['product_image1'],  ENT_QUOTES, 'UTF-8');
                        ?>
                        <tr>
                            <td><?= $title ?></td>
                            <td>
                                <img src="admin_area/product_images/<?= $img ?>"
                                     alt="<?= $title ?>"
                                     style="width:60px; height:60px; object-fit:contain;">
                            </td>
                            <td>
                                <input type="number"
                                       name="qty[<?= $pid ?>]"
                                       value="<?= $qty ?>"
                                       class="form-control form-control-sm text-center"
                                       style="width:70px; margin:auto;"
                                       min="1">
                            </td>
                            <td>₹<?= number_format($product_total, 2) ?></td>
                            <td>
                                <input type="checkbox" class="form-check-input"
                                       name="removeitem[]" value="<?= $pid ?>">
                            </td>
                            <td class="d-flex gap-1 justify-content-center flex-wrap">
                                <button type="submit" name="update_cart"
                                        class="btn btn-sm btn-info">
                                    <i class="fa-solid fa-rotate-right"></i> Update
                                </button>
                                <button type="submit" name="remove_cart"
                                        class="btn btn-sm btn-danger">
                                    <i class="fa-solid fa-trash"></i> Remove
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Subtotal row -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-3 bg-light rounded shadow-sm mb-4">
                <h5 class="mb-0">
                    Subtotal:
                    <span class="text-success fw-bold">₹<?= number_format($total, 2) ?></span>
                </h5>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left"></i> Continue Shopping
                    </a>
                    <a href="./user_area/checkout.php" class="btn btn-dark">
                        <i class="fa-solid fa-credit-card"></i> Checkout
                    </a>
                </div>
            </div>
        </form>

        <?php else: ?>
        <div class="text-center py-5">
            <i class="fa-solid fa-cart-shopping fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">Your cart is empty</h4>
            <a href="index.php" class="btn btn-primary mt-3">Start Shopping</a>
        </div>
        <?php endif; ?>
    </div>

    <?php include('includes/footer.php'); ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
