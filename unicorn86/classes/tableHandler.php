<?php

require_once "main.php";

class tableHandler extends Main {
    public function __construct(){
        parent::__construct();
    } 

    public function verifyTable($tableNo, $tableCode){
        $status = [false, ""];
        try{
            $q = $this->db->prepare("SELECT COUNT(1) as tableStatus FROM table_info LEFT JOIN table_status ON table_status.tableNumber = table_info.tableNumber WHERE table_info.tableNumber = :tableNumber AND tableCode = :tableCode AND table_status.tableStatus = 'available'");
            $q->bindParam(":tableNumber", $tableNo);
            $q->bindParam(":tableCode", $tableCode);
            $q->execute();
            $res = $q->fetch(PDO::FETCH_ASSOC);
            if($res['tableStatus'] == '1'){
                $status = [true, ""];
            }
        }catch(Exception $e){
            $status = [false, explode(": ", $e->getMessage())[2]];
        }
        return $status;
    }
}