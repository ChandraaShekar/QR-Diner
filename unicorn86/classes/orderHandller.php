<?php

// require_once "main.php";
require_once "menuHandler.php";
session_start();

class OrderHandler extends Main {
    public function __construct(){
        parent::__construct();
    }

    public function getOrderList($mainUserId){
        $result = [];
        try{
            $q = $this->db->prepare("SELECT common_cart.*, menu_items.name, menu_items.price, menu_items.offerPrice FROM common_cart LEFT JOIN menu_items ON menu_items.name = common_cart.itemName WHERE common_cart.userId = :userId");
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
            $q = $this->db->prepare("SELECT itemName, itemCount FROM common_cart WHERE userId = :userId");
            $q->bindParam(':userId', $mainUserId);
            $q->execute();
            while($row = $q->fetch(PDO::FETCH_ASSOC)){
                $status[$row['itemName']] = $row; 
            }
        }catch(Exception $e){
            die($e->getMessage());
        }
        return $status;
    }

    public function addToCart($itemName, $type){
        $status = [false, ""];
        try{
            $userSess = $_SESSION['user']['user_info'];
            $totalItemCount = "1";
            $uid =  ($userSess['isRoot'] == 'true')? $userSess['uid'] : $userSess['subOf'];
            $q = $this->db->prepare("SELECT itemCount, COUNT(1) as itemStatus FROM common_cart WHERE itemName = :itemName AND userId = :userId");
            $q->bindParam(':itemName', $itemName);
            $q->bindParam(':userId', $uid);
            $q->execute();
            $exists = $q->fetch(PDO::FETCH_ASSOC);
            
            if($exists['itemStatus'] == "1"){
                $totalItemCount = ($type == "sub")? $exists['itemCount']-1 : $exists['itemCount'] +1;
                if($totalItemCount != '0'){
                    $q1 = $this->db->prepare("UPDATE common_cart SET itemCount = '$totalItemCount' WHERE userId = :userId AND itemName = :itemName");
                    $q1->bindParam(':userId', $uid);
                    $q1->bindParam(':itemName', $itemName);
                    if($q1->execute()){
                        $status = [true, ""];
                    }else{
                        $status = [false, "Failed to Update the common Cart"];
                    }
                }else{
                    $q1 = $this->db->prepare("DELETE FROM common_cart WHERE userId = :userId AND itemName = :itemName");
                    $q1->bindParam(':userId', $uid);
                    $q1->bindParam(':itemName', $itemName);
                    if($q1->execute()){
                        $status = [true, ""];
                    }else{
                        $status = [false, "Failed to Update the common Cart"];
                    }
                }
            }else{
                if($type == "add"){
                    $q1 = $this->db->prepare("INSERT INTO common_cart (userId, itemName, itemCount, addedByRoot) VALUES (:userId, :itemName, :itemCount, :addedBy)");
                    $q1->bindParam(":userId", $uid);
                    $q1->bindParam(":itemName", $itemName);
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
            $q = $this->db->prepare("SELECT common_cart.*, menu_items.price, menu_items.offerPrice FROM common_cart LEFT JOIN menu_items ON menu_items.name = common_cart.itemName WHERE userId = :userId");
            $q->bindParam(":userId", $userId);
            $q->execute();
            while($orderItem = $q->fetch(PDO::FETCH_ASSOC)){
                $itemPrice = ($orderItem['offerPrice'] != null) ? $orderItem['offerPrice'] : $orderItem['price'];
                $totalPrice += $itemPrice * $orderItem['itemCount'];
                $priceWithTax += $itemPrice * $orderItem['itemCount'] * 1.1;
                $itemTotalPrice = $itemPrice * $orderItem['itemCount'];
                $q1 = $this->db->prepare("INSERT INTO order_items 
                                        (orderId, itemName, itemCount, itemPrice, totalPrice) 
                                        VALUES
                                        (:orderId, :itemName, :itemCount, :itemPrice, :totalPrice)");
                $q1->bindParam(":orderId", $orderId);
                $q1->bindParam(":itemName", $orderItem['itemName']);
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
            $q2 = $this->db->prepare("INSERT INTO order_info 
                                    (orderId, userId, totalPrice, totalPriceWithTax, note) 
                                    VALUES 
                                    (:orderId, :userId, :totalPrice, :priceWithTax, :note)");
            $q2->bindParam(":orderId", $orderId);
            $q2->bindParam(":userId", $userId);
            $q2->bindParam(":totalPrice", $totalPrice);
            $q2->bindParam(":priceWithTax", $priceWithTax);
            $q2->bindParam(":note", $orderNote);
            if(!$q2->execute()){
                $status = false;
                $q0 = $this->db->prepare("DELETE FROM order_items WHERE orderId = $orderId");
                $q0->execute();
            }
            $q3 = $this->db->prepare("UPDATE table_info SET tableStatus = 'orderPlaced' WHERE occupiedUser = :userId");
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
            die($e->getMessage());
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
                $q1 = $this->db->prepare("SELECT * FROM order_items WHERE orderId = :orderId");
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
}