<?php

$method = $_SERVER['REQUEST_METHOD'];
require_once "./homeHandler.php";
$home = new HomeHandler;
switch($method){
    case "GET":
        $home->getHomeData();
        break;
    default:
        echo "unknown request";
        break;
}
