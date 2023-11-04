<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location: login.php');
}

if (isset($_POST['update_product'])) {

   $pid = $_POST['pid'];
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $price = $_POST['price'];
   $price = filter_var($price, FILTER_SANITIZE_STRING);
   $category = $_POST['category'];
   $category = filter_var($category, FILTER_SANITIZE_STRING);
   $details = $_POST['details'];
   $details = filter_var($details, FILTER_SANITIZE_STRING);

   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/' . $image;
   $old_image = $_POST['old_image'];

      
        $select_category = $conn->prepare("SELECT id_categorie FROM categorie WHERE nom_categorie = ?");
        $select_category->bind_param("s", $category);
        $select_category->execute();
        $category_result = $select_category->get_result();

   if ($category_result->num_rows > 0){ 
       // Category exists, fetch the ID
       $row = $category_result->fetch_assoc();
       $categorie_id = $row['id_categorie'];
       $select_category->close();
     }


   $update_product = $conn->prepare("UPDATE `produit` SET nom_produit = ?, id_categorie = ?, description_produit = ?, prix_produit = ? WHERE id_produit = ?");
   $update_product->bind_param("ssssi", $name, $categorie_id, $details, $price, $pid);
   $update_product->execute();

   $message[] = 'Produit mis à jour avec succès !';

   if (!empty($image)) {
      if ($image_size > 2000000) {
         $message[] = 'La taille de l\'image est trop grande !';
      } else {

         $update_image = $conn->prepare("UPDATE `typeproduit` SET image_produit = ? WHERE id_produit = ?");
         $update_image->bind_param("si", $image, $pid);
         $update_image->execute();

         if ($update_image) {
            move_uploaded_file($image_tmp_name, $image_folder);
            unlink('uploaded_img/' . $old_image);
            $message[] = 'Image mise à jour avec succès !';
         }
      }
   }

   $update_product->close();


}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Modifier les produits</title>

   <!-- Lien du fichier CSS personnalisé -->
   <link rel="stylesheet" href="css/admin_style.css">
   <link rel="stylesheet" href="css/components.css">
   

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="update-product">

   <h1 class="title">Modifier le produit</h1>   

   <?php
        

        $update_id = $_GET['update'];
      $select_products = $conn->prepare("SELECT p.*, t.image_produit FROM produit p INNER JOIN typeproduit t ON t.id_produit = p.id_produit WHERE p.id_produit = ?");
      $select_products->bind_param("i", $update_id);
      $select_products->execute();
      $result = $select_products->get_result();

      $catquery = "SELECT * FROM categorie";
      $catresult = $conn->query($catquery);
      $catRows = array();
      while ($catRow = $catresult->fetch_object()) {
         $catRows[] = $catRow;
      }


      if ($result->num_rows > 0) {
         while ($fetch_products = $result->fetch_assoc()) {
             

            $select_category = $conn->prepare("SELECT nom_categorie FROM categorie WHERE id_categorie = ?");
            $select_category->bind_param("s", $fetch_products['id_categorie']);
            $select_category->execute();
            $category_result = $select_category->get_result();
            
      if ($category_result->num_rows > 0) 
        // Category exists, fetch the ID
            $row = $category_result->fetch_assoc();
            $categorie_id = $row['nom_categorie'];
            $select_category->close();

   ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="old_image" value="<?= $fetch_products['image_produit']; ?>">
      <input type="hidden" name="pid" value="<?= $fetch_products['id_produit']; ?>">
      <img src="uploaded_img/<?= $fetch_products['image_produit']; ?>" alt="">
      <input type="text" name="name" placeholder="Entrez le nom du produit" required class="box" value="<?= $fetch_products['nom_produit']; ?>">
      <input type="number" name="price" min="0" placeholder="Entrez le prix du produit" required class="box" value="<?= $fetch_products['prix_produit']; ?>">
      <select name="category" class="box" required>
        
         <?php foreach ($catRows as $cat) { ?>
                     <option value="<?php echo $cat->nom_categorie ?>"><?php echo $cat->nom_categorie ?></option>
                  <?php } ?>
      </select>
      <textarea name="details" required placeholder="Entrez les détails du produit" class="box" cols="30" rows="10"><?= $fetch_products['description_produit']; ?></textarea>
      <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
      <div class="flex-btn">
         <input type="submit" class="btn" value="Mettre à jour le produit" name="update_product">
         <a href="admin_products.php" class="option-btn">Retourner</a>
      </div>
   </form>
   <?php
         }
      } else {
         echo '<p class="empty">Aucun produit trouvé !</p>';
      }

      $select_products->close();
   ?>

</section>

<script src="js/script.js"></script>

</body>
</html>
