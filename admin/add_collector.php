<?php
require_once '../config/config.php';
require_once '../config/db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../config/db_connect.php';

    $db = new Database();
    $conn = $db->connect();
    
    $username = $conn->real_escape_string($_POST['username']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $address = $conn->real_escape_string($_POST['address']);
    
    // Check if username or email exists
    $check_sql = "SELECT id FROM users WHERE username = ? OR email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $username, $email);
    $check_stmt->execute();
    
    if ($check_stmt->get_result()->num_rows > 0) {
        header("Location: manage_users.php?error=1");
        exit();
    }
    
    // Insert new collector
    $sql = "INSERT INTO users (username, password, email, role, full_name, phone, address) 
            VALUES (?, ?, ?, 'collector', ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $username, $password, $email, $full_name, $phone, $address);
    
    if ($stmt->execute()) {
        header("Location: manage_users.php?success=1");
    } else {
        header("Location: manage_users.php?error=1");
    }
    
    $conn->close();
}
?> 