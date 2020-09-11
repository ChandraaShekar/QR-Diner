<?php

require 'dbHandler.php';
session_start();

class Admin{
    public $username;
    public $password;
    protected $db;
    public $adminInfo;
    public function __construct(){
        $conn = new DB();
        $this->db = $conn->connect();
    }

    public function validateLogin(){
        if(!isset($_SESSION['admin_user']) || empty($_SESSION['admin_user'])){
            header("Location: logout.php");
            die();
        }
    }

    public function login($username, $password){
        $status = [false, ''];
        try{
            $this->username = $username;
            $this->password = $password;
            $q = $this->db->prepare("SELECT *, COUNT(1) as adminCount FROM admin_users WHERE username = :uname AND password = :upass");
            $q->bindParam(":uname", $this->username);
            $q->bindParam(":upass", $this->password);
            $q->execute();
            $res = $q->fetch(PDO::FETCH_ASSOC);
            // print_r($res);
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

?>