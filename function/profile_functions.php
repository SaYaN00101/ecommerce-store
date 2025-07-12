<?php
// Profile Functions - Handles Pending Orders, Edit, View, and Delete Account
include(__DIR__ . '/../includes/connect.php');

// Get user_id from session
function get_user_id() {
  global $con;
  if (!isset($_SESSION['username'])) return null;

  $username = $_SESSION['username'];
  $query = "SELECT user_id FROM user_table WHERE user_name = '$username'";
  $result = mysqli_query($con, $query);
  $row = mysqli_fetch_assoc($result);
  return $row['user_id'] ?? null;
}

// 1. Show Pending Orders
function show_pending_orders() {
  global $con;

  $user_id = get_user_id();
  if (!$user_id) {
    echo "<p class='text-danger'>Login to see pending orders.</p>";
    return;
  }

  $query = "SELECT * FROM `orders_pending` WHERE user_id = $user_id AND order_status = 'pending' ORDER BY pending_id DESC";
  $result = mysqli_query($con, $query);

  if (mysqli_num_rows($result) == 0) {
    echo "<h4 class='text-center text-secondary'>No pending orders.</h4>";
  } else {
    echo "<h4 class='text-center mb-4'>Pending Orders</h4>";
    echo "<table class='table table-bordered text-center'>
            <thead class='table-warning'>
              <tr>
                <th>#</th>
                <th>Invoice</th>
                <th>Product ID</th>
                <th>Quantity</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>";
    $i = 1;
    while ($row = mysqli_fetch_assoc($result)) {
      echo "<tr>
              <td>$i</td>
              <td>{$row['invoice_number']}</td>
              <td>{$row['product_id']}</td>
              <td>{$row['quantity']}</td>
              <td class='text-danger fw-bold'>" . ucfirst($row['order_status']) . "</td>
            </tr>";
      $i++;
    }
    echo "</tbody></table>";
  }
}

// 2. Show All Orders
function get_order_details() {
  global $con;

  $user_id = get_user_id();
  if (!$user_id) {
    echo "<p class='text-danger'>Login to see orders.</p>";
    return;
  }

  $query = "SELECT * FROM user_orders WHERE user_id = $user_id ORDER BY order_id DESC";
  $result = mysqli_query($con, $query);

  if (mysqli_num_rows($result) == 0) {
    echo "<h4 class='text-center text-secondary'>No orders found.</h4>";
  } else {
    echo "<h4 class='text-center mb-4'>Your Orders</h4>";
    echo "<table class='table table-bordered text-center'>
            <thead class='table-info'>
              <tr>
                <th>#</th>
                <th>Invoice No</th>
                <th>Amount</th>
                <th>Total Products</th>
                <th>Order Date</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>";
    $i = 1;
    while ($row = mysqli_fetch_assoc($result)) {
      echo "<tr>
              <td>$i</td>
              <td>{$row['invoice_number']}</td>
              <td>₹{$row['amount_due']}</td>
              <td>{$row['total_products']}</td>
              <td>{$row['order_date']}</td>
              <td class='" . ($row['order_status'] == 'pending' ? 'text-danger' : 'text-success') . "'>" . ucfirst($row['order_status']) . "</td>
            </tr>";
      $i++;
    }
    echo "</tbody></table>";
  }
}

