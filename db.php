<?php

$host = 'localhost';
  $username = 'root';
   $password = '';
 $database = 'waste_management';


 $conn = mysqli_connect($host, $username, $password, $database, 3307);

 if($conn){
    echo "success";

 }else{
    echo "Failed". mysqli_connect_error();
 }