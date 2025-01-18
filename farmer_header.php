<?php

@include 'config.php';

// Check if a session is already active before starting a new one
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$farmer_id = $_SESSION['farmer_id'];

if (!isset($farmer_id)) {
    header('location:login.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Panel</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/farmer_style.css">

    <style>
        /* Remove underline from FarmerPanel link */
        .header .logo {
            text-decoration: none;
        }
    </style>
</head>
<body>

<header class="header">
    <div class="flex">
        <a href="farmer_dashboard.php" class="logo">Farmer<span>Panel</span></a>
        <nav class="navbar">
            <a href="farmer_dashboard.php">Home</a>
            <a href="farmer_add_product.php">Add Product</a>
            <a href="farmer_orders.php">View Orders</a>
            <a href="farmer_products.php">Your Products</a>
        </nav>
        <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <div id="account-btn" class="fas fa-user"></div> <!-- Add this line for the account icon -->
        </div>
    </div>
</header>

<script src="js/script.js"></script>

</body>
</html>