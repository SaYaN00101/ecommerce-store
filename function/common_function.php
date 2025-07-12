<?php
//including connect file
include(__DIR__ . '/../includes/connect.php');

// Message variable to store success or error message
$message = '';
// getting products

function getproducts()
{
  global $con;
  //condition to check isset or not --> 
  if (!isset($_GET['category'])) {
    if (!isset($_GET['insert_brand'])) {

      $select_query = "SELECT * FROM `products` ORDER by rand() LIMIT 0,9";
      $result_query = mysqli_query($con, $select_query);
      while ($row = mysqli_fetch_assoc($result_query)) {
        $product_id = $row['product_id'];
        $product_title = $row['product_title'];
        $product_description = $row['product_description'];
        $product_image1 = $row['product_image1'];
        $product_price = $row['product_price'];
        $category_id = $row['category_id'];
        $brand_id = $row['brand_id'];

        echo "
    <div class='col-md-4 mb-2'>
      <div class='card'>
        <img src='./admin_area/product_images/$product_image1' class='card-img-top' alt='Product Image'>
        <div class='card-body'>
          <h5 class='card-title'>$product_title</h5>
          <p class='card-text'>$product_description</p>
          <p class='card-text'>Price : $product_price/-</p>
          <a href='index.php?add_to_cart=$product_id' class='btn btn-info'>Add to Cart</a>
          <a href='product_details.php?product_id=$product_id' class='btn btn-light'>View More</a>
        </div>
      </div>
    </div>
  ";
      }
    }
  }
}


//getting all products
function get_all_products()
{
  global $con;
  //condition to check isset or not --> 
  if (!isset($_GET['category'])) {
    if (!isset($_GET['insert_brand'])) {

      $select_query = "SELECT * FROM `products` ORDER by rand()";
      $result_query = mysqli_query($con, $select_query);
      while ($row = mysqli_fetch_assoc($result_query)) {
        $product_id = $row['product_id'];
        $product_title = $row['product_title'];
        $product_description = $row['product_description'];
        $product_image1 = $row['product_image1'];
        $product_price = $row['product_price'];
        $category_id = $row['category_id'];
        $brand_id = $row['brand_id'];

        echo "
    <div class='col-md-4 mb-2'>
      <div class='card'>
        <img src='./admin_area/product_images/$product_image1' class='card-img-top' alt='Product Image'>
        <div class='card-body'>
          <h5 class='card-title'>$product_title</h5>
          <p class='card-text'>$product_description</p>
          <p class='card-text'>Price : $product_price/-</p>
          <a href='index.php?add_to_cart=$product_id' class='btn btn-info'>Add to Cart</a>
          <a href='product_details.php?product_id=$product_id' class='btn btn-light'>View More</a>
        </div>
      </div>
    </div>
  ";
      }
    }
  }
}


//getting unique categories 
function get_unique_category()
{
  global $con;
  //condition to check isset or not --> 
  if (isset($_GET['category'])) {
    $category_id = $_GET['category'];
    $select_query = "SELECT * FROM `products` where category_id=$category_id";
    $result_query = mysqli_query($con, $select_query);
    $num_of_rows = mysqli_num_rows($result_query);
    if ($num_of_rows == 0) {
      echo "<h2 class='text-secondary text-center mt-5'style='padding-left: 240px;'><i class='fa-regular fa-face-frown'></i><br>No Stock for this product</h2>";
    }
    while ($row = mysqli_fetch_assoc($result_query)) {
      $product_id = $row['product_id'];
      $product_title = $row['product_title'];
      $product_description = $row['product_description'];
      $product_image1 = $row['product_image1'];
      $product_price = $row['product_price'];
      $category_id = $row['category_id'];
      $brand_id = $row['brand_id'];

      echo "
  <div class='col-md-4 mb-2'>
    <div class='card'>
      <img src='./admin_area/product_images/$product_image1' class='card-img-top' alt='Product Image'>
      <div class='card-body'>
        <h5 class='card-title'>$product_title</h5>
        <p class='card-text'>$product_description</p>
        <p class='card-text'>Price : $product_price/-</p>
        <a href='index.php?add_to_cart=$product_id' class='btn btn-info'>Add to Cart</a>
        <a href='product_details.php?product_id=$product_id' class='btn btn-light'>View More</a>
      </div>
    </div>
  </div>
";
    }
  }
}

