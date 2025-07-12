<?php
include('../includes/connect.php');

if (isset($_GET['delete_product'])) {
    $product_id = $_GET['delete_product'];

    $delete_query = "DELETE FROM `products` WHERE product_id = $product_id";
    $result = mysqli_query($con, $delete_query);

    if ($result) {
        echo "<script>alert('Product deleted successfully.'); window.location.href='index.php?view_products';</script>";
    } else {
        echo "<script>alert('Failed to delete product.');</script>";
    }
}
?>
