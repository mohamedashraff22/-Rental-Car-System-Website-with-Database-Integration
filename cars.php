<?php

include 'config.php';
session_start();
$email = $_SESSION['email'];

if (!isset($email)) {
    header('location:login.php');
}

if (isset($_GET['logout'])) {
    unset($email);
    session_destroy();
    header('location:login.php');
}

// Function to get the customer balance
function getCustomerBalance($conn, $email)
{
    $result = mysqli_query($conn, "SELECT balance FROM customer WHERE email='$email'");
    $row = mysqli_fetch_assoc($result);
    return $row['balance'];
}

function getoffice($conn, $off_id)
{
    $result = mysqli_query($conn, "SELECT office_id FROM office WHERE country='$off_id'");
    $row = mysqli_fetch_assoc($result);
    return $row['office_id'];
}


function getcar($conn, $car_id)
{
    $result = mysqli_query($conn, "SELECT `status` FROM car WHERE car_id='$car_id'");
    $row = mysqli_fetch_assoc($result);
    return $row['status'];
}

// customer_id 
$customer_query = mysqli_query($conn, "SELECT customer_id FROM customer WHERE email = '$email'");
if ($customer_query) {
    $customer_data = mysqli_fetch_assoc($customer_query);
    $customer_id = $customer_data['customer_id'];
} else {
    die('Failed to fetch customer data.');
}

