<?php

require_once "../classes/userHandler.php";

$main = new UserHandler();
$getIp = getUserIpAddr();
$x = $main->getAllowedIps($getIp);
if(!$x){
    header("Location: ../index.php");
    die("You need to connect to the our Restaurant WiFi to access this Feature");
}

function getUserIpAddr(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        return $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        //ip pass from proxy
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        return $_SERVER['REMOTE_ADDR'];
    }
}

session_start();
$_SESSION = array();
if(isset($_GET['tableNo']) && isset($_GET['tableCode'])){
    $tableNo = $_GET['tableNo'];
    $tableCode = $_GET['tableCode'];
    // session_destroy();
    $user = new userHandler();
    if(isset($_SESSION['user'])){
        header("Location: ../menu.php");
    }else{
        $user->clearTable($tableNo, $tableCode);
        if(isset($_SESSION['user'])){
            header("Location: ../menu.php");
        }else{
            header("Location: ../error.php");
        }
    }
}else{
    header("Location: ../error.php");
}