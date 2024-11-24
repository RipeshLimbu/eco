<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "waste_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selected_waste = $_POST['waste_type'];
    $sql = "INSERT INTO waste
Sent 7m ago
Write to
