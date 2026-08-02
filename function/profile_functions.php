<?php
// Profile Functions — Pending Orders, Edit, View, Delete Account
include_once(__DIR__ . '/../includes/connect.php');
include_once(__DIR__ . '/../function/csrf.php');

// ─── Get user_id from session ─────────────────────────────────────────────
function get_user_id(): ?int {
    global $con;
    if (!isset($_SESSION['username'])) return null;

    $username = $_SESSION['username'];
    $stmt = mysqli_prepare($con, "SELECT user_id FROM user_table WHERE user_name = ?");
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row    = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return isset($row['user_id']) ? (int)$row['user_id'] : null;
}

// ─── 1. Show Pending Orders ───────────────────────────────────────────────
function show_pending_orders(): void {
    global $con;

    $user_id = get_user_id();
    if (!$user_id) {
        echo "<p class='text-danger'>Login to see pending orders.</p>";
        return;
    }

    $stmt = mysqli_prepare($con,
        "SELECT * FROM `orders_pending`
         WHERE user_id = ? AND order_status = 'pending'
         ORDER BY pending_id DESC"
    );
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 0) {
        echo "<h4 class='text-center text-secondary'>No pending orders.</h4>";
    } else {
        echo "<h4 class='text-center mb-4'>Pending Orders</h4>
              <table class='table table-bordered text-center'>
                <thead class='table-warning'>
                  <tr><th>#</th><th>Invoice</th><th>Product ID</th><th>Quantity</th><th>Status</th></tr>
                </thead><tbody>";
        $i = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $invoice  = htmlspecialchars((string)$row['invoice_number'], ENT_QUOTES, 'UTF-8');
            $prod_id  = (int)$row['product_id'];
            $qty      = (int)$row['quantity'];
            $status   = htmlspecialchars(ucfirst($row['order_status']), ENT_QUOTES, 'UTF-8');
            echo "<tr>
                    <td>{$i}</td>
                    <td>{$invoice}</td>
                    <td>{$prod_id}</td>
                    <td>{$qty}</td>
                    <td class='text-danger fw-bold'>{$status}</td>
                  </tr>";
            $i++;
        }
        echo "</tbody></table>";
    }
    mysqli_stmt_close($stmt);
}

// ─── 2. Show All Orders ───────────────────────────────────────────────────
function get_order_details(): void {
    global $con;

    $user_id = get_user_id();
    if (!$user_id) {
        echo "<p class='text-danger'>Login to see orders.</p>";
        return;
    }

    $stmt = mysqli_prepare($con,
        "SELECT * FROM user_orders WHERE user_id = ? ORDER BY order_id DESC"
    );
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 0) {
        echo "<h4 class='text-center text-secondary'>No orders found.</h4>";
    } else {
        echo "<h4 class='text-center mb-4'>Your Orders</h4>
              <table class='table table-bordered text-center'>
                <thead class='table-info'>
                  <tr>
                    <th>#</th><th>Invoice No</th><th>Amount</th>
                    <th>Total Products</th><th>Order Date</th><th>Status</th>
                  </tr>
                </thead><tbody>";
        $i = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $invoice  = htmlspecialchars((string)$row['invoice_number'], ENT_QUOTES, 'UTF-8');
            $amount   = htmlspecialchars((string)$row['amount_due'],      ENT_QUOTES, 'UTF-8');
            $total    = (int)$row['total_products'];
            $date     = htmlspecialchars((string)$row['order_date'],      ENT_QUOTES, 'UTF-8');
            $status   = ucfirst($row['order_status']);
            $color    = ($row['order_status'] === 'pending') ? 'text-danger' : 'text-success';
            $safe_status = htmlspecialchars($status, ENT_QUOTES, 'UTF-8');
            echo "<tr>
                    <td>{$i}</td>
                    <td>{$invoice}</td>
                    <td>₹{$amount}</td>
                    <td>{$total}</td>
                    <td>{$date}</td>
                    <td class='{$color}'>{$safe_status}</td>
                  </tr>";
            $i++;
        }
        echo "</tbody></table>";
    }
    mysqli_stmt_close($stmt);
}

