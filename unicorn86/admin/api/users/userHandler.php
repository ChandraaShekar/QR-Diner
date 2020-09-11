<?php

require '../classes/api.php';
class UserHandler extends Api{

    private $name;
    private $email;
    private $phone;
    private $call = true;

    public function __construct($serviceName){
      $this->serviceName = $serviceName;
      parent::__construct($this->serviceName);

    }
  
    public function addUser(){
      try{
        $this->uid = $this->validateParameter('uid', $this->param['uid'], STRING);
        $this->name = $this->validateParameter('name', $this->param['name'], STRING);
        $this->email = $this->validateParameter('email', $this->param['email'], STRING);
        $this->phone = ltrim($this->validateParameter('phone', $this->param['phone'], STRING), "+");
        $q = $this->db->prepare("SELECT * FROM users WHERE `uid` = :uid");
        $q->bindParam(':uid', $this->uid);
        $q->execute();
        $users = $q->fetch(PDO::FETCH_ASSOC);
        if(!is_array($users)){
          $q2 = $this->db->prepare("INSERT INTO users (`uid`, `name`, `phone_number`, `email`, `status`, `access_right`) VALUES (:uid, :name, :phone, :email, '1', '2')");
          $q2->bindParam(':uid', $this->uid);
          $q2->bindParam(':name', $this->name);
          $q2->bindParam(':email', $this->email);
          $q2->bindParam(':phone', $this->phone);
          if($q2->execute()){
            $this->returnResponse(SUCCESS_RESPONSE, $this->generateToken());
          }else{
            $this->throwError(QUERY_FAILED, 'Query Execution Failed');
          }
        }
      }catch(Exception $e){
        $this->throwError(FAILED, $e->getMessage());
      }
    }
  
    public function getUser(){
      $this->uid = $this->validateParameter('uid', $this->param['uid'], STRING);
      $this->phone = ltrim($this->validateParameter('phone', $this->param['phone'], STRING), '+');
      try{
        $q = $this->db->prepare("SELECT users.name, users.email, users.phone_number, users.restaurant_id,users.access_right, restaurant_info.restaurant_name, restaurant_info.city, restaurant_info.state, restaurant_info.contact_number, restaurant_info.email_id, restaurant_info.zip, restaurant_info.shipping_address FROM users
                                LEFT JOIN restaurant_info ON restaurant_info.created_by = users.uid
                                WHERE `uid` = :uid");
        $q->bindParam(':uid', $this->uid);
        $q->execute();
        $user_info = $q->fetch(PDO::FETCH_ASSOC);
        if(!is_array($user_info)){
            $q2 = $this->db->prepare("SELECT * FROM new_employee_info WHERE phone = :phone");
            $q2->bindParam(':phone', $this->phone);
            $q2->execute();
            $user_info = $q2->fetch(PDO::FETCH_ASSOC);
            if(!is_array($user_info)){
              $this->throwError(INVALID_UID_PHONE, "Invalid user details ");
            }else{
              $q3 = $this->db->prepare("INSERT INTO users (name, email, phone_number, uid, restaurant_id, access_right, status) VALUES(:name, :email, :phone, :uid, :restaurant_id,'3','1')"); //Pending
              $q3->bindParam(':name', $user_info['name']);
              $q3->bindParam(':email', $user_info['email']);
              $q3->bindParam(':phone', $user_info['phone']);
              $q3->bindParam(':uid', $this->uid);
              $q3->bindParam(':restaurant_id', $user_info['restaurant_id']);
              if($q3->execute()){
                $q3 = $this->db->prepare("DELETE FROM new_employee_info WHERE phone = :phone");
                $q3->bindParam(':phone', $this->phone);
                $q3->execute();
                if($this->call){
                  $this->call = false;
                  $this->getUser();
                }
              }
            }
        }
        $this->returnResponse(SUCCESS_RESPONSE, ["user_info"=>$user_info, "token" => $this->generateToken()]);
        
      }catch(Exception $e){
        $this->throwError(FAILED, $e->getMessage());
      }
    }
  
    public function updateUser(){
      try{
        $this->uid = $this->validateParameter('uid', $this->param['uid'], STRING);
        $this->name = $this->validateParameter('name', $this->param['name'], STRING);
        $this->email = $this->validateParameter('email', $this->param['email'], STRING);
        $this->phone = ltrim($this->validateParameter('phone', $this->param['phone'], STRING), '+');
        $q = $this->db->prepare("SELECT * FROM users WHERE `uid` = :uid");
        $q->bindParam(':uid', $this->uid);
        $q->execute();
        $users = $q->fetch(PDO::FETCH_ASSOC);
        if(is_array($users)){
          $q2 = $this->db->prepare("UPDATE users SET `name` = :name, `phone_number` = :phone, `email`=:email WHERE `uid` = :uid");
          $q2->bindParam(':name', $this->name);
          $q2->bindParam(':email', $this->email);
          $q2->bindParam(':phone', $this->phone);
          $q2->bindParam(':uid', $this->uid);
          if($q2->execute()){
            $this->returnResponse(SUCCESS_RESPONSE, 'Update was Successful');
          }else{
            $this->throwError(QUERY_FAILED, 'Update was Unsuccessful');
          }
        }
      }catch(Exception $e){
        $this->throwError(EXCEPTIONS, $e->getMessage());
      }
    }
  
    public function generateToken(){
      // $this->uid = $this->validateParameter('uid', $this->param['uid'], STRING);
      try{
        $stmt = $this->db->prepare("SELECT * FROM users WHERE `uid` = :uid");
        $stmt->bindParam(':uid', $this->uid);
        $stmt->execute();
        $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!is_array($user_info)){
          $this->throwError(INVALID_UID_PHONE, "UID or Phone Number is invalid");
        }else{
          $paylod = [
            'iat' => time(),
            'iss' => 'vfoodsupplies.lbits.co',
            'exp' => time() + (6*30*24*60*60),
            'data' => [
                'uid' => $user_info['uid'],
                'phone' => $user_info['phone_number']
            ]
          ];
    
          $token = JWT::encode($paylod, SECRET_KEY, 'HS256');
          $data = $token;
          // $this->returnResponse(SUCCESS_RESPONSE, $data);
          return $data;
        }
      }catch(Exception $e){
        $this->throwError(JWT_PROCESSING_ERROR, $e->getMessage());
      }
    }
  }