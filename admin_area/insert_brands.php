<?php
include('admin_guard.php');
include('../includes/connect.php');
include('../function/csrf.php');
$message = "";

if (isset($_POST['insert_brand'])) {
    if (!verify_csrf_token()) {
        $message = "<div class='alert alert-danger'>Invalid request.</div>";
    } else {
        $brand_title = trim($_POST['brand_title'] ?? '');

        $chk = mysqli_prepare($con, "SELECT brand_id FROM `brands` WHERE brand_title = ?");
        mysqli_stmt_bind_param($chk, 's', $brand_title);
        mysqli_stmt_execute($chk);
        mysqli_stmt_store_result($chk);

        if (mysqli_stmt_num_rows($chk) > 0) {
            $message = "<div class='alert alert-danger'>Brand already exists in the database.</div>";
        } else {
            mysqli_stmt_close($chk);
            $ins = mysqli_prepare($con, "INSERT INTO `brands` (brand_title) VALUES (?)");
            mysqli_stmt_bind_param($ins, 's', $brand_title);
            $message = mysqli_stmt_execute($ins)
                ? "<div class='alert alert-success'>Brand has been inserted successfully.</div>"
                : "<div class='alert alert-danger'>Error inserting brand.</div>";
            mysqli_stmt_close($ins);
        }
        if (isset($chk) && $chk) mysqli_stmt_close($chk);
    }
}
?>

<?php if (!empty($message)) echo $message; ?>

<h2 class="text-center">Insert Brands</h2>
<form action="" method="post" class="mb-2">
    <?php csrf_input(); ?>
    <div class="input-group w-90 mb-2">
        <span class="input-group-text bg-info" id="basic-addon1"><i class="fa-solid fa-tag"></i></span>
        <input type="text" class="form-control" name="brand_title" placeholder="Insert Brand" required>
    </div>
    <div class="input-group w-90 mb-2 m-auto">
        <input type="submit" class="bg-info border-0 p-2 my-3" name="insert_brand" value="Insert Brand">
    </div>
</form>

<script>
    setTimeout(() => { document.querySelector(".alert")?.remove(); }, 5000);
</script>
