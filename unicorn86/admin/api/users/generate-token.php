<?php

require_once "./userHandler.php";
$method = $_SERVER['REQUEST_METHOD'];
$user = new UserHandler('generateToken');

switch($method){
    case 'GET':
        $user->generateToken();
        exit;
    default:
        echo "Invalid Request Type";
        exit;
}