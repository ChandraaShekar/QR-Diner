<?php

$method = $_SERVER['REQUEST_METHOD'];

$input = json_decode(file_get_contents('php://input'));

require_once '../db/db.php';

switch ($method) {
  case 'GET':
    delete_user($db, $_GET);
    break;
  default:
    echo "unknown request";
    break;
}

function delete_user($db, $data){
    if(!isset($data['uid'])){
        echo "unknown request";
    }else{
        $uid = $data['uid'];
        if(mysqli_num_rows($q) > 0){
            echo "user exists";
        }else{
            $delete = $db->query("DELETE FROM users WHERE `uid` = '$uid'");
            if(!$delete){
                echo "Failed, Try again later";
            }else{
                echo "success";
            }
        }
    }
}