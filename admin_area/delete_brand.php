<?php
include('../includes/connect.php');

if (isset($_GET['delete_brand'])) {
    $brand_id = $_GET['delete_brand'];

    $delete_query = "DELETE FROM brands WHERE brand_id = $brand_id";
    $delete_result = mysqli_query($con, $delete_query);

    if ($delete_result) {
        echo "<script>alert('Brand deleted successfully'); window.location.href='index.php?view_brands';</script>";
    } else {
        echo "<div class='alert alert-danger'>Deletion failed. Please try again.</div>";
    }
}
