<?php
require_once '../config/config.php';
session_start();

// Check if the user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "/login.php");
    exit();
}

include '../includes/header.php';
$servername = "localhost";
$username = "root"; // replace with your database username
$password = ""; // replace with your database password
$dbname = "waste_management"; // your database name
$port = "3306";

$conn = new mysqli($servername, $username, $password, $dbname, $port);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Dashboard - Waste Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg" style="background-color: lightgreen;">
        <div class="container">
            <a class="navbar-brand" href="#">
                <div class="eco-logo">
                    <i class="fas fa-leaf"></i>
                    <span>EcoManage</span>
                </div>
            </a>
            <div class="navbar-nav ml-auto">
            <a class="nav-link text-white" href="dashboard.php">
                <i class="fas fa-home"></i> Home
            </a>
                <a class="nav-link text-white" href="./manage_users.php">
                    <i class="fas fa-users"></i> Manage Users
                </a>
                <a class="nav-link text-white" href="./display.php">
                    <i class="fas fa-users"></i> View Message
                </a>
                <a class="nav-link text-white" href="./reports.php">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
                <span class="navbar-text text-white mx-3">
                    <i class="fas fa-user"></i> Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
                </span>
                <a class="nav-link text-white" href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
                <a class="nav-link text-white" href="../payment/payment.php">
                    <i class="fas fa-sign-out-alt"></i> pay
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4" style="background-color: #f9f9f9; border-radius: 5px; padding: 15px;">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Messages</h3>

                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Message</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * from contact_submissions";
                                $result = mysqli_query($conn, $sql);
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $id = $row['id'];
                                    $name = $row['full_name'];
                                    $email = $row['email'];
                                    $subject = $row['subject'];
                                    $message = $row['message'];
                                    echo '
                    <tr>
                    <th scope="row">' . $id . '</th>
                    <td>' . $name . '</td>
                    <td>' . $email . '</td>
                    <td>' . $subject . '</td>
                    <td>' . $message . '</td>';
                                }

                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>


</body>

</html>