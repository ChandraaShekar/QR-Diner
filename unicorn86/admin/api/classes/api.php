<?php

require_once "DB.php";
require_once "jwt.php";

class Api {
    protected $request;
    protected $param;
    protected $db;
    protected $uid;
    public $serviceName;

    public function __construct($serviceName){
        $this->request = file_get_contents('php://input');
        $this->validateRequest();

        $conn = new DB;
        $this->db = $conn->connect();
        $this->serviceName = $serviceName;
        if(strtolower( $this->serviceName) != 'generatetoken' ) {
            $this->validateToken();
        }
    }


    public function validateRequest(){
        // if($_SERVER['CONTENT_TYPE'] !== 'application/json') {
        //     $this->throwError(REQUEST_CONTENT_TYPE_NOT_VALID, 'Request content type is not valid');
        // }
        
        $data = json_decode($this->request, true);
        // print_r($data);
        // print_r($_GET);
        $this->param = ($data)?$data:$_GET;
    }

    public function validateToken(){
        try{
            $token = $this->getBearerToken();
            $payload = JWT::decode($token, SECRET_KEY, ['HS256']);

            $stmt = $this->db->prepare("SELECT * FROM users WHERE `uid` = :uid");
            $stmt->bindParam(":uid", $payload->data->uid);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!is_array($user)) {
                $this->throwError(INVALID_UID_PHONE, "auth error");
            }
            $this->uid = $payload->data->uid;
        } catch (Exception $e) {
            $this->throwError(ACCESS_TOKEN_ERRORS, $e->getMessage());
        }
    }

    public function validateParameter($fieldName, $value, $dataType, $required = true) {
        if($required == true && empty($value) == true) {
            $this->throwError(VALIDATE_PARAMETER_REQUIRED, $fieldName . " parameter is required.");
        }

        switch ($dataType) {
            case BOOLEAN:
                if(!is_bool($value)) {
                    $this->throwError(VALIDATE_PARAMETER_DATATYPE, "Datatype is not valid for " . $fieldName . '. It should be boolean.');
                }
                break;
            case INTEGER:
                if(!is_numeric($value)) {
                    $this->throwError(VALIDATE_PARAMETER_DATATYPE, "Datatype is not valid for " . $fieldName . '. It should be numeric.');
                }
                break;

            case STRING:
                if(!is_string($value)) {
                    $this->throwError(VALIDATE_PARAMETER_DATATYPE, "Datatype is not valid for " . $fieldName . '. It should be string.');
                }
                break;
            
            default:
                $this->throwError(VALIDATE_PARAMETER_DATATYPE, "Datatype is not valid for " . $fieldName);
                break;
        }

        return $value;

    }


    public function throwError($code, $message) {
        header("content-type: application/json");
        $errorMsg = json_encode(['status' => 'error', 'response'=>$code, 'result'=> $message]);
        echo $errorMsg; exit;
    }

    public function returnResponse($code, $data) {
        header("content-type: application/json");
        $response = json_encode(['status'=> 'success', 'response' => $code, "result" => $data]);
        echo $response; exit;
    }

    public function getAuthorizationHeader(){
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
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
    /**
     * get access token from header
     * */
    public function getBearerToken() {
        $headers = $this->getAuthorizationHeader();
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        $this->throwError( AUTHORIZATION_HEADER_NOT_FOUND, 'Access Token Not found');
    }
}
