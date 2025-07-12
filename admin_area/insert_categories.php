<?php
include('../includes/connect.php');
$message = ""; // Initialize message variable

if(isset($_POST['insert_cat'])){
  $category_title = $_POST['category_title'];

  // Check if category already exists
  $select_query = "SELECT * FROM `categories` WHERE category_title='$category_title'";
  $result_select = mysqli_query($con, $select_query);
  $number = mysqli_num_rows($result_select); // Corrected function

  if($number > 0){
    $message = "<div class='alert alert-danger'>Category already exists in the database.</div>";
  } else {
    // Insert new category
    $insert_query = "INSERT INTO `categories` (category_title) VALUES ('$category_title')";
    $result = mysqli_query($con, $insert_query);

    if($result){
      $message = "<div class='alert alert-success'>Category has been inserted successfully.</div>";
    } else {
      $message = "<div class='alert alert-danger'>Error inserting category.</div>";
    }
  }
}
?>

<!-- Display Message Inside the Page -->
<?php if(!empty($message)) echo $message; ?>

<!-- Category Insertion Form -->
 <h2 class="text-center">Insert Categories</h2>
<form action="" method="post" class="mb-2">
  <div class="input-group w-90 mb-2">
    <span class="input-group-text bg-info" id="basic-addon1"><i class="fa-solid fa-receipt"></i></span>
    <input type="text" class="form-control" name="category_title" placeholder="Insert Categories" aria-label="Username" aria-describedby="basic-addon1">
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
