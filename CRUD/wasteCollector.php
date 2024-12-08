<?php
include 'connect.php';

// Insert Data into Waste_Collector Table
if (isset($_POST['submit'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT); // Hashing the password securely

    // Validate input
    if (empty($name) || empty($email) || empty($password)) {
        $error = "Name, Email, and Password are required fields!";
    } else {
        // Insert query
        $sql = "INSERT INTO Waste_Collector (name, email, contact, password) 
                VALUES ('$name', '$email', '$contact', '$password')";

        if (mysqli_query($conn, $sql)) {
            $success = "Waste Collector added successfully!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
    <title>Add Waste Collector</title>
</head>
<body>
<div class="container mt-5">
    <h2>Add Waste Collector</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" name="name" placeholder="Enter Name" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" name="email" placeholder="Enter Email" required>
        </div>
        <div class="form-group">
            <label for="contact">Contact</label>
            <input type="text" class="form-control" name="contact" placeholder="Enter Contact Number">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" name="password" placeholder="Enter Password" required>
        </div>
        <button type="submit" class="btn btn-primary" name="submit">Add Collector</button>
    </form>
</div>
</body>
</html>
