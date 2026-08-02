<?php
include('admin_guard.php');
include('../includes/connect.php');

if (isset($_GET['delete_category'])) {
    $category_id = (int)$_GET['delete_category'];
    $stmt = mysqli_prepare($con, "DELETE FROM categories WHERE category_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $category_id);
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Category deleted successfully.'); window.location.href='index.php?view_categories';</script>";
    } else {
        echo "<script>alert('Failed to delete category.'); window.location.href='index.php?view_categories';</script>";
    }
    mysqli_stmt_close($stmt);
}
