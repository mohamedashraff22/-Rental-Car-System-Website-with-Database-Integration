<?php
include 'config.php';
session_start();


if (!isset($_SESSION['email']) || $_SESSION['email']!='admin@admin.com') {
    header('location: login.php');    
    exit(); 
}


function generateUniqueFilename($originalFilename)
{
    $extension = pathinfo($originalFilename, PATHINFO_EXTENSION);
    $uniqueFilename = uniqid() . '.' . $extension;
    return $uniqueFilename;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_car'])) {
    $model = $_POST['model'];
    $color = $_POST['color'];
    $year = $_POST['year'];
    $price = $_POST['price'];
    $status = isset($_POST['status']) ? 'Available' : 'Rented';
    $plateId = $_POST['plate_id'];

    $imageFilename = $_FILES['image']['name'];
    $imageTempPath = $_FILES['image']['tmp_name'];
    $imageUniqueFilename = generateUniqueFilename($imageFilename);

    $uploadDirectory = __DIR__ . '/uploads/';

    if (!file_exists($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
    }

    $imageUploadPath = $uploadDirectory . $imageUniqueFilename;

    if (move_uploaded_file($imageTempPath, $imageUploadPath)) {
        $insertQuery = "INSERT INTO `car` (model, year, price, status, plate_id, color ,image) VALUES ('$model', '$year', '$price', '$status', '$plateId', '$color' ,'$imageUniqueFilename')";
        if (mysqli_query($conn, $insertQuery)) {
            $message = "Car added successfully!";
        } else {
            $error = "Error adding car: " . mysqli_error($conn);
        }
    } else {
        $error = "Error uploading image.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $carId = $_POST['car_id'];
    $newStatus = $_POST['new_status'];

    $updateQuery = "UPDATE `car` SET status = '$newStatus' WHERE car_id = '$carId'";
    if (mysqli_query($conn, $updateQuery)) {
        $messageUpdateStatus = "Car status updated successfully!";
    } else {
        $errorUpdateStatus = "Error updating car status: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-GLhlTQ8iK3u5bLzor++U5R/6vYUJbX+qDScd42Y5uZl5I5uB/6B6a5F+JckW1/0p" crossorigin="anonymous">
    <link rel="stylesheet" href="css/admin_style.css">
    <title>Manage Cars</title>
</head>

<body>

    <header>
        <h1>Welcome, Admin!</h1>
        <a href="admin_dashboard.php">Go to Admin Dashboard</a>
    </header>

    <div class="wrapper">
        <div id="content">
            <h2>Add or Update Car Status</h2>

            <?php
            if (isset($message)) {
                echo '<div class="alert alert-success" role="alert">' . $message . '</div>';
            } elseif (isset($error)) {
                echo '<div class="alert alert-danger" role="alert">' . $error . '</div>';
            }

            if (isset($messageUpdateStatus)) {
                echo '<div class="alert alert-success" role="alert">' . $messageUpdateStatus . '</div>';
            } elseif (isset($errorUpdateStatus)) {
                echo '<div class="alert alert-danger" role="alert">' . $errorUpdateStatus . '</div>';
            }
            ?>

            
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="model">Car Model:</label>
                    <input type="text" class="form-control" id="model" name="model" required>
                </div>

                <div class="form-group">
                    <label for="model">Car Color:</label>
                    <input type="text" class="form-control" id="color" name="color" required>
                </div>

                <div class="form-group">
                    <label for="year">Car Year:</label>
                    <input type="number" class="form-control" id="year" name="year" required>
                </div>
                <div class="form-group">
                    <label for="price">Car Price:</label>
                    <input type="number" class="form-control" id="price" name="price" required>
                </div>
                
                <div class="form-group">
                    <label for="plate_id">Plate ID:</label>
                    <input type="text" class="form-control" id="plate_id" name="plate_id" required>
                </div>

                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="status" name="status">
                    <label class="form-check-label" for="status">Available for Rent</label>
                </div>


                <div class="form-group">
                    <label for="image">Car Image:</label>
                    <input type="file" class="form-control-file" id="image" name="image" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-primary" name="add_car">Add Car</button>
            </form>

            <hr>

            
            <h3>Update Car Status</h3>
            <form method="post">
                <div class="form-group">
                    <label for="car_id_update">Select Car to Update:</label>
                    <select class="form-control" id="car_id_update" name="car_id" required>
                        <?php
                        $selectQuery = "SELECT * FROM `car`";
                        $result = mysqli_query($conn, $selectQuery);
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo '<option value="' . $row['car_id'] . '">' . $row['model'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="new_status">New Status:</label>
                    <select class="form-control" id="new_status" name="new_status" required>
                        <option value="Available">Available</option>
                        <option value="Rented">Rented</option>
                        <option value="Out of Service">Out of Service</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" name="update_status">Update Status</button>
            </form>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>

</body>

</html>
