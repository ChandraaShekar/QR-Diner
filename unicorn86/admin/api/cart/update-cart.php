<?php

$method = $_SERVER['REQUEST_METHOD'];
require_once './CartHandler.php';
$cart = new CartHandler;
switch ($method) {
    case 'GET':
        $cart->removeItem();
        break;
    case 'POST':
        $cart->updateCart();
        break;
    default:
      echo "unknown request";
      break;
}
