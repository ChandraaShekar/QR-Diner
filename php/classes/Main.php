<?php

require_once "db.php";

session_start();
class Main {
    protected $db;
    public function __construct(){
        $con = new DB();
        $this->db = $con->connect();
    }

    public function validateLogin(){
        if(isset($_SESSION['admin_user'])){
            return true;
        }
        return false;
    }
    
    public function throwError($e){
        if(APP_TYPE == 'dev'){
            die($e->getMessage());
        }else{
            die("Error: Current Operation has thrown an error Conact Root Admins for more information");
        }
    }

    public function loginUser($email, $password){
        $res = [];
        try{
            $q = $this->db->prepare("SELECT password FROM admin_users WHERE email = :email");
            $q->bindParam(":email", $email);
            if($q->execute()){
                $pass = $q->fetch(PDO::FETCH_ASSOC)['password'];
                if(password_verify(PEPPER_STRING . $password, $pass)){
                    $q1 = $this->db->prepare("SELECT userId, fullName, email, accessType FROM admin_users WHERE email = :email");
                    $q1->bindParam(':email', $email);
                    if($q1->execute()){
                        $_SESSION['admin_info'] = $q1->fetch(PDO::FETCH_ASSOC);
                        $res = [true, "success"];
                    }else{
                        $res = [false, "Failed to Login"];
                    }
                }else{
                    $res = [false, "Failed to Login"];
                }
            }else{
                $res = [false, "Failed to login"];
            }
        }catch(Exception $e){
            $this->throwError($e);
        }
        return $res;
    }

    public function getAllowedIps($domainName, $ipaddress){
        $allowStatus = false;
        try{
            $q = $this->db->prepare("SELECT COUNT(1) as ip FROM ip_white_list WHERE restaurantId IN (SELECT restaurantId FROM restaurant_basic_info WHERE domainName = :domainName) AND ipAddress = :ipAddress");
            $q->bindParam(':ipAddress', $ipaddress);
            $q->bindParam(':domainName', $domainName);
            $q->execute();
            $ips = $q->fetch(PDO::FETCH_ASSOC)['ip'];
            // print_r($q->fetch(PDO::FETCH_ASSOC));
            // var_dump($ips);
            // $ips = "1";
            // die("");
            if($ips != "1"){
                $allowStatus = false;
            }else{
                $allowStatus = true;
            }
        }catch(Exception $e){
            $allowStatus = false;
            die($e->getMessage());
        }
        return $allowStatus;
    }
}
