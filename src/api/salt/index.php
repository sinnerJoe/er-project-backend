<?php

require_once(__DIR__.'/../../http/Router.php');
require_once(__DIR__.'/../../models/user/index.php');

$router = new Router(false);

$user = new User();

$router->handlePost(function ($http, $body) {
    // echo $_POST;
    global $user;
    // var_dump ((array)$body);
    // exit();
    $userData = $user->findUserByEmail($body->email);
    if(!$userData) {
        $http->notAuthorized("The email you typed isn't registered.");
    }
    if($body->password !== $userData["password"]) {
        $http->notAuthorized("The account password is invalid.");
    }
    $http->ok(null, "Authentication succeeded.");
});

$router->run();
?>