// ─── 3. Edit Account Form + Handler ──────────────────────────────────────
function show_edit_account_form(): void {
    global $con;

    if (!isset($_SESSION['username'])) {
        echo "<p class='text-danger'>Please log in to edit your account.</p>";
        return;
    }

    $user_id = get_user_id();
    if (!$user_id) {
        echo "<p class='text-danger'>User not found.</p>";
        return;
    }

    // Fetch current data
    $stmt = mysqli_prepare($con, "SELECT * FROM user_table WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row    = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    $username = $row['user_name'];
    $email    = $row['user_email'];
    $address  = $row['user_address'];
    $mobile   = $row['user_mobile'];

    // Handle POST submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_account'])) {
        if (!verify_csrf_token()) {
            popup_redirect('Invalid request. Please try again.', 'profile.php?edit_account', 2000, 'error', 'top-center');
            return;
        }

        $new_name    = trim($_POST['user_name']    ?? '');
        $new_email   = trim($_POST['user_email']   ?? '');
        $new_address = trim($_POST['user_address'] ?? '');
        $new_mobile  = trim($_POST['user_mobile']  ?? '');
        $new_password = $_POST['user_password']    ?? '';

        if (empty($new_name) || empty($new_email) || empty($new_address) || empty($new_mobile)) {
            echo "<div class='alert alert-danger'>All fields except password are required.</div>";
            // fall through to re-render form below
        } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            echo "<div class='alert alert-danger'>Invalid email address.</div>";
        } else {
            if (!empty($new_password)) {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $upd = mysqli_prepare($con,
                    "UPDATE user_table SET user_name=?, user_email=?, user_address=?, user_mobile=?, user_password=? WHERE user_id=?"
                );
                mysqli_stmt_bind_param($upd, 'sssssi', $new_name, $new_email, $new_address, $new_mobile, $hashed, $user_id);
            } else {
                $upd = mysqli_prepare($con,
                    "UPDATE user_table SET user_name=?, user_email=?, user_address=?, user_mobile=? WHERE user_id=?"
                );
                mysqli_stmt_bind_param($upd, 'ssssi', $new_name, $new_email, $new_address, $new_mobile, $user_id);
            }

            if (mysqli_stmt_execute($upd)) {
                $_SESSION['username'] = $new_name;
                mysqli_stmt_close($upd);
                popup_redirect('Account updated successfully!', 'profile.php?edit_account', 1000, 'success', 'top-center');
                return;
            } else {
                mysqli_stmt_close($upd);
                popup_redirect('Failed to update account. Please try again.', '', 2000, 'error', 'top-center');
                return;
            }
        }

        // Re-use new values in form on validation error
        $username = $new_name    ?? $username;
        $email    = $new_email   ?? $email;
        $address  = $new_address ?? $address;
        $mobile   = $new_mobile  ?? $mobile;
    }

    // Render form
    $safe_username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
    $safe_email    = htmlspecialchars($email,    ENT_QUOTES, 'UTF-8');
    $safe_address  = htmlspecialchars($address,  ENT_QUOTES, 'UTF-8');
    $safe_mobile   = htmlspecialchars($mobile,   ENT_QUOTES, 'UTF-8');

    echo "
    <h4 class='text-center mb-4'>Edit Account</h4>
    <form method='post' class='w-50 mx-auto'>
      " . csrf_field() . "
      <div class='mb-3'>
        <label>Username</label>
        <input type='text' name='user_name' class='form-control' value='{$safe_username}' required>
      </div>
      <div class='mb-3'>
        <label>Email</label>
        <input type='email' name='user_email' class='form-control' value='{$safe_email}' required>
      </div>
      <div class='mb-3'>
        <label>New Password <small class='text-muted'>(leave blank to keep unchanged)</small></label>
        <input type='password' name='user_password' class='form-control'>
      </div>
      <div class='mb-3'>
        <label>Address</label>
        <textarea name='user_address' class='form-control' rows='2' required>{$safe_address}</textarea>
      </div>
      <div class='mb-3'>
        <label>Mobile</label>
        <input type='text' name='user_mobile' class='form-control' value='{$safe_mobile}' required>
      </div>
      <button type='submit' name='update_account' class='btn btn-info'>Update</button>
    </form>";
}

// ─── csrf_field() helper for echo contexts ───────────────────────────────
function csrf_field(): string {
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

// ─── 4. Delete Account Form + Handler ────────────────────────────────────
function show_delete_account_form(): void {
    global $con;

    if (!isset($_SESSION['username'])) {
        echo "<p class='text-danger'>Please log in to delete your account.</p>";
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
        if (!verify_csrf_token()) {
            popup_redirect('Invalid request. Please try again.', 'profile.php?delete_account', 2000, 'error', 'top-center');
            return;
        }

        $user_id = get_user_id();
        if ($user_id && delete_user_account($user_id)) {
            session_unset();
            session_destroy();
            popup_redirect('Account deleted successfully.', '../index.php', 2000, 'info', 'top-center');
            return;
        } else {
            popup_redirect('Failed to delete account. Please try again.', 'profile.php?delete_account', 2000, 'error', 'top-center');
            return;
        }
    }

    echo "
    <h4 class='text-center mb-4 text-danger'>Delete Account</h4>
    <p class='text-center text-muted'>This action is permanent and cannot be undone.</p>
    <form method='post' class='w-50 mx-auto text-center'>
      " . csrf_field() . "
      <button type='submit' name='confirm_delete' class='btn btn-danger'
              onclick=\"return confirm('Are you sure? This cannot be undone.')\">
        Yes, Delete My Account
      </button>
      <a href='profile.php' class='btn btn-secondary ms-2'>Cancel</a>
    </form>";
}

// ─── 5. Delete user account by ID ────────────────────────────────────────
function delete_user_account(int $user_id): bool {
    global $con;
    $stmt = mysqli_prepare($con, "DELETE FROM user_table WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}
