<?php
include('admin_guard.php');
include('../includes/connect.php');

if (isset($_GET['delete_product'])) {
    $product_id = (int)$_GET['delete_product'];
    $stmt = mysqli_prepare($con, "DELETE FROM `products` WHERE product_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $product_id);
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Product deleted successfully.'); window.location.href='index.php?view_products';</script>";
    } else {
        echo "<script>alert('Failed to delete product.');</script>";
    }
    mysqli_stmt_close($stmt);
}
