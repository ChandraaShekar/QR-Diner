<?php

require_once './userHandler.php';
$method = $_SERVER['REQUEST_METHOD'];
$user = new UserHandler('');
switch ($method) {
  case 'POST':
    $user->updateUser();
    break;
  default:
    $user->throwError(REQUEST_METHOD_NOT_VALID, "Invalid Request method");
    break;
}
