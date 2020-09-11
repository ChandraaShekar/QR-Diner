<?php

require_once "Main.php";

class MenuHandler extends Main {

    public $restaurantId = "";

    public function __construct(){
        parent::__construct();
        $this->restaurantId = $_SESSION['user']['restaurantId'];
        // die($this->restaurantId);
    }

    public function getMenuItems($restaurantId){
        $result = [];
        try{
            $q = $this->db->prepare("SELECT * FROM menu_categories WHERE id IN (SELECT DISTINCT category FROM menu_items WHERE restaurantId = :restaurantId)");
            $q->bindParam(':restaurantId', $restaurantId);
            $q->execute();
            while($row = $q->fetch(PDO::FETCH_ASSOC)){
                $categoryId = $row["id"];
                $q2 = $this->db->prepare("SELECT * FROM menu_items WHERE restaurantId = :restaurantId AND category = '$categoryId'");
                $q2->bindParam(":restaurantId", $restaurantId);
                $q2->execute();
                $result[$row['name']] = $q2->fetchAll(PDO::FETCH_ASSOC);
            }
        }catch(Exception $e){
            die($e->getMessage());
        }
        return $result;
    }

    public function getDrinks(){
        $drinks = [];
        if(!empty($this->restaurantId)){   
            try{
                $q = $this->db->prepare("SELECT * FROM menu_items WHERE category = '1' AND restaurantId = :resId");
                $q->bindParam(':resId', $this->restaurantId);
                if($q->execute()){
                    $drinks = $q->fetchAll(PDO::FETCH_ASSOC);
                }
            }catch(Exception $e){
                die($e->getMessage());
            }
        }
        return $drinks;
    }

    public function getItemPrice($itemId){
        $result = [];
        try{
            $q = $this->db->prepare("SELECT price, offerPrice FROM menu_items WHERE id = :itemId");
            $q->bindParam(':itemId', $itemId);
            $q->execute();
            $result = $q->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e){
            die($e->getMessage());
        }
        return $result;
    }
}