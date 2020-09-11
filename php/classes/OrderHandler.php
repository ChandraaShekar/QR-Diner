<?php

// require_once "main.php";
require_once "Main.php";

class OrderHandler extends Main {
    public $restaurantId = "";
    public function __construct(){
        parent::__construct();
        $this->restaurantId  = $_SESSION['user']['restaurantId'];
    }

    public function getOrderList($mainUserId){
        $result = [];
        try{
            $q = $this->db->prepare("SELECT common_cart.*, menu_items.name, menu_items.price, menu_items.offerPrice FROM common_cart LEFT JOIN menu_items ON menu_items.id = common_cart.itemId WHERE common_cart.userId = :userId");
            $q->bindParam(':userId', $mainUserId);
            $q->execute();
            $result = $q->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e){
            die($e->getMessage());
        }
        return $result;
    }

    public function getItemWithName($mainUserId){
        $status = [];
        try{
            $q = $this->db->prepare("SELECT 
                            common_cart.itemId, 
                            common_cart.itemCount,
                            menu_items.name
                            FROM common_cart 
                            LEFT JOIN menu_items ON menu_items.id = common_cart.itemId 
                            WHERE common_cart.userId = :userId");
            $q->bindParam(':userId', $mainUserId);
            $q->execute();
            while($row = $q->fetch(PDO::FETCH_ASSOC)){
                $status[$row['itemId']] = $row; 
            }
        }catch(Exception $e){
            die($e->getMessage());
        }
        return $status;
    }

    public function addToCart($itemId, $type){
        $status = [false, ""];
        try{
            $userSess = $_SESSION['user']['user_info'];
            $totalItemCount = "1";
            $uid =  ($userSess['isRoot'] == 'true')? $userSess['uid'] : $userSess['subOf'];
            $q = $this->db->prepare("SELECT itemCount, COUNT(1) as itemStatus FROM common_cart WHERE itemid = :itemId AND userId = :userId");
            $q->bindParam(':itemId', $itemId);
            $q->bindParam(':userId', $uid);
            $q->execute();
            $exists = $q->fetch(PDO::FETCH_ASSOC);
            
            if($exists['itemStatus'] == "1"){
                $totalItemCount = ($type == "sub")? $exists['itemCount']-1 : $exists['itemCount'] +1;
                if($totalItemCount != '0'){
                    $q1 = $this->db->prepare("UPDATE common_cart SET itemCount = '$totalItemCount' WHERE userId = :userId AND itemId = :itemId");
                    $q1->bindParam(':userId', $uid);
                    $q1->bindParam(':itemId', $itemId);
                    if($q1->execute()){
                        $status = [true, ""];
                    }else{
                        $status = [false, "Failed to Update the common Cart"];
                    }
                }else{
                    $q1 = $this->db->prepare("DELETE FROM common_cart WHERE userId = :userId AND itemId = :itemId");
                    $q1->bindParam(':userId', $uid);
                    $q1->bindParam(':itemId', $itemId);
                    if($q1->execute()){
                        $status = [true, ""];
                    }else{
                        $status = [false, "Failed to Update the common Cart"];
                    }
                }
            }else{
                if($type == "add"){
                    $q1 = $this->db->prepare("INSERT INTO common_cart (userId, restaurantId, itemId, itemCount, addedByRoot) VALUES (:userId, :resId, :itemId, :itemCount, :addedBy)");
                    $q1->bindParam(":userId", $uid);
                    $q1->bindParam(":resId", $this->restaurantId);
                    $q1->bindParam(":itemId", $itemId);
                    $q1->bindParam(":itemCount", $totalItemCount);
                    $q1->bindParam(":addedBy", $userSess['isRoot']);
                    if($q1->execute()){
                        $status = [true, ""];
                    }else{
                        $status = [false, "Failed to Update the common Cart"];
                    }
                }else{
                    $status = [false, "Unknown Type"];
                }
            }
        }catch(Exception $e){
            $status = [false, explode(": ", $e->getMessage())[2]];

        }
        return $status;
    }

    public function placeOrder($orderNote){
        $status = false;
        try{
            $userSess = $_SESSION['user']['user_info'];
            $orderId = uniqid("ORDER", false);
            $userId = $userSess['uid'];
            $totalPrice = 0;
            $priceWithTax = 0;
            $q = $this->db->prepare("SELECT 
                    common_cart.*, 
                    menu_items.price, 
                    menu_items.offerPrice FROM common_cart 
                    LEFT JOIN menu_items ON menu_items.id = common_cart.itemId 
                    WHERE userId = :userId");
            $q->bindParam(":userId", $userId);
            $q->execute();
            while($orderItem = $q->fetch(PDO::FETCH_ASSOC)){
                $itemPrice = ($orderItem['offerPrice'] != null) ? $orderItem['offerPrice'] : $orderItem['price'];
                $totalPrice += $itemPrice * $orderItem['itemCount'];
                $priceWithTax += $itemPrice * $orderItem['itemCount'] * 1.1;
                $itemTotalPrice = $itemPrice * $orderItem['itemCount'];
                $q1 = $this->db->prepare("INSERT INTO order_items 
                        (restaurantId, orderId, itemId, itemCount, itemPrice, totalPrice) 
                        VALUES
                        (:resId, :orderId, :itemId, :itemCount, :itemPrice, :totalPrice)");
                $q1->bindParam(":resId", $this->restaurantId);
                $q1->bindParam(":orderId", $orderId);
                $q1->bindParam(":itemId", $orderItem['itemId']);
                $q1->bindParam(":itemCount", $orderItem['itemCount']);
                $q1->bindParam(":itemPrice", $itemPrice);
                $q1->bindParam(":totalPrice", $itemTotalPrice);
                $status = true;
                
                if(!$q1->execute()){
                    $status = false;
                    $q0 = $this->db->prepare("DELETE FROM order_items WHERE orderId = '$orderId'");
                    $q0->execute();
                    break;
                }
            }
            $totalPrice = round(floatval($totalPrice), 2);
            $priceWithTax = round(floatval($priceWithTax), 2);
            $q2 = $this->db->prepare("INSERT INTO order_info 
                        (restaurantId, orderId, userId, tableNo, totalPrice, totalPriceWithTax, note) 
                        VALUES 
                        (:resId, :orderId, :userId, :tableNo, :totalPrice, :priceWithTax, :note)");
            $q2->bindParam(":resId", $this->restaurantId);
            $q2->bindParam(":orderId", $orderId);
            $q2->bindParam(":userId", $userId);
            $q2->bindParam(":tableNo", $_SESSION['user']['table_info']['table_number']);
            $q2->bindParam(":totalPrice", $totalPrice);
            $q2->bindParam(":priceWithTax", $priceWithTax);
            $q2->bindParam(":note", $orderNote);
            if(!$q2->execute()){
                print_r($q2->errorInfo());
                $status = false;
                $q0 = $this->db->prepare("DELETE FROM order_items WHERE orderId = $orderId");
                $q0->execute();
            }
            $q3 = $this->db->prepare("UPDATE table_info SET tableStatus = 'Reviewing' WHERE occupiedUser = :userId");
            $q3->bindParam(":userId", $userId);
            if($q3->execute()){
                $status = true;
                $q5 = $this->db->prepare("DELETE FROM common_cart WHERE userId = :userId");
                $q5->bindParam(":userId", $userId);
                $q5->execute();
            }else{
                header("Refresh:0");
            }

        }catch(Exception $e){
            die("ERROR " . $e->getMessage());
        }
        return $status;
    }

    public function getAllOrderList(){
        $result = [];
        try{
            $userSess = $_SESSION['user']['user_info'];
            $userId = ($userSess['isRoot'] == 'true') ? $userSess['uid'] : $userSess['subOf'];
            $q = $this->db->prepare("SELECT * FROM order_info WHERE userId = :userId");
            // $q = $this->db->prepare("SELECT common_cart.*, menu_items.name, menu_items.price, menu_items.offerPrice FROM common_cart LEFT JOIN menu_items ON menu_items.name = common_cart.itemName WHERE common_cart.userId = :userId");
            $q->bindParam(':userId', $userId);
            $q->execute();
            $res = $q->fetchAll(PDO::FETCH_ASSOC);
            foreach ($res as $key => $value) {
                $q1 = $this->db->prepare("SELECT order_items.*, menu_items.name FROM order_items LEFT JOIN menu_items ON menu_items.id = order_items.itemId WHERE orderId = :orderId");
                $q1->bindParam(":orderId", $value['orderId']);
                $q1->execute();
                $result[] = ['orderInfo' => $value, "orderItems" => $q1->fetchAll(PDO::FETCH_ASSOC)]; 
            }
        }catch(Exception $e){
            die($e->getMessage());
        }
        return $result;
    }

    public function updatePaymentSuccess($totalAmount, $status, $payedAmount, $token, $payementType){
        $retStatus = false;
        try{
            $userId = $_SESSION['user']['user_info']['uid'];
            $q = $this->db->prepare("UPDATE order_info SET paymentStatus = '$status', paymentToken = '$token' WHERE userId = '$userId'");
            $q1 = $this->db->prepare("INSERT INTO payment_info (userId, totalPayable, totalAmountPayed, PaymentMethod, paymentToken, paymentStatus) VALUES('$userId', '$totalAmount', '$payedAmount', '$payementType', '$token', '$status')");
            if($q->execute() && $q1->execute()){
                $retStatus = true;
            }else{
                $retStatus = false;
            }
        }catch(Exception $e){
            if($status == "succeeded"){
                die("Payment Success But failed to update". explode(': ', $e->getMessage())[2]);
            }else{
                die("Payment Failed and failed to update". explode(': ', $e->getMessage())[2]);
            }
            $retStatus = false;
        }
        return $retStatus;
    }

    public function orderMore(){
        try{
            $userId = $_SESSION['user']['user_info']['uid'];
            $q = $this->db->prepare("DELETE FROM common_cart WHERE userId = :userId");
            $q->bindParam(':userId', $userId);
            return $q->execute();
        }catch(Exception $e){
            return false;
        }
    }

    public function getOrderStatus(){
        $tableStatus = "";
       if(!empty($this->restaurantId)){
            try{
                $tableNumber = $_SESSION['user']['table_info']['table_number'];
                $q = $this->db->prepare("SELECT tableStatus FROM table_info WHERE tableNumber = :tableNumber AND restaurantId = :restaurantId");
                $q->bindParam(':tableNumber', $tableNumber);
                $q->bindParam(':restaurantId', $this->restaurantId);
                $q->execute();
                $tableStatus = $q->fetch(PDO::FETCH_ASSOC);   
            }catch(Exception $e){
                $this->throwError($e);
            }
       }
       return $tableStatus;
    }
}