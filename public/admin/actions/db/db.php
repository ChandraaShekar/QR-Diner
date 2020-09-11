<?php

require_once 'CONFIG.INI.php';
$db = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
if (!$db) {
  die("Failed to connect to database.");
  exit();
}
?>