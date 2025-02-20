<?php
// session_start();

// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
//     header("Location: ../login.php");
//     exit();
// }

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $conn = new mysqli('localhost', 'root', '', 'waste_management',3306);
    
//     $user_id = $_SESSION['user_id'];
//     $title = $conn->real_escape_string($_POST['title']);
//     $location = $conn->real_escape_string($_POST['location']);
//     $description = $conn->real_escape_string($_POST['description']);
    
//     $sql = "INSERT INTO complaints (user_id, title, location, description) VALUES (?, ?, ?, ?)";
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("isss", $user_id, $title, $location, $description);
    
//     if ($stmt->execute()) {
//         header("Location: dashboard.php?success=1");
//     } else {
//         header("Location: dashboard.php?error=1");
//     }
    
//     $conn->close();
// }




session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Connect to the database
    $conn = new mysqli('localhost', 'root', '', 'waste_management', 3306);
    
    $user_id = $_SESSION['user_id'];
    $title = $conn->real_escape_string($_POST['title']);
    $location = $conn->real_escape_string($_POST['location']);
    $description = $conn->real_escape_string($_POST['description']);
    
    // Handle the photo upload if there is one
    $photo_path = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        // Set the target directory for photo uploads
        $upload_dir = '../uploads/complaints_photos/';
        // Ensure the directory exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Get the uploaded file's name and extension
        $file_name = basename($_FILES['photo']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Generate a unique file name to avoid conflicts
        $new_file_name = uniqid('complaint_') . '.' . $file_ext;

        // Set the target file path
        $target_file = $upload_dir . $new_file_name;

        // Validate file type (you can extend this list if necessary)
        $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($file_ext, $valid_extensions)) {
            // Move the file to the target directory
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                $photo_path = $conn->real_escape_string($target_file);
            }
        } else {
            // Handle invalid file type
            echo 'Invalid file type. Only JPG, JPEG, PNG, GIF allowed.';
            exit;
        }
    }

    // Insert the complaint into the database
    $sql = "INSERT INTO complaints (user_id, title, location, description, photo) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $user_id, $title, $location, $description, $photo_path);
    
    if ($stmt->execute()) {
        header("Location: dashboard.php?success=1");
    } else {
        header("Location: dashboard.php?error=1");
    }
    
    $conn->close();
}
?>
