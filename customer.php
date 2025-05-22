<?php

include 'config.php';
session_start();
$email = $_SESSION['email'];

if(!isset($email)){
   header('location:login.php');
};

if(isset($_GET['logout'])){
   unset($email);
   session_destroy();
   header('location:login.php');
};

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="css/style2.css">

</head>
<body>
   
<?php
if(isset($message)){
   foreach($message as $message){
      echo '<div class="message" onclick="this.remove();">'.$message.'</div>';
   }
}
?>

<div class="user_container">

<div class="user-profile">

   <?php
      $select_user = mysqli_query($conn, "SELECT * FROM `customer` WHERE email = '$email'") or die('query failed');
      if(mysqli_num_rows($select_user) > 0){
         $fetch_user = mysqli_fetch_assoc($select_user);
      };
   ?>

   <p> First Name : <span><?php echo $fetch_user['fname']; ?></span> </p>
   <p> Last name : <span><?php echo $fetch_user['lname']; ?></span> </p>
   <p> country : <span><?php echo $fetch_user['country']; ?></span> </p>
   <p> phone : <span><?php echo $fetch_user['phone']; ?></span> </p>
   <p> balance : <span><?php echo $fetch_user['balance']; ?></span> </p>
   <p> email : <span><?php echo $fetch_user['email']; ?></span> </p>
   <div class="flex">
   <a href="cars.php" class="btn">Cars</a>
    <!-- You still logged in -->
      <a href="login.php" class="btn">login</a>
      <a href="register.php" class="option-btn">register</a>
     <!-- You are out now -->
      <a href="customer.php?logout=<?php echo $email; ?>" onclick="return confirm('are your sure you want to logout?');" class="delete-btn">logout</a>
   </div>

</div>


</body>
</html>