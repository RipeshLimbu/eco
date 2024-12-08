<?php

$server = "localhost";
$username = "root";
$password = ""; 
$database = "project_wms";

$conn = mysqli_connect($server, $username, $password, $database);

if ($conn) {
    // echo "Connection established";
} else {
    echo "Connection failed" . mysqli_connect_error();
}
