<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

include(__DIR__ . '/../includes/connect.php');

include(__DIR__ . '/../function/common_function.php');

if (isset($_POST['admin_login'])) {
    $admin_name = $_POST['admin_name'];
    $admin_password = $_POST['admin_password'];

    $select_query = "SELECT * FROM `admin_table` WHERE admin_name='$admin_name'";
    $result = mysqli_query($con, $select_query);
    $row_count = mysqli_num_rows($result);

    if ($row_count == 1) {
        $row_data = mysqli_fetch_assoc($result);
        if (password_verify($admin_password, $row_data['admin_password'])) {
            // Login success
            $_SESSION['admin_name'] = $admin_name;
            popup_redirect('Login successful!', 'index.php', 1000, 'success', 'top-center');
            exit();
        } else {
            // Incorrect password
            popup_redirect('Incorrect password!', 'admin_login.php', 1000, 'error', 'top-center');
            exit();
        }
    } else {
        // Admin not found
        popup_redirect('Admin not found!', '', 3000, 'error', 'top-center');
        exit();
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom Styling -->
  <style>
    body {
      background: #f0f8ff;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Segoe UI', sans-serif;
    }

    .container-box {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      display: flex;
      overflow: hidden;
      max-width: 900px;
      width: 100%;
    }

    .form-section {
      flex: 1;
      padding: 40px;
    }

    .form-section h2 {
      font-weight: bold;
      margin-bottom: 30px;
    }

    .form-control {
      border-radius: 8px;
    }

    .form-text {
      text-align: center;
    }

    .form-text a {
      color: #007bff;
      text-decoration: none;
    }

    .form-text a:hover {
      text-decoration: underline;
    }

    .image-section {
      background: #dbefff;
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .image-section img {
      width: 90%;
      max-width: 400px;
    }
  </style>
</head>
<body>

<div class="container-box">

  <!-- Left Image Section -->
  <div class="image-section">
    <img src="admin_images/login_illustration.png" alt="Login Illustration">
  </div>

  <!-- Right Form Section -->
  <div class="form-section">
    <h2>Admin Login</h2>

    <form action="admin_login.php" method="post">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="admin_name" class="form-control" required placeholder="Enter your username">
      </div>

      <div class="mb-4">
        <label class="form-label">Password</label>
        <input type="password" name="admin_password" class="form-control" required placeholder="Enter your password">
      </div>

      <div class="d-grid">
        <button type="submit" class="btn btn-info text-white" name="admin_login">Login</button>
      </div>
    </form>

    <p class="form-text mt-3">Don't have an account? <a href="admin_register.php">Register</a></p>
  </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
