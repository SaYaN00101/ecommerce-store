<?php
include('../includes/connect.php');

// Check if category_id is passed
if (isset($_GET['edit_category'])) {
    $category_id = $_GET['edit_category'];

    // Fetch current category data
    $get_category = "SELECT * FROM categories WHERE category_id = $category_id";
    $result = mysqli_query($con, $get_category);
    $row = mysqli_fetch_assoc($result);
    $category_title = $row['category_title'];
}

// Update category
if (isset($_POST['update_category'])) {
    $updated_title = $_POST['category_title'];

    $update_query = "UPDATE categories SET category_title = '$updated_title' WHERE category_id = $category_id";
    $update_result = mysqli_query($con, $update_query);

    if ($update_result) {
        echo "<script>alert('Category updated successfully.'); window.location.href='index.php';</script>";
    } else {
        echo "<div class='alert alert-danger'>Failed to update category.</div>";
    }
}
?>

<!-- HTML Form -->
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
        <div class="mb-3">
            <label for="category_title" class="form-label">Category Title</label>
            <input type="text" name="category_title" id="category_title" value="<?= htmlspecialchars($category_title) ?>" class="form-control" required>
        </div>
        <div class="text-center">
            <button type="submit" name="update_category" class="btn btn-primary">Update Category</button>
        </div>
    </form>
</div>
</body>
</html>
