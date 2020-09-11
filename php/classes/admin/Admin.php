<?php

require 'db.php';
session_start();
class Admin{
    public $username;
    public $password;
    protected $db;

    public function __construct(){
        $conn = new AdminDB();
        $this->db = $conn->connect();
    }

    public function validateLogin(){
        if(!isset($_SESSION['admin_user']) || empty($_SESSION['admin_user'])){
            header("Location: /logout");
            die();
        }
    }

    public function login($res_name, $username, $password){
        $status = [false, ''];
        try{
            $this->username = $username;
            $this->password = $password;
            $q = $this->db->prepare("SELECT 
                    restaurant_basic_info.*, 
                    res_admins.*, 
                    COUNT(1) as adminCount 
                    FROM res_admins LEFT JOIN restaurant_basic_info ON 
                    restaurant_basic_info.restaurantId = res_admins.restaurantId WHERE 
                    (res_admins.username = :uname OR res_admins.email = :uname) AND 
                    res_admins.password = :upass AND 
                    restaurant_basic_info.domainName = :resName
                ");
            $q->bindParam(":uname", $this->username);
            $q->bindParam(":upass", $this->password);
            $q->bindParam(":resName", $res_name);
            $q->execute();
            $res = $q->fetch(PDO::FETCH_ASSOC);
            // print_r($res);
            // die("\n$res_name");
            if($res['adminCount'] == "1"){
                $_SESSION['admin_user'] = $res;
                $status = [true, 'success'];
            }else{
                $status = [false, 'failed'];
                $_SESSION['admin_user'] = null;
            }
        }catch(Exception $e){
            $status = false;
            die($e->getMessage());
        }
        return $status;
    }
}
