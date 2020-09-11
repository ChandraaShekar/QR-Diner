<?php

/*
code x
name x
description x
price x
quantity
pkg_weight
no_of_items_pkg
pkg_type
inventory_stock
discount
barcode_info
category
tags
*/

require_once "db/db.php";

$image_data = [
    "http://dummyimage.com/480x480.jpg/cc0000/ffffff",
    "http://dummyimage.com/480x480.jpg/dddddd/000000",
    "http://dummyimage.com/480x480.jpg/ff4444/ffffff",
    "http://dummyimage.com/480x480.jpg/5fa2dd/ffffff",
    "http://dummyimage.com/480x480.jpg/ff4444/ffffff",
    "http://dummyimage.com/480x480.jpg/ff4444/ffffff",
    "http://dummyimage.com/480x480.jpg/cc0000/ffffff",
    "http://dummyimage.com/480x480.jpg/dddddd/000000",
    "http://dummyimage.com/480x480.jpg/5fa2dd/ffffff",
    "http://dummyimage.com/480x480.jpg/cc0000/ffffff",
    "http://dummyimage.com/480x480.jpg/cc0000/ffffff",
    "http://dummyimage.com/480x480.jpg/5fa2dd/ffffff",
    "http://dummyimage.com/480x480.jpg/cc0000/ffffff",
    "http://dummyimage.com/480x480.jpg/5fa2dd/ffffff",
    "http://dummyimage.com/480x480.jpg/cc0000/ffffff",
    "http://dummyimage.com/480x480.jpg/cc0000/ffffff",
    "http://dummyimage.com/480x480.jpg/cc0000/ffffff",
    "http://dummyimage.com/480x480.jpg/ff4444/ffffff",
    "http://dummyimage.com/480x480.jpg/ff4444/ffffff",
    "http://dummyimage.com/480x480.jpg/5fa2dd/ffffff",
    "http://dummyimage.com/480x480.jpg/5fa2dd/ffffff",
    "http://dummyimage.com/480x480.jpg/ff4444/ffffff",
    "http://dummyimage.com/480x480.jpg/cc0000/ffffff",
    "http://dummyimage.com/480x480.jpg/cc0000/ffffff",
    "http://dummyimage.com/480x480.jpg/ff4444/ffffff",
    "http://dummyimage.com/480x480.jpg/5fa2dd/ffffff",
    "http://dummyimage.com/480x480.jpg/ff4444/ffffff",
    "http://dummyimage.com/480x480.jpg/cc0000/ffffff",
    "http://dummyimage.com/480x480.jpg/dddddd/000000",
    "http://dummyimage.com/480x480.jpg/ff4444/ffffff",
    "http://dummyimage.com/480x480.jpg/cc0000/ffffff",
];


$q = $db->query('SELECT product_code FROM meta_products');
// $products = $q->fetch_assoc();
$x = 0;
while($row = $q->fetch_assoc()){
    $count = random_int(1, 4);
    for($i=0;$i<$count;$i++){
        $rand_image = $image_data[random_int(0, 29)];
        $q2 = $db->query("INSERT INTO product_meta_images (`product_code`, `image`) VALUES ('". $row['product_code'] ."','". $rand_image ."')");
        if($q2){
            echo ++$x . " - success<br>";
        }else{
            echo ++$x . " - error<br>";
        }
    }
}


// $pkg_types = ["can", "tin", "bottle", "frozen"];

// for($i=0;$i<500;$i++){
//     $quantity = random_int(1, 10);
//     $pkg_weight = random_int(10, 300);
//     // $no_of_items_pkg = random_int(1, 15);
//     $pkg_type = $pkg_types[random_int(0,3)];
//     $inventory_stock = random_int(1, 200);
//     $discount_p = random_int(5, 100);
//     $discount = ($discount_p > 15)? 0 : $discount_p;
//     $category = random_int(1, 15);
//     $q = $db->query("UPDATE meta_products SET 
//     pkg_weight = '$pkg_weight', 
//     pkg_quantity = '$quantity', 
//     pkg_type = '$pkg_type',
//     stock_quantity = '$inventory_stock',
//     discount = '$discount',
//     category = '$category' WHERE id = '". ($i+1) ."'");
//     if($q){
//         echo "success <br>";
//     }else{
//         echo "error <br>";
//     }
// }

?>