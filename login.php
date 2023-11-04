<?php
// Inclure le fichier de configuration
@include 'config.php';

// Démarrer la ses
session_start();

if (isset($_POST['submit'])) {
   // Récupérer et filtrer les données du formulaire
   $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
   $pass = md5($_POST['pass']); // Hacher le mot de passe
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   // Requête SQL pour vérifier l'utilisateur (client ou administrateur)
   $sql = "SELECT id_client, NULL as id_admin, mail_client, mot_de_passe_client FROM `client` 
           WHERE mail_client = ? AND mot_de_passe_client = ?
           UNION
           SELECT NULL as id_client, id_admin, mail_admin, mot_de_passe_admin FROM `administrateur`
           WHERE mail_admin = ? AND mot_de_passe_admin = ?";

   // Préparer la requête SQL
   $stmt = $conn->prepare($sql);
   $stmt->bind_param("ssss", $email, $pass, $email, $pass);
   $stmt->execute();
   $result = $stmt->get_result();

   if ($result->num_rows == 1) {
       $row = $result->fetch_assoc();
       
       if ($row["id_client"]) {
           // L'utilisateur est un client, stocker son ID dans la session et le rediriger vers la page d'accueil du client
           $_SESSION['user_id'] = $row["id_client"];
           header('location: home.php');
       } elseif ($row["id_admin"]) {
           // L'utilisateur est un administrateur, stocker son ID dans la session et le rediriger vers la page d'administration
           $_SESSION['admin_id'] = $row["id_admin"];
           header('location: admin_page.php');
       }
   } else {
       $message = 'Mot de passe ou email incorrect';
   }
}

?>


<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>connexion</title>

   
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   
   <link rel="stylesheet" href="css/components.css">

</head>
<body>

<?php

//iteration dans le tableau message pour afficher 
if(isset($message)){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      '; 
}
?>

<section class="form-container">
   <form action="" method="POST">
      <h3>Se Connecter</h3>
      <input type="email" name="email" class="box" placeholder="addresse mail" required>
      <input type="password" name="pass" class="box" placeholder="mot de passe" required>
      <input type="submit" value="Connexion" class="btn" name="submit">
      <p>Vous n'avez pas de compte? <a href="register.php">S'inscrire</a></p>
   </form>
</section>

</body>
</html>
