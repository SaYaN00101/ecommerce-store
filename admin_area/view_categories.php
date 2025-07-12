<?php
include('../includes/connect.php');
?>

<div class="container mt-4">
    <h3 class="text-center mb-4 text-primary">All Categories</h3>

    <table class="table table-bordered table-striped text-center align-middle">
        <thead class="table-dark">
            <tr>
                <th>S.No</th>
                <th>Category Title</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $get_categories = "SELECT * FROM `categories`";
            $result = mysqli_query($con, $get_categories);
            $number = 1;

            while ($row = mysqli_fetch_assoc($result)) {
                $category_id = $row['category_id'];
                $category_title = $row['category_title'];
                echo "
                <tr>
                    <td>$number</td>
                    <td>$category_title</td>
                    <td>
                        <a href='index.php?edit_category=$category_id' class='btn btn-primary btn-sm'>
                            <i class='fas fa-edit'></i> Edit
                        </a>
                        <a href='index.php?delete_category=$category_id' 
                           class='btn btn-danger btn-sm'
                           onclick='return confirm(\"Are you sure you want to delete this category?\");'>
                            <i class='fas fa-trash'></i> Delete
                        </a>
                    </td>
                </tr>
                ";
                $number++;
            }

            if ($number === 1) {
                echo "<tr><td colspan='3'>No categories found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
