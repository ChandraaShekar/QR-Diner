<?php

require_once "php/classes/admin/orderHandler.php";

$orderHandler = new OrderHandler();
$orderUpdater = $orderHandler->updateOrderStatus($_POST['tableStatus'], $_POST['tableNo']);

die($orderUpdater[1]);
