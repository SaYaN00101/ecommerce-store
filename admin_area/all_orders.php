<?php
include('../includes/connect.php');
?>

<h3 class="text-center mb-4">All Orders</h3>

<div class="container">
    <table class="table table-bordered table-hover table-striped text-center">
        <thead class="table-info">
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Invoice</th>
                <th>Amount</th>
                <th>Total Products</th>
                <th>Payment Method</th>
                <th>Status</th>
                <th>Order Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $get_orders = "SELECT * FROM user_orders ORDER BY order_id DESC";
            $result_orders = mysqli_query($con, $get_orders);
            $number = 0;

            while ($row = mysqli_fetch_assoc($result_orders)) {
                $number++;
                $order_id = $row['order_id'];
                $user_id = $row['user_id'];
                $invoice = $row['invoice_number'];
                $amount = $row['amount_due'];
                $products = $row['total_products'];
                $method = $row['payment_method'];
                $status = ucfirst($row['order_status']);
                $date = date('d M Y', strtotime($row['order_date']));

                // Get user name
                $get_user = "SELECT user_name FROM user_table WHERE user_id = $user_id";
                $user_result = mysqli_query($con, $get_user);
                $user_row = mysqli_fetch_assoc($user_result);
                $username = $user_row['user_name'] ?? 'Unknown';

                echo "<tr>
                        <td>$number</td>
                        <td>$username</td>
                        <td>$invoice</td>
                        <td>₹$amount</td>
                        <td>$products</td>
                        <td>$method</td>
                        <td class='" . ($status == 'Pending' ? 'text-warning' : 'text-success') . "'><strong>$status</strong></td>
                        <td>$date</td>
                    </tr>";
            }

            if ($number == 0) {
                echo "<tr><td colspan='8' class='text-center text-muted'>No orders found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
