<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . '/../includes/connect.php');
include(__DIR__ . '/../function/common_function.php');
include(__DIR__ . '/../function/csrf.php');

$message = '';

// (Registration logic is identical to the existing file but presented with site chrome)
// Allowed image MIME types and extensions for user profile pictures
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_IMAGE_EXTS',  ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2 MB

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    if (!verify_csrf_token()) {
        $message = "<div class='alert alert-danger text-center'>Invalid request. Please try again.</div>";
    } else {
        $user_name        = trim($_POST['username']         ?? '');
        $user_email       = trim($_POST['email']            ?? '');
        $user_password    = $_POST['password']              ?? '';
        $confirm_password = $_POST['confirm_password']      ?? '';
        $user_address     = trim($_POST['address']          ?? '');
        $user_mobile      = trim($_POST['contact']          ?? '');
        $user_ip          = getUserIP();

        $image_file = $_FILES['user_image'] ?? null;
        $image_name = $image_file['name']     ?? '';
        $image_tmp  = $image_file['tmp_name'] ?? '';
        $image_size = $image_file['size']     ?? 0;
        $image_error = $image_file['error']   ?? UPLOAD_ERR_NO_FILE;

        if (
            empty($user_name) || empty($user_email) || empty($user_password) ||
            empty($confirm_password) || empty($user_address) || empty($user_mobile)
        ) {
            $message = "<div class='alert alert-danger text-center'>Please fill all the fields.</div>";
        } elseif ($user_password !== $confirm_password) {
            $message = "<div class='alert alert-danger text-center'>Passwords do not match.</div>";
        } elseif (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
            $message = "<div class='alert alert-danger text-center'>Invalid email address.</div>";
        } elseif ($image_error !== UPLOAD_ERR_OK || empty($image_name)) {
            $message = "<div class='alert alert-danger text-center'>Please upload a profile image.</div>";
        } else {
            $ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
            if (!in_array($ext, ALLOWED_IMAGE_EXTS, true)) {
                $message = "<div class='alert alert-danger text-center'>Only JPG, PNG, GIF, and WEBP images are allowed.</div>";
            } elseif ($image_size > MAX_FILE_SIZE) {
                $message = "<div class='alert alert-danger text-center'>Image must be under 2 MB.</div>";
            } elseif (!in_array(mime_content_type($image_tmp), ALLOWED_IMAGE_TYPES, true)) {
                $message = "<div class='alert alert-danger text-center'>Uploaded file is not a valid image.</div>";
            } else {
                $check = mysqli_prepare($con, "SELECT user_id FROM `user_table` WHERE user_email = ?");
                mysqli_stmt_bind_param($check, 's', $user_email);
                mysqli_stmt_execute($check);
                mysqli_stmt_store_result($check);

                if (mysqli_stmt_num_rows($check) > 0) {
                    $message = "<div class='alert alert-warning text-center'>Email already registered. Redirecting to login...</div>";
                    echo "<script>setTimeout(() => { window.location.href = 'user_login.php'; }, 3000);</script>";
                } else {
                    mysqli_stmt_close($check);
                    $safe_filename = bin2hex(random_bytes(8)) . '.' . $ext;
                    $upload_path   = __DIR__ . '/user_images/' . $safe_filename;

                    if (!move_uploaded_file($image_tmp, $upload_path)) {
                        $message = "<div class='alert alert-danger text-center'>Failed to upload image. Please try again.</div>";
                    } else {
                        $hash_password = password_hash($user_password, PASSWORD_DEFAULT);

                        $insert = mysqli_prepare($con,
                            "INSERT INTO `user_table`
                             (user_name, user_email, user_password, user_image, user_ip, user_address, user_mobile)
                             VALUES (?, ?, ?, ?, ?, ?, ?)"
                        );
                        mysqli_stmt_bind_param($insert, 'sssssss',
                            $user_name, $user_email, $hash_password,
                            $safe_filename, $user_ip, $user_address, $user_mobile
                        );

                        if (mysqli_stmt_execute($insert)) {
                            $_SESSION['username'] = $user_name;
                            $message = "<div class='alert alert-success text-center'>Registration successful.</div>";

                            $cart = mysqli_prepare($con, "SELECT product_id FROM `card_details` WHERE ip_address = ?");
                            mysqli_stmt_bind_param($cart, 's', $user_ip);
                            mysqli_stmt_execute($cart);
                            mysqli_stmt_store_result($cart);
                            $has_cart = mysqli_stmt_num_rows($cart) > 0;
                            mysqli_stmt_close($cart);

                            if ($has_cart) {
                                echo "<script>setTimeout(() => { window.location.href = 'checkout.php'; }, 1500);</script>";
                            } else {
                                echo "<script>setTimeout(() => { window.location.href = '../index.php'; }, 1500);</script>";
                            }
                        } else {
                            $message = "<div class='alert alert-danger text-center'>Something went wrong. Try again.</div>";
                        }
                        mysqli_stmt_close($insert);
                    }
                }
                if (isset($check) && $check) mysqli_stmt_close($check);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../sty.css">
</head>
<body>
<div class="container-fluid p-0">
    <!-- First Navbar -->
    <nav class="navbar navbar-expand-lg bg-info">
        <div class="container-fluid">
            <img src="../images/icon.png" alt="Logo" class="logo">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="../display_all.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="user_registration.php">Register</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="../cart.php"><i class="fa-solid fa-cart-shopping"></i><sup><?php cart_item(); ?></sup></a></li>
                    <li class="nav-item"><a class="nav-link" href="checkout.php">Total: <?php total_cart_price(); ?>/-</a></li>
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

    <div class="container mt-4">
        <h1 class="text-center">User Registration</h1>

        <?php if (!empty($message)) echo $message; ?>

        <form action="" method="post" enctype="multipart/form-data">
            <?php csrf_input(); ?>

            <div class="form-outline mb-4 col-12 col-md-6 mx-auto">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Enter your name" autocomplete="off" required>
            </div>

            <div class="form-outline mb-4 col-12 col-md-6 mx-auto">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" autocomplete="off" required>
            </div>

            <div class="form-outline mb-4 col-12 col-md-6 mx-auto">
                <label for="user_image" class="form-label">Profile Image <small class="text-muted">(JPG/PNG/GIF/WEBP, max 2 MB)</small></label>
                <input type="file" name="user_image" id="user_image" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp" required>
            </div>

            <div class="form-outline mb-4 col-12 col-md-6 mx-auto">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <div class="form-outline mb-4 col-12 col-md-6 mx-auto">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
            </div>

            <div class="form-outline mb-4 col-12 col-md-6 mx-auto">
                <label for="address" class="form-label">Address</label>
                <textarea name="address" id="address" class="form-control" rows="2" required></textarea>
            </div>

            <div class="form-outline mb-4 col-12 col-md-6 mx-auto">
                <label for="contact" class="form-label">Mobile Number</label>
                <input type="tel" name="contact" id="contact" class="form-control" placeholder="Enter your mobile number" required>
            </div>

            <div class="form-outline mb-4 col-12 col-md-6 mx-auto text-center">
                <input type="submit" name="register" class="btn btn-primary px-4" value="Register" />
            </div>

            <div class="text-center">
                <p>Already have an account? <a href="user_login.php">Login here</a></p>
            </div>
        </form>
    </div>

    <?php include(__DIR__ . '/../includes/footer.php'); ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
