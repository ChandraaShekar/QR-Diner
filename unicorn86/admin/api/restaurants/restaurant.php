<?php

require_once './restaurantHandler.php';

$method = $_SERVER['REQUEST_METHOD'];

require_once '../db/db.php';

$restaurant = new RestaurantHandler('');

switch ($method) {
  case 'GET':
    $restaurant->getRestaurant();
    break;
  case 'POST':
    $restaurant->addRestaurant();
    break;
  default:  
    echo "unknown request";
    break;
}


function get_restaurant($db){
    if(isset($_GET['uid'])){
        $uid = $_GET['uid'];
        $q = $db->query("SELECT * FROM restaurant_info WHERE `id` IN (SELECT restaurant_id FROM users WHERE `uid` = '". $uid ."')");
        if(mysqli_num_rows($q) > 0){
            echo json_encode([$q->fetch_assoc()]);
        }else{
            echo json_encode([]);
        }
    }else{
        echo "unknown request";
    }
}