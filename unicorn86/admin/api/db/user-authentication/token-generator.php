<?php

require "../db/db.php";
require "../jwt.php";
require "../config.php";
session_start();

$input = json_decode(file_get_contents("php://input"));

$uid = $input->uid;
$phone = $input->phone;

$tokenId    = base64_encode(hash(MHASH_MD5, "48947841171sfwewfwsrg"));
$issuedAt   = time();
$notBefore  = $issuedAt + 5; //Adding 10 seconds
$expire     = $notBefore + (60 * 60 * 24 * 15); // Adding 60 seconds
$serverName = 'vfoodsupplies.lbits.co';

$data1 = [                  
    'uid'   => $uid,
    'phone' => $phone,
];

$data = json_encode([
    'iat' => $issuedAt,
    'jti'  => $tokenId,          
    'iss'  => $serverName,       
    'nbf'  => $notBefore,        
    'exp'  => $expire,           
    'data' => $data1
]);


$token = JWT::encode($data, SECRET, 'HS512');
$_SESSION['user'] = $data1;
echo $token;

?>