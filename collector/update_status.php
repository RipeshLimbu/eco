<?php
require_once '../config/config.php';
require_once '../config/Database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'collector') {
    header("Location: " . BASE_URL . "/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . BASE_URL . "/collector/dashboard.php");
    exit();
}

try {
    $db = new Database();
    $conn = $db->connect();
    
    // Validate inputs
    $assignment_id = filter_input(INPUT_POST, 'assignment_id', FILTER_VALIDATE_INT);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
    $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING);
    
    if (!$assignment_id || !$status) {
        throw new Exception("Invalid input parameters");
    }

    // Start transaction
    $conn->begin_transaction();

    // Get the complaint_id and verify collector ownership
    $stmt = $conn->prepare("
        SELECT complaint_id 
        FROM assignments 
        WHERE id = ? AND collector_id = ? 
        LIMIT 1
    ");
    
    $collector_id = $_SESSION['user_id'];
    $stmt->bind_param("ii", $assignment_id, $collector_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Assignment not found or unauthorized");
    }

    $complaint_id = $result->fetch_object()->complaint_id;

    // Update assignment status
    $stmt = $conn->prepare("
        UPDATE assignments 
        SET status = ?, 
            notes = ?,
            updated_at = CURRENT_TIMESTAMP,
            completed_at = CASE WHEN ? = 'completed' THEN CURRENT_TIMESTAMP ELSE NULL END
        WHERE id = ? AND collector_id = ?
    ");
    
    $stmt->bind_param("sssii", $status, $notes, $status, $assignment_id, $collector_id);
    if (!$stmt->execute()) {
        throw new Exception("Failed to update assignment: " . $stmt->error);
    }

    // Update complaint status
    $stmt = $conn->prepare("
        UPDATE complaints 
        SET status = ?,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = ?
    ");
    
    $complaint_status = $status;
    $stmt->bind_param("si", $complaint_status, $complaint_id);
    if (!$stmt->execute()) {
        throw new Exception("Failed to update complaint: " . $stmt->error);
    }

    $conn->commit();
    
    // Log successful update
    error_log("Status updated successfully - Assignment ID: $assignment_id, New Status: $status");
    
    header("Location: " . BASE_URL . "/collector/dashboard.php?success=1");
    exit();

} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollback();
    }
    error_log("Status Update Error: " . $e->getMessage());
    header("Location: " . BASE_URL . "/collector/dashboard.php?error=" . urlencode($e->getMessage()));
    exit();
}
?> 