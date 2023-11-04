<?php

if (isset($message)) {
    foreach ($message as $message) {
        echo '
        <div class="message">
            <span>' . $message . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>
        ';
    }
}

?>

<header class="header">

    <div class="flex">

        <a href="admin_page.php" class="logo">Groco<span>.</span></a>

        <nav class="navbar">
            <a href="home.php">Accueil</a>
            <a href="shop.php">Boutique</a>
            <a href="orders.php">Commandes</a>
            <a href="about.php">A propos</a>
            <a href="contact.php">contact</a>
        </nav>

        <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <div id="user-btn" class="fas fa-user"></div>
            <a href="search_page.php" class="fas fa-search"></a>
            <?php
            $count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $count_cart_items->bind_param("i", $user_id);
            $count_cart_items->execute();
            $count_cart_items_result = $count_cart_items->get_result();

            $count_wishlist_items = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ?");
            $count_wishlist_items->bind_param("i", $user_id);
            $count_wishlist_items->execute();
            $count_wishlist_items_result = $count_wishlist_items->get_result();
            ?>
            <a href="wishlist.php"><i class="fas fa-heart"></i><span>(<?= $count_wishlist_items_result->num_rows; ?>)</span></a>
            <a href="cart.php"><i class="fas fa-shopping-cart"></i><span>(<?= $count_cart_items_result->num_rows; ?>)</span></a>
        </div>

        <div class="profile">
            <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->bind_param("i", $user_id);
            $select_profile->execute();
            $fetch_profile_result = $select_profile->get_result();
            $fetch_profile = $fetch_profile_result->fetch_assoc();
            ?>
            <img src="uploaded_img/<?= $fetch_profile['image']; ?>" alt="">
            <p><?= $fetch_profile['name']; ?></p>
            <a href="user_profile_update.php" class="btn">update profile</a>
            <a href="logout.php" class="delete-btn">logout</a>
            <div class="flex-btn">
                <a href="login.php" class="option-btn">login</a>
                <a href="register.php" class="option-btn">register</a>
            </div>
        </div>

    </div>

</header>
