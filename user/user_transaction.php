<?php
require_once '../config/config.php';
require_once '../config/db_connect.php';
session_start();

// Check if the user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: " . BASE_URL . "/login.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

// Fetch all payment transactions
$sql = "SELECT * FROM payments ORDER BY created_at DESC";
$transactions = $conn->query($sql);

include '../includes/header.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transactions - Waste Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="./dashboard.php">
            <div class="eco-logo">
                <i class="fas fa-leaf"></i>
                <span>EcoManage</span>
            </div>
        </a>
        <div class="navbar-nav ml-auto">
            <span class="navbar-text text-white mr-3">
                <i class="fas fa-user"></i> Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>
            </span>
            <a class="nav-link text-white" href="./user_transaction.php">
            <i class="fas fa-money-check-alt"></i> Transactions
            </a>
            
            <a class="nav-link text-white" href="../payment/payment.php">
                <i class="fas fa-sign-out-alt"></i> pay
            </a>
            <a class="nav-link text-white" href="../logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</nav>

    <div class="container mt-4" style="background-color: #f9f9f9; border-radius: 5px; padding: 15px;">
        <h3>Transaction Records</h3>

        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>ID</th>
                    <th>Transaction ID</th>
                    <th>Amount</th>
                    <th>Total Amount</th>
                    <th>Mobile</th>
                    <th>Status</th>
                    <th>Order ID</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $transactions->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['transaction_id']); ?></td>
                    <td><?php echo number_format($row['amount'], 2); ?></td>
                    <td><?php echo number_format($row['total_amount'], 2); ?></td>
                    <td><?php echo htmlspecialchars($row['mobile']); ?></td>
                    <td>
                    <?php 
                        $status = strtolower(trim($row['status'])); // Normalize status
                        $statusClass = match ($status) {
                            'paid' => 'success',
                            'due' => 'warning',
                            default => 'secondary',
                        };
                    ?>
                    <span class="badge badge-<?php echo $statusClass; ?>">
                        <?php echo ucfirst($status); ?>
                    </span>
                </td>

                    <td><?php echo htmlspecialchars($row['purchase_order_id']); ?></td>
                    <td><?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js"></script>
</body>
</html>
