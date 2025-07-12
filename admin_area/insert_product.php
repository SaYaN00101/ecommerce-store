<?php
include('../includes/connect.php');

$message = '';

if (isset($_POST['insert_product'])) {
    $product_title = $_POST['product_title'];
    $product_description = $_POST['product_description'];
    $product_keywords = $_POST['product_keywords'];
    $product_category = $_POST['product_category'];
    $product_brands = $_POST['product_brands'];
    $product_price = $_POST['product_price'];
    $product_status = 'true';

    // Image uploads
    $product_image1 = $_FILES['product_image1']['name'];
    $product_image2 = $_FILES['product_image2']['name'];
    $product_image3 = $_FILES['product_image3']['name'];

    $temp_image1 = $_FILES['product_image1']['tmp_name'];
    $temp_image2 = $_FILES['product_image2']['tmp_name'];
    $temp_image3 = $_FILES['product_image3']['tmp_name'];

    if (
        empty($product_title) || empty($product_description) || empty($product_keywords) ||
        empty($product_category) || empty($product_brands) || empty($product_price) ||
        empty($product_image1) || empty($product_image2) || empty($product_image3)
    ) {
        $message = "<div class='alert alert-danger'>Please fill all the available fields.</div>";
    } else {
        move_uploaded_file($temp_image1, "./product_images/$product_image1");
        move_uploaded_file($temp_image2, "./product_images/$product_image2");
        move_uploaded_file($temp_image3, "./product_images/$product_image3");

        $insert_products = "INSERT INTO `products` 
        (product_title, product_description, product_keywords, category_id, brand_id, product_image1, product_image2, product_image3, product_price, date, status) 
        VALUES 
        ('$product_title', '$product_description', '$product_keywords', '$product_category', '$product_brands', '$product_image1', '$product_image2', '$product_image3', '$product_price', NOW(), '$product_status')";

        $result_query = mysqli_query($con, $insert_products);

        if ($result_query) {
            $message = "<div class='alert alert-success'>Product inserted successfully.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Failed to insert product.</div>";
        }
    }
}

// Display the form
?>

<h3 class="text-center text-info">Insert New Product</h3>
<?php if (!empty($message)) echo $message; ?>

<form action="" method="post" enctype="multipart/form-data">
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="product_title" class="form-label">Product Title</label>
            <input type="text" class="form-control" id="product_title" name="product_title" placeholder="Enter product title">
        </div>
        <div class="col-md-6">
            <label for="product_keywords" class="form-label">Product Keywords</label>
            <input type="text" class="form-control" id="product_keywords" name="product_keywords" placeholder="Enter product keywords">
        </div>
    </div>

    <div class="mb-3">
        <label for="product_description" class="form-label">Product Description</label>
        <textarea class="form-control" id="product_description" name="product_description" rows="3" placeholder="Enter description"></textarea>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="product_category" class="form-label">Select Category</label>
            <select name="product_category" class="form-select" id="product_category" required>
                <option value="" disabled selected>Choose a category</option>
                <?php
                $select_query = "SELECT * FROM categories";
                $result_query = mysqli_query($con, $select_query);
                while ($row = mysqli_fetch_assoc($result_query)) {
                    echo "<option value='{$row['category_id']}'>{$row['category_title']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-6">
            <label for="product_brands" class="form-label">Select Brand</label>
            <select name="product_brands" class="form-select" id="product_brands" required>
                <option value="" disabled selected>Choose a brand</option>
                <?php
                $brand_query = "SELECT * FROM brands";
                $brand_result = mysqli_query($con, $brand_query);
                while ($row = mysqli_fetch_assoc($brand_result)) {
                    echo "<option value='{$row['brand_id']}'>{$row['brand_title']}</option>";
                }
                ?>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label for="product_image1" class="form-label">Product Image 1</label>
            <input type="file" class="form-control" id="product_image1" name="product_image1" required>
        </div>
        <div class="col-md-4">
            <label for="product_image2" class="form-label">Product Image 2</label>
            <input type="file" class="form-control" id="product_image2" name="product_image2" required>
        </div>
        <div class="col-md-4">
            <label for="product_image3" class="form-label">Product Image 3</label>
            <input type="file" class="form-control" id="product_image3" name="product_image3" required>
        </div>
    </div>

    <div class="mb-4">
        <label for="product_price" class="form-label">Product Price (₹)</label>
        <input type="text" class="form-control" id="product_price" name="product_price" placeholder="Enter price" required>
    </div>

    <div class="text-center">
        <button type="submit" name="insert_product" class="btn btn-info text-white px-4">Insert Product</button>
    </div>
</form>
