<?php
include 'config.php';

$message = array(); // Initialisez le tableau des messages

if (isset($_POST['submit'])) {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $surname = filter_input(INPUT_POST, 'surname', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);
    $password = md5($_POST['password']);
    $cpass = md5($_POST['cpass']);

    $image = $_FILES['image']['name'];
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/' . $image;

    $select = $conn->prepare("SELECT * FROM `client` WHERE mail_client = ?");
    $select->bind_param("s", $email);
    $select->execute();
    $result = $select->get_result();

    if ($result->num_rows > 0) {
        $message[] = 'Cet email existe déjà!';
    } else {
        if ($password != $cpass) {
            $message[] = 'Les mots de passe ne sont pas identiques!';
        } else {
            $insert = $conn->prepare("INSERT INTO `client` (nom_client, prenom_client, mail_client, adresse_client, tel_client, mot_de_passe_client, image_profil) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $insert->bind_param("sssssss", $name, $surname, $email, $address, $telephone, $password, $image);
            $insert->execute();

            if ($insert) {
                if ($image_size > 2000000) {
                    $message[] = "Taille de l'image trop grande!";
                } else {
                    move_uploaded_file($image_tmp_name, $image_folder);
                    $message[] = 'Enregistré avec succès';
                    header('Location: login.php');
                    exit(); // Arrête l'exécution du script après la redirection
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Inscription</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/components.css">
</head>
<body>
<section class="form-container">
   <form action="" enctype="multipart/form-data" method="POST">
      <h3>S'inscrire</h3>
      <?php
      if (!empty($message)) {
          foreach ($message as $msg) {
              echo '<div class="message"><span>' . $msg . '</span><i class="fas fa-times" onclick="this.parentElement.remove();"></i></div>';
          }
      }
      ?>
      <input type="text" name="name" class="box" placeholder="Votre nom" required>
      <input type="text" name="surname" class="box" placeholder="Votre prénom" required>
      <input type="email" name="email" class="box" placeholder="Email" required>
      <input type="text" name="address" class="box" placeholder="Votre adresse" required>
      <input type="text" name="telephone" class="box" placeholder="06********" required>
      <input type="password" name="password" class="box" placeholder="Mot de passe" required>
      <input type="password" name="cpass" class="box" placeholder="Confirmer le mot de passe" required>
      <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
      <input type="submit" value="S'inscrire" class="btn" name="submit">
      <p>Avez-vous déjà un compte? <a href="login_user.php">Se Connecter</a></p>
   </form>
</section>
</body>
</html>
