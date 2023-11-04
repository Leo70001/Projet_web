<?php

// Il faut modifier le formulaire pour prendre en compte tous les champs de la table administrateur dans la base de données
// Modifier le PHP dans ce sens-là

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location: login.php');
}

if (isset($_POST['update_profile'])) {
   $nom = $_POST['nom'];
   $nom = filter_var($nom, FILTER_SANITIZE_STRING);
   $prenom = $_POST['prenom'];
   $prenom = filter_var($prenom, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);

   $update_profile = $conn->prepare("UPDATE `administrateur` SET nom_admin = ?, prenom_admin = ?, mail_admin = ? WHERE id_admin = ?");
   $update_profile->bind_param("sssi", $nom, $prenom, $email, $admin_id);
   $update_profile->execute();
   $update_profile->close();

   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/' . $image;
   $old_image = $_POST['old_image'];

   if (!empty($image)) {
      if ($image_size > 2000000) {
         $message[] = 'La taille de l\'image est trop grande !';
      } else {
         $update_image = $conn->prepare("UPDATE `administrateur` SET image_admin = ? WHERE id_admin = ?");
         $update_image->bind_param("si", $image, $admin_id);
         $update_image->execute();

         if ($update_image) {
            move_uploaded_file($image_tmp_name, $image_folder);
            if (file_exists('uploaded_img/' . $old_image)) {
               unlink('uploaded_img/' . $old_image);
           }
            $message[] = 'Image mise à jour avec succès !';
         }
         
         $update_image->close();
      }
   }

   $old_pass = $_POST['old_pass'];
   $update_pass = md5($_POST['update_pass']);
   $update_pass = filter_var($update_pass, FILTER_SANITIZE_STRING);
   $new_pass = md5($_POST['new_pass']);
   $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);
   $confirm_pass = md5($_POST['confirm_pass']);
   $confirm_pass = filter_var($confirm_pass, FILTER_SANITIZE_STRING);

   if (!empty($update_pass) && !empty($new_pass) && !empty($confirm_pass)) {
      if ($update_pass != $old_pass) {
         $message[] = 'L\'ancien mot de passe ne correspond pas !';
      } elseif ($new_pass != $confirm_pass) {
         $message[] = 'Le mot de passe de confirmation ne correspond pas !';
      } else {
         $update_pass_query = $conn->prepare("UPDATE `administrateur` SET mot_de_passe_admin = ? WHERE id_admin = ?");
         $update_pass_query->bind_param("si", $confirm_pass, $admin_id);
         $update_pass_query->execute();
         $message[] = 'Mot de passe mis à jour avec succès !';
         $update_pass_query->close();
      }
   }

   $conn->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>update admin profile</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/components.css">
   <link rel="stylesheet" href="css/admin_style.css">
   

</head>
<body>
   <?php include 'admin_header.php'; ?>

<section class="update-profile">

   <h1 class="title">Mise à jour du profil</h1>

   <form action="" method="POST" enctype="multipart/form-data">
      <img src="uploaded_img/<?= $fetch_profile['image_admin']; ?>" alt="">
      <div class="flex">
         <div class="inputBox">
            <span>Nom d'utilisateur :</span>
            <input type="text" name="nom" value="<?= $fetch_profile['nom_admin']; ?>" placeholder="Mettre à jour le nom d'utilisateur" required class="box">
            <span>Prénom d'utilisateur :</span>
            <input type="text" name="prenom" value="<?= $fetch_profile['prenom_admin']; ?>" placeholder="Mettre à jour le prénom d'utilisateur" required class="box">
            <span>Email :</span>
            <input type="email" name="email" value="<?= $fetch_profile['mail_admin']; ?>" placeholder="Mettre à jour l'email" required class="box">
            <span>Mettre à jour la photo :</span>
            <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box">
            <input type="hidden" name="old_image" value="<?= $fetch_profile['image_admin']; ?>">
         </div>
         <div class="inputBox">
            <input type="hidden" name="old_pass" value="<?= $fetch_profile['mot_de_passe_admin']; ?>">
            <span>Ancien mot de passe :</span>
            <input type="password" name="update_pass" placeholder="Entrer l'ancien mot de passe" class="box">
            <span>Nouveau mot de passe :</span>
            <input type="password" name="new_pass" placeholder="Entrer le nouveau mot de passe" class="box">
            <span>Confirmer le mot de passe :</span>
            <input type="password" name="confirm_pass" placeholder="Confirmer le nouveau mot de passe" class="box">
         </div>
      </div>
      <div class="flex-btn">
         <input type="submit" class="btn" value="Mettre à jour le profil" name="update_profile">
         <a href="admin_page.php" class="option-btn">Revenir en arrière</a>
      </div>
   </form>

</section>


   <script src="js/script.js"></script>
</body>
</html>
