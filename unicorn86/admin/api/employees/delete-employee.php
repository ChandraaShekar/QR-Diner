<?php

require_once './EmployeeHandler.php';
$method = $_SERVER['REQUEST_METHOD'];
$employee = new EmployeeHandler;

switch ($method) {
  case 'GET':
    $employee->deleteEmployee();
    break;
  default:  
    echo "unknown request";
    break;
}
