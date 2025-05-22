<?php

include 'config.php';
session_start();

if(isset($_POST['login_btn'])){

    if($_POST['email'] =='admin@admin.com' && $_POST['password']=='admin'){
        $_SESSION['email'] = ($_POST['email']);
        header('location:admin_dashboard.php');
     }
     else {
        
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $passowrd = mysqli_real_escape_string($conn, md5($_POST['password']));

   $select = mysqli_query($conn, "SELECT * FROM `account` WHERE email = '$email' AND password = '$passowrd'") or die('query failed');


   if(mysqli_num_rows($select) > 0){
      
    $row = mysqli_fetch_assoc($select);
    $_SESSION['email'] = $row['email'];
      header('location:cars.php');
   }else{
      $message[] = 'incorrect password or email!';
   }
     }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/style2.css">
    <title>Login</title>
</head>

<body>
<?php
if (isset($message)) {
    echo '<div class="message">' . $message[0] . '</div>';
}
?>

    <div class="container">
        <div class="box form-box">

            <header>Login</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="email">Email</label>
                    <input type="email" name="email" autocomplete="off" id="email" required>
                </div>
                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div class="field">
                    <button type="submit" onclick="welc()" class="button" name="login_btn">Login</button>
                </div>
                <div class="link">
                    Don't have account? <a href="register.php">Register</a>
                </div>
            </form>
        </div>
        
    </div>
</body>

</html>