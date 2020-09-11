<?php

require_once "Admin.php";

class TableHandler extends Admin {
    public $restaurantId = "";
    public function __construct(){
        parent::__construct();
        $this->restaurantId = $_SESSION['admin_user']['restaurantId'];
        $this->validateLogin();
    }
    
    public function addNewTable($tableNumber, $tableCode, $longUrl, $shortUrl){
        $status = [false, ""];
    // Access Token: ff5d0b5e0035b542e915aa1676a7294cebb25d6d
    
        try{
            $imagesPath = "QRImages/";
            $file = $tableCode.".png";
            $filepath = $imagesPath . $file;
            $ecc = "L";
            $pixel_size = 10;
            $frame_size = 10;
            $q = $this->db->prepare("INSERT INTO table_info 
                (restaurantId, tableNumber, tableCode, qrLocation, longUrl, shortUrl) VALUES 
                (:resId, :tableNo, :tableCode, :qrLocation, :longUrl, :shortUrl)");
            $q->bindParam(':resId', $this->restaurantId);
            $q->bindParam(':tableNo', $tableNumber);
            $q->bindParam(':tableCode', $tableCode);
            $q->bindParam(':qrLocation', $file);
            $q->bindParam(':longUrl', $longUrl);
            $q->bindParam(':shortUrl', $shortUrl);
            $status[0] = $q->execute();
        }catch(Exception $e){
            $status = [false, explode(": ", $e->getMessage())[2]];
        }
        return $status;
    }

    // public function deleteTable($tableNo){
    //     try{

    //     }
    // }

    public function getTables(){
        $tables = [];
        if(!empty($this->restaurantId)){
            try{
                $q = $this->db->prepare("SELECT table_info.* FROM table_info WHERE restaurantId = :resId");
                $q->bindParam(':resId', $this->restaurantId);
                $q->execute();
                $q2 = $this->db->prepare("SELECT * FROM order_info WHERE userId IN (SELECT occupiedUser FROM table_info) AND restaurantId = :resId");
                $q2->bindParam(':resId', $this->restaurantId);
                $q2->execute();
                $tables = ["table_info" => $q->fetchAll(PDO::FETCH_ASSOC), "order_info" => $q2->fetchAll(PDO::FETCH_ASSOC)];
            }catch(Exception $e){
                die($e->getMessage());
            }
        }
        return $tables;
    }
    public function getTableInfo($tableNo){
        $tableInfo = [];
        try{
            $q = $this->db->prepare("SELECT *
            FROM table_info 
            WHERE tableNumber = :tableNo AND restaurantId = :resId");
            $q->bindParam(":resId", $this->restaurantId);
            $q->bindParam(":tableNo", $tableNo);
            $q->execute();
            $tableInfo = $q->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e){
            die($e->getMessage());
        }
        return $tableInfo;
    }
    public function DeleteTable($tableNumber){
        $status = [false, ""];
        try{
            $q = $this->db->prepare("DELETE FROM table_info WHERE tableNumber = :tableNumber AND restaurantId = :resId");
            $q->bindParam(':resId', $this->restaurantId);
            $q->bindParam(":tableNumber", $tableNumber);
            if($q->execute()){
                $status = [true, "success"];
            }else{
                $status = [false, "Failed to Delete the table"];
            }
        }catch(Exception $e){
            $status = [false, explode(": ", $e->getMessage())[2]];
        }
    }
    public function updateTable(){}

    public function enableTable($tableNo){
        $status = [false, ""];
        try{
            $q = $this->db->prepare("UPDATE table_info SET tableStatus = 'Available', occupiedUser = '' WHERE tableNumber = :tableNo AND restaurantId = :resId");
            $q->bindParam(':resId', $this->restaurantId);
            $q->bindParam(":tableNo", $tableNo);
            if($q->execute()){
                $status = [true, ""];
            }else{
                $status = [false, "failed"];
            }
        
        }catch(Exception $e){
            $status = [false, explode(": ", $e->getMessage())[2]];
        }
        return $status;
    }

    public function getTableData($tableNo){
        $result = [];
        /*
        $result 
        -> tableStatus
        -> paymentStatus
        -> OrderInfo
        */
        try{
            $q = $this->db->prepare("SELECT *, COUNT(1) as tableCheck FROM table_info WHEre tableNumber = :tableNo AND restaurantId = :resId");
            $q->bindParam(":tableNo", $tableNo);
            $q->bindParam(':resId', $this->restaurantId);
            $q->execute();
            $tableData = $q->fetch(PDO::FETCH_ASSOC);
            if($tableData['tableCheck'] == "1"){
                if($tableData['tableStatus'] == "Available"){
                    $result = [
                        "requestStatus" => true, 
                        "response" => "success", 
                        "tableStatus" => $tableData['tableStatus'],
                        "orderData" => []
                    ];
                }elseif($tableData['tableStatus'] == "Occupied"){
                    $result = [
                        "requestStatus" => true,
                        "response" => "success", 
                        "tableStatus" => $tableData['tableStatus'],
                        "orderData" => []];
                }else{
                    $q1 = $this->db->prepare("SELECT * FROM order_info  WHERE userId = :userId ORDER BY time DESC");
                    $q1->bindParam(":userId", $tableData['occupiedUser']);
                    $q1->execute();
                    $order_info = [];
                    while ($row = $q1->fetch(PDO::FETCH_ASSOC)){
                        $q2 = $this->db->prepare("SELECT order_items.*, menu_items.name FROM order_items LEFT JOIN menu_items ON menu_items.id = order_items.itemId WHERE order_items.orderId = :orderId");
                        $q2->bindParam(":orderId", $row['orderId']);
                        $q2->execute();
                        $order_info[] = ["order_info" => $row, "order_items" => $q2->fetchAll(PDO::FETCH_ASSOC)];
                    }
                    $result = [
                        "requestStatus" => true, 
                        "response" => "success", 
                        "tableStatus" => $tableData['tableStatus'],
                        "orderData" => $order_info
                    ];
                }
            }else{
                $result = [
                    "requestStatus" => false, 
                    "response" => "Unknown Table", 
                    "tableStatus" => "unknown",
                    "orderData" => []];
            }
        }catch(Exception $e){
            $result = [
                "requestStatus" => false, 
                "response" => explode(": ", $e->getMessage())[2], 
                "tableStatus" => "unknown",
                "orderData" => []];
        }
        return $result;
    }

    public function disableTable($tableNumber){
        $res = false;
        try{
            $q = $this->db->prepare("UPDATE table_info SET tableStatus = 'disabled' WHERE tableNumber = :tableNo AND restaurantId = :resId");
            $q->bindParam(':tableNo', $tableNumber);
            $q->bindParam(':resId', $this->restaurantId);
            $res = $q->execute();
        }catch(Exception $e){
            $res = false;
        }
        return $res;
    }
}