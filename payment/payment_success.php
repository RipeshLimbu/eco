<?php
require '../config/config.php'; // Ensure this file contains the connection to the database
session_start();

// Check if the user is logged in (optional, but recommended)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Get parameters from the URL
$transaction_id = $_GET['transaction_id'];
$amount = $_GET['amount'];
$total_amount = $_GET['total_amount'];
$mobile = $_GET['mobile'];
$status = $_GET['status'];
$purchase_order_id = $_GET['purchase_order_id'];
$purchase_order_name = $_GET['purchase_order_name'];

// Ensure you have a valid database connection in config.php
if (isset($conn)) {
    // Insert payment details into the database
    $query = "INSERT INTO payments (transaction_id, amount, total_amount, mobile, status, purchase_order_id) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssss", $transaction_id, $amount, $total_amount, $mobile, $status, $purchase_order_id);

    if ($stmt->execute()) {
        // echo "Payment stored successfully!";
    } else {
        echo "Error storing payment: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Database connection failed.";
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Successful</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .success-icon {
            color: #28a745; /* Green */
            font-size: 4rem;
            margin-bottom: 20px;
        }

        h2 {
            color: #343a40;
        }

        p {
            color: #6c757d;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0069d9;
            border-color: #0062cc;
        }
    </style>
</head>
<body>

<div class="container text-center">
    <div class="success-icon">
        <i class="fas fa-check-circle"></i>  
    </div>
    <h2>Payment Successful!</h2>
    <p>Thank you for your payment. Your transaction details are below:</p>
    <p><strong>Transaction ID:</strong> <?php echo $transaction_id; ?></p>
    <p><strong>Amount:</strong> <?php echo $amount; ?></p>
    <p>You will receive a confirmation email shortly.</p>
    <a href="../admin/dashboard.php" class="btn btn-primary mt-3">Go to Dashboard</a> 
</div>

<script src="https://kit.fontawesome.com/your-font-awesome-kit.js" crossorigin="anonymous"></script> 
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js"></script>

</body>
</html>
