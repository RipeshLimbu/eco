<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/db_connect.php';

$db = new Database();
$conn = $db->connect();

// Fetch all users except current admin
$sql = "SELECT * FROM users WHERE id != ? ORDER BY role, created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$users = $stmt->get_result();

// Define messages
$success_message = isset($_GET['success']) ? "Operation completed successfully" : "";
$error_message = isset($_GET['error']) ? "Operation failed" : "";

// include '../includes/header.php';

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users - Admin Dashboard</title>
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
                <a class="nav-link text-white" href="manage_users.php">
                    <i class="fas fa-users"></i> Manage Users
                </a>
                <a class="nav-link text-white" href="../admin/display.php">
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
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $success_message; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo $error_message; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Manage Users</h3>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCollectorModal">
                    <i class="fas fa-plus"></i> Add Collector
                </button>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Full Name</th>
                            <th>Phone</th>
                            <th>Created At</th>
                            <th>Actions</th>
                            <th>Payment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="badge badge-<?php 
                                    echo match($user['role']) {
                                        'admin' => 'danger',
                                        'collector' => 'info',
                                        'user' => 'success'
                                    };
                                ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['phone']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                            <td>
                                <?php if ($user['role'] !== 'admin'): ?>
                                <button class="btn btn-sm btn-danger delete-user" data-id="<?php echo $user['id']; ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                            <td>
                            <form action="../payment/payment.php" method="GET">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-credit-card"></i> Pay
                            </button>
                            </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Collector Modal -->
    <div class="modal fade" id="addCollectorModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Collector</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="addCollectorForm" action="add_collector.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Username</label>
                            <input type="text" class="form-control" name="username" required 
                                   pattern="[a-zA-Z0-9_]{3,}" title="Username must be at least 3 characters long and can only contain letters, numbers and underscore">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> Password</label>
                            <input type="password" class="form-control" name="password" required 
                                   minlength="6" title="Password must be at least 6 characters long">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-user-circle"></i> Full Name</label>
                            <input type="text" class="form-control" name="full_name" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> Phone</label>
                            <input type="tel" class="form-control" name="phone" required 
                                   pattern="[0-9]{10,}" title="Please enter a valid phone number">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-map-marker-alt"></i> Address</label>
                            <textarea class="form-control" name="address" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Collector
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function() {
        // Form validation
        $('#addCollectorForm').submit(function(e) {
            var password = $('input[name="password"]').val();
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long');
                return false;
            }
            
            var email = $('input[name="email"]').val();
            if (!email.includes('@')) {
                e.preventDefault();
                alert('Please enter a valid email address');
                return false;
            }
            
            var phone = $('input[name="phone"]').val();
            if (phone.length < 10) {
                e.preventDefault();
                alert('Please enter a valid phone number');
                return false;
            }
        });

        // Show success/error messages
        <?php if (isset($_GET['success']) || isset($_GET['error'])): ?>
            setTimeout(function() {
                $('.alert').alert('close');
            }, 3000);
        <?php endif; ?>
    });
    </script>
</body>
</html> 