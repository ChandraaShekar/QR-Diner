<?php

require_once "../classes/api.php";

class OrderHandler extends Api {

    private $restaurantId;
    private $note;
    private $shipping_address;
    private $phone;
    private $email;
    private $order_id;

    public function __construct(){
        parent::__construct('');
    }

    public function placeOrder(){
        try{
            $order_id = $this->order_id_generator();
            $this->uid = $this->validateParameter('uid', $this->param['uid'], STRING);
            $this->restaurantId = $this->validateParameter('Restaurant Id', $this->param['restaurant_id'], INTEGER);
            $this->note= $this->validateParameter('Note', $this->param['note'], STRING);
            $this->shipping_address = $this->validateParameter('Shipping Address', $this->param['shipping_address'], STRING);
            $this->phone = $this->validateParameter('Phone', $this->param['phone'], STRING);
            $this->email = $this->validateParameter('Email', $this->param['email'], STRING);

            $q = $this->db->prepare("SELECT common_cart.*, meta_products.* FROM meta_products 
                                LEFT JOIN common_cart ON common_cart.product_code = meta_products.product_code 
                                WHERE owner_id = :uid AND restaurant_id = :restaurant_id");
            $q->bindParam(':uid', $this->uid);
            $q->bindParam(':restaurant_id', $this->restaurantId);
            $q->execute();
            $total_price = 0;
            $q6 = $this->db->prepare("SELECT COUNT(1) AS total_items FROM common_cart WHERE owner_id = :uid");
            $q6->bindParam(':uid', $this->uid);
            $q6->execute();
            $total_items = $q6->fetch(PDO::FETCH_ASSOC)['total_items'];
            while($item = $q->fetch(PDO::FETCH_ASSOC)){
                $item_total_price = 0;
                $product_code = $item['product_code'];
                $quantity = $item['quantity'];
                $price = $item['price'];
                $sale_price = $item['sale_price'];
                $pkg_weight = $item['pkg_weight'];
                $pkg_quantity = $item['pkg_quantity'];
                $item_total_price += (($sale_price != 0)? $sale_price : $price) * $quantity;
                $q2 = $this->db->prepare("INSERT INTO order_items (order_id, product_code, quantity, price, sale_price, total_price, pkg_weight, pkg_quantity) 
                        VALUES ('$order_id', '$product_code', '$quantity', '$price', '$sale_price', '$item_total_price', '$pkg_weight', '$pkg_quantity')");
                $this->db->prepare("UPDATE meta_products SET stock_quantity = '". ($item['stock_quantity'] - $quantity)."'")->execute();
                if(!$q2->execute()){
                    $q3 = $this->db->prepare("DELETE FROM order_items WHERE order_id = '$order_id'");
                    $q3->execute();
                    $this->throwError(QUERY_FAILED, 'Error occured while placing the order');
                }else{
                    if($sale_price > 0){
                        $ppp = $sale_price;
                    }else{
                        $ppp = $price;
                    }
                    $total_price += $ppp * $quantity;
                }
            }
            $q4 = $this->db->prepare("INSERT INTO order_info (order_id, orderd_by, restaurant_id, total_items, total_price, order_status, note, email, phone, shipping_address) 
                        VALUES('$order_id', :uid, :restaurant_id, '$total_items', '$total_price', '1', :note, :email, :phone, :shipping_address)");
            $q4->bindParam(':uid', $this->uid);
            $q4->bindParam(':restaurant_id', $this->restaurantId);
            $q4->bindParam(':note', $this->note);
            $q4->bindParam(':shipping_address', $this->shipping_address);
            $q4->bindParam(':email', $this->email);
            $q4->bindParam(':phone', $this->phone);
            if(!$q4->execute()){
                $this->db->query("DELETE FROM order_items WHERE order_id = '$order_id'")->execute();
                $this->throwError(QUERY_FAILED, "Error while placing order, Try again later.");
            }

            $q5 = $this->db->prepare("DELETE FROM common_cart WHERE owner_id = :uid AND restaurant_id = :restaurant_id");
            $q5->bindParam(':uid', $this->uid);
            $q5->bindParam(':restaurant_id', $this->restaurantId);
            $q5->execute();
            $this->returnResponse(SUCCESS_RESPONSE, "Successfully placed your order");
            
        }catch(Exception $e){
            $this->throwError(EXCEPTIONS, $e->getMessage());
        }
    }

    public function getOnGoingOrders(){
        $this->uid = $this->validateParameter('uid', $this->param['uid'], STRING);
        $this->restaurantId = $this->validateParameter('Restaurant Id', $this->param['restaurant_id'], INTEGER);
        try{
            $q = $this->db->prepare("SELECT order_info.order_id, order_info.total_items, order_info.total_price, order_info.note, order_info.date, order_status.name AS order_status FROM order_info 
                        LEFT JOIN order_status ON order_status.id = order_info.order_status
                        WHERE (order_info.order_status = '1' OR order_info.order_status = '2' OR order_info.order_status = '5') AND (order_info.orderd_by = :uid OR order_info.restaurant_id = :restaurant_id)");
            $q->bindParam(':uid', $this->uid);
            $q->bindParam(':restaurant_id', $this->restaurantId);
            $q->execute();
            $current_orders = [];
            while($order = $q->fetch(PDO::FETCH_ASSOC)){
                $current_orders[] = $order;
            }
            $this->returnResponse(SUCCESS_RESPONSE, $current_orders);

        }catch(Exception $e){
            $this->throwError(EXCEPTIONS, $e->getMessage());
        }
    }

    public function getPastOrders(){
        $this->uid = $this->validateParameter('uid', $this->param['uid'], STRING);
        $this->restaurantId = $this->validateParameter('Restaurant Id', $this->param['restaurant_id'], INTEGER);
        try{
            $q = $this->db->prepare("SELECT order_info.order_id, order_info.total_items, order_info.total_price, order_info.note, order_info.date, order_status.name AS order_status FROM order_info 
                        LEFT JOIN order_status ON order_status.id = order_info.order_status
                        WHERE NOT (order_info.order_status = '1' OR order_info.order_status = '2' OR order_info.order_status = '5') AND order_info.orderd_by = :uid");
            $q->bindParam(':uid', $this->uid);
            $q->execute();
            $current_orders = [];
            while($order = $q->fetch(PDO::FETCH_ASSOC)){
                $current_orders[] = $order;
            }
            $this->returnResponse(SUCCESS_RESPONSE, $current_orders);

        }catch(Exception $e){
            $this->throwError(EXCEPTIONS, $e->getMessage());
        }
    }

    public function getOrderInfo(){
        $this->uid = $this->validateParameter('uid', $this->param['uid'], STRING);
        $this->order_id = $this->validateParameter('uid', $this->param['order_id'], STRING);
        try{
            $q = $this->db->prepare("SELECT * FROM order_info WHERE order_id = :order_id AND restaurant_id IN (SELECT id FROM restaurant_info WHERE created_by = :uid)");
            $q->bindParam(':order_id', $this->order_id);
            $q->bindParam(':uid', $this->uid);
            $q->execute();
            $order_info = $q->fetch(PDO::FETCH_ASSOC);
            $q2 = $this->db->prepare("SELECT order_items.product_code, order_items.quantity, order_items.price, order_items.sale_price, order_items.pkg_weight, order_items.pkg_quantity, order_items.time, product_meta_images.image, meta_products.product_name FROM order_items
            LEFT JOIN product_meta_images ON product_meta_images.product_code = order_items.product_code
            LEFT JOIN meta_products ON meta_products.product_code = order_items.product_code
            WHERE order_items.order_id = :order_id GROUP BY order_items.product_code");
            $q2->bindParam(':order_id', $this->order_id);
            $q2->execute();
            $order_items = [];
            while($order_item = $q2->fetch(PDO::FETCH_ASSOC)){
                $order_items[] = $order_item;
            }
            $this->returnResponse(SUCCESS_RESPONSE, ['order_info' => $order_info, 'order_items' => $order_items]);
        }catch(Exception $e){
            $this->throwError(EXCEPTIONS, $e->getMessage());
        }
    }

    private function order_id_generator(){
        $q = $this->db->prepare("SELECT MAX(id) AS highest FROM order_info");
        $q->execute();
        $num = $q->fetch(PDO::FETCH_ASSOC);
        if(is_array($num)){
            $num = $num['highest']+1;
        }else{
            $num = 1;
        }
        $num = str_pad($num, 6, 0, STR_PAD_LEFT);
        return "VFS".$num;
    }
}