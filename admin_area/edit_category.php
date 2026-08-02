<?php
include('admin_guard.php');
include('../includes/connect.php');
include('../function/csrf.php');

$category_id    = isset($_GET['edit_category']) ? (int)$_GET['edit_category'] : 0;
$category_title = '';

if ($category_id > 0) {
    $stmt = mysqli_prepare($con, "SELECT * FROM categories WHERE category_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $category_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row    = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    $category_title = $row['category_title'] ?? '';
}

if (isset($_POST['update_category'])) {
    if (!verify_csrf_token()) {
        echo "<div class='alert alert-danger'>Invalid request.</div>";
    } else {
        $updated_title = trim($_POST['category_title'] ?? '');
        $upd = mysqli_prepare($con, "UPDATE categories SET category_title = ? WHERE category_id = ?");
        mysqli_stmt_bind_param($upd, 'si', $updated_title, $category_id);
        if (mysqli_stmt_execute($upd)) {
            echo "<script>alert('Category updated successfully.'); window.location.href='index.php?view_categories';</script>";
        } else {
            echo "<div class='alert alert-danger'>Failed to update category.</div>";
        }
        mysqli_stmt_close($upd);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Category</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h3 class="text-center mb-4">Edit Category</h3>
    <form method="post" class="w-50 m-auto">
        <?php csrf_input(); ?>
        <div class="mb-3">
            <label for="category_title" class="form-label">Category Title</label>
            <input type="text" name="category_title" id="category_title"
                   value="<?= htmlspecialchars($category_title, ENT_QUOTES, 'UTF-8') ?>"
                   class="form-control" required>
        </div>
        <div class="text-center">
            <button type="submit" name="update_category" class="btn btn-primary">Update Category</button>
        </div>
    </form>
</div>
</body>
</html>
