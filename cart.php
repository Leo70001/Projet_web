<?php
include 'config.php';

if (isset($_POST['add_to_cart'])) {
    $pid = $_POST['pid'];
    $quantity = $_POST['p_qty'];
    $p_price = $_POST['p_price'];
    $p_name = $_POST['p_name'];
    $p_image = $_POST['p_image'];

    $product_data = array(
        'product_id' => $pid,
        'product_name' => $p_name,
        'product_price' => $p_price,
        'product_quantity' => $quantity,
        'product_image' => $p_image
    );

    function addProductToCart($product_data)
    {
        if (isset($_COOKIE['cart'])) {
            $cart = json_decode($_COOKIE['cart'], true);
        } else {
            $cart = array();
        }
        $cart[] = $product_data;
        setcookie('cart', json_encode($cart), time() + (86400 * 30), "/"); // 86400 = 1 day
    }

    // Add the product to the cart
    addProductToCart($product_data);

    header('location: home.php');
} else {
    if (isset($_COOKIE['cart'])) {
        $cart = json_decode($_COOKIE['cart'], true);
    }
}

function deleteProductFromCart($product_id)
{
    if (isset($_COOKIE['cart'])) {
        $cart = json_decode($_COOKIE['cart'], true);
        foreach ($cart as $key => $product_data) {
            if ($product_data['product_id'] == $product_id) {
                unset($cart[$key]);
                break;
            }
        }
        setcookie('cart', json_encode($cart), time() + (86400 * 30), "/");
    }
}

if (isset($_POST['delete_product'])) {
    $product_id_to_delete = $_POST['product_id'];
    deleteProductFromCart($product_id_to_delete);
}

function clearCart()
{
    // Check if the cart cookie exists and unset it
    if (isset($_COOKIE['cart'])) {
        unset($_COOKIE['cart']);
        setcookie('cart', '', time() - 3600, '/'); // Set the cookie to expire in the past
    }
}

// Check if the clear cart button is clicked
if (isset($_POST['clear_cart'])) {
    clearCart(); // Clear the entire cart
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>shopping cart</title>
    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php //include 'header.php'; 
    ?>
    <section class="shopping-cart">
        <h1 class="title">products added</h1>
        <div class="box-container">
            <?php
            $grand_total = 0;
            if (isset($cart) and !empty($cart)) {
                var_dump($cart);
                foreach ($cart as $product_data) {
            ?>
                    <form action="" method="POST" class="box">
                        <a href="view_page.php?pid=<?= $product_data['product_id']; ?>" class="fas fa-eye"></a>
                        <img src="uploaded_img/<?= $product_data['product_image']; ?>" alt="">
                        <div class="name"><?= $product_data['product_name']; ?></div>
                        <div class="price">$<?= $product_data['product_price']; ?>/-</div>
                        <input type="hidden" name="cart_id" value="<?= $product_data['product_id']; ?>">
                        <div class="flex-btn">
                            <input type="number" min="1" value="<?= $product_data['product_quantity']; ?>" class="qty" name="p_qty">
                            <input type='hidden' name='product_id' value="<?= $product_data['product_id'] ?>">
                            <input type='submit' name='delete_product' class="fas fa-times" value="X" />
                            <input type="submit" value="update" name="update_qty" class="option-btn">
                        </div>
                        <div class="sub-total"> sub total : <span>$<?= $sub_total = ($product_data['product_price'] * $product_data['product_quantity']); ?>/-</span> </div>
                    </form>
            <?php
                    $grand_total += $sub_total;
                }
            } elseif (empty($cart)) {
                echo '<p class="empty">your cart is empty</p>';
            }
            ?>
        </div>
        <div class="cart-total">
            <p>grand total : <span>$<?= $grand_total; ?>/-</span></p>
            <a href="shop.php" class="option-btn">continue shopping</a>
            <form action="" method="POST"> <input type='submit' name='clear_cart' value='Clear Cart' class="delete-btn <?= ($grand_total > 1) ? '' : 'disabled'; ?>">
                <a href="checkout.php" class="btn <?= ($grand_total > 1) ? '' : 'disabled'; ?>">proceed to checkout</a>
            </form>
        </div>
    </section>
    <?php //include 'footer.php'; 
    ?>
    <script src="js/script.js"></script>
</body>

</html>
<?php
?>