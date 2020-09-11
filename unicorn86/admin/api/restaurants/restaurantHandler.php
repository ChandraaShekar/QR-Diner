<?php

require_once "../classes/api.php";

class RestaurantHandler extends Api{

    private $restaurantName;
    private $shippingAddress;
    private $city;
    private $state;
    private $zip;
    private $contact_number;
    private $email;

    public function __construct(){
        parent::__construct('');
    }

    public function addRestaurant(){
        try{
            $this->restaurantName = $this->validateParameter('restaurant name', $this->param['restaurant_name'], STRING);
            $this->shippingAddress = $this->validateParameter('Shipping Address', $this->param['shipping_address'], STRING);
            $this->city = $this->validateParameter('city', $this->param['city'], STRING);
            $this->state = $this->validateParameter('state', $this->param['state'], STRING);
            $this->zip = $this->validateParameter('zip', $this->param['zip'], STRING);
            $this->contact_number = $this->validateParameter('Contact Number', $this->param['contact_number'], STRING);
            $this->email = $this->validateParameter('Email', $this->param['email'], STRING);
            $this->uid = $this->validateParameter('uid', $this->param['uid'], STRING);

            $q = $this->db->prepare("SELECT * FROM restaurant_info WHERE created_by = :uid");
            $q->bindParam(':uid', $this->uid);
            $q->execute();
            $restaurant_info = $q->fetch(PDO::FETCH_ASSOC);
            if(is_array($restaurant_info)){
                $this->throwError(DUPLICATE_DATA, "This user has already created the Restaurant Profile");
            }else{
                $q2 = $this->db->prepare("INSERT INTO restaurant_info (
                    restaurant_name, 
                    created_by, 
                    shipping_address, 
                    city, 
                    state,
                    zip,
                    contact_number,
                    email_id) 
                    VALUES(:restaurant_name, :created_by, 
                    :shipping_address, :city, 
                    :state, :zip, 
                    :contact_number, :email_id)");
                $q2->bindParam(':restaurant_name', $this->restaurantName);
                $q2->bindParam(':created_by', $this->uid);
                $q2->bindParam(':shipping_address', $this->shippingAddress);
                $q2->bindParam(':city', $this->city);
                $q2->bindParam(':state', $this->state);
                $q2->bindParam(':zip', $this->zip);
                $q2->bindParam(':contact_number', $this->contact_number);
                $q2->bindParam(':email_id', $this->email);
                if($q2->execute()){
                    $q3 = $this->db->prepare("SELECT id AS restaurant_id FROM restaurant_info WHERE created_by = :uid");
                    $q3->bindParam(':uid', $this->uid);
                    $q3->execute();
                    $restaurant_info = $q3->fetch(PDO::FETCH_ASSOC);
                    $q4 = $this->db->prepare("UPDATE users SET restaurant_id = :restaurant_id WHERE uid = :uid");
                    $q4->bindParam(':restaurant_id', $restaurant_info['restaurant_id']);
                    $q4->bindParam(':uid', $this->uid);
                    $q4->execute();
                    $this->returnResponse(SUCCESS_RESPONSE, $restaurant_info);
                }else{
                    $this->throwError(QUERY_FAILED, "Failed to Add the restaurant");
                }

            }

        }catch(Exception $e){
            $this->throwError(EXCEPTIONS, $e->getMessage());
        }
    }

    public function getRestaurant(){
        try{
            $this->uid = $this->validateParameter('uid', $this->param['uid'], STRING);
            $q = $this->db->prepare("SELECT * FROM restaurant_info WHERE created_by = :uid");
            $q->bindParam(':uid', $this->uid);
            $q->execute();
            $restaurant_info = $q->fetch(PDO::FETCH_ASSOC);
            if(!is_array($restaurant_info)){
                $this->throwError(INVALID_UID_PHONE, "Invalid user details");
            }else{
                $this->returnResponse(SUCCESS_RESPONSE, $restaurant_info);
            }
        }catch(Exception $e){
            $this->throwError(EXCEPTIONS, $e->getMessage());
        }
    }
    
    public function updateRestaurant(){
        try{
            $this->restaurantName = $this->validateParameter('restaurant name', $this->param['restaurant_name'], STRING);
            $this->shippingAddress = $this->validateParameter('Shipping Address', $this->param['shipping_address'], STRING);
            $this->city = $this->validateParameter('city', $this->param['city'], STRING);
            $this->state = $this->validateParameter('state', $this->param['state'], STRING);
            $this->zip = $this->validateParameter('zip', $this->param['zip'], STRING);
            $this->contact_number = $this->validateParameter('Contact Number', $this->param['contact_number'], STRING);
            $this->email = $this->validateParameter('Email', $this->param['email'], STRING);
            $this->uid = $this->validateParameter('uid', $this->param['uid'], STRING);
            $q = $this->db->prepare("UPDATE restaurant_info SET restaurant_name = :res_name, shipping_address = :shipping_address, city = :city, state = :state, zip=:zip, contact_number = :contact_number, email_id = :email WHERE created_by = :uid");
            $q->bindParam(':res_name', $this->restaurantName);
            $q->bindParam(':shipping_address', $shippingAddress);
            $q->bindParam(':city', $this->city);
            $q->bindParam(':state', $this->state);
            $q->bindParam(':zip', $this->zip);
            $q->bindParam(':contact_number', $this->contact_number);
            $q->bindParam(':email', $this->email);
            $q->bindParam(':uid', $this->uid);
            if($q->execute()){
                $this->returnResponse(SUCCESS_RESPONSE, "Successfully updated Restaurant Info");
            }else{
                $this->throwError(QUERY_FAILED, "Error while updating the restaurant Info");
            }
        }catch(Exception $e){
            $this->returnResponse(EXCEPTIONS, $e->getMessage());
        }
    }
}