if (isset($_POST['rent'])) {
    $car_model = $_POST['car_model'];
    $car_id = $_POST['car_id'];
    $car_plate_id = $_POST['car_plate_id'];
    $car_price = $_POST['car_price'];
    $car_image = $_POST['car_image'];
    $pickup_date = $_POST['pickup_date'];
    $return_date = $_POST['return_date'];
    $rent_days = $_POST['rent_days'];
    $off_id = $_POST['selected_country'];
    

    $customer_balance = getCustomerBalance($conn, $email);

    $car_stat=getcar($conn,$car_id);
    $office_id=getoffice($conn,$off_id);

    $select_reservation = mysqli_query($conn, "SELECT * FROM `reservation` 
                                        WHERE car_id = '$car_id' AND customer_id = '$customer_id'") or die('query failed');
    if($car_stat!='Available'){
        $message[] = 'CAR ALREADY RENTED.';

    }

  else if ($customer_balance >= $car_price) {
        mysqli_query($conn, "INSERT INTO `reservation`(customer_id, car_id, payment_amount, image, pickup_date, return_date,office_id) 
        VALUES('$customer_id', '$car_id', '$car_price', '$car_image','$pickup_date' ,'$return_date','$office_id')") or die('query failed');
        $new_balance = $customer_balance - $car_price;
        mysqli_query($conn, "UPDATE customer SET balance = '$new_balance' WHERE email = '$email'");
        mysqli_query($conn, "UPDATE car SET `status`= 'Rented' WHERE car_id = '$car_id'");


        $message[] = 'Product added to reservation! Balance updated successfully.';
    } else {
        $message[] = 'Insufficient reservation! Please add more money to your account.';
    }
}

if (isset($_POST['search_car'])) {
    $search_query = $_POST['search_model'];

    $search_query = mysqli_real_escape_string($conn, $search_query); 

    $search_condition = "model LIKE '%$search_query%' OR plate_id LIKE '%$search_query%' OR color LIKE '%$search_query%' OR price LIKE '%$search_query%' ";

    $search_query = "SELECT * FROM `car` WHERE $search_condition";
    
    $result_search = mysqli_query($conn, $search_query) or die('Search query failed');

    if (mysqli_num_rows($result_search) > 0) {
        // Display search results
        while ($fetch_car = mysqli_fetch_assoc($result_search)) {
            echo '<script>window.location.hash = "#car_' . $fetch_car['car_id'] . '";</script>';
        }
    } else {
        echo '<div class="alert alert-danger text-center">Sorry, no results found for the provided model or plate ID.</div>';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/style1.css">
    <title>Car Rental</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100;0,200;0,300;0,400;0,500;1,200&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/brands.min.css"
        integrity="sha512-8RxmFOVaKQe/xtg6lbscU9DU0IRhURWEuiI0tXevv+lXbAHfkpamD4VKFQRto9WgfOJDwOZ74c/s9Yesv3VvIQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
</head>

<body>

<?php
if(isset($message)){
   foreach( $message as $msg){
      echo '<div class="message" onclick="this.remove();">'.$msg.'</div>';
   }
}
?>


    <header>
        <a href="#" class="logo"><img src="image\logo1.png" alt=""></a>

        <u1 class="navmenu">
            <li><a href="#">Home</a></li>
            <li><a href="customer.php">Account Info</a></li>
            <!-- <li><a href="#">Car</a></li> -->
        </u1>

        <div class="nav-icon">
           

     <div class="flexx">
    
      <a href="login.php" class="btn">login</a>
      <a href="register.php" class="option-btn">register</a>
      <a href="customer.php?logout=<?php echo $email; ?>" onclick="return confirm('are your sure you want to logout?');" class="delete-btn">logout</a>
   </div>

</div>

    <form method="post" class="search-form" action="">
        <label for="searchModel">Search Car :</label>
        <input type="text" id="searchModel" name="search_model" required>
        <input type="submit" value="Search" name="search_car" class="btn">
    </form>
            
        </div>
        
    </header>

    
    <section class="main-home">
        <div class="main-text">
            <h5>Mercides</h5>
            <h1>New cars <br> Collection 2024</h1>
            <p>There is nothing like get new car</p>

            <a href="#" class="main-btn">Lets's Start <i class='bx bx-right-arrow-alt'></i> </a>

        </div>
        <div class="down-arrow">
            <a href="#trending" class="down"><i class='bx bx-down-arrow-alt'></i></a>
        </div>
    </section>



    <!-- trending-cars-section -->
    <section class="trending-product" id="trending">
        <div class="center-text">
            <h2>Our Trending <span>cars</span> </h2>
        </div>
        <div class="products">

        <?php
            $select_car = mysqli_query($conn, "SELECT * FROM `car`") or die('query failed');
            if (mysqli_num_rows($select_car) > 0) {
                while ($fetch_car = mysqli_fetch_assoc($select_car)) {
            ?>
      <div class="row" id="car_<?php echo $fetch_car['car_id']; ?>">
      <form method="post" class="box" action="">
         <img src="image/<?php echo $fetch_car['image']; ?>" alt="">
         <div class="product-text">
                    <h5>New</h5>
                </div>
                <div class="heart-icon">
                    <i class='bx bx-heart' ></i>
                </div>
                <div class="ratting">
                    <i class='bx bx-star' ></i>
                    <i class='bx bx-star' ></i>
                    <i class='bx bx-star' ></i>
                    <i class='bx bx-star' ></i>
                    <i class='bx bxs-star-half' ></i>
                </div>
        <div class="info">       
         <div class="model"><?php echo $fetch_car['model']; ?></div>
         <div class="plate_id"><?php echo $fetch_car['plate_id']; ?></div>
         <div class="status"><?php echo $fetch_car['status']; ?></div>
         <div class="color"><?php echo $fetch_car['color']; ?></div>
         <div class="price">$<?php echo $fetch_car['price']; ?>/-</div>

         <div class="country-picker">
    <label for="country">Select Country:</label>
    <select id="country" name="selected_country" required>
        <option value="usa">United States</option>
        <option value="canada">Canada</option>
        <option value="uk">United Kingdom</option>
        <option value="egypt">Egypt</option>
      
    </select>
    <i class="bx bx-globe"></i>
</div>

         
<div class="date-picker">
    <label for="pickupDate">Pickup Date:</label>
    <input type="date" id="pickupDate" name="pickup_date" required>
    <i class="bx bx-calendar"></i>
</div>

<div class="date-picker">
    <label for="returnDate">Return Date:</label>
    <input type="date" id="returnDate" name="return_date" required>
    <i class="bx bx-calendar"></i>
</div>

<div class="rent-days-difference">
    <label for="rentDays">Rent Days:</label>
    <input type="text" id="rentDays" name="rent_days" readonly>
    <i class="bx bx-time"></i>
</div>



         <input type="hidden" name="car_image" value="<?php echo $fetch_car['image']; ?>">
         <input type="hidden" name="car_id" value="<?php echo $fetch_car['car_id']; ?>">
         <input type="hidden" name="car_plate_id" value="<?php echo $fetch_car['plate_id']; ?>">
         <input type="hidden" name="car_model" value="<?php echo $fetch_car['model']; ?>">
         <input type="hidden" name="car_price" value="<?php echo $fetch_car['price']; ?>">
         <input type="submit" value="rent" name="rent" class="btn">
         </div>
         </form>
      </div>
   <?php
      };
   };
   ?>
    </section>

    <script src="java.js"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.date-picker #pickupDate, .date-picker #returnDate').forEach(function (dateInput) {
            dateInput.addEventListener("change", updateRentDays);
        });
 
        function updateRentDays() {
            document.querySelectorAll('form').forEach(function (form) {
                const pickupDateInput = form.querySelector(".date-picker #pickupDate");
                const returnDateInput = form.querySelector(".date-picker #returnDate");
                const rentDaysInput = form.querySelector(".rent-days-difference #rentDays");
 
                if (pickupDateInput && returnDateInput && rentDaysInput) {
                    const pickupDate = new Date(pickupDateInput.value);
                    const returnDate = new Date(returnDateInput.value);
 
                    if (!isNaN(pickupDate.getTime()) && !isNaN(returnDate.getTime()) && returnDate >= pickupDate) {
                        const timeDiff = returnDate.getTime() - pickupDate.getTime();
                        const rentDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
 
                        rentDaysInput.value = rentDays;
                    } else {
                        rentDaysInput.value = 0; 
                    }
                }
            });
        }
    });
</script>

</body>

</html>
