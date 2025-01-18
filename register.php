<?php

@include 'config.php';

if (isset($_POST['submit'])) {

    // Sanitize and validate inputs
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $pass = filter_var($_POST['pass'], FILTER_SANITIZE_STRING);
    $cpass = filter_var($_POST['cpass'], FILTER_SANITIZE_STRING);
    $image = filter_var($_FILES['image']['name'], FILTER_SANITIZE_STRING);
    $user_type = filter_var($_POST['user_type'], FILTER_SANITIZE_STRING);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message[] = 'Invalid email format!';
    }

    // Validate password length and match
    if (strlen($pass) < 6) {
        $message[] = 'Password must be at least 6 characters long!';
    } elseif ($pass !== $cpass) {
        $message[] = 'Confirm password does not match!';
    }

    // Hash the passwords
    $pass_hash = md5($pass);

    // Validate image file type and size
    $allowed_file_types = ['jpg', 'jpeg', 'png'];
    $image_extension = pathinfo($image, PATHINFO_EXTENSION);
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/' . $image;

    if (!in_array($image_extension, $allowed_file_types)) {
        $message[] = 'Only JPG, JPEG, and PNG files are allowed!';
    }

    if ($image_size > 2000000) {
        $message[] = 'Image size is too large!';
    }

    // Validate user type
    $valid_user_types = ['user', 'admin', 'farmer'];
    if (!in_array($user_type, $valid_user_types)) {
        $user_type = 'user'; // Default to 'user' if invalid
    }

    // Check if email already exists
    $select = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
    $select->execute([$email]);

    if ($select->rowCount() > 0) {
        $message[] = 'User email already exists!';
    } else {
        // Proceed with registration if there are no validation errors
        if (empty($message)) {
            $insert = $conn->prepare("INSERT INTO `users`(name, email, password, image, user_type) VALUES(?,?,?,?,?)");
            $insert->execute([$name, $email, $pass_hash, $image, $user_type]);

            if ($insert) {
                move_uploaded_file($image_tmp_name, $image_folder);
                $message[] = 'Registered successfully!';
                header('location:login.php');
            } else {
                $message[] = 'Registration failed! Please try again.';
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
    <title>Register</title>
    <style>
        body {
    margin: 50px;
    padding: 50;
    font-family: Arial, sans-serif;
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.7)), url('css/farmer.jpg') no-repeat center center fixed;
    background-size: cover;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100;
    
}


.form-container {
    background: rgba(255, 255, 255, 0.95);
    padding: 20px ;
    border-radius: 10px;
    box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.3);
    width: 350px;
    text-align: center;
    animation: fadeIn 0.8s ease-in-out;
}

.form-container h3 {
    font-size: 22px;
    margin-bottom: 20px;
    color:rgb(0, 0, 0);
    text-transform: uppercase;
    letter-spacing: 1px;
}

.form-container .box {
    width: calc(100% - 20px);
    padding: 12px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px;
    background: #f9f9f9;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
}

.form-container .box:focus {
    outline: none;
    border-color: #006d77;
    box-shadow: 0 0 5px rgba(0, 109, 119, 0.5);
}

.form-container .btn {
    width: 100%;
    padding: 12px;
    background-image: linear-gradient(90deg, #11cb30, #2575fc);
    color: #fff;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s;
}

.form-container .btn:hover {
    background-color: #004f55;
    transform: scale(1.03);
}

.form-container p {
    font-size: 14px;
    margin-top: 15px;
    color: #333;
}

.form-container a {
    color: #006d77;
    font-weight: bold;
    text-decoration: none;
    transition: color 0.3s;
}

.form-container a:hover {
    color:rgb(25, 66, 128);
    text-decoration: underline;
}

.message {
    background: #f8d7da;
    color: #721c24;
    padding: 10px;
    border: 1px solid #f5c6cb;
    margin: 10px auto;
    border-radius: 5px;
    width: 90%;
    text-align: center;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    animation: slideIn 0.5s ease-in-out;
}

.message i {
    margin-left: 10px;
    cursor: pointer;
    font-size: 16px;
    color: #721c24;
    transition: color 0.3s;
}

.message i:hover {
    color: #000;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

    </style>
</head>
<body>

<?php
if (isset($message)) {
    foreach ($message as $msg) {
        echo '
        <div class="message">
            <span>' . $msg . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>
        ';
    }
}
?>
<div class="container">
<section class="form-container">
    <form action="" enctype="multipart/form-data" method="POST">
        <h2>Agribridge</h2>
        <h3>Register Now</h3>
        <input type="text" name="name" class="box" placeholder="Enter your name" required>
        <input type="email" name="email" class="box" placeholder="Enter your email" required>
        <input type="password" name="pass" class="box" placeholder="Enter your password" required>
        <input type="password" name="cpass" class="box" placeholder="Confirm your password" required>
        <input type="file" name="image" class="box" required accept="image/jpg, image/jpeg, image/png">
        <select name="user_type" class="box" required>
            <option value="user" selected>User</option>
            <option value="admin">Admin</option>
            <option value="farmer">Farmer</option>
        </select>
        <input type="submit" value="Register Now" class="btn" name="submit">
        <p>Already have an account? <a href="login.php">Login now</a></p>
    </form>
</section>
</div>
</body>
</html>
