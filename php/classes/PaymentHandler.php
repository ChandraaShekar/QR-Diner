<?php

require_once "Main.php";

class PaymentHandler extends Main{
    public function __construct(){
        parent::__construct();
    }

    public function updatePaymentSuccess($totalAmount, $status, $payedAmount, $token, $payementType){
        $retStatus = false;
        try{
            $userId = $_SESSION['user']['user_info']['uid'];
            $q = $this->db->prepare("UPDATE order_info SET paymentStatus = '$status', paymentToken = '$token' WHERE userId = '$userId'");
            $q1 = $this->db->prepare("INSERT INTO payment_info (userId, totalPayable, totalAmountPayed, PaymentMethod, paymentToken, paymentStatus) VALUES('$userId', '$totalAmount', '$payedAmount', '$payementType', '$token', '$status')");
            $q2 = $this->db->prepare("UPDATE table_info SET tableStatus = 'paymentSuccessful' WHERE occupiedUser = '$userId'");
            if($q->execute() && $q1->execute() && $q2->execute()){
                $retStatus = true;
            }else{
                $retStatus = false;
            }
        }catch(Exception $e){
            if($status == "succeeded"){
                die("Payment Success But failed to update". explode(': ', $e->getMessage())[2]);
            }else{
                die("Payment Failed and failed to update". explode(': ', $e->getMessage())[2]);
            }
            $retStatus = false;
        }
        return $retStatus;
    }
}

?>