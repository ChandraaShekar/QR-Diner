<?php

require_once 'Admin.php';

class OthersHandler extends Admin {
    public $restaurantId;
    public function __construct() {
        parent::__construct();
        $this->restaurantId = $_SESSION['admin_user']['restaurantId'];
        $this->validateLogin();
    }

    public function addCategory($itemName) {
        $status = [false, ""];
        try{
            $q1 = $this->db->prepare("SELECT COUNT(1) AS categoryCount FROM menu_categories WHERE restaurantId = :resId AND name = :itemName");
            $q1->bindParam(':resId', $this->restaurantId);
            $q1->bindParam(':itemName', $itemName);
            $q1->execute();
            if($q1->fetch(PDO::FETCH_ASSOC)['categoryCount']){
                $status = [false, "Duplicate Entry"];
            }else{
                $q = $this->db->prepare("INSERT INTO menu_categories (`name`, `restaurantId`) VALUES (:itemName, :resId)");
                $q->bindParam(":itemName", $itemName);
                $q->bindParam(':resId', $this->restaurantId);
                $status = [$q->execute(), "Success"];   
            }
        }catch(Exception $e){
            $status[0] = false;
            $status[1] = explode(": " ,$e->getMessage())[2];
            // die($e->getMessage());
        }
        return $status;
    }

    public function getFromTable($tableName){
        $items = [];
        if(!empty($this->restaurantId)){
            try{
                $q = $this->db->prepare("SELECT * FROM $tableName WHERE restaurantId = :resId OR restaurantId = 'default'");
                $q->bindParam(':resId', $this->restaurantId);
                $q->execute();
                $items = $q->fetchAll(PDO::FETCH_ASSOC);
            }catch(Exception $e){
                die($e->getMessage());
            }
        }
        return $items;
    }

    public function removeFromTable($tableName, $columnName, $id){
        $status = false;
        try { 
            $q = $this->db->prepare("DELETE FROM $tableName WHERE $columnName = :id");
            // $q->bindColumn(':colName', $columnName);
            $q->bindParam(':id', $id);
            $status = $q->execute();
        }catch(Exception $e){
            die($e->getMessage());
        }
        return $status;
    }

    // public function deleteCategory($id){
    //     $status = false;
    //     try {
    //         $q = $this->db-.prepare("");
    //     }catch(Exception $e){

    //     }
    // }
}
