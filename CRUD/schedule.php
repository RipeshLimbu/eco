<?php
include 'connect.php';

// Insert Data into Schedule Table
if (isset($_POST['submit'])) {
    $route = $_POST['route'];
    $day = $_POST['day'];
    $time = $_POST['time'];

    $sql = "INSERT INTO Schedule (route, day, time) 
            VALUES ('$route', '$day', '$time')";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        header("Location: display_schedule.php");
    } else {
        die("Error inserting data: " . mysqli_error($conn));
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
    <title>Add Schedule</title>
</head>
<body>
<div class="container mt-5">
    <h2>Add Schedule</h2>
    <form method="POST">
        <div class="form-group">
            <label for="route">Route</label>
            <input type="text" class="form-control" name="route" placeholder="Enter your Route" required>
        </div>
        <div class="form-group">
            <label for="day">Day</label>
            <select class="form-control" name="day" required>
                <option value="" disabled selected>-- Select Day --</option>
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
                <option value="Saturday">Saturday</option>
                <option value="Sunday">Sunday</option>
            </select>
        </div>
        <div class="form-group">
            <label for="time">Time</label>
            <input type="time" class="form-control" name="time" required>
        </div>
        <button type="submit" class="btn btn-primary" name="submit">Add Schedule</button>
    </form>
</div>
</body>
</html>
