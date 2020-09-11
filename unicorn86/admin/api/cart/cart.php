<?php

$method = $_SERVER['REQUEST_METHOD'];
require_once "./CartHandler.php";

$cart = new CartHandler;

switch ($method) {
  case 'GET':
    $cart->getcart();
    break;
  case 'POST':
    $cart->addItem();
    break;
  default:
    echo "unknown request";
    break;
}