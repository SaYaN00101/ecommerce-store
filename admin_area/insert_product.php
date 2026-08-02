<?php
include('admin_guard.php');
include('../includes/connect.php');
include('../function/csrf.php');

define('PROD_ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('PROD_ALLOWED_EXTS',  ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('PROD_MAX_SIZE',      5 * 1024 * 1024); // 5 MB per image

$message = '';

if (isset($_POST['insert_product'])) {
    if (!verify_csrf_token()) {
        $message = "<div class='alert alert-danger'>Invalid request.</div>";
    } else {
        $product_title       = trim($_POST['product_title']       ?? '');
        $product_description = trim($_POST['product_description'] ?? '');
        $product_keywords    = trim($_POST['product_keywords']    ?? '');
        $product_category    = (int)($_POST['product_category']   ?? 0);
        $product_brands      = (int)($_POST['product_brands']     ?? 0);
        $product_price       = (float)($_POST['product_price']    ?? 0);
        $product_status      = 'true';

        if (
            empty($product_title) || empty($product_description) || empty($product_keywords) ||
            $product_category === 0 || $product_brands === 0 || $product_price <= 0
        ) {
            $message = "<div class='alert alert-danger'>Please fill all the available fields.</div>";
        } else {
            // Validate all 3 images
            $images = [];
            $all_valid = true;
            foreach ([1, 2, 3] as $i) {
                $file = $_FILES["product_image{$i}"] ?? null;
                if (!$file || $file['error'] !== UPLOAD_ERR_OK || empty($file['name'])) {
                    $message = "<div class='alert alert-danger'>Please upload all 3 product images.</div>";
                    $all_valid = false;
                    break;
                }
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, PROD_ALLOWED_EXTS, true)) {
                    $message = "<div class='alert alert-danger'>Image {$i}: only JPG, PNG, GIF, WEBP allowed.</div>";
                    $all_valid = false;
                    break;
                }
                if ($file['size'] > PROD_MAX_SIZE) {
                    $message = "<div class='alert alert-danger'>Image {$i} exceeds 5 MB limit.</div>";
                    $all_valid = false;
                    break;
                }
                if (!in_array(mime_content_type($file['tmp_name']), PROD_ALLOWED_TYPES, true)) {
                    $message = "<div class='alert alert-danger'>Image {$i} is not a valid image file.</div>";
                    $all_valid = false;
                    break;
                }
                $safe_name = bin2hex(random_bytes(8)) . '.' . $ext;
                $images[$i] = ['tmp' => $file['tmp_name'], 'name' => $safe_name];
            }

            if ($all_valid) {
                // Move images
                foreach ($images as $i => $img) {
                    move_uploaded_file($img['tmp'], __DIR__ . "/product_images/" . $img['name']);
                }

                $stmt = mysqli_prepare($con,
                    "INSERT INTO `products`
                     (product_title, product_description, product_keywords,
                      category_id, brand_id,
                      product_image1, product_image2, product_image3,
                      product_price, date, status)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)"
                );
                mysqli_stmt_bind_param($stmt, 'sssiiidsss',
                    $product_title, $product_description, $product_keywords,
                    $product_category, $product_brands,
                    $images[1]['name'], $images[2]['name'], $images[3]['name'],
                    $product_price, $product_status
                );
                $message = mysqli_stmt_execute($stmt)
                    ? "<div class='alert alert-success'>Product inserted successfully.</div>"
                    : "<div class='alert alert-danger'>Failed to insert product.</div>";
                mysqli_stmt_close($stmt);
            }
        }
    }
}
?>

<h3 class="text-center text-info">Insert New Product</h3>
<?php if (!empty($message)) echo $message; ?>

<form action="" method="post" enctype="multipart/form-data">
    <?php csrf_input(); ?>
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="product_title" class="form-label">Product Title</label>
            <input type="text" class="form-control" id="product_title" name="product_title"
                   placeholder="Enter product title" required>
        </div>
        <div class="col-md-6">
            <label for="product_keywords" class="form-label">Product Keywords</label>
            <input type="text" class="form-control" id="product_keywords" name="product_keywords"
                   placeholder="Enter product keywords" required>
        </div>
    </div>

    <div class="mb-3">
        <label for="product_description" class="form-label">Product Description</label>
        <textarea class="form-control" id="product_description" name="product_description"
                  rows="3" placeholder="Enter description" required></textarea>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="product_category" class="form-label">Select Category</label>
            <select name="product_category" class="form-select" id="product_category" required>
                <option value="" disabled selected>Choose a category</option>
                <?php
                $cats = mysqli_query($con, "SELECT * FROM categories");
                while ($row = mysqli_fetch_assoc($cats)) {
                    $id    = (int)$row['category_id'];
                    $title = htmlspecialchars($row['category_title'], ENT_QUOTES, 'UTF-8');
                    echo "<option value='{$id}'>{$title}</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-6">
            <label for="product_brands" class="form-label">Select Brand</label>
            <select name="product_brands" class="form-select" id="product_brands" required>
                <option value="" disabled selected>Choose a brand</option>
                <?php
                $brands = mysqli_query($con, "SELECT * FROM brands");
                while ($row = mysqli_fetch_assoc($brands)) {
                    $id    = (int)$row['brand_id'];
                    $title = htmlspecialchars($row['brand_title'], ENT_QUOTES, 'UTF-8');
                    echo "<option value='{$id}'>{$title}</option>";
                }
                ?>
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label for="product_price" class="form-label">Product Price (₹)</label>
        <input type="number" step="0.01" min="0.01" class="form-control"
               id="product_price" name="product_price" placeholder="Enter price" required>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label class="form-label">Product Image 1 <small class="text-muted">(JPG/PNG/WEBP, max 5 MB)</small></label>
            <input type="file" class="form-control" name="product_image1"
                   accept=".jpg,.jpeg,.png,.gif,.webp" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Product Image 2</label>
            <input type="file" class="form-control" name="product_image2"
                   accept=".jpg,.jpeg,.png,.gif,.webp" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Product Image 3</label>
            <input type="file" class="form-control" name="product_image3"
                   accept=".jpg,.jpeg,.png,.gif,.webp" required>
        </div>
    </div>

    <div class="text-center">
        <button type="submit" name="insert_product" class="btn btn-info text-white px-5">
            Insert Product
        </button>
    </div>
</form>
