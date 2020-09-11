<?php

require_once "Admin.php";
session_start();
class AdminHandler extends Admin {
    public $restaurantId = "";
    public function __construct(){
        parent::__construct();
        $this->validateLogin();
        $this->restaurantId = $_SESSION['admin_user']['restaurantId'];
    }

    public function changePassword($oldPass, $newPass){
        $status = [false, ""];
        try{
            $username = $_SESSION['admin_user']['username'];
            $q = $this->db->prepare("SELECT *, COUNT(1) as userCount FROM res_admins WHERE username = :username");
            $q->bindParam(':username', $username);
            $q->execute();
            $user_info = $q->fetch(PDO::FETCH_ASSOC);
            if($user_info['userCount'] == "1"){
                if($user_info['password']==$oldPass){
                    $q1 = $this->db->prepare("UPDATE res_admins SET password = :password WHERE username = :username");
                    $q1->bindParam(":password",$newPass);
                    $q1->bindParam(":username", $username);
                    if($q1->execute()){
                        $status = [true, "success"];
                    }else{
                        $status = [false, "There is some problem Try again later."];
                    }
                }else{
                    $status = [false, "Wrong Password"];
                }
            }else{
                $status = [false, "Unknown Request"];
            }
        }catch(Exception $e){
            $status = [false, explode(": ", $e->getMessage())[2]];
        }
        return $status;
    }

    public function addAdminUser($username, $password, $accessType){
        try{
            
        }catch(Exception $e){
            die($e->getMessage());
        }
    }

    public function getIpAddresses(){
        $res = [];
        try{
            $q = $this->db->prepare("SELECT * FROM ip_white_list WHERE restaurantId = :resId");
            $q->bindParam(':resId', $this->restaurantId);
            $q->execute();
            $res = $q->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e){
            die($e->getMessage());
        }
        return $res;
    }

    public function addIpAddress($ip){
        $res = [false, ""];
        try{
            $username = $_SESSION['admin_user']['username'];
            if(!empty($username)){
                $q = $this->db->prepare("INSERT INTO ip_white_list (restaurantId, ipAddress, addedBy) VALUES (:resId, :ipAddress, :addedBy)");
                $q->bindParam(':resId', $this->restaurantId);
                $q->bindParam(':ipAddress', $ip);
                $q->bindParam(':addedBy', $username);
                if($q->execute()){
                    $res = [true, "Added a New Ip Address"];
                }else{
                    $res = [false, "Failed to add a New Ip Address"];
                }
            }else{
                header("Location: /logout");
            }
            
        }catch(Exception $e){
            $res = [false, explode(": ", $e->getMessage())[2]];
        }
        return $res;
    }

    public function deleteIp($ip){
        $res = [false, ""];
        try{
            if(!empty($ip)){
                $q = $this->db->prepare("DELETE FROM ip_white_list WHERE ipAddress = :ipAddress");
                $q->bindParam(':ipAddress', $ip);
                if($q->execute()){
                    $res = [true, "Deleted the IP Address '$ip'"];
                }else{
                    $res = [false, "Failed to Delete the Ip Address '$ip'"];
                }
            }else{
                header("Location: /logout");
            }
        }catch(Exception $e){
            $res = [false, explode(": ", $e->getMessage())[2]];
        }
        return $res;
    }

    public function getPrinters(){
        $res = [];
        if(!empty($this->restaurantId)){
            try{
                $q = $this->db->prepare("SELECT * FROM printer_info restaurantId = :resId");
                $q->bindParam(':resId', $this->restaurantId);
                $q->execute();
                $res = $q->fetchAll(PDO::FETCH_ASSOC);
            }catch(Exception $e){
                die($e->getMessage());
            }
        }
        return $res;
    }

    public function addNewPrinter($printerName, $ipAddress, $printerSize){
        $res = [false, ""];
        try{
            $username = $_SESSION['admin_user']['username'];
            if(!empty($username)){
                if(!empty($ipAddress) && !empty($printerSize)){
                    $q = $this->db->prepare("INSERT INTO printer_info (printerName, ipAddress, printerSize) VALUES (:printerName, :ipAddress, :printerSize)");
                    $q->bindParam(':printerName', $printerName);
                    $q->bindParam(':ipAddress', $ipAddress);
                    $q->bindParam(':printerSize', $printerSize);
                    if($q->execute()){
                        $res = [true, "Added a New Printer"];
                    }else{
                        $res = [false, "Failed to add a New Printer"];
                    }
                }else{
                    $res = [false, "Failed to add a New Printer. Invalid Printer Details."];
                }
            }else{
                header("Location: logout.php");
            }
            
        }catch(Exception $e){
            $res = [false, explode(": ", $e->getMessage())[2]];
        }
        return $res;
    }

    public function deletePrinter($printerId){
        $res = [false, ""];
        try{
            if(!empty($printerId)){
                $q = $this->db->prepare("DELETE FROM printer_info WHERE id = :printerId");
                $q->bindParam(':printerId', $printerId);
                if($q->execute()){
                    $res = [true, "Deleted the Printer"];
                }else{
                    $res = [false, "Failed to Delete the Printer"];
                }
            }else{
                $res = [false, "Invalid Printer"];
            }
        }catch(Exception $e){
            $res = [false, explode(": ", $e->getMessage())[2]];
        }
        return $res;
    }
}