<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . '/../includes/connect.php');
include(__DIR__ . '/../function/common_function.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    // Collect form data
    $user_name = $_POST['username'] ?? '';
    $user_email = $_POST['email'] ?? '';
    $user_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $user_address = $_POST['address'] ?? '';
    $user_mobile = $_POST['contact'] ?? '';
    $user_ip = getIPAddress();

    $user_image = $_FILES['user_image']['name'] ?? '';
    $temp_image = $_FILES['user_image']['tmp_name'] ?? '';

    // Basic validation
    if (
        empty($user_name) || empty($user_email) || empty($user_password) ||
        empty($confirm_password) || empty($user_image) || empty($user_address) ||
        empty($user_mobile)
    ) {
        $message = "<div class='alert alert-danger text-center'>Please fill all the fields.</div>";
    } elseif ($user_password !== $confirm_password) {
        $message = "<div class='alert alert-danger text-center'>Passwords do not match.</div>";
    } else {
        // Check if email already exists
        $check_query = "SELECT * FROM `user_table` WHERE user_email='$user_email'";
        $check_result = mysqli_query($con, $check_query);
        if (mysqli_num_rows($check_result) > 0) {
            $message = "<div class='alert alert-warning text-center'>Email already registered. Redirecting to login...</div>";
            echo "<script>setTimeout(() => { window.location.href = 'user_login.php'; }, 3000);</script>";
        } else {
            // Hash password and move image
            $hash_password = password_hash($user_password, PASSWORD_DEFAULT);
            move_uploaded_file($temp_image, "./user_images/$user_image");

            // Insert into database
            $insert_query = "INSERT INTO `user_table` 
                (user_name, user_email, user_password, user_image, user_ip, user_address, user_mobile)
                VALUES 
                ('$user_name', '$user_email', '$hash_password', '$user_image', '$user_ip', '$user_address', '$user_mobile')";

            if (mysqli_query($con, $insert_query)) {
                $_SESSION['username'] = $user_name;
                $message = "<div class='alert alert-success text-center'>Registration successful.</div>";

                // Check cart
                $cart_check =  "SELECT * FROM `card_details` WHERE ip_address='$user_ip'";
                $cart_item = mysqli_query($con, $cart_check);
                if (mysqli_num_rows($cart_item) > 0) {
                    echo "<script>setTimeout(() => { window.location.href = 'checkout.php'; }, 1500);</script>";
                } else {
                    echo "<script>setTimeout(() => { window.location.href = '../index.php'; }, 1500);</script>";
                }
            } else {
                $message = "<div class='alert alert-danger text-center'>Something went wrong. Try again.</div>";
            }
        }
    }
}

// IP Function
function getIPAddress() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
    return $_SERVER['REMOTE_ADDR'];
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Registration</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <link rel="stylesheet" href="sty.css">
</head>
<body>
<div class="container mt-4">
  <h1 class="text-center">User Registration</h1>

  <!-- Show message here -->
  <?php if (!empty($message)) echo $message; ?>

  <form action="" method="post" enctype="multipart/form-data">
    
    <!-- Username -->
    <div class="form-outline mb-4 w-50 m-auto">
      <label for="username" class="form-label">Username</label>
      <input type="text" name="username" id="username" class="form-control" placeholder="Enter your name" autocomplete="off" required>
    </div>

    <!-- Email -->
    <div class="form-outline mb-4 w-50 m-auto">
      <label for="email" class="form-label">Email</label>
      <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" autocomplete="off" required>
    </div>

    <!-- User Image -->
    <div class="form-outline mb-4 w-50 m-auto">
      <label for="user_image" class="form-label">User Image</label>
      <input type="file" name="user_image" id="user_image" class="form-control" required>
    </div>

    <!-- Password -->
    <div class="form-outline mb-4 w-50 m-auto">
      <label for="password" class="form-label">Password</label>
      <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
    </div>

    <!-- Confirm Password -->
    <div class="form-outline mb-4 w-50 m-auto">
      <label for="confirm_password" class="form-label">Confirm Password</label>
      <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm your password" required>
    </div>

    <!-- Address -->
    <div class="form-outline mb-4 w-50 m-auto">
      <label for="address" class="form-label">Address</label>
      <input type="text" name="address" id="address" class="form-control" placeholder="Enter your address" autocomplete="off" required>
    </div>

    <!-- Contact -->
    <div class="form-outline mb-4 w-50 m-auto">
      <label for="contact" class="form-label">Contact Number</label>
      <input type="text" name="contact" id="contact" class="form-control" placeholder="Enter your mobile number" autocomplete="off" required>
    </div>

    <!-- Submit Button -->
    <div class="form-outline mb-4 w-50 m-auto text-center">
      <input type="submit" name="register" class="btn btn-info mb-3 px-4" value="Register">
    </div>

    <!-- Login Redirect -->
    <div class="text-center">
      <p>Already have an account? <a href="user_login.php">Login here</a></p>
    </div>

  </form>
</div>
</body>
</html>
