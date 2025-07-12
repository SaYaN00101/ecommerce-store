<?php
include('../includes/connect.php');
?>

<h2 class="text-center text-success mb-4">All Products</h2>
<div class="table-responsive">
    <table class="table table-bordered table-striped text-center align-middle">
        <thead class="table-info">
            <tr>
                <th>Sr No</th>
                <th>Product Title</th>
                <th>Image</th>
                <th>Price</th>
                <th>Keywords</th>
                <th>Date</th>
                <th>Status</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $get_products = "SELECT p.*, c.category_title, b.brand_title 
                             FROM products p
                             JOIN categories c ON p.category_id = c.category_id
                             JOIN brands b ON p.brand_id = b.brand_id
                             ORDER BY p.date DESC";

            $result = mysqli_query($con, $get_products);
            $number = 1;

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                    <td>{$number}</td>
                    <td>{$row['product_title']}</td>
                    <td><img src='product_images/{$row['product_image1']}' class='img-thumbnail' style='width: 80px; height: auto;'></td>
                    <td>₹{$row['product_price']}</td>
                    <td>{$row['product_keywords']}</td>
                    <td>{$row['date']}</td>
                    <td><span class='badge bg-".($row['status'] == 'true' ? 'success' : 'danger')."'>".($row['status'] == 'true' ? 'Active' : 'Inactive')."</span></td>
                    <td>{$row['category_title']}</td>
                    <td>{$row['brand_title']}</td>
                    <td><a href='index.php?edit_product={$row['product_id']}' class='btn btn-sm btn-warning'><i class='fas fa-edit'></i></a></td>
                    <td><a href='index.php?delete_product={$row['product_id']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Are you sure you want to delete this product?')\"><i class='fas fa-trash'></i></a></td>
                </tr>";
                $number++;
            }
            ?>
        </tbody>
    </table>
</div>
