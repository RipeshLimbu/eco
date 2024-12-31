<?php
session_start();
if (!isset($_SESSION['collector_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

include 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assignment_id = intval($_POST['assignment_id']);
    $status = htmlspecialchars($_POST['status']);
    $notes = htmlspecialchars($_POST['notes']);
    $collector_id = $_SESSION['collector_id'];

    // Check if the assignment belongs to the logged-in collector
    $checkQuery = $conn->prepare("SELECT * FROM assignments WHERE assignment_id = ? AND collector_id = ?");
    $checkQuery->bind_param("ii", $assignment_id, $collector_id);
    $checkQuery->execute();
    $assignment = $checkQuery->get_result()->fetch_assoc();

    if (!$assignment) {
        echo json_encode(['success' => false, 'message' => 'Invalid assignment']);
        exit;
    }

    // Update the status and notes
    $updateQuery = $conn->prepare("UPDATE assignments SET status = ?, notes = ? WHERE assignment_id = ?");
    $updateQuery->bind_param("ssi", $status, $notes, $assignment_id);

    if ($updateQuery->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
