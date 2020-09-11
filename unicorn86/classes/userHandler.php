<?php

require_once "main.php";
session_start();

class userHandler extends Main {
    public $tableNumber;
    public $tableCode;
    public $time;
    public $hash;
    public $uid;
    
    public function __construct(){
        parent::__construct();
    }

    public function clearTable($tableNumber, $tableCode){
        $bool = false;
        try{ 
            $this->uid = uniqid("USER", true);
            $this->tableNumber = $tableNumber;
            $this->tableCode = $tableCode;
            $q = $this->db->prepare("SELECT *, COUNT(1) as tableCheck FROM table_info WHERE tableNumber = :tableNumber AND tableCode = :tableCode");
            $q->bindParam(":tableNumber", $tableNumber);
            $q->bindParam(":tableCode", $tableCode);
            $q->execute();
            $tableInfo = $q->fetch(PDO::FETCH_ASSOC);
            if($tableInfo['tableCheck'] == '1'){
                $bool = true;
                if($tableInfo['tableStatus'] == 'available'){
                    $q2 = $this->db->prepare("INSERT INTO userInfo (uid, isRoot, subOf, tableNo) VALUES (:uid, 'true', '', :tableNumber)");
                    $q2->bindParam(':uid', $this->uid);
                    $q2->bindParam(':tableNumber', $tableNumber);
                    if($q2->execute()){
                        $q1 = $this->db->prepare("UPDATE table_info SET tableStatus = 'occupied', occupiedUser = :uid WHERE tableNumber = :tableNumber AND tableCode = :tableCode");
                        $q1->bindParam(":uid", $this->uid);
                        $q1->bindParam(":tableNumber", $this->tableNumber);
                        $q1->bindParam(":tableCode", $this->tableCode);
                        if($q1->execute()){
                            $_SESSION['user'] = [
                                'user_info' => [
                                    'uid' => $this->uid,
                                    'isRoot' => 'true',
                                    'subOf' => null
                                ],
                                'table_info' => [
                                    "table_number" => $this->tableNumber,
                                    "table_code" => $this->tableCode
                                ]
                            ];
                        }else{
                            $bool = false;
                        }
                    }else{
                        $bool = false;
                    }
                }else{
                    $q2 = $this->db->prepare("INSERT INTO userInfo (uid, isRoot, subOf, tableNo) VALUES (:uid, 'false', :subOf, :tableNumber)");
                    $q2->bindParam(':uid', $this->uid);
                    $q2->bindParam(':subOf', $tableInfo['occupiedUser']);
                    $q2->bindParam(':tableNumber', $tableNumber);
                    if($q2->execute()){
                        $_SESSION['user'] = [
                            'user_info' => [
                                'uid' => $this->uid,
                                'isRoot' => 'false',
                                'subOf' => $tableInfo['occupiedUser']
                            ],
                            'table_info' => [
                                "table_number" => $this->tableNumber,
                                "table_code" => $this->tableCode
                            ]
                        ];
                    }

                }
            }else{
                $bool = false;
            }
        }catch(Exception $e){
            die($e->getMessage());
        }
        return $bool;
    }

    // public function addUser($name, $phone, $email){
    //     $status = [false, ""];
    //     $uid = uniqid("USER", true);
    //     $tableNumber = $_SESSION['user']['table_info']['table_number'];
    //     try{
    //         $q = $this->db->prepare("SELECT COUNT(1) as userCount, uid FROM users WHERE phone = :phone");
    //         $q->bindParam(':phone', $phone);
    //         $q->execute();
    //         $res = $q->fetch(PDO::FETCH_ASSOC);
    //         if($res['userCount'] == '1'){
    //             $uid = $res['uid'];
    //             $q1 = $this->db->prepare("UPDATE users SET name = :name WHERE phone = :phone");
    //             $q1->bindParam(':name', $name);
    //             $q1->bindParam(':phone', $phone);
    //         }else{
    //             $q1 = $this->db->prepare("INSERT INTO users (uid, name, phone, email) VALUES (:uid, :name, :phone, :email)");
    //             $q1->bindParam(":uid", $uid);
    //             $q1->bindParam(':name', $name);
    //             $q1->bindParam(':phone', $phone);
    //             $q1->bindParam(':email', $email);
    //         }
    //         $q2 = $this->db->prepare("UPDATE table_info SET tableStatus = 'occupied', occupiedUser = '$uid' WHERE tableNumber = '$tableNumber'");
    //         $q3 = $this->db->prepare("INSERT INTO table_status_dump (tableNumber, occupiedUser) VALUES (:tableNumber, :uid)");
    //         $q3->bindParam(":tableNumber", $tableNumber);
    //         $q3->bindParam(":uid", $uid);
    //         if($q1->execute() && $q2->execute() && $q3->execute()){
    //             $_SESSION['user']['user_info'] = ['uid' => $uid, 'name' => $name, 'phone' => $phone, 'email' =>  $email];
    //             $status[0] = true;
    //         }
    //     }catch(Exception $e){
    //         $status = [false, explode(": ", $e->getMessage)[2]];
    //     }
    //     return $status;
    // }

    public function checkUserStatus() {
        $status = [false, ""];
        try{
            $userId = $_SESSION['user']['user_info']['uid'];
            $tableNo = $_SESSION['user']['table_info']['table_number'];

            $q = $this->db->prepare("SELECT COUNT(1) as tableStatus FROM table_info WHERE tableNumber = :tableNo AND occupiedUser = :occupiedUser");
            $q->bindParam(":tableNo", $tableNo);
            $q->bindParam(":occupiedUser", $userId);
            $q->execute();
            $res = $q->fetch(PDO::FETCH_ASSOC);
            if($res['tableStatus'] == '1'){
                $status = [true, ""];
            }else{
                $status = [false, "Not Appropriate User"];
            }
        }catch(Exception $e){
            // die($e->getMessage());
            $status = [false, explode(": ", $e->getMessage())[2]];
        }
        return $status;
    }

    public function getUserFeedback($restaurantRating, $serviceRating, $restaurantReview, $serviceReview){
        $q = $this->db->prepare("INSERT INTO feedback (restaurantRating, serviceRating, restaurantReview, serviceReview) VALUES(:restaurantRating, :serviceRating, :restaurantReview, :serviceReview)");
        $q->bindParam(":restaurantRating", $restaurantRating);
        $q->bindParam(":serviceRating", $serviceRating);
        $q->bindParam(":restaurantReview", $restaurantReview);
        $q->bindParam(":serviceReview", $serviceReview);
        $q->execute();
        return true;
    }
}