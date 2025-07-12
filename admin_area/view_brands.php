<?php
include('../includes/connect.php');

$get_brands = "SELECT * FROM brands";
$result = mysqli_query($con, $get_brands);
$number = 0;
?>

<h3 class="text-center mb-4">All Brands</h3>

<div class="table-responsive">
    <table class="table table-bordered text-center">
        <thead class="table-info">
            <tr>
                <th>Sr No</th>
                <th>Brand Title</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                $brand_id = $row['brand_id'];
                $brand_title = $row['brand_title'];
                $number++;
                echo "<tr>
                        <td>$number</td>
                        <td>$brand_title</td>
                        <td>
                            <a href='index.php?edit_brand=$brand_id' class='btn btn-sm btn-warning'>
                                <i class='fas fa-edit'></i> Edit
                            </a>
                        </td>
                        <td>
                            <a href='index.php?delete_brand=$brand_id' class='btn btn-sm btn-danger' onclick=\"return confirm('Are you sure you want to delete this brand?');\">
                                <i class='fas fa-trash'></i> Delete
                            </a>
                        </td>
                      </tr>";
            }

            if ($number == 0) {
                echo "<tr><td colspan='4' class='text-danger fw-bold'>No brands found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
