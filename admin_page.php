<?php
// Inclure le fichier de configuration
include 'config.php';
 
session_start();
 
$admin_id = $_SESSION['admin_id'];
 
if (!isset($admin_id)) {
    header('location:login.php');
}
?>
 
<!DOCTYPE html>
<html lang="fr">
 
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Administrateur</title>
 
    <!-- Lien vers la bibliothèque Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
 
    <!-- Lien vers le fichier CSS personnalisé -->
    <link rel="stylesheet" href="css/admin_style.css">
    <link rel="stylesheet" href="css/components.css">
   
</head>
 
<body>
    <?php include 'admin_header.php'; ?>
 
    <section class="dashboard">
        <h1 class="title">Tableau de bord</h1>
        <div class="box-container">
            <div class="box">
                <?php
                $total_pendings = 0;
                $select_pendings = $conn->prepare("SELECT * FROM `commande` WHERE statut_commande = ?");
                $select_pendings->bind_param("s", $payment_status_pending); // Définir le statut de paiement
                $payment_status_pending = 'en cours';
                $select_pendings->execute();
                $result_pendings = $select_pendings->get_result();
                while ($fetch_pendings = $result_pendings->fetch_assoc()) {
                    $total_pendings += $fetch_pendings['montant_commande'];
                };
                ?>
                <h3><?= $total_pendings; ?>€</h3>
                <p>Total en cours</p>
                <a href="admin_orders.php" class="btn">voir les commandes</a>
            </div>
 
            <div class="box">
                <?php
                    $total_completed = 0;
                    $select_completed = $conn->prepare("SELECT montant_commande FROM commande WHERE statut_commande = ?");
                    $select_completed->bind_param("s", $paymentStatus);
                    $paymentStatus = 'en cours';
                    $select_completed->execute();
 
                    $result = $select_completed->get_result();
                    while ($fetch_completed = $result->fetch_assoc()) {
                        $total_completed += $fetch_completed['montant_commande'];
                    }
                    $select_completed->close();
                ?>
                <h3><?= $total_completed; ?>€</h3>
                <p>Commandes réalisés</p>
                <a href="admin_orders.php" class="btn">voir les commandes</a>
            </div>
           
 
            <div class="box">
                <?php
                    $number_of_orders = 0;
                    $select_orders = $conn->prepare("SELECT * FROM commande");
                    $select_orders->execute();
                    $select_orders->store_result();
                    $number_of_orders = $select_orders->num_rows;
                    $select_orders->close();
                ?>
                <h3><?= $number_of_orders; ?></h3>
                <p>Commandes réalisés</p>
                <a href="admin_orders.php" class="btn">Voir les commandes</a>
            </div>
 
 
            <div class="box">
            <?php
                $number_of_products = 0;
                $select_products = $conn->prepare("SELECT * FROM produit");
                $select_products->execute();
                $select_products->store_result();
                $number_of_products = $select_products->num_rows;
                $select_products->close();
            ?>
            <h3><?= $number_of_products; ?></h3>
            <p>Produits ajoutés</p>
            <a href="admin_products.php" class="btn">Voir les produits</a>
        </div>
 
        <div class="box">
            <?php
                $number_of_admin = 0;
                $select_admin = $conn->prepare("SELECT * FROM administrateur");
                $select_admin->execute();
                $select_admin->store_result();
                $number_of_admin = $select_admin->num_rows;
                $select_admin->close();
            ?>
            <h3><?= $number_of_admin; ?></h3>
            <p>Administrateurs</p>
            <a href="admin_users.php" class="btn">Voir les comptes</a>
        </div>
            <!-- A modifier pour voir les comptes -->
 
        <div class="box">
            <?php
                $number_of_users = 0;
                $select_users = $conn->prepare("SELECT * FROM client");
                $select_users->execute();
                $select_users->store_result();
                $number_of_users = $select_users->num_rows;
                $select_users->close();
            ?>
            <h3><?= $number_of_users; ?></h3>
            <p>Clients</p>
            <a href="admin_users.php" class="btn">Voir les comptes</a>
        </div>
            <!-- A modifier pour voir les comptes -->
       
        <div class="box">
            <?php
                $total = $number_of_admin + $number_of_admin;
            ?>
        <h3><?= $total; ?></h3>
        <p>Total utlisateurs</p>
        <a href="admin_users.php" class="btn">Voir les comptes</a>
        </div>
            <!-- Autres boîtes avec des requêtes similaires ici -->
        </div>
    </section>
 
    <script src="js/script.js"></script>
</body>
 
</html>