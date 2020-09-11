<?php

$method = $_SERVER['REQUEST_METHOD'];
require_once './EmployeeHandler.php';
$employee = new EmployeeHandler;
switch ($method) {
  case 'GET':
    $employee->getEmployees();
    break;
  case 'POST':
    $employee->addEmployee();
    break;
  default:  
    echo "unknown request";
    break;
}