//Update form 
function show_edit_account_form() {
  global $con;

  if (!isset($_SESSION['username'])) {
    echo "<p class='text-danger'>Please log in to edit your account.</p>";
    return;
  }

  $username = $_SESSION['username'];
  $query = "SELECT * FROM user_table WHERE user_name = '$username'";
  $result = mysqli_query($con, $query);
  $row = mysqli_fetch_assoc($result);

  $user_id = $row['user_id'];
  $email = $row['user_email'];
  $address = $row['user_address'];
  $mobile = $row['user_mobile'];

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_account'])) {
    $new_name = mysqli_real_escape_string($con, $_POST['user_name']);
    $new_email = mysqli_real_escape_string($con, $_POST['user_email']);
    $new_password = $_POST['user_password'];
    $new_address = mysqli_real_escape_string($con, $_POST['user_address']);
    $new_mobile = mysqli_real_escape_string($con, $_POST['user_mobile']);

    $update_query = "UPDATE user_table SET 
                      user_name = '$new_name',
                      user_email = '$new_email',
                      user_address = '$new_address',
                      user_mobile = '$new_mobile'";

    // Only update password if entered
    if (!empty($new_password)) {
      $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
      $update_query .= ", user_password = '$hashed_password'";
    }

    $update_query .= " WHERE user_id = $user_id";

    $run_update = mysqli_query($con, $update_query);

    if ($run_update) {
      $_SESSION['username'] = $new_name;
      //echo "<div class='alert alert-success'>Account updated successfully!</div>";
      popup_redirect('Account updated successfully!', 'profile.php?edit_account', 1000, 'sucess', 'top-center');
    } else {
      //echo "<div class='alert alert-danger'>Failed to update account. Please try again.</div>";
      popup_redirect('Failed to update account. Please try again.', '', 2000, 'error', 'top-center');
    }
  }

  echo "
  <h4 class='text-center mb-4'>Edit Account</h4>
  <form method='post' class='w-50 mx-auto'>
    <div class='mb-3'>
      <label>Username</label>
      <input type='text' name='user_name' class='form-control' value='" . htmlspecialchars($username) . "' required>
    </div>
    <div class='mb-3'>
      <label>Email</label>
      <input type='email' name='user_email' class='form-control' value='" . htmlspecialchars($email) . "' required>
    </div>
    <div class='mb-3'>
      <label>New Password (leave blank to keep unchanged)</label>
      <input type='password' name='user_password' class='form-control'>
    </div>
    <div class='mb-3'>
      <label>Address</label>
      <textarea name='user_address' class='form-control' rows='2' required>" . htmlspecialchars($address) . "</textarea>
    </div>
    <div class='mb-3'>
      <label>Mobile</label>
      <input type='text' name='user_mobile' class='form-control' value='" . htmlspecialchars($mobile) . "' required>
    </div>
    <button type='submit' name='update_account' class='btn btn-info'>Update</button>
  </form>";
}


// 3. Update Account Info
function update_user_account($username, $new_email, $new_address) {
  global $con;
  $update = "UPDATE user_table SET user_email='$new_email', user_address='$new_address' WHERE user_name='$username'";
  return mysqli_query($con, $update);
}


// delete form 

function show_delete_account_form() {
  global $con;

  if (!isset($_SESSION['username'])) {
    echo "<p class='text-danger'>Please log in to delete your account.</p>";
    return;
  }

  $username = $_SESSION['username'];

  // Handle form submission
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    if (delete_user_account($username)) {
      session_unset();
      session_destroy();
      //echo "<script>alert('Account deleted successfully.'); window.location.href='../index.php';</script>";
      popup_redirect('Account deleted successfully.', '../index.php', 1000, 'sucess', 'top-center');
      exit();
    } else {
      echo "<div class='alert alert-danger'>Failed to delete account.</div>";
      popup_redirect('Failed to delete account.', '', 2000, 'error', 'top-center');
    }
  }

  // Show confirmation form
  echo "
  <h4 class='text-center mb-4 text-danger'>Delete Account</h4>
  <form method='post' class='text-center'>
    <p class='mb-3'>Are you sure you want to delete your account? This action cannot be undone.</p>
    <div class='form-check d-flex justify-content-center mb-3'>
      <input class='form-check-input' type='checkbox' id='confirmDelete' name='confirm_delete_checkbox'>
      <label class='form-check-label ms-2' for='confirmDelete'>I understand the consequences</label>
    </div>
    <button type='submit' class='btn btn-danger' id='deleteBtn' name='confirm_delete' disabled>
      Yes, Delete My Account
    </button>
  </form>

  <script>
    const checkbox = document.getElementById('confirmDelete');
    const button = document.getElementById('deleteBtn');
    checkbox.addEventListener('change', () => {
      button.disabled = !checkbox.checked;
    });
  </script>
  ";
}



// 4. Delete Account
function delete_user_account($username) {
  global $con;

  // Get user_id
  $get_id_query = "SELECT user_id FROM user_table WHERE user_name = '$username'";
  $get_id_result = mysqli_query($con, $get_id_query);
  $user_data = mysqli_fetch_assoc($get_id_result);
  $user_id = $user_data['user_id'] ?? null;

  if (!$user_id) return false;

  // Delete from dependent tables first
  mysqli_query($con, "DELETE FROM orders_pending WHERE user_id = $user_id");
  mysqli_query($con, "DELETE FROM user_orders WHERE user_id = $user_id");

  // Then delete from user_table
  $delete_query = "DELETE FROM user_table WHERE user_id = $user_id";
  return mysqli_query($con, $delete_query);
}