//getting unique Brand 
function get_unique_brand()
{
  global $con;
  //condition to check isset or not --> 
  if (isset($_GET['insert_brand'])) {
    $brand_id = $_GET['insert_brand'];
    $select_query = "SELECT * FROM `products` where brand_id=$brand_id";
    $result_query = mysqli_query($con, $select_query);
    $num_of_rows = mysqli_num_rows($result_query);
    if ($num_of_rows == 0) {
      echo "<h2 class='text-secondary text-center mt-5'style='padding-left: 240px;'><i class='fa-regular fa-face-frown'></i><br>No Stock for this brand</h2>";
    }
    while ($row = mysqli_fetch_assoc($result_query)) {
      $product_id = $row['product_id'];
      $product_title = $row['product_title'];
      $product_description = $row['product_description'];
      $product_image1 = $row['product_image1'];
      $product_price = $row['product_price'];
      $category_id = $row['category_id'];
      $brand_id = $row['brand_id'];

      echo "
  <div class='col-md-4 mb-2'>
    <div class='card'>
      <img src='./admin_area/product_images/$product_image1' class='card-img-top' alt='Product Image'>
      <div class='card-body'>
        <h5 class='card-title'>$product_title</h5>
        <p class='card-text'>$product_description</p>
        <p class='card-text'>Price : $product_price/-</p>
        <a href='index.php?add_to_cart=$product_id' class='btn btn-info'>Add to Cart</a>
        <a href='product_details.php?product_id=$product_id' class='btn btn-light'>View More</a>
      </div>
    </div>
  </div>
";
    }
  }
}



//displaing brands in sidenav

function getbrands()
{
  global $con;
  $select_brands = "SELECT * FROM `brands`";
  $result_brands = mysqli_query($con, $select_brands);
  while ($row_data = mysqli_fetch_assoc($result_brands)) {
    $brand_id = $row_data['brand_id'];
    $brand_title = $row_data['brand_title'];
    echo '
        <li class="nav-item">
          <a href="index.php?insert_brand=' . $brand_id . '" class="nav-link text-light">' . $brand_title . '</a>
        </li>';
  }
}



//displaing category in sidenav
function getcategory()
{
  global $con;
  $select_categories = "SELECT * FROM `categories`";
  $result_categories = mysqli_query($con, $select_categories);
  while ($row_data = mysqli_fetch_assoc($result_categories)) {
    $category_id = $row_data['category_id'];
    $category_title = $row_data['category_title'];
    echo '
        <li class="nav-item">
          <a href="index.php?category=' . $category_id . '" class="nav-link text-light">' . $category_title . '</a>
        </li>';
  }
}

//serching products 
function search_product()
{
  global $con;
  if (isset($_GET['search_data_product'])) {
    $search_data_value = $_GET['search_data'];
    $search_query = "SELECT * FROM `products` WHERE product_keywords LIKE '%$search_data_value%'";
    $result_query = mysqli_query($con, $search_query);
    $num_of_rows = mysqli_num_rows($result_query);

    if ($num_of_rows == 0) {
      echo "<h2 class='text-secondary text-center mt-5'style='padding-left: 240px;'><i class='fa-regular fa-face-frown'></i><br>No products found for '$search_data_value'!</h2>";
    } else {
      while ($row = mysqli_fetch_assoc($result_query)) {
        $product_id = $row['product_id'];
        $product_title = $row['product_title'];
        $product_description = $row['product_description'];
        $product_image1 = $row['product_image1'];
        $product_price = $row['product_price'];
        $category_id = $row['category_id'];
        $brand_id = $row['brand_id'];

        echo "
  <div class='col-md-4 mb-2'>
    <div class='card'>
      <img src='./admin_area/product_images/$product_image1' class='card-img-top' alt='Product Image'>
      <div class='card-body'>
        <h5 class='card-title'>$product_title</h5>
        <p class='card-text'>$product_description</p>
        <p class='card-text'>Price : $product_price/-</p>
        <a href='index.php?add_to_cart=$product_id' class='btn btn-info'>Add to Cart</a>
        <a href='product_details.php?product_id=$product_id' class='btn btn-light'>View More</a>
      </div>
    </div>
  </div>
";
      }
    }
  }
}


//view details funcation
function view_details()
{
  global $con;

  // Condition to check if 'product_id' is set in the URL
  if (isset($_GET['product_id'])) {
    if (!isset($_GET['category'])) {
      if (!isset($_GET['insert_brand'])) {

        $product_id = $_GET['product_id'];
        $select_query = "SELECT * FROM `products` WHERE product_id = $product_id";
        $result_query = mysqli_query($con, $select_query);

        while ($row = mysqli_fetch_assoc($result_query)) {
          $product_id = $row['product_id'];
          $product_title = $row['product_title'];
          $product_description = $row['product_description'];
          $product_image1 = $row['product_image1'];
          $product_image2 = $row['product_image2'];
          $product_image3 = $row['product_image3'];
          $product_price = $row['product_price'];
          $category_id = $row['category_id'];
          $brand_id = $row['brand_id'];

          echo "
            <div class='col-md-4 mb-2'>
              <div class='card'>
                <img src='./admin_area/product_images/$product_image1' class='card-img-top' alt='Product Image'>
                <div class='card-body'>
                  <h5 class='card-title'>$product_title</h5>
                  <p class='card-text'>$product_description</p>
                  <p class='card-text'>Price : $product_price/-</p>
                  <a href='index.php?add_to_cart=$product_id' class='btn btn-info'>Add to Cart</a>
                  <a href='product_details.php?product_id=$product_id' class='btn btn-light'>View More</a>
                </div>
              </div>
            </div>

            <div class='col-md-8'>
              <div class='row'>
                <div class='col-md-12'>
                  <h4 class='text-center text-info md-5'>Releted Products</h4>
                </div>
                <div class='col-md-6'>
                  <img src='admin_area/product_images/$product_image2' class='card-img-top' alt='Product Image'>
                </div>
                <div class='col-md-6'>
                  <img src='admin_area/product_images/$product_image3' class='card-img-top' alt='Product Image'>
                </div>
              </div>
            </div>
          ";
        }
      }
    }
  }
}
// getting user ip address funcation
function getUserIP()
{
  // Check for shared internet/ISP IP
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
  }
  // Check for IPs passing through proxies
  elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  }
  // Check for the remote address
  else {
    $ip = $_SERVER['REMOTE_ADDR'];
  }
  return $ip;
}

