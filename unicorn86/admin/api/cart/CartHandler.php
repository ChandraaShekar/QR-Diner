<?php

require_once "../classes/api.php";

class CartHandler extends Api {

    private $restaurantId;
    private $addedBy;
    private $productCode;
    private $quantity;

    public function __construct(){
        parent::__construct('');
    }

    public function addItem(){
        $this->uid = $this->validateParameter('uid', $this->param['uid'], STRING);
        $this->restaurantId = $this->validateParameter('Restaurant Id', $this->param['restaurant_id'], INTEGER);
        $this->productCode = $this->validateParameter('Product Code', $this->param['product_code'], STRING);
        $this->quantity = $this->validateParameter('Quantity', $this->param['quantity'], INTEGER);
        try{
            $q = $this->db->prepare("SELECT uid FROM users WHERE restaurant_id = :restaurant_id AND access_right = '2'");
            $q->bindParam(':restaurant_id', $this->restaurantId);
            $q->execute();
            $owner_id = $q->fetch(PDO::FETCH_ASSOC)['uid'];
            $q2 = $this->db->prepare("SELECT * FROM common_cart WHERE owner_id = '$owner_id' AND product_code = :product_code");
            $q2->bindParam(':product_code', $this->productCode);
            $q2->execute();
            $product = $q2->fetch(PDO::FETCH_ASSOC);
            if(is_array($product)){
                $new_quantity = $product['quantity'] + $this->quantity;
                $q3 = $this->db->prepare("UPDATE common_cart SET quantity = :new_quantity WHERE product_code = :product_code AND owner_id = '$owner_id'");
                $q3->bindParam(':product_code', $this->productCode);
                $q3->bindParam(':new_quantity', $new_quantity);
                $q3->execute();
                $this->returnResponse(SUCCESS_RESPONSE, 'Successfully updated Cart');
            }
            $q4 = $this->db->prepare("INSERT INTO common_cart (added_by, restaurant_id, owner_id, product_code, quantity) VALUES (:addedBy, :restaurantId, '$owner_id', :productCode, :quantity)");
            $q4->bindParam(':addedBy', $this->uid);
            $q4->bindParam(':restaurantId', $this->restaurantId);
            $q4->bindParam(':productCode', $this->productCode);
            $q4->bindParam(':quantity', $this->quantity);
            if($q4->execute()){
                $this->returnResponse(SUCCESS_RESPONSE, "Added the Item to the Cart");
            }else{
                $this->throwError(QUERY_FAILED, "Failed to add the item to Cart");
            }
            
        }catch(Exception $e){
            $this->throwError(EXCEPTIONS, $e->getMessage());
        }
    }

    public function removeItem(){
        $this->uid = $this->validateParameter('uid', $this->param['uid'], STRING);
        $this->productCode = $this->validateParameter('Product Code', $this->param['product_code'], STRING);
        try{
            if(strtolower($this->productCode) != 'all'){
                $q = $this->db->prepare("DELETE FROM common_cart WHERE product_code = :product_code AND (added_by = :uid OR  owner_id = :uid)");
                $q->bindParam(':product_code', $this->productCode);
                $q->bindParam(':uid', $this->uid);
                if($q->execute()){
                    $this->returnResponse(SUCCESS_RESPONSE, "Deleted Item from Cart");
                }else{
                    $this->throwError(QUERY_FAILED, "Failed to delete Item from Cart");
                }
            }else{
                $q = $this->db->prepare("DELETE FROM common_cart WHERE added_by = :uid OR owner_id = :uid");
                $q->bindParam(':uid', $this->uid);
                if($q->execute()){
                    $this->returnResponse(SUCCESS_RESPONSE, "Deleted all Items from Cart.");
                }else{
                    $this->throwError(QUERY_FAILED, "Failed to delete Items from Cart.");
                }
            }
        }catch(Exception $e){
            $this->throwError(EXCEPTIONS, $e->getMessage());
        }
    }

    public function updateCart(){
        $this->uid = $this->validateParameter('uid', $this->param['uid'], STRING);
        $this->productCode = $this->validateParameter('Product Code', $this->param['product_code'], STRING);
        $this->quantity = $this->validateParameter('Quantity', $this->param['quantity'], INTEGER);
        try{
            $q = $this->db->prepare("UPDATE common_cart SET quantity = :quantity WHERE product_code = :product_code AND (added_by = :uid OR owner_id = :uid)");
            $q->bindParam(':quantity', $this->quantity);
            $q->bindParam(':product_code', $this->productCode);
            $q->bindParam(':uid', $this->uid);
            if($q->execute()){
                $this->returnResponse(SUCCESS_RESPONSE, "Successfully Updated Cart");
            }else{
                $this->throwError(QUERY_FAILED, "Failed to updated the cart");
            }

        }catch(Exception $e){
            $this->throwError(EXCEPTIONS, $e->getMessage());
        }
    }

    public function getcart(){
        $this->uid = $this->validateParameter('uid', $this->param['uid'], STRING);
        try{
            $q = $this->db->prepare("SELECT common_cart.quantity, common_cart.added_by, common_cart.note, meta_products.product_name,
            meta_products.product_code,meta_products.price,meta_products.sale_price, product_meta_images.image FROM meta_products 
            LEFT JOIN common_cart ON common_cart.product_code = meta_products.product_code 
            LEFT JOIN product_meta_images ON product_meta_images.product_code = meta_products.product_code
            WHERE common_cart.added_by = :uid OR common_cart.owner_id = :uid GROUP BY meta_products.product_code");
            $q->bindParam(':uid', $this->uid);
            $q->execute();
            $cartItems = [];
            while($item = $q->fetch(PDO::FETCH_ASSOC)){
                $cartItems[] = $item;
            }
            $this->returnResponse(SUCCESS_RESPONSE, $cartItems);
        }catch(Exception $e){
            $this->throwError(EXCEPTIONS, $e->getMessage());
        }
    }

    
}