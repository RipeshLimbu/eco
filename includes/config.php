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
