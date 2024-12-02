<?php

$conn = new mysqli('localhost','root','','curdphp');
if(!$conn){
    die(mysqli_error($conn));
}
else{
    // echo "Connection is successfully.";
}
?>