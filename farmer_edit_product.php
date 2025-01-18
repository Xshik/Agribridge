<?php

@include 'config.php';

session_start();

$farmer_id = $_SESSION['farmer_id'];

if (!isset($farmer_id)) {
   header('location:login.php');
   exit;
}

if (isset($_GET['edit'])) {
   $edit_id = $_GET['edit'];
   $select_product = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
   $select_product->execute([$edit_id]);
   if ($select_product->rowCount() > 0) {
      $fetch_product = $select_product->fetch(PDO::FETCH_ASSOC);
   } else {
      $message[] = 'Product not found!';
      header('location:farmer_products.php');
      exit;
   }
}

if (isset($_POST['update_product'])) {
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $price = filter_var($_POST['price'], FILTER_SANITIZE_STRING);
   $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);
   $details = filter_var($_POST['details'], FILTER_SANITIZE_STRING);
   $quantity = filter_var($_POST['quantity'], FILTER_SANITIZE_STRING);

   $update_product = $conn->prepare("UPDATE `products` SET name = ?, category = ?, details = ?, price = ?, quantity = ? WHERE id = ?");
   $update_product->execute([$name, $category, $details, $price, $quantity, $edit_id]);

   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/' . $image;

   if (!empty($image)) {
      if ($image_size > 2000000) {
         $message[] = 'Image size is too large!';
      } else {
         $update_image = $conn->prepare("UPDATE `products` SET image = ? WHERE id = ?");
         $update_image->execute([$image, $edit_id]);
         move_uploaded_file($image_tmp_name, $image_folder);
      }
   }

   $message[] = 'Product updated successfully!';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Edit Product</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/farmer_style.css">
</head>

<body>

   <?php include 'farmer_header.php'; ?>

   <section class="edit-product">

      <h1 class="title">Edit Product</h1>

      <?php
      if (isset($message)) {
         foreach ($message as $msg) {
            echo '<p class="message">' . $msg . '</p>';
         }
      }
      ?>

      <form action="" method="POST" enctype="multipart/form-data">
         <div class="flex">
            <div class="inputBox">
               <input type="text" name="name" class="box" required placeholder="Enter product name"
                  value="<?= $fetch_product['name']; ?>">
               <select name="category" class="box" required>
                  <option value="<?= $fetch_product['category']; ?>" selected><?= $fetch_product['category']; ?></option>
                  <option value="meat">Vegetables</option>
                  <option value="fruits">Fruits</option>
               </select>
            </div>
            <div class="inputBox">
               <input type="number" min="0" name="price" class="box" required placeholder="Enter product price"
                  value="<?= $fetch_product['price']; ?>">
               <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
               <input type="number" min="0" name="quantity" class="box" required
                  placeholder="Enter product quantity (kg)" value="<?= $fetch_product['quantity']; ?>">
            </div>
         </div>
         <textarea name="details" class="box" required placeholder="Enter product details" cols="30"
            rows="10"><?= $fetch_product['details']; ?></textarea>
         <input type="submit" class="btn" value="Update Product" name="update_product">
      </form>

   </section>

   <script src="js/script.js"></script>

</body>

</html>