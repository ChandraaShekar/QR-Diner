<?php

require_once "Admin.php";

class OrderHandler extends Admin {
    public $restaurantId = "";
    public function __construct(){
        parent::__construct();
        $this->restaurantId = $_SESSION['admin_user']['restaurantId'];
        $this->validateLogin();
    }

    public function getOrdersList() {
        $result = [];
        if(!empty($this->restaurantId)){
            try{
                $q = $this->db->prepare("SELECT * FROM order_info WHERE restaurantId = :resId ORDER BY time DESC");
                $q->bindParam(':resId', $this->restaurantId);
                $q->execute();
                $result = [$q->fetchAll(PDO::FETCH_ASSOC)];
            }catch(Exception $e){
                die($e->getMessage());
            }
        }
        return $result;
    }

    public function getOrderInfo($orderId){
        $result = [];
        try{
            $q = $this->db->prepare("SELECT order_info.*,user_info.* FROM order_info LEFT JOIN user_info ON order_info.userId = user_info.uid WHERE order_info.orderId = :orderId");
            $q->bindParam(':orderId', $orderId);
            $q->execute();
            $result['order_info'] = $q->fetch(PDO::FETCH_ASSOC);
            $q2 = $this->db->prepare("SELECT order_items.*, menu_items.* FROM order_items LEFT JOIN menu_items ON menu_items.id = order_items.itemId WHERE orderId = :orderId");
            $q2->bindParam(':orderId', $orderId);
            $q2->execute();
            $result['order_items'] = $q2->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e){
            die($e->getMessage());
        }
        return $result;
    }

    public function getOrderStatus(){
        $result = [];
        try{
            $q = $this->db->prepare("SELECT * FROM orderStatus");
            $q->execute();
            $result = $q->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e){
            die($e->getMessage());
        }
        return $result;
    }

    // public function checkOrderStatus(){
    //     $result = [false, []];
    //     if(!empty($this->restaurantId)){
    //         try{
    //             $q = $this->db->prepare("SELECT COUNT(1) as neworders FROM orders WHERE restaurantId = :resId AND retriveStatus = 'false'");
    //             $q->bindParam(':resId', $this->restaurantId);
    //             $q->execute();
    //             $res = $q->fetch(PDO::FETCH_ASSOC);
    //             if($res['neworders'] > '0'){
                    
    //             }
    //         }catch(Exception $e){
    //             die($e->getMessage());
    //         }
    //     }
    // }

