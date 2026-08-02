<?php
include('admin_guard.php');
include('../includes/connect.php');
include('../function/csrf.php');

$brand_id    = isset($_GET['edit_brand']) ? (int)$_GET['edit_brand'] : 0;
$brand_title = '';

if ($brand_id > 0) {
    $stmt = mysqli_prepare($con, "SELECT * FROM brands WHERE brand_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $brand_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row    = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    $brand_title = $row['brand_title'] ?? '';
}

if (isset($_POST['update_brand'])) {
    if (!verify_csrf_token()) {
        echo "<div class='alert alert-danger'>Invalid request.</div>";
    } else {
        $updated_title = trim($_POST['brand_title'] ?? '');
        $upd = mysqli_prepare($con, "UPDATE brands SET brand_title = ? WHERE brand_id = ?");
        mysqli_stmt_bind_param($upd, 'si', $updated_title, $brand_id);
        if (mysqli_stmt_execute($upd)) {
            echo "<script>alert('Brand updated successfully'); window.location.href='index.php?view_brands';</script>";
        } else {
            echo "<div class='alert alert-danger'>Update failed. Please try again.</div>";
        }
        mysqli_stmt_close($upd);
    }
}
?>

<h3 class="text-center mb-4">Edit Brand</h3>
<div class="container w-50">
    <form method="post">
        <?php csrf_input(); ?>
        <div class="mb-3">
            <label for="brand_title" class="form-label">Brand Title</label>
            <input type="text" name="brand_title" id="brand_title"
                   value="<?= htmlspecialchars($brand_title, ENT_QUOTES, 'UTF-8') ?>"
                   class="form-control" required>
        </div>
        <div class="text-center">
            <button type="submit" name="update_brand" class="btn btn-primary">Update Brand</button>
        </div>
    </form>
</div>
