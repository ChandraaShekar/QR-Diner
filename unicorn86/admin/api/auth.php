<?php

$input = json_decode(file_get_contents("php://input"), true);

function getAuthorizationHeader(){
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    }
    else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        //print_r($requestHeaders);
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
}
/**
* get access token from header
* */
function getBearerToken() {
$headers = getAuthorizationHeader();
// var_dump($headers);
// HEADER: Get the access token from the header
if (!empty($headers)) {
    if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
        return $matches[1];
    }
}
return null;
}
// $headers = apache_request_headers();

// foreach ($headers as $header => $value) {
//     echo "$header: $value <br />\n";
// }   
require_once "jwt.php";
// $credentialsAreValid = true;
// echo $input['uid'];
$jwt_token = getBearerToken();
// echo $jwt_token;
$decode_data = JWT::decode($jwt_token, "csr1998", ["HS512"]);
$d = json_decode($decode_data, true);
echo $d['data']['uid'];
// if(isset($input['uid']) && isset($input['phone'])){ 
// $uid = $input['uid'];
// $phone = $input['phone'];
// // echo $uid;


//     // if ($credentialsAreValid) {

//         $tokenId    = base64_encode(mhash(MHASH_MD5, "csr1998"));
//         $issuedAt   = time();
//         $notBefore  = $issuedAt + 5; //Adding 10 seconds
//         $expire     = $notBefore + (60 * 60 * 24 * 15); // Adding 60 seconds
//         $serverName = 'vfoodsupplies.lbits.co'; // Retrieve the server name from config file
        
//         // /*
//         // * Create the token as an array
//         // */
//         $data = json_encode([
//             'iat'  => $issuedAt,         // Issued at: time when the token was generated
//             'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
//             'iss'  => $serverName,       // Issuer
//             'nbf'  => $notBefore,        // Not before
//             'exp'  => $expire,           // Expire
//             'data' => [                  // Data related to the signer user
//                 'uid'   => $uid,
//                 'phone' => $phone,
//             ]
//         ]);

//         $token = JWT::encode($data, "csr1998", 'HS512');
//         echo $token;
//     // }

// // }