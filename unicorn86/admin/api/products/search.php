<?php

$method = $_SERVER['REQUEST_METHOD'];

require_once './ProductHandler.php';

$product = new ProductHandler;

switch($method){
    case "GET":
        $product->productSearch();
        break;
    default:
        echo "unknown request";
        break;
}
