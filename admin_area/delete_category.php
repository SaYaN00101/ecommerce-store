<?php
include('../includes/connect.php');

// Check if category_id is passed
if (isset($_GET['delete_category'])) {
    $category_id = $_GET['delete_category'];

    // Delete category query
    $delete_query = "DELETE FROM categories WHERE category_id = $category_id";
    $delete_result = mysqli_query($con, $delete_query);

    if ($delete_result) {
        echo "<script>
            alert('Category deleted successfully.');
            window.location.href='index.php?view_categories';
        </script>";
    } else {
        echo "<script>
            alert('Failed to delete category.');
            window.location.href='index.php?view_categories';
        </script>";
    }
}
?>
