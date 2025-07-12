<?php
include('../includes/connect.php');
$message = ""; // Initialize message variable

if(isset($_POST['insert_brand'])){
  $brand_title = $_POST['brand_title'];

  // Check if brand already exists
  $select_query = "SELECT * FROM `brands` WHERE brand_title='$brand_title'";
  $result_select = mysqli_query($con, $select_query);
  $number = mysqli_num_rows($result_select); // Count rows

  if($number > 0){
    $message = "<div class='alert alert-danger'>Brand already exists in the database.</div>";
  } else {
    // Insert new brand
    $insert_query = "INSERT INTO `brands` (brand_title) VALUES ('$brand_title')";
    $result = mysqli_query($con, $insert_query);

    if($result){
      $message = "<div class='alert alert-success'>Brand has been inserted successfully.</div>";
    } else {
      $message = "<div class='alert alert-danger'>Error inserting brand.</div>";
    }
  }
}
?>

<!-- Display Message Inside the Page -->
<?php if(!empty($message)) echo $message; ?>

<!-- Brand Insertion Form -->
<h2 class="text-center">Insert Brands</h2>
<form action="" method="post" class="mb-2">
  <div class="input-group w-90 mb-2">
    <span class="input-group-text bg-info" id="basic-addon1"><i class="fa-solid fa-tag"></i></span>
    <input type="text" class="form-control" name="brand_title" placeholder="Insert Brand" aria-label="brand" aria-describedby="basic-addon1">
  </div>
  <div class="input-group w-90 mb-2 m-auto">
    <input type="submit" class="bg-info border-0 p-2 my-3" name="insert_brand" value="Insert Brand">
  </div>
</form>

<!-- Auto-hide Message After 5 Seconds -->
<script>
    setTimeout(() => {
        document.querySelector(".alert")?.remove();
    }, 5000);
</script>
