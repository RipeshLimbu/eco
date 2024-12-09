<?php
// session_start();

// Check if the user is logged in and is an admin
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
//     header("Location: ");
//     exit();
// }
include '../includes/config.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_user':
                $name = $conn->real_escape_string($_POST['name']);
                $email = $conn->real_escape_string($_POST['email']);
                $role = $conn->real_escape_string($_POST['role']);
                $password = $conn->real_escape_string($_POST['password']); 
                
                $sql = "INSERT INTO users (name, email, role, password) VALUES ('$name', '$email', '$role', '$password')";
                $conn->query($sql);
                break;
            case 'add_schedule':
                $route = $conn->real_escape_string($_POST['route']);
                $day = $conn->real_escape_string($_POST['day']);
                $time = $conn->real_escape_string($_POST['time']);
                
                $sql = "INSERT INTO schedules (route, day, time) VALUES ('$route', '$day', '$time')";
                $conn->query($sql);
                break;
            case 'add_category':
                $name = $conn->real_escape_string($_POST['name']);
                $description = $conn->real_escape_string($_POST['description']);
                
                $sql = "INSERT INTO waste_categories (name, description) VALUES ('$name', '$description')";
                $conn->query($sql);
                break;
        }
    }
}

// Fetch data from database
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
$schedules = $conn->query("SELECT * FROM schedules ORDER BY created_at DESC");
$waste_categories = $conn->query("SELECT * FROM waste_categories ORDER BY created_at DESC");
$reports = $conn->query("SELECT * FROM reports ORDER BY created_at DESC");

?>



<?php
$conn->close();
?>