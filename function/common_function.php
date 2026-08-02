<?php
// Including connect file
include_once(__DIR__ . '/../includes/connect.php');

// ─── Helper: render a product card ──────────────────────────────────────────
function render_product_card(array $row, string $base_path = ''): void {
    $product_id  = (int)$row['product_id'];
    $title       = htmlspecialchars($row['product_title'],       ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($row['product_description'], ENT_QUOTES, 'UTF-8');
    $image1      = htmlspecialchars($row['product_image1'],      ENT_QUOTES, 'UTF-8');
    $price       = htmlspecialchars((string)$row['product_price'], ENT_QUOTES, 'UTF-8');

    echo "
    <div class='col-md-4 mb-2'>
      <div class='card'>
        <img src='{$base_path}admin_area/product_images/{$image1}' class='card-img-top' alt='Product Image'>
        <div class='card-body'>
          <h5 class='card-title'>{$title}</h5>
          <p class='card-text'>{$description}</p>
          <p class='card-text'>Price : {$price}/-</p>
          <a href='{$base_path}index.php?add_to_cart={$product_id}' class='btn btn-info'>Add to Cart</a>
          <a href='{$base_path}product_details.php?product_id={$product_id}' class='btn btn-light'>View More</a>
        </div>
      </div>
    </div>
    ";
}

// ─── Get 9 random products (homepage) ───────────────────────────────────────
function getproducts(): void {
    global $con;
    if (!isset($_GET['category']) && !isset($_GET['insert_brand'])) {
        $result = mysqli_query($con, "SELECT * FROM `products` ORDER BY RAND() LIMIT 9");
        while ($row = mysqli_fetch_assoc($result)) {
            render_product_card($row, './');
        }
    }
}

// ─── Get all products ────────────────────────────────────────────────────────
function get_all_products(): void {
    global $con;
    if (!isset($_GET['category']) && !isset($_GET['insert_brand'])) {
        $result = mysqli_query($con, "SELECT * FROM `products` ORDER BY RAND()");
        while ($row = mysqli_fetch_assoc($result)) {
            render_product_card($row, './');
        }
    }
}

// ─── Filter by category ──────────────────────────────────────────────────────
function get_unique_category(): void {
    global $con;
    if (!isset($_GET['category'])) return;

    $category_id = (int)$_GET['category']; // cast to int — safe, no injection possible

    $stmt = mysqli_prepare($con, "SELECT * FROM `products` WHERE category_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $category_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 0) {
        echo "<h2 class='text-secondary text-center mt-5' style='padding-left:240px;'>"
           . "<i class='fa-regular fa-face-frown'></i><br>No Stock for this product</h2>";
    }
    while ($row = mysqli_fetch_assoc($result)) {
        render_product_card($row, './');
    }
    mysqli_stmt_close($stmt);
}

// ─── Filter by brand ─────────────────────────────────────────────────────────
function get_unique_brand(): void {
    global $con;
    if (!isset($_GET['insert_brand'])) return;

    $brand_id = (int)$_GET['insert_brand'];

    $stmt = mysqli_prepare($con, "SELECT * FROM `products` WHERE brand_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $brand_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 0) {
        echo "<h2 class='text-secondary text-center mt-5' style='padding-left:240px;'>"
           . "<i class='fa-regular fa-face-frown'></i><br>No Stock for this brand</h2>";
    }
    while ($row = mysqli_fetch_assoc($result)) {
        render_product_card($row, './');
    }
    mysqli_stmt_close($stmt);
}

// ─── Sidebar: brands ─────────────────────────────────────────────────────────
function getbrands(): void {
    global $con;
    $result = mysqli_query($con, "SELECT * FROM `brands`");
    while ($row = mysqli_fetch_assoc($result)) {
        $id    = (int)$row['brand_id'];
        $title = htmlspecialchars($row['brand_title'], ENT_QUOTES, 'UTF-8');
        echo "<li class='nav-item'>"
           . "<a href='index.php?insert_brand={$id}' class='nav-link text-light'>{$title}</a>"
           . "</li>";
    }
}

// ─── Sidebar: categories ─────────────────────────────────────────────────────
function getcategory(): void {
    global $con;
    $result = mysqli_query($con, "SELECT * FROM `categories`");
    while ($row = mysqli_fetch_assoc($result)) {
        $id    = (int)$row['category_id'];
        $title = htmlspecialchars($row['category_title'], ENT_QUOTES, 'UTF-8');
        echo "<li class='nav-item'>"
           . "<a href='index.php?category={$id}' class='nav-link text-light'>{$title}</a>"
           . "</li>";
    }
}

// ─── Search products ─────────────────────────────────────────────────────────
function search_product(): void {
    global $con;
    if (!isset($_GET['search_data_product'])) return;

    $search_value = trim($_GET['search_data'] ?? '');
    $like_value   = '%' . $search_value . '%';

    $stmt = mysqli_prepare($con, "SELECT * FROM `products` WHERE product_keywords LIKE ?");
    mysqli_stmt_bind_param($stmt, 's', $like_value);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 0) {
        $safe_search = htmlspecialchars($search_value, ENT_QUOTES, 'UTF-8');
        echo "<h2 class='text-secondary text-center mt-5' style='padding-left:240px;'>"
           . "<i class='fa-regular fa-face-frown'></i><br>No products found for '{$safe_search}'!</h2>";
    } else {
        while ($row = mysqli_fetch_assoc($result)) {
            render_product_card($row, './');
        }
    }
    mysqli_stmt_close($stmt);
}

