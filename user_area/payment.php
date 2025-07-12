<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include(__DIR__ . '/../includes/connect.php');
include(__DIR__ . '/../function/common_function.php');


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../sty.css">
  <link rel="stylesheet" href="sty.css">

  <style>
    body {
      overflow-x: hidden;
    }
  </style>
</head>

<body>

  <div class="container-fluid p-0">
    <!-- Page Header -->
    <div class="bg-light text-center p-3">
      <h3>Payment</h3>
      <p>Complete your purchase securely</p>
    </div>
    <?php
    // IP Function

    $user_ip = getUserIP();
    $get_user = "SELECT * FROM `user_table` WHERE user_ip='$user_ip'";
    $result = mysqli_query($con, $get_user);
    $run_query = mysqli_fetch_array($result);
    $user_id = $run_query['user_id'];

    ?>

    <!-- Payment Section -->
    <div class="container my-5">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="card p-4 shadow-lg">
            <h4 class="text-center mb-4">Choose Payment Method</h4>

            <form action="order.php?user_id=<?php echo $user_id ?>" method="post">
              <div class="form-check mb-3">
                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                <label class="form-check-label" for="cod">Cash on Delivery</label>
              </div>
              <div class="form-check mb-4">
                <input class="form-check-input" type="radio" name="payment_method" id="online" value="online">
                <label class="form-check-label" for="online">Online Payment</label>
              </div>
              <div class="text-center">
                <button type="submit" class="btn btn-success w-50">Proceed</button>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
  

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>