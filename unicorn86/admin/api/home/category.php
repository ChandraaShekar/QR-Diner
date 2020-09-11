<?php

$method = $_SERVER['REQUEST_METHOD'];

require_once './homeHandler.php';

$category = new HomeHandler;

switch ($method) {
    case 'GET':
      $category->getCategoryItems();
      break;
    default:
      echo "unknown request";
      break;
}