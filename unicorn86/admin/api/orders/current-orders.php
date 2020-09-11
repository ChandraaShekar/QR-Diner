<?php

// $method = $_SERVER['REQUEST_METHOD'];

// // $input = json_decode(file_get_contents('php://input'));
// $input = file_get_contents('php://input');
// require_once '../db/db.php';

// switch ($method) {
//   case 'GET':
//     current_orders($db);
//     break;
//   default:
//     break;
// }

// function current_orders($db){
//     if(isset($_GET['uid'])){
//         $uid = $_GET['uid'];

//         $q = $db->query("SELECT order_info.order_id, order_info.total_items, order_info.total_price, order_info.note,order_status.name AS order_state_name FROM order_info 
//                 LEFT JOIN order_status ON order_status.id = order_info.order_status
//                 WHERE orderd_by = '$uid' AND (order_status = '1' OR order_status='2' OR order_status = '7')");

//         $orders = [];
//         while($row = $q->fetch_assoc()){
//             $orders[] = $row;
//         }
//         echo json_encode($orders);
//     }
// }