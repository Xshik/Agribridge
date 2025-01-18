<?php

@include 'config.php';

session_start();

$farmer_id = $_SESSION['farmer_id'];

if (!isset($farmer_id)) {
   header('location:login.php');
   exit;
}

$select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
$select_profile->execute([$farmer_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Farmer Dashboard</title>
   <!-- font awesome cdn link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <!-- custom css file link -->
   <link rel="stylesheet" href="css/farmer_style.css">
</head>
<body>
   
<?php include 'farmer_header.php'; ?>

<section class="profile" id="profile-section" style="display: none;">
   <img src="uploaded_img/<?= $fetch_profile['image']; ?>" alt="Profile Image">
   <p><?= $fetch_profile['name']; ?></p>
   <a href="user_profile_update.php" class="btn">Update Profile</a>
   <a href="logout.php" class="delete-btn">Logout</a>
   <div class="flex-btn">
      <a href="login.php" class="option-btn">Login</a>
      <a href="register.php" class="option-btn">Register</a>
   </div>
</section>

<section class="dashboard">
   <h1 class="title">Farmer Dashboard</h1>
   <div class="box-container">
      <div class="box">
         <h3>Add Product</h3>
         <a href="farmer_add_product.php" class="btn">Go</a>
      </div>
      <div class="box">
         <h3>View Orders</h3>
         <a href="farmer_orders.php" class="btn">Go</a>
      </div>
      <div class="box">
         <h3>Your Products</h3>
         <a href="farmer_products.php" class="btn">Go</a>
      </div>
   </div>
</section>

<!-- Footer Section -->
<footer class="footer">
   <div class="container">
      <p>Contact Us: </p>
      <p>Email: agribridge@gmail.com</p>
      <p>Phone:8549992666</p>
   </div>
</footer>

<script src="js/script.js"></script>

</body>
</html>