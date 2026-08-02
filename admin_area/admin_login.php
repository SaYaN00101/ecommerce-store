<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . '/../includes/connect.php');
include(__DIR__ . '/../function/common_function.php');
include(__DIR__ . '/../function/csrf.php');

if (isset($_POST['admin_login'])) {
    // CSRF check
    if (!verify_csrf_token()) {
        popup_redirect('Invalid request. Please try again.', 'admin_login.php', 2000, 'error', 'top-center');
        exit();
    }

    $admin_name     = $_POST['admin_name']     ?? '';
    $admin_password = $_POST['admin_password'] ?? '';

    // Prepared statement — no SQL injection
    $stmt = mysqli_prepare($con, "SELECT * FROM `admin_table` WHERE admin_name = ?");
    mysqli_stmt_bind_param($stmt, 's', $admin_name);
    mysqli_stmt_execute($stmt);
    $result   = mysqli_stmt_get_result($stmt);
    $row_data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    // Generic error to avoid user enumeration
    if ($row_data && password_verify($admin_password, $row_data['admin_password'])) {
        $_SESSION['admin_name'] = $row_data['admin_name'];
        popup_redirect('Login successful!', 'index.php', 1000, 'success', 'top-center');
    } else {
        popup_redirect('Invalid credentials!', 'admin_login.php', 1000, 'error', 'top-center');
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .form-section  { flex: 1; padding: 40px; }
        .form-section h2 { font-weight: bold; margin-bottom: 30px; }
        .form-control  { border-radius: 8px; }
        .form-text     { text-align: center; }
        .form-text a   { color: #007bff; text-decoration: none; }
        .form-text a:hover { text-decoration: underline; }
        .image-section {
            background: #e8f4ff;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .image-section img { width: 90%; max-width: 400px; }
    </style>
</head>
<body>
<div class="container-box">
    <!-- Illustration -->
    <div class="image-section">
        <img src="admin_images/login_illustration.png" alt="Admin Login Illustration">
    </div>

    <!-- Form -->
    <div class="form-section">
        <h2>Admin Login</h2>
        <form action="admin_login.php" method="post">
            <?php csrf_input(); ?>

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="admin_name" class="form-control" required placeholder="Enter admin username">
            </div>

            <div class="mb-4">
                <label class="form-label">Password</label>
                <input type="password" name="admin_password" class="form-control" required placeholder="Enter password">
            </div>

            <div class="d-grid">
                <button type="submit" name="admin_login" class="btn btn-info text-white">Login</button>
            </div>
        </form>
        <p class="form-text mt-3">Don't have an account? <a href="admin_register.php">Register</a></p>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
