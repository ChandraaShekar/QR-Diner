<?php

require_once "php/classes/OrderHandler.php";
$orderHandler = new OrderHandler();


if(isset($_SESSION['user'])){
    $userSess = $_SESSION['user']['user_info'];
    $mainUserId = ($userSess['isRoot'] == 'true')? $userSess['uid'] : $userSess['subOf'];
    $orderList = $orderHandler->getOrderList($mainUserId);
    echo json_encode($orderList);
}

?>