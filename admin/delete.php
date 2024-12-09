<?php
include '../includes/config.php';
//using get method, we can access variable/parameter from the URL
if(isset($_GET['deleteid'])){
    $id = $_GET['deleteid'];
    $sql = "DELETE from crud where `s.n` =$id";
    $result = mysqli_query($conn, $sql);
      if($result){
        echo "Data Deleted";
        header ('Location: display.php');
      }else{
        die(mysqli_error($conn));
      }
}
?>