<?php
require("stripe-php-master/init.php");

$publishKey = "pk_test_gGvB6BqxbubV3dYJuFp6H8IX00W1lAFwEy";
$secreatKey = "sk_test_d2GGlsUbVhmejdg9HEYceS9G00tsAx5neP";

// $stripe = new Stripe;

\Stripe\Stripe::setApiKey($secreatKey);

?>