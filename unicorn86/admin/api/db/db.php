<?php

require_once 'CONFIG.INI.php';
$db = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
if (!$db) {
  echo "Failed to connect to MySQL" .$db;
  exit();
}
?>