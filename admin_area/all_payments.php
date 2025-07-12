<?php
include('../includes/connect.php');
?>

<h3 class="text-center mb-4">All Payments</h3>

<div class="container">
    <table class="table table-bordered table-hover table-striped text-center">
        <thead class="table-info">
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Invoice</th>
                <th>Amount</th>
                <th>Payment Mode</th>
                <th>Payment Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $get_payments = "SELECT * FROM payments ORDER BY payment_id DESC";
            $result_payments = mysqli_query($con, $get_payments);
            $number = 0;

            while ($row = mysqli_fetch_assoc($result_payments)) {
                $number++;
                $payment_id = $row['payment_id'];
                $order_id = $row['order_id'];
                $invoice = $row['invoice_number'];
                $amount = $row['amount'];
                $mode = $row['payment_mode'];
                $date = date('d M Y, h:i A', strtotime($row['payment_date']));

                // Get user associated with the order
                $get_user = "SELECT u.user_name 
                             FROM user_orders o 
                             JOIN user_table u ON o.user_id = u.user_id 
                             WHERE o.order_id = $order_id";
                $user_result = mysqli_query($con, $get_user);
                $user_row = mysqli_fetch_assoc($user_result);
                $username = $user_row['user_name'] ?? 'Unknown';

                echo "<tr>
                        <td>$number</td>
                        <td>$username</td>
                        <td>$invoice</td>
                        <td>₹$amount</td>
                        <td>$mode</td>
                        <td>$date</td>
                    </tr>";
            }

            if ($number == 0) {
                echo "<tr><td colspan='6' class='text-center text-muted'>No payment records found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
