<?php

require_once 'paymentConfig.php';
require_once '../classes/paymentHandler.php';

if(isset($_POST['stripeToken'])){
    $token = $_POST['stripeToken'];
    $email = $_POST['stripeEmail'];
    $type = $_POST['stripeTokenType'];
    $paymentStatus = \Stripe\Charge::create(array(
        "amount" => floor($_SESSION['payableAmount']),
        "currency" => "usd",
        "description" => "Unicorn Restaurant Bill Payment",
        "source" => $token
    ));
    // echo "</pre>";
    $paymentHandler = new PaymentHandler();
    if($paymentHandler->updatePaymentSuccess((floor($_POST['paymentAmount']) / 100), $paymentStatus['status'], ($paymentStatus['amount'] / 100), $token, $type)){
        header("Location: ../feedback.php");
    }else{
        header("Location: ../error.php");
    }
    // if($paymentStatus['status'] == "succeeded"){
    //     header("Location: update-payment-status.php?paidAmount=". ($paymentStatus['amount'] / 100) . "&token=$token&type=$type");
    // }else{
    //     header("Location: ../error.php");
    // }
    
}


?>