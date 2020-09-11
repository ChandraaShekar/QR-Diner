<?php

require "php/classes/UserHandler.php";

$main = new UserHandler();
$getIp = getUserIpAddr($router);
$x = $main->getAllowedIps($router->subdomain[0], $getIp);
if(!$x){
    header("Location: /");
    die("You need to connect to the our Restaurant WiFi to access this Feature");
}

function getUserIpAddr($router){
    if(!empty($router->server['HTTP_CLIENT_IP'])){
        return $router->server['HTTP_CLIENT_IP'];
    }elseif(!empty($router->server['HTTP_X_FORWARDED_FOR'])){
        return $router->server['HTTP_X_FORWARDED_FOR'];
    }else{
        return $router->server['REMOTE_ADDR'];
    }
}

$_SESSION = array();
if(isset($router->params[0]) && isset($router->params[1])){
    $tableNo = $router->params[0];
    $tableCode = $router->params[1];
    $user = new userHandler();
    if(isset($_SESSION['user'])){
        header("Location: /welcome");
    }else{
        $user->clearTable($router->subdomain[0], $tableNo, $tableCode);
        if(isset($_SESSION['user'])){
            header("Location: /welcome");
        }else{
            header("Location: /not-authorized");
        }
    }
}else{
    header("Location: /not-authorized");
}

?>