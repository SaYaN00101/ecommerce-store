<?php
include('admin_guard.php');
include('../includes/connect.php');

if (isset($_GET['delete_brand'])) {
    $brand_id = (int)$_GET['delete_brand'];
    $stmt = mysqli_prepare($con, "DELETE FROM brands WHERE brand_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $brand_id);
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Brand deleted successfully'); window.location.href='index.php?view_brands';</script>";
    } else {
        echo "<div class='alert alert-danger'>Deletion failed. Please try again.</div>";
    }
    mysqli_stmt_close($stmt);
}
