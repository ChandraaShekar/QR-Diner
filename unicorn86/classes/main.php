<?php

require_once "dbHandler.php";
session_start();
class Main{
    protected $db;

    public function __construct(){
        $conn = new DB();
        $this->db = $conn->connect();
    }

    public function errorResponse($message){
        header("Location: ../error.php");
    }

    public function getAllowedIps($ipaddress){
        $allowStatus = false;
        try{
            $q = $this->db->prepare("SELECT COUNT(1) as ip FROM allowed_ipaddress WHERE ipAddress = :ipAddress");
            $q->bindParam(':ipAddress', $ipaddress);
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