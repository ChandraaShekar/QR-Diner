<?php
// require_once "php/auth.php";
session_start();
// unset($_SESSION['admin_user']);
session_destroy();
header("Location: /login");



?>