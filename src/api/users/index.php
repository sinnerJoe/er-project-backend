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
    try {

        $user->register((array)$body);
    } catch(Exception $e) {
        $http->badRequest("The user is already registered.");
    }
    $http->ok(null, "You registerd successfully.");
});

$router->handleGet(function ($http, $body) {
    $http->ok("JUST FUINE");
});









$router->run();
?>