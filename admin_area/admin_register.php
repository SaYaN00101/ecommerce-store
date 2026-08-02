<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . '/../includes/connect.php');
include(__DIR__ . '/../function/common_function.php');
include(__DIR__ . '/../function/csrf.php');

// ── Only an already-logged-in admin can register a new admin ──────────────
if (!isset($_SESSION['admin_name'])) {
    popup_redirect('You must be logged in as admin to register a new admin.', 'admin_login.php', 2000, 'error', 'top-center');
    exit();
}

define('ADMIN_ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ADMIN_ALLOWED_IMAGE_EXTS',  ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('ADMIN_MAX_FILE_SIZE', 2 * 1024 * 1024); // 2 MB

if (isset($_POST['admin_register'])) {
    // CSRF check
    if (!verify_csrf_token()) {
        popup_redirect('Invalid request. Please try again.', 'admin_register.php', 2000, 'error', 'top-center');
        exit();
    }

    $admin_name       = trim($_POST['admin_name']      ?? '');
    $admin_email      = trim($_POST['admin_email']     ?? '');
    $admin_password   = $_POST['admin_password']       ?? '';
    $confirm_password = $_POST['confirm_password']     ?? '';

    $image_file  = $_FILES['admin_image'] ?? null;
    $image_name  = $image_file['name']     ?? '';
    $image_tmp   = $image_file['tmp_name'] ?? '';
    $image_size  = $image_file['size']     ?? 0;
    $image_error = $image_file['error']    ?? UPLOAD_ERR_NO_FILE;

    // Basic field validation
    if (empty($admin_name) || empty($admin_email) || empty($admin_password) || empty($confirm_password)) {
        popup_redirect('Please fill all fields.', '', 3000, 'error', 'top-center');
        exit();
    }
    if (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
        popup_redirect('Invalid email address.', '', 3000, 'error', 'top-center');
        exit();
    }
    if ($admin_password !== $confirm_password) {
        popup_redirect('Passwords do not match!', '', 3000, 'error', 'top-center');
        exit();
    }
    if ($image_error !== UPLOAD_ERR_OK || empty($image_name)) {
        popup_redirect('Please upload a profile image.', '', 3000, 'error', 'top-center');
        exit();
    }

    // ── File upload validation ─────────────────────────────────────────────
    $ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
    if (!in_array($ext, ADMIN_ALLOWED_IMAGE_EXTS, true)) {
        popup_redirect('Only JPG, PNG, GIF, and WEBP images are allowed.', '', 3000, 'error', 'top-center');
        exit();
    }
    if ($image_size > ADMIN_MAX_FILE_SIZE) {
        popup_redirect('Image must be under 2 MB.', '', 3000, 'error', 'top-center');
        exit();
    }
    if (!in_array(mime_content_type($image_tmp), ADMIN_ALLOWED_IMAGE_TYPES, true)) {
        popup_redirect('Uploaded file is not a valid image.', '', 3000, 'error', 'top-center');
        exit();
    }

    // ── Check if admin already exists ─────────────────────────────────────
    $check = mysqli_prepare($con, "SELECT admin_id FROM `admin_table` WHERE admin_name = ? OR admin_email = ?");
    mysqli_stmt_bind_param($check, 'ss', $admin_name, $admin_email);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {
        mysqli_stmt_close($check);
        popup_redirect('Admin with that name or email already exists!', '', 3000, 'error', 'top-center');
        exit();
    }
    mysqli_stmt_close($check);

    // Generate unique safe filename
    $safe_filename = bin2hex(random_bytes(8)) . '.' . $ext;
    $upload_path   = __DIR__ . '/admin_images/' . $safe_filename;

    if (!move_uploaded_file($image_tmp, $upload_path)) {
        popup_redirect('Failed to upload image. Please try again.', '', 2000, 'error', 'top-center');
        exit();
    }

    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

    $insert = mysqli_prepare($con,
        "INSERT INTO `admin_table` (admin_name, admin_email, admin_password, admin_image, register_date)
         VALUES (?, ?, ?, ?, NOW())"
    );
    mysqli_stmt_bind_param($insert, 'ssss', $admin_name, $admin_email, $hashed_password, $safe_filename);

    if (mysqli_stmt_execute($insert)) {
        mysqli_stmt_close($insert);
        popup_redirect('Admin registered successfully!', 'index.php', 2000, 'success', 'top-center');
    } else {
        mysqli_stmt_close($insert);
        popup_redirect('Registration failed! Please try again.', '', 1000, 'error', 'top-center');
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .form-section  { flex: 1; padding: 40px; }
        .form-section h2 { font-weight: bold; margin-bottom: 30px; }
        .form-control  { border-radius: 8px; }
        .form-text     { text-align: center; }
        .form-text a   { color: #007bff; text-decoration: none; }
        .form-text a:hover { text-decoration: underline; }
        .image-section {
            background: #e0f2ff;
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
        <img src="admin_images/registration_illustration.png" alt="Admin Illustration">
    </div>

    <!-- Form -->
    <div class="form-section">
        <h2>Register New Admin</h2>
        <p class="text-muted small">Logged in as: <strong><?= htmlspecialchars($_SESSION['admin_name'], ENT_QUOTES, 'UTF-8') ?></strong></p>
        <form action="admin_register.php" method="post" enctype="multipart/form-data">
            <?php csrf_input(); ?>

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="admin_name" class="form-control" required placeholder="Enter username">
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="admin_email" class="form-control" required placeholder="Enter email">
            </div>
            <div class="mb-3">
                <label class="form-label">Profile Image <small class="text-muted">(JPG/PNG/GIF/WEBP, max 2 MB)</small></label>
                <input type="file" name="admin_image" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="admin_password" class="form-control" required placeholder="Enter password">
            </div>
            <div class="mb-4">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" required placeholder="Confirm password">
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-info text-white" name="admin_register">Register Admin</button>
            </div>
        </form>
        <p class="form-text mt-3"><a href="index.php">Back to Dashboard</a></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
