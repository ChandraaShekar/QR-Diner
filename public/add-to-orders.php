<?php
require_once "php/classes/OrderHandler.php";
$orderHandller = new OrderHandler();
$itemId = $_POST['itemId'];
$type = $_POST['changeType'];
$res = $orderHandller->addToCart($itemId, $type);

if($res[0]){
    echo "success";
}else{
    echo "error: ". $res[1];
}
