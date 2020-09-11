<?php

require_once "php/classes/UserHandler.php";
require_once "php/classes/OrderHandler.php";
$userHandler = new UserHandler();
$orderHandller = new OrderHandler();
$userStatus = $userHandler->checkUserStatus();
// print_r($_POST);
if(isset($_POST['submit'])){
    $note = $_POST['order-note'];
    // die($note);
    if($userStatus[0]){
        $res = $orderHandller->placeOrder($note);
        if($res){
            header("Location: /orderPlaced");
        }else{
            header("Location: /menu");
        }
    }else{
        print_r($_SESSION);
        die($userStatus[1] . "<br><a href='/menu'>Return to menu</a>");
    }
   
}