    public function getUnSeenOrders(){
        $result = [];
        if(!empty($this->restaurantId)){
            try{
                $q = $this->db->prepare("SELECT order_info.*, user_info.name, table_info.tableNumber FROM order_info 
                                LEFT JOIN user_info ON user_info.uid = order_indo.userId
                                LEFT JOIN table_info ON table_info.occupiedUser WHERE 
                                order_info.seenStatus='false' OR order_info.retriveStatus = 'false'");
                $q1 = $this->db->prepare("UPDATE order_info SET retriveStatus = 'true' WHERE restaurantId = :resId");
                $q1->bindParam(':resId', $this->restaurantId);
                $q->execute();
                if($q1->execute()){
                    $result = $q->fetchAll(PDO::FETCH_ASSOC);
                }
            }catch(Exception $e){
                die($e->getMessage());
            }
        }
        return $result;
    }

    public function getUnseenOrderCount(){
        $result = [];
        try{
            $q = $this->db->prepare("SELECT COUNT(1) AS unSeenOrderCount FROM ordersInfo WHERE orderSeenStatus = 'unseen'");
            $q->execute();
            $result = $q->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e){
            $result = [];
        }
        return $result;
    }

    public function getHomePageData(){
        $result = [];
        if(!empty($this->restaurantId)){
            try{
                $q = $this->db->prepare("SELECT COUNT(1) as new_order_count FROM order_info WHERE orderSeenStatus = 'unseen' AND restaurantId = :resId");
                $q1 = $this->db->prepare("SELECT COUNT(1) as order_count FROM order_info WHERE DATE(`time`) = CURDATE() AND restaurantId = :resId");
                $q2 = $this->db->prepare("SELECT COUNT(1) as user_count FROM user_info WHERE DATE(`time`) = CURDATE() AND restaurantId = :resId");
                $q3 = $this->db->prepare("SELECT COUNT(1) as occupied_tables FROM table_info WHERE NOT tableStatus = 'Available' AND restaurantId = :resId");
                $q->bindParam(':resId', $this->restaurantId);
                $q->execute();
                $q1->bindParam(':resId', $this->restaurantId);
                $q1->execute();
                $q2->bindParam(':resId', $this->restaurantId);
                $q2->execute();
                $q3->bindParam(':resId', $this->restaurantId);
                $q3->execute();
                $result = [
                    'new_order_count' => $q->fetch(PDO::FETCH_ASSOC)['new_order_count'], 
                    'order_count' =>  $q1->fetch(PDO::FETCH_ASSOC)['order_count'], 
                    'user_count' => $q2->fetch(PDO::FETCH_ASSOC)['user_count'], 
                    'table_count' => $q3->fetch(PDO::FETCH_ASSOC)['occupied_tables']
                ];
            }catch(Exception $e){
                die($e->getMessage());
            }
        }
        return $result;
    }

    public function getGraphData(){
        $res = [];
        try{
            $q = $this->db->prepare("SELECT 
                    SUM(order_items.itemCount) as itemCount, 
                    menu_items.name FROM order_items 
                    LEFT JOIN menu_items ON 
                    menu_items.id = order_items.itemId 
                    WHERE order_items.restaurantId = :resId GROUP BY order_items.itemId");
            $q->bindParam(':resId', $this->restaurantId);
            $q->execute();

            $q1 = $this->db->prepare("SELECT COUNT(1) as orderCount, DATE(time) mydate FROM order_info WHERE restaurantId = :resId GROUP BY mydate LIMIT 14");
            $q1->bindParam(':resId', $this->restaurantId); 
            $q1->execute();

            $q2 = $this->db->prepare("SELECT SUM(totalPriceWithTax) AS totalPriceWithTax, DATE(time) FROM order_info WHERE restaurantId = :resId GROUP BY DATE(time)");
            $q2->bindParam(':resId', $this->restaurantId);
            $q2->execute();
            
            $q3 = $this->db->prepare("SELECT SUM(order_items.itemCount) AS menuCount, menu_items.name FROM order_items LEFT JOIN menu_items ON menu_items.id = order_items.itemId WHERE order_items.restaurantId = :resId AND order_items.orderId IN (SELECT orderId FROM order_info WHERE restaurantId = :resId AND DATE(time) = SYSDATE()) GROUP BY order_items.itemId");
            $q3->bindParam(':resId', $this->restaurantId);
            $q3->execute();

            $res["menu_performance"] = $q->fetchAll(PDO::FETCH_ASSOC);
            $res['restaurant_daily_orders'] = $q1->fetchAll(PDO::FETCH_ASSOC);
            $res['daily_revenue'] = $q2->fetchAll(PDO::FETCH_ASSOC);
            $res['today_menu_performance'] = $q3->fetchAll(PDO::FETCH_ASSOC);
            
        }catch(Exception $e){
            die($e);
        }
        return $res;
    }

    public function updateOrderStatus($status, $tableNo){
        $res = [false, ""];
        if(!empty($this->restaurantId)){
            try{
                $username = $_SESSION['admin_user']['username'];
                if(!empty($username)){
                    $q = $this->db->prepare("UPDATE table_info SET tableStatus = :tableStatus WHERE tableNumber = :tableNo AND restaurantId = :resId");
                    $q->bindParam(':tableStatus', $status);
                    $q->bindParam(':tableNo', $tableNo);
                    $q->bindParam(':resId', $this->restaurantId);
                    if($q->execute()){
                        $res = [true, "success"];
                    }else{
                        $res = [false, "failed"];
                    }
                }else{
                    $res = [false, "failed"];
                }
            }catch(Exception $e){
                $res = [false, explode(': ',$e->getMessage())[2]];
            }
        }
        return $res;
    }

    public function acceptOrder($orderId, $tableNo){
            if(!empty($this->restaurantId)){
                try{    
                    $q = $this->db->prepare("UPDATE order_info SET orderStatus = 'Cooking' WHERE orderId = :orderId");
                    $q1 = $this->db->prepare("UPDATE table_info SET tableStatus = 'Cooking' WHERE restaurantId = :resId AND tableNumber = :tableNo");
                    $q->bindParam(':orderId', $orderId);
                    $q1->bindParam(':tableNo', $tableNo);
                    $q1->bindParam(':resId', $this->restaurantId);
                    if($q->execute() && $q1->execute()){
                        return true;
                    }
                }catch(Exception $e){
                    $this->throwError($e);
                }
            }
        return false;
    }

    public function declineOrder($orderId, $tableNo){
        if(!empty($this->restaurantId)){
            try{    
                $q = $this->db->prepare("UPDATE order_info SET orderStatus = 'Declined' WHERE orderId = :orderId");
            $q1 = $this->db->prepare("UPDATE table_info SET tableStatus = 'Declined' WHERE restaurantId = :resId AND tableNumber = :tableNo");
                $q->bindParam(':orderId', $orderId);
                $q1->bindParam(':tableNo', $tableNo);
                $q1->bindParam(':resId', $this->restaurantId);
                if($q->execute() && $q1->execute()){
                    return true;
                }
            }catch(Exception $e){
                $this->throwError($e);
            }
        }
    return false;
    }
}