<?php

require_once './userHandler.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
  case 'GET':
    $user = new UserHandler('generateToken');
    $user->getUser();
    break;
  case 'POST':
    $user = new UserHandler('generateToken');
    $user->addUser();
    break;
  default:  
    echo "unknown request";
    break;
}

