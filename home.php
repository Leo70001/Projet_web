<?php

@include 'config.php';

session_start();

// $user_id = $_SESSION['user_id'];

// if (!isset($user_id)) {
//     header('location: login.php');
// };

if (isset($_POST['add_to_wishlist'])) {

    $pid = $_POST['pid'];
    $pid = filter_var($pid, FILTER_SANITIZE_STRING);
    $p_name = $_POST['p_name'];
    $p_name = filter_var($p_name, FILTER_SANITIZE_STRING);
    $p_price = $_POST['p_price'];
    $p_price = filter_var($p_price, FILTER_SANITIZE_STRING);
    $p_image = $_POST['p_image'];
    $p_image = filter_var($p_image, FILTER_SANITIZE_STRING);

    $check_wishlist_numbers = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
    $check_wishlist_numbers->bind_param("si", $p_name, $user_id);
    $check_wishlist_numbers->execute();

    $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
    $check_cart_numbers->bind_param("si", $p_name, $user_id);
    $check_cart_numbers->execute();

    if ($check_wishlist_numbers->get_result()->num_rows > 0) {
        $message[] = 'already added to wishlist!';
    } elseif ($check_cart_numbers->get_result()->num_rows > 0) {
        $message[] = 'already added to cart!';
    } else {
        $insert_wishlist = $conn->prepare("INSERT INTO `wishlist`(user_id, pid, name, price, image) VALUES(?,?,?,?,?)");
        $insert_wishlist->bind_param("issds", $user_id, $pid, $p_name, $p_price, $p_image);
        $insert_wishlist->execute();
        $message[] = 'added to wishlist!';
    }
}

if (isset($_POST['add_to_cart'])) {

    $pid = $_POST['pid'];
    $pid = filter_var($pid, FILTER_SANITIZE_STRING);
    $p_name = $_POST['p_name'];
    $p_name = filter_var($p_name, FILTER_SANITIZE_STRING);
    $p_price = $_POST['p_price'];
    $p_price = filter_var($p_price, FILTER_SANITIZE_STRING);
    $p_image = $_POST['p_image'];
    $p_image = filter_var($p_image, FILTER_SANITIZE_STRING);
    $p_qty = $_POST['p_qty'];
    $p_qty = filter_var($p_qty, FILTER_SANITIZE_STRING);

    $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
    $check_cart_numbers->bind_param("si", $p_name, $user_id);
    $check_cart_numbers->execute();

    if ($check_cart_numbers->get_result()->num_rows > 0) {
        $message[] = 'already added to cart!';
    } else {

        $check_wishlist_numbers = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
        $check_wishlist_numbers->bind_param("si", $p_name, $user_id);
        $check_wishlist_numbers->execute();

        if ($check_wishlist_numbers->get_result()->num_rows > 0) {
            $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE name = ? AND user_id = ?");
            $delete_wishlist->bind_param("si", $p_name, $user_id);
            $delete_wishlist->execute();
        }

        $insert_cart = $conn->prepare("INSERT INTO `cart`(user_id, pid, name, price, quantity, image) VALUES(?,?,?,?,?,?)");
        $insert_cart->bind_param("issdis", $user_id, $pid, $p_name, $p_price, $p_qty, $p_image);
        $insert_cart->execute();
        $message[] = 'added to cart!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>home page</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php // include 'header.php'; ?>

<div class="home-bg">

   <section class="home">

      <div class="content">
      <span>Ne paniquez pas, optez pour l'organique</span>

<h3>Atteignez une meilleure santé avec des aliments bios</h3>
<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Iusto natus culpa officia quasi, accusantium explicabo?</p>
<a href="about.php" class="btn">à propos de nous</a>
      </div>

   </section>

</div>

<section class="home-category">
<h1 class="title">Achetez par catégorie</h1>

<div class="box-container">

   <div class="box">
      <img src="images/cat-1.png" alt="">
      <h3>fruits</h3>
      <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Exercitationem, quaerat.</p>
      <a href="category.php?category=fruits" class="btn">fruits</a>
   </div>

   <div class="box">
      <img src="images/cat-2.png" alt="">
      <h3>viande</h3>
      <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Exercitationem, quaerat.</p>
      <a href="category.php?category=meat" class="btn">viande</a>
   </div>

   <div class="box">
      <img src="images/cat-3.png" alt="">
      <h3>légumes</h3>
      <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Exercitationem, quaerat.</p>
      <a href="category.php?category=vegitables" class="btn">légumes</a>
   </div>

   <div class="box">
      <img src="images/cat-4.png" alt="">
      <h3>poisson</h3>
      <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Exercitationem, quaerat.</p>
      <a href="category.php?category=fish" class="btn">poisson</a>
   </div>

</div>

</section>

<section class="products">

   <h1 class="title">Derniers produits</h1>

   <div class="box-container">

   <?php
      $select_products = $conn->prepare("SELECT p.*, t.image_produit FROM produit p INNER JOIN typeproduit t ON t.id_produit = p.id_produit LIMIT 6;");
      $select_products->execute();
      $result = $select_products->get_result();
      if($result->num_rows > 0){
         while($fetch_products = $result->fetch_assoc()){ 
   ?>
   <form action="" class="box" method="POST">
      <div class="price">$<span><?= $fetch_products['prix_produit']; ?></span>/-</div>
      <a href="view_page.php?pid=<?= $fetch_products['id_produit']; ?>" class="fas fa-eye"></a>
      <img src="uploaded_img/<?= $fetch_products['image_produit']; ?>" alt="">
      <div class="name"><?= $fetch_products['nom_produit']; ?></div>
      <input type="hidden" name="pid" value="<?= $fetch_products['id_produit']; ?>">
      <input type="hidden" name="p_name" value="<?= $fetch_products['nom_produit']; ?>">
      <input type="hidden" name="p_price" value="<?= $fetch_products['prix_produit']; ?>">
      <input type="hidden" name="p_image" value="<?= $fetch_products['image_produit']; ?>">
      <input type="number" min="1" value="1" name="p_qty" class="qty">
      <input type="submit" value="add to wishlist" class="option-btn" name="add_to_wishlist">
      <input type="submit" value="add to cart" class="btn" name="add_to_cart">
   </form>
   <?php
      }
   }else{
      echo '<p class="empty">Aucun produits actuellement!</p>';
   }
   ?>

   </div>

</section>







<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>