<?php

require_once(__DIR__.'/HttpResponse.php');
require_once(__DIR__.'/Headers.php');
require_once(__DIR__.'/../config/database.php');

$secureRoute = function ($controller) {
    return function ($http) {
        if(isset($_SESSION['PHP_AUTH_USER']) && !isset($_SERVER['PHP_AUTH_PW'])) {
            $http->notAuthorized("You must authenticate to use this service.");
            exit(); 
        }

        $username = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];

        $query = "SELECT * user_account WHERE email = ?";

        $db = Database::getInstance();

        $userData = $db->fetchOne($query, [$username]);

        if(!$userData || $userData['password'] !== $password) {
            $http->notAuthorized("You provided wrong credentials.");
            exit();
        }

        $user_id = $userData['user_id'];

        $controller($http);
    };
};

class Router {
    private $handlers;
    private $secured = true;
    private $http;

    public function __construct($secured = true) {
        $this->handlers = array();
        $this->secured = $secured;
        $this->http = new HttpResponse();
    }

    public function handlePost($handler) {
        $this->handlers['POST'] = $handler;
    }

    public function handlePut($handler) {
        $this->handlers['PUT'] = $handler;
    }
    public function handleDelete($handler) {
        $this->handlers['DELETE'] = $handler;
    }
    public function handleGet($handler) {
        $this->handlers['GET'] = $handler;
    }

    private function renderHeaders() {
        Headers::sendHeaders(array_keys($this->handlers));
    }

    public function run() {
        $this->renderHeaders();
        $method = $_SERVER['REQUEST_METHOD'];
        $request_body = file_get_contents('php://input');
        if($request_body) $request_body = json_decode($request_body, true);
        try{
            if(isset($this->handlers[$method])) {
                $this->handlers[$method]($this->http, $request_body);
            }
        }catch(Exception $e) {
            $this->http->serverFault($e->getMessage());
        }
        if($method !== 'OPTIONS') {
            $this->http->badMethod("The method \"$method\" isn't supported.");
        }
    }
}

?>