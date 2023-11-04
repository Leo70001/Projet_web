<?php
@include 'config.php';
 
session_start();
 
$admin_id = $_SESSION['admin_id'];
 
if (!isset($admin_id)) {
    header('location: login.php');
}
 
if (isset($_POST['update_order'])) {
    $order_id = $_POST['order_id'];
    $update_payment = $_POST['update_payment'];
    $update_payment = filter_var($update_payment, FILTER_SANITIZE_STRING);
 
    $update_orders = $conn->prepare("UPDATE commande SET statut_commande = ? WHERE id_commande = ?");
    $update_orders->bind_param("si", $update_payment, $order_id);
 
    if ($update_orders->execute()) {
        $message[] = 'Payment has been updated!';
    } else {
        $message[] = 'Failed to update payment.';
    }
    $update_orders->close();
}
 
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
 
    $delete_orders = $conn->prepare("DELETE FROM commande WHERE id_commande = ?");
    $delete_orders->bind_param("i", $delete_id);
 
    if ($delete_orders->execute()) {
        $delete_orders->close();
        header('location: admin_orders.php');
    } else {
        $message[] = 'Failed to delete order.';
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE-=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Orders</title>
 
   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
 
   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>
 
<?php include 'admin_header.php'; ?>
 
<section class="placed-orders">
 
   <h1 class="title">Commandes</h1>
 
   <div class="box-container">
 
      <?php
         $select_orders = $conn->prepare("SELECT commande.*, client.* FROM commande INNER JOIN client ON commande.id_client = client.id_client");
         $select_orders->execute();
         $result = $select_orders->get_result();
 
         if ($result->num_rows > 0) {
            while ($fetch_orders = $result->fetch_assoc()) {
      ?>
      <div class="box">
         <p> ID Client : <span><?= $fetch_orders['id_client']; ?></span> </p>
         <p> Date de la commande : <span><?= $fetch_orders['date_commande']; ?></span> </p>
         <p> Nom du client : <span><?= $fetch_orders['nom_client']; ?></span> </p>
         <p> Prénom du client : <span><?= $fetch_orders['prenom_client']; ?></span> </p>
         <p> Email : <span><?= $fetch_orders['mail_client']; ?></span> </p>
         <p> téléphone : <span><?= $fetch_orders['tel_client']; ?></span> </p>
         <p> Adresse : <span><?= $fetch_orders['adresse_client']; ?></span> </p>
         <p> Montant : <span><?= $fetch_orders['montant_commande']; ?>€</span> </p>
         <form action="" method="POST">
            <input type="hidden" name="order_id" value="<?= $fetch_orders['id_commande']; ?>">
            <select name="update_payment" class="drop-down">
               <option value="" selected disabled><?= $fetch_orders['statut_commande']; ?></option>
               <option value="en-cours">en-cours</option>
               <option value="finalisé">finalisé</option>
            </select>
            <div class="flex-btn">
               <input type="submit" name="update_order" class="option-btn" value="Mettre à jour">
               <a href="admin_orders.php?delete=<?= $fetch_orders['id_commande']; ?>" class="delete-btn" onclick="return confirm('Vous allez supprimer la commande');">Supprimer</a>
            </div>
         </form>
      </div>
      <?php
         }
         } else {
             echo '<p class="empty">Aucune commande pour le moment</p>';
         }
         $select_orders->close();
      ?>
 
   </div>
 
</section>
 
<script src="js/script.js"></script>
 
</body>
</html>