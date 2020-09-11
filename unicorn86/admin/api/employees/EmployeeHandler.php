<?php

require '../classes/api.php';
class EmployeeHandler extends Api{

    private $name;
    private $email;
    private $phone;
    private $restaurant_id;

    public function __construct(){
      parent::__construct('');
    }

    public function getEmployees(){
        $this->uid = $this->validateParameter('uid', $this->param['uid'], STRING);
        try{
            $q = $this->db->prepare("SELECT id, name, phone_number, email FROM users WHERE restaurant_id IN (SELECT id FROM restaurant_info WHERE created_by = :uid) AND access_right = '3'");
            $q->bindParam(':uid', $this->uid);
            $q->execute();
            $active_employees = [];
            while($active_employee = $q->fetch(PDO::FETCH_ASSOC)){
                $active_employees[] = $active_employee;
            }
            $q2 = $this->db->prepare("SELECT id, name, phone, email FROM new_employee_info WHERE created_by = :uid");
            $q2->bindParam(':uid', $this->uid);
            $q2->execute();
            $new_employees = [];
            while($new_employee = $q2->fetch(PDO::FETCH_ASSOC)){
                $new_employees[] = $new_employee;
            }
            $this->returnResponse(SUCCESS_RESPONSE, ["active" => $active_employees, "new" => $new_employees]);
        }catch(Exception $e){
            $this->throwError(EXCEPTIONS, $e->getMessage());
        }
    }

    public function addEmployee(){
        try{
            $this->name = $this->validateParameter('name', $this->param['name'], STRING);
            $this->email = $this->validateParameter('email', $this->param['email'], STRING);
            $this->phone = $this->validateParameter('phone', $this->param['phone'], STRING);
            $this->uid = $this->validateParameter('uid', $this->param['uid'], STRING);
            $this->restaurant_id = $this->validateParameter('restaurant_id', $this->param['restaurant_id'], INTEGER);
            $q = $this->db->prepare("SELECT * FROM new_employee_info WHERE phone = :phone");
            $q->bindParam(':phone', $this->phone);
            $q->execute();
            $employee = $q->fetch(PDO::FETCH_ASSOC);
            if(is_array($employee)){
                $this->throwError(DUPLICATE_DATA, "Employee already Exists");
            }
            $q2 = $this->db->prepare("SELECT * FROM users WHERE phone_number = :phone");
            $q2->bindParam(':phone', $this->phone);
            $q2->execute();
            $users = $q2->fetch(PDO::FETCH_ASSOC);
            if(is_array($users)){
                $this->throwError(DUPLICATE_DATA, "User Already Exists");
            }
            $q3 = $this->db->prepare("INSERT INTO new_employee_info (name, email, phone, created_by, restaurant_id) VALUES(:name, :email, :phone, :created_by, :restaurant_id)");
            $q3->bindParam(":name", $this->name);
            $q3->bindParam(":email", $this->email);
            $q3->bindParam(":phone", $this->phone);
            $q3->bindParam(":created_by", $this->uid);
            $q3->bindParam(":restaurant_id", $this->restaurant_id);
            if($q3->execute()){
                $this->returnResponse(SUCCESS_RESPONSE, "Successfully added the employee");
            }else{
                $this->throwError(QUERY_FAILED, "Error while adding the employee");
            }
        }catch(Exception $e){
            $this->throwError(EXCEPTIONS, $e->getMessage());
        }
    }

    public function deleteEmployee(){
        $this->uid = $this->validateParameter('uid', $this->param['uid'], STRING);
        $this->phone = $this->validateParameter('employee Phone Number', $this->param['emp_phone'], STRING);
        try{
            $q = $this->db->prepare("DELETE FROM new_employee_info WHERE phone = :phone AND created_by = :uid");
            $q->bindParam(':phone', $this->phone);
            $q->bindParam(':uid', $this->uid);
            $q2 = $this->db->prepare("DELETE FROM new_employee_info WHERE phone = :phone AND restaurant_id IN (SELECT id FROM restaurant_info WHERE created_by = :uid)");
            $q2->bindParam(':phone', $this->phone);
            $q2->bindParam(':uid', $this->uid);
            if($q2->execute() && $q->execute()){
                $this->returnResponse(SUCCESS_RESPONSE, "Successfully Deleted Employee");
            }else{
                $this->throwError(QUERY_FAILED, "Failed Deleting the user");
            }
        }catch(Exception $e){
            $this->throwError(EXCEPTIONS, $e->getMessage());
        }
    }
}