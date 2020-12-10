<?php
require_once(__DIR__.'/../../models/plan/index.php');
require_once(__DIR__.'/../../http/Router.php');
$router = new Router();

$router->handlePost(function($http, $body) {
    
})->addValidator(is_authenticated);

