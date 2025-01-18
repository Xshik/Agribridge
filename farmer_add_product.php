<?php

@include 'config.php';
@include 'email_config.php'; // Include the email configuration file

session_start();

$farmer_id = $_SESSION['farmer_id'];

if (!isset($farmer_id)) {
   header('location:login.php');
}

if (isset($_POST['add_product'])) {

   $name = $_POST['name'];
   $price = $_POST['price'];
   $category = $_POST['category'];
   $details = $_POST['details'];
   $quantity = $_POST['quantity']; // New field for quantity

   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $price = filter_var($price, FILTER_SANITIZE_STRING);
   $category = filter_var($category, FILTER_SANITIZE_STRING);
   $details = filter_var($details, FILTER_SANITIZE_STRING);
   $quantity = filter_var($quantity, FILTER_SANITIZE_NUMBER_INT);

   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;

   $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
   $select_products->execute([$name]);

   if ($select_products->rowCount() > 0) {
      $_SESSION['message'] = 'Product name already exists!';
   } else {
      $insert_products = $conn->prepare("INSERT INTO `products`(name, category, details, price, quantity, image) VALUES(?,?,?,?,?,?)");
      $insert_products->execute([$name, $category, $details, $price, $quantity, $image]);

      if ($insert_products) {
         if ($image_size > 2000000) {
            $_SESSION['message'] = 'Image size is too large!';
         } else {
            move_uploaded_file($image_tmp_name, $image_folder);
            $_SESSION['message'] = 'New product added!';

            // Send email to users
            $select_users = $conn->prepare("SELECT email FROM `users`");
            $select_users->execute();
            $users = $select_users->fetchAll(PDO::FETCH_ASSOC);

            $recipients = array_column($users, 'email');
            $subject = "New Product Added: $name";
            $body = "<p>A new product has been added by a farmer:</p>
                     <p>Product Name: $name</p>
                     <p>Category: $category</p>
                     <p>Details: $details</p>
                     <p>Price: $price</p>
                     <p>Quantity: $quantity kg</p>";

            sendEmail($recipients, $subject, $body);
         }
      }
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Add Product</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/farmer_style.css">

</head>

<body>

   <?php include 'farmer_header.php'; ?>

   <section class="add-products">

      <h1 class="title">Add New Product</h1>

      <?php
      if (isset($_SESSION['message'])) {
         echo '<div class="message">'.$_SESSION['message'].'</div>';
         unset($_SESSION['message']); // Clear the message after displaying it
      }
      ?>

      <form action="" method="POST" enctype="multipart/form-data" class="form-container">
         <div class="inputBox">
            <label for="name">Product Name</label>
            <input type="text" id="name" name="name" class="box" required placeholder="Enter product name">
         </div>
         <div class="inputBox">
            <label for="category">Category</label>
            <select id="category" name="category" class="box" required>
               <option value="" selected disabled>Select category</option>
               <option value="vegetables">tables</option>
               <option value="fruits">Fruits</option>
               <option value="meat">Vegetables</option>
            </select>
         </div>
         <div class="inputBox">
            <label for="quantity">Product Quantity (kg)</label>
            <input type="number" id="quantity" min="0" name="quantity" class="box" required placeholder="Enter product quantity (kg)">
         </div>
         <div class="inputBox">
            <label for="price">Price</label>
            <input type="number" id="price" min="0" name="price" class="box" required placeholder="Enter product price">
         </div>
         <div class="inputBox">
            <label for="image">Product Image</label>
            <input type="file" id="image" name="image" required class="box" accept="image/jpg, image/jpeg, image/png">
         </div>
         <div class="inputBox">
            <label for="details">Product Details</label>
            <textarea id="details" name="details" class="box" required placeholder="Enter product details" cols="30" rows="10"></textarea>
         </div>
         <input type="submit" class="btn" value="Add Product" name="add_product">
      </form>

   </section>

   <script src="js/script.js"></script>

</body>

</html>