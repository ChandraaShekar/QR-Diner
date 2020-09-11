<?php

$method = $_SERVER['REQUEST_METHOD'];

require_once './orderHandler.php';

$order = new OrderHandler;

switch ($method) {
  case 'GET':
    $order->getOrderInfo();
    break;
  default:
    echo "unknown request";
    break;
}
