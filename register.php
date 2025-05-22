<?php

include 'config.php';

if(isset($_POST['submit'])){
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $balance = mysqli_real_escape_string($conn, $_POST['balance']);
    $password = mysqli_real_escape_string($conn, md5($_POST['password']));
    $cpassword = mysqli_real_escape_string($conn, md5($_POST['cpassword']));
    $country = mysqli_real_escape_string($conn, $_POST['country']);

   
    $emailCheck = mysqli_query($conn, "SELECT * FROM `account` WHERE email = '$email'") or die('email check query failed');

    if(mysqli_num_rows($emailCheck) > 0){
        $message[] = 'Email already exists! Please use a different email address.';
    } elseif ($password !== $cpassword) {
        $message[] = 'Password and Confirm Password do not match!';
    } else {
       
        mysqli_query($conn, "INSERT INTO `account` (email, password) VALUES ('$email', '$password')") or die('account query failed');

      
        mysqli_query($conn, "INSERT INTO `customer` (fname, lname, phone, country, email ,balance) 
        VALUES ('$fname', '$lname', '$phone', '$country', '$email' ,'$balance')") or die('customer query failed');

        $message[] = 'Registered successfully!';
        header('location: login.php');
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

<div class="container">
    <div class="box form-box">
        <header>Registration</header>
        <form action="" method="post">
            <div class="field input">
                <label for="fname">First Name</label>
                <input type="text" name="fname" autocomplete="off" id="fname"  required>
            </div>
            <div class="field input">
                <label for="lname">Last Name</label>
                <input type="text" name="lname" autocomplete="off" id="lname"  required>
            </div>
            <div class="field input">
                <label for="phone">Phone Number</label>
                <input type="text" name="phone" autocomplete="off" id="phone"  required>
            </div>
            <div class="field input">
                <label for="email">Email</label>
                <input type="email" name="email" autocomplete="off" id="email" required>
            </div>
            <div class="field input">
                <label for="balance">Balance</label>
                <input type="text" name="balance" autocomplete="off" id="balance" required>
            </div>
            <div class="field input">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="field input">
                <label for="cpassword">Confirm Password</label>
                <input type="password" name="cpassword" id="cpassword" required>
            </div>
            <div class="field input">
                <label for="country">Country</label>
                <select name="country" id="country" required>
                    <option value="" disabled selected>Select your country</option>
                    <option value="United States">United States</option>
                    <option value="United Kingdom">United Kingdom</option>
                    <option value="London">London</option>
                    <option value="Cairo">Cairo</option>
                    <option value="Alexandria">Alexandria</option>
                </select>
            </div>
           
            <div class="field">
                <button type="submit" class="button" name="submit">Registration</button>
            </div>
            <div class="link">
                Already have an account? <a href="login.php">Login</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
