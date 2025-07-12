<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

include(__DIR__ . '/../includes/connect.php');

include(__DIR__ . '/../function/common_function.php');

if (isset($_POST['admin_register'])) {
    $admin_name = mysqli_real_escape_string($con, $_POST['admin_name']);
    $admin_email = mysqli_real_escape_string($con, $_POST['admin_email']);
    $admin_password = $_POST['admin_password'];
    $confirm_password = $_POST['confirm_password'];

    // Profile image
    $admin_image = $_FILES['admin_image']['name'];
    $admin_image_tmp = $_FILES['admin_image']['tmp_name'];

    // Check empty
    if (empty($admin_name) || empty($admin_email) || empty($admin_password) || empty($confirm_password) || empty($admin_image)) {
        popup_redirect('Please fill all fields.', '', 3000, 'error', 'top-center');
        exit();
    }

    // Check password match
    if ($admin_password !== $confirm_password) {
        popup_redirect('Passwords do not match!', '', 3000, 'error', 'top-center');
        exit();
    }

    // Check if admin already exists
    $check_query = "SELECT * FROM `admin_table` WHERE admin_name='$admin_name' OR admin_email='$admin_email'";
    $result = mysqli_query($con, $check_query);
    if (mysqli_num_rows($result) > 0) {
        popup_redirect('Admin already exists!', '', 3000, 'error', 'top-center');
        exit();
    }

    // Hash password
    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

    // Move image
    move_uploaded_file($admin_image_tmp, "admin_images/$admin_image");

    // Insert into DB
    $insert_query = "INSERT INTO `admin_table` (admin_name, admin_email, admin_password, admin_image, register_date) 
                     VALUES ('$admin_name', '$admin_email', '$hashed_password', '$admin_image', NOW())";
    $insert_result = mysqli_query($con, $insert_query);

    if ($insert_result) {
        popup_redirect('Admin registered successfully!', 'admin_login.php', 2000, 'success', 'top-center');
        exit();
    } else {
        popup_redirect('Registration failed! Please try again.', 'admin_register.php', 1000, 'error', 'top-center');
        exit();
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Registration</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Custom Styling -->
  <style>
    body {
      background: #f5f9fc;
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
      max-width: 1000px;
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
      background: #e0f2ff;
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
    <img src="admin_images/registration_illustration.png" alt="Admin Illustration">
  </div>

  <!-- Right Form Section -->
  <div class="form-section">
    <h2>Admin Registration</h2>
    <form action="admin_register.php" method="post" enctype="multipart/form-data">
      
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="admin_name" class="form-control" required placeholder="Enter your username">
      </div>

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="admin_email" class="form-control" required placeholder="Enter your email">
      </div>

      <div class="mb-3">
        <label class="form-label">Profile Image</label>
        <input type="file" name="admin_image" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="admin_password" class="form-control" required placeholder="Enter your password">
      </div>

      <div class="mb-4">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control" required placeholder="Enter your confirm_password">
      </div>

      <div class="d-grid">
        <button type="submit" class="btn btn-info text-white" name="admin_register">Register</button>
      </div>

    </form>
    <p class="form-text mt-3">Already you have an account? <a href="admin_login.php">Login</a></p>
  </div>

</div>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
