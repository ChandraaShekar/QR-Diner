<?php

require_once "Main.php";

class Router extends Main {
    public $uri = "404";
    public $params = [];
    public $basePage = "";
    public $server = [];
    public $subdomain = "";
    public function __construct(){
        parent::__construct();
    }

    public function urlValidator($server){
        try{
            $url = $server['HTTP_HOST'];
            $request = $server['REQUEST_URI'];
            $uri = $request;
            $this->uri = $uri;
            $alpha = range('a', 'z');
            $params = explode("/", substr($uri, 1));
            $this->server = $server;
            $x = array_shift($params);
            $this->params = $params;
            $this->basePage = $x;
            $this->uri = "/$x";
            foreach ($params as $key => $value) {
                $this->uri .= "/:". $alpha[$key];
            }
            $subdomains = explode(".", str_replace(BASE_URL, "", $url));
            if($this->nameValidator($subdomains)){
                return true;
            }
        }catch(Exception $e){
            $this->throwError($e);
        }
        return false;
    }

    public function nameValidator($subdomain){        
        try{
            if(count($subdomain) == 1){
                $name = $subdomain[0];
                
                if($this->isValidRestaurant($name)){
                    // $_SESSION['subdomain'] = $subdomain;
                    $this->subdomain = $subdomain;
                    $_SESSION['isAdmin'] = 'false';
                    return true;
                }else{
                    $_SESSION['page'] = "404";
                    $this->uri = "404";
                }
            }elseif(count($subdomain) == 2){
                if($subdomain[0] == "admin"){
                    $name = $subdomain[1];
                    if($this->isValidRestaurant($name)){
                        // $_SESSION['subdomain'] = $subdomain;
                        $this->subdomain = $subdomain;
                        $_SESSION['isAdmin'] = 'true';
                        return true;
                    }else{
                        $_SESSION['page'] = "404";
                        $this->uri = "404";
                    }
                }
            }
        }catch(Exception $e){
            $this->throwError($e);
        }
        return false;
    }

    public function isValidRestaurant($domainName){
        try{
            $q = $this->db->prepare("SELECT COUNT(1) AS count FROM restaurant_basic_info WHERE domainName = :domainName");
            $q->bindParam(':domainName', $domainName);
            $q->execute();
            if($q->fetch(PDO::FETCH_ASSOC)['count'] == 1){
                return true;
            }
        }catch(Exception $e){
            $this->throwError($e);
        }
        return false;
    }

    public function get($req){
        if($req['REQUEST_METHOD'] == 'GET'){
            if($this->urlValidator($req)){
                return 'true';
            }
            return "get";
        }
        return "404";
    }

    public function post($req, $data){
        if($req['REQUEST_METHOD'] == 'POST'){
            if($this->urlValidator($req)){
                // $this->params = $data;
                return $req['REQUEST_URI'];
            }
        }
        return false;
    }
}