// ─── Product detail page ─────────────────────────────────────────────────────
function view_details(): void {
    global $con;
    if (!isset($_GET['product_id']) || isset($_GET['category']) || isset($_GET['insert_brand'])) return;

    $product_id = (int)$_GET['product_id'];

    $stmt = mysqli_prepare($con, "SELECT * FROM `products` WHERE product_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $pid    = (int)$row['product_id'];
        $title  = htmlspecialchars($row['product_title'],       ENT_QUOTES, 'UTF-8');
        $desc   = htmlspecialchars($row['product_description'], ENT_QUOTES, 'UTF-8');
        $img1   = htmlspecialchars($row['product_image1'], ENT_QUOTES, 'UTF-8');
        $img2   = htmlspecialchars($row['product_image2'], ENT_QUOTES, 'UTF-8');
        $img3   = htmlspecialchars($row['product_image3'], ENT_QUOTES, 'UTF-8');
        $price  = htmlspecialchars((string)$row['product_price'], ENT_QUOTES, 'UTF-8');

        echo "
            <div class='col-md-4 mb-2'>
              <div class='card'>
                <img src='./admin_area/product_images/{$img1}' class='card-img-top' alt='Product Image'>
                <div class='card-body'>
                  <h5 class='card-title'>{$title}</h5>
                  <p class='card-text'>{$desc}</p>
                  <p class='card-text'>Price : {$price}/-</p>
                  <a href='index.php?add_to_cart={$pid}' class='btn btn-info'>Add to Cart</a>
                  <a href='product_details.php?product_id={$pid}' class='btn btn-light'>View More</a>
                </div>
              </div>
            </div>

            <div class='col-md-8'>
              <div class='row'>
                <div class='col-md-12'>
                  <h4 class='text-center text-info md-5'>Related Products</h4>
                </div>
                <div class='col-md-6'>
                  <img src='admin_area/product_images/{$img2}' class='card-img-top' alt='Product Image'>
                </div>
                <div class='col-md-6'>
                  <img src='admin_area/product_images/{$img3}' class='card-img-top' alt='Product Image'>
                </div>
              </div>
            </div>
        ";
    }
    mysqli_stmt_close($stmt);
}

// ─── Get user IP (REMOTE_ADDR only — not spoofable) ──────────────────────────
function getUserIP(): string {
    // Use only REMOTE_ADDR; proxy headers are trivially spoofable by clients
    return $_SERVER['REMOTE_ADDR'];
}

