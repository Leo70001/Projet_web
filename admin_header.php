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
        <a href="admin_page.php" class="logo">Panneau d'<span>administration</span></a>
        <nav class="navbar">
            <a href="admin_page.php">Accueil</a>
            <a href="admin_products.php">Produits</a>
            <a href="admin_orders.php">Commandes</a>
            <a href="admin_users.php">Utilisateurs</a>
            <a href="admin_contacts.php">Messages</a>
        </nav>
        <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <div id="user-btn" class="fas fa-user"></div>
        </div>
        <div class="profile">
            <?php
            $select_profile = $conn->prepare("SELECT * FROM `administrateur` WHERE id_admin = ?");
            $select_profile->bind_param("i", $admin_id); // Lier la valeur de l'ID
            $admin_id = $_SESSION['admin_id'];
            $select_profile->execute();
            $result_profile = $select_profile->get_result();
            if ($fetch_profile = $result_profile->fetch_assoc()) {
            ?>
                <img src="uploaded_img/<?= $fetch_profile['image_admin']; ?>" alt="">
                <p><?= $fetch_profile['nom_admin'] .' '. $fetch_profile['prenom_admin']; ?></p>
                <a href="admin_update_profile.php" class="btn">Mettre à jour le profil</a>
                <a href="logout.php" class="delete-btn">Déconnexion</a>
                
            <?php
            }
            ?>
        </div>
    </div>
</header>
