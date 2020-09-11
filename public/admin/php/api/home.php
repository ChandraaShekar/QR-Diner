<?php

// require_once "../../classes/orderHandller.php";
require_once "php/classes/admin/orderHandler.php";

$orderHandler = new OrderHandler();

// $result = $orderHandler->getUnSeenOrders();

// echo "Hello";

$res = ['home_data' => $orderHandler->getHomePageData()];
echo json_encode($res);