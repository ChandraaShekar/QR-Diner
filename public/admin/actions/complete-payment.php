<?php
require_once "db/db.php";
require_once "../php/auth.php";

if(isset($_GET['order_id']) & isset($_GET['order_status'])){
    $order_id = $_GET['order_id'];
    $order_status = $_GET['order_status'];
    $q = $db->query("UPDATE order_info SET order_status = '$order_status' WHERE order_id = '$order_id'") or die(mysqli_error($db));
    if($q){
        header("Location: ../payments.php");
    }else{
        echo "<script>alert('There is an error.\nTry again Later');</script>";
    }
}else{
    header("Location: ../404.php");
}
