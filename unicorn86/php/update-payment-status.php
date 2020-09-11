<?php

require_once "../classes/orderHandller.php";

if(isset($_GET['paidAmount']) && isset($_GET['token']) && isset($_GET['type'])){
    $paymentHandler = new OrderHandler();
    $token = $_GET['token'];
    $paidAmount = $_GET['paidAmount'];
    $type = $_GET['type'];
    if($paymentHandler->updatePaymentSuccess(($_SESSION['payableAmount'] / 100), "succeeded", $paidAmount, $token, $type)){
        header("Location: ../feedback.php");
    }else{
        header("Location: ../error.php");
    }
}else{
    header("Location: orderPlaced.php");
}