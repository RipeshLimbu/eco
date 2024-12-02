<?php
// require 'includes/config.php';
include '../includes/config.php';

session_start();
// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    // Redirect to appropriate dashboard based on user role
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin_dash.php");
    } else {
        header("Location: user_dashboard.php"); // You'll need to create this file for non-admin users
    }
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT id, name, email, role, password FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on user role
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php"); // You'll need to create this file for non-admin users
            }
            exit();
        } else {
            $error = "Invalid email or password";
        }
    } else {
        $error = "Invalid email or password";
    }
}

$conn->close();

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