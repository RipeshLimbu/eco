<?php
// @include('order.php')
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
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Order</title>
</head>

<body>
    <!-- Form to add a new order -->
    <form method="post" action="">
        <div id="add_order" class="content-section">
            <h1>Add Order</h1>

            <label for="ordernum">Order Number:</label>
            <input type="text" id="ordernum" name="ordernum" required />

            <label for="orderdate">Order Date:</label>
            <input type="date" name="orderdate" required />

            <label for="description">Description:</label>
            <input type="text" id="descriptions" name="descriptions" required />

            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" required />

            <label for="quotedprice">Quoted Price:</label>
            <input type="text" id="quotedprice" name="quotedprice" required />

           
            <button type="submit" name="add" value="add">Add order</button>
        </div>
    </form>
</body>

</html>