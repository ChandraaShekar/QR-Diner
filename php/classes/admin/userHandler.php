<?php

require_once "Admin.php";

class UserHandler extends Admin {
    public $restaurantId = "";
    public function __construct(){
        parent::__construct();
        $this->validateLogin();
        $this->restaurantId = $_SESSION['admin_user']['restaurantId'];
    }

    public function getUsers(){
        $users = [];
        try{
            $q = $this->db->prepare("SELECT * FROM users");
            $q->execute();
            $users = $q->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e){
            die($e->getMessage());
        }
        return $users;
    }

    public function getMoreInfo($uid){
        $userInfo = [];
        try{
            $q = $this->db->prepare("SELECT * FROM users WHERE uid = :uid");
            $q->bindParam(":uid", $uid);
            $q->execute();
            $userInfo["common_info"] = $q->fetch(PDO::FETCH_ASSOC);
            $q1 = $this->db->prepare("SELECT * FROM orders WHERE userId = :userId");
            $q1->bindParam(":userId", $uid);
            $q1->execute();
            $orders = $q1->fetchAll();
            $userInfo['orderInfo'] = $orders;
        }catch(Exception $e){
            die($e->getMessage());
        }
        return $userInfo;
    }

    public function getFeedbackList(){
        $feedbackInfo = [];
        if(!empty($this->restaurantId)){
            try{
                $q = $this->db->prepare("SELECT restaurantRating, restaurantReview, time FROM feedback WHERE restaurantId = :resId");
                $q->bindParam(':resId', $this->restaurantId);
                $q->execute();
                $q1 = $this->db->prepare("SELECT AVG(restaurantRating) as avgResRating, COUNT(restaurantRating) as totalrestaurantRating FROM feedback WHERE restaurantId = :resId");
                $q1->bindParam(":resId", $this->restaurantId);
                $q1->execute();
                $feedbackInfo = ['ratings' => $q->fetchAll(PDO::FETCH_ASSOC), 'avgs' => $q1->fetchAll(PDO::FETCH_ASSOC)];
            }catch(Exception $e){
                die($e->getMessage());
            }
        }
        return $feedbackInfo;
    }

    public function changePassword($username, $oldPass, $newPass){
        $status = [false, ""];
        if(!empty($this->restaurantId)){
            try{
                $q = $this->db->prepare("SELECT `password` FROM res_admins WHERE username = :username AND restaurantId = :resId");
                $q->bindParam(":username", $username);
                $q->bindParam(':resId', $this->restaurantId);
                $q->execute();
                $olPass = $q->fetch(PDO::FETCH_ASSOC)['password'];
                if($oldPass == $olPass){
                    $q1 = $this->db->prepare("UPDATE res_admins SET password = :newPass WHERE username = :username AND restaurantId = :resId");
                    $q1->bindParam(':newPass', $newPass);
                    $q1->bindParam(':username', $username);
                    $q1->bindParam(':resId', $this->restaurantId);
                    if($q1->execute()){
                        $status = [true, 'success'];
                    }else{
                        $status = [false, 'Techinical Problem, Try later'];
                    }
                }else{
                    $status = [false, 'Old Password mush match the existing password'];
                }
            }catch(Exception $e){
                $status = [false, explode(": ", $e->getMessage())[2]];
            }
        }
        return $status;
    }

    public function addNewAdmin($username, $password, $accessType){
        $status = [false, ""];
        if(!empty($this->restaurantId)){
            try{
                $my_access = $_SESSION['admin_user']['accessType'];
                $myUsername = $_SESSION['admin_user']['username'];
                if($my_access != "1"){
                    return [false, "You cannot add a user"];
                }
                $check = $this->db->prepare("SELECT 
                        COUNT(1) AS users FROM res_admins WHERE 
                        username = :username AND restaurantId = :resId");
                $check->bindParam(":resId", $this->restaurantId);
                $check->bindParam(':username', $username);
                $check->execute();
                if($my_access == "1"){
                    $myPassVerify = $this->db->prepare("SELECT `password` FROM res_admins WHERE username = :username AND restaurantId = :resId");
                    $myPassVerify->bindParam(':resId', $this->restaurantId);
                    $myPassVerify->bindParam(':username', $username);
                    $myPassVerify->execute();
                    if(!empty($myUsername)){
                        if($check->fetch(PDO::FETCH_ASSOC)['users'] == 0){
                            $q = $this->db->prepare("INSERT INTO res_admins (`restaurantId`,`username`, `password`, `accessType`, `addedBy`) VALUES (:resId, :username, :password, :accessType, :addedBy)");
                            $q->bindParam(':resId', $this->restaurantId); 
                            $q->bindParam(':username', $username); 
                            $q->bindParam(':password' ,$password);
                            $q->bindParam(':accessType', $accessType);
                            $q->bindParam(':addedBy', $myUsername);
                            if($q->execute()){
                                $status = [true, "A new user has been added."]; 
                            }else{
                                
                                $status = [false, "Techinical Problem. Try again later."];
                            }
                        }else{
                            $status = [false, "Username already exists try another."];
                        }
                    }else{
                        $status = [false, "Wrong password. Retry with correct Password."];
                    }
                }else{
                    $status = [false, "You do not have permission to add a user."];
                }
            }catch(Exception $e){
                $status = [false, explode(": ", $e->getMessage())[2]];
            }
        }
        return  $status;
    }

    public function getAccessTypes(){
        $res = [];
        try{
            $q = $this->db->prepare("SELECT * FROM res_access_types");
            $q->execute();
            $res = $q->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e){
            die($e->getMessage());
        }
        return $res;
    }

    public function getAdminUsers(){
        $res = [];
        if(!empty($this->restaurantId)){
            try{
                $q = $this->db->prepare("SELECT 
                    res_admins.*, 
                    res_access_types.name as accessName 
                    FROM res_admins LEFT JOIN 
                    res_access_types ON 
                    res_access_types.id = res_admins.accessType WHERE res_admins.restaurantId = :resId");
                $q->bindParam(':resId', $this->restaurantId);
                $q->execute();
                $res = $q->fetchAll(PDO::FETCH_ASSOC);
            }catch(Exception $e){
                die($e->getMessage());
            }
        }
        return $res;
    }

    public function deleteAdminUser($username){
        $res = false;
        if(!empty($this->restaurantId)){
            try{
                if($_SESSION['admin_user']['accessType'] == "1"){
                    $q = $this->db->prepare("DELETE FROM res_admins WHERE username = :username AND restaurantId = :resId");
                    $q->bindParam(':resId', $this->restaurantId);
                    $q->bindParam(':username', $username);
                    $res = $q->execute();
                }
            }catch(Exception $e){
                $res = false;
            }

        }
        return $res;
    }
}