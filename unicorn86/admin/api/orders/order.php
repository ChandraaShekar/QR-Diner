<?php

$method = $_SERVER['REQUEST_METHOD'];

require_once "./orderHandler.php";

$order = new OrderHandler;

switch ($method) {
  case 'GET':
    $order->getOnGoingOrders();
    break;
  case 'POST':
    $order->placeOrder();
    break;
  default:
    echo "unknown request";
    break;
}


// function place_order($db, $data){
//     $uid = $data['uid'];
//     $restaurant_id = $data['restaurant_id'];
//     $note = $data['note'];
//     $email = $data['email'];
//     $phone = $data['phone'];
//     $shipping_address = $data['shipping_address'];
//     $order_id = order_id_generator($db);
//     $q = $db->query("SELECT common_cart.*, meta_products.* FROM meta_products 
//         LEFT JOIN common_cart ON common_cart.product_code = meta_products.product_code 
//         WHERE owner_id = '$uid' AND restaurant_id = '$restaurant_id'");
//     $total_items = mysqli_num_rows($q);
//     $total_price = 0;
//     $status = "success";
//     if($total_items > 0){
//         while($item = $q->fetch_assoc()){
//             $product_code = $item['product_code'];
//             $quantity = $item['quantity'];
//             $price = $item['price'];
//             $sale_price = $item['sale_price'];
//             $pkg_weight = $item['pkg_weight'];
//             $pkg_quantity = $item['pkg_quantity'];
//             $total_price = (($sale_price != 0)? $sale_price : $price) * $quantity;
//             $note = $item['note'];
//             $q2 = $db->query("INSERT INTO order_items (order_id, product_code, quantity, price, sale_price, total_price, pkg_weight, pkg_quantity) 
//                         VALUES ('$order_id', '$product_code', '$quantity', '$price', '$sale_price', '$total_price', '$pkg_weight', '$pkg_quantity')") or die(mysqli_error($db));
//             $q5 = $db->query("UPDATE meta_products SET stock_quantity = '". ($item['stock_quantity'] - $quantity)."'");
//             if(!$q2){
//                 $db->query("DELETE FROM order_items WHERE order_id = '$order_id'") or die(mysqli_error($db));
//                 $status = "error";
//                 echo "failed, try again later";
//                 break;
//             }else{
//                 if($sale_price > 0){
//                     $ppp = $sale_price;
//                 }else{
//                     $ppp = $price;
//                 }
//                 $total_price += $ppp * $quantity;
//             }
//         }
//         if($status == "success"){

//             $q3 = $db->query("INSERT INTO order_info (order_id, orderd_by, restaurant_id, total_items, total_price, order_status, note, email, phone, shipping_address) 
//                         VALUES('$order_id', '$uid', '$restaurant_id', '$total_items', '$total_price', '1', '$note', '$email', '$phone', '$shipping_address')") or die(mysqli_error($db));
//             $q4 = $db->query("DELETE FROM common_cart WHERE owner_id = '$uid' AND restaurant_id = '$restaurant_id'") or die(mysqli_error($db));
//             if($q3 && $q4){
//                 echo "success";
//             }else{
//                 $db->query("DELETE FROM order_items WHERE order_id = '$order_id'");
//                 echo "failed, try again later";
//             }
//         }
//     }else{
//         echo "no items in the cart";
//     }
// }

