<?php

require_once(__DIR__.'/HttpResponse.php');
require_once(__DIR__.'/Headers.php');
require_once(__DIR__.'/../config/database.php');
require_once(__DIR__.'/../api/auth/session.php');
require_once(__DIR__.'/../validators/auth.php');

class Route {
    private $validators;
    private $handler;
    public function __construct($handler) {
        $this->handler = $handler;
        $this->validators = [];
    }

    public function addValidator($validatorFn) {
        array_push($this->validators, $validatorFn);
        return $this;
    }

    public function run($http, $request_body) {
        foreach($this->validators as $validator) {
            $validator($http, $request_body);
        }
        $handler = $this->handler;
        $handler($http, $request_body);
    }
}

class Router {
    private $routes;
    public $http;

    public function __construct() {
        $this->routes = array();
        $this->http = new HttpResponse();
    }

    public function handlePost($handler) {
        $route = new Route($handler);
        $this->routes['POST'] = $route;
        return $route;
    }

    public function handlePut($handler) {
        $route = new Route($handler);
        $this->routes['PUT'] = $route;
        return $route;
    }
    public function handleDelete($handler) {
        $route = new Route($handler);
        $this->routes['DELETE'] = $route;
        return $route;
    }
    public function handleGet($handler) {
        $route = new Route($handler);
        $this->routes['GET'] = $route;
        return $route;
    }

    private function renderHeaders() {
        Headers::sendHeaders(array_keys($this->routes));
    }

    public function getSessionData() {
        return getSessionData();
    }

    private function run() {
        $this->renderHeaders();
        $method = $_SERVER['REQUEST_METHOD'];
        $request_body = file_get_contents('php://input');
        if($request_body) $request_body = json_decode($request_body, true);
        try{
            if(isset($this->routes[$method])) {
                $this->routes[$method]->run($this->http, $request_body);
            }
        }catch(Exception $e) {
            $this->http->serverFault($e->getMessage());
        }
        if($method !== 'OPTIONS') {
            $this->http->badMethod("The method \"$method\" isn't supported.");
        }
    }
    public function __destruct() {
        $this->run();
    }

}

?>