<?php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

include(__DIR__ . '/../includes/connect.php');

include(__DIR__ . '/../function/common_function.php');

$message = '';

// Get user IP for cart checking
$user_ip = getUserIP();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
  $login_input = trim($_POST['login_input']);
  $user_password = $_POST['password'];

  // Validate fields
  if (empty($login_input) || empty($user_password)) {
    $message = "<div class='alert alert-danger text-center'>All fields are required.</div>";
  } else {
    $select_query = "SELECT * FROM `user_table` WHERE user_name = '$login_input' OR user_email = '$login_input'";
    $result = mysqli_query($con, $select_query);

    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);

      if (password_verify($user_password, $row['user_password'])) {
        $_SESSION['username'] = $row['user_name'];

        // Check if cart has items
        $cart_query = "SELECT * FROM `card_details` WHERE ip_address = '$user_ip'";
        $cart_result = mysqli_query($con, $cart_query);

        $message = "<div class='alert alert-success text-center'>Login successful. Redirecting...</div>";

        if (isset($_SESSION['return_url'])) {
          $redirect = $_SESSION['return_url'];
          unset($_SESSION['return_url']);
          echo "<script>setTimeout(() => { window.location.href = '$redirect'; }, 2000);</script>";
        } elseif (mysqli_num_rows($cart_result) > 0) {
          echo "<script>setTimeout(() => { window.location.href = 'checkout.php'; }, 2000);</script>";
        } else {
          echo "<script>setTimeout(() => { window.location.href = 'profile.php'; }, 2000);</script>";
        }
      } else {
        $message = "<div class='alert alert-danger text-center'>Invalid Credintial.</div>";
      }
    } else {
      $message = "<div class='alert alert-danger text-center'>User not found.</div>";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>User Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="container mt-5">
    <h1 class="text-center mb-4">User Login</h1>

    <!-- Message display -->
    <?php if (!empty($message)) echo $message; ?>

    <form method="post" action="">
      <div class="form-outline mb-4 w-50 m-auto">
        <label for="login_input" class="form-label">Email or Username</label>
        <input type="text" name="login_input" id="login_input" class="form-control" required />
      </div>

      <div class="form-outline mb-4 w-50 m-auto">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" required />
      </div>

      <div class="form-outline mb-4 w-50 m-auto text-center">
        <input type="submit" name="login" class="btn btn-primary px-4" value="Login" />
      </div>

      <div class="text-center">
        <p>Don't have an account? <a href="user_registation.php">Register here</a></p>
      </div>
    </form>
  </div>

  <script>
    // Prevent form resubmission on refresh
    if (window.history.replaceState) {
      window.history.replaceState(null, null, window.location.href);
    }
  </script>
</body>

</html>