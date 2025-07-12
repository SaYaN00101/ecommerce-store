<?php
include('../includes/connect.php');
?>

<h3 class="text-center mb-4">Registered Users</h3>

<div class="container">
    <table class="table table-bordered table-hover table-striped text-center">
        <thead class="table-info">
            <tr>
                <th>#</th>
                <th>Username</th>
                <th>Email</th>
                <th>Address</th>
                <th>Mobile</th>
                <th>Registered On</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $get_users = "SELECT * FROM user_table ORDER BY user_id DESC";
            $result_users = mysqli_query($con, $get_users);
            $number = 0;

            while ($row = mysqli_fetch_assoc($result_users)) {
                $number++;
                $username = $row['user_name'];
                $email = $row['user_email'];
                $address = $row['user_address'];
                $mobile = $row['user_mobile'];
                $date = date('d M Y', strtotime($row['register_date']));

                echo "<tr>
                        <td>$number</td>
                        <td>$username</td>
                        <td>$email</td>
                        <td>$address</td>
                        <td>$mobile</td>
                        <td>$date</td>
                    </tr>";
            }

            if ($number == 0) {
                echo "<tr><td colspan='6' class='text-muted'>No users registered yet</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
