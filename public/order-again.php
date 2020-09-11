<?php

require_once "php/classes/OrderHandler.php";

$orderHandler = new OrderHandler();

$_SESSION['orders'] = [];
$_SESSION['orderStatus'] = "";
$orderHandler->orderMore();
header("Location: /menu");
die();