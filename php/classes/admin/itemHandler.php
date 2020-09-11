<?php

require_once "Admin.php";

class ItemHandler extends Admin{
    public $restaurantId = "";
    public function __construct(){
        parent::__construct();
        $this->validateLogin();
        $this->restaurantId = $_SESSION['admin_user']['restaurantId'];
    }

    public function addNewItem($name, $desc, $originalPrice, $offerPrice, $imgName, $category){
        $status = [false, ""];
        $itemStatus = "available";
        if(!empty($this->restaurantId)){
            try{
                $imgPath = WEBSITE_URL ."/$imgName";
                $q = $this->db->prepare("INSERT INTO menu_items (restaurantId, name, description, price, offerPrice, itemImage, category, status) VALUES (:resId, :name, :description, :price, :offerPrice, :imgLink, :category, :status)");
                $q->bindParam(":resId", $this->restaurantId);
                $q->bindParam(":name", $name);
                $q->bindParam(":description", $desc);
                $q->bindParam(":price", $originalPrice);
                $q->bindParam(":offerPrice", $offerPrice);
                $q->bindParam(":imgLink", $imgPath);
                $q->bindParam(":category", $category);
                $q->bindParam(":status", $itemStatus);
                $status[0] = $q->execute();
            }catch(Exception $e){
                $status[0] = false;
                $status[1] = explode(": ",$e->getMessage())[2];
            }
        }else{
            $status = [false, "failed to login"];
        }
        return $status;
    }

    public function getItemList(){
        $itemsList = [];
        if(!empty($this->restaurantId)){
            try {
                $q = $this->db->prepare("SELECT menu_items.*, menu_categories.name AS categoryName 
                        FROM menu_items LEFT JOIN 
                        menu_categories ON menu_categories.id = menu_items.category 
                        WHERE menu_items.restaurantId = :resId");
                $q->bindParam(":resId", $this->restaurantId);
                $q->execute();
                $itemsList = $q->fetchAll(PDO::FETCH_ASSOC);
            }catch(Exception $e){
                die($e->getMessage());
            }
        }
        return $itemsList;
    }

    public function getItemInfo($itemCode){
        $itemInfo = [];
        try {
            $q = $this->db->prepare("SELECT 
                    menu_items.*, menu_categories.name AS categoryName, menu_categories.id AS categoryId 
                    FROM menu_items LEFT JOIN menu_categories 
                    ON menu_categories.id = menu_items.category 
                    WHERE menu_items.id = :id");
            $q->bindParam(':id', $itemCode);
            $q->execute();
            $itemInfo = $q->fetchAll();
        }catch(Exception $e){
            die($e->getMessage());
        }
        return $itemInfo;
    }

    public function updateItemInfo($itemId, $name, $desc, $originalPrice, $offerPrice, $itemImage, $category, $itemStatus){
        $status = [false, ""];
        try{
            $image = WEBSITE_URL . $itemImage;
            // die($image);
            $q = $this->db->prepare("UPDATE menu_items SET 
                    name = :name, 
                    description = :description,
                    itemImage = :itemImage, 
                    price = :price, 
                    offerPrice = :offerPrice, 
                    category = :category, 
                    status = :status 
                    WHERE id = :id");
            $q->bindParam(':id', $itemId);
            $q->bindParam(":name", $name);
            $q->bindParam(":description", $desc);
            $q->bindParam(":itemImage", $image);
            $q->bindParam(":price", $originalPrice);
            $q->bindParam(":offerPrice", $offerPrice);
            $q->bindParam(":category", $category);
            $q->bindParam(":status", $itemStatus);
            $status[0] = $q->execute();
        }catch(Exception $e){
            $status[1] = explode(": ", $e->getMessage());
            $status[0] = false;
        }
        return $status;
    }
}