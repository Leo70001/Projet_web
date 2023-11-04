<?php
@include 'config.php';
 
session_start();
 
$admin_id = $_SESSION['admin_id'];
 
if (!isset($admin_id)) {
   header('location: login.php');
}
 
if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
 
   $delete_users = $conn->prepare("DELETE FROM client WHERE id_client = ?");
   $delete_users->bind_param("i", $delete_id);
 
   if ($delete_users->execute()) {
       header('location: admin_users.php');
   } else {
       $message[] = 'Erreur de suppréssion de client.';
   }
 
   $delete_users->close();
}
?>
 
<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Clients</title>
 
   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
 
   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>
 
<?php include 'admin_header.php'; ?>
 
<section class="user-accounts">
 
   <h1 class="title">Comptes clients</h1>
 
   <div class="box-container">
 
      <?php
         $select_users = $conn->prepare("SELECT * FROM client");
         $select_users->execute();
         $result = $select_users->get_result();
 
         while ($fetch_users = $result->fetch_assoc()) {
      ?>
      <div class="box">
         <img src="uploaded_img/<?= $fetch_users['image_client']; ?>" alt="">
         <p> ID Client : <span><?= $fetch_users['id_client']; ?></span></p>
         <p> Nom : <span><?= $fetch_users['nom_client']; ?></span></p>
         <p> Prénom : <span><?= $fetch_users['prenom_client']; ?></span></p>
         <p> Adresse : <span><?= $fetch_users['adresse_client']; ?></span></p>
         <p> Email : <span><?= $fetch_users['mail_client']; ?></span></p>
         <p> Téléphone : <span><?= $fetch_users['tel_client']; ?></span></p>
         <a href="admin_users.php?delete=<?= $fetch_users['id_client']; ?>" onclick="return confirm('Vous allez supprimer le client');" class="delete-btn">Supprimer</a>
      </div>
      <?php
      }
      $select_users->close();
      ?>
   </div>
 
</section>
 
<script src="js/script.js"></script>
 
</body>
</html>