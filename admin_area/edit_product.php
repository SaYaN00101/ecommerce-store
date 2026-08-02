<?php
include('admin_guard.php');
include('../includes/connect.php');
include('../function/csrf.php');

define('EP_ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('EP_ALLOWED_EXTS',  ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('EP_MAX_SIZE', 5 * 1024 * 1024);

$product_id = isset($_GET['edit_product']) ? (int)$_GET['edit_product'] : 0;
$title = $description = $keywords = $price = $image1 = '';
$category_id = $brand_id = 0;

if ($product_id > 0) {
    $stmt = mysqli_prepare($con, "SELECT * FROM `products` WHERE product_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row    = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($row) {
        $title       = $row['product_title'];
        $description = $row['product_description'];
        $keywords    = $row['product_keywords'];
        $category_id = (int)$row['category_id'];
        $brand_id    = (int)$row['brand_id'];
        $price       = $row['product_price'];
        $image1      = $row['product_image1'];
    }
}

if (isset($_POST['update_product'])) {
    if (!verify_csrf_token()) {
        echo "<div class='alert alert-danger'>Invalid request.</div>";
    } else {
        $new_title       = trim($_POST['product_title']       ?? '');
        $new_description = trim($_POST['product_description'] ?? '');
        $new_keywords    = trim($_POST['product_keywords']    ?? '');
        $new_category    = (int)($_POST['product_category']   ?? 0);
        $new_brand       = (int)($_POST['product_brands']     ?? 0);
        $new_price       = (float)($_POST['product_price']    ?? 0);

        // Handle optional image upload
        $new_image1  = $image1; // keep existing by default
        $upload_file = $_FILES['product_image1'] ?? null;

        if ($upload_file && $upload_file['error'] === UPLOAD_ERR_OK && !empty($upload_file['name'])) {
            $ext = strtolower(pathinfo($upload_file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, EP_ALLOWED_EXTS, true) ||
                !in_array(mime_content_type($upload_file['tmp_name']), EP_ALLOWED_TYPES, true) ||
                $upload_file['size'] > EP_MAX_SIZE
            ) {
                echo "<div class='alert alert-danger'>Invalid image. Use JPG/PNG/WEBP under 5 MB.</div>";
                goto render_form;
            }
            $new_image1 = bin2hex(random_bytes(8)) . '.' . $ext;
            move_uploaded_file($upload_file['tmp_name'], __DIR__ . "/../images/product_images/{$new_image1}");
        }

        $upd = mysqli_prepare($con,
            "UPDATE `products` SET
               product_title=?, product_description=?, product_keywords=?,
               category_id=?, brand_id=?, product_price=?, product_image1=?, date=NOW()
             WHERE product_id=?"
        );
        mysqli_stmt_bind_param($upd, 'sssiisd i',
            $new_title, $new_description, $new_keywords,
            $new_category, $new_brand, $new_price, $new_image1, $product_id
        );
        // fix spacing in bind string
        mysqli_stmt_close($upd);

        $upd2 = mysqli_prepare($con,
            "UPDATE `products` SET
               product_title=?, product_description=?, product_keywords=?,
               category_id=?, brand_id=?, product_price=?, product_image1=?, date=NOW()
             WHERE product_id=?"
        );
        mysqli_stmt_bind_param($upd2, 'sssiidsi',
            $new_title, $new_description, $new_keywords,
            $new_category, $new_brand, $new_price, $new_image1, $product_id
        );

        if (mysqli_stmt_execute($upd2)) {
            mysqli_stmt_close($upd2);
            // Refresh local variables
            $title = $new_title; $description = $new_description;
            $keywords = $new_keywords; $price = $new_price;
            $category_id = $new_category; $brand_id = $new_brand;
            $image1 = $new_image1;
            echo "<script>alert('Product updated successfully.'); window.location.href='index.php?view_products';</script>";
        } else {
            mysqli_stmt_close($upd2);
            echo "<div class='alert alert-danger'>Update failed. Please try again.</div>";
        }
    }
}

render_form:
?>

<div class="container mt-4">
    <h3 class="text-center text-primary">Edit Product</h3>
    <form action="" method="post" enctype="multipart/form-data" class="w-75 m-auto">
        <?php csrf_input(); ?>
        <div class="mb-3">
            <label>Product Title</label>
            <input type="text" name="product_title"
                   value="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>"
                   class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Product Description</label>
            <input type="text" name="product_description"
                   value="<?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?>"
                   class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Product Keywords</label>
            <input type="text" name="product_keywords"
                   value="<?= htmlspecialchars($keywords, ENT_QUOTES, 'UTF-8') ?>"
                   class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Category</label>
            <select name="product_category" class="form-select" required>
                <?php
                $cats = mysqli_query($con, "SELECT * FROM `categories`");
                while ($cat = mysqli_fetch_assoc($cats)) {
                    $sel = ($cat['category_id'] == $category_id) ? 'selected' : '';
                    $cid = (int)$cat['category_id'];
                    $ctitle = htmlspecialchars($cat['category_title'], ENT_QUOTES, 'UTF-8');
                    echo "<option value='{$cid}' {$sel}>{$ctitle}</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Brand</label>
            <select name="product_brands" class="form-select" required>
                <?php
                $brnds = mysqli_query($con, "SELECT * FROM `brands`");
                while ($b = mysqli_fetch_assoc($brnds)) {
                    $sel = ($b['brand_id'] == $brand_id) ? 'selected' : '';
                    $bid = (int)$b['brand_id'];
                    $btitle = htmlspecialchars($b['brand_title'], ENT_QUOTES, 'UTF-8');
                    echo "<option value='{$bid}' {$sel}>{$btitle}</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Price (₹)</label>
            <input type="number" step="0.01" min="0.01" name="product_price"
                   value="<?= htmlspecialchars((string)$price, ENT_QUOTES, 'UTF-8') ?>"
                   class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Product Image <small class="text-muted">(leave blank to keep existing)</small></label>
            <input type="file" name="product_image1" class="form-control"
                   accept=".jpg,.jpeg,.png,.gif,.webp">
            <?php if ($image1): ?>
                <img src="../images/product_images/<?= htmlspecialchars($image1, ENT_QUOTES, 'UTF-8') ?>"
                     class="mt-2" style="width:100px;">
            <?php endif; ?>
        </div>
        <div class="text-center">
            <button type="submit" name="update_product" class="btn btn-success">Update Product</button>
        </div>
    </form>
</div>