// Usage
// $user_ip = getUserIP();
// echo 'User IP Address: ' . $user_ip;

//pop up massage funcation 
function popup_redirect($message, $redirect_url = '', $delay = 3000, $type = 'success', $position = 'top-center') {
  // Define styles per type
  $styles = [
    'success' => ['bg' => '#d1e7dd', 'border' => '#badbcc', 'color' => '#0f5132'],
    'error'   => ['bg' => '#f8d7da', 'border' => '#f5c2c7', 'color' => '#842029'],
    'info'    => ['bg' => '#cff4fc', 'border' => '#b6effb', 'color' => '#055160']
  ];
  $style = $styles[$type] ?? $styles['success'];

  // Define positions
  $positions = [
    'top-center'    => 'top: 20px; left: 50%; transform: translateX(-50%);',
    'bottom-right'  => 'bottom: 20px; right: 20px;'
  ];
  $position_style = $positions[$position] ?? $positions['top-center'];

  echo "
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const popup = document.createElement('div');
        popup.innerHTML = `
          <div style=\"
            position: fixed;
            $position_style
            background-color: {$style['bg']};
            color: {$style['color']};
            border: 1px solid {$style['border']};
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 9999;
            font-family: sans-serif;
            min-width: 250px;
            text-align: center;
          \">
            <span style='cursor:pointer;font-weight:bold;float:right;font-size:18px;margin-left:10px;' onclick='this.parentElement.remove()'>&times;</span>
            <span style='font-size: 16px;'>".addslashes($message)."</span>
          </div>
        `;
        document.body.appendChild(popup);
        ". ($redirect_url ? "setTimeout(() => { window.location.href = '$redirect_url'; }, $delay);" : "") . "
      });
    </script>
  ";
}



//cart funcation
function cart()
{
  global $con;

  if (isset($_GET['add_to_cart'])) {

    $get_product_id = $_GET['add_to_cart'];
    $user_ip = getUserIP();

    // Check if product is already in cart
    $select_query = "SELECT * FROM `card_details` WHERE ip_address = '$user_ip' AND product_id = $get_product_id";
    $result_query = mysqli_query($con, $select_query);
    $num_of_rows = mysqli_num_rows($result_query);

    if ($num_of_rows > 0) {
      popup_redirect('Item already in cart!', '', 3000, 'error', 'top-center');
    } else {
      // Insert if not already in cart
      $insert_query = "INSERT INTO card_details (product_id, ip_address, quantity)
                       VALUES ($get_product_id, '$user_ip', 1)";
      mysqli_query($con, $insert_query);
      popup_redirect('Product added to cart!', 'index.php', 3000, 'success', 'top-center');

    }
  }
}

//funcation to get cart item number
function cart_item()
{
  global $con;

  if (isset($_GET['add_to_cart'])) {
    $user_ip = getUserIP();

    // Check if product is already in cart
    $select_query = "SELECT * FROM `card_details` WHERE ip_address = '$user_ip'";
    $result_query = mysqli_query($con, $select_query);
    $count_car_items = mysqli_num_rows($result_query);
  } else {

    $user_ip = getUserIP();

    // Check if product is already in cart
    $select_query = "SELECT * FROM `card_details` WHERE ip_address = '$user_ip'";
    $result_query = mysqli_query($con, $select_query);
    $count_car_items = mysqli_num_rows($result_query);
  }
  echo $count_car_items;
}

//total price function 
function total_cart_price()
{
  global $con;
  $user_ip = getUserIP();
  $total = 0;
  $cart_query = "SELECT * FROM `card_details` WHERE ip_address='$user_ip'";
  $result = mysqli_query($con, $cart_query);
  while ($row = mysqli_fetch_array($result)) {
    $product_id = $row['product_id'];
    $select_products = "SELECT * FROM `products` WHERE product_id ='$product_id'";
    $result_products = mysqli_query($con, $select_products);
    while ($row_product_price = mysqli_fetch_array($result_products)) {
      $product_price = array($row_product_price['product_price']);
      $product_values = array_sum($product_price);
      $total += $product_values;
    }
  }
  echo $total;
}
