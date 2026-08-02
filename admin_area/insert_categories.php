<?php
include('admin_guard.php');
include('../includes/connect.php');
include('../function/csrf.php');
$message = "";

if (isset($_POST['insert_cat'])) {
    if (!verify_csrf_token()) {
        $message = "<div class='alert alert-danger'>Invalid request.</div>";
    } else {
        $category_title = trim($_POST['category_title'] ?? '');

        $chk = mysqli_prepare($con, "SELECT category_id FROM `categories` WHERE category_title = ?");
        mysqli_stmt_bind_param($chk, 's', $category_title);
        mysqli_stmt_execute($chk);
        mysqli_stmt_store_result($chk);

        if (mysqli_stmt_num_rows($chk) > 0) {
            $message = "<div class='alert alert-danger'>Category already exists in the database.</div>";
        } else {
            mysqli_stmt_close($chk);
            $ins = mysqli_prepare($con, "INSERT INTO `categories` (category_title) VALUES (?)");
            mysqli_stmt_bind_param($ins, 's', $category_title);
            $message = mysqli_stmt_execute($ins)
                ? "<div class='alert alert-success'>Category has been inserted successfully.</div>"
                : "<div class='alert alert-danger'>Error inserting category.</div>";
            mysqli_stmt_close($ins);
        }
        if (isset($chk) && $chk) mysqli_stmt_close($chk);
    }
}
?>

<!-- Display Message Inside the Page -->
<?php if(!empty($message)) echo $message; ?>

<!-- Category Insertion Form -->
 <h2 class="text-center">Insert Categories</h2>
<form action="" method="post" class="mb-2">
  <?php csrf_input(); ?>
  <div class="input-group w-90 mb-2">
    <span class="input-group-text bg-info" id="basic-addon1"><i class="fa-solid fa-receipt"></i></span>
    <input type="text" class="form-control" name="category_title" placeholder="Insert Categories" aria-label="Username" aria-describedby="basic-addon1" required>
  </div>
  <div class="input-group w-90 mb-2 m-auto">
    <input type="submit" class="bg-info border-0 p-2 my-3" name="insert_cat" value="Insert Categories">
  </div>
</form>

<!-- Auto-hide Message After 3 Seconds -->
<script>
    setTimeout(() => {
        document.querySelector(".alert")?.remove();
    }, 5000);
</script>
