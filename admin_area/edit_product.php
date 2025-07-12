<?php
include('../includes/connect.php');

// Get product ID from query
if (isset($_GET['edit_product'])) {
    $product_id = $_GET['edit_product'];
    
    // Fetch product details
    $get_product = "SELECT * FROM `products` WHERE product_id = $product_id";
    $result = mysqli_query($con, $get_product);
    $row = mysqli_fetch_assoc($result);

    $title = $row['product_title'];
    $description = $row['product_description'];
    $keywords = $row['product_keywords'];
    $category_id = $row['category_id'];
    $brand_id = $row['brand_id'];
    $price = $row['product_price'];
    $image1 = $row['product_image1'];
}

if (isset($_POST['update_product'])) {
    $title = $_POST['product_title'];
    $description = $_POST['product_description'];
    $keywords = $_POST['product_keywords'];
    $category_id = $_POST['product_category'];
    $brand_id = $_POST['product_brands'];
    $price = $_POST['product_price'];

    // Image Upload Handling
    $product_image1 = $_FILES['product_image1']['name'];
    $temp_image1 = $_FILES['product_image1']['tmp_name'];

    if (!empty($product_image1)) {
        move_uploaded_file($temp_image1, "./product_images/$product_image1");
    } else {
        $product_image1 = $image1; // Keep old image if none uploaded
    }

    $update_query = "UPDATE `products` SET 
        product_title='$title', 
        product_description='$description', 
        product_keywords='$keywords', 
        category_id='$category_id', 
        brand_id='$brand_id', 
        product_price='$price', 
        product_image1='$product_image1', 
        date=NOW() 
        WHERE product_id=$product_id";

    $result_update = mysqli_query($con, $update_query);

    if ($result_update) {
        echo "<script>alert('Product updated successfully.'); window.location.href='index.php?view_products';</script>";
    }
}
?>

<div class="container mt-4">
    <h3 class="text-center text-primary">Edit Product</h3>
    <form action="" method="post" enctype="multipart/form-data" class="w-75 m-auto">
        <div class="mb-3">
            <label>Product Title</label>
            <input type="text" name="product_title" value="<?= $title ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Product Description</label>
            <input type="text" name="product_description" value="<?= $description ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Product Keywords</label>
            <input type="text" name="product_keywords" value="<?= $keywords ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Category</label>
            <select name="product_category" class="form-select" required>
                <?php
                $categories = mysqli_query($con, "SELECT * FROM `categories`");
                while ($cat = mysqli_fetch_assoc($categories)) {
                    $selected = ($cat['category_id'] == $category_id) ? 'selected' : '';
                    echo "<option value='{$cat['category_id']}' $selected>{$cat['category_title']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Brand</label>
            <select name="product_brands" class="form-select" required>
                <?php
                $brands = mysqli_query($con, "SELECT * FROM `brands`");
                while ($b = mysqli_fetch_assoc($brands)) {
                    $selected = ($b['brand_id'] == $brand_id) ? 'selected' : '';
                    echo "<option value='{$b['brand_id']}' $selected>{$b['brand_title']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Price</label>
            <input type="text" name="product_price" value="<?= $price ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Product Image</label>
            <input type="file" name="product_image1" class="form-control">
            <img src="./product_images/<?= $image1 ?>" class="mt-2" style="width:100px;">
        </div>
        <div class="text-center">
            <button type="submit" name="update_product" class="btn btn-success">Update Product</button>
        </div>
    </form>
</div>
