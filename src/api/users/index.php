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
    $data = (array)$body;
    $data['password'] = password_hash($body->password, PASSWORD_BCRYPT);
    try {
        $user->register($data);
    } catch(Exception $e) {
        $http->badRequest("The user is already registered.".$e->getMessage().$data['password']);
    }
    $http->ok(null, "You registerd successfully.");
});

$router->handleGet(function ($http, $body) {
});









$router->run();
?>