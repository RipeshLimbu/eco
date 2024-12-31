<?php
require_once '../config/config.php';
require_once '../config/db_connect.php';

if (isset($_GET['id'])) {
    $complaint_id = (int)$_GET['id'];

    // Fetch the complaint details from the database
    $sql = "SELECT * FROM complaints WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $complaint_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $complaint = $result->fetch_assoc();

        // Return the data as a JSON response
        echo json_encode($complaint);
    } else {
        echo json_encode(['error' => 'Complaint not found']);
    }
} else {
    echo json_encode(['error' => 'Invalid complaint ID']);
}

$conn->close();
?>
