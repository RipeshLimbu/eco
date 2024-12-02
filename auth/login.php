<?php
$server = "localhost";
$username = "root";
$password = ""; 
$database = "ecodb";
$port = 3307;
// Database connection
$conn = mysqli_connect($server, $username, $password, $database, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Waste Management System</title>
    <link rel="stylesheet" href="../assets/style/login.css">
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
      
        <form action="" method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>