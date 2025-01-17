<?php
require_once '../config/config.php';
require_once '../config/db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: " . BASE_URL . "/login.php");
    exit();
}

if (isset($_POST['id'])) {
    $notification_id = $_POST['id'];

    $db = new Database();
    $conn = $db->connect();

    // Update the notification status to 'read'
    $sql = "UPDATE notifications SET status = 'read' WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $notification_id, $_SESSION['user_id']);
    $stmt->execute();

    $stmt->close();
    $conn->close();
}
?>
