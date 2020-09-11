<?php

require_once "main.php";

class MenuHandler extends Main {
    public function __construct(){
        parent::__construct();
    }

    public function getMenuItems(){
        $result = [];
        try{
            $q = $this->db->prepare("SELECT * FROM categories");
            $q->execute();
            while($row = $q->fetch(PDO::FETCH_ASSOC)){
                $categoryId = $row["id"];
                $q2 = $this->db->prepare("SELECT * FROM menu_items WHERE category = '$categoryId'");
                $q2->execute();
                $result[$row['name']] = $q2->fetchAll(PDO::FETCH_ASSOC);
            }
            // $q = $this->db->prepare("SELECT * FROM menu_items");
            // $q->execute();
            // print_r($result);
            // die();
        }catch(Exception $e){
            die($e->getMessage());
        }
        return $result;
    }

    public function getItemPrice($itemName){
        $result = [];
        try{
            $q = $this->db->prepare("SELECT price, offerPrice FROM menu_items WHERE name = :name");
            $q->bindParam(':name', $itemName);
            $q->execute();
            $result = $q->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e){
            die($e->getMessage());
        }
        return $result;
    }
}