// ─── Popup redirect ──────────────────────────────────────────────────────────
function popup_redirect(
    string $message,
    string $redirect_url = '',
    int    $delay        = 3000,
    string $type         = 'success',
    string $position     = 'top-center'
): void {
    $styles = [
        'success' => ['bg' => '#d1e7dd', 'border' => '#badbcc', 'color' => '#0f5132'],
        'error'   => ['bg' => '#f8d7da', 'border' => '#f5c2c7', 'color' => '#842029'],
        'info'    => ['bg' => '#cff4fc', 'border' => '#b6effb', 'color' => '#055160'],
    ];
    $style = $styles[$type] ?? $styles['success'];

    $positions = [
        'top-center'   => 'top: 20px; left: 50%; transform: translateX(-50%);',
        'bottom-right' => 'bottom: 20px; right: 20px;',
    ];
    $position_style = $positions[$position] ?? $positions['top-center'];

    // Use json_encode for safe JS string encoding (prevents XSS)
    $js_message      = json_encode(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));
    $js_redirect_url = json_encode($redirect_url);

    $redirect_js = $redirect_url
        ? "setTimeout(() => { window.location.href = {$js_redirect_url}; }, {$delay});"
        : '';

    echo "
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const popup = document.createElement('div');
        popup.innerHTML = '<div style=\""
            . "position: fixed;"
            . "{$position_style}"
            . "background-color: {$style['bg']};"
            . "color: {$style['color']};"
            . "border: 1px solid {$style['border']};"
            . "padding: 15px 25px;"
            . "border-radius: 10px;"
            . "box-shadow: 0 5px 15px rgba(0,0,0,0.2);"
            . "z-index: 9999;"
            . "font-family: sans-serif;"
            . "min-width: 250px;"
            . "text-align: center;"
            . "\">"
            . "<span style=\\'cursor:pointer;font-weight:bold;float:right;font-size:18px;margin-left:10px;\\' "
            . "onclick=\\'this.parentElement.remove()\\'>&times;</span>"
            . "<span style=\\'font-size:16px;\\'>' + {$js_message} + '</span>"
            . "</div>';
        document.body.appendChild(popup);
        {$redirect_js}
      });
    </script>
    ";
}

// ─── Add to cart ─────────────────────────────────────────────────────────────
function cart(): void {
    global $con;
    if (!isset($_GET['add_to_cart'])) return;

    $product_id = (int)$_GET['add_to_cart'];
    $user_ip    = getUserIP();

    $stmt = mysqli_prepare($con, "SELECT product_id FROM `card_details` WHERE ip_address = ? AND product_id = ?");
    mysqli_stmt_bind_param($stmt, 'si', $user_ip, $product_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        popup_redirect('Item already in cart!', '', 3000, 'error', 'top-center');
    } else {
        mysqli_stmt_close($stmt);
        $insert = mysqli_prepare($con, "INSERT INTO card_details (product_id, ip_address, quantity) VALUES (?, ?, 1)");
        mysqli_stmt_bind_param($insert, 'is', $product_id, $user_ip);
        mysqli_stmt_execute($insert);
        mysqli_stmt_close($insert);
        popup_redirect('Product added to cart!', 'index.php', 3000, 'success', 'top-center');
        return;
    }
    mysqli_stmt_close($stmt);
}

// ─── Cart item count ─────────────────────────────────────────────────────────
function cart_item(): void {
    global $con;
    $user_ip = getUserIP();

    $stmt = mysqli_prepare($con, "SELECT product_id FROM `card_details` WHERE ip_address = ?");
    mysqli_stmt_bind_param($stmt, 's', $user_ip);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    echo (int)mysqli_stmt_num_rows($stmt);
    mysqli_stmt_close($stmt);
}

// ─── Cart total price ────────────────────────────────────────────────────────
function total_cart_price(): void {
    global $con;
    $user_ip = getUserIP();
    $total   = 0;

    $stmt = mysqli_prepare($con, "SELECT product_id, quantity FROM `card_details` WHERE ip_address = ?");
    mysqli_stmt_bind_param($stmt, 's', $user_ip);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $pid = (int)$row['product_id'];
        $qty = (int)$row['quantity'];

        $pstmt = mysqli_prepare($con, "SELECT product_price FROM `products` WHERE product_id = ?");
        mysqli_stmt_bind_param($pstmt, 'i', $pid);
        mysqli_stmt_execute($pstmt);
        $presult = mysqli_stmt_get_result($pstmt);
        if ($prow = mysqli_fetch_assoc($presult)) {
            $total += (float)$prow['product_price'] * $qty;
        }
        mysqli_stmt_close($pstmt);
    }
    mysqli_stmt_close($stmt);
    echo $total;
}
