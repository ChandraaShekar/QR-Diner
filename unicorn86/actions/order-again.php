<?php

session_start();
// require_once "../classes/orderHandler.php";
require_once "../classes/orderHandller.php";

$orderHandler = new OrderHandler();

$_SESSION['orders'] = [];
$_SESSION['orderStatus'] = "";
$orderHandler->orderMore();
header("Location: ../menu.php");
die();