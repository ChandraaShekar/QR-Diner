<?php

$method = $_SERVER['REQUEST_METHOD'];
require_once './restaurantHandler.php';
$restaurant = new RestaurantHandler;

switch ($method) {
  case 'GET':
    $restaurant->updateRestaurant();
    break;
  default:  
    echo "unknown request";
    break;
}
