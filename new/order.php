<?php
$server = "localhost";
$username = "root";
$password = ""; 
$database = "ecodb";
$port = 3307;

$conn = mysqli_connect($server, $username, $password, $database, $port);

if ($conn) {
    echo "Connection established";
} else {
    echo "Connection failed" . mysqli_connect_error();
}


if (isset($_POST['add'])) {
    $ordernum = $_POST['ordernum'];
    $orderdate = $_POST['orderdate'];
    $descriptions = $_POST['descriptions'];
    $quantity = $_POST['quantity'];
    $quotedprice = $_POST['quotedprice'];

    $sql = "INSERT INTO `order` (ordernum, orderdate, descriptions, quantity, quotedprice) 
        VALUES ('$ordernum', '$orderdate', '$descriptions', '$quantity', '$quotedprice')";

    if (mysqli_query($conn, $sql)) {
        echo "Product added";
    } else {
        echo "Failed to add product: " . mysqli_error($conn);
    }
}
?>