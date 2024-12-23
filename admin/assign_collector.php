<?php
require_once '../config/config.php';
require_once '../config/db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $conn = $db->connect();
    
    $complaint_id = (int)$_POST['complaint_id'];
    $collector_id = (int)$_POST['collector_id'];
    $admin_id = $_SESSION['user_id'];
    $notes = $conn->real_escape_string($_POST['notes']);
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update complaint status
        $sql = "UPDATE complaints SET status = 'assigned' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $complaint_id);
        $stmt->execute();
        
        // Create assignment
        $sql = "INSERT INTO assignments (complaint_id, collector_id, assigned_by, notes, status) 
                VALUES (?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiis", $complaint_id, $collector_id, $admin_id, $notes);
        $stmt->execute();
        
        $conn->commit();
        header("Location: " . BASE_URL . "/admin/dashboard.php?success=1");
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: " . BASE_URL . "/admin/dashboard.php?error=1");
    }
    
    $conn->close();
}
?> 