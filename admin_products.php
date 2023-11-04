<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:login.php');
}

// Initialize the message array
$message = array();

if (isset($_POST['add_product'])) {

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $price = $_POST['price'];
   $price = filter_var($price, FILTER_SANITIZE_STRING);
   $category = $_POST['category'];
   $category = filter_var($category, FILTER_SANITIZE_STRING);
   $details = $_POST['details'];
   $details = filter_var($details, FILTER_SANITIZE_STRING);

   // Check if the selected category exists in the database
   $select_category = $conn->prepare("SELECT id_categorie FROM categorie WHERE nom_categorie = ?");
   $select_category->bind_param("s", $category);
   $select_category->execute();
   $category_result = $select_category->get_result();

   var_dump($category_result);

   $catquyery = "SELECT * FROM categorie";
   $catresult = $conn->query($catquyery);
   $catRows = array();
   while ($catRow = $catresult->fetch_object()) {
      $catRows[] = $catRow;
   }


   if ($category_result->num_rows > 0) {
      // Category exists, fetch the ID
      $row = $category_result->fetch_assoc();
      $categorie_id = $row['id_categorie'];
      $select_category->close();

      // Continue with product insertion
      $image = $_FILES['image']['name'];
      $image = filter_var($image, FILTER_SANITIZE_STRING);
      $image_size = $_FILES['image']['size'];
      $image_tmp_name = $_FILES['image']['tmp_name'];
      $image_folder = 'uploaded_img/' . $image;

      // Check if the name of the product already exists in the product table
      $select_product = $conn->prepare("SELECT * FROM produit WHERE nom_produit = ?");
      $select_product->bind_param("s", $name);
      $select_product->execute();
      $select_product_result = $select_product->get_result();

      if ($select_product_result->num_rows > 0) {
         $message[] = 'Ce nom de produit existe déjà !';
      } else {
         // Insert the product into the product table
         $insert_product = $conn->prepare("INSERT INTO produit (nom_produit, id_categorie, description_produit, prix_produit) VALUES (?, ?, ?, ?)");
         $insert_product->bind_param("ssss", $name, $categorie_id, $details, $price);
         $insert_result = $insert_product->execute();

         if ($insert_result) {
            if ($image_size > 2000000) {
               $message[] = 'La taille de l\'image est trop grande !';
            } else {
               move_uploaded_file($image_tmp_name, $image_folder);

               // Get the ID of the inserted product
               $last_insert_id = $conn->insert_id;

               // Insert the image into the type_produit table with the product ID
               $insert_image = $conn->prepare("INSERT INTO typeproduit (image_produit, id_produit) VALUES (?, ?)");
               $insert_image->bind_param("si", $image, $last_insert_id);
               $insert_image->execute();

               $message[] = 'Nouveau produit ajouté !';
            }
         }
      }
   } else {
      // Handle the case where the selected category doesn't exist
      $message[] = 'La catégorie sélectionnée n\'existe pas.';
   }
}

if (isset($_GET['delete'])) {

   $delete_id = $_GET['delete'];

   // Select the image to delete and the corresponding product
   $select_delete_image = $conn->prepare("SELECT image_produit, id_produit FROM typeproduit WHERE id_produit = ?");
   $select_delete_image->bind_param("i", $delete_id);
   $select_delete_image->execute();
   $select_delete_image->store_result();
   $select_delete_image->bind_result($fetch_delete_image, $id_produit);
   $select_delete_image->fetch();
   unlink('uploaded_img/' . $fetch_delete_image);

   // Delete the record from the type_produit table
   $delete_image = $conn->prepare("DELETE FROM typeproduit WHERE id_produit = ?");
   $delete_image->bind_param("i", $delete_id);
   $delete_image->execute();

   // Delete the record from the product table
   $delete_product = $conn->prepare("DELETE FROM produit WHERE id_produit = ?");
   $delete_product->bind_param("i", $delete_id);
   $delete_product->execute();

   header('location:admin_products.php');
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>products</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>

<body>

   <?php include 'admin_header.php'; ?>

   <section class="add-products">
      <h1 class="title">Ajouter un nouveau produit</h1>
      <form action="" method="POST" enctype="multipart/form-data">
         <div class="flex">
            <div class="inputBox">
               <input type="text" name="name" class="box" required placeholder="Saisir le nom du produit">
               <select name="category" class="box" required>
                  <option value="" selected disabled>Choisir la catégorie</option>
                  <?php foreach ($catRows as $cat) { ?>
                     <option value="<?php echo $cat->nom_categorie ?>"><?php echo $cat->nom_categorie ?></option>
                  <?php } ?>
               </select>
            </div>

            

            <div class="inputBox">
               <input type="number" min="0" name="price" class="box" required placeholder="Saisir le prix du produit">
               <input type="file" name="image" required class="box" accept="image/jpg, image/jpeg, image/png">
            </div>
         </div>
         <textarea name="details" class="box" required placeholder="Détails du produit" cols="30" rows="10"></textarea>
         <input type="submit" class="btn" value="add product" name="add_product">
      </form>
   </section>

   <section class="show-products">
      <h1 class="title">Produits ajoutés</h1>
      <div class="box-container">

         <?php

         $show_products = $conn->prepare("SELECT p.*, t.image_produit FROM produit p INNER JOIN typeproduit t ON t.id_produit = p.id_produit;");
         $show_products->execute();
         $show_products->store_result();
         if ($show_products->num_rows > 0) {
            $show_products->bind_result($id, $name, $details, $price, $category, $image_product);
            while ($show_products->fetch()) {
               $select_category = $conn->prepare("SELECT nom_categorie FROM categorie WHERE id_categorie = ?");
               $select_category->bind_param("s", $category);
               $select_category->execute();
               $category_result = $select_category->get_result();

               if ($category_result->num_rows > 0) {
                  // Category exists, fetch the ID
                  $row = $category_result->fetch_assoc();
                  $categorie_id = $row['nom_categorie'];
                  $select_category->close();
               }
         ?>
               <div class="box">
                  <div class="price"><?= $price; ?>€/kg</div>
                  <img src="uploaded_img/<?= $image_product; ?>" alt="">
                  <div class="name"><?= $name; ?></div>
                  <div class="cat"><?= $categorie_id; ?></div>
                  <div class="details"><?= $details; ?></div>
                  <div class="flex-btn">
                     <a href="admin_update_product.php?update=<?= $id; ?>" class="option-btn">Modifier</a>
                     <a href="admin_products.php?delete=<?= $id; ?>" class="delete-btn" onclick="return confirm('delete this product?');">Supprimer</a>
                  </div>
               </div>
         <?php
            }
         } else {
            echo '<p class="empty">Aucun produit ajouté pour le moment</p>';
         }
         ?>

      </div>
   </section>

   <script src="js/script.js"></script>

</body>

</html>