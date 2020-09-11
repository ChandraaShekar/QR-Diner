<?php

require_once "php/classes/OrderHandler.php";

$orderHandler = new OrderHandler();

echo json_encode($orderHandler->getOrderStatus());