<?php
include 'config.php';
session_start();
$email = $_SESSION['email'];

if (!isset($_SESSION['email']) || $_SESSION['email']!='admin@admin.com') {
    header('location: login.php');  
}

function getCustomerReservations($conn, $customer_id)
{
    $query = "SELECT * FROM `reservation` WHERE customer_id = '$customer_id'";
    $result = mysqli_query($conn, $query) or die('Query failed: ' . mysqli_error($conn));
    return $result;
}

function getDailyPayments($conn, $search_date)
{
    $query = "SELECT DATE(payment_date) AS payment_date, SUM(payment_amount) AS total_payment FROM `reservation` WHERE DATE(payment_date) = '$search_date' GROUP BY DATE(payment_date)";
    $result = mysqli_query($conn, $query) or die('Query failed: ' . mysqli_error($conn));
    return $result;
}

if (isset($_POST['submit'])) {
    $customer_id = mysqli_real_escape_string($conn, $_POST['customer_id']);
    $reservations = getCustomerReservations($conn, $customer_id);
}

if (isset($_POST['submit_date'])) {
    $search_date = mysqli_real_escape_string($conn, $_POST['search_date']);
    $dailyPaymentsQuery = getDailyPayments($conn, $search_date);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/admin_style.css">
    <title>Admin Dashboard</title>
</head>

<body>

    <div class="container-fluid">
        <div class="row">

            <!-- Sidebar -->
            <nav class="col-md-2 d-none d-md-block bg-light sidebar">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">

                    <a href="add_car.php" class="btn">Go to Add Car</a>
                    

                        <li class="nav-item">
                            <button class="nav-link btn btn-link" onclick="toggleVisibility('customer_section')">Customers</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link btn btn-link" onclick="toggleVisibility('car_section')">Cars</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link btn btn-link" onclick="toggleVisibility('reservation_section')">Reservations</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link btn btn-link" onclick="toggleVisibility('payment_section')">Payments</button>    
                            <button class="btn btn-primary" onclick="toggleAllSections()">Toggle Reports</button>

                            <a href="login.php" class="btn">Go to Login</a>
                    </ul>
                </div>
            </nav>
            
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">


                <div id="customer_section" class="query-section" style="display: none;">
                    <h2>All Customers</h2>
                    <?php
                    $customerQuery = mysqli_query($conn, "SELECT * FROM `customer`") or die('Customer query failed');

                    if (mysqli_num_rows($customerQuery) > 0) {
                        echo '<ul>';
                        while ($customerData = mysqli_fetch_assoc($customerQuery)) {
                            echo '<li>';
                            echo '<strong>Name:</strong> ' . $customerData['fname'] . ' ' . $customerData['lname'] . '<br>';
                            echo '<strong>Email:</strong> ' . $customerData['email'] . '<br>';
                            echo '<strong>Phone:</strong> ' . $customerData['phone'] . '<br>';
                            echo '<strong>Country:</strong> ' . $customerData['country'] . '<br>';
                            echo '<strong>Balance:</strong> $' . $customerData['balance'] . '<br>';
                            echo '</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<p>No customers found.</p>';
                    }
                    ?>
                </div>

                <div id="car_section" class="query-section" style="display: none;">
                    <h2>All Cars</h2>
                    <?php
                    $carQuery = mysqli_query($conn, "SELECT * FROM `car`") or die('Car query failed');
                    if (mysqli_num_rows($carQuery) > 0) {
                        echo '<ul>';
                        while ($carData = mysqli_fetch_assoc($carQuery)) {
                            echo '<li>';
                            echo '<strong>Model:</strong> ' . $carData['model'] . '<br>';
                            echo '<strong>Price:</strong> $' . $carData['price'] . '<br>';
                            echo '<strong>Status:</strong> ' . $carData['status']  . '<br>';
                            echo '</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<p>No cars found.</p>';
                    }
                    ?>
                </div>

                <div id="reservation_section" class="query-section" style="display: none;">
                    <h2>All Reservations</h2>
                    <?php
                    $reservationQuery = mysqli_query($conn, "SELECT * FROM `reservation`  JOIN `car` ON car.car_id=reservation.car_id JOIN customer ON customer.customer_id=reservation.customer_id") or die('Reservation query failed');
                    if (mysqli_num_rows($reservationQuery) > 0) {
                        echo '<ul>';
                        while ($reservationData = mysqli_fetch_assoc($reservationQuery)) {
                            echo '<li>';
                            echo '<strong>Name:</strong> ' . $reservationData['fname'] . ' ' . $reservationData['lname'] . '<br>'; 
                            echo '<strong>Country:</strong> ' . $reservationData['country']  . '<br>';
                            echo '<strong>Model:</strong> ' . $reservationData['model'] . '<br>';
                            echo '<strong>Price:</strong> $' . $reservationData['price'] . '<br>';
                            echo '<strong>Payment Amount:</strong> ' . $reservationData['payment_amount'] . '<br>';
                            echo '<strong>PlateID:</strong> ' . $reservationData['plate_id']  . '<br>';
                            echo '<strong>Car Year:</strong> ' . $reservationData['year']  . '<br>';
                            echo '</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<p>No cars found.</p>';
                    }
                    ?>
                </div>

                <div id="payment_section" class="query-section" style="display: none;">
                    <h2>All Payments</h2>
                    <?php
                    $reservationQuery = mysqli_query($conn, "SELECT * FROM `reservation`   JOIN customer ON customer.customer_id=reservation.customer_id") or die('Reservation query failed');
                    if (mysqli_num_rows($reservationQuery) > 0) {
                        echo '<ul>';
                        while ($reservationData = mysqli_fetch_assoc($reservationQuery)) {
                            echo '<li>';
                            echo '<strong>Name:</strong> ' . $reservationData['fname'] . ' ' . $reservationData['lname'] . '<br>';     
                            echo '<strong>Payment Amount:</strong> ' . $reservationData['payment_amount'] . '<br>';
                            echo '<strong>Date:</strong>' . $reservationData['payment_date'] . '<br>';
                            echo '</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<p>No cars found.</p>';
                    }
                    ?>
                </div>
                
            
                <div id="reservations_report" class="query-section" style="display: block;">
                    <h2>Reservations report</h2>

    <!-- Form to input start and end dates -->
    <form method="post">
        <label for="start_date">Enter Start Date:</label>
        <input type="date" name="start_date" id="start_date" required>
        <label for="end_date">Enter End Date:</label>
        <input type="date" name="end_date" id="end_date" required>
        <input type="submit" name="submit_date_range" value="Search Period">
    </form>

    <?php
    if (isset($_POST['submit_date_range'])) {
        $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
        $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);

        if (strtotime($start_date) === false || strtotime($end_date) === false) {
            echo '<p>Invalid date format. Please enter dates in YYYY-MM-DD format.</p>';
        } else {
            $reservationsQuery = "SELECT * FROM `reservation` 
                                 JOIN `car` ON car.car_id=reservation.car_id 
                                 JOIN customer ON customer.customer_id=reservation.customer_id
                                 WHERE pickup_date BETWEEN '$start_date' AND '$end_date'";
            $reservationsResult = mysqli_query($conn, $reservationsQuery) or die('Reservation query failed');

            if (mysqli_num_rows($reservationsResult) > 0) {
                echo '<table class="table">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Name</th>';
                echo '<th>Country</th>';
                echo '<th>Model</th>';
                echo '<th>Price</th>';
                echo '<th>Payment Amount</th>';
                echo '<th>PlateID</th>';
                echo '<th>Car Year</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
            
                while ($reservationData = mysqli_fetch_assoc($reservationsResult)) {
                    echo '<tr>';
                    echo '<td>' . $reservationData['fname'] . ' ' . $reservationData['lname'] . '</td>';
                    echo '<td>' . $reservationData['country'] . '</td>';
                    echo '<td>' . $reservationData['model'] . '</td>';
                    echo '<td>$' . $reservationData['price'] . '</td>';
                    echo '<td>' . $reservationData['payment_amount'] . '</td>';
                    echo '<td>' . $reservationData['plate_id'] . '</td>';
                    echo '<td>' . $reservationData['year'] . '</td>';
                    echo '</tr>';
                }
            
                echo '</tbody>';
                echo '</table>';
            } else {
                echo '<p>No reservations found for the selected date range.</p>';
            }
        }
    }
    ?>

</div>

<div id="cars_status_report" class="query-section" style="display: block;">
    <h2>Cars Status Report</h2>

    <form method="post">
        <label for="search_date_cars_status">Enter Date:</label>
        <input type="date" name="search_date_cars_status" id="search_date_cars_status" required>
        <input type="submit" name="submit_date_cars_status" value="Search Car Status">
    </form>

    <?php
    if (isset($_POST['submit_date_cars_status'])) {
        $search_date_cars_status = mysqli_real_escape_string($conn, $_POST['search_date_cars_status']);

        // Validate date format
        if (strtotime($search_date_cars_status) === false) {
            echo '<p>Invalid date format. Please enter a date in YYYY-MM-DD format.</p>';
        } else {
            $carsStatusQuery = "SELECT * FROM `car` 
                                LEFT JOIN `reservation` ON car.car_id = reservation.car_id";

            $carsStatusResult = mysqli_query($conn, $carsStatusQuery) or die('Cars status query failed');

            if (mysqli_num_rows($carsStatusResult) > 0) {
                echo '<table border="1">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Model</th>';
                echo '<th>Status</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
            
                while ($carStatusData = mysqli_fetch_assoc($carsStatusResult)) {
                    echo '<tr>';
                    echo '<td>' . $carStatusData['model'] . '</td>';
                    
                    $currentDate = date('Y-m-d'); 
                    $pickupDate = $carStatusData['pickup_date'];
                    $returnDate = $carStatusData['return_date'];
                
                    if ($pickupDate <= $search_date_cars_status && $search_date_cars_status<= $returnDate) {
                        echo '<td>Rented</td>';
                    } else {
                        echo '<td>Available</td>';
                    }
                
                    echo '</tr>';
                }
            
                echo '</tbody>';
                echo '</table>';
            } else {
                echo '<p>No cars found for the specified date.</p>';
            }
        }
    }
    ?>
</div>
                <div id="customer_reservations_report" class="query-section" style="display:block;">
                    <h2>Customer Reservations Report</h2>
                    <form method="post">
                        <label for="customer_id">Enter Customer ID:</label>
                        <input type="text" name="customer_id" id="customer_id" required>
                        <input type="submit" name="submit" value="Retrieve Reservations">
                    </form>
                    <?php
                if (isset($reservations) && mysqli_num_rows($reservations) > 0) {
                    echo '<table class="table">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>Car ID</th>';
                    echo '<th>Payment Amount</th>';
                    echo '<th>Pickup Date</th>';
                    echo '<th>Return Date</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';
                
                    while ($reservationData = mysqli_fetch_assoc($reservations)) {
                        echo '<tr>';
                        echo '<td>' . $reservationData['car_id'] . '</td>';
                        echo '<td>$' . $reservationData['payment_amount'] . '</td>';
                        echo '<td>' . $reservationData['pickup_date'] . '</td>';
                        echo '<td>' . $reservationData['return_date'] . '</td>';
                        echo '</tr>';
                    }
                
                    echo '</tbody>';
                    echo '</table>';
                } else {
                    echo '<p>No reservations found for the customer.</p>';
                }
                    ?>
                </div>

<div id="daily_payments_report" class="query-section" style="display: block;">
    <h2>Daily Payments Report</h2>

    <form method="post">
        <label for="start_date_payments">Enter Start Date:</label>
        <input type="date" name="start_date_payments" id="start_date_payments" required>
        <label for="end_date_payments">Enter End Date:</label>
        <input type="date" name="end_date_payments" id="end_date_payments" required>
        <input type="submit" name="submit_date_range_payments" value="Search Period">
    </form>

    <?php
    if (isset($_POST['submit_date_range_payments'])) {
        $start_date_payments = mysqli_real_escape_string($conn, $_POST['start_date_payments']);
        $end_date_payments = mysqli_real_escape_string($conn, $_POST['end_date_payments']);

        if (strtotime($start_date_payments) === false || strtotime($end_date_payments) === false) {
            echo '<p>Invalid date format. Please enter dates in YYYY-MM-DD format.</p>';
        } else {
            $dailyPaymentsQuery = "SELECT DATE(payment_date) AS payment_date, SUM(payment_amount) AS total_payment 
                                FROM `reservation` 
                                WHERE DATE(payment_date) BETWEEN '$start_date_payments' AND '$end_date_payments' 
                                GROUP BY DATE(payment_date)";
            $dailyPaymentsResult = mysqli_query($conn, $dailyPaymentsQuery) or die('Daily payments query failed');

            if (mysqli_num_rows($dailyPaymentsResult) > 0) {
                echo '<ul>';
                while ($dailyPaymentData = mysqli_fetch_assoc($dailyPaymentsResult)) {
                    echo '<li>';
                    echo '<strong>Date:</strong> ' . $dailyPaymentData['payment_date'] . '<br>';
                    echo '<strong>Total Payment:</strong> $' . $dailyPaymentData['total_payment'] . '<br>';
                    echo '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p>No daily payments found for the selected date range.</p>';
            }
        }
    }
    ?>
</div>

            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>

    <script>
        function toggleVisibility(sectionId) {
            var allSections = document.getElementsByClassName("query-section");
            for (var i = 0; i < allSections.length; i++) {
                allSections[i].style.display = "none";
            }

            var section = document.getElementById(sectionId);
            section.style.display = "block";
        }
    </script>

<script>
    function toggleAllSections() {
        var allSections = document.getElementsByClassName("query-section");
        for (var i = 0; i < allSections.length; i++) {
            allSections[i].style.display = "none";
        }

        document.getElementById("reservations_report").style.display = "block";
        document.getElementById("cars_status_report").style.display = "block";
        document.getElementById("customer_reservations_report").style.display = "block";
        document.getElementById("daily_payments_report").style.display = "block";
    }

</script>

</body>

</html>
