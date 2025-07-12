<?php
include('../includes/connect.php');

if (isset($_GET['edit_brand'])) {
    $brand_id = $_GET['edit_brand'];

    // Fetch existing brand data
    $get_brand = "SELECT * FROM brands WHERE brand_id = $brand_id";
    $result = mysqli_query($con, $get_brand);
    $row = mysqli_fetch_assoc($result);
    $brand_title = $row['brand_title'];
}

// Update logic
if (isset($_POST['update_brand'])) {
    $updated_brand_title = $_POST['brand_title'];
    $update_query = "UPDATE brands SET brand_title = '$updated_brand_title' WHERE brand_id = $brand_id";
    $update_result = mysqli_query($con, $update_query);

    if ($update_result) {
        echo "<script>alert('Brand updated successfully'); window.location.href='index.php?view_brands';</script>";
    } else {
        echo "<div class='alert alert-danger'>Update failed. Please try again.</div>";
    }
}
?>

<h3 class="text-center mb-4">Edit Brand</h3>
<div class="container w-50">
    <form method="post">
        <div class="mb-3">
            <label for="brand_title" class="form-label">Brand Title</label>
            <input type="text" name="brand_title" id="brand_title" value="<?= htmlspecialchars($brand_title) ?>" class="form-control" required>
        </div>
        <div class="text-center">
            <button type="submit" name="update_brand" class="btn btn-primary">Update Brand</button>
        </div>
    </form>
</div>
